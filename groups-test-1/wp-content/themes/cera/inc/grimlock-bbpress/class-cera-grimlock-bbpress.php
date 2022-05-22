<?php
/**
 * Cera_Grimlock_bbPress Class
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
 * The Cera Grimlock bbPress integration class
 */
class Cera_Grimlock_bbPress {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		require_once get_template_directory() . '/inc/grimlock-bbpress/customizer/class-cera-grimlock-bbpress-archive-forum-customizer.php';
	}
}

return new Cera_Grimlock_bbPress();
