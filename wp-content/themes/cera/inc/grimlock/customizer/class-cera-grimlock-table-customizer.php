<?php
/**
 * Cera_Grimlock_Table_Customizer Class
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
 * The table class for the Customizer.
 */
class Cera_Grimlock_Table_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'grimlock_table_customizer_defaults',                          array( $this, 'change_defaults'                       ), 10, 1 );
		add_filter( 'grimlock_table_customizer_striped_background_color_elements', array( $this, 'add_striped_background_color_elements' ), 10, 1 );
		add_filter( 'grimlock_table_customizer_striped_background_color_outputs',  array( $this, 'add_striped_background_color_outputs'  ), 10, 1 );
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
		return array_merge( $defaults, array(
			'table_border_width'             => CERA_BORDER_WIDTH,
			'table_border_color'             => CERA_BLACK_FADED,
			'table_striped_background_color' => CERA_BLACK_FADED,
		) );
	}

	/**
	 * Add CSS selectors to the array of CSS selectors for the striped table row background color.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $elements The array of CSS selectors for the striped table row background color.
	 *
	 * @return array           The updated array of CSS selectors for the striped table row background color.
	 */
	public function add_striped_background_color_elements( $elements ) {
		return array_merge( $elements, array(
			'.login-footer a:hover',
			'.posts-filters .priority-nav__dropdown-toggle',
		) );
	}

	/**
	 * Add selectors and properties to the CSS rule-set for the striped table row background color.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $outputs The array of CSS selectors and properties for the striped table row background color.
	 *
	 * @return array          The updated array of CSS selectors for the striped table row background color.
	 */
	public function add_striped_background_color_outputs( $outputs ) {
		return array_merge( $outputs, array(
			array(
				'element'  => implode( ',', array(
					'.vertical-navbar .navbar-nav',
				) ),
				'property' => 'border-top-color',
			),
			array(
				'element' => implode( ',', array(
					'[class*="-cols-lateral"] .card .card-footer',
				) ),
				'property'      => 'border-top',
				'value_pattern' => '2px solid $',
			),
		) );
	}
}

return new Cera_Grimlock_Table_Customizer();
