<?php
defined( 'ABSPATH' ) || exit;

if ( !class_exists( 'BP_Better_Messages_Notifications' ) ):

    class BP_Better_Messages_Notifications
    {

        public static function instance()
        {

            static $instance = null;

            if ( null === $instance ) {
                $instance = new BP_Better_Messages_Notifications();
            }

            return $instance;
        }

        public function __construct()
        {
            add_action( 'init', array( $this, 'remove_standard_notification' ) );
            add_action( 'init', array( $this, 'register_event' ) );

            $notifications_interval = (int) BP_Better_Messages()->settings['notificationsInterval'];
            if( $notifications_interval > 0 ) {
                add_action( 'bp_send_email', array( $this, 'bp_on_send_email' ), 10, 4 );
                add_action('bp_better_messages_send_notifications', array($this, 'notifications_sender'));
            }
        }

        public function mark_notification_as_read( $target_thread_id, $user_id ){
            if( ! function_exists( 'bp_notifications_delete_notification' ) ) return false;

            global $wpdb;

            $notifications = $wpdb->get_results($wpdb->prepare("
            SELECT * FROM `" . bpbm_get_table('notifications') . "` 
            WHERE `user_id` = %d
            AND `component_name` = 'messages' 
            AND `component_action` = 'new_message' 
            AND `is_new` = 1 
            ORDER BY `id` DESC", $user_id ));


            $notifications_ids = array();
            foreach($notifications as $notification){
                $thread_id = $wpdb->get_var($wpdb->prepare("SELECT thread_id FROM `" . bpbm_get_table('messages') . "` WHERE `id` = %d", $notification->item_id));
                if($thread_id === NULL)
                {
                    bp_notifications_delete_notification($notification->id);
                    continue;
                } else {
                    if($thread_id == $target_thread_id) $notifications_ids[] = $notification->id;
                }
            }

            if( count($notifications_ids) > 0){
                $notifications_ids = array_unique($notifications_ids);
                foreach($notifications_ids as $notification_id){
                    BP_Notifications_Notification::update(
                        array( 'is_new' => false ),
                        array( 'id'     => $notification_id )
                    );
                }
            }
        }

        public function register_event()
        {
            if ( ! wp_next_scheduled( 'bp_better_messages_send_notifications' ) ) {
                wp_schedule_event( time(), 'bp_better_messages_notifications', 'bp_better_messages_send_notifications' );
            }
        }

        public function install_template_if_missing(){
            if( ! function_exists('bp_get_email_post_type') ) return false;
            if( ! apply_filters('bp_better_message_fix_missing_email_template', true ) ) return false;
            if( BP_Better_Messages()->settings['createEmailTemplate'] !== '1' ) return false;

            $defaults = array(
                'post_status' => 'publish',
                'post_type'   => bp_get_email_post_type(),
            );

            $emails = array(
                'messages-unread-group' => array(
                    /* translators: do not remove {} brackets or translate its contents. */
                    'post_title'   => __( '[{{{site.name}}}] You have unread messages: {{subject}}', 'bp-better-messages' ),
                    /* translators: do not remove {} brackets or translate its contents. */
                    'post_content' => __( "You have unread messages: &quot;{{subject}}&quot;\n\n{{{messages.html}}}\n\n<a href=\"{{{thread.url}}}\">Go to the discussion</a> to reply or catch up on the conversation.", 'bp-better-messages' ),
                    /* translators: do not remove {} brackets or translate its contents. */
                    'post_excerpt' => __( "You have unread messages: \"{{subject}}\"\n\n{{messages.raw}}\n\nGo to the discussion to reply or catch up on the conversation: {{{thread.url}}}", 'bp-better-messages' ),
                )
            );

            $descriptions[ 'messages-unread-group' ] = __( 'A member has unread private messages.', 'bp-better-messages' );

            // Add these emails to the database.
            foreach ( $emails as $id => $email ) {
                $post_args = bp_parse_args( $email, $defaults, 'install_email_' . $id );

                $template = get_page_by_title( $post_args[ 'post_title' ], OBJECT, bp_get_email_post_type() );

                if ( $template ){

                    if( $template->post_status === 'publish' ){
                        continue;
                    }
                }

                $post_id = wp_insert_post( $post_args );

                if ( !$post_id ) {
                    continue;
                }

                $tt_ids = wp_set_object_terms( $post_id, $id, bp_get_email_tax_type() );
                foreach ( $tt_ids as $tt_id ) {
                    $term = get_term_by( 'term_taxonomy_id', (int)$tt_id, bp_get_email_tax_type() );
                    wp_update_term( (int)$term->term_id, bp_get_email_tax_type(), array(
                        'description' => $descriptions[ $id ],
                    ) );
                }
            }
        }

        public function bp_on_send_email(&$email, $email_type, $to, $args){
            if( $email_type !== 'messages-unread-group' ) {
                return false;
            }

            $tokens = $email->get_tokens();

            if( isset( $tokens['subject'] ) ){
                $subject = $tokens['subject'];

                if( $subject === '' ){
                    $email_subject   = $email->get_subject();
                    $email_plaintext = $email->get_content_plaintext();
                    $email_html      = $email->get_content_html();

                    $to_remove = [ '&quot;{{subject}}&quot;', '"{{subject}}"', '{{subject}}' ];

                    foreach ( $to_remove as $str ){
                        $email_subject   = trim(str_replace( $str, '', $email_subject ) );
                        $email_plaintext = trim(str_replace( $str, '', $email_plaintext ) );
                        $email_html      = trim(str_replace( $str, '', $email_html ) );
                    }


                    if(substr($email_subject, -1, 1) === ':'){
                        $email_subject = substr($email_subject, 0, strlen($email_subject) - 1);
                    }

                    $email->set_subject( $email_subject );
                    $email->set_content_plaintext( $email_plaintext );
                    $email->set_content_html( $email_html );
                }
            }
        }

        public function notifications_sender()
        {
            global $wpdb;

            set_time_limit(0);

            $this->install_template_if_missing();

            /**
             * Update users without activity
             */
            $user_without_last_activity = get_users( array(
                'number'       => -1,
                'meta_key'     => 'bpbm_last_activity',
                'meta_compare' => 'NOT EXISTS',
                'fields'       => 'ids'
            ) );

            if( count( $user_without_last_activity ) > 0 ){
                foreach( $user_without_last_activity as $user_id ){
                    $last_activity = get_user_meta( $user_id, 'last_activity', true );

                    if( ! empty( $last_activity ) ){
                        update_user_meta( $user_id, 'bpbm_last_activity', $last_activity );
                    } else {
                        update_user_meta( $user_id, 'bpbm_last_activity', gmdate( 'Y-m-d H:i:s',  0 ) );
                    }
                }
            }

            $minutes = BP_Better_Messages()->settings['notificationsOfflineDelay'];
            $time = gmdate( 'Y-m-d H:i:s', ( strtotime( bp_core_current_time() ) - ( 60 * $minutes ) ) );

            $sql = "SELECT
              usermeta.meta_value AS last_visit,
              usermeta.user_id as user_id,
              " . bpbm_get_table('recipients') . ".thread_id,
              " . bpbm_get_table('recipients') . ".unread_count,
              " . bpbm_get_table('messages') . ".id AS last_id
            FROM " . bpbm_get_table('recipients') . "
              INNER JOIN {$wpdb->usermeta} as usermeta
                ON " . bpbm_get_table('recipients') . ".user_id = usermeta.user_id
              INNER JOIN " . bpbm_get_table('messages') . "
                ON " . bpbm_get_table('messages') . ".thread_id = " . bpbm_get_table('recipients') . ".thread_id
                  AND " . bpbm_get_table('messages') . ".id = (
                      SELECT MAX(m2.id)
                      FROM " . bpbm_get_table('messages') . " m2 
                      WHERE m2.thread_id = " . bpbm_get_table('recipients') . ".thread_id
                  )
            WHERE usermeta.meta_key = 'bpbm_last_activity'
            AND STR_TO_DATE(usermeta.meta_value, '%Y-%m-%d %H:%i:%s') < " . $wpdb->prepare('%s', $time) . "
            AND " . bpbm_get_table('recipients') . ".unread_count > 0
            AND " . bpbm_get_table('recipients') . ".is_deleted = 0
            GROUP BY usermeta.user_id,
            " . bpbm_get_table('recipients') . ".thread_id";


            $unread_threads = $wpdb->get_results( $sql );


            $last_notified = array();

            foreach ( array_unique( wp_list_pluck( $unread_threads, 'user_id' ) ) as $user_id ) {
                $meta = get_user_meta( $user_id, 'bp-better-messages-last-notified', true );
                $last_notified[ $user_id ] = ( !empty( $meta ) ) ? $meta : array();
            }

            $gmt_offset = get_option('gmt_offset') * 3600;

            foreach ( $unread_threads as $thread ) {
                $user_id   = $thread->user_id;
                $thread_id = $thread->thread_id;

                $muted_threads = BP_Better_Messages()->functions->get_user_muted_threads( $user_id );
                if( isset( $muted_threads[ $thread_id ] ) ){
                    continue;
                }

                $type = BP_Better_Messages()->functions->get_thread_type( $thread_id );

                if( $type === 'group' ) {
                    if ( BP_Better_Messages()->settings['enableGroupsEmails'] !== '1' ) {
                        $group_id = BP_Better_Messages()->functions->get_thread_meta($thread_id, 'group_id');

                        if (!empty($group_id)) {
                            $last_notified[$user_id][$thread_id] = $thread->last_id;
                            continue;
                        }
                    }

                    if ( BP_Better_Messages()->settings['PSenableGroupsEmails'] !== '1' ) {
                        $group_id = BP_Better_Messages()->functions->get_thread_meta($thread_id, 'peepso_group_id');

                        if (!empty($group_id)) {
                            $last_notified[$user_id][$thread_id] = $thread->last_id;
                            continue;
                        }
                    }

                    if ( BP_Better_Messages()->settings['UMenableGroupsEmails'] !== '1' ) {
                        $group_id = BP_Better_Messages()->functions->get_thread_meta($thread_id, 'um_group_id');

                        if (!empty($group_id)) {
                            $last_notified[$user_id][$thread_id] = $thread->last_id;
                            continue;
                        }
                    }
                }

                if( $type === 'chat-room' ) {
                    $chat_id = BP_Better_Messages()->functions->get_thread_meta($thread_id, 'chat_id');

                    if (!empty($chat_id)) {
                        $is_excluded_from_threads_list = BP_Better_Messages()->functions->get_thread_meta($thread_id, 'exclude_from_threads_list');
                        if ($is_excluded_from_threads_list === '1') {
                            $last_notified[$user_id][$thread_id] = $thread->last_id;
                            continue;
                        }

                        $notifications_enabled = BP_Better_Messages()->functions->get_thread_meta($thread_id, 'enable_notifications');
                        if ($notifications_enabled !== '1') {
                            $last_notified[$user_id][$thread_id] = $thread->last_id;
                            continue;
                        }
                    }
                }

                if ( get_user_meta( $user_id, 'notification_messages_new_message', true ) == 'no' ) {
                    $last_notified[ $user_id ][ $thread_id ] = $thread->last_id;
                    continue;
                }


                $ud = get_userdata( $user_id );

                if ( ! isset( $last_notified[ $user_id ][ $thread_id ] ) || ( $thread->last_id > $last_notified[ $user_id ][ $thread_id ] ) ) {

                    $user_last = ( isset( $last_notified[ $user_id ][ $thread_id ] ) ) ? $last_notified[ $user_id ][ $thread_id ] : 0;

                    $query = $wpdb->prepare( "
                        SELECT
                          `messages`.id,
                          `messages`.message,
                          `messages`.sender_id,
                          `messages`.subject,
                          `messages`.date_sent
                        FROM " . bpbm_get_table('messages') . " as messages
                        LEFT JOIN " . bpbm_get_table('meta') . " messagesmeta ON
                        ( messagesmeta.`message_id` = `messages`.`id` AND messagesmeta.meta_key = 'bpbm_call_accepted' )
                        WHERE `messages`.thread_id = %d
                        AND `messages`.id > %d 
                        AND `messages`.sender_id != %d 
                        AND `messages`.sender_id != 0 
                        AND ( messagesmeta.id IS NULL )
                        ORDER BY id DESC
                        LIMIT 0, %d
                    ", $thread->thread_id, $user_last, $user_id, $thread->unread_count );

                    $messages = array_reverse( $wpdb->get_results( $query ) );

                    if ( empty( $messages ) ) {
                        continue;
                    }

                    foreach($messages as $index => $message){
                        if( $message->message ){
                            $is_sticker = strpos( $message->message, '<span class="bpbm-sticker">' ) !== false;
                            if( $is_sticker ){
                                $message->message = __('Sticker', 'bp-better-messages');
                            }

                            $is_gif = strpos( $message->message, '<span class="bpbm-gif">' ) !== false;
                            if( $is_gif ){
                                $message->message = __('GIF', 'bp-better-messages');
                            }
                        }
                    }

                    if ( empty( $messages ) ) {
                        continue;
                    }

                    do_action('wpml_switch_language_for_email', $ud->user_email );

                    $email_overwritten = apply_filters( 'bp_better_messages_overwrite_email', false, $user_id, $thread_id, $messages );

                    if( $email_overwritten === false ) {
                        $messageRaw = '';
                        $messageHtml = '<table style="margin:1rem 0!important;width:100%;table-layout: auto !important;"><tbody>';
                        $last_id = 0;
                        foreach ($messages as $message) {
                            $sender = get_userdata($message->sender_id);
                            if ( ! is_object($sender) ){
                                continue;
                            }

                            $timestamp = strtotime($message->date_sent) + $gmt_offset;
                            $time_format = get_option('time_format');

                            if (gmdate('Ymd') != gmdate('Ymd', $timestamp)) {
                                $time_format .= ' ' . get_option('date_format');
                            }

                            $time    = wp_strip_all_tags(stripslashes(date_i18n($time_format, $timestamp)));
                            $author  = wp_strip_all_tags(stripslashes(sprintf(__('%s wrote:', 'bp-better-messages'), $sender->display_name)));

                            $_message = nl2br(stripslashes($message->message));
                            $_message = str_replace(['<p>', '</p>'], ['<br>', ''], $_message );
                            $_message = htmlspecialchars_decode(BP_Better_Messages()->functions->strip_all_tags($_message, '<br>'));

                            $_message = BP_Better_Messages()->functions->format_message( $_message, $message->id, 'email', $user_id );

                            if ($last_id == 0 || $last_id != $sender->ID) {
                                $messageHtml .= '<tr><td colspan="2"><b>' . $author . '</b></td></tr>';
                                $messageRaw .= "$author\n";
                            }

                            $_message_raw = str_replace("<br>", "\n", $_message );
                            $messageRaw .= "$time\n$_message_raw\n\n";

                            $messageHtml .= '<tr>';
                            $messageHtml .= '<td style="padding-right: 10px;">' . $_message . '</td>';
                            $messageHtml .= '<td style="width:1px;white-space:nowrap;vertical-align:top;text-align:right;text-overflow:ellipsis;overflow:hidden;"><i>' . $time . '</i></td>';
                            $messageHtml .= '</tr>';

                            $last_id = $sender->ID;
                        }

                        $messageHtml .= '</tbody></table>';


                        if( BP_Better_Messages()->settings['disableSubject'] === '1' && $type === 'thread' ) {
                            $subject = '';
                        } else {
                            $subject = BP_Better_Messages()->functions->remove_re(sanitize_text_field(stripslashes($messages[0]->subject)));
                            $subject = BP_Better_Messages()->functions->clean_no_subject($subject);
                        }

                        if (function_exists('bp_send_email')) {
                            $args = array(
                                'tokens' =>
                                    apply_filters('bp_better_messages_notification_tokens', array(
                                        'messages.html' => $messageHtml,
                                        'messages.raw' => $messageRaw,
                                        'sender.name' => $sender->display_name,
                                        'thread.id' => $thread_id,
                                        'thread.url' => esc_url( add_query_arg(['thread_id' => $thread_id ], BP_Better_Messages()->functions->get_link($user_id)) ),
                                        'subject' => $subject,
                                        'unsubscribe' => esc_url(bp_email_get_unsubscribe_link(array(
                                            'user_id' => $user_id,
                                            'notification_type' => 'messages-unread',
                                        )))
                                    ),
                                        $ud, // userdata object of receiver
                                        $sender, // userdata object of sender
                                        $thread_id
                                    ),
                            );

                            bp_send_email('messages-unread-group', $ud, $args);
                        } else {
                            $user = get_userdata($user_id);
                            $thread_url    = esc_url( add_query_arg(['thread_id' => $thread_id ], BP_Better_Messages()->functions->get_link($user_id)) );

                            if( $subject !== '' ) {
                                $email_subject = sprintf(_x('You have unread messages: "%s"', 'Email notification header for non BuddyPress websites', 'bp-better-messages'), $subject);
                            } else {
                                $email_subject = _x('You have unread messages:', 'Email notification header for non BuddyPress websites', 'bp-better-messages');
                            }
                            /**
                             * Composing Email HTML
                             */
                            ob_start(); ?>
                            <!doctype html>
                            <html>
                            <head>
                                <meta name="viewport" content="width=device-width">
                                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                                <title><?php echo $email_subject; ?></title>
                                <style>
                                    /* -------------------------------------
                                        INLINED WITH htmlemail.io/inline
                                    ------------------------------------- */
                                    /* -------------------------------------
                                        RESPONSIVE AND MOBILE FRIENDLY STYLES
                                    ------------------------------------- */
                                    @media only screen and (max-width: 620px) {
                                        table[class=body] h1 {
                                            font-size: 28px !important;
                                            margin-bottom: 10px !important;
                                        }
                                        table[class=body] p,
                                        table[class=body] ul,
                                        table[class=body] ol,
                                        table[class=body] td,
                                        table[class=body] span,
                                        table[class=body] a {
                                            font-size: 16px !important;
                                        }
                                        table[class=body] .wrapper,
                                        table[class=body] .article {
                                            padding: 10px !important;
                                        }
                                        table[class=body] .content {
                                            padding: 0 !important;
                                        }
                                        table[class=body] .container {
                                            padding: 0 !important;
                                            width: 100% !important;
                                        }
                                        table[class=body] .main {
                                            border-left-width: 0 !important;
                                            border-radius: 0 !important;
                                            border-right-width: 0 !important;
                                        }
                                        table[class=body] .btn table {
                                            width: 100% !important;
                                        }
                                        table[class=body] .btn a {
                                            width: 100% !important;
                                        }
                                        table[class=body] .img-responsive {
                                            height: auto !important;
                                            max-width: 100% !important;
                                            width: auto !important;
                                        }
                                    }

                                    /* -------------------------------------
                                        PRESERVE THESE STYLES IN THE HEAD
                                    ------------------------------------- */
                                    @media all {
                                        .ExternalClass {
                                            width: 100%;
                                        }
                                        .ExternalClass,
                                        .ExternalClass p,
                                        .ExternalClass span,
                                        .ExternalClass font,
                                        .ExternalClass td,
                                        .ExternalClass div {
                                            line-height: 100%;
                                        }
                                        .apple-link a {
                                            color: inherit !important;
                                            font-family: inherit !important;
                                            font-size: inherit !important;
                                            font-weight: inherit !important;
                                            line-height: inherit !important;
                                            text-decoration: none !important;
                                        }
                                        #MessageViewBody a {
                                            color: inherit;
                                            text-decoration: none;
                                            font-size: inherit;
                                            font-family: inherit;
                                            font-weight: inherit;
                                            line-height: inherit;
                                        }
                                        .btn-primary table td:hover {
                                            background-color: #34495e !important;
                                        }
                                        .btn-primary a:hover {
                                            background-color: #34495e !important;
                                            border-color: #34495e !important;
                                        }
                                    }
                                </style>
                            </head>
                            <body class="" style="background-color: #f6f6f6; font-family: sans-serif; -webkit-font-smoothing: antialiased; font-size: 14px; line-height: 1.4; margin: 0; padding: 0; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;">
                            <table border="0" cellpadding="0" cellspacing="0" class="body" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; background-color: #f6f6f6;">
                                <tr>
                                    <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;">&nbsp;</td>
                                    <td class="container" style="font-family: sans-serif; font-size: 14px; vertical-align: top; display: block; Margin: 0 auto; max-width: 580px; padding: 10px; width: 580px;">
                                        <div class="content" style="box-sizing: border-box; display: block; Margin: 0 auto; max-width: 580px; padding: 10px;">

                                            <!-- START CENTERED WHITE CONTAINER -->
                                            <table class="main" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; background: #ffffff; border-radius: 3px;">

                                                <!-- START MAIN CONTENT AREA -->
                                                <tr>
                                                    <td class="wrapper" style="font-family: sans-serif; font-size: 14px; vertical-align: top; box-sizing: border-box; padding: 20px;">
                                                        <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;">
                                                            <tr>
                                                                <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;">
                                                                    <p style="font-family: sans-serif; font-size: 16px; font-weight: bold; margin: 0; Margin-bottom: 15px;"><?php echo sprintf(__('Hi %s,', 'bp-better-messages'), $user->display_name); ?></p>
                                                                    <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;"><?php echo $email_subject; ?></p>
                                                                    <?php echo $messageHtml; ?>
                                                                    <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0;Margin-top: 20px;Margin-bottom: 15px;"><?php echo sprintf(__('<a href="%s">Go to the discussion</a> to reply or catch up on the conversation.', 'bp-better-messages'), $thread_url); ?></p>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>

                                                <!-- END MAIN CONTENT AREA -->
                                            </table>

                                            <!-- START FOOTER -->
                                            <div class="footer" style="clear: both; Margin-top: 10px; text-align: center; width: 100%;">
                                                <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;">
                                                    <tr>
                                                        <td class="content-block" style="font-family: sans-serif; vertical-align: top; padding-bottom: 10px; padding-top: 10px; font-size: 12px; color: #999999; text-align: center;">
                                                            <span class="apple-link" style="color: #999999; font-size: 12px; text-align: center;"><a href="<?php echo home_url(); ?>"><?php echo get_bloginfo('name');  ?></a></span>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <!-- END FOOTER -->

                                            <!-- END CENTERED WHITE CONTAINER -->
                                        </div>
                                    </td>
                                    <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;">&nbsp;</td>
                                </tr>
                            </table>
                            </body>
                            </html>
                            <?php
                            $content = ob_get_clean();

                            add_filter( 'wp_mail_content_type', array( $this, 'email_content_type' ) );
                            wp_mail( $user->user_email, $email_subject, $content );
                            remove_filter( 'wp_mail_content_type', array( $this, 'email_content_type' ) );
                        }
                    } else {
                        $last_id = 0;
                        foreach ($messages as $message) {
                            $last_id = $message->sender_id;
                        }
                    }

                    if (function_exists('bp_notifications_add_notification')) {
                        if( BP_Better_Messages()->settings['stopBPNotifications'] === '0' ) {
                            if( empty( $chat_id ) ) {
                                $notification_id = bp_notifications_add_notification(array(
                                    'user_id'           => $user_id,
                                    'item_id'           => $thread->last_id,
                                    'secondary_item_id' => $last_id,
                                    'component_name'    => buddypress()->messages->id,
                                    'component_action'  => 'new_message',
                                    'date_notified'     => bp_core_current_time(),
                                    'is_new'            => 1
                                ));

                                bp_notifications_add_meta($notification_id, 'thread_id', $thread_id);
                            }
                        }
                    }

                    do_action('wpml_restore_language_from_email');

                    $last_notified[ $user_id ][ $thread_id ] = $thread->last_id;
                    update_user_meta( $user_id, 'bp-better-messages-last-notified', $last_notified[ $user_id ] );
                }
            }
        }

        public function email_content_type() {
            return 'text/html';
        }

        public function remove_standard_notification()
        {
            remove_action( 'messages_message_sent', 'messages_notification_new_message', 10 );
            remove_action( 'messages_message_sent', 'bp_messages_message_sent_add_notification', 10 );
        }
    }

endif;

function BP_Better_Messages_Notifications()
{
    return BP_Better_Messages_Notifications::instance();
}
