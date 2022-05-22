<?php
/**
 * Cera_Grimlock_WordPress_SEO Class
 *
 * @package  cera
 * @author   Themosaurus
 * @since    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * The Cera Grimlock WordPress SEO integration class
 */
class Cera_Grimlock_WordPress_SEO {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		require_once get_template_directory() . '/inc/grimlock-wordpress-seo/customizer/class-cera-grimlock-wordpress-seo-breadcrumb-customizer.php';
	}
}

return new Cera_Grimlock_WordPress_SEO();
