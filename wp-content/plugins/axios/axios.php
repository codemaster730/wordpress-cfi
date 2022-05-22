<?php
/**
 * Plugin Name: Axios API
 * Description: Axios API get posts.
 * Version: 1.0.0
 * Author: CubixSol
 * Author URI: https://cubixsol.com
 * Text Domain: axios
 */

// Exit if accessed directly
defined('ABSPATH') || exit;
ini_set('memory_limit', '-1');
ini_set('max_execution_time', '-1');
ini_set('display_errors', 0);

$version = '1.0.0';
cubixsol_define_constants($version);
cubixsol_init_hooks();

/**
 * Define  Constants.
 */
function cubixsol_define_constants($version)
{
    define('PLUGIN_FILE', __FILE__);
    define('VERSION', $version);
}

function cubixsol_init_hooks() {
    register_activation_hook(PLUGIN_FILE, 'cubixsol_activation');
    register_deactivation_hook(PLUGIN_FILE, 'cubixsol_deactivation');

    add_filter('cron_schedules', 'cronjob_schedules');
    add_action('scheduled_users', 'update_users');
}

/**
 * activation hook
 */
function cubixsol_activation() {
    if (!wp_next_scheduled('scheduled_users')) {
        wp_schedule_event(time(), 'one_hour', 'scheduled_users');
    }
}

/**
 * Deactivation hook
 */
function cubixsol_deactivation() {
    wp_clear_scheduled_hook('scheduled_users');
}

/**
 * Cron jobs
 */
function cronjob_schedules($schedules) {
    if(!isset($schedules["one_hour"])) {
        $schedules["one_hour"] = array(
            'interval' => 60*60,
            'display'  => __('Once every one hour')
        );
    }
    return $schedules;
}

// add_action('wp_footer', 'update_users', 99);
function update_users(){
    $users    = get_users( array( 'fields' => array( 'ID' ) ) );
    $user_obj = array();

    foreach ($users as $key => $user) {
        $user = get_userdata($user->ID);
        $user_obj[] = $user;
    }

    $site = site_url();
    $log  = fopen( $site."/users.json", 'w' ) or die ( 'Cannot open or create file' );
    fwrite( $log, json_encode($user_obj) );
    fclose( $log );
}