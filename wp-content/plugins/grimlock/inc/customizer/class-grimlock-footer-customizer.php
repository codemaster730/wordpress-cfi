<?php
/**
 * Grimlock_Footer_Customizer Class
 *
 * @author  Themosaurus
 * @since   1.0.0
 * @package grimlock
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The Grimlock Customizer footer class.
 */
class Grimlock_Footer_Customizer extends Grimlock_Region_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->id      = 'footer';
		$this->section = 'grimlock_footer_customizer_section';
		$this->title   = esc_html__( 'Footer', 'grimlock' );

		add_action( 'after_setup_theme',                    array( $this, 'add_customizer_fields'           ), 20    );

		add_filter( 'grimlock_customizer_controls_js_data', array( $this, 'add_customizer_controls_js_data' ), 10, 1 );
		add_filter( 'grimlock_footer_args',                 array( $this, 'add_args'                        ), 10, 1 );

		add_filter( 'kirki_grimlock_dynamic_css',           array( $this, 'add_dynamic_css'                 ), 10, 1 );
	}

	/**
	 * Register default values, settings and custom controls for the Theme Customizer.
	 *
	 * @since 1.0.0
	 */
	public function add_customizer_fields() {
		$this->defaults = apply_filters( 'grimlock_footer_customizer_defaults', array(
			'footer_background_image'        => '',
			'footer_background_image_width'  => get_custom_header()->width,
			'footer_background_image_height' => get_custom_header()->height,
			'footer_layout'                  => '6-6-cols-left-right',
			'footer_container_layout'        => 'classic',
			'footer_padding_y'               => 0, // %
			'footer_mobile_displayed'        => true,
			'footer_background_color'        => 'rgba(255,255,255,0)',
			'footer_heading_color'           => GRIMLOCK_BODY_COLOR,
			'footer_color'                   => GRIMLOCK_BODY_COLOR,
			'footer_link_color'              => GRIMLOCK_LINK_COLOR,
			'footer_link_hover_color'        => GRIMLOCK_LINK_HOVER_COLOR,
			'footer_border_top_color'        => GRIMLOCK_BORDER_COLOR,
			'footer_border_top_width'        => 0, // px
			'footer_border_bottom_color'     => GRIMLOCK_BORDER_COLOR,
			'footer_border_bottom_width'     => 0, // px
		) );

		$this->add_section(                   array( 'priority' => 80  ) );

		$this->add_layout_field(              array( 'priority' => 10  ) );
		$this->add_divider_field(             array( 'priority' => 20  ) );
		$this->add_container_layout_field(    array( 'priority' => 20  ) );
		$this->add_divider_field(             array( 'priority' => 30  ) );
		$this->add_heading_field(             array(
			'label'    => esc_html__( 'Mobile Display', 'grimlock' ),
			'priority' => 30,
		) );
		$this->add_mobile_displayed_field(    array( 'priority' => 30  ) );

		$this->add_background_image_field(    array( 'priority' => 100 ) );
		$this->add_divider_field(             array( 'priority' => 110 ) );
		$this->add_padding_y_field(           array( 'priority' => 110 ) );
		$this->add_divider_field(             array( 'priority' => 120 ) );
		$this->add_background_color_field(    array( 'priority' => 120 ) );
		$this->add_divider_field(             array( 'priority' => 130 ) );
		$this->add_border_top_width_field(    array( 'priority' => 130 ) );
		$this->add_border_top_color_field(    array( 'priority' => 140 ) );
		$this->add_divider_field(             array( 'priority' => 150 ) );
		$this->add_border_bottom_width_field( array( 'priority' => 150 ) );
		$this->add_border_bottom_color_field( array( 'priority' => 160 ) );
		$this->add_divider_field(             array( 'priority' => 170 ) );
		$this->add_heading_color_field(       array( 'priority' => 170 ) );
		$this->add_color_field(               array( 'priority' => 180 ) );
		$this->add_link_color_field(          array( 'priority' => 190 ) );
		$this->add_link_hover_color_field(    array( 'priority' => 200 ) );
	}
}

return new Grimlock_Footer_Customizer();
