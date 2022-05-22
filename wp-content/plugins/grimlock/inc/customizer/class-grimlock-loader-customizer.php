<?php
/**
 * Grimlock_Loader_Customizer Class
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
class Grimlock_Loader_Customizer extends Grimlock_Base_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->section = 'grimlock_loader_customizer_section';
		$this->title   = esc_html__( 'Loader', 'grimlock' );

		add_action( 'after_setup_theme',            array( $this, 'add_customizer_fields'  ), 20    );
		add_action( 'wp_enqueue_scripts',           array( $this, 'enqueue_scripts'        ), 10    );

		add_filter( 'body_class',                   array( $this, 'add_body_classes'       ), 10, 1 );

		add_filter( 'grimlock_loader_args',         array( $this, 'add_loader_args'        ), 10, 1 );

		add_filter( 'grimlock_loader_js_data',      array( $this, 'get_js_data'            ), 10, 1 );

		add_filter( 'kirki_grimlock_dynamic_css',   array( $this, 'add_dynamic_css'        ), 10, 1 );
	}

	/**
	 * Add custom classes to body.
	 *
	 * @since 1.0.0
	 * @param $classes
	 *
	 * @return string
	 */
	public function add_body_classes( $classes ) {
		if ( true == $this->get_theme_mod( 'loader_fadein_displayed' ) ) {
			$classes[] = 'grimlock--loader-fadein-displayed';
		}
		return $classes;
	}

	/**
	 * Get loader the animation duration for the JS script.
	 *
	 * @param array $data The default data to transfer in the JS script
	 *
	 * @return array      The data to transfer in the JS script
	 * @since 1.0.0
	 */
	public function get_js_data( $data ) {
		$data['animation_duration'] = $this->get_theme_mod( 'loader_animation_duration' );
		return $data;
	}

	/**
	 * Register default values, settings and custom controls for the Theme Customizer.
	 *
	 * @since 1.0.0
	 */
	public function add_customizer_fields() {
		$this->defaults = apply_filters( 'grimlock_loader_customizer_defaults', array(
			'loader_fadein_displayed'          => true,
			'loader_fadein_animation_duration' => 500,
			'loader_displayed'                 => false,
			'loader_animation_duration'        => 1500,
			'loader_background_color'          => '#ffffff',
			'loader_color'                     => GRIMLOCK_BRAND_PRIMARY,
		) );

		$this->add_section(                         array( 'priority' => 1000 ) );

		$this->add_heading_field(                   array( 'priority' => 10, 'label' => esc_html__( 'Display', 'grimlock' ) ) );
		$this->add_displayed_field(                 array( 'priority' => 10   ) );
		$this->add_divider_field(                   array( 'priority' => 20   ) );
		$this->add_color_field(                     array( 'priority' => 20   ) );
		$this->add_background_color_field(          array( 'priority' => 30   ) );
		$this->add_divider_field(                   array( 'priority' => 40   ) );
		$this->add_animation_duration_field(        array( 'priority' => 40   ) );
		$this->add_divider_field(                   array( 'priority' => 50   ) );
		$this->add_heading_field(                   array( 'priority' => 50, 'label' => esc_html__( 'Fade In Animation', 'grimlock' ) ) );
		$this->add_fadein_displayed_field(          array( 'priority' => 50   ) );
		$this->add_fadein_animation_duration_field( array( 'priority' => 60   ) );
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
				'label'    => esc_html__( 'Display loader on page refresh', 'grimlock' ),
				'settings' => 'loader_displayed',
				'default'  => $this->get_default( 'loader_displayed' ),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_loader_customizer_displayed_field_args', $args ) );
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
			$args = wp_parse_args( $args, array(
				'type'     => 'color',
				'label'    => esc_html__( 'Symbol Color', 'grimlock' ),
				'section'  => $this->section,
				'settings' => 'loader_color',
				'default'  => $this->get_default( 'loader_color' ),
				'choices'  => array(
					'alpha'    => true,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority' => 10,
				'output'   => array( $this->get_css_var_output( 'loader_color' ) ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_loader_customizer_color_field_args', $args ) );
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

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Background Color', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => 'loader_background_color',
				'default'   => $this->get_default( 'loader_background_color' ),
				'choices'   => array(
					'alpha'    => false,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'output'    => array( $this->get_css_var_output( 'loader_background_color' ) ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_loader_customizer_background_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox field to set the fade in animation display in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_fadein_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki') ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'checkbox',
				'section'  => $this->section,
				'label'    => esc_html__( 'Display fade in animation', 'grimlock' ),
				'settings' => 'loader_fadein_displayed',
				'default'  => $this->get_default( 'loader_fadein_displayed' ),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_loader_customizer_fadein_displayed_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki slider control to set the fade in animation duration in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	public function add_fadein_animation_duration_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'slider',
				'section'  => $this->section,
				'label'    => esc_attr__( 'Fade In Animation Duration', 'grimlock' ),
				'settings' => 'loader_fadein_animation_duration',
				'description' => esc_html__( 'This is the Page Fade-in entrance duration.', 'grimlock' ),
				'default'  => $this->get_default( 'loader_fadein_animation_duration' ),
				'choices'  => array(
					'min'  => 0,
					'max'  => 5000,
					'step' => 250,
				),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_loader_customizer_fadein_animation_duration_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki slider control to set the animation duration in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	public function add_animation_duration_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'slider',
				'section'  => $this->section,
				'label'    => esc_attr__( 'Animation Duration', 'grimlock' ),
				'settings' => 'loader_animation_duration',
				'description' => esc_html__( 'Loader Display duration. The final rendering is not always loaded as it should be. Please test outside the customizer.', 'grimlock' ),
				'default'  => $this->get_default( 'loader_animation_duration' ),
				'choices'  => array(
					'min'  => 0,
					'max'  => 5000,
					'step' => 250,
				),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_loader_customizer_animation_duration_field_args', $args ) );
		}
	}

	/**
	 * Add arguments using theme mods to customize the Loader.
	 *
	 * @since 1.1.2
	 *
	 * @param array $args The default arguments to render the Loader.
	 *
	 * @return array      The arguments to render the Loader.
	 */
	public function add_loader_args( $args ) {
		$args['displayed'] = $this->get_theme_mod( 'loader_displayed' );
		return $args;
	}

	/**
	 * Add custom styles based on theme mods.
	 *
	 * @param string $styles The styles printed by Kirki
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	public function add_dynamic_css( $styles ) {
		if ( true == $this->get_theme_mod( 'loader_fadein_displayed' ) ) {
			$fadein_duration = $this->get_theme_mod( 'loader_fadein_animation_duration' ) / 1000;

			$styles .= "
			.grimlock .parallax-mirror,
		    .grimlock #site-wrapper {
				-webkit-animation-name: grimlock-fadeIn;
				animation-name: grimlock-fadeIn;
				-webkit-animation-duration: {$fadein_duration}s;
				animation-duration: {$fadein_duration}s;
				-webkit-animation-timing-function: ease-in-out;
                animation-timing-function: ease-in-out;
			}
			@-webkit-keyframes grimlock-fadeIn {
				from { opacity: 0; }
				to   { opacity: 1; }
			}
			@keyframes grimlock-fadeIn {
				from { opacity: 0; }
				to   { opacity: 1; }
			}";
		}

		return $styles;
	}

	/**
	 * Enqueue Grimlock integration scripts.
	 *
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {
		if ( true == $this->get_theme_mod( 'loader_displayed' ) ) {

			wp_enqueue_style( 'grimlock-loader-style', GRIMLOCK_PLUGIN_DIR_URL . 'assets/css/loader.css' );

			/*
			 * Load loader-rtl.css instead of style.css for RTL compatibility
			 */
			wp_style_add_data( 'grimlock-loader-style', 'rtl', 'replace' );

			wp_enqueue_script( 'grimlock-loader', GRIMLOCK_PLUGIN_DIR_URL . 'assets/js/loader.js', array( 'jquery' ), GRIMLOCK_VERSION, true );
			wp_localize_script( 'grimlock-loader', 'grimlock_loader', apply_filters( 'grimlock_loader_js_data', array(
				'animation_duration' => 2500,
			) ) );
		}
	}
}

return new Grimlock_Loader_Customizer();
