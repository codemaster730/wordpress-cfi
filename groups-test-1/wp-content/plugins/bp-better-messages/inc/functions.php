<?php
defined( 'ABSPATH' ) || exit;

if ( !class_exists( 'BP_Better_Messages_Functions' ) ):

    class BP_Better_Messages_Functions
    {

        public static function instance()
        {
            static $instance = null;

            if ( null === $instance ) {
                $instance = new BP_Better_Messages_Functions();
            }

            return $instance;
        }

        public function can_delete_thread( $user_id, $thread_id ){
            $can_delete = false;

            if( user_can( $user_id, 'manage_options' ) ){
                $can_delete = true;
            }

            return $can_delete;
        }

        public function erase_thread( $thread_id ){
            global $wpdb;

            $message_ids = $wpdb->get_col($wpdb->prepare("SELECT id FROM " . bpbm_get_table('messages') . " WHERE `thread_id` = %d", $thread_id));

            if( count( $message_ids ) > 0 ){
                foreach ( $message_ids as $message_id ) {
                    BP_Better_Messages()->functions->delete_message($message_id);
                }
            }

            $wpdb->query($wpdb->prepare("DELETE FROM " . bpbm_get_table('threadsmeta') . " WHERE `bpbm_threads_id` = %d", $thread_id));
            $wpdb->query($wpdb->prepare("DELETE FROM " . bpbm_get_table('recipients') . " WHERE `thread_id` = %d", $thread_id));
        }

        public function clear_thread( $thread_id ){
            global $wpdb;

            $subject = BP_Better_Messages()->functions->remove_re( $wpdb->get_var( $wpdb->prepare("SELECT subject FROM " . bpbm_get_table('messages') . " WHERE `thread_id` = %d ORDER BY `id` DESC LIMIT 0, 1", $thread_id ) ) );
            $message_ids = $wpdb->get_col($wpdb->prepare("SELECT id FROM " . bpbm_get_table('messages') . " WHERE `thread_id` = %d", $thread_id));

            if( count( $message_ids ) > 0 ){
                foreach ( $message_ids as $message_id ) {
                    BP_Better_Messages()->functions->delete_message($message_id);
                }
            }

            $wpdb->insert(
                bpbm_get_table('messages'),
                array(
                    'sender_id' => get_current_user_id(),
                    'thread_id' => $thread_id,
                    'subject'   => $subject,
                    'message'   => '<!-- BBPM START THREAD -->',
                    'date_sent' => bp_core_current_time()
                )
            );

        }

        public function delete_message( $message_id, $thread_id = false ){
            global $wpdb;
            $sql = $wpdb->prepare("SELECT {$wpdb->posts}.ID
                    FROM {$wpdb->posts}
                    INNER JOIN {$wpdb->postmeta}
                    ON ( {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id )
                    INNER JOIN {$wpdb->postmeta} AS mt1
                    ON ( {$wpdb->posts}.ID = mt1.post_id )
                    WHERE 1=1
                    AND ( ( {$wpdb->postmeta}.meta_key = 'bp-better-messages-attachment'
                            AND {$wpdb->postmeta}.meta_value = '1' )
                        AND ( mt1.meta_key = 'bp-better-messages-message-id'
                            AND mt1.meta_value = %d ) )
                    AND {$wpdb->posts}.post_type = 'attachment'
                    AND (({$wpdb->posts}.post_status = 'inherit'))
                    GROUP BY {$wpdb->posts}.ID
                    ORDER BY {$wpdb->posts}.post_date DESC", $message_id);

            $attachments = $wpdb->get_col( $sql );

            foreach( $attachments as $attachment_id ){
                wp_delete_attachment( $attachment_id, true );
            }

            $sql = $wpdb->prepare("DELETE FROM " . bpbm_get_table('messages') . " WHERE id = %d", $message_id);
            $wpdb->query( $sql );
            $sql = $wpdb->prepare("DELETE FROM " . bpbm_get_table('meta') . " WHERE message_id = %d", $message_id);
            $wpdb->query( $sql );

            if( $thread_id !== false ) {
                $recipients = BP_Messages_Thread::get_recipients_for_thread($thread_id);
                do_action('bp_better_messages_message_deleted', $message_id, array_keys($recipients));
            }

            return true;
        }

        public function is_thread_super_moderator($user_id, $thread_id){
            if( user_can( $user_id, 'manage_options') ) {
                return true;
            }

            $group_id = BP_Better_Messages()->functions->get_thread_meta( $thread_id, 'group_id');

            if( !! $group_id ) {
                if ( function_exists('bp_get_user_groups') ) {
                    $user_groups = bp_get_user_groups( $user_id, array(
                        'is_admin' => null,
                        'is_mod' => null,
                    ) );

                    if( isset( $user_groups[$group_id] ) ){
                        if( $user_groups[$group_id]->is_admin || $user_groups[$group_id]->is_mod ){
                            return true;
                        }
                    }

                    return false;
                }
            }

            $participants = BP_Better_Messages()->functions->get_participants( $thread_id );

            if( count($participants['links']) > 1 ){
                global $wpdb;

                $admin_user = (int) $wpdb->get_var($wpdb->prepare("
                    SELECT sender_id 
                    FROM `" . bpbm_get_table('messages') . "` 
                    WHERE `thread_id` = %d 
                    AND   `sender_id` != '0'
                    ORDER BY `" . bpbm_get_table('messages') . "`.`date_sent` ASC
                    LIMIT 0,1
                ", $thread_id));

                if( intval($user_id) === $admin_user){
                    return true;
                }

            }

            return false;
        }

        public function can_add_users_to_thread($user_id, $thread_id){
            global $wpdb;
            $canAdd = $this->is_thread_super_moderator( $user_id, $thread_id );

            if( $canAdd ){
                return true;
            }

            $allow_invite = (BP_Better_Messages()->functions->get_thread_meta( $thread_id, 'allow_invite' ) === 'yes');

            if( $allow_invite ){
                $userIsParticipant = (bool)$wpdb->get_var($wpdb->prepare("
                SELECT COUNT(*) FROM `" . bpbm_get_table('recipients') . "` WHERE `user_id` = %d AND `thread_id` = %d AND `sender_only` = '0' AND `is_deleted` = '0'
                ", get_current_user_id(), $thread_id));

                if( $userIsParticipant ){
                    return true;
                }
            }

            return false;
        }

        public function get_thread_subject($thread_id){
            global $wpdb;

            $subject = $wpdb->get_var( $wpdb->prepare( "
                SELECT subject 
                FROM `" . bpbm_get_table('messages') . "` 
                WHERE `thread_id` = %d 
                ORDER BY `date_sent` ASC
                LIMIT 0, 1
            ", $thread_id ) );

            return wp_unslash(esc_attr($subject));
        }

        public function change_thread_subject($thread_id, $new_subject){
            global $wpdb;

            $wpdb->update(
            bpbm_get_table('messages'),
            array( 'subject' => $new_subject ),
            array( 'thread_id' => $thread_id ), array( '%s' ), array( '%d' ) );

            return wp_unslash(esc_attr($new_subject));
        }

        public function get_threads( $user_id = 0, $exclude_threads = [] )
        {
            global $wpdb;

            $exclude_threads_sql = '';

            if( is_array( $exclude_threads )  && count( $exclude_threads ) > 0 ) {
                foreach ($exclude_threads as $key => $value) {
                    $exclude_threads[$key] = intval($value);
                }

                $exclude_threads_sql = 'AND recipients.`thread_id` NOT IN (' . implode(',', $exclude_threads) . ')';
            }

            $sql = $wpdb->prepare( "
                SELECT
                recipients.`thread_id`,
                recipients.`unread_count`,
                MAX(messages.date_sent) as date_sent
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
	                AND ( threadsmeta.bpbm_threads_id IS NULL )
                    {$exclude_threads_sql}
                GROUP BY recipients.thread_id
                ORDER BY date_sent DESC
                LIMIT 0, 20
            ", $user_id );

            $threads = $wpdb->get_results( $sql );

            foreach ( $threads as $index => $thread ) {
                $recipients = array();

                $results = $wpdb->get_results( $wpdb->prepare( "SELECT user_id FROM " . bpbm_get_table('recipients') . " WHERE thread_id = %d", $thread->thread_id ) );

                foreach ( (array) $results as $recipient ) {
                    if ( $user_id == $recipient->user_id ) continue;

                    $userdata = get_userdata($recipient->user_id);

                    if( !! $userdata ) {
                        $recipients[] = intval($recipient->user_id);
                    }
                }

                $threads[ $index ]->recipients = $recipients;

                $last_message = $wpdb->get_row( $wpdb->prepare( "
                    SELECT id, sender_id as user_id, subject, message, date_sent
                    FROM  `" . bpbm_get_table('messages') . "` 
                    WHERE `thread_id` = %d
                    ORDER BY `date_sent` DESC 
                    LIMIT 0, 1
                ", $thread->thread_id ) );

                if( ! $last_message || $last_message->user_id == 0 ){
                    unset($threads[$index]);
                    continue;
                }

                $user = get_userdata( $last_message->user_id );

                $__user_id                       = ( is_object( $user ) ) ? intval( $user->ID ) : 0;
                $__display_name                  = ( is_object( $user ) ) ? $user->display_name : '';
                $threads[ $index ]->subject      = BP_Better_Messages()->functions->remove_re( $last_message->subject );
                $threads[ $index ]->message      = BP_Better_Messages()->functions->format_message( $last_message->message, $last_message->id, 'site', $user_id );
                $threads[ $index ]->name         = $__display_name;
                $threads[ $index ]->date_sent    = $last_message->date_sent;

                $threads[ $index ]->avatar       = bp_core_fetch_avatar( 'type=full&html=false&item_id=' . $__user_id );
                $threads[ $index ]->user_id      = $__user_id;
                $threads[ $index ]->message_id   = intval( $last_message->id );
                $threads[ $index ]->unread_count = intval( $threads[ $index ]->unread_count );
                $threads[ $index ]->recipients   = $recipients;
                $threads[ $index ]->html         = BP_Better_Messages()->functions->render_thread( $threads[ $index ] );
            }

            return $threads;
        }

        public function get_thread_message_count($thread_id){
            global $wpdb;

            return $wpdb->get_var( $wpdb->prepare( "
            SELECT COUNT(*)
            FROM  " . bpbm_get_table('messages') . "
            WHERE `thread_id` = %d
            ", $thread_id ) );
        }

        public function get_stacks( $thread_id, $message = false, $action = 'last_messages' )
        {
            global $wpdb;

            $thread = new BP_Messages_Thread($thread_id);
            if( isset($thread::$noCache) ){
                $thread::$noCache = true;
            }

            if ( $this->get_thread_message_count( $thread_id ) === 0 ) return array();

            $stacks = array();

            $per_page = (int) BP_Better_Messages()->settings['messagesPerPage'];
            $usersIds = array_keys($thread->get_recipients());
            $userLast = array();
            foreach($usersIds as $userId){
                $userLast[$userId] = (int) get_user_meta($userId, 'bpbm-last-seen-thread-' . $thread_id, true);
            }

            switch ($action){
                case 'last_messages':
                    $query = $wpdb->prepare( "
                    SELECT id, thread_id, sender_id, message, date_sent
                    FROM  " . bpbm_get_table('messages') . "
                    WHERE `thread_id` = %d
                    ORDER BY `date_sent` DESC
                    LIMIT 0, %d
                    ", $thread_id, $per_page );
                    break;
                case 'from_message':
                    $query = $wpdb->prepare( "
                    SELECT id, thread_id, sender_id, message, date_sent
                    FROM  " . bpbm_get_table('messages') . "
                    WHERE `thread_id` = %d
                    AND   `id` < %d
                    ORDER BY `date_sent` DESC
                    LIMIT 0, %d
                    ", $thread_id, $message, $per_page );
                    break;
                case 'to_message':
                    $query = $wpdb->prepare( "
                    SELECT id, thread_id, sender_id, message, date_sent
                    FROM  " . bpbm_get_table('messages') . "
                    WHERE `thread_id` = %d
                    AND   `id` >= %d
                    ORDER BY `date_sent` DESC
                    ", $thread_id, $message );
                    break;
            }

            $messages = $wpdb->get_results( $query );
            $messages = array_reverse($messages);

            $current_user_id = get_current_user_id();
            $lastUser = 0;
            $lastTimestamp = 0;
            foreach ( $messages as $index => $message ) {
                $timestamp = strtotime( $message->date_sent );

                if($message->sender_id == get_current_user_id()){
                    $lastSeen = 0;
                    foreach($userLast as $id => $last){
                        if($id == $current_user_id) continue;
                        if($last > $lastSeen) $lastSeen = $last;
                    }
                } else {
                    if( isset($userLast[$current_user_id]) ) {
                        $lastSeen = $userLast[$current_user_id];
                    } else {
                        $lastSeen = 0;
                    }
                }


                if ( $message->sender_id != $lastUser || date('Y-m-d H:i', $timestamp) !== date('Y-m-d H:i', $lastTimestamp) ) {
                    $lastUser = $message->sender_id;
                    $lastTimestamp = $timestamp;
                    $stacks[] = array(
                        'id'        => $message->id,
                        'user_id'   => $message->sender_id,
                        'user'      => get_userdata( $message->sender_id ),
                        'thread_id' => $message->thread_id,
                        'messages'  => array(
                            array(
                                'id'        => $message->id,
                                'message'   => self::format_message( $message->message, $message->id, 'stack', get_current_user_id() ),
                                'date'      => $message->date_sent,
                                'timestamp' => $timestamp,
                                'stared'    => $this->is_message_starred( $message->id, get_current_user_id() ),
                                'seen'      => ($lastSeen >= $timestamp) ? true : false
                            )
                        )
                    );
                } else {
                    end($stacks);         // move the internal pointer to the end of the array
                    $key = key($stacks);
                    $stacks[ $key ][ 'messages' ][] = array(
                        'id'        => $message->id,
                        'message'   => self::format_message( $message->message, $message->id, 'stack', get_current_user_id() ),
                        'date'      => $message->date_sent,
                        'timestamp' => $timestamp,
                        'stared'    => $this->is_message_starred( $message->id, get_current_user_id() ),
                        'seen'      => ($lastSeen >= $timestamp) ? true : false
                    );
                }
            }

            return $stacks;

        }

        public function is_message_starred( $message_id, $user_id ){
            if( BP_Better_Messages()->settings['disableFavoriteMessages'] === '0' ) {
                return bp_messages_is_message_starred($message_id, $user_id);
            } else {
                return false;
            }
        }

        public function get_participants( $thread_id )
        {
            $thread = new BP_Messages_Thread();
            $recipients = $thread->get_recipients( $thread_id );


            $participants = array(
                'recipients' => array(),
                'links' => array(),
                'users' => array(),
                'count' => 0
            );

            foreach ( $recipients as $recipient ) {
                $user = get_userdata( $recipient->user_id );

                if( ! $user ){
                    continue;
                }

                if($recipient->is_deleted !== 1) {
                    $participants['count']++;
                }

                if($user->ID != get_current_user_id()) {
                    $link = $this->get_user_link( $recipient->user_id );

                    $participants[ 'links' ][] = $link;
                    $participants[ 'recipients' ][] = $recipient->user_id;
                }

                $args = array(
                    'name'    => ( ! empty( $user->display_name ) ) ? $user->display_name : $user->user_login,
                    'link'    => bp_core_get_userlink( $recipient->user_id, false, true ),
                    'avatar'  => BP_Better_Messages_Functions()->get_avatar($user->ID, 40)
                );

                $participants[ 'users' ][ $recipient->user_id ] = $args;
            }

            return $participants;

        }

        public function get_user_link( $user_id, $avatar_size = 20 ){
            $user = get_userdata( $user_id );
            $url = bp_core_get_userlink( $user_id, false, true );

            if( $url !== false ){
                $link = '<a href="' . bp_core_get_userlink( $user_id, false, true ) . '" class="user">' . BP_Better_Messages_Functions()->get_avatar( $user_id, $avatar_size ) . $user->display_name . '</a>';
            } else {
                $link = '<span class="user">' . BP_Better_Messages_Functions()->get_avatar( $user_id, $avatar_size ) . $user->display_name . '</span>';
            }

            return $link;
        }

        public function get_displayed_user_id(){
            $current_user_id = get_current_user_id();

            if( doing_action('wp_ajax_buddyboss_theme_get_header_unread_messages') ){
                $user_id = $current_user_id;
            }

            if ( ! isset( $user_id ) || $user_id == false ) {
                $user_id = bp_displayed_user_id();
            }

            if ( ! isset( $user_id ) || $user_id == false ) {
                $user_id = $current_user_id;
            }

            return $user_id;
        }

        public function get_link( $user_id = false )
        {
            $current_user_id = $this->get_displayed_user_id();

            $slug = BP_Better_Messages()->settings['bpProfileSlug'];

            if ( $user_id == false ) {
                $user_id = $current_user_id;
            }

            $url_overwritten = apply_filters( 'bp_better_messages_page', null, $user_id );

            if( $url_overwritten !== null ){
                return $url_overwritten;
            }

            if( class_exists('AsgarosForum') && BP_Better_Messages()->settings['chatPage'] === 'asgaros-forum' ) {
                global $asgarosforum;
                $link = $asgarosforum->get_link('profile', $user_id) . 'messages/';
                return $link;
            }

            if( class_exists('WooCommerce') && BP_Better_Messages()->settings['chatPage'] === 'woocommerce' ) {
                $link = get_permalink( get_option('woocommerce_myaccount_page_id') ) . $slug . '/';
                return $link;
            }

            if( BP_Better_Messages()->settings['chatPage'] !== '0' ){
                return get_permalink(BP_Better_Messages()->settings['chatPage']);
            }

            if( class_exists('BuddyPress') && $user_id !== $current_user_id ){
                return bp_core_get_user_domain( $user_id ) . $slug . '/';
            }

            if(class_exists('BuddyPress')) {
                return bp_core_get_user_domain( $user_id ) . $slug . '/';
            }

            return '';
        }

        public function get_starred_count()
        {
            global $wpdb;
            $user_id = get_current_user_id();

            return $wpdb->get_var( "
                SELECT
                  COUNT(" . bpbm_get_table('messages') . ".id) AS count
                FROM " . bpbm_get_table('meta') . "
                  INNER JOIN " . bpbm_get_table('messages') . "
                    ON " . bpbm_get_table('meta') . ".message_id = " . bpbm_get_table('messages') . ".id
                  INNER JOIN " . bpbm_get_table('recipients') . "
                    ON " . bpbm_get_table('recipients') . ".thread_id = " . bpbm_get_table('messages') . ".thread_id
                WHERE " . bpbm_get_table('meta') . ".meta_key = 'starred_by_user'
                AND " . bpbm_get_table('meta') . ".meta_value = $user_id
                AND " . bpbm_get_table('recipients') . ".is_deleted = 0
                AND " . bpbm_get_table('recipients') . ".user_id = $user_id
            " );
        }

        public function get_starred_stacks()
        {
            global $wpdb;

            $user_id = get_current_user_id();

            $query = $wpdb->prepare( "
                SELECT
                  " . bpbm_get_table('messages') . ".*
                FROM " . bpbm_get_table('meta') . "
                  INNER JOIN " . bpbm_get_table('messages') . "
                    ON " . bpbm_get_table('meta') . ".message_id = " . bpbm_get_table('messages') . ".id
                  INNER JOIN " . bpbm_get_table('recipients') . "
                    ON " . bpbm_get_table('recipients') . ".thread_id = " . bpbm_get_table('messages') . ".thread_id
                WHERE " . bpbm_get_table('meta') . ".meta_key = 'starred_by_user'
                AND " . bpbm_get_table('meta') . ".meta_value = %d
                AND " . bpbm_get_table('recipients') . ".is_deleted = 0
                AND " . bpbm_get_table('recipients') . ".user_id = %d
            ", $user_id, $user_id );

            $messages = $wpdb->get_results( $query );

            $stacks = array();

            $lastUser = 0;
            foreach ( $messages as $index => $message ) {
                if ( $message->sender_id != $lastUser ) {
                    $lastUser = $message->sender_id;

                    $stacks[] = array(
                        'id'        => $message->id,
                        'user_id'   => $message->sender_id,
                        'user'      => get_userdata( $message->sender_id ),
                        'thread_id' => $message->thread_id,
                        'messages'  => array(
                            array(
                                'id'        => $message->id,
                                'message'   => self::format_message( $message->message, $message->id, 'stack', $user_id ),
                                'date'      => $message->date_sent,
                                'timestamp' => strtotime( $message->date_sent ),
                                'stared'    => $this->is_message_starred( $message->id, get_current_user_id() )
                            )
                        )
                    );
                } else {
                    $stacks[ count( $stacks ) - 1 ][ 'messages' ][] = array(
                        'id'        => $message->id,
                        'message'   => self::format_message( $message->message, $message->id, 'stack', $user_id ),
                        'date'      => $message->date_sent,
                        'timestamp' => strtotime( $message->date_sent ),
                        'stared'    => $this->is_message_starred( $message->id, get_current_user_id() )
                    );
                }
            }

            return $stacks;
        }

        public function get_search_stacks( $search = '' )
        {
            global $wpdb;

            if( empty( trim($search) ) ) return array();

            $user_id = get_current_user_id();

            $searchTerm = '%' . sanitize_text_field($search) . '%';

            $query = $wpdb->prepare( "
                SELECT " . bpbm_get_table('messages') . ".*
                FROM " . bpbm_get_table('messages') . "
                INNER JOIN " . bpbm_get_table('recipients') . "
                ON " . bpbm_get_table('recipients') . ".thread_id = " . bpbm_get_table('messages') . ".thread_id
                WHERE
                " . bpbm_get_table('recipients') . ".is_deleted = 0 
                AND " . bpbm_get_table('recipients') . ".user_id = %d
                AND " . bpbm_get_table('messages') . ".message LIKE %s
            ", $user_id, $searchTerm );

            $messages = $wpdb->get_results( $query );

            $stacks = array();

            $lastUser = 0;
            foreach ( $messages as $index => $message ) {
                if ( $message->sender_id != $lastUser ) {
                    $lastUser = $message->sender_id;

                    $stacks[] = array(
                        'id'        => $message->id,
                        'user_id'   => $message->sender_id,
                        'user'      => get_userdata( $message->sender_id ),
                        'thread_id' => $message->thread_id,
                        'messages'  => array(
                            array(
                                'id'        => $message->id,
                                'message'   => self::format_message( $message->message, $message->id, 'stack', $user_id ),
                                'date'      => $message->date_sent,
                                'timestamp' => strtotime( $message->date_sent ),
                                'stared'    => $this->is_message_starred( $message->id, get_current_user_id() )
                            )
                        )
                    );
                } else {
                    $stacks[ count( $stacks ) - 1 ][ 'messages' ][] = array(
                        'id'        => $message->id,
                        'message'   => self::format_message( $message->message, $message->id, 'stack', $user_id ),
                        'date'      => $message->date_sent,
                        'timestamp' => strtotime( $message->date_sent ),
                        'stared'    => $this->is_message_starred( $message->id, get_current_user_id() )
                    );
                }
            }

            return $stacks;
        }

        public function get_formatted_time( $timestamp ){
            $gmt_offset = get_option('gmt_offset') * 3600;
            $time = $timestamp + $gmt_offset;
            $time_format = get_option( 'time_format' );
            if ( gmdate( 'Ymd' ) != gmdate( 'Ymd', $time ) ) {
                $time_format .= ' ' . get_option( 'date_format' );
            }

            $time = wp_strip_all_tags( stripslashes( date_i18n( $time_format, $time ) ) );

            return $time;
        }

        public function render_stack( $stack ){
            if( $stack[ 'user_id' ] == 0 ) return '';

            foreach ($stack['messages'] as $index => $message){
                if( $message['message'] === '<!— BBPM START THREAD —>' ){
                    unset( $stack['messages'][$index] );
                }
            }

            if( count( $stack['messages'] ) === 0 ){
                return '';
            }

            ob_start();
            global $bpbmCurrentClass;

            $status    = (BP_Better_Messages()->realtime && BP_Better_Messages()->settings['messagesStatus']);
            $timestamp = $stack[ 'messages' ][0][ 'timestamp' ];

            $time = $this->get_formatted_time( $timestamp );

            $userdata  = get_userdata($stack[ 'user_id' ]);
            $favorite_enabled = ( BP_Better_Messages()->settings['disableFavoriteMessages'] === '0' );
            $replies_enabled  = ( BP_Better_Messages()->settings['enableReplies'] === '1' );

            /**
             * Disable reply button for starred or search list
             */
            if( isset($_GET['starred'] ) || isset($_GET['search']) ) $replies_enabled = false;

            $is_sender = $stack['user_id'] == get_current_user_id();

            $styling = BP_Better_Messages()->settings['template'];
            ?><div class="messages-stack <?php echo ($is_sender) ? 'outgoing' : 'incoming'; ?>" data-user-id="<?php echo $stack[ 'user_id' ]; ?>">
                <?php if(BP_Better_Messages()->functions->show_avatars()) { ?>
                <div class="pic"><?php echo BP_Better_Messages_Functions()->get_avatar( $stack[ 'user_id' ], 40 ); ?></div>
                <?php } ?>
                <div class="content">
                    <div class="info">
                        <div class="name">
                            <?php if( ! $userdata ) { ?>
                                <a href="#" class="bpbm-deleted-user-link"><?php _e('Deleted User', 'bp-better-messages'); ?></a>
                                <?php
                            } else {
                                echo BP_Better_Messages()->functions->get_user_link( $stack['user_id'], 0 );
                            } ?>
                        </div>
                        <div class="time" title="<?php echo $time; ?>" data-livestamp="<?php echo $timestamp; ?>"></div>
                    </div>
                    <ul class="messages-list">
                        <?php foreach ( $stack[ 'messages' ] as $message ) {
                            $timestamp = $message[ 'timestamp' ];
                            $time = $this->get_formatted_time( $timestamp );

                            $class = array();
                            if($stack['user_id'] == get_current_user_id()) $class[] = 'my';
                            if(isset($message['seen']) && $status && $message['seen']) $class[] = 'seen';
                            ?>
                            <li class="<?php echo implode(' ', $class); ?>" title="<?php echo $time; ?>" data-thread="<?php echo $stack[ 'thread_id' ]; ?>" data-time="<?php echo $message[ 'timestamp' ]; ?>" data-id="<?php echo $message[ 'id' ]; ?>">
                                <?php
                                ob_start();
                                if(BP_Better_Messages()->settings['messagesStatus'] === '1' && $stack[ 'user_id' ] == get_current_user_id()){
                                    echo '<span class="status" title="' . __('Seen', 'bp-better-messages') . '"></span>';
                                }

                                if($favorite_enabled){
                                    $favorite_class = ($message[ 'stared' ] ) ? 'active' : '';
                                    echo '<span class="favorite ' . $favorite_class . '"><i class="fas" aria-hidden="true"></i></span>';
                                }
                                $actions = ob_get_clean();

                                if( strpos( $bpbmCurrentClass, 'template-standard' ) !== false ) echo $actions;

                                echo '<span class="message-content reply-enabled">';
                                if( $replies_enabled && ! $is_sender ){
                                    echo '<span class="bpbm-reply" title="' . __('Reply', 'bp-better-messages') . '"><i class="fas fa-reply"></i></span>';
                                }

                                if( strpos( $bpbmCurrentClass, 'template-modern' ) !== false ) echo $actions;

                                echo $message[ 'message' ];
                                echo '</span>';
                                ?>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
            <?php
            return ob_get_clean();
        }

        public function format_message( $message = '', $message_id = 0, $context = 'stack', $user_id = false )
        {
            global $processedUrls;

            if ( !isset( $processedUrls ) ) $processedUrls = array();

            $message = apply_filters( 'bp_better_messages_pre_format_message', $message, $message_id, $context, $user_id );

            // Removing slashes
            $message = wp_unslash( $message );

            if ( $context == 'site' ) {
                $message = $this->truncate( $message, 100 );
            } else {
                // New line to html <br>
                $message = nl2br( $message );
            }

            #$message = str_replace( ['[', ']'], ['&#91;', '&#93;'], $message );

            $message = apply_filters( 'bp_better_messages_after_format_message', $message, $message_id, $context, $user_id );

            if ( isset( $processedUrls[ $message_id ] ) && !empty( $processedUrls[ $message_id ] ) ) {
                foreach ( $processedUrls[ $message_id ] as $index => $link ) {
                    $message = str_replace( '%%link_' . ( $index + 1 ) . '%%', $link, $message );
                }
            }

            $message = str_replace('--', '—', $message);

            return $this->clean_string( $message );
        }

        public function filter_message_content( $content ){
            $allowed_tags = [
                'p', 'b', 'i', 'u', 'strike', 'sub', 'sup'
            ];

            if (substr($content, 0, strlen('<p>')) == '<p>') {
                $content = substr($content, strlen('<p>'));
            }

            if (substr($content, 0 - strlen('</p>') ) == '</p>') {
                $content = substr($content, 0, 0 - strlen('</p>'));
            }

            $content = str_replace(array(' style=""', ' style=\"\"'), '', $content);
            $content = esc_textarea( str_replace('<br>', "\n", $content) );

            foreach( $allowed_tags as $tag ){
                $content = str_replace("&lt;".$tag."&gt;", "<".$tag.">",    $content);
                $content = str_replace("&lt;/".$tag."&gt;", "</".$tag.">", $content);
            }

            $content = trim(str_replace(array("&nbsp;", '&amp;nbsp;'), " ", $content));

            return $content;
        }

        function truncate($text, $length) {
            $is_sticker = strpos( $text, '<span class="bpbm-sticker">', 0 ) === 0;
            $is_file    = strpos( $text, '<i class="fas fa-file">' ) !== false;

            if( ! $is_sticker && ! $is_file ) {
                $text = strip_tags($text);
            }

            $length = abs((int)$length);
            if(strlen($text) > $length) {
                $text = preg_replace("/^(.{1,$length})(\s.*|$)/s", '\\1...', $text);
            }
            return($text);
        }

        public function get_thread_count( $thread_id, $user_id )
        {
            global $wpdb, $bp;

            return $wpdb->get_var( $wpdb->prepare( "
            SELECT unread_count 
            FROM   " . bpbm_get_table('recipients') . "
            WHERE  `thread_id` = %d
            AND    `user_id`   = %d
            ", $thread_id, $user_id ) );
        }

        public function get_name($user_id){
            $user = get_userdata($user_id);

            if ( is_object( $user ) ) {
                $name = (!empty($user->fullname)) ? $user->fullname : $user->display_name;
            } else {
                $name = '';
            }

            return $name;
        }

        public function get_avatar($user_id, $size, $args = array()){
            if( $size === 0 ) return '';

            if( ! BP_Better_Messages()->functions->show_avatars() ) {
                return '';
            }

            $user = get_userdata($user_id);

            if ( is_object( $user ) ) {
                $fullname = (!empty($user->fullname)) ? $user->fullname : $user->display_name;
            } else {
                $fullname = '';
            }

            $_user_id = ( is_object( $user ) ) ? $user->ID : 0;

            $defaults = array(
                'type'   => 'full',
                'width'  => $size,
                'height' => $size,
                'class'  => 'avatar',
                'html'   => true,
                'id'     => false,
                'alt'    => sprintf( __( 'Profile picture of %s', 'buddypress' ), $fullname )
            );

            $r = wp_parse_args( $args, $defaults );
            $r['class'] .= ' bpbm-avatar-user-id-' . $_user_id;

            extract( $r, EXTR_SKIP );

            $email = ( is_object( $user ) ) ? $user->user_email : '';

            $extra_attr = apply_filters('bp_better_messages_avatar_extra_attr', ' data-size="' . $size . '" data-user-id="' . $_user_id . '"', $_user_id, $size );

            $avatar = apply_filters( 'bp_get_member_avatar',
                bp_core_fetch_avatar(
                    array(
                        'item_id' => $_user_id,
                        'type' => $type,
                        'alt' => $alt,
                        'css_id' => $id,
                        'class' => $class,
                        'width' => $width,
                        'height' => $height,
                        'email' => $email,
                        'html'  => $html,
                        'extra_attr' => $extra_attr
                    )
                ), $r );

            return $avatar;
        }

        public function find_existing_threads($from, $to){
            global $wpdb;

            $query_from = $wpdb->prepare("SELECT
                  recipients.thread_id
                FROM " . bpbm_get_table('recipients') . " as recipients
                LEFT JOIN " . bpbm_get_table('threadsmeta') . " threadsmetagroup ON
                    ( threadsmetagroup.`bpbm_threads_id` = recipients.`thread_id`
                    AND threadsmetagroup.meta_key = 'group_id' )
                LEFT JOIN " . bpbm_get_table('threadsmeta') . " threadsmetachat ON
                ( threadsmetachat.`bpbm_threads_id` = recipients.`thread_id`
                AND threadsmetachat.meta_key = 'chat_id' )
                WHERE recipients.user_id = %d
	            AND ( threadsmetagroup.bpbm_threads_id IS NULL )
	            AND ( threadsmetachat.bpbm_threads_id IS NULL )
                AND recipients.is_deleted = 0", $from);

            $query_to = $wpdb->prepare("SELECT
                  recipients.thread_id
                FROM " . bpbm_get_table('recipients') . " as recipients
                LEFT JOIN " . bpbm_get_table('threadsmeta') . " threadsmetagroup ON
                    ( threadsmetagroup.`bpbm_threads_id` = recipients.`thread_id`
                    AND threadsmetagroup.meta_key = 'group_id' )
                LEFT JOIN " . bpbm_get_table('threadsmeta') . " threadsmetachat ON
                ( threadsmetachat.`bpbm_threads_id` = recipients.`thread_id`
                AND threadsmetachat.meta_key = 'chat_id' )
                WHERE recipients.user_id = %d
	            AND ( threadsmetagroup.bpbm_threads_id IS NULL )
	            AND ( threadsmetachat.bpbm_threads_id IS NULL )
                AND recipients.is_deleted = 0", $to);

            $threads_from = $wpdb->get_col($query_from);
            $threads_to = $wpdb->get_col($query_to);

            $threads_between_users = [];
            foreach ( $threads_from as $thread_id ){
                if( in_array( $thread_id, $threads_to )){
                    $threads_between_users[] = intval($thread_id);
                }
            }

            $thread_ids = [];
            if( count( $threads_between_users ) > 0 ) {
                $threads_in = '("' . implode('","', $threads_between_users) . '")';

                $query = "SELECT thread_id, COUNT(*) as count
                FROM " . bpbm_get_table('recipients') . "
                WHERE " . bpbm_get_table('recipients') . ".thread_id IN {$threads_in}
                GROUP BY thread_id
                HAVING count = 2";

                $threads = $wpdb->get_results($query);

                if( count($threads) > 0 ){
                    foreach ( $threads as $thread ){
                        $thread_ids[] = $thread->thread_id;
                    }
                }

            }

            return $thread_ids;
        }

        public function render_thread( $thread, $user_id = false )
        {
            $current_user_id = get_current_user_id();

            if ( $user_id == false ) {
                $user_id = bp_displayed_user_id();

                if( $current_user_id !== $user_id && ! current_user_can('manage_options') ){
                    $user_id = false;
                }
            }

            if ( $user_id == false ) {
                $user_id = $current_user_id;
            }

            if( function_exists('groups_get_user_groups') ) {
                $group_id = BP_Better_Messages()->functions->get_thread_meta($thread->thread_id, 'group_id');
            } else {
                $group_id = false;
            }

            $chat_id = (int) BP_Better_Messages()->functions->get_thread_meta($thread->thread_id, 'chat_id');

            $admin_mode = false;
            if( get_current_user_id() !== $user_id ) $admin_mode = true;

            $delete_allowed = BP_Better_Messages()->settings['restrictThreadsDeleting'] === '0';
            if( current_user_can('manage_options') ) {
                $delete_allowed = true;
            }

            if( $admin_mode ){
                $delete_allowed = false;
            }

            if( !! $group_id || !! $chat_id ){
                $delete_allowed = false;
            }

            ob_start();

            $classes = [];
            if ( $thread->unread_count > 0 && BP_Better_Messages()->settings['mechanism'] === 'ajax' ) {
                $classes[] = 'unread';
            }

            $show_avatars = BP_Better_Messages()->functions->show_avatars();

            if( ! $show_avatars ){
                $classes[] = 'no-avatars';
            }

            $recipients_count = count( $thread->recipients );

            $muted_threads = $this->get_user_muted_threads( get_current_user_id() );
            $is_muted = false;
            if( isset($muted_threads[ $thread->thread_id ]) ){
                $is_muted = true;
            }

            if( $is_muted ){
                $classes[] = 'muted';
            }
            ?><div class="thread <?php echo implode(' ', $classes); ?>"
                 data-id="<?php echo $thread->thread_id; ?>"
                 data-message="<?php echo (isset($thread->message_id)) ? $thread->message_id : 0; ?>"
                 data-href="<?php echo add_query_arg( 'thread_id', $thread->thread_id, BP_Better_Messages()->functions->get_link( $user_id ) ); ?>">
                <?php if( $show_avatars ) {  ?>
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
                        }
                    } ?>
                </div><?php } ?>
                <div class="info">
                    <?php
                    if ( ! $chat_id && ! $group_id && $recipients_count <= 1 ) {
                        $user_id  = array_values( $thread->recipients )[ 0 ];
                        $userdata = get_userdata( $user_id );

                        if( $userdata ){
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

                    ?>
                    <p><?php
                        if ( ( $thread->user_id !== $user_id ) && ( $recipients_count > 1 ) )
                        echo BP_Better_Messages_Functions()->get_avatar( $thread->user_id, 20 );
                        echo $thread->message;
                        ?>
                    </p>
                </div>
                <div class="time">
                    <?php if( $delete_allowed ){ ?><span class="delete" data-nonce="<?php echo wp_create_nonce( 'delete_' . $thread->thread_id ); ?>" title="<?php _e( 'Delete', 'bp-better-messages' ); ?>"><i class="fas fa-times" aria-hidden="true"></i></span><?php } ?>
                    <span class="time-wrapper" data-livestamp="<?php echo strtotime( $thread->date_sent ); ?>"></span>
                    <div class="bpbm-counter-row">
                        <?php if ( $is_muted ) echo '<span class="bpbm-thread-muted"><i class="fas fa-bell-slash"></i></span>'; ?>
                    <span class="unread-count"><?php if ( $thread->unread_count > 0 && BP_Better_Messages()->settings['mechanism'] === 'ajax' ) echo '+' . $thread->unread_count; ?></span>
                    </div>
                </div>
                <?php if( $delete_allowed ){ ?>
                <div class="deleted">
                    <?php _e( 'Thread was deleted.', 'bp-better-messages' ); ?>
                    <a class="undelete" data-nonce="<?php echo wp_create_nonce( 'un_delete_' . $thread->thread_id ); ?>" href="#"><?php _e( 'Recover?', 'bp-better-messages' ); ?></a>
                </div><?php } ?>
                <div class="loading">
                    <div class="bounce1"></div>
                    <div class="bounce2"></div>
                    <div class="bounce3"></div>
                </div>
            </div>
            <?php
            return $this->clean_string( ob_get_clean() );
        }

        public function get_pm_thread_id( $to, $from = false ){
            global $wpdb;

            if( ! is_user_logged_in() ) return false;

            if($from === false) $from = get_current_user_id();

            $to_user = get_userdata($to);
            $from_user = get_userdata($from);

            $existing_threads = $this->find_existing_threads( $from_user->ID, $to_user->ID );

            if( count( $existing_threads ) > 0 ){
                return $existing_threads[ 0 ];
            }

            $args = array(
                'sender_id'  => $from_user->ID,
                'thread_id'  => false,
                'recipients' => $to_user->ID,
                'subject'    => '',
                'content'    => "<!-- BBPM START THREAD -->",
                'date_sent'  => null
            );

            do_action_ref_array( 'bp_better_messages_before_new_thread', array( &$args, &$errors ));

            if( empty( $errors ) ) {
                if( class_exists('BP_Better_Messages_Premium') ) {
                    remove_action( 'messages_message_sent', array( BP_Better_Messages_Premium(), 'on_message_sent' ) );
                }

                remove_action( 'messages_message_sent', 'messages_notification_new_message', 10 );
                remove_action( 'messages_message_sent', 'bp_messages_message_sent_add_notification', 10 );

                $thread_id = messages_new_message($args);

                if ( is_wp_error( $thread_id ) ) {
                    $errors[] = $thread_id->get_error_message();
                } else {
                    $message_id = intval($wpdb->get_var($wpdb->prepare("SELECT id FROM `" . bpbm_get_table('messages') . "` WHERE `thread_id` = %d ORDER BY `id` ASC;", $thread_id)));

                    $wpdb->update(
                        bpbm_get_table('messages'),
                        [
                            'sender_id' => 0,
                            'subject' => ''
                        ],
                        [
                            'id' => $message_id
                        ]
                    );

                    do_action( 'bp_better_messages_new_thread_created', $thread_id, $message_id );

                    return $thread_id;
                }
            }

            return '0&bbpm-errors=' . urlencode(implode(',', $errors));
        }

        public function get_member_id(){
            if( function_exists('bp_get_member_user_id') ) {
                $loop_user_id = bp_get_member_user_id();
                if (!!$loop_user_id) return $loop_user_id;
            }

            $displayed_user_id = bp_displayed_user_id();

            if( !! $displayed_user_id ) return $displayed_user_id;

            if( is_singular() ){
                $author_id = get_the_author_meta('ID');
                if( !! $author_id ) return $author_id;
            }

            return false;
        }

        public function clean_string( $string )
        {
            $string = str_replace( PHP_EOL, ' ', $string );
            $string = preg_replace( '/[\r\n]+/', "\n", $string );
            $string = preg_replace( '/[ \t]+/', ' ', $string );

            return trim($string);
        }

        public function clean_site_url( $url )
        {

            $url = strtolower( $url );

            $url = str_replace( '://www.', '://', $url );

            $url = str_replace( array( 'http://', 'https://' ), '', $url );

            $port = parse_url( $url, PHP_URL_PORT );

            if ( $port ) {
                // strip port number
                $url = str_replace( ':' . $port, '', $url );
            }

            return sanitize_text_field( $url );
        }

        public function hex2rgba($color, $opacity = false) {

            $default = 'rgb(0,0,0)';

            //Return default if no color provided
            if(empty($color))
                return $default;

            //Sanitize $color if "#" is provided
            if ($color[0] == '#' ) {
                $color = substr( $color, 1 );
            }

            //Check if color has 6 or 3 characters and get values
            if (strlen($color) == 6) {
                $hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
            } elseif ( strlen( $color ) == 3 ) {
                $hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
            } else {
                return $default;
            }

            //Convert hexadec to rgb
            $rgb =  array_map('hexdec', $hex);

            //Check if opacity is set(rgba or rgb)
            if($opacity){
                if(abs($opacity) > 1)
                    $opacity = 1.0;
                $output = 'rgba('.implode(",",$rgb).','.$opacity.')';
            } else {
                $output = 'rgb('.implode(",",$rgb).')';
            }

            //Return rgb(a) color string
            return $output;
        }

        public function get_undeleted_recipients($thread_id){
            $recipients = BP_Messages_Thread::get_recipients_for_thread( $thread_id );

            $undeleted = [];

            if( count($recipients) > 0 ){
                foreach ( $recipients as $recipient ){
                    if( ! $recipient->is_deleted ){
                        $undeleted[$recipient->user_id] = $recipient;
                    }
                }
            }

            return $undeleted;
        }

        public function get_page(){
            if (defined('WP_DEBUG') && true === WP_DEBUG) {
                // some debug to add later
            } else {
                error_reporting(0);
            }

            do_action('bp_better_messages_before_generation');

            $path = apply_filters('bp_better_messages_views_path', BP_Better_Messages()->path . '/views/');

            $thread_id = false;
            $is_mini = (isset($_GET['mini'])) ? true : false;

            $can_start_new_thread = BP_Better_Messages()->settings['disableNewThread'] === '0';
            $can_see_starred      = BP_Better_Messages()->settings['disableFavoriteMessages'] === '0';
            $can_see_search       = BP_Better_Messages()->settings['disableSearch'] === '0';
            $can_see_settings     = BP_Better_Messages()->settings['disableUserSettings'] === '0';

            if( current_user_can('manage_options') ) {
                $can_start_new_thread = true;
                $can_see_starred = true;
                $can_see_search = true;
                $can_see_settings = true;
            }

            global $bpbm_errors;
            $bpbm_errors = [];

            if( isset($_GET['bbpm-errors']) ){
                $bpbm_errors = explode(',', $_GET['bbpm-errors']);
            }

            if ( isset( $_GET[ 'thread_id' ] ) ) {
                $thread_id = absint( $_GET[ 'thread_id' ] );

                if( BP_Better_Messages()->settings['enableGroups'] === '1' ) {
                    $group_id = BP_Better_Messages()->functions->get_thread_meta($thread_id, 'group_id');

                    if ( !! $group_id && bp_is_active('groups') ) {
                        if( BP_Better_Messages()->groups->is_group_messages_enabled( $group_id ) === 'enabled' ) {
                            return $this->get_group_page($group_id);
                        }
                    }
                }

                $chat_id = BP_Better_Messages()->functions->get_thread_meta($thread_id, 'chat_id');

                if( $chat_id ) {
                    return BP_Better_Messages_Chats()->layout( [ 'id' => $chat_id ] );
                }

                if ( count($this->get_undeleted_recipients($thread_id)) === 0 ) {
                    if( $thread_id !== 0 ) {
                        $bpbm_errors[] = __('Thread not found.', 'bp-better-messages');
                    }
                    if( $is_mini ){
                        wp_send_json($bpbm_errors, 403);
                    }
                    $template = 'layout-index.php';
                } else if ( ! BP_Messages_Thread::check_access( $thread_id ) && ! current_user_can('manage_options') ) {
                    $thread_id = false;
                    $bpbm_errors[] = __( 'Access restricted', 'bp-better-messages' );

                    if( $is_mini ){
                        wp_send_json($bpbm_errors, 403);
                    }

                    $template = 'layout-index.php';
                } else {
                    $template =  'layout-thread.php';
                }
            } else if ( isset( $_GET[ 'new-message' ] ) && $can_start_new_thread ) {
                $template =  'layout-new.php';
            } else if ( isset( $_GET[ 'starred' ] ) && $can_see_starred ) {
                $template = 'layout-starred.php';
            } else if ( isset( $_GET[ 'search' ] ) && $can_see_search ) {
                $template = 'layout-search.php';
            } else if ( isset( $_GET[ 'bulk-message' ] ) && current_user_can('manage_options')){
                $template = 'layout-bulk.php';
            } else if (isset( $_GET[ 'settings' ] ) && $can_see_settings ){
                $template = 'layout-user-settings.php';
            } else {
                $template = 'layout-index.php';
            }

            ob_start();

            $template = apply_filters( 'bp_better_messages_current_template', $path . $template, $template );

            if( ! $this->is_ajax() && count( $bpbm_errors ) > 0 ) {
                echo '<p class="bpbm-notice">' . implode('</p><p class="bpbm-notice">', $bpbm_errors) . '</p>';
            }

            if($template !== false) {
                include($template);
            }

            if( isset($thread_id) && is_int($thread_id)  && ! isset($_GET['mini']) ){
                BP_Better_Messages()->functions->messages_mark_thread_read( $thread_id );
                update_user_meta(get_current_user_id(), 'bpbm-last-seen-thread-' . $thread_id, time());
            }

            $content = ob_get_clean();
            $content = str_replace('loading="lazy"', '', $content);

            $content = BP_Better_Messages()->functions->minify_html( $content );
            return $content;
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

            $thread_id = BP_Better_Messages()->groups->get_group_thread_id( $group_id );
            $is_mini = isset($_GET['mini']);

            $template = 'layout-group.php';

            if( ! current_user_can('manage_options') ) {
                if ( ! BP_Groups_Member::check_is_member(get_current_user_id(), $group_id) ) {
                    $thread_id = false;
                    $bpbm_errors[] = __('Access restricted', 'bp-better-messages');

                    if ($is_mini) {
                        wp_send_json($bpbm_errors, 403);
                    }

                    $template = 'layout-index.php';
                }
            }

            ob_start();

            $template = apply_filters( 'bp_better_messages_current_template', $path . $template, $template );


            if( ! $this->is_ajax() && count( $bpbm_errors ) > 0 ) {
                echo '<p class="bpbm-notice">' . implode('</p><p class="bpbm-notice">', $bpbm_errors) . '</p>';
            }

            if($template !== false) {
                include($template);
            }

            if( isset($thread_id) && is_int($thread_id)  && ! isset($_GET['mini']) ){
                BP_Better_Messages()->functions->messages_mark_thread_read( $thread_id );
                update_user_meta(get_current_user_id(), 'bpbm-last-seen-thread-' . $thread_id, time());
            }

            $content = ob_get_clean();
            $content = str_replace('loading="lazy"', '', $content);

            $content = BP_Better_Messages()->functions->minify_html( $content );
            return $content;
        }

        public function get_thread_meta( $thread_id, $key = '' ) {
            return get_metadata( 'bpbm_threads', $thread_id, $key, true );
        }

        function update_thread_meta( $thread_id, $meta_key, $meta_value ) {
            return update_metadata( 'bpbm_threads', $thread_id, $meta_key, $meta_value );
        }

        function delete_thread_meta( $thread_id, $meta_key ) {
            return delete_metadata( 'bpbm_threads', $thread_id, $meta_key);
        }

        public function get_user_muted_threads( $user_id ){
            if( BP_Better_Messages()->settings['allowMuteThreads'] !== '1' ) {
                return [];
            }

            $meta_key  = 'bpbm_muted_threads';
            $muted_threads = get_user_meta( $user_id, $meta_key, true);

            if( ! is_array( $muted_threads ) ) {
                $muted_threads = [];
            }

            return $muted_threads;
        }

        public function get_friends_sorted( $user_id ){
            global $wpdb, $bp;

            $friends = friends_get_friend_user_ids(get_current_user_id());

            if( empty ( $friends ) ) return [];
            $last_active_users = [];

            foreach ( $friends as $friend ){
                $last_active_users[$friend] = 0;
            }

            $query = "SELECT user_id, meta_value FROM {$wpdb->usermeta} WHERE `user_id` IN (" . implode( ',', $friends ) . ") AND `meta_key` = 'bpbm_last_activity' ORDER BY `meta_value` DESC";
            $last_activity = $wpdb->get_results( $query );

            if ( ! empty ( $last_activity ) ) {
                foreach ($last_activity as $item) {
                    $last_active_users[$item->user_id] = strtotime( $item->meta_value );
                }
            }

            arsort($last_active_users);

            return $last_active_users;
        }

        public function check_this_is_multsite() {
            global $wpmu_version;
            if (function_exists('is_multisite')){
                if (is_multisite()) {
                    return true;
                }
                if (!empty($wpmu_version)){
                    return true;
                }
            }
            return false;
        }

        public function is_ajax(){
            if( ! empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' ) {
                return true;
            }

            return false;
        }

        public function minify_js($input) {
            if(trim($input) === "") return $input;
            return preg_replace(
                array(
                    // Remove comment(s)
                    '#\s*("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')\s*|\s*\/\*(?!\!|@cc_on)(?>[\s\S]*?\*\/)\s*|\s*(?<![\:\=])\/\/.*(?=[\n\r]|$)|^\s*|\s*$#',
                    // Remove white-space(s) outside the string and regex
                    '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\'|\/\*(?>.*?\*\/)|\/(?!\/)[^\n\r]*?\/(?=[\s.,;]|[gimuy]|$))|\s*([!%&*\(\)\-=+\[\]\{\}|;:,.<>?\/])\s*#s',
                    // Remove the last semicolon
                    '#;+\}#',
                    // Minify object attribute(s) except JSON attribute(s). From `{'foo':'bar'}` to `{foo:'bar'}`
                    '#([\{,])([\'])(\d+|[a-z_][a-z0-9_]*)\2(?=\:)#i',
                    // --ibid. From `foo['bar']` to `foo.bar`
                    '#([a-z0-9_\)\]])\[([\'"])([a-z_][a-z0-9_]*)\2\]#i'
                ),
                array(
                    '$1',
                    '$1$2',
                    '}',
                    '$1$3',
                    '$1.$3'
                ),
                $input);
        }

        public function minify_css($input) {
            if(trim($input) === "") return $input;
            return preg_replace(
                array(
                    // Remove comment(s)
                    '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')|\/\*(?!\!)(?>.*?\*\/)|^\s*|\s*$#s',
                    // Remove unused white-space(s)
                    '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\'|\/\*(?>.*?\*\/))|\s*+;\s*+(})\s*+|\s*+([*$~^|]?+=|[{};,>~]|\s(?![0-9\.])|!important\b)\s*+|([[(:])\s++|\s++([])])|\s++(:)\s*+(?!(?>[^{}"\']++|"(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')*+{)|^\s++|\s++\z|(\s)\s+#si',
                    // Replace `0(cm|em|ex|in|mm|pc|pt|px|vh|vw|%)` with `0`
                    '#(?<=[\s:])(0)(cm|em|ex|in|mm|pc|pt|px|vh|vw|%)#si',
                    // Replace `:0 0 0 0` with `:0`
                    '#:(0\s+0|0\s+0\s+0\s+0)(?=[;\}]|\!important)#i',
                    // Replace `background-position:0` with `background-position:0 0`
                    '#(background-position):0(?=[;\}])#si',
                    // Replace `0.6` with `.6`, but only when preceded by `:`, `,`, `-` or a white-space
                    '#(?<=[\s:,\-])0+\.(\d+)#s',
                    // Minify string value
                    '#(\/\*(?>.*?\*\/))|(?<!content\:)([\'"])([a-z_][a-z0-9\-_]*?)\2(?=[\s\{\}\];,])#si',
                    '#(\/\*(?>.*?\*\/))|(\burl\()([\'"])([^\s]+?)\3(\))#si',
                    // Minify HEX color code
                    '#(?<=[\s:,\-]\#)([a-f0-6]+)\1([a-f0-6]+)\2([a-f0-6]+)\3#i',
                    // Replace `(border|outline):none` with `(border|outline):0`
                    '#(?<=[\{;])(border|outline):none(?=[;\}\!])#',
                    // Remove empty selector(s)
                    '#(\/\*(?>.*?\*\/))|(^|[\{\}])(?:[^\s\{\}]+)\{\}#s'
                ),
                array(
                    '$1',
                    '$1$2$3$4$5$6$7',
                    '$1',
                    ':0',
                    '$1:0 0',
                    '.$1',
                    '$1$3',
                    '$1$2$4$5',
                    '$1$2$3',
                    '$1:0',
                    '$1$2'
                ),
                $input);
        }

        public function minify_html($input) {
            if(trim($input) === "") return $input;
            // Remove extra white-space(s) between HTML attribute(s)
            $input = preg_replace_callback('#<([^\/\s<>!]+)(?:\s+([^<>]*?)\s*|\s*)(\/?)>#s', function($matches) {
                return '<' . $matches[1] . preg_replace('#([^\s=]+)(\=([\'"]?)(.*?)\3)?(\s+|$)#s', ' $1$2', $matches[2]) . $matches[3] . '>';
            }, str_replace("\r", "", $input));
            // Minify inline CSS declaration(s)
            if(strpos($input, ' style=') !== false) {
                $input = preg_replace_callback('#<([^<]+?)\s+style=([\'"])(.*?)\2(?=[\/\s>])#s', function($matches) {
                    return '<' . $matches[1] . ' style=' . $matches[2] . $this->minify_css($matches[3]) . $matches[2];
                }, $input);
            }
            if(strpos($input, '</style>') !== false) {
                $input = preg_replace_callback('#<style(.*?)>(.*?)</style>#is', function($matches) {
                    return '<style' . $matches[1] .'>'. $this->minify_css($matches[2]) . '</style>';
                }, $input);
            }
            if(strpos($input, '</script>') !== false) {
                $input = preg_replace_callback('#<script(.*?)>(.*?)</script>#is', function($matches) {
                    return '<script' . $matches[1] .'>'. $this->minify_js($matches[2]) . '</script>';
                }, $input);
            }

            return preg_replace(
                array(
                    // t = text
                    // o = tag open
                    // c = tag close
                    // Keep important white-space(s) after self-closing HTML tag(s)
                    '#<(img|input)(>| .*?>)#s',
                    // Remove a line break and two or more white-space(s) between tag(s)
                    '#(<!--.*?-->)|(>)(?:\n*|\s{2,})(<)|^\s*|\s*$#s',
                    '#(<!--.*?-->)|(?<!\>)\s+(<\/.*?>)|(<[^\/]*?>)\s+(?!\<)#s', // t+c || o+t
                    '#(<!--.*?-->)|(<[^\/]*?>)\s+(<[^\/]*?>)|(<\/.*?>)\s+(<\/.*?>)#s', // o+o || c+c
                    '#(<!--.*?-->)|(<\/.*?>)\s+(\s)(?!\<)|(?<!\>)\s+(\s)(<[^\/]*?\/?>)|(<[^\/]*?\/?>)\s+(\s)(?!\<)#s', // c+t || t+o || o+t -- separated by long white-space(s)
                    '#(<!--.*?-->)|(<[^\/]*?>)\s+(<\/.*?>)#s', // empty tag
                    '#<(img|input)(>| .*?>)<\/\1>#s', // reset previous fix
                    '#(&nbsp;)&nbsp;(?![<\s])#', // clean up ...
                    '#(?<=\>)(&nbsp;)(?=\<)#', // --ibid
                    // Remove HTML comment(s) except IE comment(s)
                    '#\s*<!--(?!\[if\s).*?-->\s*|(?<!\>)\n+(?=\<[^!])#s'
                ),
                array(
                    '<$1$2</$1>',
                    '$1$2$3',
                    '$1$2$3',
                    '$1$2$3$4$5',
                    '$1$2$3$4$5$6$7',
                    '$1$2$3',
                    '<$1$2',
                    '$1 ',
                    '$1',
                    ""
                ),
                $input);
        }

        public function license_proposal(){
            if( ! bpbm_fs()->can_use_premium_code() ) {
                echo '<a style="font-size: 10px;" href="' .  admin_url('admin.php?page=bp-better-messages-pricing') . '">' . __('Get WebSocket License', 'bp-better-messages') . '</a>';
            } else {
                if( ! bpbm_fs()->is_premium() ){
                    $url = bpbm_fs()->_get_latest_download_local_url();
                    $string = sprintf(__('<a href="%s" target="_blank">Download</a> and install Premium version of plugin to use this feature', 'bp-better-messages'), $url);
                    echo '<span style="display: block;margin: 10px 0;max-width: 200px;padding: 10px;color: #721c24;background-color: #f8d7da;border: 1px solid #f5c6cb;">' . $string . '</span>';
                }
            }
        }

        public function render_me(){
            $user_id     = get_current_user_id();

            if( BP_Better_Messages()->settings['myProfileButton'] !== '1' ) {
                $profile_url = false;
            } else {
                $profile_url = bp_core_get_userlink($user_id, false, true);
            }

            $statuses_enabled = BP_Better_Messages()->settings['userStatuses'] === '1';

            $render = false;

            if( $profile_url !== false ){
                $render = true;
            }

            if( $statuses_enabled ) {
                $status = BP_Better_Messages()->premium->get_user_status($user_id);
                $statuses = BP_Better_Messages()->premium->get_all_statuses();

                $render = true;
            }

            ob_start();

            if( $render ) { ?>
            <span class="bpbm-user-me">
            <span class="bpbm-user-me-avatar"><?php echo BP_Better_Messages()->functions->get_avatar( $user_id, 30 ); ?></span>
            <span class="bpbm-user-info">
                <span class="bpbm-user-me-name"><?php esc_attr_e( BP_Better_Messages()->functions->get_name( $user_id ) ); ?></span>
                <?php if ( $statuses_enabled ){ ?>
                <span class="bpbm-status">
                    <span class="current-status"><?php echo $statuses[$status]['icon']; echo BP_Better_Messages()->premium->get_status_display_name($status) ;?></span>
                </span>
                <?php } ?>
            </span>

            <span class="bpbm-user-me-popup" data-nonce="<?php echo wp_create_nonce( 'bp_messages_change_user_option_' . $user_id ); ?>">
                <span class="bpbm-user-me-popup-list">
                    <?php if( $profile_url !== false ){ ?>
                    <a href="<?php echo $profile_url; ?>" class="bpbm-user-me-popup-list-item">
                        <span class="bpbm-user-me-popup-list-item-title"><i class="fas fa-user-circle"></i> <?php _e('My profile', 'bp-better-messages'); ?></span>
                    </a>
                    <?php } ?>
                    <?php if ( $statuses_enabled ){ ?>
                    <span class="bpbm-user-me-popup-list-hr"></span>
                    <?php foreach( $statuses as $slug => $status ){ ?>
                        <span class="bpbm-user-me-popup-list-item" data-status="<?php esc_attr_e($slug); ?>">
                        <span class="bpbm-user-me-popup-list-item-title"><?php echo $status['icon'] ?> <?php esc_attr_e($status['name']); ?></span>
                        <?php if( isset( $status['desc'] ) ) { ?>
                            <span class="bpbm-user-me-popup-list-item-desc"><?php esc_attr_e($status['desc']); ?></span>
                        <?php } ?>
                    </span>
                    <?php } ?>
                    <?php } ?>
                </span>

            </span>
        </span>
        <?php
        }
        return ob_get_clean();
        }

        public function highlightKeywords($text, $keyword) {
            $wordsAry = explode(" ", $keyword);
            $wordsCount = count($wordsAry);

            for($i=0;$i<$wordsCount;$i++) {
                $highlighted_text = "<span style='font-weight:bold;'>$wordsAry[$i]</span>";
                $text = str_ireplace($wordsAry[$i], $highlighted_text, $text);
            }

            return $text;
        }

        public function show_avatars(){
            return ! empty( get_option('show_avatars') );
        }

        public function render_footer(){
            ob_start();
            if( BP_Better_Messages()->settings['disableUserSettings'] === '0' ) {
                echo '<a href="' . add_query_arg( 'settings', '', BP_Better_Messages()->functions->get_link() ) . '" class="settings ajax" title="'. __( 'Settings', 'bp-better-messages' ) . '"><i class="fas fa-cog" aria-hidden="true"></i></a>';
            }
            echo BP_Better_Messages()->functions->render_me();
            $footer = trim(ob_get_clean());

            if( ! empty( $footer )){
                echo '<div class="chat-footer">';
                echo $footer;
                echo '</div>';
            }
        }

        public function render_preloader(){
            ?><div class="preloader"></div>

            <?php if( BP_Better_Messages()->settings['disableTapToOpen'] === '0' ){ ?>
                <div class="bp-messages-mobile-tap"><?php _e( 'Tap to open messages', 'bp-better-messages' ); ?></div>
            <?php } ?><?php
        }

        public function render_side_column( $user_id ){
            if( ! isset( $_REQUEST['ignore_threads'] ) ) {
                $threads = BP_Better_Messages()->functions->get_threads( $user_id );
                if( count( $threads ) === 0 ) return; ?>
                <div class="bp-messages-side-threads">
                    <div class="chat-header side-header">
                        <?php
                        if( BP_Better_Messages()->settings['disableNewThread'] === '0' || current_user_can('manage_options') ) {
                            echo '<a href="' . add_query_arg( 'new-message', '', BP_Better_Messages()->functions->get_link() ) . '" class="new-message ajax" title="'. __( 'New Thread', 'bp-better-messages' ) . '"><i class="far fa-edit" aria-hidden="true"></i></a>';
                        }

                        if( BP_Better_Messages()->settings['disableSearch'] === '0' ) { ?>
                            <div class="bpbm-search">
                                <form>
                                    <input title="<?php _e('Search', 'bp-better-messages'); ?>" placeholder="<?php _e('Search...', 'bp-better-messages'); ?>" type="text" name="search" value="">
                                    <span class="close"><i class="fas fa-times" aria-hidden="true"></i></span>
                                </form>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="scroller scrollbar-inner threads-list-wrapper">
                        <div class="bpbm-search-results"></div>
                        <div class="threads-list">
                            <?php
                            if ( !empty( $threads ) ) {
                                foreach ( $threads as $thread ) {
                                    echo BP_Better_Messages()->functions->render_thread( $thread );
                                }
                            } ?>
                            <div class="loading-messages">
                                <div class="bounce1"></div>
                                <div class="bounce2"></div>
                                <div class="bounce3"></div>
                            </div>
                        </div>
                    </div>
                    <?php BP_Better_Messages()->functions->render_footer(); ?>
                </div>
                <?php
            } else {
                echo '<div class="bp-messages-side-threads"></div>';
            }
        }

        function strip_all_tags( $string, $allowed_tags = [], $remove_breaks = false ) {
            $string = preg_replace( '@<(script|style)[^>]*?>.*?</\\1>@si', '', $string );
            $string = strip_tags( $string, $allowed_tags );

            if ( $remove_breaks ) {
                $string = preg_replace( '/[\r\n\t ]+/', ' ', $string );
            }

            return trim( $string );
        }

        public function messages_classes( $thread_id = false, $type = 'thread' ){
            global $bpbmCurrentClass;
            $classes = [];

            if( $type === 'chat-room' ){
                $chat_id       = BP_Better_Messages()->functions->get_thread_meta( $thread_id, 'chat_id' );
                $chat_settings = BP_Better_Messages()->chats->get_chat_settings( $chat_id );

                if( $chat_settings['template'] === 'default' ){
                    $class = 'bpbm-template-' . BP_Better_Messages()->settings['template'];
                } else {
                    $class = 'bpbm-template-' . $chat_settings['template'];
                }

                if( $class === 'bpbm-template-modern' ) {
                    if( $chat_settings['modernLayout'] === 'default' ) {
                        $classes[] = $class . '-' . BP_Better_Messages()->settings['modernLayout'];
                    } else {
                        $classes[] = $class . '-' . $chat_settings['modernLayout'];
                    }
                }

            } else {
                $class = 'bpbm-template-' . BP_Better_Messages()->settings['template'];

                if (BP_Better_Messages()->settings['template'] === 'modern') {
                    $classes[] = $class . '-' . BP_Better_Messages()->settings['modernLayout'];
                }
            }

            $classes[] = $class;

            $bpbmCurrentClass = implode(' ',  $classes);

            echo $bpbmCurrentClass;
        }

        public function remove_re( $str ){
            $prefix = 're:';

            if (substr(strtolower($str), 0, strlen($prefix)) == $prefix) {
                $str = substr($str, strlen($prefix));
            }

            return trim($str);
        }

        /*
         * Inserts a new key/value before the key in the array.
         *
         * @param $key
         *   The key to insert before.
         * @param $array
         *   An array to insert in to.
         * @param $new_key
         *   The key to insert.
         * @param $new_value
         *   An value to insert.
         *
         * @return
         *   The new array if the key exists, FALSE otherwise.
         *
         * @see array_insert_after()
         */
        function array_insert_before($key, array &$array, $new_key, $new_value) {
            if (array_key_exists($key, $array)) {
                $new = array();
                foreach ($array as $k => $value) {
                    if ($k === $key) {
                        $new[$new_key] = $new_value;
                    }
                    $new[$k] = $value;
                }
                return $new;
            }
            return FALSE;
        }

        /*
         * Inserts a new key/value after the key in the array.
         *
         * @param $key
         *   The key to insert after.
         * @param $array
         *   An array to insert in to.
         * @param $new_key
         *   The key to insert.
         * @param $new_value
         *   An value to insert.
         *
         * @return
         *   The new array if the key exists, FALSE otherwise.
         *
         * @see array_insert_before()
         */
        function array_insert_after($key, array &$array, $new_key, $new_value) {
            if (array_key_exists ($key, $array)) {
                $new = array();
                foreach ($array as $k => $value) {
                    $new[$k] = $value;
                    if ($k === $key) {
                        $new[$new_key] = $new_value;
                    }
                }
                return $new;
            }
            return FALSE;
        }

        public function messages_mark_thread_read( $thread_id, $user_id = false ){
            global $wpdb;

            if( $user_id === false ) {
                $user_id = bp_displayed_user_id() ? bp_displayed_user_id() : bp_loggedin_user_id();
            }

            $wpdb->query( $wpdb->prepare( "UPDATE " . bpbm_get_table('recipients'). " SET unread_count = 0 WHERE user_id = %d AND thread_id = %d", $user_id, $thread_id ) );

            wp_cache_delete( 'thread_recipients_' . $thread_id, 'bp_messages' );
            wp_cache_delete( $user_id, 'bp_messages_unread_count' );

            $this->clean_thread_notifications( $thread_id, $user_id );

            return true;
        }

        public function clean_thread_notifications($thread_id, $user_id){
            if ( ! function_exists('bp_notifications_add_notification') ) {
                return false;
            }

            BP_Better_Messages_Notifications()->mark_notification_as_read( $thread_id, $user_id );
            /*global $wpdb;
            $table_name      = buddypress()->notifications->table_name;
            $table_name_meta = buddypress()->notifications->table_name_meta;

            return $wpdb->query($wpdb->prepare("UPDATE `{$table_name}` as `notifications`
            RIGHT JOIN `{$table_name_meta}` as `notifications_meta`
            ON `notifications`.`id` = `notifications_meta`.`notification_id`
            AND `notifications_meta`.`meta_key` = 'thread_id'  
            AND `notifications_meta`.`meta_value` = %d  
            SET `notifications`.`is_new` = 0
            WHERE `notifications`.`component_name` = 'messages'
            AND `notifications`.`component_action` = 'new_message'
            AND `notifications`.`user_id` = %d
            AND `notifications`.`is_new` = 1", $thread_id, $user_id )); */

        }
    }

endif;

/**
 * @return BP_Better_Messages_Functions instance | null
 */
function BP_Better_Messages_Functions()
{
    return BP_Better_Messages_Functions::instance();
}
