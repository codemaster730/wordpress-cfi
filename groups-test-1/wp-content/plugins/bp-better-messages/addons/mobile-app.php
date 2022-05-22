<?php
defined( 'ABSPATH' ) || exit;

if ( !class_exists( 'BP_Better_Messages_Mobile_App' ) ):

    class BP_Better_Messages_Mobile_App
    {

        public static function instance()
        {

            static $instance = null;

            if ( null === $instance ) {
                $instance = new BP_Better_Messages_Mobile_App();
                #$instance->setup_actions();
            }

            return $instance;
        }


        public function setup_actions()
        {
            add_action('wp_ajax_bp_better_messages_mobile_app',        [ $this, 'mobile_app' ]);
            add_action('wp_ajax_nopriv_bp_better_messages_mobile_app', [ $this, 'mobile_app' ]);
        }

        public function mobile_app(){
            ?>
            <html>
                <head>
                    <link rel='stylesheet' href='https://temp.wordplus.org/wp-content/plugins/bp-better-messages-premium/assets/css/bp-messages.css?ver=1.9.8.153' type='text/css' media='all' />
                </head>
                <body>
                <?php echo BP_Better_Messages()->functions->get_page(); ?>
                </body>
            </html>
            <?php
            exit;
        }
    }

endif;


function BP_Better_Messages_Mobile_App()
{
    return BP_Better_Messages_Mobile_App::instance();
}
