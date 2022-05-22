<?php

/*
@wordpress-plugin
Plugin Name: BP Better Messages
Plugin URI: https://www.wordplus.org
Description: Enhanced Private Messages System for BuddyPress and WordPress
Version: 1.9.9.5
Author: WordPlus
Author URI: https://www.wordplus.org
License: GPL2
Text Domain: bp-better-messages
Domain Path: /languages
*/
defined( 'ABSPATH' ) || exit;

if ( !class_exists( 'BP_Better_Messages' ) && !function_exists( 'bpbm_fs' ) ) {
    class BP_Better_Messages
    {
        public  $realtime ;
        public  $version = '1.9.9.5' ;
        public  $path ;
        public  $url ;
        public  $settings ;
        /** @var BP_Better_Messages_Options $functions */
        public  $options ;
        /** @var BP_Better_Messages_Functions $functions */
        public  $functions ;
        /** @var BP_Better_Messages_Ajax $functions */
        public  $ajax ;
        /** @var BP_Better_Messages_Shortcodes $functions */
        public  $shortcodes ;
        /** @var BP_Better_Messages_Stickers $stickers */
        public  $stickers ;
        /** @var BP_Better_Messages_Giphy $giphy */
        public  $giphy ;
        /** @var BP_Better_Messages_Emojies $emoji */
        public  $emoji ;
        /** @var BP_Better_Messages_Urls $urls */
        public  $urls ;
        /** @var BP_Better_Messages_Files $files */
        public  $files ;
        /** @var BP_Better_Messages_Mini_List $mini_list */
        public  $mini_list ;
        /** @var BP_Better_Messages_Chats $chats */
        public  $chats ;
        /** @var BP_Better_Messages_Bulk $bulk */
        public  $bulk ;
        /** @var BP_Better_Messages_Notifications $email */
        public  $email ;
        /** @var BP_Better_Messages_Component $tab */
        public  $tab ;
        /** @var BP_Better_Messages_Hooks $hooks */
        public  $hooks ;
        /** @var BP_Better_Messages_Group $groups */
        public  $groups ;
        /** @var BP_Better_Messages_Mobile_App $functions */
        public  $mobile_app = false ;
        /** @var BP_Better_Messages_Premium $functions */
        public  $premium = false ;
        public static function instance()
        {
            // Store the instance locally to avoid private static replication
            static  $instance = null ;
            // Only run these methods if they haven't been run previously
            
            if ( null === $instance ) {
                $instance = new BP_Better_Messages();
                $instance->load_textDomain();
                $instance->setup_vars();
                $instance->setup_actions();
                $instance->setup_classes();
            }
            
            // Always return the instance
            return $instance;
            // The last metroid is in captivity. The galaxy is at peace.
        }
        
        public function setup_vars()
        {
            global  $wpdb ;
            $wpdb->__set( 'bpbm_threadsmeta', bpbm_get_table( 'threadsmeta' ) );
            if ( !function_exists( 'buddypress' ) ) {
                $wpdb->__set( 'messagemeta', bpbm_get_table( 'meta' ) );
            }
            $this->realtime = false;
            $this->path = plugin_dir_path( __FILE__ );
            $this->url = plugin_dir_url( __FILE__ );
        }
        
        public function setup_actions()
        {
            $this->require_files();
            add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ) );
        }
        
        /**
         * Require necessary files
         */
        public function require_files()
        {
            require_once 'inc/functions.php';
            /**
             * Require component only if BuddyPress is active
             */
            if ( class_exists( 'BP_Component' ) ) {
                require_once 'inc/component.php';
            }
            require_once 'inc/ajax.php';
            require_once 'inc/hooks.php';
            require_once 'inc/options.php';
            require_once 'inc/notifications.php';
            require_once 'inc/bulk.php';
            require_once 'inc/chats.php';
            require_once 'inc/mini-list.php';
            require_once 'inc/shortcodes.php';
            require_once 'addons/urls.php';
            require_once 'addons/files.php';
            require_once 'addons/emojies.php';
            require_once 'addons/stickers.php';
            require_once 'addons/giphy.php';
            if ( function_exists( 'bp_is_active' ) && bp_is_active( 'groups' ) ) {
                require_once 'inc/component-group.php';
            }
            require_once BP_Better_Messages()->path . 'vendor/AES256.php';
        }
        
        public function setup_classes()
        {
            $this->options = BP_Better_Messages_Options();
            $this->functions = BP_Better_Messages_Functions();
            $this->load_options();
            $this->hooks = BP_Better_Messages_Hooks();
            $this->ajax = BP_Better_Messages_Ajax();
            if ( function_exists( 'BP_Better_Messages_Tab' ) ) {
                $this->tab = BP_Better_Messages_Tab();
            }
            if ( $this->settings['replaceStandardEmail'] === '1' ) {
                $this->email = BP_Better_Messages_Notifications();
            }
            $this->bulk = BP_Better_Messages_Bulk();
            $this->chats = BP_Better_Messages_Chats();
            $this->mini_list = BP_Better_Messages_Mini_List();
            if ( $this->settings['attachmentsEnable'] === '1' ) {
                $this->files = BP_Better_Messages_Files();
            }
            if ( $this->settings['searchAllUsers'] === '1' && !defined( 'BP_MESSAGES_AUTOCOMPLETE_ALL' ) ) {
                define( 'BP_MESSAGES_AUTOCOMPLETE_ALL', true );
            }
            $this->urls = BP_Better_Messages_Urls();
            $this->emoji = BP_Better_Messages_Emojies();
            $this->stickers = BP_Better_Messages_Stickers();
            $this->giphy = BP_Better_Messages_Giphy();
            $this->shortcodes = BP_Better_Messages_Shortcodes();
            if ( function_exists( 'BP_Better_Messages_Calls' ) ) {
                if ( $this->settings['videoCalls'] === '1' || $this->settings['audioCalls'] === '1' ) {
                    $this->calls = BP_Better_Messages_Calls();
                }
            }
            if ( function_exists( 'bp_is_active' ) && bp_is_active( 'groups' ) ) {
                $this->groups = BP_Better_Messages_Group();
            }
            if ( function_exists( 'BP_Better_Messages_Mobile_App' ) ) {
                $this->mobile_app = BP_Better_Messages_Mobile_App();
            }
        }
        
        public function load_options()
        {
            $this->settings = $this->options->settings;
            $this->settings = apply_filters( 'bp_better_messages_overwrite_settings', $this->settings );
        }
        
        public function load_textDomain()
        {
            load_plugin_textdomain( 'bp-better-messages', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
        }
        
        public function load_scripts()
        {
            if ( !is_user_logged_in() ) {
                return false;
            }
            $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min' );
            wp_register_script(
                'bpbm-emojionearea-js',
                plugins_url( 'assets/js/emojionearea' . $suffix . '.js', __FILE__ ),
                [],
                $this->version
            );
            wp_register_script(
                'bpbm-taggle-js',
                plugins_url( 'assets/js/taggle.min.js', __FILE__ ),
                [],
                $this->version
            );
            wp_register_script(
                'bpbm-scrollbar-js',
                plugins_url( 'assets/js/overlay-scrollbars' . $suffix . '.js', __FILE__ ),
                [],
                $this->version
            );
            wp_register_script(
                'bpbm-runner-js',
                plugins_url( 'assets/js/jquery.runner' . $suffix . '.js', __FILE__ ),
                [],
                $this->version
            );
            wp_register_script(
                'bpbm-howler-sound-js',
                plugins_url( 'assets/js/howler.min.js', __FILE__ ),
                [],
                $this->version
            );
            wp_register_script(
                'bpbm-amaran-js',
                plugins_url( 'assets/js/jquery.amaran' . $suffix . '.js', __FILE__ ),
                [],
                $this->version
            );
            wp_register_script(
                'bpbm-store-js',
                plugins_url( 'assets/js/store.min.js', __FILE__ ),
                [],
                $this->version
            );
            wp_register_script(
                'bpbm-if-visible',
                plugins_url( 'assets/js/ifvisible.min.js', __FILE__ ),
                [],
                $this->version
            );
            wp_register_script(
                'bpbm-magnific-popup',
                plugins_url( 'assets/js/magnific' . $suffix . '.js', __FILE__ ),
                [],
                $this->version
            );
            wp_register_script(
                'bpbm-medium-editor-js',
                plugins_url( 'assets/js/medium-editor' . $suffix . '.js', __FILE__ ),
                [],
                $this->version
            );
            wp_register_script(
                'bpbm-context-menu-js',
                plugins_url( 'assets/js/jquery.contextMenu' . $suffix . '.js', __FILE__ ),
                [],
                $this->version
            );
            wp_register_script(
                'bpbm-aes-256-js',
                plugins_url( 'assets/js/aes256.min.js', __FILE__ ),
                [],
                $this->version
            );
            wp_register_script(
                'bpbm-touchswipe-js',
                plugins_url( 'assets/js/touchSwipe' . $suffix . '.js', __FILE__ ),
                [],
                $this->version
            );
            wp_register_script(
                'bpbm-popper-js',
                plugins_url( 'assets/js/popper' . $suffix . '.js', __FILE__ ),
                [],
                $this->version
            );
            wp_register_script(
                'bpbm-tippy-js',
                plugins_url( 'assets/js/tippy' . $suffix . '.js', __FILE__ ),
                [ 'bpbm-popper-js' ],
                $this->version
            );
            $dependencies = array(
                'jquery',
                'jquery-ui-autocomplete',
                //'jquery-ui-position',
                //'jquery-ui-tooltip',
                'bpbm-tippy-js',
                'bpbm-emojionearea-js',
                'bpbm-scrollbar-js',
                'bpbm-taggle-js',
                'bpbm-amaran-js',
                'bpbm-store-js',
                'bpbm-if-visible',
                'wp-mediaelement',
                'bpbm-magnific-popup',
                'bpbm-howler-sound-js',
                'bp-livestamp',
                'bpbm-runner-js',
                'bpbm-medium-editor-js',
                'bpbm-context-menu-js',
                'bpbm-touchswipe-js',
            );
            if ( !wp_script_is( 'bp-moment' ) ) {
                wp_register_script(
                    'bp-moment',
                    plugins_url( 'vendor/buddypress/moment-js/moment.js', __FILE__ ),
                    array(),
                    $this->version
                );
            }
            if ( !wp_script_is( 'bp-livestamp' ) ) {
                wp_register_script(
                    'bp-livestamp',
                    plugins_url( 'vendor/buddypress/livestamp.js', __FILE__ ),
                    array( 'jquery', 'bp-moment' ),
                    $this->version
                );
            }
            
            if ( wp_script_is( 'bp-moment-locale' ) ) {
                $dependencies[] = 'bp-moment-locale';
            } else {
                $locale = sanitize_file_name( strtolower( get_locale() ) );
                $locale = str_replace( '_', '-', $locale );
                $locale = apply_filters( 'bp_better_messages_time_locale', $locale );
                
                if ( file_exists( $this->path . "vendor/buddypress/moment-js/locale/{$locale}.min.js" ) ) {
                    $moment_locale_url = "/vendor/buddypress/moment-js/locale/{$locale}.min.js";
                } else {
                    $locale = substr( $locale, 0, strpos( $locale, '-' ) );
                    if ( file_exists( $this->path . "vendor/buddypress/moment-js/locale/{$locale}.min.js" ) ) {
                        $moment_locale_url = "/vendor/buddypress/moment-js/locale/{$locale}.min.js";
                    }
                }
                
                
                if ( isset( $moment_locale_url ) ) {
                    wp_register_script(
                        'bp-moment-locale',
                        plugins_url( $moment_locale_url, __FILE__ ),
                        array( 'bp-moment' ),
                        $this->version
                    );
                    global  $bpbm_locale ;
                    $bpbm_locale = $locale;
                    add_action( 'wp_head', function () {
                        global  $bpbm_locale ;
                        ?>
                        <script id='bp-livestamp-js-after-bpbm'>
                            jQuery(function() {
                                moment.locale( '<?php 
                        echo  $bpbm_locale ;
                        ?>.min' );
                            });
                        </script>
                        <?php 
                    } );
                }
            
            }
            
            $enableSound = '1';
            
            if ( BP_Better_Messages()->settings['allowSoundDisable'] === '1' ) {
                $disabled = get_user_meta( get_current_user_id(), 'bpbm_disable_sound_notification', true ) === 'yes';
                if ( $disabled ) {
                    $enableSound = '0';
                }
            }
            
            $script_variables = array(
                'ajaxUrl'                   => rtrim( admin_url( 'admin-ajax.php' ), '/' ),
                'siteRefresh'               => ( isset( $this->settings['site_interval'] ) ? intval( $this->settings['site_interval'] ) * 1000 : 10000 ),
                'threadRefresh'             => ( isset( $this->settings['thread_interval'] ) ? intval( $this->settings['thread_interval'] ) * 1000 : 3000 ),
                'url'                       => $this->functions->get_link(),
                'threadUrl'                 => $this->functions->get_link( get_current_user_id() ) . '?thread_id=',
                'baseUrl'                   => $this->functions->get_link( get_current_user_id() ),
                'assets'                    => apply_filters( 'bp_better_messages_sounds_assets', plugin_dir_url( __FILE__ ) . 'assets/sounds/' ),
                'user_id'                   => get_current_user_id(),
                'displayed_user_id'         => $this->functions->get_displayed_user_id(),
                'realtime'                  => $this->realtime,
                'total_unread'              => BP_Messages_Thread::get_total_threads_for_user( get_current_user_id(), 'inbox', 'unread' ),
                'max_height'                => apply_filters( 'bp_better_messages_max_height', $this->settings['messagesHeight'] ),
                'fastStart'                 => ( $this->settings['fastStart'] == '1' ? '1' : '0' ),
                'blockScroll'               => ( $this->settings['blockScroll'] == '1' ? '1' : '0' ),
                'disableEnterForTouch'      => ( $this->settings['disableEnterForTouch'] == '1' ? '1' : '0' ),
                'mobileFullScreen'          => ( $this->settings['mobileFullScreen'] == '1' ? '1' : '0' ),
                'enableGroups'              => ( $this->settings['enableGroups'] == '1' ? '1' : '0' ),
                'enableReplies'             => ( $this->settings['enableReplies'] == '1' ? '1' : '0' ),
                'disableEnterForDesktop'    => ( $this->settings['disableEnterForDesktop'] == '1' ? '1' : '0' ),
                'disableTapToOpen'          => ( $this->settings['disableTapToOpen'] == '1' ? '1' : '0' ),
                'autoFullScreen'            => ( $this->settings['autoFullScreen'] == '1' ? '1' : '0' ),
                'miniChats'                 => ( $this->realtime && $this->settings['miniChatsEnable'] ? '1' : '0' ),
                'miniMessages'              => ( $this->realtime && $this->settings['miniThreadsEnable'] ? '1' : '0' ),
                'messagesStatus'            => ( $this->realtime && $this->settings['messagesStatus'] ? '1' : '0' ),
                'userStatus'                => ( $this->realtime && $this->settings['userStatuses'] ? $this->premium->get_user_status( get_current_user_id() ) : '0' ),
                'mobileEmojiEnable'         => ( $this->settings['mobileEmojiEnable'] == '1' ? '1' : '0' ),
                'allowDeleteMessages'       => ( $this->settings['allowDeleteMessages'] == '1' ? '1' : '0' ),
                'allowEditMessages'         => ( $this->settings['allowEditMessages'] == '1' ? '1' : '0' ),
                'combinedView'              => ( $this->settings['combinedView'] == '1' ? '1' : '0' ),
                'allowMuteThreads'          => ( $this->settings['allowMuteThreads'] == '1' ? '1' : '0' ),
                'disableUsersSearch'        => ( $this->settings['disableUsersSearch'] == '1' ? '1' : '0' ),
                'disableFavoriteMessages'   => ( $this->settings['disableFavoriteMessages'] == '1' ? '1' : '0' ),
                'disableOnSiteNotification' => ( $this->settings['disableOnSiteNotification'] == '1' ? '1' : '0' ),
                'titleNotifications'        => ( $this->settings['titleNotifications'] == '1' ? '1' : '0' ),
                'miniWindowsHeight'         => $this->settings['miniWindowsHeight'],
                'miniChatsHeight'           => $this->settings['miniChatsHeight'],
                'fixedHeaderHeight'         => $this->settings['fixedHeaderHeight'],
                'soundLevels'               => array(
                'notification' => $this->settings['notificationSound'] / 100,
                'sent'         => $this->settings['sentSound'] / 100,
                'calling'      => $this->settings['callSound'] / 100,
            ),
                'editNonce'                 => wp_create_nonce( 'bpbm_edit_nonce' ),
                'enableSound'               => $enableSound,
                'forceMobile'               => apply_filters( 'bp_better_messages_force_mobile_view', '0' ),
                'strings'                   => array(
                'writing'                 => __( 'typing...', 'bp-better-messages' ),
                'sent'                    => __( 'Sent', 'bp-better-messages' ),
                'delivered'               => __( 'Delivered', 'bp-better-messages' ),
                'seen'                    => __( 'Seen', 'bp-better-messages' ),
                'new_messages'            => __( 'You have %s new messages', 'bp-better-messages' ),
                'confirm_delete'          => __( 'Are you sure you want to delete this message?', 'bp-better-messages' ),
                'connection_drop'         => __( 'Connection dropped! Most likely the user closed the browser', 'bp-better-messages' ),
                'incoming_call'           => __( 'Incoming Call', 'bp-better-messages' ),
                'call_reject'             => __( 'User rejected your call', 'bp-better-messages' ),
                'call_offline'            => __( 'User is offline at the moment. Try later.', 'bp-better-messages' ),
                'cam_not_works'           => __( 'Webcam not available', 'bp-better-messages' ),
                'mic_not_works'           => __( 'Microphone not available', 'bp-better-messages' ),
                'answer_call'             => __( 'Answer', 'bp-better-messages' ),
                'reject_call'             => __( 'Reject', 'bp-better-messages' ),
                'you_are_in_call'         => __( 'You are currently in call, are you sure you want to leave this page?', 'bp-better-messages' ),
                'no_webrtc'               => __( 'This browser not support video calls feature yet. Please use another browser.', 'bp-better-messages' ),
                'push_request'            => __( 'Enable browser push notifications to receive private messages when you are offline?', 'bp-better-messages' ),
                'enable'                  => __( 'Enable', 'bp-better-messages' ),
                'dismiss'                 => __( 'Dismiss', 'bp-better-messages' ),
                'mute_thread'             => __( 'Are you sure you want to mute this thread?', 'bp-better-messages' ),
                'leave_thread'            => __( 'Are you sure you want to leave this thread?', 'bp-better-messages' ),
                'unmute_thread'           => __( 'Are you sure you want to unmute this thread?', 'bp-better-messages' ),
                'delete_thread'           => __( 'Are you sure you want to delete this thread and all its messages forever? (This action is irreversible)', 'bp-better-messages' ),
                'clear_chat_thread'       => __( 'Are you sure you want to delete all messages in this chat? (This action is irreversible)', 'bp-better-messages' ),
                'exclude'                 => __( 'Exclude %s from this thread?', 'bp-better-messages' ),
                'exclude_chat'            => __( 'Exclude %s from this chat room?', 'bp-better-messages' ),
                'websocket_not_connected' => __( 'Realtime connection not established. Please wait until you will be connected to realtime server and try again', 'bp-better-messages' ),
                'video_call'              => __( 'Video Call', 'bp-better-messages' ),
                'audio_call'              => __( 'Audio Call', 'bp-better-messages' ),
                'maximize'                => __( 'Maximize', 'bp-better-messages' ),
                'close'                   => __( 'Close', 'bp-better-messages' ),
                'edit_message'            => __( 'Edit message', 'bp-better-messages' ),
                'reply'                   => __( 'Reply', 'bp-better-messages' ),
                'edit'                    => __( 'Edit', 'bp-better-messages' ),
                'copy_text'               => __( 'Copy text', 'bp-better-messages' ),
                'delete'                  => __( 'Delete', 'bp-better-messages' ),
                'open'                    => __( 'Open', 'bp-better-messages' ),
            ),
            );
            $script_variables['mutedThreads'] = BP_Better_Messages()->functions->get_user_muted_threads( get_current_user_id() );
            wp_register_script(
                'bp_messages_js',
                plugins_url( 'assets/js/bp-messages' . $suffix . '.js', __FILE__ ),
                $dependencies,
                $this->version
            );
            wp_localize_script( 'bp_messages_js', 'BP_Messages', $script_variables );
            wp_localize_script( 'bpbm-emojionearea-js', 'BP_Messages_Emoji', array(
                'tab'              => __( 'Use the TAB key to insert emoji faster', 'bp-better-messages' ),
                'search'           => __( 'SEARCH', 'bp-better-messages' ),
                'Diversity'        => __( 'Diversity', 'bp-better-messages' ),
                'Recent'           => __( 'Recent', 'bp-better-messages' ),
                'Smileys & People' => __( 'Smileys & People', 'bp-better-messages' ),
                'Animals & Nature' => __( 'Animals & Nature', 'bp-better-messages' ),
                'Food & Drink'     => __( 'Food & Drink', 'bp-better-messages' ),
                'Activity'         => __( 'Activity', 'bp-better-messages' ),
                'Travel & Places'  => __( 'Travel & Places', 'bp-better-messages' ),
                'Objects'          => __( 'Objects', 'bp-better-messages' ),
                'Symbols'          => __( 'Symbols', 'bp-better-messages' ),
                'Flags'            => __( 'Flags', 'bp-better-messages' ),
            ) );
            bp_core_enqueue_livestamp();
            wp_enqueue_script( 'bp_messages_js' );
            #wp_enqueue_style('bp-messages-jquery-ui', 'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css');
            wp_enqueue_style(
                'bp-messages',
                plugins_url( 'assets/css/bp-messages' . $suffix . '.css', __FILE__ ),
                false,
                $this->version
            );
            return true;
        }
    
    }
    function BP_Better_Messages()
    {
        return BP_Better_Messages::instance();
    }
    
    function bpbm_get_table( $table )
    {
        global  $wpdb ;
        $bp_prefix = apply_filters( 'bp_core_get_table_prefix', $wpdb->base_prefix );
        
        if ( function_exists( 'buddypress' ) && bp_is_active( 'messages' ) ) {
            switch ( $table ) {
                case 'messages':
                    return buddypress()->messages->table_name_messages;
                    break;
                case 'meta':
                    return buddypress()->messages->table_name_meta;
                    break;
                case 'recipients':
                    return buddypress()->messages->table_name_recipients;
                    break;
                case 'notifications':
                    
                    if ( isset( buddypress()->notifications ) ) {
                        return buddypress()->notifications->table_name;
                    } else {
                        return false;
                    }
                    
                    break;
                case 'threadsmeta':
                    return $bp_prefix . 'bpbm_threadsmeta';
                    break;
            }
        } else {
            switch ( $table ) {
                case 'messages':
                    return $bp_prefix . 'bp_messages_messages';
                    break;
                case 'meta':
                    return $bp_prefix . 'bp_messages_meta';
                    break;
                case 'recipients':
                    return $bp_prefix . 'bp_messages_recipients';
                    break;
                case 'threadsmeta':
                    return $bp_prefix . 'bpbm_threadsmeta';
                    break;
                case 'notifications':
                    return false;
                    break;
            }
        }
        
        return false;
    }
    
    function BP_Better_Messages_Init()
    {
        
        if ( class_exists( 'BuddyPress' ) && bp_is_active( 'messages' ) ) {
            BP_Better_Messages();
        } else {
            
            if ( isset( $_GET['plugin'] ) && isset( $_GET['action'] ) && strpos( $_GET['plugin'], 'bp-loader.php' ) !== false && $_GET['action'] == 'activate' ) {
                BP_Better_Messages();
            } else {
                require_once 'vendor/buddypress/functions.php';
                require_once 'vendor/buddypress/class-bp-messages-thread.php';
                require_once 'vendor/buddypress/class-bp-messages-message.php';
                require_once 'vendor/buddypress/class-bp-user-query.php';
                require_once 'vendor/buddypress/class-bp-suggestions.php';
                require_once 'vendor/buddypress/class-bp-members-suggestions.php';
                BP_Better_Messages();
            }
        
        }
    
    }
    
    add_action( 'plugins_loaded', 'BP_Better_Messages_Init', 20 );
    require_once 'inc/install.php';
    register_activation_hook( __FILE__, 'bp_better_messages_activation' );
    register_deactivation_hook( __FILE__, 'bp_better_messages_deactivation' );
    
    if ( !function_exists( 'bpbm_fs' ) ) {
        // Create a helper function for easy SDK access.
        function bpbm_fs()
        {
            global  $bbm_fs ;
            
            if ( !isset( $bbm_fs ) ) {
                if ( !defined( 'WP_FS__PRODUCT_1557_MULTISITE' ) ) {
                    define( 'WP_FS__PRODUCT_1557_MULTISITE', true );
                }
                // Include Freemius SDK.
                require_once dirname( __FILE__ ) . '/inc/freemius/start.php';
                $bbm_fs = fs_dynamic_init( array(
                    'id'             => '1557',
                    'slug'           => 'bp-better-messages',
                    'type'           => 'plugin',
                    'public_key'     => 'pk_8af54172153e9907893f32a4706e2',
                    'is_premium'     => false,
                    'has_addons'     => true,
                    'has_paid_plans' => true,
                    'trial'          => array(
                    'days'               => 3,
                    'is_require_payment' => true,
                ),
                    'menu'           => array(
                    'slug'    => 'bp-better-messages',
                    'support' => false,
                ),
                    'is_live'        => true,
                ) );
            }
            
            return $bbm_fs;
        }
        
        // Init Freemius.
        bpbm_fs();
        // Signal that SDK was initiated.
        do_action( 'bbm_fs_loaded' );
    }

}
