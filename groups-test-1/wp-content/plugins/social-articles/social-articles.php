<?php
/*
 Plugin Name: Social Articles
 Description: This is the first BuddyPress plugin that let you to create and manage posts from your profile. It supports all buddypres themes, so you don't need to be an expert to use it!
 Version: 2.9.5
 Author: Broobe
 Author URI: http://www.broobe.com
 Text Domain: social-articles

 Copyright 2016  broobe  (email : dev@broobe.com)

 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License, version 2, as
 published by the Free Software Foundation.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program; if not, write to the Free Software
 Broobe, Arribeños 2610, 1A, Belgrano, Buenos Aires, Argentina. (+54 11) 3221-4476 AR
 */
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

if (!class_exists('SocialArticles') && is_plugin_active( 'buddypress/bp-loader.php' )) {

    require_once plugin_dir_path( __FILE__ ) . 'includes/admin/social-articles-tools.php';
    require_once plugin_dir_path( __FILE__ ) . 'includes/sa-form/sa-form-class.php';
    require_once plugin_dir_path( __FILE__ ) . 'includes/sa-form/sa-form-settings-class.php';
    require_once plugin_dir_path( __FILE__ ) . 'includes/sa-form/sa-helper-class.php';
    require_once plugin_dir_path( __FILE__ ) . 'includes/sa-form/fields/classes/sa-field-class.php';


    class SocialArticles extends Broobe_SA_Plugin_Admin {
        var $options;
        public $form_settings;

        public function __construct() {
            global $sa_actions;
            $sa_actions = array('articles', 'draft', 'publish', 'under-review', 'new');
            $this -> options = get_option('social_articles_options');
            $this -> loadConstants();

            add_action('plugins_loaded', array(&$this, 'start'));
            add_action( 'bp_include', array(&$this, 'bpInit'));
            register_activation_hook(__FILE__, array(&$this, 'activate'));

            $this->form_settings = new SA_Form_Settings();
            add_action('plugins_loaded', array(&$this, 'add_field_actions'));

        }

        public function start() {
            load_plugin_textdomain('social-articles', false, SA_DIR_NAME . '/languages');

            add_filter('plugin_row_meta', array(&$this, 'pluginMetaLinks'), 10, 2);
            add_filter('plugin_action_links_' . SA_BASE_NAME, array($this, 'pluginActionLinks'));


            if ( is_admin() ){
                add_action('admin_menu', array(&$this,'adminMenu'));
                add_action('admin_enqueue_scripts', array(&$this,'loadAdminScripts'));
                add_action('admin_enqueue_scripts', array(&$this,'loadAdminStyles'));
            }else{
                add_action('wp_enqueue_scripts', array(&$this, 'loadScripts'));
                add_action('wp_enqueue_scripts', array(&$this, 'loadStyles'));
            }
        }

        public function bpInit() {
            if ( version_compare( BP_VERSION, '1.5', '>' ) )
                require(dirname(__FILE__) . '/includes/social-articles-load.php');
        }

        private function loadConstants() {
            spl_autoload_register('sa_fields_autoload');

            define('SA_PLUGIN_VERSION', '2.9.5');
            define('SA_PLUGIN_DIR', dirname(__FILE__));
            define('SA_SLUG', 'articles');
            define('SA_ADMIN_SLUG', 'social-articles');
            define('SA_DIR_NAME', plugin_basename(dirname(__FILE__)));
            define('SA_BASE_NAME', plugin_basename(__FILE__));
            define('SA_BASE_PATH', plugin_dir_path( __FILE__ ));
            define('SA_BASE_URL', plugins_url() . '/' . SA_DIR_NAME);

            $upload_dir = wp_upload_dir();
            define('SA_TEMP_IMAGE_URL',$upload_dir['baseurl'].'/');
            define('SA_TEMP_IMAGE_PATH',$upload_dir['basedir'].'/');
        }
        
        public function add_field_actions(){
            //execute actions inside fields
            $post_type = SA_Helper::get_post_type();

            $all_fields = SA_Helper::get_registered_fields_instances($post_type);
            $selected_fields = SA_Helper::get_selected_fields();

            foreach ($selected_fields as $key => $field_config) {
                if (array_key_exists($key,$all_fields)) {
                    $original_field = $all_fields[$key];
                    if (is_object($original_field)) {
                        $original_field->add_actions();
                    }
                }
            }
        }

        public function activate(){

            $options = get_option('social_articles_options');

            $options = set_default_options($options);


            update_option('social_articles_options', $options);
        }

        public function deactivate(){
        }

        public function loadScripts() {
            global $bp, $sa_actions;
            if(in_array($bp->current_action, $sa_actions)){

                if (!wp_script_is( 'jquery', 'queue' )){
                    wp_enqueue_script( 'jquery' );
                }

                if (!wp_script_is( 'jquery-ui-core', 'queue' )){
                    wp_enqueue_script( 'jquery-ui-core' );
                }

                wp_enqueue_script('social-articles-js', SA_BASE_URL . '/assets/js/social-articles.js',array('jquery'),SA_PLUGIN_VERSION);
                wp_localize_script( 'social-articles-js', 'MyAjax', array(
                    'ajaxurl' => admin_url( 'admin-ajax.php' ),
                    'security' =>  wp_create_nonce( "sa_security_ajax" ),
                    'baseUrl' =>SA_BASE_URL,
                    'tmpImageUrl' =>SA_TEMP_IMAGE_URL) );
            }
        }

        public function loadStyles() {
            global $bp, $sa_actions;
            if(in_array($bp->current_action, $sa_actions)){
                wp_enqueue_style( 'social-articles-css', SA_BASE_URL.'/assets/css/social-articles.css', array(),SA_PLUGIN_VERSION,'all' );
            }
        }


        public function adminMenu() {
            include (SA_BASE_PATH . '/includes/admin/social-articles-options.php');
            add_options_page('Social Articles', 'Social Articles', 'manage_options', SA_ADMIN_SLUG, 'social_articles_page');

        }

        public function loadAdminScripts (){
            if (isset($_GET['page']) && $_GET['page'] == SA_ADMIN_SLUG) {
                wp_enqueue_script('postbox');
                wp_enqueue_script('dashboard');
                wp_enqueue_script('sa-admin-settings', SA_BASE_URL . '/includes/admin/assets/js/sa-admin-settings.js', array( 'jquery' ),SA_PLUGIN_VERSION);
                wp_enqueue_script('sa-form', SA_BASE_URL . '/includes/sa-form/assets/js/sa-form.js', array( 'jquery' ),SA_PLUGIN_VERSION);
            }
        }

        public function loadAdminStyles() {
            if (isset($_GET['page']) && $_GET['page'] == SA_ADMIN_SLUG) {
                wp_enqueue_style('dashboard');
                wp_enqueue_style('global');
                wp_enqueue_style('wp-admin');
                wp_enqueue_style( 'sa-admin-settings',SA_BASE_URL.'/includes/admin/assets/css/sa-admin-settings.css', array(),SA_PLUGIN_VERSION,'all' );
            }
        }

        public function pluginActionLinks($links) {
            $settings_link = '<a href="' . menu_page_url( SA_ADMIN_SLUG, false ) . '">'
                . esc_html( __( 'Settings', 'social-articles' ) ) . '</a>';

            array_unshift( $links, $settings_link );

            return $links;
        }

        public function pluginMetaLinks( $links, $file ) {
            $plugin = plugin_basename(__FILE__);

            if ( $file == $plugin ) {
                $donate_link = array('<a target="_blank" href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=PXXBLQV92XEXL">'
                    . esc_html( __( 'Donate', 'social-articles' ) ) . '</a>');

                $links = array_merge($links, $donate_link);
            }
            return $links;
        }
    }


    /*
     * Initiate the plug-in.
     */

    if ( defined( 'SOCIAL_ARTICLES_LATE_LOAD' ) ) {
        add_action('plugins_loaded', 'social_articles', (int)SOCIAL_ARTICLES_LATE_LOAD);
    }else{
        social_articles();
    }

}else {
    add_action( 'admin_notices', 'bp_social_articles_install_buddypress_notice');
}

