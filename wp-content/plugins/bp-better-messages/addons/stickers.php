<?php
defined( 'ABSPATH' ) || exit;

if ( !class_exists( 'BP_Better_Messages_Stickers' ) ):

    class BP_Better_Messages_Stickers
    {
        public $api_key;

        public $country;

        public static function instance()
        {

            static $instance = null;

            if ( null === $instance ) {
                $instance = new BP_Better_Messages_Stickers();
            }

            return $instance;
        }


        public function __construct()
        {
            if( ! empty(BP_Better_Messages()->settings['stipopApiKey']) ) {
                $this->api_key = BP_Better_Messages()->settings['stipopApiKey'];
                $this->country = BP_Better_Messages()->settings['stipopLanguage'];

                add_action('bp_messages_after_reply_div', array($this, 'stickers_html'), 10, 1);
                add_action('bp_messages_before_reply_textarea', array($this, 'stickers_button'), 10, 1);

                add_action('wp_ajax_bpbm_messages_send_sticker', array($this, 'send_sticker'));
                add_action('wp_ajax_bpbm_messages_get_sticker_tab', array($this, 'get_sticker_tab'));
                add_action('wp_ajax_bpbm_messages_search_stickers', array($this, 'get_search_stickers'));
            }

            add_filter('bp_better_messages_after_format_message', array($this, 'format_message'), 9, 4);
            add_action('bp_better_chat_settings_updated', array($this, 'check_if_api_key_valid'));
        }

        public function check_if_api_key_valid( $settings ){
            if( ! empty( $settings['stipopApiKey'] ) ){
                $this->api_key = $settings['stipopApiKey'];
                $this->check_api_key();
            }

            global $wpdb;
            $user_ids = $wpdb->get_col("SELECT * FROM {$wpdb->usermeta} WHERE `meta_key` IN ('bpbm_latest_stickers_cache','bpbm_available_packs_cache')");

            foreach( $user_ids as $user_id ){
                delete_user_meta($user_id, 'bpbm_latest_stickers_cache');
                delete_user_meta($user_id, 'bpbm_available_packs_cache');
            }

            delete_option('bpbm_packs_cache');
        }

        public function send_sticker(){
            $thread_id = intval( $_POST[ 'thread_id' ] );
            $errors    = array();

            if ( !wp_verify_nonce( $_POST[ '_wpnonce' ], 'sendMessage_' . $thread_id ) ) {
                $errors[] = __( 'Security error while sending message', 'bp-better-messages' );
            } else {

                $sticker_id  = intval($_POST['sticker_id']);
                $sticker_img = esc_url(strip_tags($_POST['sticker_img']));

                if( strpos( $sticker_img, 'https://img.stipop.io/', 0 ) !== 0 ){
                    return false;
                }

                $message = '<span class="bpbm-sticker"><img src="' . $sticker_img . '" alt=""></span>';

                $args = array(
                    'content'    => $message,
                    'thread_id'  => $thread_id,
                    'error_type' => 'wp_error'
                );

                if( ! apply_filters('bp_better_messages_can_send_message', BP_Better_Messages()->functions->check_access( $thread_id ), get_current_user_id(), $thread_id ) ) {
                    $errors[] = __( 'You can`t reply to this thread.', 'bp-better-messages' );
                }

                do_action_ref_array( 'bp_better_messages_before_message_send', array( &$args, &$errors ));

                if( empty( $errors ) ){
                    remove_action( 'messages_message_sent', 'messages_notification_new_message', 10 );
                    $sent = BP_Better_Messages()->functions->new_message( $args );
                    add_action( 'messages_message_sent', 'messages_notification_new_message', 10 );
                    BP_Better_Messages()->functions->messages_mark_thread_read( $thread_id );

                    if ( is_wp_error( $sent ) ) {
                        $errors[] = $sent->get_error_message();
                    } else {
                        $this->register_usage( get_current_user_id(), $sticker_id );
                    }
                }
            }

            if( ! empty($errors) ) {
                do_action( 'bp_better_messages_on_message_not_sent', $thread_id, $errors );

                $redirect = 'redirect';

                if( count( $errors ) === 1 && isset( $errors['empty'] ) ){
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
        }

        public function get_sticker_tab(){
            $used_id = get_current_user_id();
            $package = sanitize_text_field($_POST['package']);

            if( $package === 'latest' ){
                $stickers = $this->get_user_latest_stickers($used_id);
                echo '<span class="bpbm-stickers-selector-sticker-list">';
                $this->render_sticker( $stickers, 'latest' );
                echo '</span>';
            } else if( $package === 'search' ){
                echo '<span class="bpbm-stickers-search">';
                echo '<input type="text" name="search" value="" placeholder="' . __('Search', 'bp-better-messages') . '">';
                echo '</span>';
                echo '<span class="bpbm-stickers-selector-sticker-list">';
                echo '<span class="bpbm-stickers-selector-empty">' . __('Search results will display here', 'bp-better-messages') . '</span>';
                echo '</span>';
            } else {
                $stickers = $this->get_stickers_from_pack($used_id, $package);
                echo '<span class="bpbm-stickers-selector-sticker-list">';
                $this->render_sticker( $stickers, 'pack' );
                echo '</span>';
            }
            exit;
        }

        public function stickers_button(){
            echo '<span class="bpbm-stickers-btn" title="' . __('Stickers', 'bp-better-messages') . '"><i class="fas fa-sticky-note" aria-hidden="true"></i></span>';
        }

        public function stickers_html( $thread_id ){
        ?><div class="bpbm-stickers-selector" style="display: none">
            <span class="bpbm-stickers-head">
                <?php $sticker_packs = BP_Better_Messages_Stickers()->get_available_packs( get_current_user_id() ); ?>
                <span class="bpbm-stickers-tabs"><?php
                    echo '<span data-package-id="latest" class="bpbm-stickers-tabs-active" title="' . __('Latest', 'bp-better-messages') . '"><i class="fas fa-history"></i></span>';
                    echo '<span data-package-id="search" class="" title="' . __('Search', 'bp-better-messages') . '"><i class="fas fa-search"></i></span>';
                    $i=0; foreach( $sticker_packs as $sticker_pack ){
                        $i++;
                        echo '<span data-package-id="' . $sticker_pack->packageId . '"><img src="' . $sticker_pack->packageImg . '" alt="' . $sticker_pack->packageName . '"></span>';
                    }
                ?></span>
                <span class="bpbm-stickers-close" title="<?php _e('Close', 'bp-better-messages'); ?>"><i class="fas fa-times"></i></span>
            </span>
            <?php
            echo '<span class="bpbm-stickers-selector-sticker-container">';
            $stickers = BP_Better_Messages_Stickers()->get_user_latest_stickers( get_current_user_id() );
            echo '<span class="bpbm-stickers-selector-sticker-list">';
            $this->render_sticker( $stickers, 'latest' );
            echo '</span>';
            echo '</span>';
            ?>
            </div>
            <?php
        }

        function render_sticker( $stickers, $type ){
            if( count( $stickers ) > 0 ){

                foreach( $stickers as $sticker_id => $sticker ){
                    echo '<span class="bpbm-stickers-selector-sticker" data-sticker-id="' . $sticker_id . '">';
                    echo '<img src="' . $sticker->stickerImg . '" alt="">';
                    echo '</span>';
                }
            } else {
                if( $type === 'latest' ){
                    echo '<span class="bpbm-stickers-selector-empty">' . __('You have not used any stickers yet', 'bp-better-messages') . '</span>';
                } else {
                    echo '<span class="bpbm-stickers-selector-empty">' . __('No stickers found', 'bp-better-messages') . '</span>';
                }
            }
        }

        public function format_message( $message, $message_id, $context, $user_id ) {
            $is_sticker = strpos( $message, '<span class="bpbm-sticker">', 0 ) === 0;

            if( $is_sticker ){
                global $processedUrls;
                $sticker_img = bp_messages_get_meta( $message_id, 'bpbm_sticker_img', true );

                global $processedUrls;

                $regex = '/\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|$!:,.;]*[A-Z0-9+&@#\/%=~_|$]/i';
                preg_match_all( $regex, $message, $urls );

                if( ! empty( $urls[0] ) ){
                    $urls[0] = array_unique($urls[0]);
                }

                foreach ( $urls[ 0 ] as $_url ) {
                    $processedUrls[$message_id][] = $_url;
                }

                $desc = '<i class="fas fa-sticky-note"></i> ' . __('Sticker', 'bp-better-messages');
                if( $context !== 'stack' ) {
                    return $desc;
                } else {
                    return str_replace('<span class="bpbm-sticker">', '<span class="bpbm-sticker" data-desc="' . base64_encode($desc) . '">',$message);
                }
            }

            return $message;
        }

        public function get_search_stickers(){
            $user_id = get_current_user_id();
            $search  = sanitize_text_field($_POST['search']);
            $page    = ( isset( $_POST['page'] ) ) ? intval( $_POST['page'] ) : false;

            if( ! $page ){
                $results = $this->search( $user_id, $search );
                echo '<span class="bpbm-stickers-selector-sticker-list" data-pages="' . $results['pages'] . '" data-pages-loaded="1">';
                $this->render_sticker( $results['stickers'], 'search' );
                echo '</span>';
            } else {
                $results = $this->search( $user_id, $search, $page );
                $this->render_sticker( $results['stickers'], 'search' );
            }

            exit;
        }

        public function search( $user_id, $search, $page = 1 ){

            $endpoint = add_query_arg([
                'userId'     => $user_id,
                'pageNumber' => $page,
                'searchText' => $search
            ], 'https://messenger.stipop.io/v1/search');

            $args = array(
                'headers'     => array(
                    'apikey' => $this->api_key,
                ),
            );

            $request = wp_remote_get($endpoint, $args);

            if( is_wp_error( $request ) ){
                return [
                    'stickers' => [],
                    'pages'    => 0
                ];
            }

            $response = json_decode($request['body']);

            $stickers = [];

            foreach( $response->body->stickerList as $sticker ){
                $stickers[ $sticker->stickerId ] = $sticker;
            }

            return [
                'stickers' => $stickers,
                'pages'    => $response->body->pageMap->pageCount
            ];
        }

        public function register_usage( $user_id, $sticker_id ){
            $sticker_id = intval($sticker_id);

            $endpoint = add_query_arg([
                'userId'     => $user_id
            ], 'https://messenger.stipop.io/v1/analytics/send/' . $sticker_id);

            $args = array(
                'blocking' => false,
                'headers'     => array(
                    'apikey' => $this->api_key,
                ),
            );

            wp_remote_post($endpoint, $args);

            $latest_stickers = get_user_meta($user_id, 'bpbm_latest_stickers_cache', true);
            if( !! $latest_stickers && isset($latest_stickers[$sticker_id]) ) {
                //probably sort here later
            } else {
                delete_user_meta($user_id, 'bpbm_latest_stickers_cache');
            }
            return true;
        }

        public function check_api_key(){
            $user_id  = get_current_user_id();
            $endpoint = add_query_arg([], 'https://messenger.stipop.io/v1/package/send/' . $user_id);

            $args = array(
                'headers'     => array(
                    'apikey' => $this->api_key,
                ),
            );

            $request = wp_remote_get($endpoint, $args);

            if( is_wp_error( $request ) ){
                update_option( 'bp_better_messages_stipop_error', 'Stipop Error:' . $request->get_error_message() );
            } else {
                $response = json_decode($request['body']);

                if (isset($response->status) && $response->status === 'fail') {
                    update_option('bp_better_messages_stipop_error', $response->message);
                } else {
                    delete_option('bp_better_messages_stipop_error');
                }
            }
        }

        public function get_user_latest_stickers( $user_id ){
            $latest_stickers = get_user_meta($user_id, 'bpbm_latest_stickers_cache', true);
            if( is_array($latest_stickers) ) return $latest_stickers;

            $endpoint = add_query_arg([], 'https://messenger.stipop.io/v1/package/send/' . $user_id);

            $args = array(
                'timeout'     => 2,
                'headers'     => array(
                    'apikey' => $this->api_key,
                ),
            );

            $request = wp_remote_get($endpoint, $args);

            if( is_wp_error( $request ) ){
                return [];
            }

            $response = json_decode($request['body']);

            $stickers = [];
            foreach( $response->body->stickerList as $sticker ){
                $stickers[ $sticker->stickerId ] = $sticker;
            }

            update_user_meta($user_id, 'bpbm_latest_stickers_cache', $stickers);

            return $stickers;
        }

        public function get_available_packs( $user_id ){
            $available_packs = get_user_meta($user_id, 'bpbm_available_packs_cache', true);
            if( !! $available_packs && count($available_packs ) > 0 ) return $available_packs;

            $endpoint = add_query_arg([
                'userId' => $user_id,
                'pageNumber' => 1,
                'country' => $this->country
            ], 'https://messenger.stipop.io/v1/package');

            $args = array(
                'timeout'     => 2,
                'headers'     => array(
                    'apikey' => $this->api_key,
                ),
            );

            $request = wp_remote_get($endpoint, $args);

            if( is_wp_error( $request ) ){
                return [];
            }

            $response = json_decode($request['body']);

            $packages = [];

            foreach( $response->body->packageList as $package ){
                $packages[ $package->packageId ] = $package;
            }

            update_user_meta($user_id, 'bpbm_available_packs_cache', $packages);
            return $packages;
        }

        public function get_stickers_from_pack( $user_id, $package_id ){
            $available_packs = get_option('bpbm_packs_cache', []);
            if( is_array( $available_packs ) && isset( $available_packs[ $package_id ]) ) {
                return $available_packs[ $package_id ];
            }

            if( ! is_array($available_packs) ){
                $available_packs = array();
            }

            $package_id = intval($package_id);

            $endpoint = add_query_arg([
                'userId' => $user_id
            ], 'https://messenger.stipop.io/v1/package/' . $package_id);

            $args = array(
                'headers'     => array(
                    'apikey' => $this->api_key,
                ),
            );

            $request = wp_remote_get($endpoint, $args);

            if( is_wp_error( $request ) ){
                return [];
            }

            $response = json_decode($request['body']);

            $stickers = [];

            foreach( $response->body->package->stickers as $sticker ){
                $stickers[ $sticker->stickerId ] = $sticker;
            }

            $available_packs[ $package_id ] = $stickers;

            update_option('bpbm_packs_cache', $available_packs);

            return $stickers;
        }
    }

endif;


function BP_Better_Messages_Stickers()
{
    return BP_Better_Messages_Stickers::instance();
}
