<?php
/*
 * Plugin name: Grimlock for Author Avatars List/Block
 * Plugin URI:  http://www.themosaurus.com
 * Description: Adds integration features for Grimlock and Author Avatars List/Block.
 * Author:      Themosaurus
 * Author URI:  http://www.themosaurus.com
 * Version:     1.1.1
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: grimlock-author-avatars
 * Domain Path: /languages
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'GRIMLOCK_AUTHOR_AVATARS_VERSION',         '1.1.1' );
define( 'GRIMLOCK_AUTHOR_AVATARS_PLUGIN_FILE',     __FILE__ );
define( 'GRIMLOCK_AUTHOR_AVATARS_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'GRIMLOCK_AUTHOR_AVATARS_PLUGIN_DIR_URL',  plugin_dir_url( __FILE__ ) );

// Initialize update checker
require 'libs/plugin-update-checker/plugin-update-checker.php';
Puc_v4_Factory::buildUpdateChecker(
	'http://files.themosaurus.com/grimlock-author-avatars/version.json',
	__FILE__,
	'grimlock-author-avatars'
);

/**
 * Load plugin.
 */
function grimlock_author_avatars_loaded() {
	require_once 'inc/class-grimlock-author-avatars.php';

	global $grimlock_author_avatars;
	$grimlock_author_avatars = new Grimlock_Author_Avatars();

	do_action( 'grimlock_author_avatars_loaded' );
}
add_action( 'grimlock_loaded', 'grimlock_author_avatars_loaded' );
