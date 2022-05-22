<?php
/*
 * Plugin name: Grimlock Isotope
 * Plugin URI:  http://www.themosaurus.com
 * Description: Uses jQuery Isotope to dynamically filter through WP Posts.
 * Author:      Themosaurus
 * Author URI:  http://www.themosaurus.com
 * Version:     1.0.8
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: grimlock-isotope
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

define( 'GRIMLOCK_ISOTOPE_VERSION',         '1.0.8' );
define( 'GRIMLOCK_ISOTOPE_PLUGIN_FILE',     __FILE__ );
define( 'GRIMLOCK_ISOTOPE_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'GRIMLOCK_ISOTOPE_PLUGIN_DIR_URL',  plugin_dir_url( __FILE__ ) );

// Initialize update checker
require 'libs/plugin-update-checker/plugin-update-checker.php';
Puc_v4_Factory::buildUpdateChecker(
	'http://files.themosaurus.com/grimlock-isotope/version.json',
	__FILE__,
	'grimlock-isotope'
);

/**
 * Load plugin.
 */
function grimlock_isotope_loaded() {
	require_once 'inc/class-grimlock-isotope.php';

	global $grimlock_isotope;
	$grimlock_isotope = new Grimlock_Isotope();

	do_action( 'grimlock_isotope_loaded' );
}
add_action( 'grimlock_loaded', 'grimlock_isotope_loaded' );
