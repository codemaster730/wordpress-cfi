<?php
/**
 * Cera_Grimlock_Back_To_Top_Button_Customizer Class
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
 * The back to top button class for the Customizer.
 */
class Cera_Grimlock_Back_To_Top_Button_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'grimlock_back_to_top_button_customizer_defaults', array( $this, 'change_defaults' ), 10, 1 );
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
		$defaults['back_to_top_button_displayed']        = true;
		$defaults['back_to_top_button_border_radius']    = .25;
		$defaults['back_to_top_button_padding']          = .75;
		$defaults['back_to_top_button_background_color'] = 'rgba(0,0,0,0.25)';
		$defaults['back_to_top_button_color']            = '#ffffff';
		$defaults['back_to_top_button_border_color']     = 'rgba(0,0,0,0)';
		$defaults['back_to_top_button_border_width']     = 0;
		$defaults['back_to_top_button_position']         = 'right';
		return $defaults;
	}
}

return new Cera_Grimlock_Back_To_Top_Button_Customizer();
