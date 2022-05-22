<?php
defined( 'ABSPATH' ) || exit;
class BP_Better_Messages_Options
{
    protected  $path ;
    public  $settings ;
    public static function instance()
    {
        static  $instance = null ;

        if ( null === $instance ) {
            $instance = new BP_Better_Messages_Options();
            $instance->setup_globals();
            $instance->setup_actions();
        }

        return $instance;
    }

    public function setup_globals()
    {
        $this->path = BP_Better_Messages()->path . '/views/';
        $defaults = array(
            'mechanism'                 => 'ajax',
            'template'                  => 'standard',
            'thread_interval'           => 3,
            'site_interval'             => 10,
            'messagesPerPage'           => 20,
            'attachmentsFormats'        => array(),
            'attachmentsRetention'      => 365,
            'attachmentsEnable'         => '0',
            'attachmentsHide'           => '1',
            'attachmentsRandomName'     => '1',
            'attachmentsMaxSize'        => wp_max_upload_size() / 1024 / 1024,
            'miniChatsEnable'           => '0',
            'searchAllUsers'            => '0',
            'disableSubject'            => '0',
            'disableEnterForTouch'      => '1',
            'disableTapToOpen'          => '0',
            'autoFullScreen'            => '0',
            'mobilePopup'               => '0',
            'mobileFullScreen'          => '1',
            'chatPage'                  => '0',
            'messagesStatus'            => '0',
            'allowDeleteMessages'       => '0',
            'disableDeleteThreadCheck'  => '0',
            'fastStart'                 => '1',
            'miniThreadsEnable'         => '0',
            'miniFriendsEnable'         => '0',
            'friendsMode'               => '0',
            'singleThreadMode'          => '0',
            'redirectToExistingThread'  => '0',
            'disableGroupThreads'       => '0',
            'replaceStandardEmail'      => '1',
            'oEmbedEnable'              => '1',
            'disableEnterForDesktop'    => '0',
            'rateLimitReply'            => [],
            'rateLimitReplyMessage'     => __( 'Your limit for replies is exceeded', 'bp-better-messages' ),
            'restrictNewThreads'        => [],
            'restrictNewThreadsMessage' => __( 'You are not allowed to start new threads', 'bp-better-messages' ),
            'restrictNewThreadsRemoveNewThreadButton' => '0',
            'restrictNewReplies'        => [],
            'restrictNewRepliesMessage' => __( 'You are not allowed to reply', 'bp-better-messages' ),
            'restrictViewMessages'        => [],
            'restrictViewMessagesMessage' => __( 'Message hidden', 'bp-better-messages' ),
            'videoCalls'                => '0',
            'audioCalls'                => '0',
            'blockScroll'               => '1',
            'userListButton'            => '0',
            'combinedView'              => '0',
            'enablePushNotifications'   => '0',
            'colorGeneral'              => '#21759b',
            'mobileEmojiEnable'         => '0',
            'encryptionEnabled'         => '1',
            'stipopApiKey'              => '',
            'stipopLanguage'            => 'en',
            'allowMuteThreads'          => '1',
            'callsRevertIcons'          => '0',
            'callRequestTimeLimit'      => '30',
            'offlineCallsNotifications' => '0',
            'callsLimitFriends'         => '0',
            'stopBPNotifications'       => '0',
            'restrictThreadsDeleting'   => '0',
            'disableFavoriteMessages'   => '0',
            'disableSearch'             => '0',
            'disableUserSettings'       => '0',
            'disableNewThread'          => '0',
            'profileVideoCall'          => '0',
            'profileAudioCall'          => '0',
            'miniChatAudioCall'         => '0',
            'miniChatVideoCall'         => '0',
            'disableUsersSearch'        => '0',
            'fixedHeaderHeight'         => '0',
            'miniWindowsHeight'         => 426,
            'miniChatsHeight'           => 426,
            'rateLimitNewThread'        => 0,
            'notificationsInterval'     => 15,
            'disableOnSiteNotification' => '0',
            'allowSoundDisable'         => '1',
            'enableGroups'              => '0',
            'enableMiniGroups'          => '0',
            'allowGroupLeave'           => '0',
            'giphyApiKey'               => '',
            'giphyContentRating'        => 'g',
            'giphyLanguage'             => 'en',
            'enableReplies'             => '0',
            'messagesHeight'            => 650,
            'notificationSound'         => 100,
            'sentSound'                 => 50,
            'callSound'                 => 100,
            'modernLayout'              => 'left',
            'allowEditMessages'         => '0',
            'enableNiceLinks'           => '1',
            'modernBorderRadius'        => 2,
            'userStatuses'              => '0',
            'myProfileButton'           => '1',
            'titleNotifications'        => '1',
            'enableMiniCloseButton'     => '0',
            'compatibilityMode'         => '0',
            'bpProfileSlug'             => 'bp-messages'
        );

        $args = get_option( 'bp-better-chat-settings', array() );

        if ( ! bpbm_fs()->can_use_premium_code() || ! bpbm_fs()->is_premium() ) {
            $args['mechanism'] = 'ajax';
            $args['miniChatsEnable'] = '0';
            $args['messagesStatus'] = '0';
            $args['miniThreadsEnable'] = '0';
            $args['videoCalls'] = '0';
            $args['audioCalls'] = '0';
            $args['encryptionEnabled'] = '0';
            $args['userStatuses'] = '0';
        }

        if( bpbm_fs()->can_use_premium_code() && bpbm_fs()->is_premium() ){
            $args['mechanism'] = 'websocket';
        }

        if ( ! function_exists('bp_send_email') ) {
            $args['replaceStandardEmail'] = '1';
        }

        $this->settings = wp_parse_args( $args, $defaults );
    }

