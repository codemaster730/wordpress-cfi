<?php
defined( 'ABSPATH' ) || exit;

if ( !class_exists( 'BP_Better_Messages_Hooks' ) ):

    class BP_Better_Messages_Hooks
    {

        public static function instance()
        {

            static $instance = null;

            if ( null === $instance ) {
                $instance = new BP_Better_Messages_Hooks();
            }

            return $instance;
        }

        public function __construct()
        {
            add_action( 'admin_init',     array( $this, 'update_db_if_needed' ) );
            add_action( 'bp_init',        array( $this, 'redirect_standard_component' ) );
            add_action( 'admin_bar_menu', array( $this, 'remove_standard_topbar' ), 999 );

            add_filter( 'bp_nouveau_get_members_buttons',   array( $this, 'pm_link_nouveau' ), 20, 3);
            add_filter( 'bp_get_send_private_message_link', array( $this, 'pm_link' ), 20, 1 );
            add_filter( 'yz_get_send_private_message_url',  array( $this, 'pm_link' ), 20, 1 );
            add_filter( 'bp_get_send_message_button_args',  array( $this, 'pm_link_args'), 20, 1 );


            if( BP_Better_Messages()->settings['userListButton'] == '1' ) {
                add_action('bp_directory_members_actions', array($this, 'pm_link_legacy'), 10);
            }

            add_filter( 'bp_get_message_thread_view_link',  array( $this, 'thread_link' ), 20, 2 );

            add_filter( 'cron_schedules', array( $this, 'cron_intervals' ) );

            add_action('pre_get_posts', array($this, 'exclude_attachments_global'));

            if( BP_Better_Messages()->settings['attachmentsHide'] === '1' ) {
                add_action('ajax_query_attachments_args', array($this, 'exclude_attachments'));
            }

            add_action( 'wp_head', array( $this, 'themes_adaptation' ) );

            if( BP_Better_Messages()->settings['chatPage'] !== '0' ){
                add_filter( 'the_content', array( $this, 'chat_page' ), 12 );
            }

            add_action( 'admin_notices', array( $this, 'admin_notice') );
            if( BP_Better_Messages()->settings['fastStart'] == '1' ) {
                add_action('template_redirect', array($this, 'catch_fast_thread'));
            }

            if( BP_Better_Messages()->settings['friendsMode'] == '1' && function_exists('friends_check_friendship') ) {
                add_filter( 'bp_better_messages_can_send_message', array( $this, 'disable_non_friends_reply' ), 10, 3);
                add_action( 'bp_better_messages_before_new_thread', array( $this, 'disable_start_thread_for_non_friends' ), 10, 2 );
            }

            if( BP_Better_Messages()->settings['allowUsersRestictNewThreads'] === '1' ){
                add_filter( 'bp_better_messages_before_new_thread', array( $this, 'disable_start_thread_if_user_restricted' ), 10, 2);
            }

            /*
             * Block, Suspend, Report for BuddyPress integration
             */
            add_filter('bp_better_messages_can_send_message',         array($this, 'disable_message_for_blocked_user'), 10, 3);
            #add_filter('bp_better_messages_thread_expanding_buttons', array($this, 'block_user_button'), 20, 5);

            if( isset(BP_Better_Messages()->settings['restrictNewReplies'])
                && is_array(BP_Better_Messages()->settings['restrictNewReplies'])
                && count(BP_Better_Messages()->settings['restrictNewReplies']) > 0
            ) {
                add_filter( 'bp_better_messages_can_send_message', array( $this, 'disable_message_for_blocked_restricted_role' ), 10, 3);
            }

            if( isset(BP_Better_Messages()->settings['restrictNewThreads'])
                && is_array(BP_Better_Messages()->settings['restrictNewThreads'])
                && count(BP_Better_Messages()->settings['restrictNewThreads']) > 0
            ) {
                add_action( 'bp_better_messages_before_new_thread', array( $this, 'disable_thread_for_blocked_restricted_role' ), 10, 2);

                if( BP_Better_Messages()->settings['restrictNewThreadsRemoveNewThreadButton'] === '1' ){
                    $this->disable_new_thread_button_if_needed();
                }
            }

            if( isset(BP_Better_Messages()->settings['restrictViewMessages'])
                && is_array(BP_Better_Messages()->settings['restrictViewMessages'])
                && count(BP_Better_Messages()->settings['restrictViewMessages']) > 0
            ) {
                add_filter( 'bp_better_messages_after_format_message', array( $this, 'hide_message_for_restricted_role' ), 10, 4 );
                add_filter( 'bp_better_messages_fast', array( $this, 'disable_fast_mode' ), 10, 1 );
            }

            if( isset(BP_Better_Messages()->settings['restrictRoleBlock'])
                && is_array(BP_Better_Messages()->settings['restrictRoleBlock'])
                && count(BP_Better_Messages()->settings['restrictRoleBlock']) > 0
            ) {
                add_filter( 'bp_better_messages_can_send_message',  array( $this, 'disable_replied_role_to_role_block' ), 10, 3);
                add_filter( 'bp_better_messages_before_new_thread', array( $this, 'disable_start_thread_role_to_role_block' ), 10, 2);
            }


            if( BP_Better_Messages()->settings['singleThreadMode'] == '1' ) {
                add_action( 'bp_better_messages_before_new_thread', array( $this, 'disable_start_thread_if_thread_exist' ), 10, 2 );
            }

            if( BP_Better_Messages()->settings['disableGroupThreads'] == '1' ) {
                add_action( 'bp_better_messages_before_new_thread', array( $this, 'disable_group_threads' ), 10, 2 );
            }

            if( BP_Better_Messages()->settings['mechanism'] == 'websocket' && BP_Better_Messages()->settings['messagesStatus'] == '0'){
                add_action( 'wp_head', array( $this, 'disableStatuses'), 1 );
                add_action( 'bm_app_head', array( $this, 'disableStatuses' ) );
            }

            add_action( 'template_redirect', array( $this, 'update_last_activity' ) );

            // I have 0 idea why this fixes messages link when using Youzer plugin
            add_action( 'init', array( $this, 'fix_youzer' ) );

            add_action( 'bp_screens', array( $this, 'fix_404' ) );

            /*
             * BuddyBoss trying to fix
             */
            add_action('wp_enqueue_scripts', array( $this, 'inbox_counter_javascript' ) );
            add_filter('messages_thread_get_inbox_count', array( $this, 'replace_unread_count' ), 10, 2 );
            add_filter('bp_messages_thread_current_threads', array( $this, 'buddyboss_notifications_fix' ), 10, 1 );

            add_action( 'wp_footer', array( $this, 'mobile_popup_button') );

            /*
             * BeeHive premium theme integration
             * https://www.wordplus.org/beehive
            */
            add_action('wp_head', array( $this, 'beehive_theme_integration' ), 100 );
            add_action('wp_head', array( $this, 'max_height_css' ), 100 );

            add_action( 'bp_core_user_updated_last_activity', array( $this, 'override_last_activity_2' ), 10, 2 );
            add_filter( 'update_user_metadata', array( $this, 'override_last_activity' ), 1, 5 );

            add_action( 'wp_head', array( $this, 'css_customizations' ) );
            add_action( 'bm_app_head', array( $this, 'css_customizations' ) );

            /**
             * PMPRO Access
             */
            if( defined('PMPROBP_DIR') && function_exists('pmpro_bp_user_can') ){
                add_action( 'bp_better_messages_before_new_thread', array( $this, 'disable_thread_for_pmpro_restricted_role' ), 10, 2);
                add_filter( 'bp_better_messages_can_send_message', array( $this, 'disable_messages_for_pmpro_restricted_role' ), 10, 3);
            }


            add_action('admin_head', array($this, 'hide_admin_counter' ) );

            add_filter( 'bp_messages_allowed_tags', array($this, 'allow_additional_tags'), 10, 1 );

            add_filter( 'bp_better_messages_can_send_message', array( $this, 'disable_message_to_deleted_users' ), 10, 3);

            /**
             * https://wordpress.org/plugins/asgaros-forum/
             */
            if( class_exists('AsgarosForum') ){
                add_action('asgarosforum_custom_profile_menu',        array( $this, 'asragaros_profile_link'));
                if( apply_filters('asgarosforum_filter_show_header', true) ){
                    add_action('asgarosforum_content_header', array( $this, 'asragaros_profile_messages'));
                } else {
                    add_action('asgarosforum_profile_custom_content_top', array( $this, 'asragaros_profile_messages'));
                }

                add_action('asgarosforum_after_post_author', array( $this, 'asragaros_thread_view'), 10, 2);
            }

            /**
             * BBPress
             */
            if( class_exists('bbPress') ){
                if( BP_Better_Messages()->settings['bbPressAuthorDetailsLink'] === '1' ) {
                    add_action('bbp_theme_after_reply_author_details', array($this, 'pm_link_bbpress'));
                }
                #add_action( 'bbp_template_after_user_details_menu_items', array( $this, 'show_in_bbpress_profile' ) );
            }

            /**
             * BP_Verified_Member
             */
            if( class_exists('BP_Verified_Member') ){
                add_filter('bp_better_messages_full_chat_username', array( $this, 'verified_member_badge' ), 10, 3 );
                add_filter('bp_better_messages_mini_chat_username', array( $this, 'verified_member_badge' ), 10, 3 );
                add_filter('bp_better_messages_thread_displayname', array( $this, 'verified_member_badge' ), 10, 3 );
            }

            if( defined('BP_PLATFORM_VERSION') ) {
                add_filter('bp_better_messages_after_format_message', array($this, 'buddyboss_group_messages'), 10, 4);
                add_filter('heartbeat_received', array($this, 'heartbeat_unread_notifications'), 12);


                if (BP_Better_Messages()->settings['replaceBuddyBossHeader'] === '1') {
                    add_action('wp_ajax_buddyboss_theme_get_header_unread_messages', array($this, 'buddyboss_theme_get_header_unread_messages'), 9);
                }
            }

            /**
             * BuddyBoss moderation
             */
            if( function_exists('bp_is_moderation_member_blocking_enable') ){
                $bb_blocking_enabled = bp_is_moderation_member_blocking_enable();
                if( $bb_blocking_enabled ){
                    add_filter( 'bp_better_messages_can_send_message', array($this, 'buddyboss_disable_message_to_blocked'), 10, 3);
                }
            }

            if( function_exists('bb_access_control_member_can_send_message') ) {
                add_filter( 'bp_better_messages_can_send_message', array($this, 'buddyboss_blocked_message'), 10, 3);
            }

            if ( class_exists( 'myCRED_BP_Charge_Messaging' ) ){
                add_filter( 'bp_better_messages_can_send_message',  array( $this, 'mycred_bp_charge_message'), 10, 3);
                add_action( 'messages_message_sent',                array( $this, 'mycred_bp_charge_for_message' ) );
                add_action( 'bp_better_messages_before_new_thread', array( $this, 'mycred_bp_charge_for_new_thread' ), 10, 2 );
            }

            if ( class_exists( 'myCRED_Core' ) ){
                add_filter( 'bp_better_messages_can_send_message',  array( $this, 'mycred_core_charge_message'), 10, 3);
                add_action( 'messages_message_sent',                array( $this, 'mycred_core_charge_for_message' ) );
                add_action( 'bp_better_messages_new_thread_created', array( $this, 'mycred_core_charge_new_thread_created'), 10, 2 );
                add_action( 'bp_better_messages_before_new_thread', array( $this, 'mycred_core_charge_for_new_thread' ), 10, 2 );
            }

            if( BP_Better_Messages()->settings['allowMuteThreads'] === '1' ) {
                add_filter('bp_better_messages_thread_expanding_buttons', array($this, 'mute_thread_button'), 10, 5);

                /**
                 * Remove standard Email & Notifications
                 */
                add_filter('bp_email_validate', array($this, 'mute_thread_remove_standard_email'), 10, 2);
                add_action('bp_notification_after_save', array( $this, 'mute_thread_delete_notification'), 10, 1 );
            }

            if( BP_Better_Messages()->settings['rateLimitNewThread'] > 0 ){
                add_action( 'bp_better_messages_before_new_thread',  array( $this, 'new_thread_rate_limit' ), 10, 2 );
                add_action( 'bp_better_messages_new_thread_created', array( $this, 'new_thread_record_last_thread_time' ), 10, 2 );
            }

            add_action( 'bp_better_messages_before_message_send',  array( $this, 'new_replies_rate_limit' ), 10, 2 );
            add_filter( 'bp_better_messages_can_send_message', array( $this, 'disable_message_for_new_replies_rate_limited' ), 10, 3 );

            add_filter( 'bp_better_messages_thread_expanding_buttons', array( $this, 'group_thread_buttons' ), 10, 5 );

            if( BP_Better_Messages()->settings['enableReplies'] === '1' ) {
                add_filter('bp_better_messages_after_format_message', array($this, 'reply_message_formatting'), 101, 4);
            }

            if( ! empty( BP_Better_Messages()->settings['badWordsList'] ) ){
                add_action( 'bp_better_messages_before_new_thread',  array( $this, 'disable_new_thread_with_bad_word' ), 10, 2 );
                add_filter( 'bp_better_messages_before_message_send', array( $this, 'disable_reply_with_bad_word' ), 10, 2 );
            }

            add_filter('bp_better_messages_thread_expanding_buttons', array($this, 'delete_thread_button'), 11, 5);
            add_filter('bp_better_messages_thread_expanding_buttons', array($this, 'clear_thread_button'), 10, 5);

            if( class_exists('WooCommerce') && BP_Better_Messages()->settings['chatPage'] === 'woocommerce' ) {
                add_action( 'init', array( $this, 'woocommerce_add_messages_endpoint' ) );
                add_filter( 'query_vars', array( $this, 'woocommerce_messages_query_vars' ), 0 );

                $slug = BP_Better_Messages()->settings['bpProfileSlug'];
                add_filter( 'woocommerce_account_menu_items', array( $this, 'woocommerce_add_messages_link_my_account' ) );
                add_action( 'woocommerce_account_' . $slug . '_endpoint', array( $this, 'woocommerce_messages_content' ) );
            }

            /**
             * Youzify button
             */
            add_filter( 'youzify_get_send_message_button', array( $this, 'modify_youzify_button' ) );

            /**
             * Smart caching hooks
             */
            add_action( 'messages_message_sent', array( $this, 'on_message_sent' ), 1000 );
            add_action( 'profile_update', array( $this, 'on_user_update' ), 1000, 2 );
            add_action( 'bp_better_chat_settings_updated', array( $this, 'settings_updated' ), 1000, 1 );

            add_action('bp_better_messages_thread_pre_header', array( $this, 'full_screen_button' ), 10, 4);

            if( defined('ultimatemember_version') ){
                require_once BP_Better_Messages()->path . 'addons/ultimate-member.php';
                BP_Better_Messages_Ultimate_Member::instance();
            }

            if( BP_Better_Messages()->settings['allowUsersBlock'] === '1' ) {
                require_once BP_Better_Messages()->path . 'addons/users-block.php';
                BP_Better_Messages_Block_Users::instance();
            }

            if( BP_Better_Messages()->settings['enableReactions'] === '1' ) {
                BP_Better_Messages_Reactions::instance();
            }

            #if( BP_Better_Messages()->settings['enablePinnedMessage'] === '1' ) { 
                #require_once BP_Better_Messages()->path . 'addons/pinned-message.php';
                #BP_Better_Messages_Pinned_Message::instance();
            #}

            if( class_exists( 'PeepSo' ) ) {
                require_once BP_Better_Messages()->path . 'addons/peepso.php';
                BP_Better_Messages_Peepso::instance();
            }

            add_action('init', array( $this, 'flush_rewrite_rules' ) );

            if( defined('AUTOPTIMIZE_PLUGIN_VERSION') ) {
                add_filter('autoptimize_filter_js_exclude', array($this, 'autoptimize_filter_js_exclude'), 10, 1);
            }

            add_action('bp_better_messages_thread_pre_header', array( $this, 'participants_counter' ), 10, 4);
        }

        public function participants_counter( $thread_id, $participants, $is_mini, $type = 'thread' ){
            if( $is_mini ) return false;
            if( $type !== 'thread' ) return false;

            $showParticipants = false;

            if( $participants['count'] > 2 ) {
                $showParticipants = true;
            } else {
                if( current_user_can('manage_options') && ! isset( $participants['users'][ get_current_user_id() ] ) ){
                    $showParticipants = true;
                }
            }

            if( $showParticipants ) {
                echo ' <a href="#" class="participants" title="' . __('Participants', 'bp-better-messages') . '"><i class="fas fa-users"></i> ' . $participants['count'] . '</a>';
            }
        }

        public function autoptimize_filter_js_exclude( $exclude ){
            return $exclude . ", bpbm-livekit";
        }

        public function modify_youzify_button($args){
            $args['link_href'] = $this->pm_link();
            return $args;
        }

        public function flush_rewrite_rules(){
            if( ! is_admin() ) return false;
            global $wp_rewrite;

            $is_updated = get_option( 'bp-better-chat-settings-updated', false );

            if( $is_updated ) {
                flush_rewrite_rules();
                $wp_rewrite->init();
                delete_option( 'bp-better-chat-settings-updated' );
            }
        }

        public function disable_start_thread_role_to_role_block(&$args, &$errors){
            $recipients = $args['recipients'];
            if( ! is_array( $recipients ) ) return false;
            if( count($recipients) === 0 ) return false;

            $restricted_roles = (array) BP_Better_Messages()->settings['restrictRoleBlock'];
            $user             = wp_get_current_user();
            $user_roles       = (array) $user->roles;

            $restrict_to = [];
            foreach( $restricted_roles as $restricted_role ){
                if( in_array($restricted_role['from'], $user_roles) ){
                    $restrict_to[ $restricted_role['to'] ] = $restricted_role['message'];
                }
            }

            $is_restricted = false;
            $restricted_message = false;

            if( count( $restrict_to ) > 0 ) {
                foreach ($recipients as $recipient) {
                    $recipient_userdata = get_user_by('slug', $recipient);
                    $recipient_roles = $recipient_userdata->roles;

                    foreach( $recipient_roles as $recipient_role ){
                        if( isset( $restrict_to[$recipient_role] ) ){
                            $is_restricted = true;
                            $restricted_message = $restrict_to[$recipient_role];
                        }
                    }
                }
            }

            if( $is_restricted ) {
                $errors[] = $restricted_message;
            }

        }

        public function disable_replied_role_to_role_block( $allowed, $user_id, $thread_id ){
            $restricted_roles = (array)  BP_Better_Messages()->settings['restrictRoleBlock'];
            $user             = wp_get_current_user();
            $user_roles       = (array) $user->roles;

            $restrict_to = [];
            foreach( $restricted_roles as $restricted_role ){
                if( in_array($restricted_role['from'], $user_roles) ){
                    $restrict_to[ $restricted_role['to'] ] = $restricted_role['message'];
                }
            }

            $is_restricted = false;
            $restricted_message = false;

            if( count( $restrict_to ) > 0 ){
                $group_id = BP_Better_Messages()->functions->get_thread_meta( $thread_id, 'group_id');

                /**
                 * Disable restrictions for group threads
                 */
                if( ! empty( $group_id ) ) {
                    return $allowed;
                }

                /**
                 * Disable restrictions for chat threads
                 */
                $chat_id = BP_Better_Messages()->functions->get_thread_meta( $thread_id, 'chat_id' );
                if( ! empty( $chat_id ) ) {
                    return $allowed;
                }

                $recipients = BP_Messages_Thread::get_recipients_for_thread( $thread_id );

                foreach( $recipients as $recipient ){
                    if( $recipient->user_id === $user->ID ) continue;

                    $recipient_userdata = get_userdata( $recipient->user_id );
                    if( ! $recipient_userdata ) continue;

                    $recipient_roles = $recipient_userdata->roles;

                    foreach( $recipient_roles as $recipient_role ){
                        if( isset( $restrict_to[$recipient_role] ) ){
                            $is_restricted = true;
                            $restricted_message = $restrict_to[$recipient_role];
                        }
                    }

                }
            }

            if( $is_restricted ) {
                $allowed = false;
                global $bp_better_messages_restrict_send_message;
                $bp_better_messages_restrict_send_message['role_block_restricted'] = $restricted_message;
            }

            return $allowed;
        }

        public function full_screen_button( $thread_id, $participants, $is_mini, $type = 'thread' ){
            if( $is_mini || ! is_user_logged_in() ) return;
            $full_screen_enabled = BP_Better_Messages()->settings['desktopFullScreen'] === '1';
            if( ! $full_screen_enabled ) return;
            echo '<a href="#" class="bpbm-maximize" title="' . __('Maximize', 'bp-better-messages') . '"><i class="fas fa-expand-alt"></i></a>';
            echo '<a href="#" class="bpbm-minimize" title="' . __('Minimize', 'bp-better-messages') . '" style="display: none"><i class="fas fa-compress-alt"></i></a>';
        }

        public function clean_thread_cache( $thread_id ){
            $cache_enabled = BP_Better_Messages()->settings['smartCache'] === '1';

            if( $cache_enabled ) {
                global $wpdb;
                $user_ids = $wpdb->get_col($wpdb->prepare("SELECT user_id FROM " . bpbm_get_table('recipients') . " WHERE thread_id = %d AND is_deleted = 0", $thread_id));

                foreach ($user_ids as $user_id) {
                    delete_user_meta($user_id, 'bpbm_threads_cache');
                }
            }

            wp_cache_delete( 'thread_recipients_' . $thread_id, 'bp_messages' );
            wp_cache_delete( 'bm_thread_recipients_' . $thread_id, 'bp_messages' );
        }

        public function settings_updated( $settings ){
            $cache_enabled = $settings['smartCache'] === '1';

            if( ! $cache_enabled ) {
                global $wpdb;
                $wpdb->query("DELETE FROM " . bpbm_get_table('threadsmeta') . " WHERE `meta_key` = 'participants_cache'");
                $wpdb->query("DELETE FROM " . $wpdb->usermeta . " WHERE `meta_key` = 'bpbm_threads_cache'");
            }

        }

        public function on_user_update( $user_id, $old_user_data = false ){
            $cache_enabled = BP_Better_Messages()->settings['smartCache'] === '1';

            if( $cache_enabled ) {
                global $wpdb;

                $wpdb->query($wpdb->prepare("DELETE FROM " . bpbm_get_table('threadsmeta') . "
                WHERE `bpbm_threads_id` IN(
                    SELECT `thread_id` 
                    FROM " . bpbm_get_table('recipients') . " 
                    WHERE `user_id` = %d
                )
                AND `meta_key` = 'participants_cache'", $user_id));
            }
        }

        public function on_message_sent( $message ){
            $cache_enabled = BP_Better_Messages()->settings['smartCache'] === '1';

            if( $cache_enabled ) {
                // All recipients
                $dummy_recipients = array();

                foreach ($message->recipients as $recipient) {
                    if (is_object($recipient)) {
                        $dummy_recipients[$recipient->user_id] = $recipient->user_id;
                    } else {
                        $dummy_recipients[$recipient] = $recipient;
                    }
                }

                $dummy_recipients[$message->sender_id] = $message->sender_id;

                foreach ($dummy_recipients as $recipient) {
                    delete_user_meta($recipient, 'bpbm_threads_cache');
                }
            }
        }

        public function woocommerce_add_messages_endpoint(){
            $page = get_option('woocommerce_myaccount_page_id');
            $page = get_post($page);

            $slug = BP_Better_Messages()->settings['bpProfileSlug'];

            if( !! $page ) {
                add_rewrite_endpoint($slug, EP_ROOT | EP_PAGES);
            }
        }

        public function woocommerce_messages_query_vars( $vars ){
            $slug = BP_Better_Messages()->settings['bpProfileSlug'];
            $vars[] = $slug;
            return $vars;
        }

        public function woocommerce_add_messages_link_my_account( $items ){
            $slug = BP_Better_Messages()->settings['bpProfileSlug'];
            $label = __('Messages', 'bp-better-messages');

            if( isset( $items['customer-logout']) ) {
                $items = BP_Better_Messages()->functions->array_insert_before('customer-logout', $items, $slug, $label);
            } else {
                $items['bp-messages'] = $label;
            }

            return $items;
        }

        public function woocommerce_messages_content(){
            echo BP_Better_Messages()->functions->get_page( true );
        }

        public function reply_message_formatting( $message, $message_id, $context, $user_id ){
            if( $context !== 'stack' ) return $message;
            $is_reply = strpos( $message, '<!-- BPBM REPLY -->' ) !== false;
            if( ! $is_reply ) return $message;

            $reply_message_id = (int) bp_messages_get_meta( $message_id, 'reply_to_message_id', true );

            if( ! $reply_message_id ) return $message;

            global $wpdb;
            $reply_message = $wpdb->get_row($wpdb->prepare("SELECT sender_id, message FROM " . bpbm_get_table('messages') . " WHERE `id` = %d", $reply_message_id));
            if( ! $reply_message ) return $message;

            $sender_id       = $reply_message->sender_id;
            $message_content = str_replace('<!-- BPBM REPLY -->', '', $reply_message->message);

            #remove_filter( 'bp_better_messages_after_format_message', array( BP_Better_Messages()->urls, 'nice_links' ), 100 );
            $message_content = BP_Better_Messages()->functions->format_message($message_content, $reply_message_id, 'stack', $user_id);
            #add_filter( 'bp_better_messages_after_format_message', array( BP_Better_Messages()->urls, 'nice_links' ), 100, 4 );

            $sender = get_userdata( $sender_id );
            if( ! $sender ) {
                $display_name = __('Deleted User', 'bp-better-messages');
            } else {
                $display_name = esc_attr( $sender->display_name );
            }

            $newMessage  = '<span class="bpbm-replied-message" data-reply-message-id="' . $reply_message_id . '">';
            $newMessage .= '<span class="bpbm-replied-message-name">' . $display_name . '</span>';
            $newMessage .= '<span class="bpbm-replied-message-text">' . $message_content . '</span>';
            $newMessage .= '</span>';
            $newMessage .= '<span class="bpbm-replied-message-reply">' . $message . '</span>';

            return $newMessage;
        }

        public function new_thread_record_last_thread_time($sent, $bpbm_last_message_id){
            $user_id = get_current_user_id();
            update_user_meta( $user_id, 'bpbm_last_thread_created', time() );
        }

        public function new_replies_rate_limit( &$args, &$errors ){
            global $wpdb;
            $sender_id = (isset($args['sender_id'])) ? $args['sender_id'] : bp_loggedin_user_id();
            $rate_limit_reply = (array) BP_Better_Messages()->settings['rateLimitReply'];

            $sender           = get_userdata( $sender_id );
            $user_roles       = (array) $sender->roles;

            foreach( $user_roles as $user_role ){

                if( isset($rate_limit_reply[$user_role]) ) {
                    $value = (int) $rate_limit_reply[$user_role]['value'];
                    if( $value === 0 ) continue;

                    $type  = $rate_limit_reply[$user_role]['type'];

                    $timeAgo = 0;
                    switch ( $type ){
                        case 'hour':
                            $timeAgo = 3600;
                            break;
                        case 'day':
                            $timeAgo = 3600 * 24;
                            break;
                    }

                    $since = time() - $timeAgo;
                    $datetime = date('Y-m-d H:i:s', $since);

                    $count = $wpdb->get_var($wpdb->prepare( "
                        SELECT COUNT(*)
                        FROM " . bpbm_get_table('messages') . "
                        WHERE `sender_id`  = %d
                        AND   `date_sent`  > %s
                    ", $sender->ID, $datetime ));

                    if( $count >= $value ){
                        $errors['restrictRateLimitReply'] = BP_Better_Messages()->settings['rateLimitReplyMessage'];
                        break;
                    }
                }
            }

        }

        public function disable_new_thread_with_bad_word( &$args, &$errors ){
            $user_id = get_current_user_id();

            $words_list = array_map('trim', explode("\n", BP_Better_Messages()->settings['badWordsList']));

            $contains_word = false;

            $message = strip_tags($args['content']);

            foreach($words_list as $word) {
                if (stripos($message, $word) !== false) {
                    $contains_word = true;
                    break;
                }
            }

            if( $contains_word ) {
                $errors['restrictBadWord'] = BP_Better_Messages()->settings['restrictBadWordsList'];
            }
        }

        public function disable_reply_with_bad_word( &$args, &$errors ){
            $words_list = array_map('trim', explode("\n", BP_Better_Messages()->settings['badWordsList']));

            $contains_word = false;

            $message = strip_tags($args['content']);

            foreach($words_list as $word) {
                if (stripos($message, $word) !== false) {
                    $contains_word = true;
                    break;
                }
            }

            if( $contains_word ) {
                $errors['restrictBadWord'] = BP_Better_Messages()->settings['restrictBadWordsList'];
            }
        }

        public function disable_message_for_new_replies_rate_limited( $allowed, $user_id, $thread_id ){
            global $wpdb;
            $user             = get_userdata($user_id);

            if( ! $user ) {
                return $allowed;
            }

            $rate_limit_reply = (array) BP_Better_Messages()->settings['rateLimitReply'];
            $user_roles       = (array) $user->roles;

            $is_restricted = false;
            foreach( $user_roles as $user_role ){

                if( isset($rate_limit_reply[$user_role]) ) {
                    $value = (int) $rate_limit_reply[$user_role]['value'];
                    if( $value === 0 ) continue;

                    $type  = $rate_limit_reply[$user_role]['type'];

                    $timeAgo = 0;
                    switch ( $type ){
                        case 'hour':
                            $timeAgo = 3600;
                            break;
                        case 'day':
                            $timeAgo = 3600 * 24;
                            break;
                    }

                    $since = time() - $timeAgo;
                    $datetime = date('Y-m-d H:i:s', $since);

                    $count = $wpdb->get_var($wpdb->prepare( "
                        SELECT COUNT(*)
                        FROM " . bpbm_get_table('messages') . "
                        WHERE `sender_id`  = %d
                        AND   `date_sent`  > %s
                    ", $user->ID, $datetime ));

                    if( $count >= $value ){
                        $is_restricted = true;
                        break;
                    }
                }
            }

            if( $is_restricted ) {
                $allowed = false;
                global $bp_better_messages_restrict_send_message;
                $bp_better_messages_restrict_send_message['restrictRateLimitReply'] = BP_Better_Messages()->settings['rateLimitReplyMessage'];
            }
            return $allowed;
        }

        public function new_thread_rate_limit( &$args, &$errors ){
            if( current_user_can('manage_options' ) ) return false;

            $user_id = get_current_user_id();

            $last_thread_created = (int) get_user_meta( $user_id, 'bpbm_last_thread_created', true );

            if( $last_thread_created !== 0 ){
                $rateLimit = BP_Better_Messages()->settings['rateLimitNewThread'];
                $last_thread_created_ago = time() - $last_thread_created;

                if( $last_thread_created_ago < $rateLimit ){
                    $errors['restrictNewThreadsRateLimit'] = sprintf(__('You cant start new thread now. Please wait %s seconds.', 'bp-better-messages'), $rateLimit - $last_thread_created_ago);
                }
            }
        }

        public function mute_thread_delete_notification( $notification ){
            if( $notification->component_name !== 'messages') return false;
            if( $notification->component_action !== 'new_message') return false;
            if( ! isset( $_REQUEST['thread_id'] ) ) return false;

            $thread_id = intval($_REQUEST['thread_id']);
            $user_id   = $notification->user_id;

            $muted_threads = BP_Better_Messages()->functions->get_user_muted_threads( $user_id );

            if( isset( $muted_threads[ $thread_id ] ) ){
                BP_Notifications_Notification::delete( array( 'id' => $notification->id ) );
            }

            return true;
        }

        public function mute_thread_remove_standard_email($retval, $email){
            if($email->get('type') !== 'messages-unread') return $retval;
            if( ! isset( $_REQUEST['thread_id'] ) ) return $retval;

            $user_id = $email->get_to()[0]->get_user()->ID;
            $thread_id = intval($_REQUEST['thread_id']);
            $muted_threads = BP_Better_Messages()->functions->get_user_muted_threads( $user_id );

            if( isset( $muted_threads[ $thread_id ] ) ){
                $error_code = 'messages_user_muted_thread';
                $feedback   = __( 'Your message was not sent. User muted this thread.', 'bp-better-messages' );
                return new WP_Error( $error_code, $feedback );
            }

            return $retval;
        }

        public function delete_thread_button( $buttons, $thread_id, $participants, $is_mini, $type = 'thread'){
            if( $type !== 'thread' ) return $buttons;

            $can_delete = BP_Better_Messages()->functions->can_delete_thread( get_current_user_id(), $thread_id );

            if( $can_delete ) {
                $buttons[] = '<span title="' . __('Delete thread and all messages', 'bp-better-messages') . '" class="bpbm-dropdown-item bpbm-delete-thread bpbm-can-be-hidden"><i class="fas fa-trash"></i> ' . __('Delete thread', 'bp-better-messages') . '</span>';
            }

            return $buttons;
        }

        public function clear_thread_button( $buttons, $thread_id, $participants, $is_mini, $type = 'thread'){
            $can_delete = BP_Better_Messages()->functions->can_erase_thread( get_current_user_id(), $thread_id );

            $title = __('Delete all messages in this thread', 'bp-better-messages');
            if( $type === 'chat-room' ) {
                $title = __('Delete all messages in this chat', 'bp-better-messages');
            }

            if( $can_delete ) {
                $buttons[] = '<span title="' . $title . '" class="bpbm-dropdown-item bpbm-clear-thread bpbm-can-be-hidden"><i class="fas fa-eraser"></i> ' . __('Clear messages', 'bp-better-messages') . '</span>';
            }

            return $buttons;
        }

        public function mute_thread_button( $buttons, $thread_id, $participants, $is_mini, $type = 'thread'){
            if( $type === 'chat-room' ) return $buttons;

            $muted_threads = BP_Better_Messages()->functions->get_user_muted_threads( get_current_user_id() );

            if( isset( $muted_threads[$thread_id] ) ) {
                $buttons[] = '<span title="' . __('Unmute thread notifications', 'bp-better-messages') . '" class="bpbm-dropdown-item bpbm-unmute-thread bpbm-can-be-hidden"><i class="fas fa-bell"></i> ' . __('Unmute thread', 'bp-better-messages') . '</span>';
            } else {
                $buttons[] = '<span title="' . __('Mute thread notifications', 'bp-better-messages') . '" class="bpbm-dropdown-item bpbm-mute-thread bpbm-can-be-hidden"><i class="fas fa-bell-slash"></i> ' . __('Mute thread', 'bp-better-messages') . '</span>';
            }

            return $buttons;
        }


        public function block_user_button( $buttons, $thread_id, $participants, $is_mini, $type ){
            if( ! class_exists('BPTK_Block') ) return $buttons;
            if( $type !== 'thread' ) return $buttons;
            if( count($participants['recipients']) !== 1 ) return $buttons;

            $buttons['block_user'] = '<span title="' . __('Block user', 'bp-better-messages') . '" class="bpbm-dropdown-item bpbm-block-user bpbm-can-be-hidden"><i class="fas fa-ban"></i> ' . __('Block user', 'bp-better-messages') . '</span>';

            return $buttons;
        }

        public function group_thread_buttons( $buttons, $thread_id, $participants, $is_mini, $type = 'thread' ){
            if( $type === 'chat-room' ) return $buttons;
            if( $type === 'group' ) return $buttons;
            #if( count($participants['recipients']) < 2 ) return $buttons;
            $is_moderator = BP_Better_Messages()->functions->is_thread_super_moderator( get_current_user_id(), $thread_id );

            if( count($participants['recipients']) > 1 ) {
                $allow_invite = (BP_Better_Messages()->functions->get_thread_meta($thread_id, 'allow_invite') === 'yes');

                if (!$is_mini && ($is_moderator || $allow_invite)) {
                    $buttons['invite_thread'] = '<span title="' . __('Add new participants', 'bp-better-messages') . '" class="bpbm-dropdown-item bpbm-invite-thread bpbm-can-be-hidden"><i class="fas fa-plus"></i> ' . __('Add new participants', 'bp-better-messages') . '</span>';
                }
            }

            if( count($participants['recipients']) === 1 ) {
                $allow_invite = (BP_Better_Messages()->settings['privateThreadInvite'] === '1');

                if (!$is_mini && ($is_moderator || $allow_invite)) {
                    $buttons['invite_thread'] = '<span title="' . __('Add new participants', 'bp-better-messages') . '" class="bpbm-dropdown-item bpbm-invite-thread bpbm-can-be-hidden"><i class="fas fa-plus"></i> ' . __('Add new participants', 'bp-better-messages') . '</span>';
                }
            }

            if( count($participants['recipients']) < 2 ) {
                if( BP_Better_Messages()->settings['allowGroupLeave'] === '1' && ! $is_moderator ) {
                    $buttons['leave_thread'] = '<span title="' . __('Leave thread', 'bp-better-messages') . '" class="bpbm-dropdown-item bpbm-leave-thread bpbm-can-be-hidden"><i class="fas fa-sign-out-alt"></i>' . __('Leave thread', 'bp-better-messages') . '</span>';
                }
            }

            return $buttons;
        }

        public function mycred_bp_charge_for_message( $message ){
            global $wp_filter;

            $myCRED_BP_Charge_Messaging = false;
            foreach($wp_filter[ 'mycred_init' ]->callbacks[10] as $callback){
                if( isset($callback['function'])){
                    if( isset( $callback['function'][0] ) ){
                        if( is_a($callback['function'][0], 'myCRED_BP_Charge_Messaging') ){
                            $myCRED_BP_Charge_Messaging = $callback['function'][0];
                        }
                    }
                }
            }

            if( $myCRED_BP_Charge_Messaging !== false ){
                if( doing_action('wp_ajax_bp_messages_new_thread') ){
                    $myCRED_BP_Charge_Messaging->charge_messages( $message->recipients, (int) $message->thread_id );
                } else {
                    $myCRED_BP_Charge_Messaging->charge_new_reply( (int) $message->thread_id );
                }
            }
        }

        public function mycred_bp_charge_for_new_thread( &$args, &$errors ){
            global $wp_filter;

            if( ! is_array( $args['recipients'] ) ){
                $args['recipients'] = [$args['recipients']];
            }

            $myCRED_BP_Charge_Messaging = false;
            foreach($wp_filter[ 'mycred_init' ]->callbacks[10] as $callback){
                if( isset($callback['function'])){
                    if( isset( $callback['function'][0] ) ){
                        if( is_a($callback['function'][0], 'myCRED_BP_Charge_Messaging') ){
                            $myCRED_BP_Charge_Messaging = $callback['function'][0];
                        }
                    }
                }
            }

            if( $myCRED_BP_Charge_Messaging !== false ){
                $can_afford = $myCRED_BP_Charge_Messaging->current_user_can_afford( 'new_message', count( $args['recipients'] ) );

                if( ! $can_afford ){
                    $errors['mycred_restricted'] = __('You don\'t have sufficient balance to start new thread', 'bp-better-messages');
                }
            }
        }

        public function mycred_bp_charge_message( $allowed, $user_id, $thread_id ){
            global $wp_filter;

            $myCRED_BP_Charge_Messaging = false;
            foreach($wp_filter[ 'mycred_init' ]->callbacks[10] as $callback){
                if( isset($callback['function'])){
                    if( isset( $callback['function'][0] ) ){
                        if( is_a($callback['function'][0], 'myCRED_BP_Charge_Messaging') ){
                            $myCRED_BP_Charge_Messaging = $callback['function'][0];
                        }
                    }
                }
            }

            if( $myCRED_BP_Charge_Messaging !== false ){
                $can_afford = $myCRED_BP_Charge_Messaging->current_user_can_afford( 'new_reply' );

                if( ! $can_afford ){
                    $allowed = false;
                    global $bp_better_messages_restrict_send_message;
                    $bp_better_messages_restrict_send_message['mycred_restricted'] = __('You don\'t have sufficient balance to reply', 'bp-better-messages');
                }
            }

            return $allowed;
        }

        public function mycred_core_charge_new_thread_created( $thread_id, $bpbm_last_message_id ){
            if( ! function_exists('mycred') ) {
                return false;
            }

            $user_id = get_current_user_id();

            $user_charge_rate = BP_Better_Messages()->functions->get_user_mycred_charge_rate( $user_id );
            if( $user_charge_rate === 0 ) return false;

            $amount_to_deduct = 0 - $user_charge_rate;
            $log_entry = sprintf( _x('Better Messages charge for new thread #%d', 'MyCred Log Entry', 'bp-better-messages'), $thread_id );
            mycred()->add_creds( 'better_messages_new_thread_' . $thread_id, $user_id, $amount_to_deduct, $log_entry );
        }

        public function mycred_core_charge_for_message( $message ){
            if( ! function_exists('mycred') ) {
                return false;
            }

            $user_id = (int) $message->sender_id;

            $user_charge_rate = BP_Better_Messages()->functions->get_user_mycred_charge_rate( $user_id );
            if( $user_charge_rate === 0 ) return false;

            $amount_to_deduct = 0 - $user_charge_rate;
            $log_entry = sprintf(_x('Better Messages charge for message #%d', 'MyCred Log Entry', 'bp-better-messages'), $message->id);
            mycred()->add_creds( 'bm_message_' . $message->id, $user_id, $amount_to_deduct, $log_entry );
        }

        public function mycred_core_charge_for_new_thread( &$args, &$errors ){
            if( ! function_exists('mycred') ) {
                return false;
            }

            $user_id = get_current_user_id();

            if( ! is_array( $args['recipients'] ) ){
                $args['recipients'] = [$args['recipients']];
            }

            $user_charge_rate = BP_Better_Messages()->functions->get_user_mycred_charge_new_thread_rate( $user_id );
            if( $user_charge_rate === 0 ) return false;

            $balance = mycred()->get_users_balance( $user_id );

            if( $balance >= $user_charge_rate ){
                $can_afford = true;
            } else {
                $can_afford = false;
            }

            if( ! $can_afford ){
                $errors['mycred_core_restricted'] = BP_Better_Messages()->settings['myCredNewThreadChargeMessage'];
            }
        }

        public function mycred_core_charge_message( $allowed, $user_id, $thread_id ){
            if( ! function_exists('mycred') ) {
                return $allowed;
            }

            $user_charge_rate = BP_Better_Messages()->functions->get_user_mycred_charge_rate( $user_id );

            if( $user_charge_rate === 0 ) return $allowed;

            $balance = mycred()->get_users_balance( $user_id );

            if( $balance >= $user_charge_rate ){
                $can_afford = true;
            } else {
                $can_afford = false;
            }

            if( ! $can_afford ){
                $allowed = false;
                global $bp_better_messages_restrict_send_message;
                $bp_better_messages_restrict_send_message['mycred_core_restricted'] = BP_Better_Messages()->settings['myCredNewMessageChargeMessage'];
            }

            return $allowed;
        }

        public function buddyboss_disable_message_to_blocked( $allowed, $user_id, $thread_id ){
            if ( ! bp_is_active( 'moderation' ) ) return $allowed;
            if( ! class_exists( 'BP_Moderation' ) ) return $allowed;
            if( ! function_exists( 'bp_moderation_is_user_blocked' ) ) return $allowed;

            $participants = BP_Better_Messages()->functions->get_participants($thread_id);

            if( ! isset( $participants['recipients'] ) ) {
                return $allowed;
            }

            /**
             * Not block in group thread
             */
            if( count($participants['recipients']) > 1 ){
                return $allowed;
            }

            $thread_type = BP_Better_Messages()->functions->get_thread_type( $thread_id );
            if( $thread_type !== 'thread') return $allowed;

            foreach( $participants['recipients'] as $recipient_user_id ){

                if( bp_moderation_is_user_blocked( $recipient_user_id ) ){
                    global $bp_better_messages_restrict_send_message;
                    $bp_better_messages_restrict_send_message['bb_blocked_user'] = __( "You can't message a blocked member.", 'bp-better-messages' );
                    $allowed = false;

                    continue;
                }

                $moderation            = new BP_Moderation();
                $moderation->user_id   = $recipient_user_id;
                $moderation->item_id   = $user_id;
                $moderation->item_type = 'user';

                $id = BP_Moderation::check_moderation_exist( $user_id, 'user' );

                if ( ! empty( $id ) ) {
                    $moderation->id = (int) $id;
                    $moderation->populate();
                }

                $is_blocked = ( ! empty( $moderation->id ) && ! empty( $moderation->report_id ) );

                if( $is_blocked ){
                    global $bp_better_messages_restrict_send_message;
                    $bp_better_messages_restrict_send_message['bb_blocked_by_user'] = __("You can't message this member.", 'bp-better-messages');
                    $allowed = false;
                }
            }

            return $allowed;
        }

        public function buddyboss_blocked_message( $allowed, $user_id, $thread_id ){
            $thread = new BP_Messages_Thread( $thread_id );

            if( ! isset( $thread->recipients ) ) return $allowed;
            if( ! is_array( $thread->recipients ) ) return $allowed;
            if( count( $thread->recipients ) === 0 ) return $allowed;

            $check_buddyboss_access = bb_access_control_member_can_send_message( $thread, $thread->recipients, 'wp_error' );

            if( is_wp_error($check_buddyboss_access) ){
                $allowed = false;
                global $bp_better_messages_restrict_send_message;
                $bp_better_messages_restrict_send_message['buddyboss_restricted'] = $check_buddyboss_access->get_error_message();
            }
            return $allowed;
        }

        public function buddyboss_theme_get_header_unread_messages(){
            $response = array();
            ob_start();

            echo BP_Better_Messages()->functions->get_threads_html( get_current_user_id() );
            ?>
            <script type="text/javascript">
                var notification_list = jQuery('.site-header .messages-wrap .notification-list');
                notification_list.removeClass('notification-list').addClass('bm-notification-list');

                notification_list.css({'margin' : 0, 'padding' : 0});

                jQuery(document).trigger("bp-better-messages-init-scrollers");
            </script>
            <?php
            $response['contents'] = ob_get_clean();

            wp_send_json_success( $response );
        }

        public function buddyboss_group_messages( $message, $message_id, $context, $user_id ){
            global $wpdb;
            $group_id         = bp_messages_get_meta( $message_id, 'group_id', true );
            $message_deleted  = bp_messages_get_meta( $message_id, 'bp_messages_deleted', true );

            if( $group_id ) {
                if ( function_exists('bp_get_group_name') ) {
                    $group_name = bp_get_group_name(groups_get_group($group_id));
                } else {
                    $bp_prefix = bp_core_get_table_prefix();
                    $table = $bp_prefix . 'bp_groups';
                    $group_name = $wpdb->get_var( "SELECT `name` FROM `{$table}` WHERE `id` = '{$group_id}';" );
                }

                $message_left     = bp_messages_get_meta( $message_id, 'group_message_group_left', true );
                $message_joined   = bp_messages_get_meta( $message_id, 'group_message_group_joined', true );

                if ($message_left && 'yes' === $message_left) {
                    $message = '<i>' . sprintf(__('Left "%s"', 'bp-better-messages'), ucwords($group_name)) . '</i>';
                } else if ($message_joined && 'yes' === $message_joined) {
                    $message = '<i>' . sprintf(__('Joined "%s"', 'bp-better-messages'), ucwords($group_name)) . '</i>';
                }
            }

            if ( $message_deleted && 'yes' === $message_deleted ) {
                $message =  '<i>' . __( 'This message was deleted.', 'bp-better-messages' ) . '</i>';
            }

            return $message;
        }

        public function buddyboss_catch_group_id_meta($val, $object_id, $meta_key, $single, $meta_type){
            if( wp_doing_ajax() && isset( $_REQUEST['action'] ) && $_REQUEST['action'] === 'buddyboss_theme_get_header_unread_messages' && $meta_key === 'group_id' ){
                global $bpbm_buddyboss_catch_group_id_meta;
                if( ! is_array($bpbm_buddyboss_catch_group_id_meta) ){
                    $bpbm_buddyboss_catch_group_id_meta = [];
                }

                if( isset( $bpbm_buddyboss_catch_group_id_meta[ $object_id ] ) ) {
                    if( $bpbm_buddyboss_catch_group_id_meta[ $object_id ] !== false ) {
                        return $bpbm_buddyboss_catch_group_id_meta[ $object_id ];
                    }
                } else {
                    global $wpdb, $messages_template;
                    $table = bpbm_get_table('messages');

                    $thread_id = $wpdb->get_var($wpdb->prepare("SELECT `thread_id` FROM `{$table}` WHERE `id` = %d;", $object_id));
                    $first_message = $wpdb->get_var($wpdb->prepare("SELECT `id` FROM `{$table}` WHERE `thread_id` = %d ORDER BY `id` ASC LIMIT 0,1 ;", $thread_id));

                    if (isset($messages_template->thread) && is_object($messages_template->thread)) {
                        $messages_template->thread->thread_id = (int)$thread_id;
                    }

                    bp_messages_update_meta($first_message, 'first_message', true);
                    bp_messages_update_meta($first_message, 'group_message_thread_id', $thread_id);
                    bp_messages_update_meta($first_message, 'message_from', 'group');
                    bp_messages_update_meta($first_message, 'group_message_users', 'all');
                    bp_messages_update_meta($first_message, 'group_message_type', 'open');

                    $group_id = BP_Better_Messages()->functions->get_thread_meta($thread_id, 'group_id');
                    if (!empty($group_id)) {
                        $bpbm_buddyboss_catch_group_id_meta[$object_id] = $group_id;
                        return $group_id;
                    } else {
                        $bpbm_buddyboss_catch_group_id_meta[$object_id] = false;
                    }
                }
            }

            return $val;
        }

        public function verified_member_badge($username, $user_id, $thread_id){
            global $bp_verified_member_admin, $bp_verified_member;
            if ( empty( get_user_meta( $user_id, $bp_verified_member_admin->meta_box->meta_keys['verified'], true ) ) ) {
                return $username;
            }

            $badge = $bp_verified_member->get_verified_badge();

            if( strpos($username, '</a>') !== false ) {
                return str_replace('</a>', $badge . '</a>', $username);
            }

            return $username . $badge;
        }

        public function show_in_bbpress_profile(){
            $messages_total = BP_Messages_Thread::get_total_threads_for_user( get_current_user_id(), 'inbox', 'unread' );
            $class = ( 0 === $messages_total ) ? 'no-count' : 'count';

            $title = sprintf( _x( 'Messages <span class="%s bp-better-messages-unread">%s</span>', 'Messages list sub nav', 'bp-better-messages' ), esc_attr( $class ), bp_core_number_format( $messages_total ) );
            echo '<ul><li><a href="#">' . $title . '</a></li></ul>';
        }

        public function pm_link_bbpress(){
            if( ! is_user_logged_in() ) return false;
            $reply_id = bbp_get_reply_id();
            if ( bbp_is_reply_anonymous( $reply_id ) ) return false;

            $user_id = bbp_get_reply_author_id( $reply_id );

            if( get_current_user_id() === $user_id ) return false;
            $user = get_userdata($user_id);
            $nice_name = $user->user_nicename;

            $link = add_query_arg([
                'new-message' => '',
                'to' => $nice_name
            ], BP_Better_Messages()->functions->get_link());

            echo '<a href="' . $link . '" class="bpbm-private-message-link-buddypress">' . __('Private Message', 'bp-better-messages') . '</a>';
        }

        public function asragaros_thread_view($author_id, $author_posts){
            if( ! is_user_logged_in() ) return false;
            if( get_current_user_id() === intval($author_id) ) return false;

            $view_user = get_userdata( $author_id );
            $nice_name = $view_user->user_nicename;

            $link = add_query_arg([
                'new-message' => '',
                'to' => $nice_name
            ], BP_Better_Messages()->functions->get_link());

            echo '<a href="' . $link .'" class="bpbm-asragaros-messages-link">' . __('Private Message', 'bp-better-messages') . ' </a>';
        }

        public function asragaros_profile_messages(){
            if( ! is_user_logged_in() ) return false;
            $url_parts = explode('/', $_SERVER['REQUEST_URI']);
            if( ! in_array('profile', $url_parts) || ! in_array('messages', $url_parts) ) return false;

            global $asgarosforum;

            $asgarosforum->current_view = 'messages';
            $asgarosforum->error        = 'No error';

            echo '<style type="text/css">#af-wrapper .error{display:none}</style>';
            $user_id = $asgarosforum->current_element;
            $userData = get_user_by('id', $user_id);

            if ($userData) {
                if ($asgarosforum->profile->hideProfileLink()) {
                    _e('You need to login to have access to profiles.', 'asgaros-forum');
                } else {
                    $asgarosforum->profile->show_profile_header($userData);
                    $asgarosforum->profile->show_profile_navigation($userData);

                    echo '<div id="profile-content" style="padding: 0">';
                    echo BP_Better_Messages()->functions->get_page( true );
                    echo '</div>';
                }
            } else {
                _e('This user does not exist.', 'asgaros-forum');
            }
        }

        public function asragaros_profile_link(){
            if( ! is_user_logged_in() ) return false;

            global $asgarosforum;
            $user_id = get_current_user_id();
            $view_id = $asgarosforum->current_element;

            if( $user_id !== $view_id ) {
                $view_user = get_userdata( $view_id );
                $nice_name = $view_user->user_nicename;

                $link = add_query_arg([
                    'new-message' => '',
                    'to' => $nice_name
                ], BP_Better_Messages()->functions->get_link());
                echo '<a href="' . $link .'">' . __('Private Message', 'bp-better-messages') . ' </a>';
            } else {
                $messages_total = BP_Messages_Thread::get_total_threads_for_user( $user_id, 'inbox', 'unread' );
                $class = ( 0 === $messages_total ) ? 'no-count' : 'count';

                $title = sprintf( _x( 'Messages <span class="%s bp-better-messages-unread">%s</span>', 'Messages list sub nav', 'bp-better-messages' ), esc_attr( $class ), bp_core_number_format( $messages_total ) );

                $link = $asgarosforum->get_link('profile', $user_id) . 'messages/';

                if( $asgarosforum->current_view === 'messages' ) {
                    echo '<a class="active" href="' . $link .'">' . $title . ' </a>';
                } else {
                    echo '<a href="' . $link .'">' . $title . ' </a>';
                }
            }
        }

        public function disable_message_to_deleted_users( $allowed, $user_id, $thread_id ){
            $type = BP_Better_Messages()->functions->get_thread_type( $thread_id );
            if( $type !== 'thread' ) return $allowed;

            $thread = new BP_Messages_Thread();
            $recipients = $thread->get_recipients( $thread_id );
            unset( $recipients[$user_id] );

            if( count( $recipients ) === 1 ){
                $userdata = get_userdata(array_keys($recipients)[0]);
                if( ! $userdata ) return false;
            }

            return $allowed;
        }

        public function allow_additional_tags( $tags ){
            $tags['u'] = [];
            $tags['sub'] = [];
            $tags['sup'] = [];

            return $tags;
        }

        public function hide_admin_counter(){
            echo '<style type="text/css">.no-count.bp-better-messages-unread{display:none!important}</style>';
        }

        public function disable_thread_for_pmpro_restricted_role( &$args, &$errors ){
            if( ! pmpro_bp_user_can( 'private_messaging', get_current_user_id() ) ) {
                $errors['pmpro_restricted'] = __('Your membership does not allow to use messages', 'bp-better-messages');
            }
        }

        public function disable_messages_for_pmpro_restricted_role( $allowed, $user_id, $thread_id ){
            if( ! pmpro_bp_user_can( 'private_messaging', $user_id ) ) {
                $allowed = false;
                global $bp_better_messages_restrict_send_message;
                $bp_better_messages_restrict_send_message['pmpro_restricted'] = __('Your membership does not allow to use messages', 'bp-better-messages');
            }

            return $allowed;
        }

        public function css_customizations( $only_logged_in ){
            if( doing_action('wp_head') ) {
                if (!is_user_logged_in()) return false;
            }

            $rules = array();

            $user_id = get_current_user_id();
            $rules[] = '.bm-medium-editor-mention-user-' . $user_id .'{font-weight:bold!important}';

            if( ! empty( BP_Better_Messages()->settings['colorGeneral'] ) && BP_Better_Messages()->settings['colorGeneral'] !== '#21759b' ){
                $main_color     = BP_Better_Messages()->settings['colorGeneral'];

                $rgba_color_75 = BP_Better_Messages()->functions->hex2rgba($main_color, 0.75);
                $rgba_color_60  = BP_Better_Messages()->functions->hex2rgba($main_color, 0.6);
                $rgba_color_50  = BP_Better_Messages()->functions->hex2rgba($main_color, 0.5);
                $rgba_color_03  = BP_Better_Messages()->functions->hex2rgba($main_color, 0.03);
                $rgba_color_075 = BP_Better_Messages()->functions->hex2rgba($main_color, 0.075);
                $rgba_color_25  = BP_Better_Messages()->functions->hex2rgba($main_color, 0.25);

                $rules[] = ':root{';
                $rules[] = '--main-bm-color:' . $main_color . ';';
                $rules[] = '--main-bm-color-75:' . $rgba_color_75 . ';';
                $rules[] = '--main-bm-color-60:' . $rgba_color_60 . ';';
                $rules[] = '--main-bm-color-50:' . $rgba_color_50 . ';';
                $rules[] = '--main-bm-color-25:' . $rgba_color_25 . ';';
                $rules[] = '--main-bm-color-075:' . $rgba_color_075 . ';';
                $rules[] = '--main-bm-color-003:' . $rgba_color_03;
                $rules[] = '}';

                #$rules[] = '.bp-better-messages-list .tabs>div[data-tab=messages] .unread-count, .bp-better-messages-mini .chats .chat .head .unread-count{background:' . $main_color . ' !important}';
                #$rules[] = '.bp-messages-wrap .chat-header .fas,.bp-messages-wrap .chat-header>a,.bp-messages-wrap .chat-footer .fas,.bp-messages-wrap .chat-footer>a,.bp-messages-wrap:not(.bp-messages-mobile) .reply .send button,.uppy-Dashboard-browse,.bp-messages-wrap.mobile-ready:not(.bp-messages-mobile) .bp-messages-mobile-tap{color:' . $main_color . ' !important}';
                #$rules[] = '.uppy-Dashboard-close .UppyIcon{fill:' . $main_color . '!important}';
                #$rules[] = '#bp-better-messages-mini-mobile-open{background:' . $main_color . '}';
                #$rules[] = '.bp-messages-wrap .bp-emojionearea.focused,.bp-messages-wrap .new-message form>div>input[type="text"]:focus,.bp-messages-wrap .active .taggle_list,.bp-messages-wrap .chat-header .bpbm-search form input:focus{border-color:' . $main_color . '!important;-moz-box-shadow: inset 0 1px 1px '. $rgba_color_075 . ', 0 0 8px ' . $rgba_color_06 . '!important;-webkit-box-shadow: inset 0 1px 1px ' . $rgba_color_075 . ', 0 0 8px ' . $rgba_color_06 . '!important;box-shadow: inset 0 1px 1px ' . $rgba_color_075 . ', 0 0 8px ' . $rgba_color_06 . '!important}';
                #$rules[] = '.bp-messages-wrap #send-to .ui-autocomplete{border-color: ' . $main_color . ';-moz-box-shadow: inset 0 0 0 ' . $rgba_color_075 . ', 0 3px 3px ' . $rgba_color_06 . ';-webkit-box-shadow: inset 0 0 0 ' . $rgba_color_075 . ', 0 3px 3px ' . $rgba_color_06 . ';box-shadow: inset 0 0 0 ' . $rgba_color_075 . ', 0 3px 3px ' . $rgba_color_06 . '}';
                #$rules[] = '.bp-messages-wrap .list .messages-stack .content .info .name a{color: ' . $main_color . '}';
                #$rules[] = '.bp-messages-wrap.bp-messages-mobile .reply .send button[type=submit],.bp-messages-wrap.bp-messages-mobile .reply .send button.bpbm-record-voice-button{background-color: ' . $main_color . '!important}';
                #$rules[] = '.bp-messages-wrap .threads-list .thread.bp-messages-active-thread{background: ' . $rgba_color_03 . '}';
                #$rules[] = '.bp-messages-wrap .chat-header .expandingButtons .bpbm-dropdown-menu .bpbm-dropdown-item:hover{background: ' . $rgba_color_003 . '}';
                #$rules[] = '.bp-better-messages-mini .chats .chat .head .controls div,.bp-better-messages-mini .chats .chat .head .controls span,.bp-better-messages-mini .chats .chat .head .controls a{color:' . $main_color . '}';
                #$rules[] = '.bpbmuc,.bpbm-join-to-chat-button,.bpbm-join-to-chat-button:hover{background: ' . $main_color . '}';
                #$rules[] = '.bp-messages-wrap .list .messages-stack .content .messages-list li .status{color: ' . $main_color . '}';
                #$rules[] = 'a.bpbm-pm-button,a.bpbm-pm-button:hover,a.bpbm-pm-button:focus,.new-message button[type="submit"]{background-color: ' . $main_color . '}';
                #$rules[] = '.bpbm-replied-message{border-color:' . $main_color . ';background:' . $rgba_color_003 . '}';
                #$rules[] = '.bpbm-preview-message .bpbm-preview-message-cancel{color: ' . $main_color . '}';
                #$rules[] = '.bpbm-replied-message .bpbm-replied-message-name,.bpbm-preview-message .bpbm-preview-message-content .bpbm-preview-message-name{color: ' . $main_color . '}';
                #$rules[] = '.bp-messages-wrap .bp-verified-badge{background-color:' . $main_color . '!important}';
                #$rules[] = '.bpbm-context-menu-icon.bpbm-context-menu-icon--fa5 i,.bpbm-context-menu-icon.bpbm-context-menu-icon--fa5 svg{color:' . $main_color . '}';
                #$rules[] = '.bpbm-context-menu-item.bpbm-context-menu-hover{background-color:' . $main_color . '}';
                #$rules[] = '.bpbm-send-files{background-color:' . $main_color . '}';
                #$rules[] = '.bp-messages-wrap.bpbm-template-modern .list .messages-stack.incoming .content .messages-list li .message-content{border-color:' . $main_color . ';background:' . $main_color . '}';
            }

            if( BP_Better_Messages()->settings['template'] === 'modern' ) {
                $border_radius = BP_Better_Messages()->settings['modernBorderRadius'];
                if( $border_radius != 2 ){
                    $rules[] = '.bp-messages-wrap.bpbm-template-modern .list .messages-stack .content .messages-list li .message-content{border-radius:' . $border_radius . 'px}';
                    $rules[] = '.bp-messages-wrap.bpbm-template-modern .list .messages-stack .content .messages-list li .bpbm-attachments .videos .video video{border-radius:' . $border_radius . 'px}';
                    $rules[] = '.bp-messages-wrap.bpbm-template-modern .list .messages-stack .content .messages-list li .bpbm-attachments .images a{border-radius:' . $border_radius . 'px}';
                    $rules[] = '.bp-messages-wrap.bpbm-template-modern .list .messages-stack .content .messages-list li .bpbm-gif{border-radius:' . $border_radius . 'px}';
                    $rules[] = '.bp-messages-wrap.bpbm-template-modern .list .messages-stack .content .messages-list li .bp-messages-iframe-container{border-radius:' . $border_radius . 'px}';
                    $rules[] = '.bp-messages-wrap.bpbm-template-modern .list .messages-stack.incoming .content .messages-list li.selected .message-content:before{border-radius:' . $border_radius . 'px}';
                    $reply_radius = $border_radius;
                    if( $reply_radius > 10 ) $reply_radius = 10;
                    $rules[] = '.bpbm-replied-message{border-radius:' . $reply_radius . 'px}';
                }
            }

            if( ! empty( BP_Better_Messages()->settings['miniWindowsHeight'] ) && BP_Better_Messages()->settings['miniWindowsHeight'] !== 426 ){

                $change = BP_Better_Messages()->settings['miniWindowsHeight'] - 426;

                $css_1   = 351 + $change;
                $css_2   = 351 + $change;

                $rules[] = '.bp-messages-wrap.bp-better-messages-list .messages .scroller{max-height:' . $css_1 . 'px!important;}';
                $rules[] = '.bp-messages-wrap.bp-better-messages-list .messages.no-chat-footer .scroller{max-height:' . $css_2 . 'px!important;}';
                $rules[] = '.bp-messages-wrap.bp-better-messages-list .friends .scroller,.bp-messages-wrap.bp-better-messages-list .um-friends .scroller,.bp-messages-wrap.bp-better-messages-list .ps-friends .scroller{max-height:' . $css_2 . 'px!important;}';
                $rules[] = '.bp-messages-wrap.bp-better-messages-list .bpbm-groups .scroller,.bp-messages-wrap.bp-better-messages-list .ps-groups .scroller,.bp-messages-wrap.bp-better-messages-list .bmum-groups .scroller{max-height:' . $css_2 . 'px!important;}';
            }

            if( ! empty( BP_Better_Messages()->settings['miniChatsHeight'] ) && BP_Better_Messages()->settings['miniChatsHeight'] !== 426 ) {
                $change = BP_Better_Messages()->settings['miniChatsHeight'] - 426;
                $css_3   = 395 + $change;
                $rules[] = '.bp-messages-wrap.bp-better-messages-mini .bp-messages-threads-wrapper{max-height:' . $css_3 . 'px!important;}';
            }

            if( ! empty( BP_Better_Messages()->settings['miniWindowsOffset'] ) && BP_Better_Messages()->settings['miniWindowsOffset'] !== 70 ){
                $rules[] = ':root{--bm-mini-widgets-offset:' . BP_Better_Messages()->settings['miniWindowsOffset'] . 'px}';
            }

            if( ! empty( BP_Better_Messages()->settings['miniWindowsWidth'] ) && BP_Better_Messages()->settings['miniWindowsWidth'] !== 320 ){
                $rules[] = ':root{--bm-mini-widgets-width:' . BP_Better_Messages()->settings['miniWindowsWidth'] . 'px}';
            }

            if( ! empty( BP_Better_Messages()->settings['miniChatsWidth'] ) && BP_Better_Messages()->settings['miniChatsWidth'] !== 300 ){
                $rules[] = ':root{--bm-mini-chats-width:' . BP_Better_Messages()->settings['miniChatsWidth'] . 'px}';
            }

            if( defined('VIKINGER_VERSION') ){
                $rules[] = 'body.bp-messages-mobile .bp-messages-wrap .chat-header{border-radius:0!important}';
                $rules[] = '#bp-better-messages-mini-mobile-container,#bp-better-messages-mobile-view-container{background-color:var(--color-box-background)}';
                $rules[] = '.bp-better-messages-mini .chats .chat{box-shadow:0 0 3px 1px var(--color-border);font-family:var(--font-secondary);background-color:var(--color-box-background)}';
                $rules[] = '.bp-better-messages-mini .chats .chat .head,.bp-messages-wrap.bp-better-messages-list .chat-header{background-color:var(--color-box-background);border-color:var(--color-border);color:var(--color-text)}';
                $rules[] = '.bp-better-messages-list .tabs{border-color:var(--color-border)}';
                $rules[] = '.bp-better-messages-list .tabs>div.active{background-color:var(--color-box-highlight-background)}';
                $rules[] = '.bp-better-messages-list .tabs>div,.bp-better-messages-mini .chats .chat .head .controls span,.bp-messages-user-list{background-color:var(--color-box-background);color:var(--color-text)}';
                $rules[] = '.bp-messages-wrap.mobile-ready:not(.bp-messages-mobile) .bp-messages-mobile-tap{color:var(--color-text);font-family:var(--font-primary)}';
                $rules[] = '.bp-messages-user-list .user .loading{background-color:var(--color-box-background)}';
                $rules[] = '.bp-better-messages-mini .chats .chat .head.blink{background:#f1f1f1;background-color:var(--color-box-background-alt)}';
            }


            if( ! empty( BP_Better_Messages()->settings['mobilePopupLocationBottom'] ) && BP_Better_Messages()->settings['mobilePopupLocationBottom'] !== 20 ){
                $bottom  = (int) BP_Better_Messages()->settings['mobilePopupLocationBottom'];
                $rules[] = '#bp-better-messages-mini-mobile-open{bottom:' . $bottom . 'px}';
            }

            $side_width = (int) BP_Better_Messages()->settings['sideThreadsWidth'];
            if( $side_width !== 320 ){
                $rules[] = '.bp-messages-wrap .bp-messages-side-threads{width:' . $side_width . 'px}';
                $rules[] = '.bp-messages-wrap .bp-messages-side-threads > *{width:' . $side_width . 'px}';
            }

            if( count( $rules ) > 0 ) {
                echo '<style type="text/css">';
                echo implode('', $rules);
                echo '</style>';
            }
        }

        public function override_last_activity_2($object_id, $meta_value){
            update_user_meta($object_id, 'bpbm_last_activity', $meta_value);
        }

        public function override_last_activity($null, $object_id, $meta_key, $meta_value, $prev_value){
            if( $meta_key === 'last_activity' ) {
                update_user_meta($object_id, 'bpbm_last_activity', $meta_value);
            }

            return $null;
        }

        public function max_height_css(){
            if( ! is_user_logged_in() ) return false;
            $max_height = (int) apply_filters('bp_better_messages_max_height', BP_Better_Messages()->settings['messagesHeight']);
            $fixed_header_height = (int) BP_Better_Messages()->settings['fixedHeaderHeight'];

            if( $fixed_header_height > 0 ){
                $max_height = $max_height - $fixed_header_height;
            }

            #echo '<style type="text/css">body:not(.bp-messages-mobile) .bp-messages-wrap.bp-messages-wrap-main > .scroller,body:not(.bp-messages-mobile) .bp-messages-wrap.bp-messages-wrap-main > .bp-messages-side-threads-wrapper > .bp-messages-column > .scroller,body:not(.bp-messages-mobile) .bp-messages-wrap.bp-messages-wrap-main > .bp-messages-side-threads-wrapper > .bp-messages-column > .scroller > .scroller,body:not(.bp-messages-mobile) .bp-messages-wrap.bp-messages-wrap-main > .scroller > .scroller{max-height:'. $max_height .'px;}body:not(.bp-messages-mobile) .bp-messages-threads-wrapper{max-height:' . ($max_height) .'px!important;}</style>';
            echo '<style type="text/css">.bp-messages-threads-wrapper{height:' . $max_height . 'px}</style>';
        }

        public function buddyboss_notifications_fix( $array ){
            if ( function_exists( 'buddyboss_theme_register_required_plugins' ) || class_exists('BuddyBoss_Theme') ) {
                if( count( $array['threads'] ) > 0 && isset( $array['total'] ) ) {
                    $new_threads = [];

                    foreach ($array['threads'] as $i => $thread) {
                        if ( ! isset($thread->last_message_date) || strtotime($thread->last_message_date) <= 0 ) {
                            unset($array['threads'][$i]);
                            $array['total']--;
                        } else {
                            $new_threads[] = $thread;
                        }
                    }


                    $array['threads'] = $new_threads;
                }
                if( $array['total'] < 0 ) $array['total'] = 0;
            }

            return $array;
        }

        public function beehive_theme_integration(){
            if( ! class_exists('Beehive') ) return false;

            $options = get_option('beehive_opts', [
                'primary' => '#21759b'
            ]);

            if( is_array($options) && isset($options['primary'] ) ) {
                $main_color = $options['primary'];
                $rgba_color_075 = BP_Better_Messages()->functions->hex2rgba($main_color, 0.075);
                $rgba_color_06  = BP_Better_Messages()->functions->hex2rgba($main_color, 0.6);
                ?><style type="text/css">
                    body.bp-messages-mobile header{
                        display: none;
                    }

                    .bp-messages-wrap.bp-messages-mobile .reply .send button[type=submit]{
                        background: #f7f7f7 !important;
                    }

                    .bp-better-messages-list .tabs>div[data-tab=messages] .unread-count, .bp-better-messages-mini .chats .chat .head .unread-count{
                        background: <?php echo $main_color; ?> !important;
                    }

                    .bp-messages-wrap .chat-header .fas,
                    .bp-messages-wrap .chat-header>a,
                    .bp-messages-wrap .reply .send button[type=submit],
                    .uppy-Dashboard-browse,
                    .bp-messages-wrap.mobile-ready:not(.bp-messages-mobile) .bp-messages-mobile-tap{
                        color: <?php echo $main_color; ?> !important;
                    }

                    .uppy-Dashboard-close .UppyIcon{
                        fill: <?php echo $main_color; ?> !important;
                    }

                    .bp-messages-wrap .bp-emojionearea.focused,
                    .bp-messages-wrap .new-message form>div input:focus,
                    .bp-messages-wrap .active .taggle_list,
                    .bp-messages-wrap .chat-header .bpbm-search form input:focus{
                        border-color: <?php echo $main_color; ?>!important;
                        -moz-box-shadow: inset 0 1px 1px <?php echo $rgba_color_075; ?>, 0 0 8px <?php echo $rgba_color_06; ?>;
                        -webkit-box-shadow: inset 0 1px 1px <?php echo $rgba_color_075; ?>, 0 0 8px <?php echo $rgba_color_06; ?>;
                        box-shadow: inset 0 1px 1px <?php echo $rgba_color_075; ?>, 0 0 8px <?php echo $rgba_color_06; ?>;
                    }

                    .bp-messages-wrap #send-to .ui-autocomplete{
                        border-color: <?php echo $main_color; ?>;
                        -moz-box-shadow: inset 0 0 0 <?php echo $rgba_color_075; ?>, 0 3px 3px <?php echo $rgba_color_06; ?>;
                        -webkit-box-shadow: inset 0 0 0 <?php echo $rgba_color_075; ?>, 0 3px 3px <?php echo $rgba_color_06; ?>;
                        box-shadow: inset 0 0 0 <?php echo $rgba_color_075; ?>, 0 3px 3px <?php echo $rgba_color_06; ?>;
                    }
                </style>
                <script type="text/javascript">
                    jQuery(document).on('bp-better-messages-update-unread', function( event ) {
                        var unread = parseInt(event.detail.unread);
                        var private_messages = jQuery('#nav_private_messages');

                        if( unread > 0 ){
                            var count = private_messages.find('span.count');
                            if( count.length === 0 ){
                                private_messages.append('<span class="count">' + unread + '</span>');
                            } else {
                                private_messages.find('.count').text(unread);
                            }
                        } else {
                            private_messages.find('span.count').remove();
                        }
                    });
                </script>
                <?php
            }
        }

        public function update_last_activity(){
            if( is_user_logged_in() ) {
                $user_id = get_current_user_id();
                bp_update_user_last_activity($user_id);
            }
        }

        public function mobile_popup_button(){
            if( ! is_user_logged_in() || BP_Better_Messages()->settings['mobilePopup'] == '0' ) return '';

            $user_id = get_current_user_id();
            $restricted_roles = BP_Better_Messages()->settings['restrictMobilePopup'];

            if( count($restricted_roles) > 0 ) {
                $user = get_userdata($user_id);
                $roles = $user->roles;

                $is_restricted = false;

                if (count($restricted_roles) > 0) {
                    foreach ($restricted_roles as $restricted_role) {
                        if (in_array($restricted_role, $roles)) {
                            $is_restricted = true;
                        }
                    }
                }
                if( $is_restricted ) return '';
            }


            if( BP_Better_Messages()->settings['mechanism'] === 'websocket') {
                $count = 0;
            } else {
                $count = BP_Messages_Thread::get_total_threads_for_user( $user_id, 'inbox', 'unread' );
            }

            $class = ($count === 0) ? 'no-count' : '';

            ob_start();
            BP_Better_Messages()->functions->messages_classes();
            $classes = ob_get_clean();

            $positionClass = (BP_Better_Messages()->settings['mobilePopupLocation'] === 'left') ? ' bpbm-mobile-open-left' : '';

            echo '<div id="bp-better-messages-mini-mobile-open" class="bp-messages-wrap' . $positionClass . '"><i class="fas fa-comments"></i><span class="count ' . $class . ' bp-better-messages-unread">' . $count . '</span></div>';
            echo '<div id="bp-better-messages-mini-mobile-container" class="bp-messages-wrap ' . $classes . '"></div>';
        }

        public function heartbeat_unread_notifications( $response = array() ){
            if( BP_Better_Messages()->settings['mechanism'] === 'websocket') {
                if (isset($response['total_unread_messages'])) {
                    unset($response['total_unread_messages']);
                }
            }

            return $response;
        }

        public function replace_unread_count( $unread_count, $user_id ){
            if( BP_Better_Messages()->settings['mechanism'] === 'websocket'){
                /** Replacing BuddyBoss Messages Count */
                if( class_exists('BuddyBoss_Theme') || function_exists( 'buddyboss_theme_register_required_plugins' ) ) {
                    return 0;
                }

                /** BuddyX Counter */
                if( defined('BUDDYX_MINIMUM_WP_VERSION') ){
                    return 0;
                }

                /** BuddyXPro Counter */
                if( defined('BUDDYXPRO_MINIMUM_WP_VERSION') ){
                    return 0;
                }

                /** CERA Counter */
                if( defined('GRIMLOCK_BUDDYPRESS_VERSION')){
                    return 0;
                }
            }

            return BP_Messages_Thread::get_total_threads_for_user( $user_id, 'inbox', 'unread' );
        }

        public function inbox_counter_javascript(){
            if( ! is_user_logged_in() ) return false;
            ob_start();

            if(class_exists('WooCommerce')){ ?>
                jQuery(document).ready(function( event ) {
                    var unread = BP_Messages['total_unread'];
                    var element = jQuery('.woocommerce-MyAccount-navigation-link--bp-messages a .bp-better-messages-unread');
                    if( element.length === 0 ){
                        jQuery('<span class="bp-better-messages-unread bpbmuc bpbmuc-preserve-space bpbmuc-hide-when-null" data-count="' + unread + '">' + unread + '</span>').appendTo('.woocommerce-MyAccount-navigation-link--bp-messages a');
                    }
                });
            <?php
                //
            }

            if( class_exists('BuddyBoss_Theme') ){ ?>
                    jQuery(document).on('bp-better-messages-update-unread', function( event ) {
                        var unread = parseInt(event.detail.unread);
                        var messages_count = jQuery('.header-notifications.user-messages span');
                        if( unread > 0 ){
                            messages_count.text(unread).attr('class', 'count');
                        } else {
                            messages_count.text(unread).attr('class', 'no-alert');
                        }
                    });
            <?php } else if( function_exists( 'buddyboss_theme_register_required_plugins' ) ){  ?>
                    jQuery(document).on('bp-better-messages-update-unread', function( event ) {
                        var unread = parseInt(event.detail.unread);

                        var iconSelector = '.bb-icon-inbox-small';

                        if( jQuery('body').hasClass('bb-template-v2') || jQuery('body').hasClass('bb-template-v1') ){
                            iconSelector = '.bb-icon-inbox';
                        }

                        var messages_count = jQuery('.notification-wrap.messages-wrap .count');
                        if( unread > 0 ){
                            if( messages_count.length === 0 ){
                                jQuery('.notification-wrap.messages-wrap').find(iconSelector).parent().append( '<span class="count">' + unread + '</span>' );
                            } else {
                                messages_count.text(unread).show();
                            }
                        } else {
                            messages_count.text(unread).hide();
                        }

                        var buddypanel = jQuery('.buddypanel-menu .bp-messages-nav > a');
                        if( buddypanel.length > 0 ){
                            var messages_count = buddypanel.find('.count');
                            if( unread > 0 ){
                                if( messages_count.length === 0 ){
                                    buddypanel.append( '<span class="count">' + unread + '</span>' );
                                } else {
                                    messages_count.text(unread).show();
                                }
                            } else {
                                messages_count.text(unread).hide();
                            }
                        }
                    });
            <?php } else if( defined('BUDDYX_MINIMUM_WP_VERSION') || defined('BUDDYXPRO_MINIMUM_WP_VERSION') ){ ?>
                    jQuery(document).on('bp-better-messages-update-unread', function( event ) {
                        var unread = parseInt(event.detail.unread);
                        var messages_count = jQuery('.buddypress-icons-wrapper .bp-msg sup');
                        messages_count.text(unread);
                    });
                <?php
            } else if( defined('GRIMLOCK_BUDDYPRESS_VERSION') ){ ?>
                    jQuery(document).on('bp-better-messages-update-unread', function( event ) {
                        var unread = parseInt(event.detail.unread);
                        var container = jQuery('.menu-item--messages');

                        var bubbleSpan = container.find('.bubble-count');

                        if( unread > 0 ){
                            if( bubbleSpan.length > 0 ){
                                bubbleSpan.text(unread);
                            } else {
                                var bubble = '<span class="bubble-count messages-count">' + unread + '</span>';
                                $(bubble).prependTo( container );
                            }
                        } else {
                            bubbleSpan.remove();
                        }

                        if( jQuery('body').hasClass('my-account') ) {
                            var grimlock_counter = jQuery('#profile-content__nav ul.settings-nav #user-bp_better_messages_tab');
                            var count = grimlock_counter.find('span.count');

                            if (unread > 0) {
                                if (count.length > 0) {
                                    count.text(unread);
                                } else {
                                    jQuery('<span class="count">' + unread + '</span>').appendTo(grimlock_counter);
                                }
                            } else {
                                count.remove();
                            }
                        }
                    });
                <?php
            }

            $js = ob_get_clean();
            if( trim( $js ) !== '' ){
                wp_add_inline_script( 'bp_messages_js', BP_Better_Messages()->functions->minify_js( $js ), 'before' );
            }
        }


        public function fix_404(){
            $slug = BP_Better_Messages()->settings['bpProfileSlug'];
            if ( function_exists('bp_core_no_access') && bm_bp_is_current_component( $slug ) && ! is_user_logged_in() ) {
                bp_core_no_access();
            }
        }

        public function fix_youzer(){
            if( function_exists('bp_nav_menu_get_item_url') ){
                $messages_link = bp_nav_menu_get_item_url( 'messages' );
            }
        }

        public function themes_adaptation(){
            $theme = wp_get_theme();
            $theme_name = $theme->get_template();

            switch ($theme_name){
                case 'boss':
                    echo '<style type="text/css">';
                    echo 'body.bp-messages-mobile #mobile-header{display:none}';
                    echo 'body.bp-messages-mobile #inner-wrap{margin-top:0}';
                    echo 'body.bp-messages-mobile .site{min-height:auto}';
                    echo '</style>';
                    break;
            }
        }

        public function disableStatuses(){
            ?><style type="text/css">.bp-messages-wrap .list .messages-stack .content .messages-list li .status{display: none !important;}.bp-messages-wrap .list .messages-stack .content .messages-list li .favorite{right: 5px !important;}</style><?php
        }

        public function disable_group_threads(&$args, &$errors){
            if( ! is_array($args['recipients']) ) return false;
            $recipients = $args['recipients'];
            if(count($recipients) > 1) {
                $message = __('You can start conversation only with 1 user per thread', 'bp-better-messages');
                $errors[] = $message;
            }
        }

        public function disable_message_for_blocked_user( $allowed, $user_id, $thread_id ){
            if( ! class_exists('BPTK_Block') ) return $allowed;

            $participants = BP_Better_Messages()->functions->get_participants($thread_id);

            if( ! isset( $participants['recipients'] ) ) {
                return $allowed;
            }
            /**
             * Not block in group thread
             */
            if( count($participants['recipients']) > 1 ){
                return $allowed;
            }

            foreach( $participants['recipients'] as $recipient_user_id ){
                $list = get_user_meta( $recipient_user_id, 'bptk_block', true );
                if ( empty($list) ) {
                    $list = array();
                }
                $_list = apply_filters( 'get_blocked_users', $list, $recipient_user_id );
                $recipient_blocked = array_filter( $_list );

                if( in_array( $user_id, $recipient_blocked ) ){
                    global $bp_better_messages_restrict_send_message;
                    $bp_better_messages_restrict_send_message['blocked_by_user'] = __('You were blocked by recipient', 'bp-better-messages');
                    $allowed = false;
                }
            }


            return $allowed;
        }

        public function disable_message_for_blocked_restricted_role( $allowed, $user_id, $thread_id ){
            $user             = wp_get_current_user();
            $restricted_roles = (array)  BP_Better_Messages()->settings['restrictNewReplies'];
            $user_roles       = (array) $user->roles;

            $is_restricted = false;
            foreach( $user_roles as $user_role ){
                if( in_array( $user_role, $restricted_roles ) ){
                    $is_restricted = true;
                }
            }

            if( $is_restricted ) {
                $allowed = false;
                global $bp_better_messages_restrict_send_message;
                $bp_better_messages_restrict_send_message['role_reply_restricted'] = BP_Better_Messages()->settings['restrictNewRepliesMessage'];
            }

            return $allowed;
        }

        public function disable_fast_mode( $enabled ){
            $user             = wp_get_current_user();
            $restricted_roles = (array) BP_Better_Messages()->settings['restrictViewMessages'];
            $user_roles       = (array) $user->roles;

            $is_restricted = false;
            foreach( $user_roles as $user_role ){
                if( in_array( $user_role, $restricted_roles ) ){
                    $is_restricted = true;
                }
            }

            if( $is_restricted ){
                return '0';
            }

            return $enabled;
        }

        public function hide_message_for_restricted_role( $message, $message_id, $context, $user_id ){
            $user             = get_userdata( $user_id );
            $restricted_roles = (array) BP_Better_Messages()->settings['restrictViewMessages'];
            $user_roles       = (array) $user->roles;

            $is_restricted = false;
            foreach( $user_roles as $user_role ){
                if( in_array( $user_role, $restricted_roles ) ){
                    $is_restricted = true;
                }
            }

            if( $is_restricted ){
                return BP_Better_Messages()->settings['restrictViewMessagesMessage'];
            }

            return $message;
        }

        public function disable_new_thread_button_if_needed(){
            $user             = wp_get_current_user();
            $restricted_roles = (array)  BP_Better_Messages()->settings['restrictNewThreads'];
            $user_roles       = (array) $user->roles;

            $is_restricted = false;
            foreach( $user_roles as $user_role ){
                if( in_array( $user_role, $restricted_roles ) ){
                    $is_restricted = true;
                }
            }

            if( $is_restricted ) {
                BP_Better_Messages()->settings['disableNewThread'] = '1';
            }
        }

        public function disable_thread_for_blocked_restricted_role( &$args, &$errors ){
            $user             = wp_get_current_user();
            $restricted_roles = (array)  BP_Better_Messages()->settings['restrictNewThreads'];
            $user_roles       = (array) $user->roles;

            $is_restricted = false;
            foreach( $user_roles as $user_role ){
                if( in_array( $user_role, $restricted_roles ) ){
                    $is_restricted = true;
                }
            }

            if( $is_restricted ) {
                $errors['restrictNewThreadsMessage'] = BP_Better_Messages()->settings['restrictNewThreadsMessage'];
            }
        }

        public function disable_non_friends_reply( $allowed, $user_id, $thread_id ){
            $participants = BP_Better_Messages()->functions->get_participants($thread_id);
            if( count($participants['users']) !== 2) return $allowed;
            unset($participants['users'][$user_id]);
            reset($participants['users']);


            $friend_id = key($participants['users']);
            /**
             * Allow users reply to admins even if not friends
             */
            if( current_user_can('manage_options') || user_can( $friend_id, 'manage_options' ) ) {
                return $allowed;
            }

            $allowed = friends_check_friendship($user_id, $friend_id);

            if( ! $allowed ){
                global $bp_better_messages_restrict_send_message;
                $bp_better_messages_restrict_send_message['friendship_needed'] = __('You must become friends to send messages', 'bp-better-messages');
            }

            return $allowed;
        }

        public function disable_start_thread_if_thread_exist(&$args, &$errors){
            $recipients = $args['recipients'];
            if( ! is_array( $recipients ) ) return false;
            if(count($recipients) > 1) return false;
            $threadExists = array();


            foreach($recipients as $recipient){
                $user = get_user_by('slug', $recipient);

                $from = get_current_user_id();
                $to   = $user->ID;

                $threads = BP_Better_Messages()->functions->find_existing_threads($from, $to);

                if( count($threads) > 0) {
                    $threadExists[] = $recipient;

                    if(BP_Better_Messages()->settings['redirectToExistingThread'] == '1'){
                        $args['thread_id'] = $threads[0];

                        if( apply_filters('bp_better_messages_can_send_message', BP_Better_Messages()->functions->check_access( $args['thread_id'] ), $from, $args['thread_id'] ) ) {
                            messages_new_message( $args );
                        }

                        wp_send_json( array(
                            'result'   => $threads[0]
                        ) );
                        exit;
                    }
                }
            }

            if(count($threadExists) > 0){
                $message = sprintf(__('You already have threads with %s', 'bp-better-messages'), implode(', ', $threadExists));
                $errors[] = $message;
            }
        }

        public function disable_start_thread_if_user_restricted(&$args, &$errors){
            if( current_user_can('manage_options' ) ) {
                return null;
            }

            $current_user_id = get_current_user_id();

            $recipients = $args['recipients'];
            if( ! is_array( $recipients ) ) $recipients = [ $recipients ];

            foreach($recipients as $recipient){
                $user = get_user_by('slug', $recipient);

                $who_can_start = BP_Better_Messages()->functions->get_who_can_start_value( $user->ID );

                if( in_array('everyone', $who_can_start) ) continue;

                if( class_exists('BuddyPress') ) {
                    if (in_array('only_friends', $who_can_start) && function_exists('friends_check_friendship')) {
                        if ( ! friends_check_friendship($current_user_id, $user->ID) ) {
                            $errors[] = sprintf(__('%s allows only friends to message him', 'bp-better-messages'), BP_Better_Messages()->functions->get_name($user->ID));
                        }
                    }
                }

                if( defined('ultimatemember_version') ){
                    $um_allow_friends   = in_array( 'um_allow_friends', $who_can_start ) && class_exists('UM_Friends_API');
                    $um_allow_followers = in_array( 'um_allow_followers', $who_can_start ) && class_exists('UM_Followers_API');

                    if( $um_allow_followers || $um_allow_friends ) {
                        $possible_errors = [];
                        $is_allowed = false;

                        if ($um_allow_friends) {
                            if ( UM()->Friends_API()->api()->is_friend($current_user_id, $user->ID)) {
                                $is_allowed = true;
                            } else {
                                $possible_errors[] = sprintf(_x('%s allows only friends to message him', 'Ultimate Member User Restriction', 'bp-better-messages'), BP_Better_Messages()->functions->get_name($user->ID));
                            }
                        }

                        if ($um_allow_followers) {
                            $allowed = UM()->Followers_API()->api()->followed($current_user_id, $user->ID);

                            if (!$allowed) {
                                $allowed = UM()->Followers_API()->api()->followed($user->ID, $current_user_id);
                            }

                            if ($allowed) {
                                $is_allowed = true;
                            } else {
                                $possible_errors[] = sprintf(_x('%s allows only followers to message him', 'Ultimate Member User Restriction', 'bp-better-messages'), BP_Better_Messages()->functions->get_name($user->ID));
                            }
                        }

                        if (!$is_allowed) {
                            foreach ($possible_errors as $possible_error) {
                                $errors[] = $possible_error;
                            }
                        }
                    }
                }


                if( class_exists('BP_Better_Messages_Peepso') ) {
                    $ps_allow_friends   = in_array( 'ps_allow_friends', $who_can_start ) && class_exists('PeepSoFriendsPlugin');

                    if( $ps_allow_friends ) {
                        $possible_errors = [];
                        $is_allowed = false;

                        if ($ps_allow_friends) {

                            if ( PeepSoFriendsModel::get_instance()->are_friends( $current_user_id, $user->ID ) ) {
                                $is_allowed = true;
                            } else {
                                $possible_errors[] = sprintf(_x('%s allows only friends to message him', 'Ultimate Member User Restriction', 'bp-better-messages'), BP_Better_Messages()->functions->get_name($user->ID));
                            }
                        }

                        if ( ! $is_allowed ) {
                            foreach ($possible_errors as $possible_error) {
                                $errors[] = $possible_error;
                            }
                        }
                    }
                }

                if( in_array('nobody', $who_can_start) ){
                    $errors[] = sprintf(__('%s not allow to message him', 'bp-better-messages'), BP_Better_Messages()->functions->get_name($user->ID) );
                }
            }
        }

        public function disable_start_thread_for_non_friends(&$args, &$errors){
            if( current_user_can('manage_options' ) ) {
                return null;
            }

            $recipients = $args['recipients'];
            if( ! is_array( $recipients ) ) $recipients = [ $recipients ];

            $notFriends = array();

            foreach($recipients as $recipient){
                $user = get_user_by('slug', $recipient);

                if( ! friends_check_friendship( get_current_user_id(), $user->ID ) ) {
                    $notFriends[] = BP_Better_Messages()->functions->get_name($user->ID);
                }
            }

            if(count($notFriends) > 0){
                $message = sprintf(__('%s not on your friends list', 'bp-better-messages'), implode(', ', $notFriends));
                $errors[] = $message;
            }
        }

        public function catch_fast_thread(){
            $requested_page_slug = array_reverse(explode( "/", trim(rtrim(str_replace($_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI']), '?'), '/')));


            $settings_page_slug = array_reverse(explode( "?", str_replace(site_url('').'/', '', BP_Better_Messages()->functions->get_link())));
            $settings_page_slug = array_reverse(array_filter(explode( "/", end($settings_page_slug))));
            $is_bp_profile_page = (function_exists ('bp_is_my_profile')) ? bp_is_my_profile() : false;


            $slug_match = true;

            foreach( $settings_page_slug as $index => $val ){
                if( ! isset($requested_page_slug[$index]) || $requested_page_slug[$index] !== $val ){
                    $slug_match = false;
                    break;
                }
            }

            if(
                isset($_GET['new-message'])
                && isset($_GET['fast'])
                && isset($_GET['to'])
                && ! empty($_GET['fast'])
                && ! empty($_GET['to'])
                && ( $slug_match || $is_bp_profile_page )
            ){
                $getTo = sanitize_text_field($_GET['to']);
                $to = false;

                if( is_numeric($getTo) ){
                    $to = get_userdata( $getTo );
                }

                if( ! $to ) {
                    $to = get_user_by('slug', $getTo );
                }

                if( ! $to ) return false;

                if( BP_Better_Messages()->settings['singleThreadMode'] == '1' ) {
                    $threads = BP_Better_Messages()->functions->find_existing_threads(get_current_user_id(), $to->ID);
                    if( count($threads) > 0) {
                        $url = BP_Better_Messages()->functions->get_link();
                        $url = add_query_arg( 'thread_id', $threads[0], $url );
                        wp_redirect($url);
                        exit;
                    }
                }

                $thread_id = BP_Better_Messages()->functions->get_pm_thread_id($to->ID);
                $url = BP_Better_Messages()->functions->get_link();
                $url = add_query_arg( ['thread_id' => $thread_id, 'scrollToContainer' => '' ], $url );
                wp_redirect($url);
                exit;
            }
        }

        public function admin_notice(){
            if( ! class_exists('BuddyPress') && ! defined('ultimatemember_version') ){
                if(BP_Better_Messages()->settings['chatPage'] == '0'){
                    echo '<div class="notice notice-error">';
                    echo '<p><b>Better Messages</b> require <b><a href="'. admin_url('options-general.php?page=bp-better-messages#general').'">installing Chat Page</a></b> or installed <b>BuddyPress</b> with <b>Messages Component</b> active.</p>';
                    echo '</div>';
                }
            } else {
                if( ! bp_is_active('messages') && ! defined('ultimatemember_version') ) {
                    $url = admin_url('options-general.php?page=bp-components');
                    if( defined('BP_PLATFORM_VERSION') ){
                        $url = admin_url('admin.php?page=bp-components');
                    }
                    echo '<div class="notice notice-error">';
                    echo '<p><b>Better Messages</b> require <b>BuddyPress</b> <b>Messages Component</b> to be active. <a href="'.$url.'">Activate</a></p>';
                    echo '</div>';
                }
            }
        }

        public function chat_page($content){
            $page_id = get_the_ID();
            $chat_page_id = BP_Better_Messages()->settings['chatPage'];

            if( ! is_numeric( $chat_page_id ) ) return $content;

            if( defined('ICL_LANGUAGE_CODE') ){
                $chat_page_id = apply_filters( 'wpml_object_id', $chat_page_id, 'page', true, ICL_LANGUAGE_CODE );
            }

            if( $chat_page_id != $page_id ) return $content;

            if( ! is_user_logged_in() ){
                $messages_content = BP_Better_Messages()->functions->render_login_form();

                if( strpos($content, '[bp-better-messages]') !== FALSE ){
                    return str_replace( '[bp-better-messages]', $messages_content, $content );
                } else {
                    return $messages_content;
                }
            }

            if( function_exists('pmpro_has_membership_access') ) {
                // PM PRO PLUGIN ACTIVE
                $hasaccess = pmpro_has_membership_access(NULL, NULL, false);

                if( ! $hasaccess ) return $content;
            }

            $messages_content = BP_Better_Messages()->functions->get_page( true );

            if( strpos($content, '[bp-better-messages]') !== FALSE ){
                $content = str_replace( '[bp-better-messages]', $messages_content, $content );
            } else {
                $content = $messages_content;
            }

            return $content;
        }

        public function exclude_attachments_global( $query ){

            if( BP_Better_Messages()->settings['attachmentsHide'] === '1' ) {
                if( is_admin() && $query->is_main_query()  && ( isset( $query->query['post_type'] )  && $query->query['post_type'] === 'attachment' ) ) {
                    $meta_query = $query->get('meta_query');
                    if( ! is_array($meta_query) ) $meta_query = array();

                    $meta_query[] = array(
                        'key'     => 'bp-better-messages-attachment',
                        'value'   => '1',
                        'compare' => 'NOT EXISTS'
                    );

                    $query->set('meta_query', $meta_query);
                }
            }

            if( ! is_admin() && $query->is_main_query() && isset( $query->query['pagename'] ) ){
                $meta_query = $query->get('meta_query');
                if( ! is_array($meta_query) ) $meta_query = array();

                $meta_query[] = array(
                    'key'     => 'bp-better-messages-attachment',
                    'value'   => '1',
                    'compare' => 'NOT EXISTS'
                );

                $query->set('meta_query', $meta_query);
            }

        }

        function exclude_attachments($query){
            if( isset( $query['meta_query'] ) ) {
                $meta_query = $query['meta_query'];
            } else {
                $meta_query = array();
            }

            if( ! is_array($meta_query) ) $meta_query = array();

            $meta_query[] = array(
                'key'     => 'bp-better-messages-attachment',
                'value'   => '1',
                'compare' => 'NOT EXISTS'
            );

            $query['meta_query'] = $meta_query;

            return $query;
        }

        function cron_intervals( $schedules )
        {
            /*
             * Cron for our new mailer!
             */
            $schedules[ 'fifteen_minutes' ] = array(
                'interval' => 60 * 15,
                'display'  => esc_html__( 'Every Fifteen Minutes' ),
            );

            $schedules[ 'one_minute' ] = array(
                'interval' => 60,
                'display'  => esc_html__( 'Every Minute' ),
            );

            $notifications_interval = (int) BP_Better_Messages()->settings['notificationsInterval'];
            if( $notifications_interval < 1 ) $notifications_interval = 1;

            $schedules['bp_better_messages_notifications'] = array(
                'interval' => 60 * $notifications_interval,
                'display' => esc_html__('Better Messages Notifications Interval'),
            );

            return $schedules;
        }

        function pm_link_nouveau($buttons, $user_id, $type){
            if( $user_id === get_current_user_id() ) {
                return $buttons;
            }

            if( isset( $buttons['private_message'] ) ){
                $buttons['private_message']['button_attr']['href'] = $this->pm_link();
            }

            return $buttons;
        }

        public function pm_link_legacy(){
            if( ! is_user_logged_in() ) return false;
            $user_id = BP_Better_Messages()->functions->get_member_id();
            if( get_current_user_id() === $user_id ) return false;
            echo '<div class="generic-button bp-better-messages-private-message-link"><a href="' . $this->pm_link() . '">' . __('Private Message', 'bp-better-messages') . '</a></div>';
        }

        public function pm_link( $user_id = false )
        {

            if( ! $user_id ) {
                $user_id = BP_Better_Messages()->functions->get_member_id();
            }

            if( BP_Better_Messages()->settings['fastStart'] == '1' ){
                $link = add_query_arg([
                    'new-message' => '',
                    'fast' => '1',
                    'to' => bp_core_get_username( $user_id )
                ], BP_Better_Messages()->functions->get_link(get_current_user_id()));
                return $link;
            } else {
                $link = add_query_arg([
                    'new-message' => '',
                    'to' => bp_core_get_username( $user_id )
                ], BP_Better_Messages()->functions->get_link(get_current_user_id()));
                return $link;
            }
        }

        public function pm_link_args($args){
            if ( ! is_user_logged_in() ) {
                return false;
            }

            $args['link_href'] = $this->pm_link();

            return $args;
        }

        public function thread_link( $thread_link, $thread_id )
        {
            $link = add_query_arg([
                'thread_id' => $thread_id
            ], BP_Better_Messages()->functions->get_link());

            return $link;
        }

        public function redirect_standard_component()
        {
            if ( bp_is_messages_component() ) {
                $link = BP_Better_Messages()->functions->get_link();

                if( bp_action_variable(0) !== false ){
                    $link = add_query_arg([
                        'thread_id' => bp_action_variable(0)
                    ], BP_Better_Messages()->functions->get_link());
                }

                if( bp_is_current_action('compose') ){
                    $link = add_query_arg([
                        'new-message' => ''
                    ], BP_Better_Messages()->functions->get_link());
                }

                if(isset($_GET['r'])){
                    $username = sanitize_text_field($_GET['r']);
                    $user = get_user_by('slug', $username);
                    if( $user ) {
                        $nice_name = $user->user_nicename;
                        $link = add_query_arg([
                            'new-message' => '',
                            'to' => $nice_name
                        ], BP_Better_Messages()->functions->get_link());
                    }
                }

                wp_redirect( $link );
                exit;
            }
        }

        public function update_db_if_needed(){
            $db_version = get_option('bp_better_messages_db_version', false);
            $current_db = '1.0';
            if( $db_version !== $current_db ) {
                $sql = array();
                $charset_collate = $GLOBALS['wpdb']->get_charset_collate();

                $sql[] = "CREATE TABLE " . bpbm_get_table('messages') . " (
                    id bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    thread_id bigint(20) NOT NULL,
                    sender_id bigint(20) NOT NULL,
                    subject varchar(200) NOT NULL,
                    message longtext NOT NULL,
                    date_sent datetime NOT NULL,
                    KEY sender_id (sender_id),
                    KEY thread_id (thread_id)
                ) {$charset_collate};";

                $sql[] = "CREATE TABLE " . bpbm_get_table('recipients') . " (
                    id bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    user_id bigint(20) NOT NULL,
                    thread_id bigint(20) NOT NULL,
                    unread_count int(10) NOT NULL DEFAULT '0',
                    sender_only tinyint(1) NOT NULL DEFAULT '0',
                    is_deleted tinyint(1) NOT NULL DEFAULT '0',
                    KEY user_id (user_id),
                    KEY thread_id (thread_id),
                    KEY is_deleted (is_deleted),
                    KEY sender_only (sender_only),
                    KEY unread_count (unread_count)
                ) {$charset_collate};";

                $sql[] = "CREATE TABLE " . bpbm_get_table('meta') . " (
                    id bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    message_id bigint(20) NOT NULL,
                    meta_key varchar(255) DEFAULT NULL,
                    meta_value longtext DEFAULT NULL,
                    KEY message_id (message_id),
                    KEY meta_key (meta_key(191))
                ) {$charset_collate};";


                $sql[] = "CREATE TABLE IF NOT EXISTS " . bpbm_get_table('threadsmeta') . " (
                  `meta_id` bigint(20) NOT NULL AUTO_INCREMENT,
                  `bpbm_threads_id` bigint(20) NOT NULL,
                  `meta_key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                  `meta_value` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                  PRIMARY KEY (`meta_id`),
                  KEY `meta_key` (`meta_key`(191)),
                  KEY `thread_id` (`bpbm_threads_id`) USING BTREE
                ) {$charset_collate};";

                require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
                dbDelta($sql);

                update_option('bp_better_messages_db_version', $current_db);
            }
        }

        public function remove_standard_topbar( $wp_admin_bar )
        {
            $wp_admin_bar->remove_node( 'my-account-messages' );
        }

    }

endif;

function BP_Better_Messages_Hooks()
{
    return BP_Better_Messages_Hooks::instance();
}
