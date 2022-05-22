<?php
/*
 * Plugin name: Member Swipe for BuddyPress
 * Plugin URI:  https://www.themosaurus.com
 * Description: This plugin allows you to create a directory to swipe your members in a mobile friendly layout..
 * Author:      Themosaurus
 * Author URI:  https://www.themosaurus.com
 * Version:     1.1.6
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: bp-member-swipe
 * Domain Path: /languages
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'BP_MEMBER_SWIPE_VERSION',         '1.1.6' );
define( 'BP_MEMBER_SWIPE_PLUGIN_FILE',     __FILE__ );
define( 'BP_MEMBER_SWIPE_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'BP_MEMBER_SWIPE_PLUGIN_DIR_URL',  plugin_dir_url( __FILE__ ) );

/**
 * Show notice if BuddyPress is not installed
 */
function bp_member_swipe_dependency_notice() {
	?>
	<div class="notice notice-error">
		<p>
			<?php
			// translators: placeholders are opening and closing <a> tag, linking to BuddyPress plugin
			printf( esc_html__( 'Member Swipe for BuddyPress requires %1$sBuddyPress%2$s to be installed and activated.', 'bp-member-swipe' ), '<a href="https://wordpress.org/plugins/buddypress/" target="_blank">', '</a>' );
			?>
		</p>
	</div>
	<?php
}
add_action( 'admin_notices', 'bp_member_swipe_dependency_notice' );

/**
 * Load plugin.
 */
function bp_member_swipe_loaded() {
	// Remove admin notice if plugin is able to load
	remove_action( 'admin_notices', 'bp_member_swipe_dependency_notice' );

	require_once 'inc/class-bp-member-swipe.php';
	require_once 'admin/class-bp-member-swipe-admin.php';

	global $bp_member_swipe;
	$bp_member_swipe = new BP_Member_Swipe();

	global $bp_member_swipe_admin;
	$bp_member_swipe_admin = new BP_Member_Swipe_Admin();

	register_activation_hook( __FILE__, array( $bp_member_swipe, 'activate' ) );
	register_deactivation_hook( __FILE__, array( $bp_member_swipe, 'deactivate' ) );

	do_action( 'bp_member_swipe_loaded' );
}
add_action( 'bp_include', 'bp_member_swipe_loaded', 10 );