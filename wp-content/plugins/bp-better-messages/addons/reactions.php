<?php
defined( 'ABSPATH' ) || exit;

if ( !class_exists( 'BP_Better_Messages_Reactions' ) ){

    class BP_Better_Messages_Reactions
    {

        public static function instance()
        {

            static $instance = null;

            if (null === $instance) {
                $instance = new BP_Better_Messages_Reactions();
            }

            return $instance;
        }

        public function __construct(){
            add_filter('bp_better_messages_script_variables', array( $this, 'add_reactions_to_js' ) );

            add_action('wp_ajax_bp_messages_add_reaction', array( $this, 'add_reaction_ajax' ) );

            add_action( 'bp_better_messages_message_content_end', array( $this, 'render_icons_in_message' ), 10, 2 );
        }

        public function render_icons_in_message( $message_id, $thread_id ){
            $reactions = $this->get_reactions();
            $message_reactions = $this->get_message_reactions( $message_id );

            if( empty( $message_reactions ) ) return;

            $result_reactions = [];
            foreach( $message_reactions as $user_id => $reaction ){
                if( ! isset($reactions[$reaction]) ) {
                    continue;
                }

                if( ! isset( $result_reactions[ $reaction ] ) ){
                    $result_reactions[ $reaction ] = 0;
                }

                $result_reactions[ $reaction ]++;
            }

            if( empty( $result_reactions ) ) return;

            echo '<span class="bm-reactions">';
            foreach( $result_reactions as $unicode => $count ){
                echo '<span class="bm-reaction">';
                    echo '<img class="emojione" alt="" src="https://cdn.bpbettermessages.com/emojies/6.6/png/unicode/32/' . $unicode . '.png">';
                    echo '<span class="bm-reaction-count">' . $count . '</span>';
                echo '</span>';
            }
            echo '</span>';
        }

        public static function get_default_reactions(){
            return [
                '1f914' => _x('Thinking', 'Reaction name', 'bp-better-messages'),
                '2b50'  => _x('Star', 'Reaction name', 'bp-better-messages'),
                '1f632' => _x('WOW', 'Reaction name', 'bp-better-messages'),
                '1f60d' => _x('Love', 'Reaction name', 'bp-better-messages'),
                '1f44c' => _x('Okay', 'Reaction name', 'bp-better-messages'),
                '1f44d' => _x('Thumbs up', 'Reaction name', 'bp-better-messages'),
            ];
        }

        public function get_reactions(){
            $reactions = BP_Better_Messages()->settings['reactionsEmojies'];
            return apply_filters( 'bp_better_messages_reactions_list', $reactions );
        }

        public function add_reactions_to_js( $variables ){
            $variables['reactions'] = $this->get_reactions();
            return $variables;
        }

        public function add_reaction_ajax(){
            if( ! wp_verify_nonce( $_POST[ 'nonce' ], 'bpbm_edit_nonce' ) ){
                exit;
            }

            global $wpdb;
            $user_id = get_current_user_id();
            $unicode = sanitize_text_field($_POST['unicode']);
            $available_reactions = $this->get_reactions();

            if( ! isset( $available_reactions[ $unicode ] ) ){
                wp_send_json_error();
            }

            $message_id = intval($_POST['message_id']);

            $message   = $wpdb->get_row($wpdb->prepare( "SELECT * FROM " . bpbm_get_table('messages') . " WHERE `id` = %d", $message_id ));

            if( ! $message ) {
                wp_send_json_error();
            }

            $sender_id = intval( $message->sender_id );
            $thread_id = intval( $message->thread_id );

            if( $sender_id === $user_id ){
                wp_send_json_error();
            }

            if( ! BP_Better_Messages()->functions->check_access( $thread_id ) ){
                wp_send_json_error();
            }

            $reactions = $this->get_message_reactions( $message_id );

            if( isset( $reactions[ $user_id ] ) && $reactions[ $user_id ] === $unicode ){
                unset( $reactions[$user_id] );
            } else {
                $reactions[$user_id] = $unicode;
            }

            bp_messages_update_meta( $message_id, 'bm_reactions', $reactions );

            ob_start();
            $this->render_icons_in_message( $message_id, $thread_id );
            $new_reactions = ob_get_clean();

            do_action( 'bp_better_messages_thread_reaction', $message_id, $thread_id, $new_reactions );

            wp_send_json_success( $new_reactions );
        }

        public function get_message_reactions( $message_id ){
            $reactions = bp_messages_get_meta( $message_id, 'bm_reactions', true );

            if( empty( $reactions ) || ! is_array( $reactions ) ){
                $reactions = [];
            }

            return $reactions;
        }
    }
}

