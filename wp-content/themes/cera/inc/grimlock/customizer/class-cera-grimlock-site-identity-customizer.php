<?php
/**
 * Cera_Grimlock_Site_Identity_Customizer Class
 *
 * @author   Themosaurus
 * @since    1.0.0
 * @package grimlock
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The site itdentity class for the Customizer.
 */
class Cera_Grimlock_Site_Identity_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'grimlock_site_identity_customizer_defaults', array( $this, 'change_defaults' ), 10, 1 );

		add_filter( 'grimlock_site_identity_customizer_custom_logo_size_outputs', array( $this, 'custom_logo_size_outputs' ), 10, 1 );
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
		$defaults['site_identity_custom_logo_displayed'] = true;
		$defaults['site_identity_custom_logo_size']      = 70;

		$defaults['site_identity_blogname_displayed']   = false;
		$defaults['site_identity_blogname_font']        = array(
			'font-family'    => CERA_FONT_FAMILY_BASE,
			'font-weight'    => CERA_FONT_WEIGHT_HEADING,
			'font-size'      => '1.25rem',
			'line-height'    => CERA_LINE_HEIGHT_BASE,
			'letter-spacing' => '0',
			'subsets'        => array( 'latin-ext' ),
			'text-transform' => 'none',
		);
		$defaults['site_identity_blogname_color']       = CERA_NAVIGATION_ITEM_COLOR_ACTIVE;
		$defaults['site_identity_blogname_hover_color'] = CERA_NAVIGATION_ITEM_COLOR;

		$defaults['site_identity_blogdescription_displayed'] = false;

		return $defaults;
	}

	/**
	 * Add selectors and properties to the CSS rule-set for the primary button background color.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $outputs The array of CSS selectors and properties for the primary button background color.
	 *
	 * @return array          The updated array of CSS selectors for the primary button background color.
	 */
	public function custom_logo_size_outputs( $outputs ) {
		return array_merge( $outputs, array(
			array(
				'element'  => implode( ',', array(
					'.main-navigation.navbar--fat-center .navbar-nav--buddypress',
					'.main-navigation.navbar--fat-left .navbar-nav--buddypress',
				) ),
				'property'      => 'height',
				'value_pattern' => 'calc( $px / 2 )',
				'media_query'   => '@media (min-width: 992px)',
			),
		) );
	}
}

return new Cera_Grimlock_Site_Identity_Customizer();