    public function setup_actions()
    {
        add_action( 'admin_menu', array( $this, 'settings_page' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'add_color_picker' ) );
    }

    /**
     * Settings page
     */
    public function settings_page()
    {
        add_menu_page(
            __( 'BP Better Messages' ),
            __( 'Better Messages', 'bp-better-messages' ),
            'manage_options',
            'bp-better-messages',
            array( $this, 'settings_page_html' ),
            'dashicons-format-chat'
        );

        add_submenu_page(
            'bp-better-messages',
            __( 'Better Messages' ),
            __( 'Settings', 'bp-better-messages' ),
            'manage_options',
            'bp-better-messages',
            array( $this, 'settings_page_html' ),
            0
        );
    }

    public function add_color_picker( $hook )
    {

        if ( $hook === 'toplevel_page_bp-better-messages' && is_admin() ) {
            wp_enqueue_style( 'wp-color-picker' );
            wp_enqueue_script( 'wp-color-picker' );
        }

    }

    public function settings_page_html()
    {
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'wp-color-picker' );

        if ( isset( $_POST['_wpnonce'] ) && !empty($_POST['_wpnonce']) && wp_verify_nonce( $_POST['_wpnonce'], 'bp-better-messages-settings' ) ) {
            unset( $_POST['_wpnonce'], $_POST['_wp_http_referer'] );

            if ( isset( $_POST['save'] ) ) {
                unset( $_POST['save'] );
                $this->update_settings( $_POST );
            }

        }

