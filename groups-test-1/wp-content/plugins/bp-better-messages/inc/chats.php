<?php
defined( 'ABSPATH' ) || exit;

class BP_Better_Messages_Chats
{

    public static function instance()
    {

        // Store the instance locally to avoid private static replication
        static $instance = null;

        // Only run these methods if they haven't been run previously
        if ( null === $instance ) {
            $instance = new BP_Better_Messages_Chats;
            $instance->setup_actions();
        }

        // Always return the instance
        return $instance;

        // The last metroid is in captivity. The galaxy is at peace.
    }

    public function setup_actions(){
        add_action( 'init',      array( $this, 'register_post_type' ) );
        add_action( 'save_post', array( $this, 'save_post' ), 1, 2 );

        add_shortcode( 'bp_better_messages_chat_room', array( $this, 'layout' ) );

        add_filter('bp_better_messages_thread_expanding_buttons', array($this, 'chat_buttons'), 10, 5);

        add_action( 'wp_ajax_bp_better_messages_join_chat',  array( $this, 'join_chat' ) );
        add_action( 'wp_ajax_bp_better_messages_leave_chat', array( $this, 'leave_chat' ) );

        add_action( 'messages_message_sent', array( $this, 'on_message_sent' ) );

        add_action( "save_post_bpbm-chat", array( $this, 'on_chat_update' ), 10, 3 );
        add_action( 'before_delete_post', array( $this, 'on_chat_delete' ), 10, 1 );
    }

    public function on_chat_update( $post_ID, $post, $update ){
        $thread_id = $this->get_chat_thread_id( $post_ID );

        $name = get_the_title( $post_ID );
        global $wpdb;

        $wpdb->update(
            bpbm_get_table('messages'),
            array(
                'subject'   => $name,
            ),
            array(
                'thread_id' => $thread_id,
            ),
            array( '%s' ), array( '%d' )
        );
    }

    public function on_chat_delete( $post_ID ){
        $post = get_post( $post_ID );
        if( $post->post_type === 'bpbm-chat' ){
            $thread_id = $this->get_chat_thread_id( $post_ID );
            BP_Better_Messages()->functions->erase_thread( $thread_id );
        }
    }

    public function on_message_sent( $message )
    {
        if( ! isset($message->thread_id) ) return false;

        $thread_id = $message->thread_id;
        $chat_id   = BP_Better_Messages()->functions->get_thread_meta( $thread_id, 'chat_id' );

        if( ! $chat_id ) return false;
        global $wpdb;
        $wpdb->update(bpbm_get_table('recipients'), ['unread_count' => 0], ['thread_id' => $thread_id], ['%d'], ['%d']);

        return true;
    }

    public function chat_buttons( $buttons, $thread_id, $participants, $is_mini, $type = 'thread' ){
        if( $type !== 'chat-room' ) {
            return $buttons;
        }

        $current_user_id = get_current_user_id();

        if( isset($participants['users'][$current_user_id]) ) {
            $buttons['leave_chat'] = '<span class="bpbm-dropdown-item bpbm-leave-chat-room"><i class="fas fa-sign-out-alt"></i> ' . __('Leave chat room', 'bp-better-messages') . '</span>';
        }

        return $buttons;
    }

