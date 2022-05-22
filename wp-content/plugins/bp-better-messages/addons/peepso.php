<?php
defined( 'ABSPATH' ) || exit;

if ( !class_exists( 'BP_Better_Messages_Peepso' ) ){

    class BP_Better_Messages_Peepso
    {

        public static function instance()
        {

            static $instance = null;

            if (null === $instance) {
                $instance = new BP_Better_Messages_Peepso();
            }

            return $instance;
        }

        public function __construct()
        {
            /**
             * Adding header button
             */
            add_filter('peepso_navigation', array(&$this, 'filter_peepso_navigation'));

            add_filter('peepso_profile_actions', array(&$this, 'profile_actions'), 99, 2);
            add_filter('peepso_friends_friend_options', array(&$this, 'member_options'), 10, 2);

            add_filter('peepso_friends_friend_buttons', array(&$this, 'member_buttons'), 20, 2);
            add_filter('peepso_member_buttons', array(&$this, 'member_buttons'), 20, 2);

            add_action('wp_head', array($this, 'counter_in_header'));
            add_filter('bp_core_get_userlink', array($this, 'member_link'), 10, 2);

            add_filter('get_avatar_url', array($this, 'get_avatar_data'), 10, 3);

            if (BP_Better_Messages()->settings['peepsoHeader'] === '1' && !wp_doing_ajax()) {
                add_action('bp_better_messages_before_main_template_rendered', array($this, 'before_main_template_rendered'));
                add_action('bp_better_messages_after_main_template_rendered', array($this, 'after_main_template_rendered'));
            }

            if (BP_Better_Messages()->settings['PSonlyFriendsMode'] === '1' && class_exists('PeepSoFriendsPlugin')) {
                add_filter('bp_better_messages_can_send_message', array($this, 'disable_non_friends_reply'), 10, 3);
                add_action('bp_better_messages_before_new_thread', array($this, 'disable_start_thread_for_non_friends'), 10, 2);
            }

            /*
             * Mini Widgets
             */
            add_filter('bp_better_messages_bottom_widgets', array($this, 'bottom_widgets'), 20, 1);
            add_action('bp_better_messages_mini_tabs_head', array($this, 'bottom_widgets_head_html'), 10, 1);
            add_action('bp_better_messages_mini_tabs_content', array($this, 'bottom_widgets_content_html'), 10, 1);

            /**
             * Side Widgets
             */
            add_filter('bp_better_messages_side_extra_widgets', array($this, 'side_extra_widgets'), 20, 1);
            add_action('bp_better_messages_side_extra_tabs_head', array($this, 'side_extra_tabs_head_html'), 10, 1);
            add_action('bp_better_messages_side_extra_tabs_content', array($this, 'side_extra_tabs_content_html'), 10, 1);

            /**
             * Mobile Widgets
             */
            add_filter('bp_better_messages_mobile_extra_widgets', array($this, 'mobile_extra_widgets'), 20, 1);
            add_action('bp_better_messages_mobile_extra_tabs_head', array($this, 'mobile_extra_tabs_head_html'), 10, 1);
            add_action('bp_better_messages_mobile_extra_tabs_content', array($this, 'mobile_extra_tabs_content_html'), 10, 1);

            add_action('wp_ajax_bp_messages_load_ps_friends_list', array($this, 'load_friends_list'));

            if ( class_exists('PeepSoGroupsPlugin') ) {
                require_once BP_Better_Messages()->path . 'addons/peepso-groups.php';
                BP_Better_Messages_Peepso_Groups::instance();
                add_action( 'wp_ajax_bp_messages_load_ps_groups_list', array( $this, 'load_groups_list' ) );
            }


            add_filter('bp_better_messages_display_name', array( $this, 'display_name_override' ), 10, 2 );
        }

        public function display_name_override( $display_name, $user_id ){
            $user = PeepSoUser::get_instance( $user_id );
            return $user->get_fullname();
        }

        public function load_groups_list(){
            if( ! wp_verify_nonce( $_POST[ 'nonce' ], 'bpbm_edit_nonce' ) ){
                exit;
            }

            if( ! class_exists('PeepSoGroups') ) {
                exit;
            }

            $is_mini = isset($_REQUEST['mini']);

            $PeepSoGroups = new PeepSoGroups();
            $groups = $PeepSoGroups->get_groups(0, -1, 'post_title', 'ASC', '', get_current_user_id() );

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
            $group_id = $group->id;
            ob_start();

            $messages_enabled = 'bpbm-messages-' . BP_Better_Messages_Peepso_Groups::instance()->is_group_messages_enabled( $group_id );
            $thread_id = BP_Better_Messages_Peepso_Groups::instance()->get_group_thread_id( $group->id );
            ?><div class="group <?php echo $messages_enabled; ?>" data-url="<?php echo $group->get_url(); ?>messages/?scrollToContainer" data-group-id="<?php esc_attr_e($group->id); ?>" data-thread-id="<?php esc_attr_e($thread_id); ?>">
            <?php $avatar = $group->get_avatar_url();
            if( !! $avatar ){ ?>
                <div class="pic">
                    <img loading="lazy" src="<?php echo $avatar; ?>" class="avatar avatar-50 photo" width="50" height="50" alt="<?php esc_attr_e($group->name); ?>">
                </div>
            <?php } ?>
            <div class="name"><?php esc_attr_e($group->name); ?></div>
            <div class="actions">
                <a title="<?php _e('Group homepage', 'bp-better-messages'); ?>" href="<?php echo $group->get_url(); ?>" class="open-group"><i class="fas fa-home"></i></a>
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


        public function disable_start_thread_for_non_friends(&$args, &$errors){
            if( ! class_exists('PeepSoFriendsPlugin') ) {
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

                if( ! PeepSoFriendsModel::get_instance()->are_friends( get_current_user_id(), $user->ID ) ) {
                    $notFriends[] = BP_Better_Messages()->functions->get_name($user->ID);
                }
            }

            if(count($notFriends) > 0){
                $message = sprintf(__('%s not on your friends list', 'bp-better-messages'), implode(', ', $notFriends));
                $errors[] = $message;
            }

        }

        public function disable_non_friends_reply( $allowed, $user_id, $thread_id ){
            if( ! class_exists('PeepSoFriendsPlugin') ) {
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

            $allowed = PeepSoFriendsModel::get_instance()->are_friends( $user_id, $friend_id );

            if( ! $allowed ){
                global $bp_better_messages_restrict_send_message;
                $bp_better_messages_restrict_send_message['friendship_needed'] = __('You must become friends to send messages', 'bp-better-messages');
            }

            return $allowed;
        }

        public function load_friends_list(){
            if( ! wp_verify_nonce( $_POST[ 'nonce' ], 'bpbm_edit_nonce' ) ){
                exit;
            }

            if( ! class_exists('PeepSoFriendsPlugin') ) {
                exit;
            }

            $is_mini = isset($_REQUEST['mini']);
            $user_id = (int) get_current_user_id();
            $friends = PeepSoFriendsModel::get_instance()->get_friends_ids( $user_id );

            $return = '';
            if( count( $friends ) > 0 ) {
                if( ! $is_mini ) $return .= '<div class="bp-messages-user-list">';
                foreach($friends as $index => $friend_id){
                    $user = get_userdata($friend_id);
                    if( ! $user ) continue;
                    $return .= BP_Better_Messages()->functions->render_user( $user );
                }
                if( ! $is_mini ) $return .= '</div>';
            } else {
                $return .= '<div class="bp-messages-user-list empty">';
                $return .= '<div class="bpbm-empty-icon"><i class="fas fa-user-friends"></i></div>';
                $return .= '<div class="bpbm-empty-text">' . _x('No friends added yet', 'Combined view - Empty friends list', 'bp-better-messages') . '</div>';
                $return .= '</div>';
            }

            wp_send_json( $return );
        }

        public function mobile_extra_widgets( $tabs ){
            if( BP_Better_Messages()->settings['PSmobileFriendsEnable'] === '1' && class_exists('PeepSoFriendsPlugin') ) {
                $tabs['ps_friends'] = 'ps_friends';
            }

            if( BP_Better_Messages()->settings['PSmobileGroupsEnable'] === '1' && class_exists('PeepSoGroups') ) {
                $tabs[] = 'ps_groups';
            }

            return $tabs;
        }

        public function mobile_extra_tabs_content_html( $tab )
        {
            if ($tab === 'ps_friends' && BP_Better_Messages()->settings['PSmobileFriendsEnable'] === '1' && class_exists('PeepSoFriendsPlugin')) {
                echo '<div class="bpbm-ps-friends-list" style="display: none"><div class="bpbm-loader-icon"><i class="fas fa-spinner fa-spin"></i></div></div>';
            }

            if( $tab === 'ps_groups' && BP_Better_Messages()->settings['PSmobileGroupsEnable'] === '1' && class_exists('PeepSoGroups') ) {
                echo '<div class="bpbm-ps-groups-list" style="display: none"><div class="bpbm-loader-icon"><i class="fas fa-spinner fa-spin"></i></div></div>';
            }
        }

        public function mobile_extra_tabs_head_html( $tab ){
            if( $tab === 'ps_friends' && BP_Better_Messages()->settings['PSmobileFriendsEnable'] === '1' && class_exists('PeepSoFriendsPlugin') ){
                echo '<div data-tab="bpbm-ps-friends-list"><i class="fas fa-user-friends"></i> ' . _x('Friends', 'Combined View Tabs', 'bp-better-messages') . '</div>';
            }

            if( $tab === 'ps_groups' && BP_Better_Messages()->settings['PSmobileGroupsEnable'] === '1' && class_exists('PeepSoGroups') ){
                echo '<div data-tab="bpbm-ps-groups-list"><i class="fas fa-users"></i> ' . _x('Groups', 'Combined View Tabs', 'bp-better-messages') . '</div>';
            }
        }

        public function side_extra_tabs_content_html( $tab ){
            if( $tab === 'ps_friends' && BP_Better_Messages()->settings['PScombinedFriendsEnable'] === '1' && class_exists('PeepSoFriendsPlugin') ) {
                echo '<div class="bpbm-ps-friends-list" style="display: none"><div class="bpbm-loader-icon"><i class="fas fa-spinner fa-spin"></i></div></div>';
            }

            if( $tab === 'ps_groups' && BP_Better_Messages()->settings['PScombinedGroupsEnable'] === '1' && class_exists('PeepSoGroups') ) {
                echo '<div class="bpbm-ps-groups-list" style="display: none"><div class="bpbm-loader-icon"><i class="fas fa-spinner fa-spin"></i></div></div>';
            }
        }

        public function side_extra_tabs_head_html( $tab ){
            if( $tab === 'ps_friends' && BP_Better_Messages()->settings['PScombinedFriendsEnable'] === '1' && class_exists('PeepSoFriendsPlugin') ){
                echo '<div data-tab="bpbm-ps-friends-list"><i class="fas fa-user-friends"></i> ' . _x('Friends', 'Combined View Tabs', 'bp-better-messages') . '</div>';
            }

            if( $tab === 'ps_groups' && BP_Better_Messages()->settings['PScombinedGroupsEnable'] === '1' && class_exists('PeepSoGroups') ){
                echo '<div data-tab="bpbm-ps-groups-list"><i class="fas fa-users"></i> ' . _x('Groups', 'Combined View Tabs', 'bp-better-messages') . '</div>';
            }
        }
        public function side_extra_widgets( $tabs ){
            if( BP_Better_Messages()->settings['PScombinedFriendsEnable'] === '1' && class_exists('PeepSoFriendsPlugin') ){
                $tabs[] = 'ps_friends';
            }

            if( BP_Better_Messages()->settings['PScombinedGroupsEnable'] === '1' && class_exists('PeepSoGroups') ) {
                $tabs[] = 'ps_groups';
            }

            return $tabs;
        }

        public function bottom_widgets( $tabs ){
            if( BP_Better_Messages()->settings['PSminiFriendsEnable'] === '1' && class_exists('PeepSoFriendsPlugin') ) {
                $tabs['ps_friends'] = 'ps_friends';
            }

            if( BP_Better_Messages()->settings['PSminiGroupsEnable'] === '1' && class_exists('PeepSoGroups') ) {
                $PeepSoGroups = new PeepSoGroups();
                $groups = $PeepSoGroups->get_groups(0, 1, 'post_title', 'ASC', '', get_current_user_id());
                if( count( $groups ) > 0 ){
                    $tabs['ps_groups'] = 'ps_groups';
                }
            }

            return $tabs;
        }

        public function bottom_widgets_head_html( $key ){
            if( $key === 'ps_friends' && BP_Better_Messages()->settings['PSminiFriendsEnable'] === '1' && class_exists('PeepSoFriendsPlugin') ) {
                $user_id = (int) get_current_user_id();
                if( PeepSoFriends::get_instance()->has_friends( $user_id ) ){
                    echo '<div data-tab="ps-friends"><i class="fas fa-user-friends"></i> ' . _x('Friends', 'PeepSo Mini Widget', 'bp-better-messages') . '</div>';
                }
            }

            if( $key === 'ps_groups' && BP_Better_Messages()->settings['PSminiGroupsEnable'] === '1' && class_exists('PeepSoGroups') ) {
                echo '<div data-tab="ps-groups"><i class="fas fa-users"></i> ' . _x('Groups', 'PeepSo Mini Widget', 'bp-better-messages') . '</div>';
            }
        }

        public function bottom_widgets_content_html( $chat_footer ){
            if( BP_Better_Messages()->settings['PSminiFriendsEnable'] === '1' && class_exists('PeepSoFriendsPlugin') ){

                $user_id = (int) get_current_user_id();
                $friends = PeepSoFriendsModel::get_instance()->get_friends_ids( $user_id );

                if( ! $friends ) return false;
                if( count( $friends ) === 0 ) return false;
                ?><div class="ps-friends">
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

            if( BP_Better_Messages()->settings['PSminiGroupsEnable'] === '1' && class_exists('PeepSoGroups') ){
                ?><div class="ps-groups">
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


        public function before_main_template_rendered(){
            if( ! is_page() ) return;
            echo PeepSoTemplate::get_before_markup();
            echo '<div class="peepso">';
            echo '<div class="ps-page ps-page--messages">';
            PeepSoTemplate::exec_template('general','navbar');
        }

        public function after_main_template_rendered(){
            if( ! is_page() ) return;
            echo '</div></div>';
            echo PeepSoTemplate::get_after_markup();
        }

        public function get_avatar_data( $url, $id_or_email, $args ){
            $user = false;

            if ( is_numeric( $id_or_email ) ) {
                $user = get_user_by( 'id', absint( $id_or_email ) );
            } elseif ( is_string( $id_or_email ) ) {
                $user = get_user_by('email', $id_or_email );
            } elseif ( $id_or_email instanceof WP_User ) {
                // User object.
                $user = $id_or_email;
            } elseif ( $id_or_email instanceof WP_Post ) {
                // Post object.
                $user = get_user_by( 'id', (int) $id_or_email->post_author );
            } else {
                return $url;
            }

            if( ! $user ) {
                return $url;
            }

            $user = PeepSoUser::get_instance( $user->ID );
            return $user->get_avatar();
        }

        public function member_link( $link, $user_id ){
            $user = PeepSoUser::get_instance( $user_id );
            return $user->get_profileurl();
        }

        /**
         * Add the send message button when a user is viewing the friends list
         * @param  array $options
         * @return array
         */
        public function member_options($options, $user_id)
        {
            $options['bm_message'] = array(
                'label' => _x('Send Message', 'PeepSo Integration', 'bp-better-messages'),
                'click'    => 'BPBMOpenUrlOrNewTab("' . BP_Better_Messages()->hooks->pm_link( $user_id ) . '"); event.preventDefault()',
                'icon' => 'comment',
                'loading' => FALSE,
            );

            return ($options);
        }

        /**
         * Add the send message button when a user is viewing the friends list
         * @param  array $options
         * @return array
         */
        public function member_buttons($options, $user_id)
        {

            $current_user = intval(get_current_user_id());

            if ($current_user !== $user_id ) {
                $options['bm_message'] = array(
                    'class' => 'ps-member__action ps-member__action--message',
                    'click'    => 'BPBMOpenUrlOrNewTab("' . BP_Better_Messages()->hooks->pm_link( $user_id ) . '"); event.preventDefault()',
                    'icon' => 'gcir gci-envelope',
                    'loading' => FALSE,
                );
            }
            return ($options);
        }

        public function profile_actions($act, $user_id)
        {

            $current_user = intval( get_current_user_id() );

            if ($current_user !== $user_id ) {
                $act['bm_message'] = array(
                    'icon'    => 'gcir gci-envelope',
                    'class'   => 'ps-focus__cover-action',
                    'title'   => _x('Start a conversation', 'PeepSo Integration', 'bp-better-messages'),
                    'click'    => 'BPBMOpenUrlOrNewTab("' . BP_Better_Messages()->hooks->pm_link( $user_id ) . '"); event.preventDefault()',
                    'loading' => FALSE,
                    'extra' => 'data-user-id="' . $user_id . '"'
                );

                $base_link = BP_Better_Messages()->functions->get_link( get_current_user_id() );

                if( BP_Better_Messages()->settings['peepsoProfileVideoCall'] === '1') {
                    $link = add_query_arg([
                        'fast-call' => '',
                        'to' => $user_id,
                        'type' => 'video'
                    ], $base_link);

                    $act['bm_video_call'] = array(
                        'icon' => 'gci gci-video',
                        'class' => 'ps-focus__cover-action bpbm-video-call',
                        'title' => _x('Video Call', 'PeepSo Integration', 'bp-better-messages'),
                        'click' => 'event.preventDefault();',
                        'loading' => FALSE,
                        'extra' => 'data-user-id="' . $user_id . '" data-url="' . $link . '"'
                    );
                }


                if( BP_Better_Messages()->settings['peepsoProfileAudioCall'] === '1') {
                    $link = add_query_arg([
                        'fast-call' => '',
                        'to' => $user_id,
                        'type' => 'audio'
                    ], $base_link);

                    $act['bm_audio_call'] = array(
                        'icon' => 'gci gci-phone',
                        'class' => 'ps-focus__cover-action bpbm-audio-call',
                        'title' => _x('Audio Call', 'PeepSo Integration', 'bp-better-messages'),
                        'click' => 'event.preventDefault();',
                        'loading' => FALSE,
                        'extra' => 'data-user-id="' . $user_id . '" data-url="' . $link . '"'
                    );
                }
            }

            return ($act);
        }

        public function filter_peepso_navigation($navigation)
        {

            $received = array(
                'href' => BP_Better_Messages()->functions->get_link(),
                'icon' => 'gcis gci-envelope',
                'class' => 'ps-notif--better-messages',
                'title' => _x('New Messages', 'PeepSo Integration', 'bp-better-messages'),
                'label' => _x('Messages', 'Peepso Integration', 'bp-better-messages'),
                'count' => 0,
                'primary'           => FALSE,
                'secondary'         => TRUE,
                'mobile-primary'    => FALSE,
                'mobile-secondary'  => TRUE,
                'widget'            => FALSE,
                'notifications'     => TRUE,
                'icon-only'         => TRUE,
            );

            $navigation['better-messages-notification'] = $received;

            return ($navigation);
        }

        public function counter_in_header(){
            if( ! is_user_logged_in() ) return false;
            ob_start(); ?>
            <script type="text/javascript">
                jQuery(document).on('bp-better-messages-update-unread', function( event ) {
                    var unread = parseInt(event.detail.unread);
                    var private_messages = jQuery('.ps-notif--better-messages .js-counter');

                    private_messages.each(function(){
                        var item = jQuery(this);
                        if( unread > 0 ){
                            item.text(unread);
                        } else {
                            item.text('');
                        }
                    });
                });
            </script>
            <?php
            $script = ob_get_clean();

            echo BP_Better_Messages()->functions->minify_js( $script );
        }
    }
}

