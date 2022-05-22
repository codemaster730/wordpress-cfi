<?php
/**
 * Grimlock_Global_Customizer Class
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
 * The Grimlock Customizer style class.
 */
class Grimlock_Global_Customizer extends Grimlock_Base_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->section = 'background_image';
		$this->title   = esc_html__( 'Global', 'grimlock' );

		add_action( 'customize_register',                   array( $this, 'customize_register'              ), 20, 1 );
		add_action( 'after_setup_theme',                    array( $this, 'add_customizer_fields'           ), 20    );
		add_action( 'after_setup_theme',                    array( $this, 'add_editor_color_palette'        ), 100   );
		add_filter( 'body_class',                           array( $this, 'add_body_classes'                ), 10, 1 );
		add_filter( 'grimlock_customizer_controls_js_data', array( $this, 'add_customizer_controls_js_data' ), 10, 1 );
	}

	/**
	 * Add custom classes to body to modify layout.
	 *
	 * @since 1.0.0
	 * @param $classes
	 *
	 * @return string
	 */
	public function add_body_classes( $classes ) {
		$classes[] = "grimlock--{$this->get_theme_mod( 'wrapper_layout' )}";
		return $classes;
	}

	/**
	 * Add settings and custom controls for the Theme Customizer.
	 *
	 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
	 *
	 * @since 1.0.0
	 */
	public function customize_register( $wp_customize ) {
		// Move the 'Background' section in the 'Appearance' panel and rename it as 'Global'.
		$wp_customize->get_section( 'background_image' )->panel    = 'grimlock_appearance_customizer_panel';
		$wp_customize->get_section( 'background_image' )->priority = 10;
		$wp_customize->get_section( 'background_image' )->title    = esc_html__( 'Global', 'grimlock' );

		// Change default setting value for the background color.
		$wp_customize->get_setting( 'background_color' )->default = $this->get_default( 'background_color' );
		$wp_customize->get_control( 'background_color' )->section = 'background_image';
		$wp_customize->get_control( 'background_color' )->label   = esc_html__( 'Color', 'grimlock' );

		// Change default setting value for the background color.
		$wp_customize->get_control( 'background_image' )->label   = esc_html__( 'Image', 'grimlock' );
	}

	/**
	 * Register default values, settings and custom controls for the Theme Customizer.
	 *
	 * @since 1.0.0
	 */
	public function add_customizer_fields() {
		$this->defaults = apply_filters( 'grimlock_global_customizer_defaults', array(
			'wrapper_layout'           => 'classic',
			'content_background_color' => 'rgba(255,255,255,0)',
		) );

		$this->add_wrapper_layout_field(           array( 'priority' => 10 ) );
		$this->add_content_background_color_field( array( 'priority' => 20 ) );
	}

	/**
	 * Add tabs to the Customizer to group controls.
	 *
	 * @param  array $js_data The array of data for the Customizer controls.
	 *
	 * @return array          The filtred array of data for the Customizer controls.
	 */
	public function add_customizer_controls_js_data( $js_data ) {
		$js_data['tabs'][$this->section] = array(
			array(
				'label'    => esc_html__( 'Background', 'grimlock' ),
				'class'    => 'background_image-background-tab',
				'controls' => array(
					'background_color',
					'background_image',
					'background_preset',
					'background_position',
					'background_size',
					'background_repeat',
					'background_attachment',
				),
			),
			array(
				'label'    => esc_html__( 'Layout', 'grimlock' ),
				'class'    => 'background_image-layout-tab',
				'controls' => array(
					'wrapper_layout',
				),
			),
			array(
				'label'    => esc_html__( 'Content', 'grimlock' ),
				'class'    => 'background_image-content-tab',
				'controls' => array(
					'content_background_color',
				),
			),
		);
		return $js_data;
	}

	/**
	 * Add a Kirki radio-image field to set the layout in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_wrapper_layout_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'      => 'radio-image',
				'section'   => $this->section,
				'label'     => esc_html__( 'Layout', 'grimlock' ),
				'settings'  => 'wrapper_layout',
				'default'   => $this->get_default( 'wrapper_layout' ),
				'priority'  => 10,
				'choices'   => array(
					'classic'  => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/global-classic.png',
					'boxed'    => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/global-boxed.png',
					'bordered' => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/global-bordered.png',
				),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_global_customizer_wrapper_layout_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the background color in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_content_background_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_global_customizer_content_background_color_elements', array(
				'.site-content',
			) );

			$outputs  = apply_filters( 'grimlock_global_customizer_content_background_color_outputs', array(
				$this->get_css_var_output( 'content_background_color' ),
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'background-color',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Background color', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => 'content_background_color',
				'default'   => $this->get_default( 'content_background_color' ),
				'choices'   => array(
					'alpha'    => true,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'output'    => $outputs,
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_global_customizer_content_background_color_field_args', $args ) );
		}
	}

	/**
	 * Add colors to the editor color palette
	 *
	 * @since 1.3.12
	 */
	public function add_editor_color_palette() {
		$color_palette = ! empty( get_theme_support( 'editor-color-palette' ) ) ? current( get_theme_support( 'editor-color-palette' ) ) : array();
		$colors        = ! empty( $color_palette ) ? array_map( 'strtolower', array_column( $color_palette, 'color' ) ) : array();

		$content_background_color = strtolower( $this->get_theme_mod( 'content_background_color' ) );
		if ( ! in_array( $content_background_color, $colors ) ) {
			$color_palette[] = array(
				'name'  => esc_html__( 'Content', 'grimlock' ),
				'slug'  => 'content-background-color',
				'color' => $content_background_color,
			);
		}

		add_theme_support( 'editor-color-palette', $color_palette );
	}

}

return new Grimlock_Global_Customizer();