    public function leave_chat(){
        global $wpdb;

        $user_id = get_current_user_id();
        $chat_id = sanitize_text_field($_POST['chat_id']);

        $thread_id = $this->get_chat_thread_id( $chat_id );

        $userIsParticipant = (bool) $wpdb->get_var($wpdb->prepare("
        SELECT COUNT(*) FROM `" . bpbm_get_table('recipients') . "` WHERE `user_id` = %d AND `thread_id` = %d AND `sender_only` = '0'
        ", $user_id, $thread_id));

        if( $userIsParticipant ) {

            $wpdb->delete(
                bpbm_get_table('recipients'),
                array(
                    'user_id' => $user_id,
                    'thread_id' => $thread_id
                ),
                array( '%d', '%d' )
            );

            wp_cache_delete( 'thread_recipients_' . $thread_id, 'bp_messages' );
        }

        wp_send_json(true);
    }

    public function join_chat(){
        global $wpdb;

        $user_id = get_current_user_id();
        $chat_id = sanitize_text_field($_POST['chat_id']);

        if( ! $this->user_can_join( $user_id, $chat_id ) ){
            return false;
        }

        $thread_id = $this->get_chat_thread_id( $chat_id );

        $userIsParticipant = (bool) $wpdb->get_var($wpdb->prepare("
        SELECT COUNT(*) FROM `" . bpbm_get_table('recipients') . "` WHERE `user_id` = %d AND `thread_id` = %d AND `sender_only` = '0'
        ", $user_id, $thread_id));

        if( ! $userIsParticipant ) {

            $wpdb->insert(
                bpbm_get_table('recipients'),
                array(
                    'user_id' => $user_id,
                    'thread_id' => $thread_id,
                    'unread_count' => 0,
                    'sender_only' => 0,
                    'is_deleted' => 0
                )
            );

            wp_cache_delete( 'thread_recipients_' . $thread_id, 'bp_messages' );
        }

        wp_send_json(true);
    }

    public function register_post_type(){
        $args = array(
            'public'               => false,
            'labels'               => [
                'name'          => __( 'Chat Rooms', 'bp-better-messages' ),
                'singular_name' => __( 'Chat Room', 'bp-better-messages' ),
                'add_new'       => __( 'Create new Chat Room', 'bp-better-messages' ),
                'add_new_item'  => __( 'Create new Chat Room', 'bp-better-messages' ),
                'edit_item'     => __( 'Edit Chat Room', 'bp-better-messages' ),
                'new_item'      => __( 'New Chat Room', 'bp-better-messages' ),
            ],
            'publicly_queryable'   => false,
            'show_ui'              => true,
            'show_in_menu'         => 'bp-better-messages',
            'menu_position'        => 1,
            'query_var'            => false,
            'capability_type'      => 'page',
            'has_archive'          => false,
            'hierarchical'         => false,
            'show_in_admin_bar'    => false,
            'show_in_nav_menus'    => false,
            'supports'             => array( 'title' ),
            'register_meta_box_cb' => array( $this, 'register_meta_box' )

        );

        register_post_type( 'bpbm-chat', $args );
    }

    public function register_meta_box(){
        add_meta_box(
            'bpbm-chat-settings',
            __( 'Chat settings', 'bp-better-messages' ),
            array( $this, 'bpbm_chat_settings' )
        );
    }

    public function get_chat_settings( $chat_id ){
        $defaults = array(
            'only_joined_can_read' => '0',
            'enable_chat_email_notifications' => '0',
            'can_join'     => [],
            'can_reply'    => [],
            'template'     => 'default',
            'modernLayout' => 'default'
        );

        $args = get_post_meta( $chat_id, 'bpbm-chat-settings', true );

        if( empty($args) || ! is_array($args) ){
            $args = array();
        }

        return wp_parse_args( $args, $defaults );
    }

    public function save_post( $post_id, $post ){
        if( ! isset($_POST['bpbm_save_chat_nonce']) ){
            return $post->ID;
        }

        //Verify it came from proper authorization.
        if ( ! wp_verify_nonce($_POST['bpbm_save_chat_nonce'], 'bpbm-save-chat-settings-' . $post->ID ) ) {
            return $post->ID;
        }

        //Check if the current user can edit the post
        if ( ! current_user_can( 'manage_options' ) ) {
            return $post->ID;
        }

        if( isset( $_POST['bpbm'] ) && is_array($_POST['bpbm']) ){
            $settings = (array) $_POST['bpbm'];

            if ( ! isset( $settings['only_joined_can_read'] ) ) {
                $settings['only_joined_can_read'] = '0';
            }

            if ( ! isset( $settings['enable_chat_email_notifications'] ) ) {
                $settings['enable_chat_email_notifications'] = '0';
            }

            update_post_meta( $post->ID, 'bpbm-chat-settings', $settings );

            $thread_id = $this->get_chat_thread_id( $post->ID );

            BP_Better_Messages()->functions->update_thread_meta($thread_id, 'exclude_from_threads_list', true);
        }

    }

    public function bpbm_chat_settings( $post ){
        $roles = get_editable_roles();
        if(isset($roles['administrator'])) unset( $roles['administrator'] );

        wp_nonce_field( 'bpbm-save-chat-settings-' . $post->ID, 'bpbm_save_chat_nonce' );

        $settings = $this->get_chat_settings( $post->ID ); ?>
        <style type="text/css">
            .bp-better-messages-roles-list {
                max-height: 250px;
                overflow: auto;
                background: white;
                padding: 15px;
                border: 1px solid #ccc;
            }
        </style>
        <script type="text/javascript">
            jQuery('body').on('click', '.bpbm-select-all', function (event){
                event.preventDefault();

                var ul = jQuery(this).closest('ul');
                ul.find('input[type="checkbox"]').prop('checked', true);
            });

            jQuery('body').on('click', '.bpbm-unselect-all', function (event){
                event.preventDefault();

                var ul = jQuery(this).closest('ul');
                ul.find('input[type="checkbox"]').prop('checked', false);
            });
        </script>
        <div style="margin: 20px 0">
            <label for="bpbm-shortcode" style="font-size: 13px;font-weight: bold">Shortcode</label>
            <input id="bpbm-shortcode" readonly="" type="text" style="width: 100%;" onclick="this.focus();this.select()" value='[bp_better_messages_chat_room id="<?php echo $post->ID; ?>"]'>
        </div>
        <div style="margin: 20px 0">
            <label for="bpbm_only_joined_can_read" style="font-size: 13px;font-weight: bold"><input id="bpbm_only_joined_can_read" type="checkbox" name="bpbm[only_joined_can_read]"  <?php checked( $settings[ 'only_joined_can_read' ], '1' ); ?> value="1" >Only joined can read messages</label>
        </div>
        <?php /*<div style="margin: 20px 0">
            <label for="bpbm_enable_chat_email_notifications" style="font-size: 13px;font-weight: bold"><input id="bpbm_enable_chat_email_notifications" type="checkbox" name="bpbm[enable_chat_email_notifications]" <?php checked( $settings[ 'enable_chat_email_notifications' ], '1' ); ?> value="1">Enable email notifications for this chat</label>
        </div>*/ ?>
        <div style="margin: 20px 0">
            <label style="font-size: 13px;font-weight: bold">Who can join to this chat room?</label>
            <ul class="bp-better-messages-roles-list">
                <li><a href="#" class="bpbm-select-all">Select All</a> | <a href="#" class="bpbm-unselect-all">Unselect All</a></li>
                <?php foreach( $roles as $slug => $role ){ ?>
                    <li><input id="<?php echo $slug; ?>_1" type="checkbox" name="bpbm[can_join][]" value="<?php echo $slug; ?>" <?php if( in_array($slug, $settings['can_join']) ) echo 'checked'; ?>><label for="<?php echo $slug; ?>_1"><?php echo $role['name']; ?></label></li>
                <?php } ?>
            </ul>
        </div>
        <div style="margin: 20px 0">
            <label style="font-size: 13px;font-weight: bold">Who can reply in this chat room?</label>
            <ul class="bp-better-messages-roles-list">
                <li><a href="#" class="bpbm-select-all">Select All</a> | <a href="#" class="bpbm-unselect-all">Unselect All</a></li>
                <?php foreach( $roles as $slug => $role ){ ?>
                    <li><input id="<?php echo $slug; ?>_2" type="checkbox" name="bpbm[can_reply][]" value="<?php echo $slug; ?>" <?php if( in_array($slug, $settings['can_reply']) ) echo 'checked'; ?>><label for="<?php echo $slug; ?>_2"><?php echo $role['name']; ?></label></li>
                <?php } ?>
            </ul>
        </div>

        <table class="form-table">
            <tr>
                <th scope="row" style="width: 300px">
                    <?php _e( 'Messages styling', 'bp-better-messages' ); ?>
                </th>
                <td>
                    <fieldset>
                        <fieldset>
                            <label><input type="radio" name="bpbm[template]" value="default" <?php checked( $settings[ 'template' ], 'default' ); ?>><?php _e( 'Default', 'bp-better-messages' ); ?></label>
                            <br>
                            <label><input type="radio" name="bpbm[template]" value="standard" <?php checked( $settings[ 'template' ], 'standard' ); ?>><?php _e( 'Standard', 'bp-better-messages' ); ?></label>
                            <br>
                            <label><input type="radio" name="bpbm[template]" value="modern" <?php checked( $settings[ 'template' ], 'modern' ); ?>><?php _e( 'Modern', 'bp-better-messages' ); ?></label>
                        </fieldset>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <th scope="row" style="width: 300px">
                    <?php _e( 'Modern messages layout', 'bp-better-messages' ); ?>
                </th>
                <td>
                    <fieldset>
                        <fieldset>
                            <label><input type="radio" name="bpbm[modernLayout]" value="default" <?php checked( $settings[ 'modernLayout' ], 'default' ); ?>>
                                <?php _e( 'Default', 'bp-better-messages' ); ?>
                            </label>
                            <br>
                            <label><input type="radio" name="bpbm[modernLayout]" value="left" <?php checked( $settings[ 'modernLayout' ], 'left' ); ?>>
                                <?php _e( 'My messages at left side', 'bp-better-messages' ); ?>
                            </label>
                            <br>
                            <label><input type="radio" name="bpbm[modernLayout]" value="right" <?php checked( $settings[ 'modernLayout' ], 'right' ); ?>>
                                <?php _e( 'My messages at right side', 'bp-better-messages' ); ?>
                            </label>
                            <br>
                            <label><input type="radio" name="bpbm[modernLayout]" value="leftAll" <?php checked( $settings[ 'modernLayout' ], 'leftAll' ); ?>>
                                <?php _e( 'All messages at left side', 'bp-better-messages' ); ?>
                            </label>
                        </fieldset>
                    </fieldset>
                </td>
            </tr>
        </table>
    <?php
    }

    public function layout( $args ){
        if( ! is_user_logged_in() ){
            ob_start();
            wp_login_form();
            return ob_get_clean();
        }

        $chat_id = $args['id'];

        if (defined('WP_DEBUG') && true === WP_DEBUG) {
            // some debug to add later
        } else {
            error_reporting(0);
        }

        $thread_id     = $this->get_chat_thread_id( $chat_id );

        if( ! $thread_id ) return false;

        $chat_settings = $this->get_chat_settings( $chat_id );

        global $bpbm_errors;
        $bpbm_errors = [];

        do_action('bp_better_messages_before_generation');

        $path = apply_filters('bp_better_messages_views_path', BP_Better_Messages()->path . '/views/');

        $is_mini = isset($_GET['mini']);

        $template = 'layout-chat-room.php';

        ob_start();

        $template = apply_filters( 'bp_better_messages_current_template', $path . $template, $template );

        if( ! BP_Better_Messages()->functions->is_ajax() && count( $bpbm_errors ) > 0 ) {
            echo '<p class="bpbm-notice">' . implode('</p><p class="bpbm-notice">', $bpbm_errors) . '</p>';
        }

        add_filter( 'bp_better_messages_can_send_message', array( $this, 'disable_not_joined' ), 10, 3);

        if( $template !== false ) {
            include($template);
        }

        remove_filter( 'bp_better_messages_can_send_message', array( $this, 'disable_not_joined' ), 10 );

        if( isset($thread_id) && is_int($thread_id)  && ! isset($_GET['mini']) ){
            BP_Better_Messages()->functions->messages_mark_thread_read( $thread_id );
            update_user_meta(get_current_user_id(), 'bpbm-last-seen-thread-' . $thread_id, time());
        }

        $content = ob_get_clean();
        $content = str_replace( 'loading="lazy"', '', $content );

        $content = BP_Better_Messages()->functions->minify_html( $content );

        return $content;
    }

    public function user_can_join( $user_id, $chat_id ){
        if( user_can( $user_id, 'manage_options') ) return true;
        $settings = $this->get_chat_settings( $chat_id );
        $user  = get_userdata( $user_id );
        $has_access = false;
        foreach( $user->roles as $role ){
            if( in_array($role, $settings['can_join']) ) {
                $has_access = true;
            }
        }

        return $has_access;
    }

    public function user_can_reply( $user_id, $chat_id ){
        if( user_can( $user_id, 'manage_options') ) return true;
        $settings = $this->get_chat_settings( $chat_id );
        $user  = get_userdata( $user_id );

        $has_access = false;

        foreach( $user->roles as $role ){
            if( in_array($role, $settings['can_reply']) ) {
                $has_access = true;
            }
        }

        return $has_access;
    }

    public function disable_not_joined( $allowed, $user_id, $thread_id ){
        $participants = BP_Better_Messages()->functions->get_participants($thread_id);
        $chat_id      = BP_Better_Messages()->functions->get_thread_meta( $thread_id, 'chat_id' );

        global $bp_better_messages_restrict_send_message;
        if( ! isset($participants['users'][$user_id]) ){
            $has_access = $this->user_can_join( $user_id, $chat_id );

            if( $has_access ) {
                $message = __('You must to join this chat room to send messages', 'bp-better-messages');
                $message .= "<br>";
                $message .= '<span class="bpbm-join-to-chat-button">' . __('Join chat room', 'bp-better-messages') . '</span>';
            } else {
                $message = __('You are not allowed to join this chat room', 'bp-better-messages');
            }

            $bp_better_messages_restrict_send_message['not_joined_to_chat'] = $message;

            return false;
        } else {
            $has_access = $this->user_can_reply( $user_id, $chat_id );

            if( ! $has_access ){
                $message = __('You are not allowed to reply in this chat room', 'bp-better-messages');
                $bp_better_messages_restrict_send_message['cant_reply_to_chat'] = $message;
                return false;
            }
        }

        return $allowed;
    }

    public function get_chat_thread_id( $chat_id ){
        global $wpdb;

        $thread_id = (int) $wpdb->get_var( $wpdb->prepare( "
        SELECT bpbm_threads_id 
        FROM `" . bpbm_get_table('threadsmeta') . "` 
        WHERE `meta_key` = 'chat_id' 
        AND   `meta_value` = %s
        ", $chat_id ) );

        if( $thread_id === 0 ) {
            $thread_id = false;
        } else {
            $messages_count = (int) $wpdb->get_var($wpdb->prepare("SELECT COUNT(*)  FROM `" . bpbm_get_table('messages') . "` WHERE `thread_id` = %d", $thread_id));

            if( $messages_count === 0 ) {
                $thread_id = false;
            }
        }

        if( ! $thread_id ) {
            $chat = get_post($chat_id);
            if( ! $chat ) return false;

            $name = get_the_title( $chat_id );

            $last_thread = intval($wpdb->get_var("SELECT MAX(thread_id) FROM `" . bpbm_get_table('messages') . "`;"));
            $thread_id = $last_thread + 1;

            $wpdb->insert(
                bpbm_get_table('messages'),
                array(
                    'sender_id' => 0,
                    'thread_id' => $thread_id,
                    'subject'   => $name,
                    'message'   => '<!-- BBPM START THREAD -->'
                )
            );

            BP_Better_Messages()->functions->update_thread_meta( $thread_id, 'chat_thread', true );
            BP_Better_Messages()->functions->update_thread_meta( $thread_id, 'chat_id', $chat_id );
        }

        return $thread_id;
    }
}

function BP_Better_Messages_Chats()
{
    return BP_Better_Messages_Chats::instance();
}