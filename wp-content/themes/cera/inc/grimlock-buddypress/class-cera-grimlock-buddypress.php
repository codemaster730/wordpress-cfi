<?php
/**
 * Cera_Grimlock_BuddyPress Class
 *
 * @package  cera
 * @author   Themosaurus
 * @since    1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The Cera Grimlock BuddyPress integration class.
 */
class Cera_Grimlock_BuddyPress {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		global $cera_grimlock_buddypress_customizer;
		$cera_grimlock_buddypress_customizer = require_once get_template_directory() . '/inc/grimlock-buddypress/customizer/class-cera-grimlock-buddypress-customizer.php';

		require_once get_template_directory() . '/inc/grimlock-buddypress/customizer/class-cera-grimlock-buddypress-button-customizer.php';
		require_once get_template_directory() . '/inc/grimlock-buddypress/customizer/class-cera-grimlock-buddypress-navigation-customizer.php';
		require_once get_template_directory() . '/inc/grimlock-buddypress/customizer/class-cera-grimlock-buddypress-typography-customizer.php';

		require_once get_template_directory() . '/inc/grimlock-buddypress/customizer/class-cera-grimlock-buddypress-buddypress-docs-archive-bp-doc-customizer.php';
		require_once get_template_directory() . '/inc/grimlock-buddypress/customizer/class-cera-grimlock-buddypress-buddypress-docs-single-bp-doc-customizer.php';
	}

}

return new Cera_Grimlock_BuddyPress();
