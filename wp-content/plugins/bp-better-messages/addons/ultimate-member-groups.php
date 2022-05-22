<?php
defined( 'ABSPATH' ) || exit;

if ( !class_exists( 'Better_Messages_Ultimate_Member_Groups' ) ){

    class Better_Messages_Ultimate_Member_Groups
    {

        public static function instance()
        {

            static $instance = null;

            if (null === $instance) {
                $instance = new Better_Messages_Ultimate_Member_Groups();
            }

            return $instance;
        }

        public function __construct(){
            if(  BP_Better_Messages()->settings['UMenableGroups'] === '1' ) {
                add_filter('um_groups_tabs', array($this, 'add_group_tab'), 20, 3);
                add_action('um_groups_single_page_content__messages', array($this, 'group_tab_content'));
                add_action('update_post_meta', array( $this, 'catch_members_update' ), 10 , 4);


                if (BP_Better_Messages()->settings['UMenableGroupsFiles'] === '0') {
                    add_action('bp_better_messages_user_can_upload_files', array($this, 'disable_upload_files'), 10, 3);
                }
            }
        }

        public function catch_members_update( $meta_id, $object_id, $meta_key, $_meta_value ){
            $query = 'um_groups_members_count_';

            if( substr($meta_key, 0, strlen($query)) === $query ){
                $group = get_post( $object_id );

                if( $group->post_type === 'um_groups' ) {
                    $this->on_something_changed( $group->ID );
                }
            }

        }

        public function on_something_changed( $group_id, $user_id = false ){
            $thread_id = $this->get_group_thread_id( $group_id );
            $this->sync_thread_members( $thread_id );
        }

        public function disable_upload_files( $can_upload, $user_id, $thread_id ){
            if( BP_Better_Messages()->functions->get_thread_type( $thread_id ) === 'group' ) {
                return false;
            }

            return $can_upload;
        }

        public function add_group_tab( $tabs , $group_id, $param_tab ){
            if( $this->user_has_access( $group_id, get_current_user_id() ) ) {
                $tabs['messages'] = [
                    'slug' => 'messages',
                    'name' => _x('Messages', 'Ultimate Member - Group Tab Name', 'bp-better-messages')
                ];
            }

            return $tabs;
        }

        public function group_tab_content( $um_group_id ){
            if( $this->user_has_access( $um_group_id, get_current_user_id() ) ) {
                echo $this->get_group_page( $um_group_id );
            }
        }


        public function user_has_access( $group_id, $user_id ){
            $thread_id = $this->get_group_thread_id( $group_id );
            return BP_Better_Messages()->functions->check_access( $thread_id, $user_id );
        }

        public function is_group_messages_enabled( $um_group_id ){
            return 'enabled';
        }

        public function get_group_page( $group_id ){
            if (defined('WP_DEBUG') && true === WP_DEBUG) {
                // some debug to add later
            } else {
                error_reporting(0);
            }

            remove_filter('bp_better_messages_can_send_message', array( BP_Better_Messages_Bulk(), 'disabled_thread_reply' ), 10);
            global $bpbm_errors;
            $bpbm_errors = [];
            do_action('bp_better_messages_before_generation');

            $path = apply_filters('bp_better_messages_views_path', BP_Better_Messages()->path . '/views/');

            $thread_id = $this->get_group_thread_id( $group_id );

            $is_mini = isset($_GET['mini']);

            $template = 'layout-um-group.php';

            if( ! current_user_can('manage_options') ) {
                if ( ! $this->user_has_access($group_id, get_current_user_id()) ) {
                    $thread_id = false;

                    $bpbm_errors[] = __('Access restricted', 'bp-better-messages');

                    if ($is_mini) {
                        wp_send_json($bpbm_errors, 403);
                    }

                    $template = 'layout-index.php';
                }
            }

            $disable_admin_mode = true;

            ob_start();

            $template = apply_filters( 'bp_better_messages_current_template', $path . $template, $template );

            do_action('bp_better_messages_before_main_template_rendered');

            if( ! BP_Better_Messages()->functions->is_ajax() && count( $bpbm_errors ) > 0 ) {
                echo '<p class="bpbm-notice">' . implode('</p><p class="bpbm-notice">', $bpbm_errors) . '</p>';
            }

            if($template !== false) {
                BP_Better_Messages()->functions->pre_template_include();
                include($template);
                BP_Better_Messages()->functions->after_template_include();
            }

            do_action('bp_better_messages_after_main_template_rendered');

            if( isset($thread_id) && is_int($thread_id)  && ! isset($_GET['mini']) ){
                BP_Better_Messages()->functions->messages_mark_thread_read( $thread_id );
                //update_user_meta(get_current_user_id(), 'bpbm-last-seen-thread-' . $thread_id, time());
            }

            $content = ob_get_clean();
            $content = str_replace('loading="lazy"', '', $content);

            $content = BP_Better_Messages()->functions->minify_html( $content );

            do_action('bp_better_messages_after_generation');

            return $content;
        }

        public function user_can_moderate( $group_id, $user_id ){
            return UM()->Groups()->api()->can_moderate_posts( $group_id, $user_id );
        }

        public function get_group_thread_id( $group_id ){
            global $wpdb;

            $thread_id = (int) $wpdb->get_var( $wpdb->prepare( "
            SELECT bpbm_threads_id 
            FROM `" . bpbm_get_table('threadsmeta') . "` 
            WHERE `meta_key` = 'um_group_id' 
            AND   `meta_value` = %s
            ", $group_id ) );

            $recipients_count = (int) $wpdb->get_var($wpdb->prepare("SELECT COUNT(*)  FROM `" . bpbm_get_table('recipients') . "` WHERE `thread_id` = %d", $thread_id));

            if( $recipients_count === 0 ){
                $thread_id = false;
            }

            if( ! $thread_id ) {
                $wpdb->query( $wpdb->prepare( "
                DELETE  
                FROM `" . bpbm_get_table('threadsmeta') . "` 
                WHERE `meta_key` = 'um_group_id' 
                AND   `meta_value` = %s
                ", $group_id ) );

                $group = get_post( $group_id );

                $last_thread = intval($wpdb->get_var("SELECT MAX(thread_id) FROM `" . bpbm_get_table('messages') . "`;"));
                $thread_id = $last_thread + 1;

                $wpdb->insert(
                    bpbm_get_table('messages'),
                    array(
                        'sender_id' => 0,
                        'thread_id' => $thread_id,
                        'subject'   => esc_html( $group->post_title ),
                        'message'   => '<!-- BBPM START THREAD -->'
                    )
                );

                BP_Better_Messages()->functions->update_thread_meta( $thread_id, 'um_group_thread', true );
                BP_Better_Messages()->functions->update_thread_meta( $thread_id, 'um_group_id', $group_id );

                $this->sync_thread_members( $thread_id );
            }

            return $thread_id;
        }

        public function get_groups_members( $group_id ){
            global $wpdb;

            $table = "{$wpdb->prefix}um_groups_members";

            $query = $wpdb->prepare("
                SELECT `user_id1` 
                FROM {$table} 
                WHERE `group_id` = %d
                AND `status` = 'approved'
                AND `role` IN ('member', 'moderator', 'admin')
            ", $group_id);

            return $wpdb->get_col( $query );
        }

        public function sync_thread_members( $thread_id ){
            wp_cache_delete( 'thread_recipients_' . $thread_id, 'bp_messages' );
            wp_cache_delete( 'bm_thread_recipients_' . $thread_id, 'bp_messages' );
            $group_id = BP_Better_Messages()->functions->get_thread_meta( $thread_id, 'um_group_id' );

            $members = $this->get_groups_members( $group_id );

            if( count($members) === 0 ) {
                return false;
            }

            global $wpdb;
            $array     = [];
            /**
             * All users ids in thread
             */
            $recipients = BP_Messages_Thread::get_recipients_for_thread( $thread_id );

            foreach( $members as $index => $user_id ){
                if( isset( $recipients[$user_id] ) ){
                    unset( $recipients[$user_id] );
                    continue;
                }

                $array[] = [
                    $user_id,
                    $thread_id,
                    0,
                    0,
                    0,
                ];
            }

            if( count($array) > 0 ) {
                $sql = "INSERT INTO " . bpbm_get_table('recipients') . "
                (user_id, thread_id, unread_count, sender_only, is_deleted)
                VALUES ";

                $values = [];

                foreach ($array as $item) {
                    $values[] = $wpdb->prepare( "(%d, %d, %d, %d, %d)", $item );
                }

                $sql .= implode( ',', $values );

                $wpdb->query( $sql );
            }

            if( count($recipients) > 0 ) {
                foreach ($recipients as $user_id => $recipient) {
                    global $wpdb;
                    $wpdb->delete( bpbm_get_table('recipients'), [
                        'thread_id' => $thread_id,
                        'user_id'   => $user_id
                    ], ['%d','%d'] );
                }
            }

            BP_Better_Messages()->hooks->clean_thread_cache( $thread_id );

            return true;
        }

    }
}

