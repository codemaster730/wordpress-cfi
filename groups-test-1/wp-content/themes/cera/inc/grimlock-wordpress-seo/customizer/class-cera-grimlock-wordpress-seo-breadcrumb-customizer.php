<?php
/**
 * Cera_Grimlock_WordPress_SEO_Breadcrumb_Customizer Class
 *
 * @author  Themosaurus
 * @since   1.0.0
 * @package grimlock
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The Cera Grimlock WordPress SEO Breadcrumb class for the Customizer.
 */
class Cera_Grimlock_WordPress_SEO_Breadcrumb_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'grimlock_wordpress_seo_breadcrumb_customizer_defaults', array( $this, 'change_defaults' ), 10, 1 );
	}

	/**
	 * Change default values and control settings for the Customizer.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $defaults The array of default values for the Customizer controls.
	 *
	 * @return array           The updated array of default values for the Customizer controls.
	 */
	public function change_defaults( $defaults ) {
		$defaults['breadcrumb_custom_header_displayed'] = true;
		$defaults['breadcrumb_color']                   = CERA_GRAY_LIGHT;
		$defaults['breadcrumb_link_color']              = CERA_GRAY_LIGHT;
		$defaults['breadcrumb_link_hover_color']        = CERA_GRAY;
		return $defaults;
	}
}

return new Cera_Grimlock_WordPress_SEO_Breadcrumb_Customizer();
