<?php
/*
 * Plugin name: Grimlock for bbPress
 * Plugin URI:  http://www.themosaurus.com
 * Description: Adds integration features for Grimlock and bbPress.
 * Author:      Themosaurus
 * Author URI:  http://www.themosaurus.com
 * Version:     1.1.9
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: grimlock-bbpress
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

define( 'GRIMLOCK_BBPRESS_VERSION',              '1.1.9' );
define( 'GRIMLOCK_BBPRESS_MIN_GRIMLOCK_VERSION', '1.3.0' );
define( 'GRIMLOCK_BBPRESS_PLUGIN_FILE',         __FILE__ );
define( 'GRIMLOCK_BBPRESS_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'GRIMLOCK_BBPRESS_PLUGIN_DIR_URL',  plugin_dir_url( __FILE__ ) );

// Initialize update checker
require 'libs/plugin-update-checker/plugin-update-checker.php';
Puc_v4_Factory::buildUpdateChecker(
	'http://files.themosaurus.com/grimlock-bbpress/version.json',
	__FILE__,
	'grimlock-bbpress'
);

/**
 * Display notice if Grimlock version doesn't match minimum requirement
 */
function grimlock_bbpress_dependency_notice() {
	$url = self_admin_url( 'update-core.php?action=do-plugin-upgrade&plugins=' ) . urlencode( plugin_basename( GRIMLOCK_PLUGIN_FILE ) );
	$url = wp_nonce_url( $url, 'upgrade-core' ); ?>
	<div class="notice notice-error">
		<p><?php printf( esc_html__( '%1$sGrimlock for bbPress%2$s requires %1$sGrimlock %3$s%2$s to continue running properly. Please %4$supdate Grimlock.%5$s', 'grimlock-bbpress' ), '<strong>', '</strong>', GRIMLOCK_BBPRESS_MIN_GRIMLOCK_VERSION, '<a href="' . esc_url( $url ) . '">', "</a>" ); ?></p>
	</div>
	<?php
}

/**
 * Load plugin.
 */
function grimlock_bbpress_loaded() {
	if ( version_compare( GRIMLOCK_VERSION, GRIMLOCK_BBPRESS_MIN_GRIMLOCK_VERSION, '<' ) ) {
		add_action( 'admin_notices', 'grimlock_bbpress_dependency_notice' );
		return;
	}

	require_once 'inc/class-grimlock-bbpress.php';

	global $grimlock_bbpress;
	$grimlock_bbpress = new Grimlock_bbPress();

	do_action( 'grimlock_bbpress_loaded' );
}
add_action( 'grimlock_loaded', 'grimlock_bbpress_loaded' );
