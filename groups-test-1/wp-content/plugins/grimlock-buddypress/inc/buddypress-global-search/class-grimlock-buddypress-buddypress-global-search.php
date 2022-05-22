<?php
/**
 * Grimlock_BuddyPress_BuddyPress_Global_Search Class
 *
 * @package  grimlock-buddypress
 * @author   Themosaurus
 * @since    1.3.19
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The BuddyPress Global Search integration class
 */
class Grimlock_BuddyPress_BuddyPress_Global_Search {
	/**
	 * Setup class.
	 */
	public function __construct() {
		require_once GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_PATH . 'inc/buddypress-global-search/customizer/class-grimlock-buddypress-buddypress-global-search-search-customizer.php';
	}
}

return new Grimlock_BuddyPress_BuddyPress_Global_Search();
