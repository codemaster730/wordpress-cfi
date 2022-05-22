<?php
/**
 * Grimlock_Back_To_Top_Button_Customizer Class
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
class Grimlock_Back_To_Top_Button_Customizer extends Grimlock_Base_Customizer {

	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->section = 'grimlock_back_to_top_button_customizer_section';
		$this->title   = esc_html__( 'Back To Top Button', 'grimlock' );

		add_filter( 'body_class',                       array( $this, 'add_body_classes'      ), 10, 1 );
		add_filter( 'grimlock_back_to_top_button_args', array( $this, 'add_args'              ), 10, 1 );

		add_action( 'after_setup_theme',                array( $this, 'add_customizer_fields' ), 20    );
		add_action( 'wp_enqueue_scripts',               array( $this, 'enqueue_scripts'       ), 10    );
	}

	/**
	 * Add arguments using theme mods to customize the button component.
	 *
	 *
	 * @return array      The arguments to render the button.
	 */
	public function add_args( $args ) {
		$args['displayed'] = $this->get_theme_mod( 'back_to_top_button_displayed' );
		return $args;
	}

	/**
	 * Add custom classes to body to modify layout.
	 *
	 * @param $classes
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function add_body_classes( $classes ) {
		$classes[] = "grimlock--back-to-top-{$this->get_theme_mod( 'back_to_top_button_position' )}";

		if ( ! empty( $this->get_theme_mod( 'back_to_top_button_displayed' ) ) ) {
			$classes[] = "grimlock--back-to-top-displayed";
		}

		return $classes;
	}

	/**
	 * Register default values, settings and custom controls for the Theme Customizer.
	 *
	 * @since 1.0.0
	 */
	public function add_customizer_fields() {
		$this->defaults = apply_filters( 'grimlock_back_to_top_button_customizer_defaults', array(
			'back_to_top_button_displayed'        => true,
			'back_to_top_button_position'         => 'right',
			'back_to_top_button_border_radius'    => GRIMLOCK_BORDER_RADIUS,
			'back_to_top_button_padding'          => 1, // rem
			'back_to_top_button_background_color' => 'rgba(0,0,0,0.25)',
			'back_to_top_button_color'            => '#ffffff',
			'back_to_top_button_border_color'     => 'rgba(0,0,0,0.2)',
			'back_to_top_button_border_width'     => GRIMLOCK_BORDER_WIDTH,
		) );

		$this->add_section(                array( 'priority' => 1100 ) );

		$this->add_heading_field(          array( 'priority' => 10, 'label' => esc_html__( 'Display', 'grimlock' ) ) );
		$this->add_displayed_field(        array( 'priority' => 10   ) );
		$this->add_divider_field(          array( 'priority' => 20   ) );
		$this->add_position_field(         array( 'priority' => 20   ) );
		$this->add_divider_field(          array( 'priority' => 30   ) );
		$this->add_border_radius_field(    array( 'priority' => 30   ) );
		$this->add_border_width_field(     array( 'priority' => 40   ) );
		$this->add_divider_field(          array( 'priority' => 50   ) );
		$this->add_padding_field(          array( 'priority' => 50   ) );
		$this->add_divider_field(          array( 'priority' => 60   ) );
		$this->add_background_color_field( array( 'priority' => 60   ) );
		$this->add_color_field(            array( 'priority' => 70   ) );
		$this->add_border_color_field(     array( 'priority' => 80   ) );
	}

	/**
	 * Add a Kirki checkbox field to set the component display in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki') ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'checkbox',
				'section'  => $this->section,
				'label'    => esc_html__( 'Display in pages', 'grimlock' ),
				'settings' => 'back_to_top_button_displayed',
				'default'  => $this->get_default( 'back_to_top_button_displayed' ),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_back_to_top_button_customizer_displayed_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the color in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$output = array( $this->get_css_var_output( 'back_to_top_button_color' ) );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Text Color', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => 'back_to_top_button_color',
				'default'   => $this->get_default( 'back_to_top_button_color' ),
				'choices'   => array(
					'alpha'    => true,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'js_vars'   => $this->to_js_vars( $output ),
				'output'    => $output,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_back_to_top_button_customizer_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the background color in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_background_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$output = array( $this->get_css_var_output( 'back_to_top_button_background_color' ) );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Background Color', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => 'back_to_top_button_background_color',
				'default'   => $this->get_default( 'back_to_top_button_background_color' ),
				'choices'   => array(
					'alpha'    => true,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'js_vars'   => $this->to_js_vars( $output ),
				'output'    => $output,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_back_to_top_button_customizer_background_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the border color in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_border_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$output = array( $this->get_css_var_output( 'back_to_top_button_border_color' ) );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Border Color', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => 'back_to_top_button_border_color',
				'default'   => $this->get_default( 'back_to_top_button_border_color' ),
				'choices'   => array(
					'alpha'    => true,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'js_vars'   => $this->to_js_vars( $output ),
				'output'    => $output,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_back_to_top_button_customizer_border_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox field to stick the navigation to top (or not) in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	public function add_position_field( $args = array() ) {
		if ( class_exists( 'Kirki') ) {
			$args = wp_parse_args( $args, array(
				'type'      => 'radio',
				'label'     => esc_html__( 'Position', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => 'back_to_top_button_position',
				'default'   => $this->get_default( 'back_to_top_button_position' ),
				'priority'  => 10,
				'choices'   => array(
					'left'  => esc_attr__( 'Left', 'grimlock' ),
					'right' => esc_attr__( 'Right', 'grimlock' ),
				),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_back_to_top_button_customizer_position_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki slider field to set the padding in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_padding_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$output = array( $this->get_css_var_output( 'back_to_top_button_padding', 'rem' ) );

			$args = wp_parse_args( $args, array(
				'type'      => 'slider',
				'section'   => $this->section,
				'label'     => esc_attr__( 'Padding', 'grimlock' ),
				'settings'  => 'back_to_top_button_padding',
				'default'   => $this->get_default( 'back_to_top_button_padding' ),
				'choices'   => array(
					'min'   => 0,
					'max'   => 5,
					'step'  => .25,
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'js_vars'   => $this->to_js_vars( $output ),
				'output'    => $output,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_back_to_top_button_customizer_padding_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki slider control to set the border radius in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_border_radius_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$output = array( $this->get_css_var_output( 'back_to_top_button_border_radius', 'rem' ) );

			$args = wp_parse_args( $args, array(
				'type'      => 'slider',
				'section'   => $this->section,
				'label'     => esc_attr__( 'Border Radius', 'grimlock' ),
				'settings'  => 'back_to_top_button_border_radius',
				'default'   => $this->get_default( 'back_to_top_button_border_radius' ),
				'choices'   => array(
					'min'   => 0,
					'max'   => 10,
					'step'  => .05,
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'js_vars'   => $this->to_js_vars( $output ),
				'output'    => $output,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_back_to_top_button_customizer_border_radius_field_args', $args ) );
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
			$output = array( $this->get_css_var_output( 'back_to_top_button_border_width', 'px' ) );

			$args = wp_parse_args( $args, array(
				'type'      => 'slider',
				'section'   => $this->section,
				'label'     => esc_attr__( 'Border Width', 'grimlock' ),
				'settings'  => 'back_to_top_button_border_width',
				'default'   => $this->get_default( 'back_to_top_button_border_width' ),
				'choices'   => array(
					'min'   => 0,
					'max'   => 10,
					'step'  => 1,
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'js_vars'   => $this->to_js_vars( $output ),
				'output'    => $output,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_back_to_top_button_customizer_border_width_field_args', $args ) );
		}
	}

	/**
	 * Enqueue scripts for the back to top button.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {
		if ( true == $this->get_theme_mod( 'back_to_top_button_displayed' ) ) {
			wp_enqueue_script( 'grimlock-back-to-top-button', GRIMLOCK_PLUGIN_DIR_URL . 'assets/js/back-to-top-button.js', array( 'jquery' ), GRIMLOCK_VERSION, true );
		}
	}
}

return new Grimlock_Back_To_Top_Button_Customizer();
