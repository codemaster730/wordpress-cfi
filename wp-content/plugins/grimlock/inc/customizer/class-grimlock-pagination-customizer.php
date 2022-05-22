<?php
/**
 * Grimlock_Pagination_Customizer Class
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
class Grimlock_Pagination_Customizer extends Grimlock_Base_Customizer {
	/**
	 * @var array The array of elements to target the pagination in theme.
	 * @since 1.0.0
	 */
	protected $elements;

	/**
	 * @var array The array of elements to target the pagination on hover in theme.
	 * @since 1.0.0
	 */
	protected $hover_elements;

	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->section = 'grimlock_pagination_customizer_section';
		$this->title   = esc_html( 'Pagination', 'grimlock' );

		add_action( 'after_setup_theme', array( $this, 'add_customizer_fields' ), 20 );
	}

	/**
	 * Register default values, settings and custom controls for the Theme Customizer.
	 *
	 * @since 1.0.0
	 */
	public function add_customizer_fields() {
		$this->defaults = apply_filters( 'grimlock_pagination_customizer_defaults', array(
			'pagination_background_color'       => '#ffffff',
			'pagination_color'                  => GRIMLOCK_LINK_COLOR,
			'pagination_border_color'           => '#dddddd',

			'pagination_hover_background_color' => GRIMLOCK_GRAY_LIGHTER,
			'pagination_hover_color'            => GRIMLOCK_LINK_HOVER_COLOR,
			'pagination_hover_border_color'     => '#dddddd',

			'pagination_padding_y'              => .5, // rem
			'pagination_padding_x'              => .75, // rem
			'pagination_border_radius'          => GRIMLOCK_BORDER_RADIUS, // rem
			'pagination_border_width'           => GRIMLOCK_BORDER_WIDTH, // px
		) );

		$this->elements = apply_filters( 'grimlock_pagination_customizer_elements', array(
			'.grimlock_pagination_customizer_elements',
		) );

		$this->hover_elements = array(
			'.grimlock_pagination_hover_customizer_elements',
		);

		foreach( $this->elements as $element ) {
			$this->hover_elements[] = "$element:hover";
			$this->hover_elements[] = "$element:focus";
			$this->hover_elements[] = "$element:active";
		}
		$this->hover_elements = apply_filters( 'grimlock_pagination_customizer_hover_elements', $this->hover_elements );

		$this->add_section(                      array( 'priority' => 110 ) );

		$this->add_border_radius_field(          array( 'priority' => 10  ) );
		$this->add_border_width_field(           array( 'priority' => 20  ) );
		$this->add_divider_field(                array( 'priority' => 30  ) );
		$this->add_padding_y_field(              array( 'priority' => 30  ) );
		$this->add_padding_x_field(              array( 'priority' => 40  ) );
		$this->add_divider_field(                array( 'priority' => 50  ) );
		$this->add_background_color_field(       array( 'priority' => 50  ) );
		$this->add_color_field(                  array( 'priority' => 60  ) );
		$this->add_border_color_field(           array( 'priority' => 70  ) );
		$this->add_divider_field(                array( 'priority' => 80  ) );
		$this->add_hover_background_color_field( array( 'priority' => 80  ) );
		$this->add_hover_color_field(            array( 'priority' => 90  ) );
		$this->add_hover_border_color_field(     array( 'priority' => 100 ) );
	}

	/**
	 * Add a Kirki color field to set the color in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_color_field( $args  = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_pagination_customizer_color_elements', $this->elements );
			$outputs  = apply_filters( 'grimlock_pagination_customizer_color_outputs', array(
				$this->get_css_var_output( 'pagination_color' ),
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'color',
					'suffix'   => '!important',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Text Color', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => 'pagination_color',
				'default'   => $this->get_default( 'pagination_color' ),
				'choices'   => array(
					'alpha'    => false,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'output'    => $outputs,
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_pagination_customizer_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the background color in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_background_color_field( $args  = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_pagination_customizer_background_color_elements', $this->elements );
			$outputs  = apply_filters( 'grimlock_pagination_customizer_background_color_outputs', array(
				$this->get_css_var_output( 'pagination_background_color' ),
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'background-color',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Background Color', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => 'pagination_background_color',
				'default'   => $this->get_default( 'pagination_background_color' ),
				'choices'   => array(
					'alpha'    => true,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'output'    => $outputs,
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_pagination_customizer_background_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the border color in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_border_color_field( $args  = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_pagination_customizer_border_color_elements', $this->elements );
			$outputs  = apply_filters( 'grimlock_pagination_customizer_border_color_outputs', array(
				$this->get_css_var_output( 'pagination_border_color' ),
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'border-color',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'     => 'color',
				'label'    => esc_html__( 'Border Color', 'grimlock' ),
				'section'  => $this->section,
				'settings' => 'pagination_border_color',
				'default'  => $this->get_default( 'pagination_border_color' ),
				'choices'   => array(
					'alpha'    => true,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority' => 10,
				'transport' => 'postMessage',
				'output'    => $outputs,
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_pagination_customizer_border_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki slider control to set the border radius in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_border_radius_field( $args  = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_pagination_customizer_border_radius_elements', $this->elements );
			$outputs  = apply_filters( 'grimlock_pagination_customizer_border_radius_outputs', array(
				$this->get_css_var_output( 'pagination_border_radius', 'rem' ),
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'border-radius',
					'units'    => 'rem',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'slider',
				'section'   => $this->section,
				'label'     => esc_attr__( 'Border Radius', 'grimlock' ),
				'settings'  => 'pagination_border_radius',
				'default'   => $this->get_default( 'pagination_border_radius' ),
				'choices'   => array(
					'min'   => 0,
					'max'   => 10,
					'step'  => .05,
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'output'    => $outputs,
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_pagination_customizer_border_radius_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki slider control to set the border width in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_border_width_field( $args  = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_pagination_customizer_border_width_elements', $this->elements );
			$outputs  = apply_filters( 'grimlock_pagination_customizer_border_width_outputs', array(
				$this->get_css_var_output( 'pagination_border_width', 'px' ),
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'border-width',
					'units'    => 'px',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'slider',
				'section'   => $this->section,
				'label'     => esc_attr__( 'Border Width', 'grimlock' ),
				'settings'  => 'pagination_border_width',
				'default'   => $this->get_default( 'pagination_border_width' ),
				'choices'   => array(
					'min'   => 0,
					'max'   => 10,
					'step'  => 1,
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'output'    => $outputs,
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_pagination_customizer_border_width_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the color on hover in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_hover_color_field( $args  = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_pagination_customizer_hover_color_elements', $this->hover_elements );
			$outputs  = apply_filters( 'grimlock_pagination_customizer_hover_color_outputs', array(
				$this->get_css_var_output( 'pagination_hover_color' ),
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'color',
					'suffix'   => '!important',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Text Color on Hover', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => 'pagination_hover_color',
				'default'   => $this->get_default( 'pagination_hover_color' ),
				'choices'   => array(
					'alpha'    => false,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'output'    => $outputs,
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_pagination_customizer_hover_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the border color on hover in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_hover_border_color_field( $args  = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_pagination_customizer_hover_border_color_elements', $this->hover_elements );
			$outputs  = apply_filters( 'grimlock_pagination_customizer_hover_border_color_outputs', array(
				$this->get_css_var_output( 'pagination_hover_border_color' ),
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'border-color',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Border Color on Hover', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => 'pagination_hover_border_color',
				'default'   => $this->get_default( 'pagination_hover_border_color' ),
				'choices'   => array(
					'alpha'    => true,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'output'    => $outputs,
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_pagination_customizer_hover_border_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the background color on hover in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_hover_background_color_field( $args  = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_pagination_customizer_hover_background_color_elements', $this->hover_elements );
			$outputs  = apply_filters( 'grimlock_pagination_customizer_hover_background_color_outputs', array(
				$this->get_css_var_output( 'pagination_hover_background_color' ),
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'background-color',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Background Color on Hover', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => 'pagination_hover_background_color',
				'default'   => $this->get_default( 'pagination_hover_background_color' ),
				'choices'   => array(
					'alpha'    => true,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'output'    => $outputs,
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_pagination_customizer_hover_background_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki slider field to set the horinzontal padding in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_padding_x_field( $args  = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_pagination_customizer_hover_padding_x_elements', $this->elements );
			$outputs  = apply_filters( 'grimlock_pagination_customizer_hover_padding_x_outputs', array(
				$this->get_css_var_output( 'pagination_padding_x', 'rem' ),
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'padding-left',
					'units'    => 'rem',
				),
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'padding-right',
					'units'    => 'rem',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'slider',
				'section'   => $this->section,
				'label'     => esc_attr__( 'Horizontal Padding', 'grimlock' ),
				'settings'  => 'pagination_padding_x',
				'default'   => $this->get_default( 'pagination_padding_x' ),
				'choices'   => array(
					'min'   => 0,
					'max'   => 5,
					'step'  => .1,
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'output'    => $outputs,
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_pagination_customizer_padding_x_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki slider field to set the vertical padding in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_padding_y_field( $args  = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_pagination_customizer_hover_padding_y_elements', $this->elements );
			$outputs  = apply_filters( 'grimlock_pagination_customizer_hover_padding_y_outputs', array(
				$this->get_css_var_output( 'pagination_padding_y', 'rem' ),
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'padding-top',
					'units'    => 'rem',
				),
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'padding-bottom',
					'units'    => 'rem',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'slider',
				'section'   => $this->section,
				'label'     => esc_attr__( 'Vertical Padding', 'grimlock' ),
				'settings'  => 'pagination_padding_y',
				'default'   => $this->get_default( 'pagination_padding_y' ),
				'choices'   => array(
					'min'   => 0,
					'max'   => 6,
					'step'  => .1,
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'output'    => $outputs,
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_pagination_customizer_padding_y_field_args', $args ) );
		}
	}
}

return new Grimlock_Pagination_Customizer();
