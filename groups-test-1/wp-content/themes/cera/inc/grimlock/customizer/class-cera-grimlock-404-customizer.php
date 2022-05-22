<?php
/**
 * Cera_Grimlock_404_Customizer Class
 *
 * @author  Themosaurus
 * @since   1.1.8
 * @package cera
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The single post class for the Customizer.
 */
class Cera_Grimlock_404_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.1.8
	 */
	public function __construct() {
		add_filter( 'grimlock_404_customizer_defaults', array( $this, 'change_defaults' ), 10, 1 );
	}

	/**
	 * Change default values and control settings for the Customizer.
	 *
	 * @since 1.1.8
	 *
	 * @param  array $defaults The array of default values for the Customizer controls.
	 *
	 * @return array           The updated array of default values for the Customizer controls.
	 */
	public function change_defaults( $defaults ) {
		$defaults['404_padding_y']                     = CERA_404_PADDING_Y;
		$defaults['404_full_screen_displayed']         = CERA_404_FULL_SCREEN_DISPLAYED;
		$defaults['404_background_color']              = CERA_404_BACKGROUND_COLOR;
		$defaults['404_thumbnail']                     = CERA_404_THUMBNAIL;
		$defaults['404_title']                         = CERA_404_TITLE;
		$defaults['404_title_color']                   = CERA_404_TITLE_COLOR;
		$defaults['404_title_format']                  = CERA_404_TITLE_FORMAT;
		$defaults['404_subtitle']                      = CERA_404_SUBTITLE;
		$defaults['404_subtitle_color']                = CERA_404_SUBTITLE_COLOR;
		$defaults['404_text']                          = CERA_404_TEXT;
		$defaults['404_text_color']                    = CERA_404_TEXT_COLOR;
		$defaults['404_button_displayed']              = CERA_404_BUTTON_DISPLAYED;
		$defaults['404_button_color']                  = CERA_404_BUTTON_COLOR;
		$defaults['404_button_background_color']       = CERA_404_BUTTON_BACKGROUND_COLOR;
		$defaults['404_button_border_color']           = CERA_404_BUTTON_BORDER_COLOR;
		$defaults['404_button_hover_background_color'] = CERA_404_BUTTON_HOVER_BACKGROUND_COLOR;
		$defaults['404_button_hover_color']            = CERA_404_BUTTON_HOVER_COLOR;
		$defaults['404_button_hover_border_color']     = CERA_404_BUTTON_HOVER_BORDER_COLOR;
		$defaults['404_layout']                        = CERA_404_LAYOUT;
		$defaults['404_container_layout']              = CERA_404_CONTAINER_LAYOUT;
		return $defaults;
	}
}

return new Cera_Grimlock_404_Customizer();
