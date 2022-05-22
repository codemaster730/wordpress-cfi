<?php
/**
 * Plugin name: Grimlock Login
 * Plugin URI:  http://www.themosaurus.com
 * Description: Uses theme options to style the WP login page.
 * Author:      Themosaurus
 * Author URI:  http://www.themosaurus.com
 * Version:     1.1.10
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: grimlock-login
 * Domain Path: /languages
 *
 * @package grimlock-login
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'GRIMLOCK_LOGIN_VERSION',         '1.1.10' );
define( 'GRIMLOCK_LOGIN_PLUGIN_FILE',     __FILE__ );
define( 'GRIMLOCK_LOGIN_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'GRIMLOCK_LOGIN_PLUGIN_DIR_URL',  plugin_dir_url( __FILE__ ) );


// Initialize update checker.
require 'libs/plugin-update-checker/plugin-update-checker.php';
Puc_v4_Factory::buildUpdateChecker(
	'http://files.themosaurus.com/grimlock-login/version.json',
	__FILE__,
	'grimlock-login'
);

/**
 * Load plugin.
 */
function grimlock_login_loaded() {
	require_once 'inc/class-grimlock-login.php';

	global $grimlock_login;
	$grimlock_login = new Grimlock_Login();

	do_action( 'grimlock_login_loaded' );
}
add_action( 'grimlock_loaded', 'grimlock_login_loaded' );

/**
 * Load Login Recaptcha add on
 */
function grimlock_login_login_recaptcha_loaded() {
	if ( class_exists( 'LoginNocaptcha' ) ) {
		require_once 'inc/login-recaptcha/class-grimlock-login-login-recaptcha.php';
		global $grimlock_login_login_recaptcha;
		$grimlock_login_login_recaptcha = new Grimlock_Login_Login_Recaptcha();
	}
}
add_action( 'grimlock_login_loaded', 'grimlock_login_login_recaptcha_loaded' );

/**
 * Load Super Socializer add on
 */
function grimlock_login_super_socializer_loaded() {
	if ( function_exists( 'the_champ_init' ) ) {
		require_once 'inc/super-socializer/class-grimlock-login-super-socializer.php';
		global $grimlock_login_super_socializer;
		$grimlock_login_super_socializer = new Grimlock_Login_Super_Socializer();
	}
}
add_action( 'grimlock_login_loaded', 'grimlock_login_super_socializer_loaded' );

/**
 * Load WordPress Social Login add on
 */
function grimlock_login_wordpress_social_login_loaded() {
	if ( function_exists( 'wsl_activate' ) ) {
		require_once 'inc/wordpress-social-login/class-grimlock-login-wordpress-social-login.php';
		global $grimlock_login_wordpress_social_login;
		$grimlock_login_wordpress_social_login = new Grimlock_Login_WordPress_Social_Login();
	}
}
add_action( 'grimlock_login_loaded', 'grimlock_login_wordpress_social_login_loaded' );
