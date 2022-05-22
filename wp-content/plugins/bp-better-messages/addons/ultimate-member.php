<?php
defined( 'ABSPATH' ) || exit;

if ( !class_exists( 'BP_Better_Messages_Ultimate_Member' ) ){

    class BP_Better_Messages_Ultimate_Member
    {

        public static function instance()
        {

            static $instance = null;

            if (null === $instance) {
                $instance = new BP_Better_Messages_Ultimate_Member();
            }

            return $instance;
        }

        public function __construct(){
            add_filter( 'um_user_profile_tabs', array( $this, 'um_add_profile_tab' ), 200 );

            add_action( 'um_profile_content_messages_default', array( $this, 'um_content_messages' ), 1 );

            if( BP_Better_Messages()->settings['chatPage'] === '0' ) {
                add_filter('bp_better_messages_page', array($this, 'um_message_page_url'), 10, 2);
            }

            if( BP_Better_Messages()->settings['umProfilePMButton'] === '1' ) {
                add_action('um_profile_navbar', array($this, 'um_profile_message_button'), 5);
            }

            if( BP_Better_Messages()->settings['userListButton'] == '1' ) {
                add_action('um_members_just_after_name_tmpl', array($this, 'um_pm_link'), 10);
                add_action('um_members_list_just_after_actions_tmpl', array( $this, 'um_pm_link' ), 10);
            }

            add_filter( 'bp_core_get_userlink', array( $this, 'um_member_link' ), 10, 2 );

            if( BP_Better_Messages()->settings['umOnlyFriendsMode'] === '1' && class_exists('UM_Friends_API') ) {
                add_filter('bp_better_messages_can_send_message',  array($this, 'disable_non_friends_reply'), 10, 3);
                add_action('bp_better_messages_before_new_thread', array($this, 'disable_start_thread_for_non_friends'), 10, 2);
            }

            if( BP_Better_Messages()->settings['umOnlyFollowersMode'] === '1' && class_exists('UM_Followers_API') ) {
                add_filter('bp_better_messages_can_send_message',  array($this, 'disable_non_followers_reply'), 10, 3);
                add_action('bp_better_messages_before_new_thread', array($this, 'disable_start_thread_for_non_followers'), 10, 2);
            }

            add_action( 'wp_head', array( $this, 'um_counter_in_profile' ) );

            /*
             * Mini Widgets
             */
            add_filter('bp_better_messages_bottom_widgets',    array( $this, 'bottom_widgets' ), 20, 1 );
            add_action('bp_better_messages_mini_tabs_head',    array( $this, 'bottom_widgets_head_html' ), 10, 1 );
            add_action('bp_better_messages_mini_tabs_content', array( $this, 'bottom_widgets_content_html' ), 10, 1 );

            /**
             * Side Widgets
             */
            add_filter('bp_better_messages_side_extra_widgets',      array( $this, 'side_extra_widgets' ), 20, 1 );
            add_action('bp_better_messages_side_extra_tabs_head',    array( $this, 'side_extra_tabs_head_html'), 10, 1 );
            add_action('bp_better_messages_side_extra_tabs_content', array( $this, 'side_extra_tabs_content_html'), 10, 1 );

            /**
             * Mobile Widgets
             */
            add_filter('bp_better_messages_mobile_extra_widgets',   array( $this, 'mobile_extra_widgets' ), 20, 1 );
            add_action('bp_better_messages_mobile_extra_tabs_head',    array( $this, 'mobile_extra_tabs_head_html'), 10, 1 );
            add_action('bp_better_messages_mobile_extra_tabs_content', array( $this, 'mobile_extra_tabs_content_html'), 10, 1 );

            add_action( 'wp_ajax_bp_messages_load_um_friends_list', array( $this, 'load_friends_list' ) );


            if( class_exists('UM_Groups') ){
                require_once BP_Better_Messages()->path . 'addons/ultimate-member-groups.php';
                Better_Messages_Ultimate_Member_Groups::instance();

                add_action( 'wp_ajax_bp_messages_load_um_groups_list', array( $this, 'load_groups_list' ) );
            }

        }

        public function load_groups_list(){
            if( ! wp_verify_nonce( $_POST[ 'nonce' ], 'bpbm_edit_nonce' ) ){
                exit;
            }

            if( ! class_exists('UM_Groups') ) {
                exit;
            }

            $is_mini = isset($_REQUEST['mini']);

            $groups = [];
            $user_groups = UM()->Groups()->member()->get_groups_joined();

            if( count( $user_groups ) > 0 ) {
                foreach ($user_groups as $user_group) {
                    $groups[] = get_post($user_group->group_id);
                }
            }

            $return = '';

            if( ! $is_mini ) $return .= '<div class="bp-messages-group-list">';

            if( count( $groups ) > 0 ) {
                foreach ($groups as $group) {
                    if( $group->id === NULL ) continue;

                    $return .= $this->render_group($group);
                }
                if( ! $is_mini ) $return .= '</div>';
            } else {
                $return .= '<div class="bp-messages-user-list empty">';
                $return .= '<div class="bpbm-empty-icon"><i class="fas fa-users"></i></div>';
                $return .= '<div class="bpbm-empty-text">' . _x('No groups added yet', 'Combined view - Empty groups list', 'bp-better-messages') . '</div>';
                $return .= '</div>';
            }

            wp_send_json( $return );
        }

        public function render_group( $group ){
            $group_id = $group->ID;
            ob_start();

            $messages_enabled = 'bpbm-messages-' . Better_Messages_Ultimate_Member_Groups::instance()->is_group_messages_enabled( $group_id );
            $thread_id = Better_Messages_Ultimate_Member_Groups::instance()->get_group_thread_id( $group_id );
            ?><div class="group <?php echo $messages_enabled; ?>" data-url="<?php echo add_query_arg(['tab' => 'messages', 'scrollToContainer' => ''], get_permalink( $group_id )); ?>" data-group-id="<?php esc_attr_e($group_id); ?>" data-thread-id="<?php esc_attr_e($thread_id); ?>">
            <?php $avatar = UM()->Groups()->api()->get_group_image( $group->ID, 'default', 50, 50, false );
            if( !! $avatar ){ ?>
                <div class="pic">
                    <?php echo $avatar; ?>
                </div>
            <?php } ?>
            <div class="name"><?php echo esc_html($group->post_title); ?></div>
            <div class="actions">
                <a title="<?php _e('Group homepage', 'bp-better-messages'); ?>" href="<?php echo get_permalink( $group_id ); ?>" class="open-group"><i class="fas fa-home"></i></a>
            </div>
            <div class="loading">
                <div class="bounce1"></div>
                <div class="bounce2"></div>
                <div class="bounce3"></div>
            </div>
            </div>
            <?php

            return ob_get_clean();
        }

        public function load_friends_list(){
            if( ! wp_verify_nonce( $_POST[ 'nonce' ], 'bpbm_edit_nonce' ) ){
                exit;
            }

            if( ! class_exists('UM_Friends_API') ) {
                exit;
            }

            $user_id = (int) get_current_user_id();
            $friends = UM()->Friends_API()->api()->friends( $user_id );

            $is_mini = isset($_REQUEST['mini']);

            $return = '';
            if( !! $friends && count( $friends ) > 0 ) {
                if( ! $is_mini ) $return .= '<div class="bp-messages-user-list">';
                 foreach($friends as $index => $users){
                    if( $user_id === (int) $users['user_id1'] ) {
                        $friend_id = (int) $users['user_id2'];
                    } else {
                        $friend_id = (int) $users['user_id1'];
                    }

                    $user = get_userdata($friend_id);
                    if( ! $user ) continue;
                    $return .= BP_Better_Messages()->functions->render_user( $user );
                }
                $return .= '</div>';
            } else {
                $return .= '<div class="bp-messages-user-list empty">';
                $return .= '<div class="bpbm-empty-icon"><i class="fas fa-user-friends"></i></div>';
                $return .= '<div class="bpbm-empty-text">' . _x('No friends added yet', 'Combined view - Empty friends list', 'bp-better-messages') . '</div>';
                $return .= '</div>';
            }

            wp_send_json( $return );
        }

        public function side_extra_tabs_content_html( $tab ){
            if( $tab === 'um_friends' && BP_Better_Messages()->settings['UMcombinedFriendsEnable'] === '1' && class_exists('UM_Friends_API') ) {
                echo '<div class="bpbm-um-friends-list" style="display: none"><div class="bpbm-loader-icon"><i class="fas fa-spinner fa-spin"></i></div></div>';
            }

            if( $tab === 'bmum_groups' && BP_Better_Messages()->settings['UMcombinedGroupsEnable'] === '1' && class_exists('UM_Groups') ) {
                echo '<div class="bpbm-bmum-groups-list" style="display: none"><div class="bpbm-loader-icon"><i class="fas fa-spinner fa-spin"></i></div></div>';
            }
        }

        public function side_extra_tabs_head_html( $tab ){
            if( $tab === 'um_friends' && BP_Better_Messages()->settings['UMcombinedFriendsEnable'] === '1' && class_exists('UM_Friends_API') ){
                echo '<div data-tab="bpbm-um-friends-list"><i class="fas fa-user-friends"></i> ' . _x('Friends', 'Combined View Tabs', 'bp-better-messages') . '</div>';
            }

            if( $tab === 'bmum_groups' && BP_Better_Messages()->settings['UMcombinedGroupsEnable'] === '1' && class_exists('UM_Groups') ){
                echo '<div data-tab="bpbm-bmum-groups-list"><i class="fas fa-users"></i> ' . _x('Groups', 'Combined View Tabs', 'bp-better-messages') . '</div>';
            }
        }

        public function side_extra_widgets( $tabs ){
            if( BP_Better_Messages()->settings['UMcombinedFriendsEnable'] === '1' && class_exists('UM_Friends_API') ){
                $tabs[] = 'um_friends';
            }

            if( BP_Better_Messages()->settings['UMcombinedGroupsEnable'] === '1' && class_exists('UM_Groups') ) {
                $tabs[] = 'bmum_groups';
            }

            return $tabs;
        }

        public function mobile_extra_widgets( $tabs ){
            if( BP_Better_Messages()->settings['UMmobileFriendsEnable'] === '1' && class_exists('UM_Friends_API') ) {
                $tabs['um_friends'] = 'um_friends';
            }

            if( BP_Better_Messages()->settings['UMmobileGroupsEnable'] === '1' && class_exists('UM_Groups') ) {
                $tabs[] = 'bmum_groups';
            }

            return $tabs;
        }


        public function mobile_extra_tabs_content_html( $tab ){
            if( $tab === 'um_friends' && BP_Better_Messages()->settings['UMmobileFriendsEnable'] === '1' && class_exists('UM_Friends_API') ) {
                echo '<div class="bpbm-um-friends-list" style="display: none"><div class="bpbm-loader-icon"><i class="fas fa-spinner fa-spin"></i></div></div>';
            }

            if( $tab === 'bmum_groups' && BP_Better_Messages()->settings['UMmobileGroupsEnable'] === '1' &&  class_exists('UM_Groups') ) {
                echo '<div class="bpbm-bmum-groups-list" style="display: none"><div class="bpbm-loader-icon"><i class="fas fa-spinner fa-spin"></i></div></div>';
            }
        }

        public function mobile_extra_tabs_head_html( $tab ){
            if( $tab === 'um_friends' && BP_Better_Messages()->settings['UMmobileFriendsEnable'] === '1' && class_exists('UM_Friends_API') ){
                echo '<div data-tab="bpbm-um-friends-list"><i class="fas fa-user-friends"></i> ' . _x('Friends', 'Combined View Tabs', 'bp-better-messages') . '</div>';
            }

            if( $tab === 'bmum_groups' && BP_Better_Messages()->settings['UMmobileGroupsEnable'] === '1' && class_exists('UM_Groups') ){
                echo '<div data-tab="bpbm-bmum-groups-list"><i class="fas fa-users"></i> ' . _x('Groups', 'Combined View Tabs', 'bp-better-messages') . '</div>';
            }
        }

        public function bottom_widgets( $tabs ){
            if( BP_Better_Messages()->settings['UMminiFriendsEnable'] === '1' && class_exists('UM_Friends_API') ) {
                $tabs['um_friends'] = 'um_friends';
            }

            if( BP_Better_Messages()->settings['UMminiGroupsEnable'] === '1' && class_exists('UM_Groups') ) {
                $user_groups = UM()->Groups()->member()->get_groups_joined();
                if( count( $user_groups ) > 0 ) {
                    $tabs['um_groups'] = 'um_groups';
                }
            }

            return $tabs;
        }

        public function bottom_widgets_head_html( $key ){
            if( $key === 'um_friends' && BP_Better_Messages()->settings['UMminiFriendsEnable'] === '1' && class_exists('UM_Friends_API') ) {
                $user_id = (int) get_current_user_id();
                $friends = (int) UM()->Friends_API()->api()->count_friends_plain( $user_id );

                if( $friends > 0 ){
                    echo '<div data-tab="um-friends"><i class="fas fa-user-friends"></i> ' . _x('Friends', 'Ultimate Member Mini Widget', 'bp-better-messages') . '</div>';
                }
            }

            if( $key === 'um_groups' && BP_Better_Messages()->settings['UMminiGroupsEnable'] === '1' && defined('um_groups_plugin') ) {
                echo '<div data-tab="bmum-groups"><i class="fas fa-users"></i> ' . _x('Groups', 'Ultimate Member Mini Widget', 'bp-better-messages') . '</div>';
            }
        }

        public function bottom_widgets_content_html( $chat_footer ){
            if( BP_Better_Messages()->settings['UMminiFriendsEnable'] === '1' && class_exists('UM_Friends_API') ){
            ?><div class="um-friends">
                <div class="scroller scrollbar-inner">
                    <div class="bpbm-search-in-list">
                        <input title="<?php _e('Search...', 'bp-better-messages'); ?>" type="text" name="bpbm-search" value="" placeholder="<?php _e('Search...', 'bp-better-messages'); ?>">
                    </div>
                    <div class="bp-messages-user-list">
                        <div class="bpbm-loader-icon"><i class="fas fa-spinner fa-spin"></i></div>
                    </div>
                </div>
                <?php echo $chat_footer; ?>
            </div>
            <?php
            }

            if( BP_Better_Messages()->settings['UMminiGroupsEnable'] === '1' && defined('um_groups_plugin') ){
                ?><div class="bmum-groups">
                <div class="scroller scrollbar-inner">
                    <div class="bpbm-search-in-list">
                        <input title="<?php _e('Search...', 'bp-better-messages'); ?>" type="text" name="bpbm-search" value="" placeholder="<?php _e('Search...', 'bp-better-messages'); ?>">
                    </div>
                    <div class="bp-messages-group-list">
                        <div class="bpbm-loader-icon"><i class="fas fa-spinner fa-spin"></i></div>
                    </div>
                </div>
                <?php echo $chat_footer; ?>
                </div>
            <?php
            }
        }

        public function um_counter_in_profile(){
            if( ! is_user_logged_in() ) return false;
            ob_start();

            //<span class="um-tab-notifier">1</span>
            ?>
            <script type="text/javascript">
                jQuery(document).on('bp-better-messages-update-unread', function( event ) {
                    var unread = parseInt(event.detail.unread);
                    var private_messages = jQuery('.um-profile-nav-item.um-profile-nav-messages > a');

                    private_messages.each(function(){
                        var tab = jQuery(this);

                        if( unread > 0 ){
                            var count = tab.find('span.um-tab-notifier');

                            if( count.length === 0 ){
                                tab.append('<span class="um-tab-notifier">' + unread + '</span>');
                            } else {
                                count.text(unread);
                            }
                        } else {
                            tab.find('span.um-tab-notifier').remove();
                        }
                    });
                });
            </script>
            <?php
            $script = ob_get_clean();

            echo BP_Better_Messages()->functions->minify_js( $script );
        }

        public function disable_start_thread_for_non_followers(&$args, &$errors){
            if( ! class_exists('UM_Followers_API') ) {
                return null;
            }

            if( current_user_can('manage_options' ) ) {
                return null;
            }

            $recipients = $args['recipients'];

            if( ! is_array( $recipients ) ) $recipients = [ $recipients ];

            $notFollowed = array();

            foreach($recipients as $recipient){
                $user = get_user_by('slug', $recipient);

                $allowed = UM()->Followers_API()->api()->followed( get_current_user_id(), $user->ID );

                if( ! $allowed ) {
                    $allowed = UM()->Followers_API()->api()->followed($user->ID, get_current_user_id() );
                }

                if( ! $allowed ) {
                    $notFollowed[] = BP_Better_Messages()->functions->get_name($user->ID);
                }
            }

            if(count($notFollowed) > 0){
                $message = sprintf(_x('%s need to be followed to start new conversation', 'Ultimate member - follower restriction', 'bp-better-messages'), implode(', ', $notFollowed));
                $errors[] = $message;
            }

        }

        public function disable_non_followers_reply( $allowed, $user_id, $thread_id ){
            if( ! class_exists('UM_Followers_API') ) {
                return $allowed;
            }

            $participants = BP_Better_Messages()->functions->get_participants($thread_id);
            if( count($participants['users']) !== 2) return $allowed;
            unset($participants['users'][$user_id]);
            reset($participants['users']);

            $user_id_2 = key($participants['users']);
            /**
             * Allow users reply to admins even if not friends
             */
            if( current_user_can('manage_options') || user_can( $user_id_2, 'manage_options' ) ) {
                return $allowed;
            }

            $allowed = UM()->Followers_API()->api()->followed( $user_id, $user_id_2 );

            if( ! $allowed ) {
                $allowed = UM()->Followers_API()->api()->followed($user_id_2, $user_id);
            }

            if( ! $allowed ){
                global $bp_better_messages_restrict_send_message;
                $bp_better_messages_restrict_send_message['follow_needed'] = _x('You must follower this user to send messages', 'Ultimate member - follower restriction', 'bp-better-messages');
            }

            return $allowed;
        }

        public function disable_start_thread_for_non_friends(&$args, &$errors){
            if( ! class_exists('UM_Friends_API') ) {
                return null;
            }

            if( current_user_can('manage_options' ) ) {
                return null;
            }

            $recipients = $args['recipients'];

            if( ! is_array( $recipients ) ) $recipients = [ $recipients ];

            $notFriends = array();

            foreach($recipients as $recipient){
                $user = get_user_by('slug', $recipient);

                if( ! UM()->Friends_API()->api()->is_friend( get_current_user_id(), $user->ID ) ) {
                    $notFriends[] = BP_Better_Messages()->functions->get_name($user->ID);
                }
            }

            if(count($notFriends) > 0){
                $message = sprintf(__('%s not on your friends list', 'bp-better-messages'), implode(', ', $notFriends));
                $errors[] = $message;
            }

        }

        public function disable_non_friends_reply( $allowed, $user_id, $thread_id ){
            if( ! class_exists('UM_Friends_API') ) {
                return $allowed;
            }

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

            $allowed = UM()->Friends_API()->api()->is_friend( $user_id, $friend_id );

            if( ! $allowed ){
                global $bp_better_messages_restrict_send_message;
                $bp_better_messages_restrict_send_message['friendship_needed'] = __('You must become friends to send messages', 'bp-better-messages');
            }

            return $allowed;
        }

        public function um_message_page_url( $url, $user_id ){
            $um_profile_url = um_user_profile_url( $user_id );
            return add_query_arg( ['profiletab' => 'messages'], $um_profile_url );
        }

        public function um_add_profile_tab( $tabs ) {
            $user_id  = (int) um_profile_id();
            $can_view = is_user_logged_in() && get_current_user_id() === $user_id;

            if( $can_view ) {
                $tabs['messages'] = array(
                    'name' => __('Messages', 'bp-better-messages'),
                    'icon' => 'um-faicon-envelope-o',
                    'default_privacy' => 3,
                );
            }

            return $tabs;
        }

        public function um_content_messages( $args ) {
            echo BP_Better_Messages()->functions->get_page( true );
        }


        public function um_pm_link( $args ){
            if ( ! is_user_logged_in() ) return;

            $base_url = BP_Better_Messages()->functions->get_link(get_current_user_id());

            $args = [
                'new-message' => '',
                'to' => '{{{user.id}}}'
            ];

            if( BP_Better_Messages()->settings['fastStart'] == '1'){
                $args['fast'] = '1';
            }

            $url = add_query_arg( $args, $base_url );

            $class = 'um-members-bpbm-btn';

            if( doing_action('um_members_list_just_after_actions_tmpl') ){
                $class .= ' um-members-list-footer-button-wrapper';
            }
            echo '<div class="' . $class . '">';
            echo '<a href="' . $url . '" class="um-button um-alt" target="_self">' . __('Private Message', 'bp-better-messages') . '</a>';
            echo '</div>';
        }

        public function um_member_link($link, $user_id){
            $um_profile_url = um_user_profile_url( $user_id );
            return $um_profile_url;
        }

        public function um_profile_message_button( $args ){
            if( ! function_exists('um_profile_id') ) return false;
            $user_id = um_profile_id();

            if ( is_user_logged_in() ) {
                if ( get_current_user_id() == $user_id ) {
                    return;
                }
            }
            ?>
            <div class="um-messaging-btn">
                <?php echo do_shortcode( '[bp_better_messages_pm_button text="' . __('Private Message', 'bp-better-messages') . '" target="_self" fast_start="1" user_id="' . $user_id . '"]' ) ?>
            </div>
            <?php
        }


    }
}

