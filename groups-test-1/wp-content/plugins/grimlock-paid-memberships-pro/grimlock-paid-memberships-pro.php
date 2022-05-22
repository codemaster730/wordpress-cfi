<?php
/*
 * Plugin name: Grimlock for Paid Memberships Pro
 * Plugin URI:  https://www.themosaurus.com/
 * Description: Adds integration features for Grimlock and Paid Memberships Pro.
 * Author:      Themosaurus
 * Author URI:  https://www.themosaurus.com/
 * Version:     1.0.2
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: grimlock-paid-memberships-pro
 * Domain Path: /languages
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'GRIMLOCK_PAID_MEMBERSHIPS_PRO_VERSION',         '1.0.2' );
define( 'GRIMLOCK_PAID_MEMBERSHIPS_PRO_PLUGIN_FILE',     __FILE__ );
define( 'GRIMLOCK_PAID_MEMBERSHIPS_PRO_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'GRIMLOCK_PAID_MEMBERSHIPS_PRO_PLUGIN_DIR_URL',  plugin_dir_url( __FILE__ ) );

// Initialize update checker
require 'libs/plugin-update-checker/plugin-update-checker.php';
Puc_v4_Factory::buildUpdateChecker(
	'http://files.themosaurus.com/grimlock-paid-memberships-pro/version.json',
	__FILE__,
	'grimlock-paid-memberships-pro'
);

/**
 * Load plugin.
 */
function grimlock_paid_memberships_pro_loaded() {
	require_once 'inc/class-grimlock-paid-memberships-pro.php';

	global $grimlock_paid_memberships_pro;
	$grimlock_paid_memberships_pro = new Grimlock_Paid_Memberships_Pro();

	do_action( 'grimlock_paid_memberships_pro_loaded' );
}
add_action( 'grimlock_loaded', 'grimlock_paid_memberships_pro_loaded' );
