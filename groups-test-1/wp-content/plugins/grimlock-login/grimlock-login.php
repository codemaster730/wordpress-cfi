<?php
/**
 * Plugin name: Grimlock Login
 * Plugin URI:  http://www.themosaurus.com
 * Description: Uses theme options to style the WP login page.
 * Author:      Themosaurus
 * Author URI:  http://www.themosaurus.com
 * Version:     1.1.4
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

define( 'GRIMLOCK_LOGIN_VERSION',         '1.1.4' );
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
