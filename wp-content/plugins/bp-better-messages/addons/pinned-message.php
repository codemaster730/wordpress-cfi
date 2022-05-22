<?php
defined( 'ABSPATH' ) || exit;

if ( !class_exists( 'BP_Better_Messages_Pinned_Message' ) ) {

    class BP_Better_Messages_Pinned_Message
    {

        public static function instance()
        {

            static $instance = null;

            if (null === $instance) {
                $instance = new BP_Better_Messages_Pinned_Message();
                $instance->setup_actions();
            }

            return $instance;
        }

        public function setup_actions(){
            add_filter( 'bp_better_messages_thread_expanding_buttons', array( $this, 'pinned_message_buttons' ), 10, 5 );
        }

        public function pinned_message_buttons( $buttons, $thread_id, $participants, $is_mini, $type = 'thread' ){
            if( $type === 'thread' &&  count($participants['recipients']) < 2 ) return $buttons;

            $can_pin_message = current_user_can('manage_options');

            if( $can_pin_message ) {
                $label = _x('Add pinned message', 'Extra thread control buttons', 'bp-better-messages');
                $buttons['pin_message'] = '<span title="' . $label . '" class="bpbm-dropdown-item bpbm-pin-message bpbm-can-be-hidden"><i class="fas fa-thumbtack"></i> ' . $label . '</span>';
            }

            return $buttons;
        }

        public function render_pinned_message(){
            /* <div class="bpbm-top-pinned-message">
                <div class="bpbm-top-pinned-message-icon">
                    <i class="fas fa-thumbtack"></i>
                </div>
                <div class="bpbm-top-pinned-message-content">
                    <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>
                </div>
            </div> */
        }

    }
}
