<?php
/**
 * Cera_Grimlock_LearnDash Class
 *
 * @package cera
 * @author  Themosaurus
 * @since   1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The Cera Grimlock for LearnDash integration class.
 */
class Cera_Grimlock_LearnDash {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		require_once get_template_directory() . '/inc/grimlock-learndash/customizer/class-cera-grimlock-learndash-customizer.php';
	}
}

return new Cera_Grimlock_LearnDash();
