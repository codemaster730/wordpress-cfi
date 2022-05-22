<?php
/*
 * Plugin name: Grimlock for Yoast SEO
 * Plugin URI:  http://www.themosaurus.com
 * Description: Adds integration features for Grimlock and Yoast SEO.
 * Author:      Themosaurus
 * Author URI:  http://www.themosaurus.com
 * Version:     1.0.3
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: grimlock-wordpress-seo
 * Domain Path: /languages
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'GRIMLOCK_WORDPRESS_SEO_VERSION',         '1.0.3' );
define( 'GRIMLOCK_WORDPRESS_SEO_PLUGIN_FILE',     __FILE__ );
define( 'GRIMLOCK_WORDPRESS_SEO_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'GRIMLOCK_WORDPRESS_SEO_PLUGIN_DIR_URL',  plugin_dir_url( __FILE__ ) );

// Initialize update checker
require 'libs/plugin-update-checker/plugin-update-checker.php';
Puc_v4_Factory::buildUpdateChecker(
	'http://files.themosaurus.com/grimlock-wordpress-seo/version.json',
	__FILE__,
	'grimlock-wordpress-seo'
);

/**
 * Load plugin.
 */
function grimlock_wordpress_seo_loaded() {
	require_once 'inc/class-grimlock-wordpress-seo.php';

	global $grimlock_wordpress_seo;
	$grimlock_wordpress_seo = new Grimlock_WordPress_SEO();

	do_action( 'grimlock_wordpress_seo_loaded' );
}
add_action( 'grimlock_loaded', 'grimlock_wordpress_seo_loaded' );
