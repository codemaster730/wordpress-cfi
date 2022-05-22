<?php
/*
 * Plugin name: Grimlock for Jetpack
 * Plugin URI:  http://www.themosaurus.com
 * Description: Adds integration features for Grimlock and Jetpack.
 * Author:      Themosaurus
 * Author URI:  http://www.themosaurus.com
 * Version:     1.0.8
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: grimlock-jetpack
 * Domain Path: /languages
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'GRIMLOCK_JETPACK_VERSION',         '1.0.8' );
define( 'GRIMLOCK_JETPACK_PLUGIN_FILE',     __FILE__ );
define( 'GRIMLOCK_JETPACK_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'GRIMLOCK_JETPACK_PLUGIN_DIR_URL',  plugin_dir_url( __FILE__ ) );

// Initialize update checker
require 'libs/plugin-update-checker/plugin-update-checker.php';
Puc_v4_Factory::buildUpdateChecker(
	'http://files.themosaurus.com/grimlock-jetpack/version.json',
	__FILE__,
	'grimlock-jetpack'
);

/**
 * Load plugin.
 */
function grimlock_jetpack_loaded() {
	require_once 'inc/class-grimlock-jetpack.php';

	global $grimlock_jetpack;
	$grimlock_jetpack = new Grimlock_Jetpack();

	do_action( 'grimlock_jetpack_loaded' );
}
add_action( 'grimlock_loaded', 'grimlock_jetpack_loaded' );
