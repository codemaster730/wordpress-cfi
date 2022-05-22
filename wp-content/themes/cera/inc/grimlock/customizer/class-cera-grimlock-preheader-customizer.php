<?php
/**
 * Cera_Grimlock_Preheader_Customizer Class
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
 * The preheader class for the Customizer.
 */
class Cera_Grimlock_Preheader_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'grimlock_preheader_customizer_defaults', array( $this, 'change_defaults' ), 10, 1 );
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
			'preheader_background_image'        => '',
			'preheader_background_image_width'  => get_custom_header()->width,
			'preheader_background_image_height' => get_custom_header()->height,
			'preheader_layout'                  => '6-6-cols-left-right',
			'preheader_container_layout'        => 'fluid',
			'preheader_padding_y'               => 0, // %
			'preheader_mobile_displayed'        => false,
			'preheader_background_color'        => '#ffffff',
			'preheader_heading_color'           => CERA_GRAY,
			'preheader_color'                   => CERA_GRAY,
			'preheader_link_color'              => CERA_GRAY,
			'preheader_link_hover_color'        => CERA_GRAY_DARKER,
			'preheader_border_top_color'        => CERA_GRAY_LIGHTER,
			'preheader_border_top_width'        => 0, // px.
			'preheader_border_bottom_color'     => CERA_GRAY_LIGHTER,
			'preheader_border_bottom_width'     => 1, // px.
		) );
	}
}

return new Cera_Grimlock_Preheader_Customizer();
