<?php
/**
 * Grimlock_Prefooter_Customizer Class
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
 * The Grimlock Customizer prefooter class.
 */
class Grimlock_Prefooter_Customizer extends Grimlock_Region_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->id      = 'prefooter';
		$this->section = 'grimlock_prefooter_customizer_section';
		$this->title   = esc_html__( 'Pre Footer', 'grimlock' );

		add_action( 'after_setup_theme',                    array( $this, 'add_customizer_fields'           ), 20    );

		add_filter( 'grimlock_customizer_controls_js_data', array( $this, 'add_customizer_controls_js_data' ), 10, 1 );
		add_filter( 'grimlock_prefooter_args',              array( $this, 'add_args'                        ), 10, 1 );

		add_filter( 'kirki_grimlock_dynamic_css',           array( $this, 'add_dynamic_css'                 ), 10, 1 );
	}

	/**
	 * Register default values, settings and custom controls for the Theme Customizer.
	 *
	 * @since 1.0.0
	 */
	public function add_customizer_fields() {
		$this->defaults = apply_filters( 'grimlock_prefooter_customizer_defaults', array(
			'prefooter_background_image'        => '',
			'prefooter_background_image_width'  => get_custom_header()->width,
			'prefooter_background_image_height' => get_custom_header()->height,
			'prefooter_layout'                  => '3-3-3-3-cols-left-right',
			'prefooter_container_layout'        => 'classic',
			'prefooter_padding_y'               => 0, // %
			'prefooter_mobile_displayed'        => true,
			'prefooter_background_color'        => 'rgba(255,255,255,0)',
			'prefooter_heading_color'           => GRIMLOCK_BODY_COLOR,
			'prefooter_color'                   => GRIMLOCK_BODY_COLOR,
			'prefooter_link_color'              => GRIMLOCK_LINK_COLOR,
			'prefooter_link_hover_color'        => GRIMLOCK_LINK_HOVER_COLOR,
			'prefooter_border_top_color'        => GRIMLOCK_BORDER_COLOR,
			'prefooter_border_top_width'        => 0, // px
			'prefooter_border_bottom_color'     => GRIMLOCK_BORDER_COLOR,
			'prefooter_border_bottom_width'     => 0, // px
		) );

		$this->add_section(                   array( 'priority' => 70  ) );

		$this->add_layout_field(              array( 'priority' => 10  ) );
		$this->add_divider_field(             array( 'priority' => 20  ) );
		$this->add_container_layout_field(    array( 'priority' => 20  ) );
		$this->add_divider_field(             array( 'priority' => 30  ) );
		$this->add_heading_field(             array( 'priority' => 30, 'label' => esc_html__( 'Mobile Display', 'grimlock' ) ) );
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

return new Grimlock_Prefooter_Customizer();
