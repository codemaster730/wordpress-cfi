<?php
defined( 'ABSPATH' ) || exit;

if ( !class_exists( 'BP_Better_Messages_Giphy' ) ):

    class BP_Better_Messages_Giphy
    {
        public $api_key;

        public $content_rating;

        public $lang;

        public static function instance()
        {

            static $instance = null;

            if ( null === $instance ) {
                $instance = new BP_Better_Messages_Giphy();
            }

            return $instance;
        }


        public function __construct()
        {
            if( ! empty(BP_Better_Messages()->settings['giphyApiKey']) ) {
                $this->api_key        = BP_Better_Messages()->settings['giphyApiKey'];
                $this->content_rating = BP_Better_Messages()->settings['giphyContentRating'];
                $this->lang           = BP_Better_Messages()->settings['giphyLanguage'];

                add_action('bp_messages_after_reply_div', array($this, 'gifs_html'), 10, 1);
                add_action('bp_messages_before_reply_textarea', array($this, 'gifs_button'), 10, 1);

                add_action('wp_ajax_bpbm_messages_send_gif', array($this, 'send_gif'));
                add_action('wp_ajax_bpbm_messages_get_gif_tab', array($this, 'get_gif_tab'));
                add_action('wp_ajax_bpbm_messages_search_gifs', array($this, 'get_search_gifs'));

                add_filter('bp_better_messages_pre_format_message', array($this, 'format_message'), 9, 4);
                add_filter('bp_better_messages_after_format_message', array($this, 'after_format_message'), 9, 4);
                add_action('bp_better_chat_settings_updated', array($this, 'check_if_api_key_valid'));
            }
        }

        public function send_gif(){
            $thread_id = intval( $_POST[ 'thread_id' ] );
            $errors    = array();

            if ( !wp_verify_nonce( $_POST[ '_wpnonce' ], 'sendMessage_' . $thread_id ) ) {
                $errors[] = __( 'Security error while sending message', 'bp-better-messages' );
            } else {

                $gif_id  = sanitize_text_field($_POST['gif_id']);

                $gif = $this->get_gif( $gif_id, get_current_user_id() );

                if( ! $gif ) return false;

                $gif_mp4 = esc_url($gif->images->original_mp4->mp4);
                $poster  = esc_url($gif->images->{"480w_still"}->url);

                $message  = '<span class="bpbm-gif">';
                $message .= '<video preload="auto" muted playsinline="playsinline" loop="loop" poster="' . $poster . '">';
                $message .= '<source src="' . $gif_mp4 . '" type="video/mp4">';
                $message .= '</video>';
                $message .= '</span>';

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
                    remove_filter( 'messages_message_content_before_save', 'bp_messages_filter_kses', 1 );
                    remove_action( 'messages_message_sent', 'messages_notification_new_message', 10 );
                    $sent = BP_Better_Messages()->functions->new_message( $args );
                    add_action( 'messages_message_sent', 'messages_notification_new_message', 10 );
                    BP_Better_Messages()->functions->messages_mark_thread_read( $thread_id );
                    add_filter( 'messages_message_content_before_save', 'bp_messages_filter_kses', 1 );

                    if ( is_wp_error( $sent ) ) {
                        $errors[] = $sent->get_error_message();
                    } else {
                        //$this->register_usage( get_current_user_id(), $gif_id );
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

        public function get_gif( $gif_id, $user_id ){
            $user_id = $this->get_random_id( $user_id );

            $offset = 0;

            $endpoint = add_query_arg([
                'api_key'   => $this->api_key,
                'gif_id'    => $gif_id,
                'random_id' => $user_id,
            ], 'https://api.giphy.com/v1/gifs/' . $gif_id);

            $args = array(
                'timeout'     => 2,
            );

            $request = wp_remote_get($endpoint, $args);

            if( is_wp_error( $request ) ){
                return [];
            }

            $response = json_decode($request['body']);

            if( isset( $response->data->id ) ) {
                return $response->data;
            } else {
                return false;
            }
        }

        public function get_random_id($user_id){
            $random_id = get_user_meta($user_id, 'bpbm_giphy_random_id', true);
            if( !! $random_id ) return $random_id;

            $endpoint = add_query_arg([
                'api_key' => $this->api_key
            ], 'https://api.giphy.com/v1/randomid');

            $args = array(
                'timeout'     => 2,
            );

            $request = wp_remote_get($endpoint, $args);

            if( is_wp_error( $request ) ){
                return [];
            }

            $response = json_decode($request['body']);

            $unique_id = $response->data->random_id;

            update_user_meta($user_id, 'bpbm_giphy_random_id', $unique_id);

            return $unique_id;
        }

        public function get_trending_gifs( $user_id, $page = 1 ){

            $user_id = $this->get_random_id( $user_id );

            if( $page <= 1 ) {
                $offset = 0;
            } else {
                $offset = ($page * 20) - 20;
            }

            $endpoint = add_query_arg([
                'api_key' => $this->api_key,
                'limit'       => 20,
                'rating'      => $this->content_rating,
                'random_id'   => $user_id,
                'offset'      => $offset
            ], 'https://api.giphy.com/v1/gifs/trending');

            $args = array(
                'timeout'     => 2,
            );

            $request = wp_remote_get($endpoint, $args);

            if( is_wp_error( $request ) ){
                return [];
            }

            $response = json_decode($request['body']);

            $return = [
                'pagination' => $response->pagination,
                'gifs'       => []
            ];

            $gifs = $response->data;

            if( count($gifs ) > 0 ){
                foreach ( $gifs as $gif ){
                    $return['gifs'][] = (array) $gif;
                }
            }

            #update_user_meta($user_id, 'bpbm_latest_stickers_cache', $stickers);

            return $return;
        }

        public function get_content_ratings(){

        }


        public function check_if_api_key_valid( $settings ){
            if( ! empty( $settings['giphyApiKey'] ) ){
                $this->api_key = $settings['giphyApiKey'];
                $this->check_api_key();
            }

            /*global $wpdb;
            $user_ids = $wpdb->get_col("SELECT * FROM {$wpdb->usermeta} WHERE `meta_key` IN ('bpbm_latest_stickers_cache','bpbm_available_packs_cache')");

            foreach( $user_ids as $user_id ){
                delete_user_meta($user_id, 'bpbm_latest_stickers_cache');
                delete_user_meta($user_id, 'bpbm_available_packs_cache');
            }

            delete_option('bpbm_packs_cache');*/
        }


        public function get_gif_tab(){
            $used_id = get_current_user_id();
            $package = sanitize_text_field($_POST['package']);
            $page    = intval( $_POST['page'] );
            if( $package === 'trending' ){
                if( $page !== 0 ){
                    $trending_gifs = $this->get_trending_gifs(get_current_user_id(), $page);
                    $this->render_gif($trending_gifs['gifs'], 'latest');
                } else {
                    $trending_gifs = $this->get_trending_gifs(get_current_user_id());
                    $pages = ceil($trending_gifs['pagination']->total_count / $trending_gifs['pagination']->count);
                    echo '<div class="bpbm-gifs-selector-gif-list" data-pages="' . $pages . '" data-pages-loaded="1">';
                    $this->render_gif($trending_gifs['gifs'], 'latest');
                }
                echo '</div>';
            } else if( $package === 'search' ){
                echo '<div class="bpbm-gifs-selector-gif-list empty">';
                echo '<div class="bpbm-gifs-selector-empty">' . __('Search results will display here', 'bp-better-messages') . '</div>';
                echo '</div>';
            } else {
                echo '<div class="bpbm-gifs-selector-gif-list">';
                echo '</div>';
            }
            exit;
        }

        public function gifs_button(){
            echo '<span class="bpbm-gifs-btn" title="' . __('GIFs', 'bp-better-messages') . '"><i class="bpbm-gifs-icon"></i></span>';
        }

        public function gifs_html( $thread_id ){
        ?><div class="bpbm-gifs-selector" >
            <span class="bpbm-gifs-head">
                <span class="bpbm-gifs-tabs"><?php
                    #echo '<span data-package-id="latest" class="bpbm-gifs-tabs-active" class=""  title="' . __('Latest', 'bp-better-messages') . '"><i class="fas fa-history"></i></span>';
                    echo '<span data-package-id="trending" title="' . __('Trending', 'bp-better-messages') . '"><i class="fas fa-signal"></i></span>';
                    echo '<span data-package-id="search" class=""><input type="text" name="search" placeholder="' . __('Search', 'bp-better-messages') . '"></span>';
                ?></span>
                <span class="bpbm-gifs-close" title="<?php _e('Close', 'bp-better-messages'); ?>"><i class="fas fa-times"></i></span>
            </span>
            <?php
            echo '<div class="bpbm-gifs-selector-gif-container">';
            echo '<div class="bpbm-gifs-selector-gif-list">';
            echo '</div>';
            echo '</div>';
            ?>
            </div>
            <?php
        }

        function render_gif( $gifs, $type ){
            if( count( $gifs ) > 0 ){
                foreach( $gifs as $gif ){
                    echo '<div class="bpbm-gifs-selector-gif" data-gif-id="' . $gif['id'] . '">';
                    echo '<img src="' . $gif['images']->fixed_width->url . '" alt="">';
                    echo '</div>';
                }
            }
        }

        public function format_message( $message, $message_id, $context, $user_id ) {
            $is_gif = strpos( $message, '<span class="bpbm-gif">', 0 ) === 0;

            if( $is_gif ){
                if( $context !== 'stack' ) {
                    return '%bpbmgif%';
                }
            }
            return $message;
        }

        public function after_format_message( $message, $message_id, $context, $user_id ){
            $is_gif = strpos( $message, '<span class="bpbm-gif">', 0 ) === 0 || $message === '%bpbmgif%';

            if( $is_gif ){
                $desc = '<i class="bpbm-gifs-icon" title="' . __('GIF', 'bp-better-messages') . '"></i>';
                if( $context !== 'stack' ) {
                    return $desc;
                } else {
                    return str_replace('<span class="bpbm-gif">', '<span class="bpbm-gif" title="' . __('GIF', 'bp-better-messages') . '" data-desc="' . base64_encode($desc) . '"><span class="bpbm-gif-play"><i class="far fa-play-circle"></i></span>', $message);
                }
            }
            return $message;
        }

        public function get_search_gifs(){
            $user_id = get_current_user_id();
            $search  = sanitize_text_field($_POST['search']);
            $page    = ( isset( $_POST['page'] ) ) ? intval( $_POST['page'] ) : false;

            if( ! $page ){;
                $results = $this->search( $user_id, $search );
                if($results['pagination']->total_count === 0){
                    echo '<div class="bpbm-gifs-selector-gif-list empty" data-pages="' . 0 . '" data-pages-loaded="0">';
                    echo '<div class="bpbm-gifs-selector-empty">' . __('Search results will display here', 'bp-better-messages') . '</div>';
                    echo '</div>';

                } else {
                    $pages = ceil($results['pagination']->total_count / $results['pagination']->count);
                    echo '<div class="bpbm-gifs-selector-gif-list" data-pages="' . $pages . '" data-pages-loaded="1">';
                    $this->render_gif($results['gifs'], 'search');
                    echo '</div>';
                }
            } else {
                $results = $this->search( $user_id, $search, $page );
                $this->render_gif( $results['gifs'], 'search' );
            }

            exit;
        }

        public function search( $user_id, $search, $page = 1 ){

            $user_id = $this->get_random_id( $user_id );

            if( $page <= 1 ) {
                $offset = 0;
            } else {
                $offset = ($page * 20) - 20;
            }

            $endpoint = add_query_arg([
                'api_key'     => $this->api_key,
                'q'           => $search,
                'limit'       => 20,
                'rating'      => $this->content_rating,
                'random_id'   => $user_id,
                'offset'      => $offset,
                'lang'        => $this->lang
            ], 'https://api.giphy.com/v1/gifs/search');

            $args = array(
                'timeout'     => 2,
            );

            $request = wp_remote_get($endpoint, $args);

            if( is_wp_error( $request ) ){
                return [];
            }

            $response = json_decode($request['body']);

            $return = [
                'pagination' => $response->pagination,
                'gifs'       => []
            ];

            $gifs = $response->data;

            if( count($gifs ) > 0 ){
                foreach ( $gifs as $gif ){
                    $return['gifs'][] = (array) $gif;
                }
            }

            return $return;
        }

        public function check_api_key(){
            $endpoint = add_query_arg([
                'api_key' => $this->api_key,
                'limit'       => 20,
                'rating'      => $this->content_rating,
                'offset'      => 0
            ], 'https://api.giphy.com/v1/gifs/trending');

            $args = array(
                'timeout'     => 2,
            );

            $request = wp_remote_get($endpoint, $args);

            if( is_wp_error( $request ) ){
                return [];
            }

            $response = json_decode($request['body']);

            if( is_wp_error( $request ) ){
                update_option( 'bp_better_messages_giphy_error', 'GIPHY Error:' . $request->get_error_message() );
            } else {
                if (isset($request['response']) && $request['response']['code'] !== 200) {
                    update_option('bp_better_messages_giphy_error', $response->message);
                } else {
                    delete_option('bp_better_messages_giphy_error');
                }
            }
        }


    }

endif;


function BP_Better_Messages_Giphy()
{
    return BP_Better_Messages_Giphy::instance();
}
