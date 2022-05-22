<?php
/**
 * Grimlock_Table_Customizer Class
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
class Grimlock_Table_Customizer extends Grimlock_Base_Customizer {

	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->section = 'grimlock_table_customizer_section';
		$this->title   = esc_html__( 'Tables', 'grimlock' );

		add_action( 'after_setup_theme', array( $this, 'add_customizer_fields'    ), 20  );
		add_action( 'after_setup_theme', array( $this, 'add_editor_color_palette' ), 100 );
	}

	/**
	 * Register default values, settings and custom controls for the Theme Customizer.
	 *
	 * @since 1.0.0
	 */
	public function add_customizer_fields() {
		$this->defaults = apply_filters( 'grimlock_table_customizer_defaults', array(
			'table_border_width'             => GRIMLOCK_BORDER_WIDTH,
			'table_border_color'             => GRIMLOCK_BORDER_COLOR,
			'table_striped_background_color' => GRIMLOCK_GRAY_LIGHTEST,
		) );

		$this->add_section(                        array( 'priority' => 100 ) );

		$this->add_border_width_field(             array( 'priority' => 10  ) );
		$this->add_border_color_field(             array( 'priority' => 20  ) );
		$this->add_divider_field(                  array( 'priority' => 30  ) );
		$this->add_striped_background_color_field( array( 'priority' => 30  ) );
	}

	/**
	 * Add a Kirki color field to set the border color in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_border_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$output = array( $this->get_css_var_output( 'table_border_color' ) );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Border Color', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => 'table_border_color',
				'default'   => $this->get_default( 'table_border_color' ),
				'choices'   => array(
					'alpha'    => true,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'output'    => $output,
				'js_vars'   => $this->to_js_vars( $output ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_table_customizer_border_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki slider control to set the border width in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_border_width_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {

			$output = array( $this->get_css_var_output( 'table_border_width', 'px' ) );

			$args = wp_parse_args( $args, array(
				'type'      => 'slider',
				'section'   => $this->section,
				'label'     => esc_attr__( 'Border Width', 'grimlock' ),
				'settings'  => 'table_border_width',
				'default'   => $this->get_default( 'table_border_width' ),
				'choices'   => array(
					'min'   => 0,
					'max'   => 10,
					'step'  => 1,
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'output'    => $output,
				'js_vars'   => $this->to_js_vars( $output ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_table_customizer_border_width_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the alternative row color in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_striped_background_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {

			$output = array( $this->get_css_var_output( 'table_striped_background_color' ) );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Alternative Row Color', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => 'table_striped_background_color',
				'default'   => $this->get_default( 'table_striped_background_color' ),
				'choices'   => array(
					'alpha'    => true,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'js_vars'   => $this->to_js_vars( $output ),
				'output'    => $output,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_table_customizer_striped_background_color_field_args', $args ) );
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

		$table_striped_background_color = strtolower( $this->get_theme_mod( 'table_striped_background_color' ) );
		if ( ! in_array( $table_striped_background_color, $colors ) ) {
			$color_palette[] = array(
				'name'  => esc_html__( 'Table Striped', 'grimlock' ),
				'slug'  => 'table-striped',
				'color' => $table_striped_background_color,
			);
		}

		add_theme_support( 'editor-color-palette', $color_palette );
	}
}

return new Grimlock_Table_Customizer();