function bp_social_articles_install_buddypress_notice() {
    echo '<div id="message" class="error fade"><p style="line-height: 150%">';
    _e('<strong>Social Articles</strong></a> requires the BuddyPress plugin to work. Please <a href="http://buddypress.org">install BuddyPress</a> first, or <a href="plugins.php">deactivate Social Articles</a>.', 'social-articles');
    echo '</p></div>';
}


function sa_fields_autoload($class) {
    if ( 0 !== strpos( $class, 'SA_Field_' ) )
        return;
    include(plugin_dir_path( __FILE__ ) . '/includes/sa-form/fields/classes/' . strtolower(str_replace('_', '-',str_replace('SA_Field_', 'sa-' ,$class))).'-class.php');
}

function set_default_options($options){
    if (!isset($options['post_per_page']))
        $options['post_per_page'] = '10';

    if (!isset($options['excerpt_length']))
        $options['excerpt_length'] = '30';

    if (!isset($options['excerpt_length']))
        $options['category_type'] = 'single';

    if (!isset($options['workflow']))
        $options['workflow'] = 'approval';

    if (!isset($options['bp_notifications']))
        $options['bp_notifications'] = 'false';

    if (!isset($options['allow_author_adition']))
        $options['allow_author_adition'] = 'true';

    if (!isset($options['allow_author_deletion']))
        $options['allow_author_deletion'] = 'true';

    if (!isset($options['published_post_counter']))
        $options['published_post_counter'] = 'true';

    if (!isset($options['show_to_logged_out_users']))
        $options['show_to_logged_out_users'] = 'true';

    return $options;
}

function social_articles(){
    global $socialArticles;
    $socialArticles = new SocialArticles();
}

function isDirectWorkflow(){
    global $socialArticles;
    return $socialArticles->options['workflow'] == 'direct' ;
}


?>