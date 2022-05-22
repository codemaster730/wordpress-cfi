<?php
defined( 'ABSPATH' ) || exit;

if ( !class_exists( 'BP_Better_Messages_Functions' ) ):

    class BP_Better_Messages_Functions
    {
        private  $multisite_resolved = null;

        public static function instance()
        {
            static $instance = null;

            if ( null === $instance ) {
                $instance = new BP_Better_Messages_Functions();
            }

            return $instance;
        }

        public function can_erase_thread( $user_id, $thread_id ){
            $can_erase = false;

            if( user_can( $user_id, 'manage_options' ) ){
                $can_erase = true;
            }

            return apply_filters( 'bp_better_messages_can_erase_thread', $can_erase, $user_id, $thread_id );
        }

        public function can_delete_thread( $user_id, $thread_id ){
            $can_delete = false;

            if( user_can( $user_id, 'manage_options' ) ){
                $can_delete = true;
            }

            return apply_filters( 'bp_better_messages_can_delete_thread', $can_delete, $user_id, $thread_id );
        }

        public function erase_thread( $thread_id ){
            global $wpdb;

            $message_ids = $wpdb->get_col($wpdb->prepare("SELECT id FROM " . bpbm_get_table('messages') . " WHERE `thread_id` = %d", $thread_id));

            if( count( $message_ids ) > 0 ){
                foreach ( $message_ids as $message_id ) {
                    BP_Better_Messages()->functions->delete_message($message_id);
                }
            }

            BP_Better_Messages()->hooks->clean_thread_cache( $thread_id );

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

            $type = BP_Better_Messages()->functions->get_thread_type( $thread_id );
            if( $type === 'group' ) {
                if (function_exists('bp_get_user_groups')) {
                    $group_id = BP_Better_Messages()->functions->get_thread_meta($thread_id, 'group_id');
                    if (!!$group_id) {
                        $user_groups = bp_get_user_groups($user_id, array(
                            'is_admin' => null,
                            'is_mod' => null,
                        ));

                        if (isset($user_groups[$group_id])) {
                            if ($user_groups[$group_id]->is_admin || $user_groups[$group_id]->is_mod) {
                                return true;
                            }
                        }

                        return false;
                    }
                }

                if (class_exists('PeepSoGroupsPlugin')) {
                    $group_id = BP_Better_Messages()->functions->get_thread_meta($thread_id, 'peepso_group_id');
                    if (!!$group_id) {
                        return BP_Better_Messages_Peepso_Groups::instance()->user_can_moderate( $group_id, $user_id );
                    }
                }

                if ( class_exists('UM_Groups')) {
                    $group_id = BP_Better_Messages()->functions->get_thread_meta($thread_id, 'um_group_id');
                    if (!!$group_id) {
                        return Better_Messages_Ultimate_Member_Groups::instance()->user_can_moderate( $group_id, $user_id );
                    }
                }
            }

            $participants = BP_Better_Messages()->functions->get_participants( $thread_id );

            if( $participants['count'] > 2 ){
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

            $participants_count = (bool) $wpdb->get_var($wpdb->prepare("
            SELECT COUNT(*) FROM `" . bpbm_get_table('recipients') . "` WHERE `thread_id` = %d AND `sender_only` = '0' AND `is_deleted` = '0'
            ", $thread_id ));

            if( ( $participants_count > 2 && $allow_invite ) || ( $participants_count <= 2 && BP_Better_Messages()->settings['privateThreadInvite'] === '1' ) ){
                $userIsParticipant = (bool) $wpdb->get_var($wpdb->prepare("
                SELECT COUNT(*) FROM `" . bpbm_get_table('recipients') . "` WHERE `user_id` = %d AND `thread_id` = %d AND `sender_only` = '0' AND `is_deleted` = '0'
                ", get_current_user_id(), $thread_id ));

                if( $userIsParticipant ){
                    return true;
                }
            }

            return false;
        }

        public function is_thread_participant( $user_id, $thread_id ){
            global $wpdb;

            $userIsParticipant = (bool) $wpdb->get_var($wpdb->prepare("
            SELECT COUNT(*) FROM `" . bpbm_get_table('recipients') . "` WHERE `user_id` = %d AND `thread_id` = %d AND `sender_only` = '0' AND `is_deleted` = '0'
            ", $user_id, $thread_id));

            if( $userIsParticipant ){
                return true;
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

            return $this->clean_no_subject( wp_unslash(esc_attr($subject)) );
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

            $cache_enabled = BP_Better_Messages()->settings['smartCache'] === '1';
            if( $cache_enabled && empty( $exclude_threads ) ){
                $cache = get_user_meta( $user_id, 'bpbm_threads_cache', true );

                if( ! empty( $cache ) && isset( $cache['time'] ) && isset( $cache['cached'] ) ){
                    $current_time = time();
                    $cache_expiration = 60 * 60 * 12;
                    $cache_life = $current_time - $cache['time'];

                    if( $cache_life < $cache_expiration ) {
                        foreach( $cache['cached'] as $index => $thread ){
                            $___user_id = $thread->user_id;
                            $___user = get_userdata( $___user_id );
                            $__display_name = ( is_object( $___user ) ) ? $___user->display_name : '';
                            $cache['cached'][$index]->name   = $__display_name;
                            $cache['cached'][$index]->avatar = bp_core_fetch_avatar( 'type=full&html=false&item_id=' . $___user_id );
                            $cache['cached'][$index]->html   = BP_Better_Messages()->functions->render_thread( $cache['cached'][$index] );
                        }

                        return $cache['cached'];
                    }
                }
            }

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

                $results = $wpdb->get_results( $wpdb->prepare( "
                    SELECT `recipients`.`user_id` 
                    FROM " . bpbm_get_table('recipients') . " recipients
                    RIGHT JOIN {$wpdb->users} users
                    ON `users`.`ID` = `recipients`.`user_id`
                    WHERE `recipients`.`thread_id` = %d
                ", $thread->thread_id ) );

                foreach ( (array) $results as $recipient ) {
                    if ( $user_id == $recipient->user_id ) continue;
                    $recipients[] = intval($recipient->user_id);
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
                $threads[ $index ]->date_sent    = $last_message->date_sent;

                $threads[ $index ]->user_id      = $__user_id;
                $threads[ $index ]->message_id   = intval( $last_message->id );
                $threads[ $index ]->unread_count = intval( $threads[ $index ]->unread_count );
                $threads[ $index ]->recipients   = $recipients;

                $threads[ $index ]->name         = $__display_name;
                $threads[ $index ]->avatar       = bp_core_fetch_avatar( 'type=full&html=false&item_id=' . $__user_id );
                $threads[ $index ]->html         = BP_Better_Messages()->functions->render_thread( $threads[ $index ] );
            }


            if( $cache_enabled && empty( $exclude_threads ) ){
                update_user_meta( $user_id, 'bpbm_threads_cache', [
                    'cached' => $threads,
                    'time'   => time()
                ] );
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


        public function get_messages( $thread_id, $message = false, $action = 'last_messages' ){
            global $wpdb;

            $thread = new BP_Messages_Thread($thread_id);
            if( isset($thread::$noCache) ){
                $thread::$noCache = true;
            }

            if ( $this->get_thread_message_count( $thread_id ) === 0 ) return array();

            $per_page = (int) BP_Better_Messages()->settings['messagesPerPage'];

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

            return $messages;
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
            $userLast = array();

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

        public function get_recipients( $thread_id = 0 ) {
            global $wpdb;

            $thread_id = (int) $thread_id;

            $recipients = wp_cache_get( 'bm_thread_recipients_' . $thread_id, 'bp_messages' );

            if ( false === $recipients ) {
                $recipients = array();
                $sql        = $wpdb->prepare( "
                SELECT `recipients`.* 
                FROM " . bpbm_get_table('recipients') . " `recipients`
                RIGHT JOIN {$wpdb->users} users
                ON `users`.`ID` = `recipients`.`user_id`
                WHERE `recipients`.`thread_id` = %d", $thread_id );
                $results    = $wpdb->get_results( $sql );

                foreach ( (array) $results as $recipient ) {
                    $recipients[ $recipient->user_id ] = $recipient;
                }

                wp_cache_set( 'bm_thread_recipients_' . $thread_id, $recipients, 'bp_messages' );
            }

            // Cast all items from the messages DB table as integers.
            foreach ( (array) $recipients as $key => $data ) {
                $recipients[ $key ] = (object) array_map( 'intval', (array) $data );
            }

            /**
             * Filters the recipients of a message thread.
             *
             * @since 2.2.0
             *
             * @param array $recipients Array of recipient objects.
             * @param int   $thread_id  ID of the current thread.
             */
            return apply_filters( 'bp_messages_thread_get_recipients', $recipients, $thread_id );
        }

        public function get_participants( $thread_id )
        {
            $current_user_id = get_current_user_id();
            $recipients = $this->get_recipients( $thread_id );

            if( count( $recipients ) > 2 ) {
                $users = [];
                foreach ( $recipients as $user_id => $_user ){
                    $users[ $user_id ] = $user_id;
                }

                $participants = array(
                    'recipients' => $users,
                    'links'      => array(),
                    'users'      => $users,
                    'count'      => count($recipients)
                );

                if( isset( $participants['recipients'][$current_user_id]) ) {
                    unset($participants['recipients'][$current_user_id]);
                }

                return $participants;
            }

            $cache_enabled = BP_Better_Messages()->settings['smartCache'] === '1';

            if( $cache_enabled ) {
                $user_ids = array_keys($recipients);
                sort($user_ids);
                $cache_hash = md5('cachekey:' . implode(',', $user_ids));

                $cache = BP_Better_Messages()->functions->get_thread_meta($thread_id, 'participants_cache');

                if (!empty($cache) && isset($cache['time']) && isset($cache['hash']) && isset($cache['cached'])) {

                    $current_time = time();
                    $cache_expiration = 60 * 60 * 12;
                    $cache_life = $current_time - $cache['time'];

                    if ($cache_life < $cache_expiration && $cache_hash === $cache['hash']) {
                        if (isset($cache['cached']['recipients'][$current_user_id])) {
                            unset($cache['cached']['recipients'][$current_user_id]);
                        }

                        if( isset( $cache['cached']['links'][$current_user_id] ) ) {
                            unset( $cache['cached']['links'][$current_user_id] );
                        }

                        if( count( $cache['cached']['links'] ) === 1 ) {
                            foreach ($cache['cached']['links'] as $recipient_user_id => $link) {
                                $cache['cached']['links'][$recipient_user_id] = $this->get_user_link( $recipient_user_id );
                            }
                        }

                        $cache['cached']['recipients'] = array_values($cache['cached']['recipients']);
                        $cache['cached']['links'] = array_values($cache['cached']['links']);


                        return $cache['cached'];
                    }
                }
            }

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

                //if($user->ID != get_current_user_id()) {
                $link = $this->get_user_link( $recipient->user_id );

                $participants[ 'links' ][$recipient->user_id] = $link;
                $participants[ 'recipients' ][$recipient->user_id] = $recipient->user_id;
                //}

                $args = array(
                    'name'   => ( ! empty( $user->display_name ) ) ? $user->display_name : $user->user_login,
                    'link'   => bp_core_get_userlink($recipient->user_id, false, true),
                    'avatar' => BP_Better_Messages_Functions()->get_avatar($user->ID, 40)
                );

                $participants[ 'users' ][ $recipient->user_id ] = $args;
            }


            if( $cache_enabled ) {
                BP_Better_Messages()->functions->update_thread_meta( $thread_id, 'participants_cache', [
                    'cached' => $participants,
                    'hash' => $cache_hash,
                    'time' => time()
                ] );
            }

            if( isset( $participants['recipients'][$current_user_id]) ) {
                unset($participants['recipients'][$current_user_id]);
            }

            if( isset( $participants['links'][$current_user_id]) ) {
                unset($participants['links'][$current_user_id]);
            }

            $participants['recipients'] = array_values( $participants['recipients'] );
            $participants['links'] = array_values( $participants['links'] );

            return $participants;
        }

        public function get_user_link( $user_id, $avatar_size = 20 ){
            $user = get_userdata( $user_id );
            $url  = bp_core_get_userlink( $user_id, false, true );

            $url  = apply_filters('bp_better_messages_user_url', $url, $user_id );
            $display_name = $user->display_name;
            if( empty( $display_name ) ) $display_name = $user->user_nicename;

            $display_name = apply_filters( 'bp_better_messages_display_name', $display_name, $user_id );

            if( $url !== false ){
                $link = '<a href="' . bp_core_get_userlink( $user_id, false, true ) . '" class="user bm-user">' . BP_Better_Messages_Functions()->get_avatar( $user_id, $avatar_size ) . $display_name . '</a>';
            } else {
                $link = '<span class="user bm-user">' . BP_Better_Messages_Functions()->get_avatar( $user_id, $avatar_size ) . $display_name . '</span>';
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
                $link = trailingslashit(get_permalink( get_option('woocommerce_myaccount_page_id') ) ) . $slug . '/';
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
                                do_action('bp_better_messages_message_content_start', $message['id'], $stack[ 'thread_id' ] );

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

                                do_action('bp_better_messages_message_content_end', $message['id'], $stack[ 'thread_id' ] );

                                echo '</span>'; ?>
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
                $content = str_replace("&lt;".$tag."&gt;", "<".$tag.">",  $content);
                $content = str_replace("&lt;/".$tag."&gt;", "</".$tag.">", $content);
            }

            $content = trim(str_replace(array("&nbsp;", '&amp;nbsp;'), " ", $content));

            $content = $this->process_mentions($content);

            return $content;
        }

        function process_mentions( $content ){
            $has_mentions = strpos( $content, '&lt;span class=\&quot;bm-medium-editor-mention-at' ) !== false;

            if( $has_mentions ) {
                $content = $this->subprocess_mention( $content );
            }

            return $content;
        }

        function subprocess_mention( $content ){
            $mention_start = strpos($content, '&lt;span class=\&quot;bm-medium-editor-mention-at');
            $mention_end = strpos( $content, '&lt;/span&gt;', $mention_start );

            $original_string = substr( $content, $mention_start, $mention_end - $mention_start + 13 );

            $new_string = str_replace('&lt;', '<', $original_string );
            $new_string = str_replace('&gt;', '>', $new_string );
            $new_string = str_replace('&quot;', '"', $new_string );


            $content = str_replace( $original_string, $new_string, $content );

            $has_mentions = strpos( $content, '&lt;span class=\&quot;bm-medium-editor-mention-at' ) !== false;

            if( $has_mentions ) {
                $content = $this->subprocess_mention( $content );
            }

            return $content;
        }

        function truncate($text, $length) {
            $is_sticker  = strpos( $text, '<span class="bpbm-sticker">', 0 ) === 0;
            $file_icon   = strpos( $text, '<i class="fas fa-file">' );
            $is_file     = $file_icon !== false;
            $bottom_html = false;

            if( $is_file ){
                $bottom_html = substr($text, $file_icon, strlen( $text ) - $file_icon );
                $text = substr($text, 0, $file_icon );
            }

            if( ! $is_sticker && ! $is_file ) {
                $text = strip_tags($text);
            }

            $length = abs( (int) $length );

            if(strlen($text) > $length) {
                $text = preg_replace("/^(.{1,$length})(\s.*|$)/s", '\\1...', $text);
            }

            if( $bottom_html !== false ) {
                if( strlen(trim($text)) > 0 ) {
                    $text .= "<br><br>";
                }
                $text .= $bottom_html;
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

            $name = '';
            if ( is_object( $user ) ) {
                if( ! empty($user->fullname) ) {
                    $name = $user->fullname;
                } else if( ! empty( $user->display_name ) ){
                    $name = $user->display_name;
                } else if( ! empty( $user->user_nicename ) ){
                    $name = $user->user_nicename;
                }
            } else {
                $name = '';
            }

            return apply_filters( 'bp_better_messages_display_name', $name, $user_id );
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

            return str_replace('src="//', 'src="https://', $avatar);
        }

        public function find_existing_threads($from, $to){
            global $wpdb;

            $exclude_sql = "SELECT meta_value as thread_id FROM `{$wpdb->postmeta}` as `postmeta`
            RIGHT JOIN `{$wpdb->posts}` as `posts`
            ON `posts`.`ID` = `postmeta`.`post_id`
            WHERE `posts`.`post_type` = 'bpbm-bulk-report'
            AND `postmeta`.`meta_key` = 'thread_ids'";

            $groups_sql = "'group_id','um_group_id','peepso_group_id'";

            $query_from = $wpdb->prepare("SELECT
                  recipients.thread_id
                FROM " . bpbm_get_table('recipients') . " as recipients
                LEFT JOIN " . bpbm_get_table('threadsmeta') . " threadsmetagroup ON
                    ( threadsmetagroup.`bpbm_threads_id` = recipients.`thread_id`
                    AND threadsmetagroup.meta_key IN ({$groups_sql}) )
                LEFT JOIN " . bpbm_get_table('threadsmeta') . " threadsmetachat ON
                ( threadsmetachat.`bpbm_threads_id` = recipients.`thread_id`
                AND threadsmetachat.meta_key = 'chat_id' )
                WHERE recipients.user_id = %d
	            AND ( threadsmetagroup.bpbm_threads_id IS NULL )
	            AND ( threadsmetachat.bpbm_threads_id IS NULL )
                AND recipients.is_deleted = 0
                AND recipients.thread_id NOT IN ({$exclude_sql})
                ", $from);

            $query_to = $wpdb->prepare("SELECT
                  recipients.thread_id
                FROM " . bpbm_get_table('recipients') . " as recipients
                LEFT JOIN " . bpbm_get_table('threadsmeta') . " threadsmetagroup ON
                    ( threadsmetagroup.`bpbm_threads_id` = recipients.`thread_id`
                    AND threadsmetagroup.meta_key IN ({$groups_sql}) )
                LEFT JOIN " . bpbm_get_table('threadsmeta') . " threadsmetachat ON
                ( threadsmetachat.`bpbm_threads_id` = recipients.`thread_id`
                AND threadsmetachat.meta_key = 'chat_id' )
                WHERE recipients.user_id = %d
	            AND ( threadsmetagroup.bpbm_threads_id IS NULL )
	            AND ( threadsmetachat.bpbm_threads_id IS NULL )
                AND recipients.is_deleted = 0
                AND recipients.thread_id NOT IN ({$exclude_sql})
                ", $to);

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

            $thread_type = BP_Better_Messages()->functions->get_thread_type( $thread->thread_id );

            $group_id = false;
            $group    = false;
            if( $thread_type === 'group' ) {
                if( class_exists('BP_Groups_Group') ) {
                    $group_id = BP_Better_Messages()->functions->get_thread_meta($thread->thread_id, 'group_id');
                    $group = new BP_Groups_Group((int)$group_id);
                }

                if( class_exists('PeepSoGroup') ){
                    $group_id = BP_Better_Messages()->functions->get_thread_meta($thread->thread_id, 'peepso_group_id');
                    $group = new PeepSoGroup( (int) $group_id );
                }

                if( class_exists('UM_Groups') ){
                    $group_id = BP_Better_Messages()->functions->get_thread_meta($thread->thread_id, 'um_group_id');
                    $group    = get_post( (int) $group_id );
                }
            }

            if( $thread_type === 'chat-room' ) {
                $chat_id = (int) BP_Better_Messages()->functions->get_thread_meta($thread->thread_id, 'chat_id');
            } else {
                $chat_id = false;
            }

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

            if( $recipients_count > 1 ){
                $classes[] = 'group-chat';
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

                        if( function_exists('bp_get_group_name') ) {
                            $avatar = bp_core_fetch_avatar( array(
                                'item_id'    => $group_id,
                                'avatar_dir' => 'group-avatars',
                                'object'     => 'group',
                                'type'       => 'thumb',
                            ));
                        } else if(  class_exists('PeepSoGroup') ) {
                            $avatar = $group->get_avatar_url();
                            $avatar = '<img alt="" src="' . $avatar . '" class="avatar avatar-50 photo avatar" height="50" width="50" data-size="50" loading="lazy">';
                        } else if( class_exists('UM_Groups') ) {
                            $avatar = UM()->Groups()->api()->get_group_image( $group->ID, 'default', 50, 50, false );
                        }

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

                    if( !! $group_id ) {
                        if( function_exists('bp_get_group_name') ) {
                            $group_name = bp_get_group_name($group);
                        } else if(  class_exists('PeepSoGroup') ) {
                            $group_name = $group->name;
                        } else if(  class_exists('UM_Groups') ) {
                            $group_name = esc_html( $group->post_title );
                        }
                        echo '<h4>' . $group_name . '</h4>';
                    } else if( !! $chat_id ){
                        echo '<h4>' . get_the_title( $chat_id ) . '</h4>';
                    } else if( BP_Better_Messages()->settings['disableSubject'] !== '1' ) {
                        if( ( ! empty( $thread->subject ) ) ) {
                            echo '<h4>' . wp_unslash($thread->subject) . '</h4>';
                        } else if ( $recipients_count > 1 ){
                            echo '<h4>' . ( $recipients_count + 1 ) . ' ' . __('Participants', 'bp-better-messages') . '</h4>';
                        }
                    } else if ( $recipients_count > 1 ){
                        echo '<h4>' . ( $recipients_count + 1 ) . ' ' . __('Participants', 'bp-better-messages') . '</h4>';
                    }

                    ?>
                    <p><?php
                        if ( ( $thread->user_id !== $user_id ) && ( $recipients_count > 1 ) ) {
                            echo BP_Better_Messages_Functions()->get_avatar($thread->user_id, 20);
                        }

                        echo $thread->message;
                    ?></p>
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
                'recipients' => $to_user->user_nicename,
                'subject'    => '',
                'new_thread' => true,
                'content'    => "<!-- BBPM START THREAD -->",
                'date_sent'  => null
            );
            do_action_ref_array( 'bp_better_messages_before_new_thread', array( &$args, &$errors ));

            if( empty( $errors ) ) {
                $new_thread_id = (int) $wpdb->get_var( "SELECT MAX(thread_id) FROM " . bpbm_get_table('recipients') ) + 1;

                $wpdb->insert(bpbm_get_table('recipients'), [
                    'user_id'     => $from_user->ID,
                    'thread_id'   => $new_thread_id,
                    'sender_only' => 1
                ]);

                $wpdb->insert(bpbm_get_table('recipients'), [
                    'user_id'   => $to_user->ID,
                    'thread_id' => $new_thread_id
                ]);

                $args['thread_id'] = $new_thread_id;

                if( class_exists('BP_Better_Messages_Premium') ) {
                    remove_action( 'messages_message_sent', array( BP_Better_Messages_Premium(), 'on_message_sent' ) );
                }

                remove_action( 'messages_message_sent', 'messages_notification_new_message', 10 );
                remove_action( 'messages_message_sent', 'bp_messages_message_sent_add_notification', 10 );

                $thread_id = BP_Better_Messages()->functions->new_message($args);

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

                    $wpdb->update(
                        bpbm_get_table('recipients'),
                        [
                            'unread_count' => 0
                        ],
                        [
                            'thread_id' => $thread_id
                        ]
                    );

                    do_action( 'bp_better_messages_new_thread_created', $thread_id, $message_id );

                    return $new_thread_id;
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

        public function add_user_to_thread( $thread_id, $user_id ){
            global $wpdb;

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

                BP_Better_Messages()->hooks->clean_thread_cache( $thread_id );
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

        public function get_page( $disable_admin_mode = false ){
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

                if( BP_Better_Messages()->settings['PSenableGroups'] === '1' ) {
                    $group_id = BP_Better_Messages()->functions->get_thread_meta($thread_id, 'peepso_group_id');

                    if ( !! $group_id && class_exists('BP_Better_Messages_Peepso_Groups') ) {
                        if( BP_Better_Messages_Peepso_Groups::instance()->is_group_messages_enabled( $group_id ) === 'enabled' ) {
                            return BP_Better_Messages_Peepso_Groups::instance()->get_group_page($group_id);
                        }
                    }
                }

                if( BP_Better_Messages()->settings['UMenableGroups'] === '1' ) {
                    $group_id = BP_Better_Messages()->functions->get_thread_meta($thread_id, 'um_group_id');

                    if ( !! $group_id && class_exists('UM_Groups') ) {
                        if( Better_Messages_Ultimate_Member_Groups::instance()->is_group_messages_enabled( $group_id ) === 'enabled' ) {
                            return Better_Messages_Ultimate_Member_Groups::instance()->get_group_page($group_id);
                        }
                    }
                }

                $chat_id = BP_Better_Messages()->functions->get_thread_meta($thread_id, 'chat_id');

                if( ! empty($chat_id) ) {
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
                } else if ( ! BP_Better_Messages()->functions->check_access( $thread_id ) && ! current_user_can('manage_options') ) {
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

            do_action('bp_better_messages_before_main_template_rendered');

            if( ! $this->is_ajax() && count( $bpbm_errors ) > 0 ) {
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
                $this->pre_template_include();
                include($template);
                $this->after_template_include();
            }

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

        public function get_threads_html( $user_id, $height = 400 ){
            ob_start();
            $threads = BP_Better_Messages()->functions->get_threads( $user_id );
            ?>
            <div class="bp-messages-wrap bm-threads-list" style="height: <?php echo $height; ?>px">
                <?php if ( !empty( $threads ) ) { ?>
                    <div class="scroller scrollbar-inner threads-list-wrapper">
                        <div class="threads-list">
                            <?php foreach ( $threads as $thread ) {
                                echo BP_Better_Messages()->functions->render_thread( $thread, get_current_user_id() );
                            } ?>
                            <div class="loading-messages">
                                <div class="bounce1"></div>
                                <div class="bounce2"></div>
                                <div class="bounce3"></div>
                            </div>
                        </div>
                    </div>
                <?php } else { ?>
                    <div class="threads-list">
                        <p class="empty">
                            <?php _e( 'Nothing found', 'bp-better-messages' ); ?>
                        </p>
                    </div>
                <?php } ?>
            </div>
            <?php

            return ob_get_clean();
        }

        public function get_thread_meta( $thread_id, $key = '' ) {
            $has_filter = false;
            if( has_filter( 'query', 'bp_filter_metaid_column_name') ) {
                remove_filter('query', 'bp_filter_metaid_column_name');
                $has_filter = true;
            }
            $retval = get_metadata( 'bpbm_threads', $thread_id, $key, true );

            if( $has_filter ) {
                add_filter('query', 'bp_filter_metaid_column_name');
            }
            return $retval;
        }

        function update_thread_meta( $thread_id, $meta_key, $meta_value ) {
            $has_filter = false;
            if( has_filter( 'query', 'bp_filter_metaid_column_name') ) {
                remove_filter('query', 'bp_filter_metaid_column_name');
                $has_filter = true;
            }
            $retval = update_metadata( 'bpbm_threads', $thread_id, $meta_key, $meta_value );
            if( $has_filter ) {
                add_filter('query', 'bp_filter_metaid_column_name');
            }
            return $retval;
        }

        function delete_thread_meta( $thread_id, $meta_key ) {
            $has_filter = false;
            if( has_filter( 'query', 'bp_filter_metaid_column_name') ) {
                remove_filter('query', 'bp_filter_metaid_column_name');
                $has_filter = true;
            }
            $retval = delete_metadata( 'bpbm_threads', $thread_id, $meta_key);
            if( $has_filter ) {
                add_filter('query', 'bp_filter_metaid_column_name');
            }
            return $retval;
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

        public function get_thread_type( $thread_id ){
            if( BP_Better_Messages()->settings['enableGroups'] === '1' ) {
                $group_id = BP_Better_Messages()->functions->get_thread_meta($thread_id, 'group_id');

                if ( !! $group_id && bp_is_active('groups') ) {
                    if (BP_Better_Messages()->groups->is_group_messages_enabled($group_id) === 'enabled') {
                        return 'group';
                    }
                }
            }


            if( BP_Better_Messages()->settings['PSenableGroups'] === '1' ) {
                $group_id = BP_Better_Messages()->functions->get_thread_meta($thread_id, 'peepso_group_id');

                if ( !! $group_id ){
                    return 'group';
                }
            }


            if( function_exists('UM') && BP_Better_Messages()->settings['UMenableGroups'] === '1' ) {
                $group_id = BP_Better_Messages()->functions->get_thread_meta($thread_id, 'um_group_id');

                if ( !! $group_id ){
                    return 'group';
                }
            }

            $chat_id = BP_Better_Messages()->functions->get_thread_meta( $thread_id, 'chat_id' );
            if( ! empty( $chat_id ) ) {
                return 'chat-room';
            }

            return 'thread';
        }

        public function get_thread_title( $thread_id ){
            $type = $this->get_thread_type( $thread_id );

            if( $type === 'group' ) {
                if( class_exists('BP_Groups_Group') ) {
                    $group_id = BP_Better_Messages()->functions->get_thread_meta($thread_id, 'group_id');

                    $group = new BP_Groups_Group((int)$group_id);
                    return bp_get_group_name($group);
                }
            }

            return false;
        }

        public function get_thread_image( $thread_id ){
            $type = $this->get_thread_type( $thread_id );

            if( $type === 'group' ) {
                $group_id = BP_Better_Messages()->functions->get_thread_meta($thread_id, 'group_id');

                $avatar = bp_core_fetch_avatar( array(
                    'item_id'    => $group_id,
                    'avatar_dir' => 'group-avatars',
                    'object'     => 'group',
                    'type'       => 'thumb',
                    'html'       => 'false',
                ));

                if( !! $avatar ){
                    return $avatar;
                }
            }

            return false;
        }

        public function get_friends_sorted( $user_id, $count = 'all' ){
            global $wpdb;

            if( ! function_exists('friends_get_friend_user_ids') ) {
                return [];
            }

            $friends = friends_get_friend_user_ids( $user_id );
            if( empty ( $friends ) ) return [];
            $last_active_users = [];

            foreach ( $friends as $friend ){
                $last_active_users[$friend] = 0;
            }

            $query = "SELECT user_id, meta_value FROM {$wpdb->usermeta} WHERE `user_id` IN (" . implode( ',', $friends ) . ") AND `meta_key` = 'bpbm_last_activity' ORDER BY `meta_value` DESC";
            if( $count !== 'all' ) {
                $query .= $wpdb->prepare(' LIMIT 0, %d', $count );
            }

            $last_activity = $wpdb->get_results( $query );

            if ( ! empty ( $last_activity ) ) {
                foreach ($last_activity as $item) {
                    $last_active_users[$item->user_id] = strtotime( $item->meta_value );
                }
            }

            arsort($last_active_users);

            return $last_active_users;
        }

        public function get_users_sorted( $user_id, $exclude = [], $count = 10 ){
            global $wpdb;
            $last_active_users = [];

            $excluded = [];
            $excluded_sql = '';
            if( count( $exclude ) > 0 ) {
                foreach ($exclude as $item) {
                    $excluded[] = $item;
                }

                $excluded_sql = "`user_id` NOT IN (" . implode( ',', $excluded ) . ") AND";
            }

            $query = $wpdb->prepare("SELECT user_id, meta_value FROM {$wpdb->usermeta} WHERE `user_id` != %d AND " . $excluded_sql . " `meta_key` = 'bpbm_last_activity' ORDER BY `meta_value` DESC", $user_id);
            $query .= $wpdb->prepare(' LIMIT 0, %d', $count );

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

            return $input;
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

        public function license_proposal( $return = false ){
            ob_start();
            if( ! BP_Better_Messages()->functions->can_use_premium_code() ) {
                echo '<a style="font-size: 10px;" href="' .  admin_url('admin.php?page=bp-better-messages-pricing') . '">' . __('Get WebSocket License', 'bp-better-messages') . '</a>';
            } else {
                if( ! bpbm_fs()->is_premium() ){
                    $url = bpbm_fs()->_get_latest_download_local_url();
                    $string = sprintf(__('<a href="%s" target="_blank">Download</a> and install Premium version of plugin to use this feature', 'bp-better-messages'), $url);
                    echo '<span style="display: block;margin: 10px 0;max-width: 200px;padding: 10px;color: #721c24;background-color: #f8d7da;border: 1px solid #f5c6cb;">' . $string . '</span>';
                }
            }

            $html = ob_get_clean();

            if( $return ) {
                return $html;
            } else {
                echo $html;
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

        public function render_footer( $extraClass = '' ){
            ob_start();

            do_action('bp_better_messages_render_footer_before');

            if( BP_Better_Messages()->settings['disableUserSettings'] === '0' ) {
                echo '<a href="' . add_query_arg( 'settings', '', BP_Better_Messages()->functions->get_link() ) . '" class="settings ajax" title="'. __( 'Settings', 'bp-better-messages' ) . '"><i class="fas fa-cog" aria-hidden="true"></i></a>';
            }
            echo BP_Better_Messages()->functions->render_me();

            do_action('bp_better_messages_render_footer_after');

            $footer = trim(ob_get_clean());


            if( ! empty( $footer )){
                echo '<div class="chat-footer ' . $extraClass . '">';
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
                            echo '<a href="' . add_query_arg( 'new-message', '', BP_Better_Messages()->functions->get_link( get_current_user_id() ) ) . '" class="new-message ajax" title="'. __( 'New Thread', 'bp-better-messages' ) . '"><i class="far fa-edit" aria-hidden="true"></i></a>';
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
                    <?php
                    $extra_tabs = BP_Better_Messages()->functions->side_view_extra_tabs();
                    echo BP_Better_Messages()->functions->render_extra_tabs( $extra_tabs );
                    ?>
                    <div class="scroller scrollbar-inner threads-list-wrapper">
                        <div class="bpbm-search-results"></div>
                        <?php echo BP_Better_Messages()->functions->render_extra_tabs_content( $extra_tabs ); ?>

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

        public function get_who_can_start_options(){
            $options = [
                'everyone' => _x('Everyone', 'User settings', 'bp-better-messages'),
            ];

            if( class_exists('BuddyPress') ) {
                if ( function_exists('friends_check_friendship') ) {
                    $options['only_friends'] = _x('Allow Friends', 'User settings', 'bp-better-messages');

                    if (BP_Better_Messages()->settings['friendsMode'] === '1') {
                        unset($options['everyone']);
                    }
                }
            }

            if( class_exists('BP_Better_Messages_Ultimate_Member') ){
                if( class_exists('UM_Friends_API') ) {
                    $options['um_allow_friends'] = _x('Allow Friends', 'User settings', 'bp-better-messages');
                    if ( BP_Better_Messages()->settings['umOnlyFriendsMode'] === '1' ) {
                        unset( $options['everyone'] );
                    }
                }


                if( class_exists('UM_Followers_API') ) {
                    $options['um_allow_followers'] = _x('Allow Followers', 'User settings', 'bp-better-messages');

                    if( BP_Better_Messages()->settings['umOnlyFollowersMode'] === '1' ){
                        unset( $options['everyone'] );
                    }
                }
            }



            if( class_exists('BP_Better_Messages_Peepso') ){
                if( class_exists('PeepSoFriendsPlugin') ) {
                    $options['ps_allow_friends'] = _x('Allow Friends', 'User settings', 'bp-better-messages');

                    if ( BP_Better_Messages()->settings['PSonlyFriendsMode'] === '1' ) {
                       unset( $options['everyone'] );
                    }
                }
            }

            $options['nobody'] = _x('Nobody', 'User settings', 'bp-better-messages');

            return $options;
        }

        public function get_who_can_start_value( $user_id ){
            $options = $this->get_who_can_start_options();

            $default = 'everyone';
            if( BP_Better_Messages()->settings[ 'friendsMode' ] === '1' ){
                $default = 'only_friends';
            }

            if( class_exists('BP_Better_Messages_Ultimate_Member') ) {
                if (BP_Better_Messages()->settings['umOnlyFriendsMode'] === '1' && BP_Better_Messages()->settings['umOnlyFriendsMode'] === '1') {
                    $default = 'um_allow_friends,um_allow_followers';
                }if (BP_Better_Messages()->settings['umOnlyFriendsMode'] === '1') {
                    $default = 'um_allow_friends';
                } else if (BP_Better_Messages()->settings['umOnlyFollowersMode'] === '1') {
                    $default = 'um_allow_followers';
                }
            }

            if( class_exists('PeepSoFriendsPlugin') ) {
                if (BP_Better_Messages()->settings['PSonlyFriendsMode'] === '1') {
                    $default = 'ps_allow_friends';
                }
            }

            $current = get_user_meta($user_id, 'bpbm_who_can_start_conversations', true);

            if( ! empty( $current ) ){
                $current = explode( ',', $current );
            } else {
                $current = explode(',', $default );
            }

            foreach( $current as $i => $item ){
                if( ! isset( $options[$item] ) ){
                    unset( $current[ $i ] );
                }
            }

            if( empty( $current ) ){
                $current = [ $default ];
            }

            return $current;
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

            if( ! is_user_logged_in() ) {
                $classes[] = 'bpbm-not-logged-in';
            }

            $bpbmCurrentClass = implode(' ',  $classes);

            echo $bpbmCurrentClass;
        }

        public function remove_re( $str ){
            $prefix = 're:';

            $str = trim($str);

            while( substr(strtolower($str), 0, strlen($prefix)) == $prefix ) {
                $str = trim(substr($str, strlen($prefix)));
            }

            return trim($str);
        }

        public function clean_no_subject( $subject ){
            if( defined('BP_PLATFORM_VERSION') ){
                $text = __( 'No Subject', 'buddyboss' );
            } else {
                $text = __( 'No Subject', 'buddypress' );
            }

            if( trim( $subject ) === $text ){
                return '';
            } else {
                return $subject;
            }
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

            $current_unread = (int) $wpdb->get_var( $wpdb->prepare("SELECT unread_count FROM " . bpbm_get_table('recipients') . " WHERE user_id = %d AND thread_id = %d", $user_id, $thread_id) );

            if( $current_unread > 0 ){
                $wpdb->query( $wpdb->prepare( "UPDATE " . bpbm_get_table('recipients'). " SET unread_count = 0 WHERE user_id = %d AND thread_id = %d", $user_id, $thread_id ) );
            }

            wp_cache_delete( 'thread_recipients_' . $thread_id, 'bp_messages' );
            wp_cache_delete( 'bm_thread_recipients_' . $thread_id, 'bp_messages' );

            wp_cache_delete( $user_id, 'bp_messages_unread_count' );


            $this->clean_thread_notifications( $thread_id, $user_id );


            return true;
        }

        function sanitize_xss($value) {
            return htmlspecialchars(strip_tags($value));
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

        public function can_use_premium_code_premium_only(){
            if( $this->is_network_subsite_and_has_license() ) {
                return true;
            }

            return bpbm_fs()->can_use_premium_code__premium_only();
        }

        public function can_use_premium_code(){
            if( $this->is_network_subsite_and_has_license() ) {
                return true;
            }

            return bpbm_fs()->can_use_premium_code();
        }

        public function is_network_subsite_and_has_license(){
            if( defined('MULTISITE') && defined('SUBDOMAIN_INSTALL') && MULTISITE === true && SUBDOMAIN_INSTALL === false ) {
                if( $this->multisite_resolved !== null ){
                    return $this->multisite_resolved;
                }

                if( is_plugin_active_for_network(basename(BP_Better_Messages()->path) . '/bp-better-messages.php') ) {
                    $network = get_network();
                    $main_site_id = (int)$network->site_id;
                    $main_blog_id = (int)$network->blog_id;
                    if (get_current_blog_id() !== $main_blog_id) {
                        $fs_blog = get_blog_option($main_blog_id, 'fs_accounts', false);
                        if (isset($fs_blog['sites']['bp-better-messages'])) {
                            $site = $fs_blog['sites']['bp-better-messages'];

                            if (isset($site->license_id)) {
                                $license_id = $site->license_id;

                                $fs_network = get_network_option($main_site_id, 'fs_accounts', false);
                                if (isset($fs_network['all_licenses']['1557']) && is_array($fs_network['all_licenses']['1557'])) {
                                    foreach ($fs_network['all_licenses']['1557'] as $_license) {
                                        if ( (int)$license_id === (int)$_license->id ) {
                                            define('BP_BETTER_MESSAGES_FORCE_LICENSE_KEY', $_license->secret_key);
                                            define('BP_BETTER_MESSAGES_FORCE_DOMAIN', $site->url);
                                            $this->multisite_resolved = true;
                                            return true;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                $this->multisite_resolved = false;

                return false;
            }

            return false;
        }

        public function get_user_mycred_charge_rate( $user_id ){
            $charge_values = BP_Better_Messages()->settings['myCredNewMessageCharge'];

            $enabled_roles = [];

            foreach ( $charge_values as $role => $value ){
                if( $value['value'] > 0 ){
                    $enabled_roles[$role] = (int) $value['value'];
                }
            }

            if( count( $enabled_roles ) === 0 ) {
                return 0;
            }

            $user       = get_userdata( $user_id );
            $user_roles = (array) $user->roles;

            $user_charge_rate = 0;

            foreach( $user_roles as $user_role ){
                if( isset( $enabled_roles[ $user_role ] ) ) {
                    $role_charge = (int) $enabled_roles[ $user_role ];

                    if( $role_charge > $user_charge_rate ){
                        $user_charge_rate = $role_charge;
                    }
                }
            }

            return $user_charge_rate;
        }

        public function render_user( $user ){
            $user_id = $user->ID;
            ob_start();

            $base_link = BP_Better_Messages()->functions->get_link( get_current_user_id() );

            $audioCall = false;
            $videoCall = false;

            if( BP_Better_Messages()->settings['audioCalls'] === '1' ) {
                $audioCall = add_query_arg([
                    'fast-call' => '',
                    'to' => $user_id,
                    'type' => 'audio'
                ], $base_link);
            }

            if( BP_Better_Messages()->settings['videoCalls'] === '1' ) {
                $videoCall = add_query_arg([
                    'fast-call' => '',
                    'to' => $user_id,
                    'type' => 'video'
                ], $base_link);
            }

            ?>
            <div class="user" data-id="<?php esc_attr_e($user_id); ?>" data-username="<?php esc_attr_e($user->user_nicename); ?>">
                <div class="pic">
                    <?php echo BP_Better_Messages_Functions()->get_avatar( $user_id, 30 ); ?>
                </div>
                <div class="name"><?php echo BP_Better_Messages_Functions()->get_name( $user_id ); ?></div>
                <div class="actions">
                    <?php if( !! $videoCall ) { ?>
                    <a title="<?php _e('Video Call', 'bp-better-messages'); ?>" href="<?php echo $videoCall; ?>" class="open-profile bpbm-audio-call" data-user-id="<?php echo $user_id; ?>"><i class="fas fa-video"></i></a>
                    <?php } ?>
                    <?php if( !! $audioCall ) { ?>
                    <a title="<?php _e('Audio Call', 'bp-better-messages'); ?>" href="<?php echo $audioCall; ?>" class="open-profile bpbm-video-call" data-user-id="<?php echo $user_id; ?>"><i class="fas fa-phone"></i></a>
                    <?php } ?>
                    <a title="<?php _e('User profile', 'bp-better-messages'); ?>" href="<?php echo bp_core_get_userlink( $user_id, false, true ); ?>" class="open-profile"><i class="fas fa-user"></i></a>
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

        public function render_group( $group ){
            $group_id = $group->id;
            ob_start();

            $messages_enabled = 'bpbm-messages-' . BP_Better_Messages()->groups->is_group_messages_enabled( $group_id );
            $thread_id = BP_Better_Messages()->groups->get_group_thread_id( $group->id );
            ?><div class="group <?php echo $messages_enabled; ?>" data-group-id="<?php esc_attr_e($group->id); ?>" data-thread-id="<?php esc_attr_e($thread_id); ?>">
                <?php $avatar = bp_core_fetch_avatar( array(
                    'item_id'    => $group_id,
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

            return ob_get_clean();
        }

        public function get_user_mycred_charge_new_thread_rate( $user_id ){
            $charge_values = BP_Better_Messages()->settings['myCredNewThreadCharge'];

            $enabled_roles = [];

            foreach ( $charge_values as $role => $value ){
                if( $value['value'] > 0 ){
                    $enabled_roles[$role] = (int) $value['value'];
                }
            }

            if( count( $enabled_roles ) === 0 ) {
                return 0;
            }

            $user       = get_userdata( $user_id );
            $user_roles = (array) $user->roles;

            $user_charge_rate = 0;

            foreach( $user_roles as $user_role ){
                if( isset( $enabled_roles[ $user_role ] ) ) {
                    $role_charge = (int) $enabled_roles[ $user_role ];

                    if( $role_charge > $user_charge_rate ){
                        $user_charge_rate = $role_charge;
                    }
                }
            }

            return $user_charge_rate;
        }

        public function side_view_extra_tabs(){
            $extra_tabs = [];

            if( BP_Better_Messages()->settings['combinedFriendsEnable'] === '1' && function_exists('friends_get_friend_user_ids') ){
                $extra_tabs[] = 'friends';
            }

            if( BP_Better_Messages()->settings['combinedGroupsEnable'] === '1' && function_exists('groups_get_user_groups') ){
                $extra_tabs[] = 'groups';
            }


            return apply_filters( 'bp_better_messages_side_extra_widgets', $extra_tabs );
        }

        public function side_view_mobile_extra_tabs(){
            $extra_tabs = [];

            if( BP_Better_Messages()->settings['mobileFriendsEnable'] === '1' && function_exists('friends_get_friend_user_ids') ){
                $extra_tabs[] = 'friends';
            }

            if( BP_Better_Messages()->settings['mobileGroupsEnable'] === '1' && function_exists('groups_get_user_groups') ){
                $extra_tabs[] = 'groups';
            }

            return apply_filters( 'bp_better_messages_mobile_extra_widgets', $extra_tabs );
        }

        public function render_extra_tabs( $tabs, $extraClass = '' ){
            ob_start();
            if( count( $tabs ) > 0 ){
                echo '<div class="chat-tabs chat-tabs-border-bottom ' . $extraClass . '">';
                echo '<div data-tab="threads-list" class="active"><i class="fas fa-comments"></i> ' . _x('Messages', 'Combined View Tabs', 'bp-better-messages') . '</div>';
                foreach ( $tabs as $tab ){
                    switch ( $tab ){
                        case 'friends':
                            if( function_exists('friends_get_friend_user_ids') ){
                                echo '<div data-tab="bpbm-friends-list"><i class="fas fa-user-friends"></i> ' . _x('Friends', 'Combined View Tabs', 'bp-better-messages') . '</div>';
                            }
                            break;
                        case 'groups':
                            if( function_exists('groups_get_user_groups') ){
                                echo '<div data-tab="bpbm-groups-list"><i class="fas fa-users"></i> ' . _x('Groups', 'Combined View Tabs', 'bp-better-messages') . '</div>';
                            }
                            break;

                        default:
                            do_action('bp_better_messages_side_extra_tabs_head', $tab);
                            break;
                    }
                }
                echo '</div>';
            }
            return ob_get_clean();
        }

        public function render_extra_mobile_tabs( $tabs ){
            ob_start();
            if( count( $tabs ) > 0 ){
                echo '<div class="chat-tabs chat-tabs-border-top bpbm-mobile-only">';
                echo '<div data-tab="threads-list" class="active"><i class="fas fa-comments"></i> ' . _x('Messages', 'Combined View Tabs', 'bp-better-messages') . '</div>';

                foreach ( $tabs as $tab ){
                    switch ( $tab ){
                        case 'friends':
                            if( function_exists('friends_get_friend_user_ids') ){
                                echo '<div data-tab="bpbm-friends-list"><i class="fas fa-user-friends"></i> ' . _x('Friends', 'Combined View Tabs', 'bp-better-messages') . '</div>';
                            }
                            break;
                        case 'groups':
                            if( function_exists('groups_get_user_groups') ){
                                echo '<div data-tab="bpbm-groups-list"><i class="fas fa-users"></i> ' . _x('Groups', 'Combined View Tabs', 'bp-better-messages') . '</div>';
                            }
                            break;

                        default:
                            do_action('bp_better_messages_mobile_extra_tabs_head', $tab);
                            break;
                    }
                }
                echo '</div>';
            }
            return ob_get_clean();
        }

        public function render_extra_tabs_content( $tabs ){
            ob_start();
            if( count( $tabs ) > 0 ){
                foreach ( $tabs as $tab ){
                    switch ( $tab ){
                        case 'friends':
                            if( function_exists('friends_get_friend_user_ids') ){
                                echo '<div class="bpbm-friends-list" style="display: none"><div class="bpbm-loader-icon"><i class="fas fa-spinner fa-spin"></i></div></div>';
                            }
                            break;
                        case 'groups':
                            if( function_exists('groups_get_user_groups') ){
                                echo '<div class="bpbm-groups-list" style="display: none"><div class="bpbm-loader-icon"><i class="fas fa-spinner fa-spin"></i></div></div>';
                            }
                            break;

                        default:
                            do_action('bp_better_messages_side_extra_tabs_content', $tab);
                            break;
                    }
                }
            }
            return ob_get_clean();
        }

        public function render_extra_mobile_tabs_content( $tabs ){
            ob_start();
            if( count( $tabs ) > 0 ){
                foreach ( $tabs as $tab ){
                    switch ( $tab ){
                        case 'friends':
                            if( function_exists('friends_get_friend_user_ids') ){
                                echo '<div class="bpbm-friends-list" style="display: none"><div class="bpbm-loader-icon"><i class="fas fa-spinner fa-spin"></i></div></div>';
                            }
                            break;
                        case 'groups':
                            if( function_exists('groups_get_user_groups') ){
                                echo '<div class="bpbm-groups-list" style="display: none"><div class="bpbm-loader-icon"><i class="fas fa-spinner fa-spin"></i></div></div>';
                            }
                            break;

                        default:
                            do_action('bp_better_messages_mobile_extra_tabs_content', $tab);
                            break;
                    }
                }
            }
            return ob_get_clean();
        }

        public function render_login_form(){
            ob_start();
            ?>
            <style type="text/css">
                .bm-login-form{
                    background: white;
                    border: 1px solid #ccc;
                    color: black;
                    padding: 15px 25px;
                    margin: 15px auto;
                    width: 100%;
                    max-width: 600px;
                }

                .bm-login-form .bm-login-text{
                    color: black;
                    font-size: 16px;
                    margin: 10px 0 20px;
                    font-weight: bold;
                }

                .bm-login-form form label{
                    display: block;
                    width: 100%;
                    margin-bottom: 10px;
                }
                .bm-login-form form input[type="text"],
                .bm-login-form form input[type="password"]{
                    display: block;
                    width: 100%;
                }
            </style>
            <div class="bm-login-form">
                <?php
                echo '<p class="bm-login-text">' . _x('Login required', 'Login form for unlogged users', 'bp-better-messages') . '</p>';

                wp_login_form([
                    'form_id' => 'bm-login-form'
                ]);
                ?>
            </div>
            <?php return ob_get_clean();
        }

        public function pre_template_include(){
            remove_filter( 'the_content', 'convert_smilies', 20 );
        }

        public function after_template_include(){
            #add_filter( 'the_content', 'convert_smilies', 20 );
        }

        public function array_map_recursive($callback, $array)
        {
            $func = function ($item) use (&$func, &$callback) {
                return is_array($item) ? array_map($func, $item) : call_user_func($callback, $item);
            };

            return array_map($func, $array);
        }

        public function new_message( $args = '' ) {
            // Parse the default arguments.
            $r = bp_parse_args( $args, array(
                'sender_id'    => bp_loggedin_user_id(),
                'thread_id'    => false,   // False for a new message, thread id for a reply to a thread.
                'recipients'   => array(), // Can be an array of usernames, user_ids or mixed.
                'subject'      => false,
                'content'      => false,
                'send_push'    => true,
                'count_unread' => true,
                'show_on_site' => true,
                'meta'         => false,
                'date_sent'    => bp_core_current_time(),
                'return'       => 'thread_id',
                'error_type'   => 'bool'
            ), 'messages_new_message' );

            // Bail if no sender or no content.
            if ( empty( $r['sender_id'] ) || empty( $r['content'] ) ) {
                if ( 'wp_error' === $r['error_type'] ) {
                    if ( empty( $r['sender_id'] ) ) {
                        $error_code = 'messages_empty_sender';
                        $feedback   = __( 'Your message was not sent. Please use a valid sender.', 'buddypress' );
                    } else {
                        $error_code = 'messages_empty_content';
                        $feedback   = __( 'Your message was not sent. Please enter some content.', 'buddypress' );
                    }

                    return new WP_Error( $error_code, $feedback );

                } else {
                    return false;
                }
            }

            // Create a new message object.
            $message            = new BP_Messages_Message;
            $message->thread_id = $r['thread_id'];
            $message->sender_id = $r['sender_id'];
            $message->subject   = $r['subject'];
            $message->message   = $r['content'];
            $message->date_sent = $r['date_sent'];
            $message->send_push = $r['send_push'];

            $new_thread = false;

            // If we have a thread ID...
            if ( ! empty( $r['thread_id'] ) ) {

                // ...use the existing recipients
                $thread              = new BP_Messages_Thread( $r['thread_id'] );
                $message->recipients = $this->get_recipients( $thread->thread_id );

                // Strip the sender from the recipient list, and unset them if they are
                // not alone. If they are alone, let them talk to themselves.
                if ( isset( $message->recipients[ $r['sender_id'] ] ) && ( count( $message->recipients ) > 1 ) ) {
                    unset( $message->recipients[ $r['sender_id'] ] );
                }

                // Set a default reply subject if none was sent.
                if ( empty( $message->subject ) ) {
                    $message->subject = sprintf( __( 'Re: %s', 'buddypress' ), $thread->messages[0]->subject );
                }

                // ...otherwise use the recipients passed
            } else {

                // Bail if no recipients.
                if ( empty( $r['recipients'] ) ) {
                    if ( 'wp_error' === $r['error_type'] ) {
                        return new WP_Error( 'message_empty_recipients', __( 'Message could not be sent. Please enter a recipient.', 'buddypress' ) );
                    } else {
                        return false;
                    }
                }

                // Set a default subject if none exists.
                if ( empty( $message->subject ) ) {
                    $message->subject = __( 'No Subject', 'buddypress' );
                }

                // Setup the recipients array.
                $recipient_ids = array();

                // Invalid recipients are added to an array, for future enhancements.
                $invalid_recipients = array();

                // Loop the recipients and convert all usernames to user_ids where needed.
                foreach ( (array) $r['recipients'] as $recipient ) {

                    // Trim spaces and skip if empty.
                    $recipient = trim( $recipient );
                    if ( empty( $recipient ) ) {
                        continue;
                    }

                    // Check user_login / nicename columns first
                    // @see http://buddypress.trac.wordpress.org/ticket/5151.
                    if ( bp_is_username_compatibility_mode() ) {
                        $recipient_id = bp_core_get_userid( urldecode( $recipient ) );
                    } else {
                        $recipient_id = bp_core_get_userid_from_nicename( $recipient );
                    }

                    // Check against user ID column if no match and if passed recipient is numeric.
                    if ( empty( $recipient_id ) && is_numeric( $recipient ) ) {
                        if ( bp_core_get_core_userdata( (int) $recipient ) ) {
                            $recipient_id = (int) $recipient;
                        }
                    }

                    // Decide which group to add this recipient to.
                    if ( empty( $recipient_id ) ) {
                        $invalid_recipients[] = $recipient;
                    } else {
                        $recipient_ids[] = (int) $recipient_id;
                    }
                }

                // Strip the sender from the recipient list, and unset them if they are
                // not alone. If they are alone, let them talk to themselves.
                $self_send = array_search( $r['sender_id'], $recipient_ids );
                if ( ! empty( $self_send ) && ( count( $recipient_ids ) > 1 ) ) {
                    unset( $recipient_ids[ $self_send ] );
                }

                // Remove duplicates & bail if no recipients.
                $recipient_ids = array_unique( $recipient_ids );
                if ( empty( $recipient_ids ) ) {
                    if ( 'wp_error' === $r['error_type'] ) {
                        return new WP_Error( 'message_invalid_recipients', __( 'Message could not be sent because you have entered an invalid username. Please try again.', 'buddypress' ) );
                    } else {
                        return false;
                    }
                }

                // Format this to match existing recipients.
                foreach ( (array) $recipient_ids as $i => $recipient_id ) {
                    $message->recipients[ $i ]          = new stdClass;
                    $message->recipients[ $i ]->user_id = $recipient_id;
                }

                $new_thread = true;
            }

            // Bail if message failed to send.
            $send = $message->send();
            if ( false === is_int( $send ) ) {
                if ( 'wp_error' === $r['error_type'] ) {
                    if ( is_wp_error( $send ) ) {
                        return $send;
                    } else {
                        return new WP_Error( 'message_generic_error', __( 'Message was not sent. Please try again.', 'buddypress' ) );
                    }
                }

                return false;
            }

            if( $new_thread ){
                BP_Better_Messages()->functions->update_thread_meta( $message->thread_id, 'thread_starter_user_id', $r['sender_id'] );
                BP_Better_Messages()->functions->update_thread_meta( $message->thread_id, 'thread_start_time', time() );
            }

            $message->count_unread = $r['count_unread'] ? '1' : '0';
            $message->show_on_site = $r['show_on_site'] ? '1' : '0';
            $message->send_push    = $r['send_push'];
            $message->meta         = $r['meta'];

            /**
             * Fires after a message has been successfully sent.
             *
             * @since 1.1.0
             *
             * @param BP_Messages_Message $message Message object. Passed by reference.
             */
            do_action_ref_array( 'messages_message_sent', array( &$message ) );

            if( $args['return'] === 'message_id' ){
                return $message->id;
            }
            // Return the thread ID.
            return $message->thread_id;
        }

        public function update_message( $args = '' ){
            global $wpdb;

            // Parse the default arguments.
            $r = bp_parse_args( $args, array(
                'sender_id'    => bp_loggedin_user_id(),
                'thread_id'    => false,
                'message_id'   => false,
                'send_push'    => true,
                'count_unread' => false,
                'show_on_site' => false,
                'subject'      => false,
                'content'      => false
            ), 'messages_update_message' );

            $message = new BP_Messages_Message( $r['message_id'] );

            if( (int) $r['sender_id'] !== (int) $message->sender_id ) {
                return false;
            }

            $message->recipients = $message->get_recipients();

            $message->message = apply_filters( 'messages_message_content_before_save', $r['content'], $message->id );

            $wpdb->update(bpbm_get_table('messages'), [
                'message' => $message->message
            ], [
                'id' => $message->id
            ], ['%s'], ['%d']);

            if( function_exists('BP_Better_Messages_Premium') ) {
                $message->count_unread = $r['count_unread'] ? '1' : '0';
                $message->send_push    = $r['send_push'];
                $message->show_on_site = $r['show_on_site'] ? '1' : '0';
                $message->meta         = $r['meta'];

                BP_Better_Messages_Premium()->on_message_sent($message);
            }

            return true;
        }

        public function check_access( $thread_id, $user_id = 0 ) {

            if ( empty( $user_id ) ) {
                $user_id = bp_loggedin_user_id();
            }

            $recipients = $this->get_recipients( $thread_id );

            if ( isset( $recipients[ $user_id ] ) && 0 == $recipients[ $user_id ]->is_deleted ) {
                return $recipients[ $user_id ]->id;
            } else {
                return null;
            }
        }

        public function user_has_role( $user_id, $roles = [] ){
            $user             = wp_get_current_user();
            $user_roles       = (array) $user->roles;

            $has_role = false;

            foreach( $user_roles as $user_role ){
                if( in_array( $user_role, $roles ) ){
                    $has_role = true;
                }
            }

            return $has_role;

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
