<?php
/**
 * Cera_Grimlock_Custom_Header_Customizer Class
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
 * The custom header class for the Customizer.
 */
class Cera_Grimlock_Custom_Header_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'grimlock_custom_header_customizer_defaults', array( $this, 'change_defaults'  ), 10, 1 );
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
		$defaults['custom_header_padding_y']        = CERA_HEADER_PADDING_Y;
		$defaults['custom_header_background_color'] = CERA_CUSTOM_HEADER_BACKGROUND_COLOR;

		$defaults['custom_header_title_displayed'] = true;
		$defaults['custom_header_title_format']    = 'display-2';
		$defaults['custom_header_title_color']     = '#fff';

		$defaults['custom_header_subtitle_displayed'] = true;
		$defaults['custom_header_subtitle_format']    = 'lead';
		$defaults['custom_header_subtitle_color']     = 'rgba(255,255,255,.8)';

		$defaults['custom_header_link_color']       = '#fff';
		$defaults['custom_header_link_hover_color'] = 'rgba(255,255,255,0.8)';

		$defaults['custom_header_layout']           = CERA_CUSTOM_HEADER_LAYOUT;
		$defaults['custom_header_container_layout'] = CERA_CUSTOM_HEADER_CONTAINER_LAYOUT;
		$defaults['custom_header_mobile_displayed'] = true;
		return $defaults;
	}

}

return new Cera_Grimlock_Custom_Header_Customizer();
