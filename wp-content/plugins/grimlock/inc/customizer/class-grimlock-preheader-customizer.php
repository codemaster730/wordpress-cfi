<?php
/**
 * Grimlock_Preheader_Customizer Class
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
 * The Grimlock Customizer preheader class.
 */
class Grimlock_Preheader_Customizer extends Grimlock_Region_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->id      = 'preheader';
		$this->section = 'grimlock_preheader_customizer_section';
		$this->title   = esc_html__( 'Pre Header', 'grimlock' );

		add_action( 'after_setup_theme',                                                      array( $this, 'add_customizer_fields'                       ), 20    );

		add_filter( 'grimlock_customizer_controls_js_data',                                   array( $this, 'add_customizer_controls_js_data'             ), 10, 1 );
		add_filter( 'grimlock_preheader_args',                                                array( $this, 'add_args'                                    ), 10, 1 );

		add_filter( 'grimlock_navigation_customizer_sub_menu_item_background_color_elements', array( $this, 'add_sub_menu_item_background_color_elements' ), 10, 1 );
		add_filter( 'grimlock_navigation_customizer_sub_menu_item_color_elements',            array( $this, 'add_sub_menu_item_color_elements'            ), 10, 1 );

		add_filter( 'kirki_grimlock_dynamic_css',                                             array( $this, 'add_dynamic_css'                             ), 10, 1 );
	}

	/**
	 * Add elements to use the background color applied to the Navigation sub menu items.
	 *
	 * @param $elements
	 *
	 * @return array
	 */
	public function add_sub_menu_item_background_color_elements( $elements ) {
		return array_merge( $elements, array(
			'.grimlock-preheader .menu > .menu-item .sub-menu',
			'.grimlock-preheader .wpml-ls-sub-menu',
		) );
	}

	/**
	 * Add elements to use the color applied to the Navigation sub menu items.
	 *
	 * @param $elements
	 *
	 * @return array
	 */
	public function add_sub_menu_item_color_elements( $elements ) {
		return array_merge( $elements, array(
			'.grimlock-preheader .menu > .menu-item .sub-menu',
			'.grimlock-preheader .menu > .menu-item .sub-menu .menu-item > a',
			'.grimlock-preheader .wpml-ls-sub-menu li',
			'.grimlock-preheader .wpml-ls-sub-menu li a',
		) );
	}

	/**
	 * Register default values, settings and custom controls for the Theme Customizer.
	 *
	 * @since 1.0.0
	 */
	public function add_customizer_fields() {
		$this->defaults = apply_filters( 'grimlock_preheader_customizer_defaults', array(
			'preheader_background_image'        => '',
			'preheader_background_image_width'  => get_custom_header()->width,
			'preheader_background_image_height' => get_custom_header()->height,
			'preheader_layout'                  => '6-6-cols-left-right',
			'preheader_container_layout'        => 'classic',
			'preheader_padding_y'               => 0, // %
			'preheader_mobile_displayed'        => true,
			'preheader_background_color'        => 'rgba(255,255,255,0)',
			'preheader_heading_color'           => GRIMLOCK_BODY_COLOR,
			'preheader_color'                   => GRIMLOCK_BODY_COLOR,
			'preheader_link_color'              => GRIMLOCK_LINK_COLOR,
			'preheader_link_hover_color'        => GRIMLOCK_LINK_HOVER_COLOR,
			'preheader_border_top_color'        => GRIMLOCK_BORDER_COLOR,
			'preheader_border_top_width'        => 0, // px
			'preheader_border_bottom_color'     => GRIMLOCK_BORDER_COLOR,
			'preheader_border_bottom_width'     => 0, // px
		) );

		$this->add_section(                   array( 'priority' => 50  ) );

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

return new Grimlock_Preheader_Customizer();
