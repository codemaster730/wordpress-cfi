<?php
/**
 * Cera BP Profile Search Class
 *
 * @package  cera
 * @author   Themosaurus
 * @since    1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Cera_BP_Profile_Search' ) ) :
	/**
	 * The Cera BP Profile Search integration class
	 */
	class Cera_BP_Profile_Search {
		/**
		 * Setup class.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			if ( class_exists( 'Grimlock_Hero' ) ) {
				require_once get_template_directory() . '/inc/bp-profile-search/customizer/class-cera-bp-profile-search-grimlock-hero-customizer.php';
			}
		}
	}
endif;

return new Cera_BP_Profile_Search();