        include $this->path . 'layout-settings.php';
    }

    public function update_settings( $settings )
    {
        if ( !isset( $settings['attachmentsEnable'] ) ) {
            $settings['attachmentsEnable'] = '0';
        }
        if ( !isset( $settings['attachmentsHide'] ) ) {
            $settings['attachmentsHide'] = '0';
        }
        if ( !isset( $settings['attachmentsRandomName'] ) ) {
            $settings['attachmentsRandomName'] = '0';
        }
        if ( !isset( $settings['miniChatsEnable'] ) ) {
            $settings['miniChatsEnable'] = '0';
        }
        if ( !isset( $settings['searchAllUsers'] ) ) {
            $settings['searchAllUsers'] = '0';
        }
        if ( !isset( $settings['disableSubject'] ) ) {
            $settings['disableSubject'] = '0';
        }
        if ( !isset( $settings['disableEnterForTouch'] ) ) {
            $settings['disableEnterForTouch'] = '0';
        }
        if ( !isset( $settings['disableTapToOpen'] ) ) {
            $settings['disableTapToOpen'] = '0';
        }
        if ( !isset( $settings['mobileFullScreen'] ) ) {
            $settings['mobileFullScreen'] = '0';
        }
        if ( !isset( $settings['messagesStatus'] ) ) {
            $settings['messagesStatus'] = '0';
        }
        if ( !isset( $settings['allowDeleteMessages'] ) ) {
            $settings['allowDeleteMessages'] = '0';
        }
        if ( !isset( $settings['fastStart'] ) ) {
            $settings['fastStart'] = '0';
        }
        if ( !isset( $settings['miniFriendsEnable'] ) ) {
            $settings['miniFriendsEnable'] = '0';
        }
        if ( !isset( $settings['miniThreadsEnable'] ) ) {
            $settings['miniThreadsEnable'] = '0';
        }
        if ( !isset( $settings['friendsMode'] ) ) {
            $settings['friendsMode'] = '0';
        }
        if ( !isset( $settings['singleThreadMode'] ) ) {
            $settings['singleThreadMode'] = '0';
        }
        if ( !isset( $settings['redirectToExistingThread'] ) ) {
            $settings['redirectToExistingThread'] = '0';
        }
        if ( !isset( $settings['disableGroupThreads'] ) ) {
            $settings['disableGroupThreads'] = '0';
        }
        if ( !isset( $settings['replaceStandardEmail'] ) ) {
            $settings['replaceStandardEmail'] = '0';
        }
        if ( !isset( $settings['mobilePopup'] ) ) {
            $settings['mobilePopup'] = '0';
        }
        if ( !isset( $settings['autoFullScreen'] ) ) {
            $settings['autoFullScreen'] = '0';
        }
        if ( !isset( $settings['disableDeleteThreadCheck'] ) ) {
            $settings['disableDeleteThreadCheck'] = '0';
        }
        if ( !isset( $settings['oEmbedEnable'] ) ) {
            $settings['oEmbedEnable'] = '0';
        }
        if ( !isset( $settings['disableEnterForDesktop'] ) ) {
            $settings['disableEnterForDesktop'] = '0';
        }
        if ( !isset( $settings['restrictNewThreads'] ) ) {
            $settings['restrictNewThreads'] = [];
        }
        if ( !isset( $settings['restrictNewReplies'] ) ) {
            $settings['restrictNewReplies'] = [];
        }
        if ( !isset( $settings['restrictNewThreads'] ) ) {
            $settings['restrictNewThreads'] = [];
        }
        if ( !isset( $settings['restrictViewMessages'] ) ) {
            $settings['restrictViewMessages'] = [];
        }
        if ( !isset( $settings['videoCalls'] ) ) {
            $settings['videoCalls'] = '0';
        }
        if ( !isset( $settings['audioCalls'] ) ) {
            $settings['audioCalls'] = '0';
        }
        if ( !isset( $settings['blockScroll'] ) ) {
            $settings['blockScroll'] = '0';
        }
        if ( !isset( $settings['userListButton'] ) ) {
            $settings['userListButton'] = '0';
        }
        if ( !isset( $settings['combinedView'] ) ) {
            $settings['combinedView'] = '0';
        }
        if ( !isset( $settings['enablePushNotifications'] ) ) {
            $settings['enablePushNotifications'] = '0';
        }
        if ( !isset( $settings['mobileEmojiEnable'] ) ) {
            $settings['mobileEmojiEnable'] = '0';
        }
        if ( !isset( $settings['encryptionEnabled'] ) ) {
            $settings['encryptionEnabled'] = '0';
        }
        if ( !isset( $settings['allowMuteThreads'] ) ) {
            $settings['allowMuteThreads'] = '0';
        }
        if ( !isset( $settings['callsRevertIcons'] ) ) {
            $settings['callsRevertIcons'] = '0';
        }
        if ( !isset( $settings['callRequestTimeLimit'] ) ) {
            $settings['callRequestTimeLimit'] = '30';
        }
        if ( !isset( $settings['fixedHeaderHeight'] ) ) {
            $settings['fixedHeaderHeight'] = '0';
        }
        if ( !isset( $settings['offlineCallsNotifications'] ) ) {
            $settings['offlineCallsNotifications'] = '0';
        }
        if ( !isset( $settings['callsLimitFriends'] ) ) {
            $settings['callsLimitFriends'] = '0';
        }
        if ( !isset( $settings['stopBPNotifications'] ) ) {
            $settings['stopBPNotifications'] = '0';
        }
        if ( !isset( $settings['restrictThreadsDeleting'] ) ) {
            $settings['restrictThreadsDeleting'] = '0';
        }
        if ( !isset( $settings['disableFavoriteMessages'] ) ) {
            $settings['disableFavoriteMessages'] = '0';
        }
        if ( !isset( $settings['disableSearch'] ) ) {
            $settings['disableSearch'] = '0';
        }
        if ( !isset( $settings['disableUserSettings'] ) ) {
            $settings['disableUserSettings'] = '0';
        }
        if ( !isset( $settings['disableNewThread'] ) ) {
            $settings['disableNewThread'] = '0';
        }
        if ( !isset( $settings['profileVideoCall'] ) ) {
            $settings['profileVideoCall'] = '0';
        }
        if ( !isset( $settings['profileAudioCall'] ) ) {
            $settings['profileAudioCall'] = '0';
        }
        if ( !isset( $settings['miniChatAudioCall'] ) ) {
            $settings['miniChatAudioCall'] = '0';
        }
        if ( !isset( $settings['miniChatVideoCall'] ) ) {
            $settings['miniChatVideoCall'] = '0';
        }
        if ( !isset( $settings['disableUsersSearch'] ) ) {
            $settings['disableUsersSearch'] = '0';
        }
        if ( !isset( $settings['disableOnSiteNotification'] ) ) {
            $settings['disableOnSiteNotification'] = '0';
        }
        if ( !isset( $settings['allowSoundDisable'] ) ) {
            $settings['allowSoundDisable'] = '0';
        }

        if ( !isset( $settings['enableGroups'] ) ) {
            $settings['enableGroups'] = '0';
        }

        if ( !isset( $settings['enableMiniGroups'] ) ) {
            $settings['enableMiniGroups'] = '0';
        }

        if ( !isset( $settings['allowGroupLeave'] ) ) {
            $settings['allowGroupLeave'] = '0';
        }

        if ( !isset( $settings['enableReplies'] ) ) {
            $settings['enableReplies'] = '0';
        }

        if ( !isset( $settings['allowEditMessages'] ) ) {
            $settings['allowEditMessages'] = '0';
        }

        if ( !isset( $settings['enableNiceLinks'] ) ) {
            $settings['enableNiceLinks'] = '0';
        }

        if ( !isset( $settings['userStatuses'] ) ) {
            $settings['userStatuses'] = '0';
        }

        if ( !isset( $settings['myProfileButton'] ) ) {
            $settings['myProfileButton'] = '0';
        }

        if ( !isset( $settings['titleNotifications'] ) ) {
            $settings['titleNotifications'] = '0';
        }

        if ( !isset( $settings['restrictNewThreadsRemoveNewThreadButton'] ) ) {
            $settings['restrictNewThreadsRemoveNewThreadButton'] = '0';
        }

        if ( !isset( $settings['enableMiniCloseButton'] ) ) {
            $settings['enableMiniCloseButton'] = '0';
        }

        if ( !isset( $settings['compatibilityMode'] ) ) {
            $settings['compatibilityMode'] = '0';
        }

        $links_allowed = [ 'restrictNewThreadsMessage', 'restrictNewRepliesMessage', 'restrictViewMessagesMessage', 'rateLimitReplyMessage' ];

        $int_only = [
            'callRequestTimeLimit' => 10,
            'fixedHeaderHeight'    => 0,
            'messagesHeight'       => 200,
            'miniWindowsHeight'    => 300,
            'miniChatsHeight'      => 300,
            'rateLimitNewThread'   => 0,
            'notificationsInterval' => 0,
            'notificationSound'    => 0,
            'sentSound'            => 0,
            'callSound'            => 0,
            'modernBorderRadius'   => 0
        ];

        $arrays = [
            'rateLimitReply'
        ];

        foreach ( $settings as $key => $value ) {
            /** Processing checkbox groups **/

            if( in_array( $key, $arrays ) ){
                $this->settings[$key] = (array) $value;
            } else if ( is_array( $value ) ) {
                $this->settings[$key] = array();
                foreach ( $value as $val ) {
                    $this->settings[$key][] = sanitize_text_field( $val );
                }
            } else {
                if ( in_array( $key, $links_allowed ) ) {
                    $this->settings[$key] = wp_kses( $value, 'user_description' );
                } else {
                    $this->settings[$key] = sanitize_text_field( $value );

                    if ( array_key_exists( $key, $int_only ) ) {
                        $intval = intval( $value );
                        if ( $intval <= $int_only[$key] ) {
                            $intval = $int_only[$key];
                        }
                        $this->settings[$key] = $intval;
                    }

                }

            }
        }

        $this->settings['bpProfileSlug'] = preg_replace('/\s+/', '', trim( $this->settings['bpProfileSlug'] ) );

        if ( ! isset( $this->settings['bpProfileSlug'] ) || empty( $this->settings['bpProfileSlug'] ) || $this->settings['bpProfileSlug'] === 'messages' ) {
            $this->settings['bpProfileSlug'] = 'bp-messages';
        }

        wp_unschedule_hook('bp_better_messages_send_notifications');
        flush_rewrite_rules();

        update_option( 'bp-better-chat-settings', $this->settings );
        do_action( 'bp_better_chat_settings_updated', $this->settings );
    }

}
function BP_Better_Messages_Options()
{
    return BP_Better_Messages_Options::instance();
}
