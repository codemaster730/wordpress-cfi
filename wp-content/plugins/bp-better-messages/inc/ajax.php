<?php
defined( 'ABSPATH' ) || exit;

if ( !class_exists( 'BP_Better_Messages_Ajax' ) ):

    class BP_Better_Messages_Ajax
    {

        public static function instance()
        {

            static $instance = null;

            if ( null === $instance ) {
                $instance = new BP_Better_Messages_Ajax();
            }

            return $instance;
        }

        public function __construct()
        {
            /**
             * Ajax checker actions
             */
            add_action( 'wp_ajax_bp_messages_thread_check_new', array( $this, 'thread_check_new' ) );
            add_action( 'wp_ajax_bp_messages_check_new',        array( $this, 'check_new' ) );

            /**
             * New thread actions
             */
            add_action( 'wp_ajax_bp_messages_new_thread',   array( $this, 'new_thread' ) );
            add_action( 'wp_ajax_bp_messages_send_message', array( $this, 'send_message' ) );

            if( BP_Better_Messages()->settings['disableUsersSearch'] !== '1' ) {
                add_action('wp_ajax_bp_messages_autocomplete', array($this, 'bp_messages_autocomplete_results'));
            }

            /**
             * Thread actions
             */
            add_action( 'wp_ajax_bp_messages_favorite',              array( $this, 'favorite' ) );

            add_action('wp_ajax_bp_messages_delete_thread',          array($this, 'delete_thread'));
            add_action('wp_ajax_bp_messages_un_delete_thread',       array($this, 'un_delete_thread'));

            add_action( 'wp_ajax_bp_messages_thread_load_messages',  array( $this, 'thread_load_messages' ) );
            add_action( 'wp_ajax_nopriv_bp_messages_thread_load_messages',  array( $this, 'thread_load_messages' ) );

            add_action( 'wp_ajax_bp_messages_prepare_edit_message',  array( $this, 'prepare_edit_message' ) );

            add_action( 'wp_ajax_bp_messages_last_activity_refresh', array( $this, 'last_activity_refresh' ) );
            add_action( 'wp_ajax_bp_messages_get_pm_thread',         array( $this, 'get_pm_thread' ) );
            add_action( 'wp_ajax_bp_messages_delete_message',        array( $this, 'delete_message' ) );

            /**
             * Group Thread actions
             */
            add_action('wp_ajax_bp_better_messages_exclude_user_from_thread', array( $this, 'exclude_user_from_thread' ));
            add_action('wp_ajax_bp_better_messages_add_user_to_thread',       array( $this, 'add_user_to_thread') );

            /**
             * List threads
             */
            add_action( 'wp_ajax_bp_messages_get_more_threads',               array( $this, 'get_more_threads' ) );

            /*
             * User settings
             */
            add_action( 'wp_ajax_bp_messages_change_user_option',             array( $this, 'change_user_option' ) );

            /**
             * Thread settings
             */
            add_action( 'wp_ajax_bp_messages_change_thread_option',             array( $this, 'change_thread_option' ) );

            add_action( 'wp_ajax_bp_messages_load_via_ajax', array( $this, 'load_via_ajax' ) );

            if( BP_Better_Messages()->settings['allowMuteThreads'] === '1' ) {
                add_action('wp_ajax_bp_messages_mute_thread', array($this, 'mute_thread'));
                add_action('wp_ajax_bp_messages_unmute_thread', array($this, 'unmute_thread'));
            }

            if( BP_Better_Messages()->settings['allowEditMessages'] === '1' ) {
                add_action('wp_ajax_bp_messages_get_edit_message', array($this, 'get_edit_message'));
            }

            add_action( 'wp_ajax_bp_messages_admin_import_options', array( $this, 'import_admin_options' ) );
            add_action( 'wp_ajax_bp_messages_admin_export_options', array( $this, 'export_admin_options' ) );

            add_action( 'wp_ajax_bp_messages_leave_thread', array( $this, 'leave_thread' ) );

            if( BP_Better_Messages()->settings['disableSearch'] === '0' ) {
                add_action('wp_ajax_bp_messages_thread_search', array($this, 'thread_search'));
            }

            add_action('wp_ajax_bp_messages_load_thread_participants', array($this, 'load_thread_participants'));
            add_action('wp_ajax_bp_messages_erase_thread', array($this, 'erase_thread') );
            add_action('wp_ajax_bp_messages_clear_thread', array($this, 'clear_thread') );

            add_action( 'wp_ajax_bp_messages_load_friends_list', array( $this, 'load_friends_list' ) );
            add_action( 'wp_ajax_bp_messages_load_groups_list', array( $this, 'load_groups_list' ) );

            add_action('wp_ajax_bm_get_thread_mentions_suggestions', array( $this, 'get_thread_mentions_suggestions' ) );

            add_action( 'wp_ajax_bm_messages_get_messages',        array( $this, 'get_messages' ) );
            add_action( 'wp_ajax_bm_messages_get_threads',         array( $this, 'get_threads' ) );
            add_action( 'wp_ajax_bm_messages_sync_with_server',    array( $this, 'sync_with_server' ) );
        }

        public function sync_with_server(){
            exit;
            if( ! wp_verify_nonce( $_POST[ 'nonce' ], 'bpbm_edit_nonce' ) ){
                exit;
            }

            global $wpdb;
            $current_user_id = get_current_user_id();

            $get_threads = $wpdb->get_results( $wpdb->prepare("
            SELECT thread_id, GROUP_CONCAT(user_id) as participants
            FROM `" . bpbm_get_table("recipients") . "`
            WHERE `thread_id` IN (
                SELECT thread_id
                FROM `" . bpbm_get_table("recipients") . "`
                WHERE is_deleted = 0
                AND user_id = %d
            )
            GROUP BY thread_id", $current_user_id) );

            $threads = [];
            foreach ( $get_threads as $thread ){
                $threads[] = [
                    'id'            => (int) $thread->thread_id,
                    'participants'  => array_map('intval', explode(',', $thread->participants))
                ];
            }

            $messages = $wpdb->get_results( $wpdb->prepare(
            "SELECT id, sender_id, thread_id, message 
            FROM `" . bpbm_get_table("messages") . "`
            WHERE `thread_id` IN (
                SELECT thread_id
                FROM `" . bpbm_get_table("recipients") . "`
                WHERE is_deleted = 0
                AND user_id = %d
            ) AND `sender_id` != 0", $current_user_id) );

            $user_ids = [];

            foreach ( $messages as $key => $message ){
                $messages[ $key ]->id        = (int) $message->id;
                $messages[ $key ]->sender_id = (int) $message->sender_id;
                $messages[ $key ]->thread_id = (int) $message->thread_id;
                $messages[ $key ]->message   = BP_Better_Messages()->functions->format_message( $message->message, $message->id, 'stack', $current_user_id );
                $messages[ $key ]->favorited = (bool) BP_Better_Messages()->functions->is_message_starred( $message->id, $current_user_id );

                $user_ids[] = (int) $message->sender_id;
            }

            $user_ids = array_unique( $user_ids );

            $users = [];
            foreach( $user_ids as $user_id ){
                $user = get_userdata( $user_id );
                if( ! $user ) continue;
                $users[] = [
                    'id'     => (int) $user_id,
                    'name'   => BP_Better_Messages()->functions->get_name( $user_id ),
                    'avatar' => BP_Better_Messages()->functions->get_avatar( $user_id, 50, ['html' => false] ),
                    'url'    => bp_core_get_userlink( $user_id, false, true )
                ];
            }

            wp_send_json([
                'threads'  => $threads,
                'messages' => $messages,
                'users'    => $users
            ]);
        }

        public function get_threads(){
            if( ! wp_verify_nonce( $_POST[ 'nonce' ], 'bpbm_edit_nonce' ) ){
                exit;
            }

            global $wpdb;
            $current_user_id = get_current_user_id();

            $sql = $wpdb->prepare("
            SELECT
            `recipients`.`thread_id` as `thread_id`,
            `recipients`.`unread_count` as `unread_count`,
            MAX(`messages`.`id`) as `message_id`,
            MAX(`messages`.`date_sent`) as `date_sent`,
            subject
            FROM
                " . bpbm_get_table('recipients') . " as recipients
            INNER JOIN " . bpbm_get_table('messages') . " messages 
                ON recipients.`thread_id` = messages.`thread_id`
            LEFT JOIN " . bpbm_get_table('threadsmeta') . " threadsmeta ON
                ( threadsmeta.`bpbm_threads_id` = messages.`thread_id`
                AND threadsmeta.meta_key = 'exclude_from_threads_list' )
            WHERE
                recipients.`user_id` = %d 
                AND recipients.`is_deleted` = 0
                AND date_sent != '0000-00-00 00:00:00'
                AND `threadsmeta`.`meta_value` IS NULL
            GROUP BY recipients.thread_id
            ORDER BY date_sent DESC", $current_user_id);

            $get_threads = $wpdb->get_results( $sql );

            $muted_threads = BP_Better_Messages()->functions->get_user_muted_threads( get_current_user_id() );

            $threads  = [];
            $user_ids = [];

            foreach ( $get_threads as $thread ){
                $recipients = BP_Better_Messages()->functions->get_recipients( $thread->thread_id );
                unset($recipients[$current_user_id]);

                $_user_ids = array_map( 'intval', array_keys($recipients) );
                foreach ( $_user_ids as $user_id ){ $user_ids[] = $user_id; }

                $thread_type = BP_Better_Messages()->functions->get_thread_type( $thread->thread_id );

                $image    = '';
                $title    = '';

                $group_id = false;

                if( $thread_type === 'group' ) {
                    if( class_exists('BP_Groups_Group') ) {
                        $group_id = BP_Better_Messages()->functions->get_thread_meta($thread->thread_id, 'group_id');
                        $group = new BP_Groups_Group((int)$group_id);

                        $image = bp_core_fetch_avatar( array(
                            'item_id'    => $group_id,
                            'avatar_dir' => 'group-avatars',
                            'object'     => 'group',
                            'type'       => 'thumb',
                            'html'       => false
                        ));

                        $title = bp_get_group_name($group);
                    }

                    if( class_exists('PeepSoGroup') ){
                        $group_id = BP_Better_Messages()->functions->get_thread_meta($thread->thread_id, 'peepso_group_id');
                        $group = new PeepSoGroup( (int) $group_id );
                        $image = $group->get_avatar_url();
                        $title = $group->name;
                    }

                    if( class_exists('UM_Groups') ){
                        $group_id = BP_Better_Messages()->functions->get_thread_meta($thread->thread_id, 'um_group_id');
                        $group    = get_post( (int) $group_id );
                        $title = esc_html( $group->post_title );
                        $image = UM()->Groups()->api()->get_group_image( $group->ID, 'default', 50, 50, true );
                    }
                }

                if( $thread_type === 'chat-room' ) {
                    $chat_id = (int) BP_Better_Messages()->functions->get_thread_meta($thread->thread_id, 'chat_id');
                } else {
                    $chat_id = false;
                }

                $delete_allowed = true;

                if( !! $group_id || !! $chat_id ){
                    $delete_allowed = false;
                }

                $is_muted = false;
                if( isset($muted_threads[ $thread->thread_id ]) ){
                    $is_muted = true;
                }

                $threads[] = [
                    'id'            => (int)    $thread->thread_id,
                    'last_message'  => (int)    $thread->message_id,
                    'unread'        => (int)    $thread->unread_count,
                    'subject'       => (string) BP_Better_Messages()->functions->remove_re( $thread->subject ),
                    'last_time'     => (string) $thread->date_sent,
                    'participants'  => array_map( 'intval', array_keys($recipients) ),
                    'type'          => $thread_type,
                    'title'         => $title,
                    'image'         => $image,
                    'permissions'   => [
                        'is_muted'       => $is_muted,
                        'delete_allowed' => $delete_allowed
                    ],
                    'last_pull'     => (int) time()
                ];
            }

            $user_ids = array_unique( $user_ids );
            $users = [];

            foreach( $user_ids as $user_id ){
                $user = get_userdata( $user_id );

                if( ! $user ) continue;

                $users[] = [
                    'id'        => (int) $user_id,
                    'name'      => BP_Better_Messages()->functions->get_name( $user_id ),
                    'avatar'    => BP_Better_Messages()->functions->get_avatar( $user_id, 50, ['html' => false] ),
                    'url'       => bp_core_get_userlink( $user_id, false, true ),
                    'last_pull' => (int) time()
                ];
            }

            wp_send_json([
                'threads' => $threads,
                'users'   => $users
            ]);

        }

        public function get_messages(){
            if( ! wp_verify_nonce( $_POST[ 'nonce' ], 'bpbm_edit_nonce' ) ){
                exit;
            }

            $current_user_id   = get_current_user_id();
            $thread_id = intval($_POST['thread_id']);

            if ( ! BP_Better_Messages()->functions->check_access( $thread_id ) && ! current_user_can('manage_options') ) die();

            $recipients = BP_Better_Messages()->functions->get_recipients( $thread_id );

            $return = [
                'thread' => [
                    'id'         => (int) $thread_id,
                    'subject'    => "Subject",
                    'image'      => '',
                    'url'        => '',
                    'recipients' => array_keys( $recipients )
                ],
                'users' => []
            ];

            $messages = BP_Better_Messages()->functions->get_messages( $thread_id );

            $user_ids = [];

            foreach( $messages as $key => $message ){
                $user_id = (int) $message->sender_id;

                $user_ids[] = $user_id;

                $messages[ $key ]->id         = (int) $message->id;
                $messages[ $key ]->sender_id  = (int) $message->sender_id;
                $messages[ $key ]->thread_id  = (int) $message->thread_id;
                $messages[ $key ]->message    = BP_Better_Messages()->functions->format_message( $message->message, $message->id, 'stack', $current_user_id );
                $messages[ $key ]->favorited  = BP_Better_Messages()->functions->is_message_starred( $message->id, $current_user_id );
            }

            $user_ids = array_unique( $user_ids );

            foreach ( $user_ids as $user_id ){
                $return['users'][] = [
                    'id'     => $user_id,
                    'name'   => BP_Better_Messages()->functions->get_name( $user_id ),
                    'avatar' => BP_Better_Messages()->functions->get_avatar( $user_id, 50, ['html' => false] ),
                    'url'    => bp_core_get_userlink( $user_id, false, true )
                ];
            }

            $return['messages'] = $messages;

            wp_send_json($return);
        }

        public function get_thread_mentions_suggestions(){
            if( ! wp_verify_nonce( $_POST[ 'nonce' ], 'bpbm_edit_nonce' ) ){
                exit;
            }

            global $wpdb;

            $thread_id      = intval( $_POST['thread_id'] );
            $search_request = sanitize_text_field( $_POST['search'] );
            $search         = '%' . $search_request . '%';

            $query = $wpdb->prepare("
                SELECT `users`.`ID`
                FROM `{$wpdb->users}` as users
                RIGHT JOIN " . bpbm_get_table('recipients') . " recipients 
                ON ( `users`.`ID` = `recipients`.`user_id`
                    AND `recipients`.`thread_id` = %d
                    AND `recipients`.`is_deleted` = 0)
                WHERE `user_login` LIKE %s 
                   OR `user_nicename` LIKE %s 
                   OR `display_name` LIKE %s 
                ORDER BY `display_name` ASC
                LIMIT 0, 50
            ", $thread_id, $search, $search, $search);

            $user_ids = $wpdb->get_col( $query );

            $response = [];

            foreach( $user_ids as $user_id ){
                $user = get_userdata( $user_id );

                $response[ $user_id ] = [
                    'user_id' => $user_id,
                    'name'    => (!empty($user->display_name)) ? $user->display_name : $user->user_login,
                    'avatar'  => BP_Better_Messages_Functions()->get_avatar($user->ID, 40)
                ];
            }

            wp_send_json( ['search' => $search_request, 'users' => $response] );
        }

        public function load_friends_list(){
            if( ! wp_verify_nonce( $_POST[ 'nonce' ], 'bpbm_edit_nonce' ) ){
                exit;
            }

            if( ! function_exists('friends_get_friend_user_ids') ) {
                exit;
            }

            $is_mini = isset($_REQUEST['mini']);
            $friends = BP_Better_Messages()->functions->get_friends_sorted( get_current_user_id() );

            $return = '';
            if( count( $friends ) > 0 ) {
                if( ! $is_mini ) $return .= '<div class="bp-messages-user-list">';
                foreach ($friends as $user_id => $last_activity) {
                    $user = get_userdata($user_id);
                    if (!$user) continue;
                    $return .= BP_Better_Messages()->functions->render_user($user);
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

        public function load_groups_list(){
            if( ! wp_verify_nonce( $_POST[ 'nonce' ], 'bpbm_edit_nonce' ) ){
                exit;
            }

            if( ! function_exists('groups_get_user_groups') ) {
                exit;
            }

            $is_mini = isset($_REQUEST['mini']);
            $groups = groups_get_user_groups(get_current_user_id());


            $return = '';
            if( ! $is_mini ) $return .= '<div class="bp-messages-group-list">';

            if( count( $groups['groups'] ) > 0 ) {
                foreach ($groups['groups'] as $group_id) {
                    $group = new BP_Groups_Group((int)$group_id);
                    if ($group->id === 0) continue;

                    $return .= BP_Better_Messages()->functions->render_group($group);
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

        public function load_thread_participants(){
            global $wpdb;

            $thread_id    = intval($_POST['thread_id']);
            $type         = BP_Better_Messages()->functions->get_thread_type( $thread_id );

            if( $type === 'group' ) {
                if( class_exists('BP_Groups_Group') ) {
                    $group_id = BP_Better_Messages()->functions->get_thread_meta($thread_id, 'group_id');
                    $has_access = current_user_can('manage_options') || BP_Groups_Member::check_is_member(get_current_user_id(), $group_id);

                } else if( class_exists('PeepSoGroup') ){
                    $group_id = BP_Better_Messages()->functions->get_thread_meta($thread_id, 'peepso_group_id');
                    $has_access = BP_Better_Messages_Peepso_Groups::instance()->user_has_access( $group_id, get_current_user_id() );
                } else if( class_exists('UM_Groups') ){
                    $group_id = BP_Better_Messages()->functions->get_thread_meta($thread_id, 'um_group_id');
                    $has_access = Better_Messages_Ultimate_Member_Groups::instance()->user_has_access( $group_id, get_current_user_id() );
                } else {
                    $has_access = false;
                }
            } else {
                $has_access = current_user_can('manage_options') || BP_Better_Messages()->functions->check_access($thread_id);
            }

            if( ! $has_access ) exit;

            $page     = 1;

            if( isset( $_POST['page']) ){
                $page = intval( $_POST['page'] );
                if( $page < 1 ) $page = 1;
            }

            $per_page = 100;
            $offset   = 0;

            if( $page > 1 ){
                $offset = ( $per_page * $page ) - $per_page;
            }

            $participants_count = (int) $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT COUNT(*) FROM (SELECT COUNT(*)
                    FROM " . bpbm_get_table('recipients') . " recipients
                    RIGHT JOIN {$wpdb->users} users
                    ON `users`.`ID` = `recipients`.`user_id`
                    WHERE `recipients`.`thread_id` = %d
                    GROUP BY `recipients`.`user_id`) count",
                    $thread_id
                )
            );

            $pages = ceil( $participants_count / $per_page );

            $participants = $wpdb->get_col(
                $wpdb->prepare(
                    "SELECT 
                    `recipients`.`user_id` 
                    FROM " . bpbm_get_table('recipients') . " recipients
                    RIGHT JOIN {$wpdb->users} users
                    ON `users`.`ID` = `recipients`.`user_id`
                    WHERE `recipients`.`thread_id` = %d
                    GROUP BY `recipients`.`user_id`
                    ORDER BY `recipients`.`id`
                    LIMIT %d, %d",
                    $thread_id, $offset, $per_page
                )
            );

            $can_moderate = BP_Better_Messages()->functions->is_thread_super_moderator( get_current_user_id(), $thread_id );

            ob_start();
            foreach($participants as $user_id){
                $user = get_userdata($user_id);
                if( ! is_object( $user ) ) continue;
                ?>
                <div class="user" data-id="<?php esc_attr_e($user_id); ?>" data-thread-id="<?php esc_attr_e($thread_id); ?>" data-username="<?php esc_attr_e($user->user_nicename); ?>">
                    <div class="pic">
                        <?php echo BP_Better_Messages_Functions()->get_avatar( $user_id, 30 ); ?>
                    </div>
                    <div class="name"><?php echo BP_Better_Messages()->functions->get_user_link( $user_id, 0 ); ?></div>
                    <div class="actions">
                        <?php if( $type !== 'group' ) {
                            if($user_id !== get_current_user_id() && $can_moderate){ ?>
                            <a href="#" class="remove-from-thread" title="<?php _e('Exclude user from thread', 'bp-better-messages'); ?>"><i class="fas fa-ban"></i></a>
                            <?php }
                        } ?>
                    </div>
                    <div class="loading"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div>
                </div>
            <?php }

            $html = ob_get_clean();

            $args = [
                'html'     => $html,
                'loadMore' => false,
                'count'    => $participants_count
            ];

            if( $pages > 1 && $pages > $page ) {
                $args['loadMore'] = '<div class="bm-load-more-participants-div"><span class="bm-load-more-participants" data-thread-id="' . $thread_id . '" data-page="' . ($page + 1) . '">' . _x('Load more participants', 'Load more participants button', 'bp-better-messages') . '</span></div>';
            }

            wp_send_json($args);
        }

        public function remove_current_user($user_query, $_this){
            $user_query['exclude'] = get_current_user_id();
            return $user_query;
        }

        public function thread_search(){
            global $wpdb;
            $search = sanitize_text_field($_POST['search']);

            if( empty( $search ) ) {
                exit;
            }

            $user_id = get_current_user_id();

            ob_start();

            $has_results = false;

            $users_listed = [];
            $members = $friends = [];


            if( BP_Better_Messages()->settings['disableUsersSearch'] !== '1' ) {
                add_filter('bp_members_suggestions_query_args', array($this, 'remove_current_user'), 10, 2);
                if( function_exists('friends_get_friend_user_ids') ) {
                    $friends = bp_core_get_suggestions(array(
                        'limit' => 10,
                        'only_friends' => true,
                        'term' => $search,
                        'type' => 'members',
                        'exclude' => [$user_id]
                    ));
                }

                if (BP_Better_Messages()->settings['searchAllUsers'] === '1') {
                    $members = bp_core_get_suggestions(array(
                        'limit' => 10 + count($friends),
                        'only_friends' => false,
                        'term' => $search,
                        'type' => 'members',
                        'exclude' => [$user_id]
                    ));
                }
                remove_filter('bp_members_suggestions_query_args', array($this, 'remove_current_user'), 10);
            }

            if( count( $friends ) > 0 ) {
                $has_results = true;
            ?>
            <div class="bpbm-search-results-section">
                <p class="bpbm-search-results-header"><?php _e('Friends', 'bp-better-messages') ?></p>
                <div class="bp-messages-user-list">
                    <?php foreach($friends as $_user){
                        $user_id = $_user->user_id;
                        $users_listed[] = $user_id;
                        $user = get_userdata($_user->user_id);
                        if( ! $user ) continue; ?>
                        <div class="user" data-id="<?php esc_attr_e($user_id); ?>" data-username="<?php esc_attr_e($user->user_nicename); ?>">
                            <div class="pic">
                                <?php echo BP_Better_Messages_Functions()->get_avatar( $user_id, 30 ); ?>
                            </div>
                            <div class="name"><?php echo BP_Better_Messages_Functions()->get_name( $user_id ); ?></div>
                            <div class="actions">
                                <a title="<?php _e('User profile', 'bp-better-messages'); ?>" href="<?php echo bp_core_get_userlink( $user_id, false, true ); ?>" class="open-profile"><i class="fas fa-user"></i></a>
                            </div>
                            <div class="loading">
                                <div class="bounce1"></div>
                                <div class="bounce2"></div>
                                <div class="bounce3"></div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <?php } ?>

            <?php
            $current_user_id = get_current_user_id();

            $searchTerm = '%' . sanitize_text_field($search) . '%';

            $query = $wpdb->prepare( "
                SELECT " . bpbm_get_table('messages') . ".thread_id,
                COUNT(" . bpbm_get_table('messages') . ".thread_id) as count,
                " . bpbm_get_table('messages') . ".message as message,
                " . bpbm_get_table('messages') . ".id as message_id,
                " . bpbm_get_table('messages') . ".subject as subject
                FROM " . bpbm_get_table('messages') . "
                INNER JOIN " . bpbm_get_table('recipients') . "
                ON " . bpbm_get_table('recipients') . ".thread_id = " . bpbm_get_table('messages') . ".thread_id
                WHERE
                " . bpbm_get_table('recipients') . ".is_deleted = 0 
                AND " . bpbm_get_table('recipients') . ".user_id = %d
                AND (" . bpbm_get_table('messages') . ".message LIKE %s OR " . bpbm_get_table('messages') . ".subject LIKE %s)
                GROUP BY " . bpbm_get_table('messages') . ".thread_id
                LIMIT 0, 10
            ", $current_user_id, $searchTerm, $searchTerm );

            $threads = $wpdb->get_results( $query );

            if( count( $threads ) > 0 ){ $has_results = true; ?>
            <div class="bpbm-search-results-section">
                <p class="bpbm-search-results-header"><?php _e('Messages', 'bp-better-messages') ?></p>
                <div class="threads-list">
                    <?php foreach ( $threads as $thread ) {
                        $thread->subject = BP_Better_Messages()->functions->remove_re($thread->subject);
                        $thread->subject = BP_Better_Messages()->functions->highlightKeywords( wp_unslash($thread->subject), $search);
                        $thread->message = BP_Better_Messages()->functions->highlightKeywords( wp_unslash($thread->message), $search);

                        $thread->message = BP_Better_Messages()->functions->format_message( $thread->message, $thread->message_id, 'site', $current_user_id );

                        $classes = [];
                        $show_avatars = BP_Better_Messages()->functions->show_avatars();

                        if( ! $show_avatars ){
                            $classes[] = 'no-avatars';
                        }

                        $recipients = array();
                        $results = $wpdb->get_results( $wpdb->prepare( "SELECT user_id FROM " . bpbm_get_table('recipients') . " WHERE thread_id = %d", $thread->thread_id ) );

                        foreach ( (array) $results as $recipient ) {
                            if ( $current_user_id === intval($recipient->user_id) ) continue;
                            $userdata = get_userdata($recipient->user_id);

                            if( !! $userdata ) {
                                $recipients[] = intval($recipient->user_id);
                            }
                        }

                        $thread->recipients = $recipients;

                        $recipients_count = count($recipients);

                        if( function_exists('groups_get_user_groups') ) {
                            $group_id = BP_Better_Messages()->functions->get_thread_meta($thread->thread_id, 'group_id');
                        } else {
                            $group_id = false;
                        }
                        ?><div class="thread <?php echo implode(' ', $classes); ?>" data-id="<?php echo $thread->thread_id; ?>" data-href="<?php echo add_query_arg( 'thread_id', $thread->thread_id, BP_Better_Messages()->functions->get_link( $user_id ) ); ?>">
                            <div class="pic <?php if ( ! $group_id && $recipients_count > 1 ) echo 'group'; ?>">
                                <?php
                                if( !! $group_id ){
                                    $avatar = bp_core_fetch_avatar( array(
                                        'item_id'    => $group_id,
                                        'avatar_dir' => 'group-avatars',
                                        'object'     => 'group',
                                        'type'       => 'thumb',
                                    ));

                                    echo $avatar;
                                } else if ( $recipients_count > 1 ) {
                                    $i = 0;
                                    foreach ( $thread->recipients as $recipient ) {
                                        $i++;
                                        $userdata = get_userdata($recipient);

                                        if ($userdata) {
                                            $link = bp_core_get_userlink($recipient, false, true);
                                            $avatar = BP_Better_Messages_Functions()->get_avatar($recipient, 25);
                                            echo '<a href="' . $link . '">' . $avatar . '</a>';
                                        } else {
                                            $avatar = BP_Better_Messages_Functions()->get_avatar(0, 25);
                                            echo $avatar;
                                        }

                                        if ( $i == 4 ) break;
                                    }
                                    if ( $i < 4 ) echo BP_Better_Messages_Functions()->get_avatar( $user_id, 25 );
                                } else {
                                    if( $recipients_count > 0 ) {
                                        $user_id = array_values($thread->recipients)[0];
                                        $userdata = get_userdata($user_id);

                                        if ($userdata) {
                                            $link = bp_core_get_userlink($user_id, false, true);
                                            $avatar = BP_Better_Messages_Functions()->get_avatar($user_id, 50);
                                            echo '<a href="' . $link . '">' . $avatar . '</a>';
                                        } else {
                                            $avatar = BP_Better_Messages_Functions()->get_avatar(0, 50);
                                            echo $avatar;
                                        }
                                    } else {
                                        echo BP_Better_Messages_Functions()->get_avatar(0, 50);
                                    }
                                } ?>
                            </div>
                            <div class="info">
                                <?php
                                if ( ! $group_id && $recipients_count <= 1 ) {
                                    $user_id  = array_values( $thread->recipients )[ 0 ];
                                    $userdata = get_userdata( $user_id );

                                    if( $recipients_count === 1 && $userdata ){
                                        $name = apply_filters( 'bp_better_messages_thread_displayname', bp_core_get_user_displayname( $user_id ), $user_id, $thread->thread_id );
                                    } else {
                                        $name = __('Deleted User', 'bp-better-messages');
                                    }

                                    echo '<h4 class="name">' . $name . '</h4>';
                                }

                                if( BP_Better_Messages()->settings['disableSubject'] !== '1' ) {
                                    if( ( ! empty( $thread->subject ) ) ) {
                                        echo '<h4>' . wp_unslash($thread->subject) . '</h4>';
                                    } else if ( $recipients_count > 1 ){
                                        echo '<h4>' . $recipients_count . ' ' . __('Participants', 'bp-better-messages') . '</h4>';
                                    }
                                } else {
                                    if( !! $group_id ) {
                                        echo '<h4>' . wp_unslash($thread->subject) . '</h4>';
                                    } else if ( $recipients_count > 1 ){
                                        echo '<h4>' . $recipients_count . ' ' . __('Participants', 'bp-better-messages') . '</h4>';
                                    }
                                }

                                if( intval($thread->count) === 1 ){
                                    echo '<p>' . $thread->message . '</p>';
                                } else {
                                    echo '<p>' . sprintf( __('%s matches', 'bp-better-messages'), $thread->count ) . '</p>';
                                } ?>
                            </div>
                            <div class="time"></div>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <?php }

            if( function_exists('groups_get_groups') && BP_Better_Messages()->settings['enableGroups'] === '1' ) {
                $groups = groups_get_groups([
                    'search_terms' => $search
                ]);

                if( $groups['total'] > 0 ){
                    echo '<div class="bpbm-search-results-section">';
                    echo '<p class="bpbm-search-results-header">' . __('Groups', 'bp-better-messages') . '</p>';
                    echo '<div class="bp-messages-group-list">';
                    foreach( $groups['groups'] as $group ){
                        $messages_enabled = 'bpbm-messages-' . BP_Better_Messages()->groups->is_group_messages_enabled( $group_id );
                        $thread_id = BP_Better_Messages()->groups->get_group_thread_id( $group->id ); ?>
                        <div class="group <?php echo $messages_enabled; ?>" data-group-id="<?php esc_attr_e($group->id); ?>" data-thread-id="<?php esc_attr_e($thread_id); ?>">
                            <?php
                            $avatar = bp_core_fetch_avatar( array(
                                'item_id'    => $group->id,
                                'avatar_dir' => 'group-avatars',
                                'object'     => 'group',
                                'type'       => 'thumb',
                            ));
                            if( !! $avatar ){ ?>
                                <div class="pic">
                                    <?php echo $avatar; ?>
                                </div>
                            <?php } ?>
                            <div class="name"><?php esc_attr_e($group->name); ?></div>
                            <div class="actions">
                                <a title="<?php _e('Group homepage', 'bp-better-messages'); ?>" href="<?php echo bp_get_group_permalink( $group ); ?>" class="open-group"><i class="fas fa-home"></i></a>
                            </div>
                            <div class="loading">
                                <div class="bounce1"></div>
                                <div class="bounce2"></div>
                                <div class="bounce3"></div>
                            </div>
                        </div>
                    <?php
                    }
                    echo '</div>';
                    echo '</div>';
                }
            }

            if( count( $members ) > 0 ) {
                foreach( $members as $i=>$member){
                    $user_id = $member->user_id;

                    if( in_array( $user_id, $users_listed ) ) {
                        unset( $members[ $i ] );
                    }
                }
            }

            if( count( $members ) > 0 ) {
                $has_results = true;
            ?>
            <div class="bpbm-search-results-section">
                <p class="bpbm-search-results-header"><?php _e('Members', 'bp-better-messages') ?></p>
                <div class="bp-messages-user-list">
                    <?php foreach($members as $_user){
                        $user_id = $_user->user_id;
                        if( in_array( $user_id, $users_listed ) ) continue;
                        $users_listed[] = $user_id;
                        $user = get_userdata($_user->user_id);
                        if( ! $user ) continue; ?>
                        <div class="user" data-id="<?php esc_attr_e($user_id); ?>" data-username="<?php esc_attr_e($user->user_nicename); ?>">
                            <div class="pic">
                                <?php echo BP_Better_Messages_Functions()->get_avatar( $user_id, 30 ); ?>
                            </div>
                            <div class="name"><?php echo BP_Better_Messages_Functions()->get_name( $user_id ); ?></div>
                            <div class="actions">
                                <a title="<?php _e('User profile', 'bp-better-messages'); ?>" href="<?php echo bp_core_get_userlink( $user_id, false, true ); ?>" class="open-profile"><i class="fas fa-user"></i></a>
                            </div>
                            <div class="loading">
                                <div class="bounce1"></div>
                                <div class="bounce2"></div>
                                <div class="bounce3"></div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <?php } ?>
            <?php

            if( ! $has_results ){ ?>
                <div class="empty bpbm-search-empty">
                    <p class="bpbm-empty-icon"><i class="fas fa-search"></i></p>
                    <p class="bpbm-empty-message"><?php _e( 'Nothing found', 'bp-better-messages' ); ?></p>
                </div>
            <?php }

            echo BP_Better_Messages()->functions->minify_html(ob_get_clean());

            exit;
        }

        public function leave_thread(){
            global $wpdb;

            if( ! wp_verify_nonce( $_POST[ 'nonce' ], 'bpbm_edit_nonce' ) ){
                exit;
            }

            $thread_id = intval($_POST['thread_id']);
            $user_id   = get_current_user_id();

            BP_Better_Messages()->hooks->clean_thread_cache( $thread_id );

            $deleted = $wpdb->delete(bpbm_get_table('recipients'), [
                'thread_id' => $thread_id,
                'user_id'   => $user_id
            ], ['%d', '%d']);

            wp_send_json( $deleted );
        }

        public function export_admin_options(){

            $nonce    = $_POST['nonce'];
            if ( ! wp_verify_nonce($nonce, 'bpbm-import-options') ){
                exit;
            }

            if( ! current_user_can('manage_options') ){
                exit;
            }

            $options = get_option( 'bp-better-chat-settings', array() );
            wp_send_json(base64_encode(json_encode($options)));
        }

        public function import_admin_options(){

            $nonce    = $_POST['nonce'];
            if ( ! wp_verify_nonce($nonce, 'bpbm-import-options') ){
                exit;
            }

            if( ! current_user_can('manage_options') ){
                exit;
            }

            $settings = sanitize_text_field($_POST['settings']);

            $options  = base64_decode( $settings );
            $options  = json_decode( $options, true );

            if( is_null( $options ) ){
                wp_send_json_error('Error to decode data');
            } else {
                update_option( 'bp-better-chat-settings', $options );
                wp_send_json_success('Succesfully imported');
            }
        }

        public function mute_thread(){
            if( ! wp_verify_nonce( $_POST[ 'nonce' ], 'bpbm_edit_nonce' ) ){
                exit;
            }

            $thread_id = intval($_POST['thread_id']);
            $user_id   = get_current_user_id();
            $muted_threads = BP_Better_Messages_Functions()->get_user_muted_threads( $user_id );

            $muted_threads[$thread_id] = time();

            update_user_meta( $user_id, 'bpbm_muted_threads', $muted_threads );
            wp_send_json(true);
        }

        public function unmute_thread(){
            if( ! wp_verify_nonce( $_POST[ 'nonce' ], 'bpbm_edit_nonce' ) ){
                exit;
            }

            $thread_id = intval($_POST['thread_id']);
            $user_id   = get_current_user_id();
            $muted_threads = BP_Better_Messages_Functions()->get_user_muted_threads( $user_id );

            if( isset( $muted_threads[ $thread_id ] ) ){
                unset( $muted_threads[ $thread_id ] );
            }

            update_user_meta( $user_id, 'bpbm_muted_threads', $muted_threads );

            wp_send_json(true);
        }

        public function load_via_ajax(){
            global $bpbm_errors;
            if(
                isset($_GET['new-message'])
                && isset($_GET['fast'])
                && isset($_GET['to'])
                && ! empty($_GET['fast'])
                && ! empty($_GET['to'])
            ){
                $getTo = sanitize_text_field($_GET['to']);
                $to = false;

                if( is_numeric($getTo) ){
                    $to = get_userdata( $getTo );
                }

                if( ! $to ) {
                    $to = get_user_by('slug', $getTo );
                }

                if( !! $to ) {

                    if (BP_Better_Messages()->settings['singleThreadMode'] == '1') {
                        $threads = BP_Better_Messages()->functions->find_existing_threads(get_current_user_id(), $to->ID);
                        if (count($threads) > 0) {
                            $url = admin_url('admin-ajax.php');

                            $url = add_query_arg('thread_id', $threads[0], $url);
                            $url = add_query_arg('action', 'bp_messages_load_via_ajax', $url);

                            wp_redirect($url);
                            exit;
                        }
                    }

                    $thread_id = BP_Better_Messages()->functions->get_pm_thread_id($to->ID);
                    $url = add_query_arg(['action' => 'bp_messages_load_via_ajax', 'thread_id' => $thread_id, 'scrollToContainer' => ''], admin_url('admin-ajax.php'));

                    wp_redirect($url);
                    exit;
                }
            }

            $html = BP_Better_Messages()->functions->get_page();

            $json = [
                'html' => $html
            ];

            if( count( $bpbm_errors ) > 0 ){
                $json['errors'] = $bpbm_errors;
            }

            if( BP_Better_Messages()->settings['mechanism'] !== 'websocket'){
                $json['total_unread'] = BP_Messages_Thread::get_total_threads_for_user( get_current_user_id(), 'inbox', 'unread' );
            }

            wp_send_json( $json );
        }

        public function delete_message()
        {
            $thread_id = intval($_POST['thread_id']);
            $messages_ids = $_POST['messages_ids'];

            $errors = [];
            if ( ! wp_verify_nonce($_POST['_wpnonce'], 'bpbm_edit_nonce' ) ) {
                $errors[] = __('Security error while deleting messages', 'bp-better-messages');
            }

            if( ! empty($errors) ) {
                wp_send_json( array(
                    'result'   => false,
                    'errors'   => $errors,
                    'redirect' => false
                ) );
            }

            global $wpdb;

            $user_id = get_current_user_id();

            foreach( $messages_ids as $message_id ){
                $message = new BP_Messages_Message( $message_id );

                if( $message->sender_id === $user_id || BP_Better_Messages()->functions->is_thread_super_moderator( $user_id, $message->thread_id ) ){
                    BP_Better_Messages()->functions->delete_message( $message_id, $message->thread_id );
                }
            }

            BP_Better_Messages()->hooks->clean_thread_cache( $message->thread_id );

            if( ! empty($errors) ) {
                wp_send_json( array(
                    'result'   => false,
                    'errors'   => $errors,
                    'redirect' => false
                ) );
            } else {
                wp_send_json( array(
                    'result'   => true,
                    'message'  => __('Deleted successfully', 'bp-better-messages'),
                    'redirect' => false
                ) );
            }

            exit;
        }

        public function change_thread_option(){
            $thread_id  = intval($_POST['thread_id']);
            $option     = sanitize_text_field( $_POST['option'] );
            $value      = sanitize_text_field( $_POST['value'] );

            $errors = [];
            if ( !wp_verify_nonce( $_POST[ '_wpnonce' ], 'bp_messages_change_thread_option_' . $thread_id ) ) {
                $errors[] = __( 'Security error while changing thread option', 'bp-better-messages' );
            }

            $user_id = get_current_user_id();
            /** User can change option? */
            $can_change = BP_Better_Messages()->functions->is_thread_super_moderator( $user_id, $thread_id );

            if( ! $can_change ) {
                $errors[] = __( 'You can`t change options for this thread', 'bp-better-messages' );
            }

            if( ! empty($errors) ) {
                wp_send_json( array(
                    'result'   => false,
                    'errors'   => $errors,
                    'redirect' => false
                ) );
            }

            $message = __('Saved successfully', 'bp-better-messages');

            $errors  = [];

            switch( $option ){
                case 'allow_invite':
                    $new_value = ( $value === 'false' ) ? 'no' : 'yes';
                    BP_Better_Messages()->functions->update_thread_meta( $thread_id, 'allow_invite', $new_value );
                    break;
                case 'rename_thread':
                    $new_subject = sanitize_text_field($_POST['value']);
                    BP_Better_Messages()->functions->change_thread_subject( $thread_id, $new_subject );

                    $message = __('Thread subject changed', 'bp-better-messages');
                    break;
            }

            if( ! empty($errors) ) {
                wp_send_json( array(
                    'result'   => false,
                    'errors'   => $errors,
                    'redirect' => false
                ) );
            } else {
                wp_send_json( array(
                    'result'   => true,
                    'message'  => $message,
                    'redirect' => false
                ) );
            }
        }

        public function change_user_option(){
            $user_id = intval($_POST['user_id']);
            $option  = sanitize_text_field( $_POST['option'] );
            $value   = sanitize_text_field( $_POST['value'] );

            $errors = [];
            if ( ! wp_verify_nonce( $_POST[ '_wpnonce' ], 'bp_messages_change_user_option_' . $user_id ) ) {
                $errors[] = __( 'Security error while changing user option', 'bp-better-messages' );
            }

            /** User can change option? */
            $can_change = false;
            if( get_current_user_id() === $user_id ){
                $can_change = true;
            } else if( current_user_can('manage_options') ){
                $can_change = true;
            } else {
                $errors[] = __( 'You can`t change options for this user', 'bp-better-messages' );
            }

            if( ! empty($errors) ) {
                wp_send_json( array(
                    'result'   => false,
                    'errors'   => $errors,
                    'redirect' => false
                ) );
            }

            $message = __('Saved successfully', 'bp-better-messages');

            $errors  = [];


            switch( $option ){
                case 'email_notifications':
                    $new_value = ( $value === 'false' ) ? 'no' : 'yes';
                    update_user_meta( $user_id, 'notification_messages_new_message', $new_value );
                    break;
                case 'who_can_start_conversations':
                    update_user_meta( $user_id, 'bpbm_who_can_start_conversations', $value );
                    break;
                case 'sound_notifications':
                    $new_value = ( $value === 'false' ) ? 'no' : 'yes';
                    update_user_meta( $user_id, 'bpbm_disable_sound_notification', $new_value );
                    break;
                case 'online_status':
                    update_user_meta( $user_id, 'bpbm_online_status', $value );
                    if( class_exists('BP_Better_Messages_Premium') ){
                    $status = BP_Better_Messages()->premium->get_user_full_status($user_id);
                    $message = $status['icon'] . $status['name'];
                    }
                    break;
            }

            if( ! empty($errors) ) {
                wp_send_json( array(
                    'result'   => false,
                    'errors'   => $errors,
                    'redirect' => false
                ) );
            } else {
                wp_send_json( array(
                    'result'   => true,
                    'message'  => $message,
                    'redirect' => false
                ) );
            }

            exit;
        }

        public function get_more_threads(){
            $user_id = get_current_user_id();

            if( current_user_can('manage_options') ){
                $user_id = intval( $_POST['user_id'] );
            }

            $loaded_threads = (array) $_POST['loaded_threads'];

            $threads = BP_Better_Messages()->functions->get_threads( $user_id, $loaded_threads );

            foreach ( $threads as $thread ) {
                echo BP_Better_Messages()->functions->render_thread( $thread );
            }

            exit;
        }

        public function add_user_to_thread(){
            global $wpdb;

            if( ! wp_verify_nonce( $_POST[ 'nonce' ], 'bpbm_edit_nonce' ) ){
                exit;
            }

            $errors = array();

            if( ! isset( $_POST['thread_id'] ) || ! isset( $_POST['users'] ) ){
                exit;
            }

            $thread_id = intval($_POST['thread_id']);
            $users = (array) $_POST['users'];

            $userCanAdd   = BP_Better_Messages_Functions()->can_add_users_to_thread(get_current_user_id(), $thread_id);

            if( ! $userCanAdd ) $errors[] = __('You can`t add members to this thread', 'bp-better-messages');

            if( empty($errors) ) {
                foreach ($users as $username) {
                    $user = get_user_by('slug', $username);
                    if (!$user) continue;

                    $userIsParticipant = (bool)$wpdb->get_var($wpdb->prepare("
                    SELECT COUNT(*) FROM `" . bpbm_get_table('recipients') . "` WHERE `user_id` = %d AND `thread_id` = %d AND `sender_only` = '0'
                    ", $user->ID, $thread_id));

                    if($userIsParticipant) continue;

                    $wpdb->insert(
                        bpbm_get_table('recipients'),
                        array(
                            'user_id'       => $user->ID,
                            'thread_id'     => $thread_id,
                            'unread_count'  => 0,
                            'sender_only'   => 0,
                            'is_deleted'    => 0
                        )
                    );

                    BP_Better_Messages()->hooks->clean_thread_cache( $thread_id );
                }
            }

            exit;
        }

        public function exclude_user_from_thread(){
            global $wpdb;

            if( ! wp_verify_nonce( $_POST[ 'nonce' ], 'bpbm_edit_nonce' ) ){
                exit;
            }

            $errors = array();
            $user_id = intval($_POST['user_id']);
            $thread_id = intval($_POST['thread_id']);

            $userCanExclude = BP_Better_Messages_Functions()->is_thread_super_moderator(get_current_user_id(), $thread_id);

            if( ! $userCanExclude ) $errors[] = __('You can`t exclude members from this thread', 'bp-better-messages');

            $userIsParticipant = (bool) $wpdb->get_var($wpdb->prepare("
            SELECT COUNT(*) FROM `" . bpbm_get_table('recipients') . "` WHERE `user_id` = %d AND `thread_id` = %d AND `sender_only` = '0'
            ", $user_id, $thread_id));

            if( ! $userIsParticipant ) $errors[] = __('Not found member in this thread', 'bp-better-messages');

            if( empty($errors) ){
                BP_Better_Messages()->hooks->clean_thread_cache( $thread_id );

                $result = $wpdb->delete(
                    bpbm_get_table('recipients'),
                    array(
                        'user_id' => $user_id,
                        'thread_id' => $thread_id
                    ),
                    array( '%d', '%d' )
                );

                wp_send_json(array(
                    'result'   => true
                ));
            } else {
                wp_send_json( array(
                    'result'   => false,
                    'errors'   => $errors
                ) );
            }

            exit;
        }

        public function prepare_edit_message(){
            global $wpdb;

            $thread_id  = intval($_POST['thread_id']);
            $message_id = intval($_POST['message_id']);
            $user_id    = get_current_user_id();

            $message = $wpdb->get_row($wpdb->prepare(
                "SELECT * 
                FROM `" . bpbm_get_table('messages') . "` 
                WHERE `thread_id` = %d 
                AND `id` = %d 
                AND `sender_id` = %d"
                , $thread_id, $message_id, $user_id));

            if( ! $message ) wp_send_json(false);

            $attachments = bp_messages_get_meta( $message->id, 'attachments', true );

            $json = array(
                'id'      => $message->id,
                'message' => str_replace('  ', ' ', BP_Better_Messages_Emojies()->convert_emojies_to_unicode($message->message))
            );

            wp_send_json($json);
            exit;
        }

        public function get_edit_message(){
            global $wpdb;

            if ( ! wp_verify_nonce($_POST['_wpnonce'], 'bpbm_edit_nonce' ) ) {
                $errors[] = __('Security error while deleting messages', 'bp-better-messages');
            }

            if( ! empty($errors) ) {
                wp_send_json( array(
                    'result'   => false,
                    'errors'   => $errors,
                    'redirect' => false
                ) );
            }

            $user_id    = get_current_user_id();
            $message_id = intval($_POST['message_id']);

            $message_content = $wpdb->get_var( $wpdb->prepare( "SELECT message FROM " . bpbm_get_table('messages') . " WHERE id = %d AND sender_id = %d", $message_id, $user_id ) );

            $attachments = bp_messages_get_meta( $message_id, 'attachments', true );
            if( is_array( $attachments ) && count( $attachments ) > 0 ) {
                foreach ($attachments as $attachment) {
                    $message_content = str_replace($attachment, '', $message_content);
                }
            }

            $is_gif = strpos( $message_content, '<span class="bpbm-gif">', 0 ) === 0;
            $is_sticker = strpos( $message_content, '<span class="bpbm-sticker">', 0 ) === 0;

            if( $is_gif || $is_sticker ){
                $errors[] = __('This message can`t be edited', 'bp-better-messages');

                if( ! empty($errors) ) {
                    wp_send_json(array(
                        'result' => false,
                        'errors' => $errors,
                        'redirect' => false
                    ));
                }
            }

            $replace = [
                '<!-- BPBM REPLY -->'
            ];

            echo str_replace($replace, '', trim(wp_unslash($message_content)));
            exit;
        }

        public function edit_message(){
            if( BP_Better_Messages()->settings['allowEditMessages'] !== '1' )  return false;
            global $wpdb;

            $thread_id  = intval( $_POST[ 'thread_id' ] );
            $message_id = intval( $_POST['message_id'] );
            $user_id    = get_current_user_id();
            $errors     = array();

            $new_message = BP_Better_Messages()->functions->filter_message_content($_POST['message']);

            if( trim($new_message) == '') $errors['empty'] = __( 'Your message was empty.', 'bp-better-messages' );

            $old_message_content = $wpdb->get_var( $wpdb->prepare( "SELECT message FROM " . bpbm_get_table('messages') ." WHERE id = %d AND sender_id = %d", $message_id, $user_id ) );
            $old_message = $old_message_content;

            $attachments = bp_messages_get_meta( $message_id, 'attachments', true );
            if( is_array( $attachments ) && count( $attachments ) > 0 ) {
                foreach ($attachments as $attachment) {
                    $old_message_content = str_replace($attachment, '', $old_message_content);
                }
            }

            $old_message_content = trim($old_message_content);

            if( strpos($old_message_content, '<!-- BPBM REPLY -->', 0) === 0 ){
                $new_message = '<!-- BPBM REPLY -->' . $new_message;
            }

            $update_message      = str_replace( $old_message_content, $new_message, $old_message );

            $message = $wpdb->get_row($wpdb->prepare(
                "SELECT * 
                FROM `" . bpbm_get_table('messages') . "` 
                WHERE `thread_id` = %d 
                AND `id` = %d 
                AND `sender_id` = %d"
                , $thread_id, $message_id, $user_id)
            );

            if( ! $message ) $errors['not_found'] = __('Message not found', 'bp-better-messages');

            $updated = false;
            if( empty($errors) ){
                $updated = $wpdb->update(
                    bpbm_get_table('messages'),
                    array(
                        'message'   => $update_message
                    ),
                    array(
                        'thread_id' => $thread_id,
                        'id'        => $message_id,
                        'sender_id' => $user_id
                    ),
                    array('%s'),
                    array('%d', '%d', '%d')
                );

                $message->message = $new_message;
                $message->recipients = array();
                $participants = BP_Better_Messages()->functions->get_participants($thread_id);
                foreach(array_keys($participants['users']) as $user_id){
                    $message->recipients[$user_id] = $user_id;
                }
            }

            if( ! empty($errors) ) {
                wp_send_json( array(
                    'result'   => false,
                    'errors'   => $errors,
                    'redirect' => false
                ) );
            } else {
                if( class_exists( 'BP_Better_Messages_Premium' ) ) {
                    BP_Better_Messages_Premium()->on_message_sent($message);
                }

                wp_send_json( array(
                    'result'   => $updated,
                    'redirect' => false
                ) );
            }
        }

        public function get_pm_thread(){
            $user_id = intval($_POST['user_id']);

            if( BP_Better_Messages()->settings['singleThreadMode'] == '1' ) {
                $threads = BP_Better_Messages()->functions->find_existing_threads(get_current_user_id(), $user_id);
                if( count($threads) > 0) {
                    $thread_id = $threads[0];
                    wp_send_json($thread_id);
                    exit;
                }
            }

            $thread_id = BP_Better_Messages()->functions->get_pm_thread_id($user_id);

            wp_send_json($thread_id);
        }

        public function thread_load_messages(){
            if( ! isset( $_POST['thread_id'] )) {
                exit;
            }

            $thread_id = intval($_POST['thread_id']);
            $type = 'thread';

            if( isset( $_POST['message_id'] )) {
                $last_message = intval($_POST['message_id']);
            } else {
                $last_message = false;
            }

            $has_access = false;

            if ( BP_Better_Messages()->functions->check_access( $thread_id ) || current_user_can('manage_options')  ) {
                $has_access = true;
            }

            if( ! $has_access ){
                $chat_id = BP_Better_Messages()->functions->get_thread_meta( $thread_id, 'chat_id' );
                if( ! empty( $chat_id ) ) {
                    $type = 'chat-room';
                    $chat_settings = BP_Better_Messages()->chats->get_chat_settings( $chat_id );

                    if( is_user_logged_in() ) {
                        $anyone_can_read = $chat_settings['only_joined_can_read'] !== '1';
                        if( $anyone_can_read ) {
                            $has_access = true;
                        }
                    }

                    if( ! is_user_logged_in() ) {
                        $guests_allowed  = $chat_settings['allow_guests'] === '1';
                        if( $guests_allowed ) {
                            $has_access = true;
                        }
                    }
                }
            }

            if( ! $has_access ){
                exit;
            }

            ob_start();
            BP_Better_Messages()->functions->messages_classes( $thread_id, $type );
            ob_get_clean();

            $stacks = BP_Better_Messages()->functions->get_stacks( $thread_id, $last_message, 'from_message' );

            if( empty($stacks) ) exit;

            foreach ( $stacks as $stack ) {
                echo BP_Better_Messages()->functions->render_stack( $stack );
            }

            exit;
        }

        public function last_activity_refresh()
        {
            $user_id = get_current_user_id();
            bp_update_user_last_activity( $user_id );
            exit;
        }

        public function thread_check_new()
        {
            status_header(200);
            global $wpdb;

            $user_id = get_current_user_id();
            #$bp = buddypress();

            $response = array();

            $last_message = $last_check = date( "Y-m-d H:i:s", 0 );

            if ( isset( $_POST[ 'last_check' ] ) ) {
                $last_check = date( "Y-m-d H:i:s", intval( $_POST[ 'last_check' ] ) );
            }

            if ( isset( $_POST[ 'last_message' ] ) ) {
                $last_message = date("Y-m-d H:i:s", intval($_POST['last_message']));
            }
            $thread_id = intval( $_POST[ 'thread_id' ] );

            if ( ! BP_Better_Messages()->functions->check_access( $thread_id ) && ! current_user_can('manage_options') ) die();

            setcookie( 'bp-messages-last-check', time(), time() + ( 86400 * 31 ), '/' );

            $messages = $wpdb->get_results( $wpdb->prepare( "
            SELECT id, sender_id as user_id, subject, message as content, date_sent as date
            FROM  `" . bpbm_get_table('messages') . "` 
            WHERE `thread_id`  = %d
            AND   `date_sent`  > %s
            AND   `message` != '<!-- BBPM START THREAD -->'
            ORDER BY `date_sent` ASC
            ", $thread_id, $last_message ) );

            foreach ( $messages as $index => $message ) {
                $user = get_userdata( $message->user_id );
                $messages[ $index ]->message   = BP_Better_Messages()->functions->format_message( $message->content, $message->id, 'stack', $user_id );
                $messages[ $index ]->timestamp = strtotime( $message->date );
                $messages[ $index ]->avatar    = BP_Better_Messages_Functions()->get_avatar( $message->user_id, 40 );
                $messages[ $index ]->name      = BP_Better_Messages()->functions->get_name($message->user_id);;
                $messages[ $index ]->link      = bp_core_get_userlink( $message->user_id, false, true );
            }

            $response[ 'messages' ] = $messages;

            $threads = $wpdb->get_results( "
                SELECT recipients.thread_id, recipients.unread_count 
                FROM   " . bpbm_get_table('recipients') . " as recipients
                LEFT JOIN " . bpbm_get_table('threadsmeta') . " threadsmeta ON
                    ( threadsmeta.`bpbm_threads_id` = recipients.`thread_id`
                    AND threadsmeta.meta_key = 'exclude_from_threads_list' )
                WHERE  recipients.`user_id`      = {$user_id}
                AND    recipients.`is_deleted`   = 0
                AND    recipients.`unread_count` > 0
                AND    recipients.`thread_id`    != {$thread_id}
                AND    ( threadsmeta.bpbm_threads_id IS NULL )
            " );

            foreach ( $threads as $index => $thread ) {
                $recipients = array();
                $results = $wpdb->get_results( $wpdb->prepare( "SELECT user_id FROM " . bpbm_get_table('recipients') . " WHERE thread_id = %d", $thread->thread_id ) );

                foreach ( (array)$results as $recipient ) {
                    if ( get_current_user_id() == $recipient->user_id ) continue;
                    $recipients[] = $recipient->user_id;
                }

                $message = $wpdb->get_row( $wpdb->prepare( "
                SELECT id, sender_id as user_id, subject, message as content, date_sent
                FROM  `" . bpbm_get_table('messages') . "` 
                WHERE `thread_id`  = %d
                AND   `sender_id`  != %d
                AND   `date_sent`  >= %s
                ORDER BY `date_sent` DESC 
                LIMIT 0, 1", $thread->thread_id, $user_id, $last_check ) );

                if ( !$message ) {
                    unset( $threads[ $index ] );
                    continue;
                }

                $user = get_userdata( $message->user_id );
                $_user_id = ( is_object( $user ) ) ? $user->ID : 0;

                $threads[ $index ]->subject      = $message->subject;
                $threads[ $index ]->message      = BP_Better_Messages()->functions->format_message( $message->content, $message->id, 'site', $user_id );
                $threads[ $index ]->name         = BP_Better_Messages()->functions->get_name($_user_id);
                $threads[ $index ]->date_sent    = $message->date_sent;
                $threads[ $index ]->avatar       = bp_core_fetch_avatar( 'type=full&html=false&item_id=' . $_user_id );
                $threads[ $index ]->user_id      = intval( $user_id );
                $threads[ $index ]->unread_count = intval( $threads[ $index ]->unread_count );
                $threads[ $index ]->recipients   = $recipients;
                $threads[ $index ]->html         = BP_Better_Messages()->functions->render_thread( $threads[ $index ] );
            }

            usort( $threads, function ( $item1, $item2 ) {
                $item1_date_sent = (isset($item1->message->date_sent)) ? $item1->message->date_sent : 0;
                $item2_date_sent = (isset($item2->message->date_sent)) ? $item2->message->date_sent : 0;

                if ( strtotime( $item1_date_sent ) == strtotime( $item2_date_sent ) ) return 0;

                return ( strtotime( $item1_date_sent ) < strtotime( $item2_date_sent ) ) ? 1 : -1;
            } );

            $response[ 'threads' ] = $threads;

            BP_Better_Messages()->functions->messages_mark_thread_read( $thread_id );

            $response[ 'total_unread' ] = BP_Messages_Thread::get_total_threads_for_user( $user_id, 'inbox', 'unread' );

            wp_send_json( $response );

            exit;
        }

        public function check_new()
        {
            status_header(200);

            global $wpdb;

            $user_id = get_current_user_id();

            $last_check = date( "Y-m-d H:i:s", 0 );

            if ( isset( $_POST[ 'last_check' ] ) ) {
                $last_check = date( "Y-m-d H:i:s", absint( $_POST[ 'last_check' ] ) );
            }

            setcookie( 'bp-messages-last-check', time(), time() + ( 86400 * 31 ), '/' );

            $threads = $wpdb->get_results( $wpdb->prepare( "
                SELECT recipients.thread_id, recipients.unread_count 
                FROM " . bpbm_get_table('recipients') . " as recipients
                LEFT JOIN " . bpbm_get_table('threadsmeta') . " threadsmeta ON
                    ( threadsmeta.`bpbm_threads_id` = recipients.`thread_id`
                    AND threadsmeta.meta_key = 'exclude_from_threads_list' )
                WHERE  recipients.`user_id`      = %d
                AND    recipients.`is_deleted`   = 0
                AND    recipients.`unread_count` > 0
                AND    ( threadsmeta.bpbm_threads_id IS NULL )
            ", $user_id ) );

            foreach ( $threads as $index => $thread ) {
                $recipients = array();
                $results = $wpdb->get_results( $wpdb->prepare( "SELECT user_id FROM " . bpbm_get_table('recipients') . " WHERE thread_id = %d", $thread->thread_id ) );

                foreach ( (array)$results as $recipient ) {
                    if ( get_current_user_id() == $recipient->user_id ) continue;
                    $recipients[] = $recipient->user_id;
                }

                $message = $wpdb->get_row( $wpdb->prepare( "
                SELECT id, sender_id as user_id, subject, message as content, date_sent
                FROM  `" . bpbm_get_table('messages') . "` 
                WHERE `thread_id`  = %d
                AND   `sender_id`  != %d
                AND   `date_sent`  >= %s
                ORDER BY `id` DESC 
                LIMIT 0, 1", $thread->thread_id, $user_id, $last_check ) );

                if ( !$message ) {
                    unset( $threads[ $index ] );
                    continue;
                }

                $user = get_userdata( $message->user_id );

                $threads[ $index ]->subject      = $message->subject;
                $threads[ $index ]->message      = BP_Better_Messages()->functions->format_message( $message->content, $message->id, 'site', $user_id );
                $threads[ $index ]->name         = BP_Better_Messages()->functions->get_name($user->ID);
                $threads[ $index ]->date_sent    = $message->date_sent;
                $threads[ $index ]->avatar       = bp_core_fetch_avatar( 'type=full&html=false&item_id=' . $user->ID );
                $threads[ $index ]->user_id      = intval( $user->ID );
                $threads[ $index ]->unread_count = intval( $threads[ $index ]->unread_count );
                $threads[ $index ]->recipients   = $recipients;
                $threads[ $index ]->html         = BP_Better_Messages()->functions->render_thread( $threads[ $index ] );
            }

            usort( $threads, function ( $item1, $item2 ) {
                $item1_date_sent = (isset($item1->message->date_sent)) ? $item1->message->date_sent : 0;
                $item2_date_sent = (isset($item2->message->date_sent)) ? $item2->message->date_sent : 0;
                if ( $item1_date_sent == strtotime( $item2_date_sent ) ) return 0;

                return ( strtotime( $item1_date_sent ) < strtotime( $item2_date_sent ) ) ? 1 : -1;
            } );

            $response[ 'threads' ] = $threads;

            $response[ 'total_unread' ] = BP_Messages_Thread::get_total_threads_for_user( $user_id, 'inbox', 'unread' );

            wp_send_json( $response );

            exit;
        }

        public function favorite()
        {
            if( BP_Better_Messages()->settings['disableFavoriteMessages'] === '1' ) {
                exit;
            }

            if ( ! wp_verify_nonce( $_POST[ 'nonce' ], 'bpbm_edit_nonce' ) ) {
                exit;
            }

            $message_id = absint( $_POST[ 'message_id' ] );
            $thread_id  = absint( $_POST[ 'thread_id' ] );
            $type       = sanitize_text_field( $_POST[ 'type' ] );

            $result = bp_messages_star_set_action( array(
                'action'     => $type,
                'message_id' => $message_id,
                'thread_id'  => $thread_id,
                'user_id'    => get_current_user_id(),
            ) );

            wp_send_json( $result );

            exit;
        }

        public function send_message()
        {
            $thread_id = intval( $_POST[ 'thread_id' ] );
            $errors    = array();

            if ( !wp_verify_nonce( $_POST[ '_wpnonce' ], 'sendMessage_' . $thread_id ) ) {
                $errors[] = __( 'Security error while sending message', 'bp-better-messages' );
            } else {
                global $bpbm_new_message_meta;
                $bpbm_new_message_meta = [];
                add_action( 'messages_message_sent', array( $this, 'save_message_meta' ), 1 );

                $reply = false;

                if(isset($_POST['reply']) && isset($_POST['message_id']) && ! empty($_POST['message_id'])){
                    $reply = intval( $_POST['message_id'] );
                }

                $edit = false;

                if(isset($_POST['edit']) && isset($_POST['message_id']) && ! empty($_POST['message_id'])){
                    $edit = intval( $_POST['message_id'] );
                }

                $content = BP_Better_Messages()->functions->filter_message_content($_POST['message']);
                if( !! $reply && ! empty( trim( $content ) ) ){
                    $content = "<!-- BPBM REPLY -->" . $content;
                    $bpbm_new_message_meta['reply_to_message_id'] = $reply;
                }


                if( BP_Better_Messages()->settings['allowEditMessages'] === '1' ) {
                    if (!!$edit) {
                        $this->edit_message();
                    }
                }

                $group_id = BP_Better_Messages()->functions->get_thread_meta($thread_id, 'group_id');

                $group_thread = false;

                if( ! empty( $group_id ) ) {
                    $group_thread = true;
                }

                $args = array(
                    'content'      => $content,
                    'thread_id'    => $thread_id,
                    'group_thread' => $group_thread,
                    'error_type'   => 'wp_error'
                );

                $type = BP_Better_Messages()->functions->get_thread_type( $thread_id );

                if( $type === 'group' ){
                    if( class_exists('BP_Groups_Member') ) {
                        $group_id = BP_Better_Messages()->functions->get_thread_meta($thread_id, 'group_id');

                        if (!!$group_id) {
                            if (!apply_filters('bp_better_messages_can_send_message', BP_Groups_Member::check_is_member(get_current_user_id(), $group_id), get_current_user_id(), $thread_id)) {
                                $errors[] = __('You can`t reply to this thread.', 'bp-better-messages');
                            }
                        }
                    }

                    if( class_exists('PeepSoGroupsPlugin') ) {
                        $group_id = BP_Better_Messages()->functions->get_thread_meta($thread_id, 'peepso_group_id');

                        if ( !! $group_id ) {
                            $has_access  = BP_Better_Messages_Peepso_Groups::instance()->user_has_access( $group_id, get_current_user_id() );

                            if (!apply_filters('bp_better_messages_can_send_message', $has_access, get_current_user_id(), $thread_id)) {
                                $errors[] = __('You can`t reply to this thread.', 'bp-better-messages');
                            }
                        }
                    }
                } else {
                    if ( ! apply_filters('bp_better_messages_can_send_message', BP_Better_Messages()->functions->check_access($thread_id), get_current_user_id(), $thread_id)) {
                        $errors[] = __('You can`t reply to this thread.', 'bp-better-messages');
                    }
                }

                if( trim($args['content']) == '') {
                    $errors['empty'] = __( 'Your message was empty.', 'bp-better-messages' );
                } else if ($args['content'] === '0') {
                    $args['content'] .= ' ';
                }

                do_action_ref_array( 'bp_better_messages_before_message_send', array( &$args, &$errors ));

                if( empty( $errors ) ){
                    global $bpbm_last_message_id;
                    add_action( 'messages_message_sent', array( $this, 'catch_last_message_id' ) );
                    $sent = BP_Better_Messages()->functions->new_message( $args );
                    remove_action( 'messages_message_sent', array( $this, 'catch_last_message_id' ) );

                    if ( is_wp_error( $sent ) ) {
                        $errors[] = $sent->get_error_message();
                    } else {
                        BP_Better_Messages()->functions->messages_mark_thread_read( $thread_id );

                    }
                }
            }

            if( ! empty($errors) ) {
                do_action( 'bp_better_messages_on_message_not_sent', $thread_id, $errors );

                $redirect = 'refresh';

                if( count( $errors ) === 1 && ( isset( $errors['empty'] ) || isset( $errors['restrictBadWord'] ) ) ){
                    $redirect = false;
                }

                wp_send_json( array(
                    'result'   => false,
                    'errors'   => $errors,
                    'redirect' => $redirect
                ) );
            } else {
                wp_send_json( array(
                    'result'   => $sent,
                    'redirect' => false
                ) );
            }

            exit;
        }

        public function new_thread()
        {
            global $bpbm_last_message_id;

            $errors = array();

            if ( !wp_verify_nonce( $_POST[ '_wpnonce' ], 'newThread' ) ) {
                $errors[] = __( 'Security error while starting new thread', 'bp-better-messages' );

                wp_send_json( array(
                    'result'   => false,
                    'errors'   => $errors,
                    'redirect' => false
                ) );

            } else {
                $user = wp_get_current_user();

                $content = BP_Better_Messages()->functions->filter_message_content($_POST['message']);

                $args = array(
                    'subject'       => (isset ($_POST[ 'subject' ]) ) ? sanitize_text_field( $_POST[ 'subject' ] ) : '',
                    'content'       => $content,
                    'new_thread'    => true,
                    'error_type'    => 'wp_error',
                    'append_thread' => false
                );

                if ( isset( $_POST[ 'recipients' ] ) && is_array( $_POST[ 'recipients' ] ) && !empty( $_POST[ 'recipients' ] ) ) {
                    foreach ( $_POST[ 'recipients' ] as $one ) {
                        if($user->user_login == $one || $user->ID == $one) continue;
                        $args[ 'recipients' ][] = sanitize_text_field( $one );
                    }
                }

                if ($args['content'] === '0') {
                    $args['content'] .= ' ';
                }

                do_action_ref_array( 'bp_better_messages_before_new_thread', array( &$args, &$errors ));

                if( empty( $errors ) ){
                    add_action( 'messages_message_sent', array( $this, 'catch_last_message_id' ) );
                    $sent = BP_Better_Messages()->functions->new_message( $args );
                    remove_action( 'messages_message_sent', array( $this, 'catch_last_message_id' ) );

                    if ( is_wp_error( $sent ) ) {
                        $errors[] = $sent->get_error_message();
                    }
                }
            }


            if( ! empty( $errors ) ) {
                wp_send_json( array(
                    'result'   => false,
                    'errors'   => $errors,
                    'redirect' => false
                ) );
            } else {
                // $sent - thread_id

                do_action( 'bp_better_messages_new_thread_created', $sent, $bpbm_last_message_id );

                wp_send_json( array(
                    'result'   => $sent,
                    'redirect' => false
                ) );
            }

            exit;
        }

        public function save_message_meta(&$message){
            global $bpbm_new_message_meta;
            $message_id = $message->id;
            if(count( $bpbm_new_message_meta ) > 0){
                foreach ( $bpbm_new_message_meta as $key => $value ) {
                    bp_messages_add_meta( $message_id, $key, $value, true );
                }
            }
        }

        public function catch_last_message_id(  &$message ){
            global $bpbm_last_message_id;
            $bpbm_last_message_id = $message->id;
        }
        /**
         * AJAX handler for autocomplete.
         *
         * Displays friends only, unless BP_MESSAGES_AUTOCOMPLETE_ALL is defined.
         *
         * @since 1.0.0
         */
        public function bp_messages_autocomplete_results()
        {
            /**
             * Filters the max results default value for ajax messages autocomplete results.
             *
             * @since 1.0.0
             *
             * @param int $value Max results for autocomplete. Default 10.
             */
            $limit = isset( $_GET[ 'limit' ] ) ? absint( $_GET[ 'limit' ] ) : (int)apply_filters( 'bp_autocomplete_max_results', 10 );
            $term = isset( $_GET[ 'q' ] ) ? sanitize_text_field( $_GET[ 'q' ] ) : '';

            // Include everyone in the autocomplete, or just friends?
            if ( defined('BP_MESSAGES_AUTOCOMPLETE_ALL') ) {
                $only_friends = ( BP_MESSAGES_AUTOCOMPLETE_ALL === false );
            } else {
                $only_friends = true;
            }

            if( BP_Better_Messages()->settings[ 'friendsMode' ] === '1' ){
                $only_friends = true;
            }

            if( ! bp_is_active('friends') ){
                $only_friends = false;
            }

            add_filter( 'bp_members_suggestions_query_args', array( $this, 'remove_current_user' ), 10, 2 );
            $suggestions = bp_core_get_suggestions( array(
                'limit'        => $limit,
                'only_friends' => $only_friends,
                'term'         => $term,
                'type'         => 'members',
            ) );
            remove_filter( 'bp_members_suggestions_query_args', array( $this, 'remove_current_user' ), 10 );

            if ( $suggestions && !is_wp_error( $suggestions ) ) {
                $response = array();

                foreach ( $suggestions as $index => $suggestion ) {
                    $userdata = get_userdata($suggestion->user_id);
                    $avatar   = BP_Better_Messages_Functions()->get_avatar( $suggestion->user_id, 40 );

                    $item = array(
                        'id'    => $userdata->user_nicename,
                        'label' => BP_Better_Messages_Functions()->get_name( $suggestion->user_id ),
                        'value' => $userdata->user_nicename,
                    );

                    if( ! empty( $avatar ) ) {
                        $item['img'] = $avatar;
                    }

                    $response[] = $item;
                }

                wp_send_json( $response );
            }

            exit;
        }

        public function delete_thread()
        {

            $errors = array();

            $thread_id = intval( $_POST[ 'thread_id' ] );

            if (
                ( BP_Better_Messages()->settings['disableDeleteThreadCheck'] !== '1' && ! wp_verify_nonce( $_POST[ 'nonce' ], 'delete_' . $thread_id ) )
                || ( ! BP_Better_Messages()->functions->check_access( $thread_id )  && ! current_user_can('manage_options') )
            ) {
                $errors[] = __( 'Security error while deleting thread', 'bp-better-messages' );

                status_header( 200 );

                wp_send_json( array(
                    'result'   => false,
                    'errors'   => $errors,
                    'redirect' => false
                ) );

            } else if( ! apply_filters( 'bp_better_messages_can_delete_thread', true, $thread_id, get_current_user_id() ) ) {
                $errors[] = __( 'You can`t delete this thread', 'bp-better-messages' );

                status_header( 200 );

                wp_send_json( array(
                    'result'   => false,
                    'errors'   => $errors,
                    'redirect' => false
                ) );
            } else {
                global $wpdb;

                $delete_allowed = BP_Better_Messages()->settings['restrictThreadsDeleting'] === '0';
                if( current_user_can('manage_options') ) {
                    $delete_allowed = true;
                }

                if( ! $delete_allowed ){
                    exit;
                }

                $thread_id = (int) $thread_id;
                $user_id = bp_loggedin_user_id();


                BP_Better_Messages()->hooks->clean_thread_cache( $thread_id );
                /**
                 * Fires before a message thread is marked as deleted.
                 *
                 * @since 2.2.0
                 * @since 2.7.0 The $user_id parameter was added.
                 *
                 * @param int $thread_id ID of the thread being deleted.
                 * @param int $user_id   ID of the user that the thread is being deleted for.
                 */
                do_action( 'bp_messages_thread_before_mark_delete', $thread_id, $user_id );


                // Mark messages as deleted
                $wpdb->query( $wpdb->prepare( "UPDATE " . bpbm_get_table('recipients') . " SET is_deleted = 1 WHERE thread_id = %d AND user_id = %d", $thread_id, $user_id ) );

                // Get the message ids in order to pass to the action.
                $message_ids = $wpdb->get_col( $wpdb->prepare( "SELECT id FROM " . bpbm_get_table('messages') . " WHERE thread_id = %d", $thread_id ) );

                // Check to see if any more recipients remain for this message.
                $recipients = $wpdb->get_results( $wpdb->prepare( "SELECT id FROM " . bpbm_get_table('recipients') . " WHERE thread_id = %d AND is_deleted = 0", $thread_id ) );

                // No more recipients so delete all messages associated with the thread.
                if ( empty( $recipients ) ) {

                    /**
                     * Fires before an entire message thread is deleted.
                     *
                     * @since 2.2.0
                     *
                     * @param int   $thread_id   ID of the thread being deleted.
                     * @param array $message_ids IDs of messages being deleted.
                     */
                    do_action( 'bp_messages_thread_before_delete', $thread_id, $message_ids );

                    // Delete all the messages.
                    $wpdb->query( $wpdb->prepare( "DELETE FROM " . bpbm_get_table('messages') . " WHERE thread_id = %d", $thread_id ) );

                    // Do something for each message ID.
                    foreach ( $message_ids as $message_id ) {

                        // Delete message meta.
                        bp_messages_delete_meta( $message_id );

                        /**
                         * Fires after a message is deleted. This hook is poorly named.
                         *
                         * @since 1.0.0
                         *
                         * @param int $message_id ID of the message.
                         */
                        do_action( 'messages_thread_deleted_thread', $message_id );
                    }

                    // Delete all the recipients.
                    $wpdb->query( $wpdb->prepare( "DELETE FROM " . bpbm_get_table('recipients') . " WHERE thread_id = %d", $thread_id ) );
                }

                /**
                 * Fires after a message thread is either marked as deleted or deleted.
                 *
                 * @since 2.2.0
                 * @since 2.7.0 The $user_id parameter was added.
                 *
                 * @param int   $thread_id   ID of the thread being deleted.
                 * @param array $message_ids IDs of messages being deleted.
                 * @param int   $user_id     ID of the user the threads were deleted for.
                 */
                do_action( 'bp_messages_thread_after_delete', $thread_id, $message_ids, $user_id );

                wp_send_json( array(
                    'result'   => true,
                    'errors'   => $errors,
                    'redirect' => false
                ) );

            }

            die();
        }

        public function un_delete_thread()
        {
            global $wpdb;

            $errors = array();

            $thread_id = intval( $_POST[ 'thread_id' ] );
            $user_id = bp_loggedin_user_id();

            $has_access = (bool)$wpdb->get_var( $wpdb->prepare( "
                SELECT COUNT(*)
                FROM " . bpbm_get_table('recipients') . "
                WHERE `thread_id`  = %d
                AND   `user_id`    = %d
                AND   `is_deleted` = 1
            ", $thread_id, $user_id ) );


            if ( ( BP_Better_Messages()->settings['disableDeleteThreadCheck'] !== '1' && ! wp_verify_nonce( $_POST[ 'nonce' ], 'un_delete_' . $thread_id ) ) || ! $has_access ) {
                $errors[] = __( 'Security error while recovering thread', 'bp-better-messages' );

                status_header( 200 );

                wp_send_json( array(
                    'result'   => false,
                    'errors'   => $errors,
                    'redirect' => false
                ) );

            } else {

                $delete_allowed = BP_Better_Messages()->settings['restrictThreadsDeleting'] === '0';
                if( current_user_can('manage_options') ) {
                    $delete_allowed = true;
                }

                if( ! $delete_allowed ){
                    exit;
                }


                $restored = $wpdb->update( bpbm_get_table('recipients'), array(
                    'is_deleted' => 0
                ), array(
                    'thread_id' => $thread_id,
                    'user_id'   => $user_id
                ) );

                BP_Better_Messages()->hooks->clean_thread_cache( $thread_id );

                wp_send_json( array(
                    'result'   => $restored,
                    'errors'   => $errors,
                    'redirect' => false
                ) );

            }

            die();
        }

        public function erase_thread(){
            $thread_id = intval($_POST['thread_id']);
            $can_delete = BP_Better_Messages()->functions->can_erase_thread( get_current_user_id(), $thread_id );

            $errors = [];

            if ( ! $can_delete || ! wp_verify_nonce($_POST['_wpnonce'], 'bpbm_edit_nonce' ) ) {
                $errors[] = __('Security error while deleting messages', 'bp-better-messages');
            }

            if( ! empty($errors) ) {
                wp_send_json( array(
                    'result'   => false,
                    'errors'   => $errors,
                    'redirect' => false
                ) );
            }

            BP_Better_Messages()->functions->erase_thread( $thread_id );

            wp_send_json( array(
                'result'   => true,
                'redirect' => false
            ) );
        }

        public function clear_thread(){
            $thread_id = intval($_POST['thread_id']);
            $can_delete = BP_Better_Messages()->functions->can_delete_thread( get_current_user_id(), $thread_id );

            $errors = [];

            if ( ! $can_delete || ! wp_verify_nonce($_POST['_wpnonce'], 'bpbm_edit_nonce' ) ) {
                $errors[] = __('Security error while deleting messages', 'bp-better-messages');
            }

            if( ! empty($errors) ) {
                wp_send_json( array(
                    'result'   => false,
                    'errors'   => $errors,
                    'redirect' => false
                ) );
            }

            BP_Better_Messages()->functions->clear_thread( $thread_id );
            BP_Better_Messages()->hooks->clean_thread_cache( $thread_id );

            wp_send_json( array(
                'result'   => true,
                'redirect' => false
            ) );
        }

    }
endif;

function BP_Better_Messages_Ajax()
{
    return BP_Better_Messages_Ajax::instance();
}
