<?php
defined( 'ABSPATH' ) || exit;

if ( !class_exists( 'BP_Better_Messages_Urls' ) ):

    class BP_Better_Messages_Urls
    {

        public $user_agent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; de; rv:1.9.2.3) Gecko/20100401 Firefox/3.6.3';

        public static function instance()
        {

            static $instance = null;

            if ( null === $instance ) {
                $instance = new BP_Better_Messages_Urls();
            }

            return $instance;
        }


        public function __construct()
        {
            add_filter( 'bp_better_messages_after_format_message', array( $this, 'nice_links' ), 100, 4 );
        }

        public function check_if_url_is_file( $url ){
            $parsed_url = parse_url( $url );

            if( isset( $parsed_url['path'] ) ) {
                $pathInfo = pathinfo( $parsed_url['path'] );
                if( isset( $pathInfo['extension'] ) ){
                    return true;
                }
            }

            return false;
        }


        public function nice_links( $message, $message_id, $context, $user_id )
        {

            if ( $context !== 'stack' ) return $message;
            global $processedUrls;

            $links = array();
            $original_message = $message;

            $message = preg_replace_callback('~(<a .*?>.*?</a>|<.*?>)~i', function ($match) use (&$links) { return '<' . array_push($links, $match[1]) . '>'; }, $message);

            $regex = '/\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|$!:,.;]*[A-Z0-9+&@#\/%=~_|$]/i';
            preg_match_all( $regex, $message, $urls );

            if( ! empty( $urls[0] ) ){
                $urls[0] = array_unique($urls[0]);
            }


            foreach ( $urls[ 0 ] as $_url ) {
                $url = strip_tags(html_entity_decode(esc_url( $_url )));
                $_url = esc_url_raw($url);
                if ( ! isset( $processedUrls[ $message_id ] )
                    || !in_array( $_url, $processedUrls[ $message_id ] )
                    || !in_array( $url, $processedUrls[ $message_id ] )
                ) {

                    $url_md5 = md5( $url );

                    if( BP_Better_Messages()->settings['enableNiceLinks'] === '1' ) {
                        $cache = bp_messages_get_meta($message_id, 'url_info_' . $url_md5, true);

                        if (!empty($cache)) {
                            $link = $this->render_nice_link($message_id, $url, $cache);
                        } else {

                            if ($this->check_if_url_is_file($url)) {
                                $info = false;
                            } else {
                                $info = $this->fetch_meta_tags($url);
                            }

                            if ($info !== false) {
                                bp_messages_update_meta($message_id, 'url_info_' . $url_md5, $info);
                                $link = $this->render_nice_link($message_id, $url, $info);
                            } else {
                                bp_messages_update_meta($message_id, 'url_info_' . $url_md5, '404');
                                $link = $this->render_nice_link($message_id, $url, '404');
                            }

                        }
                    }

                    if( BP_Better_Messages()->settings['oEmbedEnable'] === '1' ){

                        /*
                         * https://wordpress.org/support/article/embeds/
                         *
                         * Tested
                         * YouTube
                         * Vimeo
                         * VideoPress
                         * Flickr
                         * DailyMotion
                         * Kickstarter
                         * Meetup.com
                         * Mixcloud
                         * SoundCloud
                         */

                        $video_providers = [
                            'youtube',
                            'vimeo',
                            'videopress',
                            'dailymotion',
                            'kickstarter'
                        ];

                        $hide_link = [
                            'twitter',
                        ];

                        $excluded_oembed = [
                            'facebook',  // issues on ajax refresh
                            'giphy',     // not works
                            'reverbnation', // not works
                            'twitter',   // mini chats issues
                            'cloudup',   // mini chats issues
                            'imgur',     // mini chats issues,
                            'instagram', // mini chats and ajax refresh issues
                            'issuu', // mini chats issues
                            'reddit', // too long content usually
                            'plugins', // not needed in messages,
                        ];

                        $is_excluded = false;
                        foreach( $excluded_oembed as $item ){
                            if( strpos( $url, $item ) !== false ){
                                $is_excluded = true;
                                break;
                            }
                        }


                        $oembed = new WP_oEmbed();
                        if( $is_excluded ){
                            $embed = false;
                        } else {
                            $cache = bp_messages_get_meta( $message_id, 'media_info_' . $url_md5, true );
                            if ( ! empty( $cache ) ) {
                                $embed  = $cache;
                            } else {
                                $embed  = $oembed->get_data( $url, ['height' => '200', 'discover' => false] );
                                bp_messages_update_meta( $message_id, 'media_info_' . $url_md5, $embed );
                            }
                        }

                        if( $embed !== false ){
                            if( in_array( strtolower($embed->provider_name), $video_providers ) ){
                                $html = '<span class="bp-messages-iframe-container">' . $embed->html . '</span>';
                            } else {
                                $html = $embed->html;
                            }

                            if( in_array( strtolower($embed->provider_name), $hide_link ) ){
                                $link = $html;
                            } else {
                                $link = $html . $link;
                            }

                            $message = str_replace( $_url, '', $message );
                        } else {
                            $message = str_replace( $_url, '<a target="_blank" href="' . $_url . '">' . $_url . '</a>', $message );
                        }
                    } else {

                        $message = str_replace( $_url, '<a target="_blank" href="' . $_url . '">' . $_url . '</a>', $message );
                    }


                    if( isset( $link ) ) {
                        $processedUrls[$message_id][] = $link;

                        $message .= '%%link_' . count($processedUrls[$message_id]) . '%%';

                    }
                }
            }


            return preg_replace_callback('/<(\d+)>/', function ($match) use (&$links) { return $links[$match[1] - 1]; }, $message);

        }

        public function render_nice_link( $message_id, $url, $info )
        {
            if ( $info == '404' || empty( $info[ 'title' ] ) ) {
                return '';
            }
            ob_start();
            ?>
            <a href="<?php echo $url; ?>" target="_blank" class="url-wrap">
                <?php /*if ( $info[ 'image' ] ) { ?>
                    <span class="url-image" style="background-image: url(<?php echo esc_attr( $info[ 'image' ] ); ?>)"></span>
                <?php }*/?>
                <span class="url-description">
                    <span class="url-title"><i class="fas fa-external-link-alt" aria-hidden="true"></i> <?php echo esc_attr( $info[ 'title' ] ); ?></span>
                    <span class="url-site"><?php echo esc_attr( $info[ 'site' ] ); ?></span>
                </span>
            </a>
            <?php
            return ob_get_clean();
        }

        public function fetch_meta_tags( $url )
        {

            $args = [
                'user-agent' => $this->user_agent,
                'headers' => []
            ];

            if( isset ( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ) {
                $args['headers']['Accept-Language'] = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
            }

            $response = wp_remote_get( $url, $args );

            if ( !is_wp_error( $response ) && $response[ 'response' ][ 'code' ] == '200' ) {
                $tags = $this->getMetaTags( $response[ 'body' ] );

                $url_parts = parse_url( $url );

                $info = array(
                    'title'       => false,
                    'description' => false,
                    'image'       => false,
                    'site'        => $url_parts[ 'host' ]
                );

                if ( isset( $tags[ 'title' ] ) ) $info[ 'title' ] = $tags[ 'title' ];
                if ( isset( $tags[ 'og:title' ] ) ) $info[ 'title' ] = $tags[ 'og:title' ];
                if ( isset( $tags[ 'og:description' ] ) ) $info[ 'description' ] = $tags[ 'og:description' ];

                if ( isset( $tags[ 'thumbnail' ] ) ) $info[ 'image' ] = $tags[ 'thumbnail' ];
                if ( isset( $tags[ 'twitter:image' ] ) ) $info[ 'image' ] = $tags[ 'twitter:image' ];
                if ( isset( $tags[ 'og:image' ] ) ) $info[ 'image' ] = $tags[ 'og:image' ];

                if ( $info[ 'image' ] ) {
                    $image_check = wp_remote_get( $info[ 'image' ], array(
                        'user-agent' => $this->user_agent
                    ) );

                    if ( is_wp_error( $image_check ) || $image_check[ 'response' ][ 'code' ] != '200' ) {
                        $info[ 'image' ] = false;
                    }
                }

                if ( isset( $tags[ 'og:site_name' ] ) ) $info[ 'site' ] = $tags[ 'og:site_name' ];

                return $info;
            } else {
                return false;
            }
        }

        public function getMetaTags( $str )
        {
            $pattern = '
            ~<\s*meta\s
        
            # using lookahead to capture type to $1
            (?=[^>]*?
            \b(?:name|property|http-equiv)\s*=\s*
            (?|"\s*([^"]*?)\s*"|\'\s*([^\']*?)\s*\'|
            ([^"\'>]*?)(?=\s*/?\s*>|\s\w+\s*=))
            )
        
            # capture content to $2
            [^>]*?\bcontent\s*=\s*
              (?|"\s*([^"]*?)\s*"|\'\s*([^\']*?)\s*\'|
              ([^"\'>]*?)(?=\s*/?\s*>|\s\w+\s*=))
            [^>]*>
        
            ~ix';
            preg_match_all( $pattern, $str, $out );

            preg_match( '/<title>(.*?)<\/title>/', $str, $titles );

            $out = array_combine( $out[ 1 ], $out[ 2 ] );
            if ( isset( $titles[ 1 ] ) ) $out[ 'title' ] = $titles[ 1 ];

            return $out;
        }
    }

endif;


function BP_Better_Messages_Urls()
{
    return BP_Better_Messages_Urls::instance();
}
