<?php
/**
 * Cera_Grimlock_Control_Customizer Class
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
 * The control class for the Customizer.
 */
class Cera_Grimlock_Control_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'grimlock_control_customizer_defaults',                    array( $this, 'change_defaults'                 ), 10, 1 );
		add_filter( 'grimlock_control_customizer_color_elements',              array( $this, 'add_color_elements'              ), 10, 1 );
		add_filter( 'grimlock_control_customizer_background_color_elements',   array( $this, 'add_background_color_elements'   ), 10, 1 );
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
		$defaults['control_background_color']       = CERA_CONTROL_BACKGROUND_COLOR;
		$defaults['control_color']                  = CERA_CONTROL_COLOR;
		$defaults['control_placeholder_color']      = CERA_CONTROL_PLACEHOLDER_COLOR;
		$defaults['control_border_color']           = CERA_CONTROL_BORDER_COLOR;
		$defaults['control_focus_background_color'] = CERA_CONTROL_FOCUS_BACKGROUND_COLOR;
		$defaults['control_focus_color']            = CERA_CONTROL_FOCUS_COLOR;
		$defaults['control_focus_border_color']     = CERA_CONTROL_FOCUS_BORDER_COLOR;
		$defaults['control_border_width']           = CERA_CONTROL_BORDER_WIDTH;
		$defaults['control_border_radius']          = CERA_CONTROL_BORDER_RADIUS;
		return $defaults;
	}

	/**
	 * Add CSS selectors to the array of CSS selectors for the control background color.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $elements The array of CSS selectors for the control background color.
	 *
	 * @return array           The updated array of CSS selectors for the control background color.
	 */
	public function add_background_color_elements( $elements ) {
		return array_merge( $elements, array(
			'.custom-control-indicator',
		) );
	}

	/**
	 * Add CSS selectors to the array of CSS selectors for the control color.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $elements The array of CSS selectors for the control color.
	 *
	 * @return array           The updated array of CSS selectors for the control color.
	 */
	public function add_color_elements( $elements ) {
		return array_merge( $elements, array(
			'.site-preheader .search-form button[type="submit"]',
			'.site-prefooter .search-form button[type="submit"]',
			'.site-footer .search-form button[type="submit"]',
		) );
	}
}

return new Cera_Grimlock_Control_Customizer();
