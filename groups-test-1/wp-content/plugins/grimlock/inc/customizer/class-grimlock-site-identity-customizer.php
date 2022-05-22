<?php
/**
 * Grimlock_Site_Identity_Customizer Class
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
 * The Grimlock Customizer site identity class.
 */
class Grimlock_Site_Identity_Customizer extends Grimlock_Base_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->section = 'title_tagline';

		add_action( 'customize_register',                   array( $this, 'customize_register'              ), 20    );
		add_action( 'after_setup_theme',                    array( $this, 'add_customizer_fields'           ), 20    );
		add_filter( 'grimlock_customizer_controls_js_data', array( $this, 'add_customizer_controls_js_data' ), 10, 1 );
		add_filter( 'grimlock_site_identity_args',          array( $this, 'add_args'                        ), 10, 1 );
		add_filter( 'body_class',                           array( $this, 'add_body_classes'                ), 10, 1 );
	}

	/**
	 * Add arguments to the Site Identity component.
	 *
	 * @since 1.0.0
	 * @param $args
	 *
	 * @return array
	 */
	public function add_args( $args ) {
		return wp_parse_args( array(
			'custom_logo'               => function_exists( 'get_custom_logo' ) ? get_custom_logo() : '',
			'custom_logo_displayed'     => $this->get_theme_mod( 'site_identity_custom_logo_displayed' ),
			'blogname_displayed'        => $this->get_theme_mod( 'site_identity_blogname_displayed' ),
			'blogdescription_displayed' => $this->get_theme_mod( 'site_identity_blogdescription_displayed' ),
		), $args );
	}

	/**
	 * Add custom classes to navigation to adapt when site title and/or tagline are shown.
	 *
	 * @since 1.0.0
	 * @param $classes array The array of classes.
	 *
	 * @return array         The filtred array of classes.
	 */
	public function add_body_classes( $classes ) {
		if ( $this->get_theme_mod( 'site_identity_blogname_displayed' ) ) {
			$classes[] = 'grimlock--blogname-displayed';
		}

		if ( $this->get_theme_mod( 'site_identity_blogdescription_displayed' ) ) {
			$classes[] = 'grimlock--blogdescription-displayed ';
		}
		return $classes;
	}

	/**
	 * Add settings and custom controls for the Theme Customizer.
	 *
	 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
	 * @since 1.0.0
	 */
	public function customize_register( $wp_customize ) {
		// Change the `title_tagline` section priority.
		$wp_customize->get_section( $this->section )->priority = 10;

		// Change the custom logo control priority.
		$wp_customize->get_control( 'custom_logo' )->priority = 10;

		// Add postMessage support for site title for the Theme Customizer.
		$wp_customize->get_setting( 'blogname' )->transport = 'postMessage';
		$wp_customize->get_control( 'blogname' )->priority  = 100;

		// Remove unnecessary checkbox to replace it with others.
		$wp_customize->remove_control( 'display_header_text');

		// Add postMessage support for tagline for the Theme Customizer.
		$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';
		$wp_customize->get_control( 'blogdescription')->priority   = 200;

		// Change the site icon control priority.
		$wp_customize->get_control( 'site_icon' )->priority = 300;
	}

	/**
	 * Register default values, settings and custom controls for the Theme Customizer.
	 *
	 * @since 1.0.0
	 */
	public function add_customizer_fields() {
		$this->defaults = apply_filters( 'grimlock_site_identity_customizer_defaults', array(
			'site_identity_custom_logo_displayed'     => false,
			'site_identity_custom_logo_size'          => 125,

			'site_identity_blogname_displayed'        => true,
			'site_identity_blogname_font'             => array(
				'font-family'                         => GRIMLOCK_FONT_FAMILY_SANS_SERIF,
				'font-weight'                         => 'regular',
				'font-size'                           => '1.25rem',
				'line-height'                         => GRIMLOCK_LINE_HEIGHT,
				'letter-spacing'                      => GRIMLOCK_LETTER_SPACING,
				'subsets'                             => array( 'latin-ext' ),
				'text-transform'                      => 'none',
			),
			'site_identity_blogname_color'            => GRIMLOCK_BODY_COLOR,
			'site_identity_blogname_hover_color'      => GRIMLOCK_NAVIGATION_ITEM_COLOR,

			'site_identity_blogdescription_displayed' => true,
		) );

		$this->add_custom_logo_displayed_field(     array( 'priority' => 20  ) );
		$this->add_custom_logo_size_field(          array( 'priority' => 30  ) );

		$this->add_blogname_displayed_field(        array( 'priority' => 110 ) );
		$this->add_blogname_font_field(             array( 'priority' => 120 ) );
		$this->add_blogname_color_field(            array( 'priority' => 130 ) );
		$this->add_blogname_hover_color_field(      array( 'priority' => 140 ) );

		$this->add_blogdescription_displayed_field( array( 'priority' => 210 ) );
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
				'label' => esc_html__( 'Logo', 'grimlock' ),
				'class' => 'title_tagline-custom_logo-tab',
				'controls' => array(
					'custom_logo',
					'site_identity_custom_logo_displayed',
					'site_identity_custom_logo_size',
				),
			),
			array(
				'label' => esc_html__( 'Title', 'grimlock' ),
				'class' => 'title_tagline-blogname-tab',
				'controls' => array(
					'blogname',
					'site_identity_blogname_displayed',
					'site_identity_blogname_font',
					'site_identity_blogname_color',
					'site_identity_blogname_hover_color',
				),
			),
			array(
				'label' => esc_html__( 'Tagline', 'grimlock' ),
				'class' => 'title_tagline-blogdescription-tab',
				'controls' => array(
					'blogdescription',
					'site_identity_blogdescription_displayed',
				),
			),
			array(
				'label' => esc_html__( 'Icon', 'grimlock' ),
				'class' => 'title_tagline-site_icon-tab',
				'controls' => array(
					'site_icon',
				),
			),
		);
		return $js_data;
	}

	/**
	 * Add a Kirki typography field to set the typography in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_blogname_font_field( $args = array() ) {
		if ( class_exists( 'Kirki') ) {
			$elements = apply_filters( 'grimlock_site_identity_customizer_blogname_font_elements', array(
				'.grimlock-site_identity .site-title',
				'.grimlock-site_identity .site-title a.site-title-link',
			) );

			$outputs = apply_filters( 'grimlock_site_identity_customizer_blogname_font_outputs', array(
				array(
					'element'  => implode( ',', $elements ),
				),
				array(
					'element'       => $elements,
					'property'      => 'font-family',
					'choice'        => 'font-family',
					'value_pattern' => '$, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji"',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'typography',
				'settings'  => 'site_identity_blogname_font',
				'label'     => esc_attr__( 'Typography', 'grimlock' ),
				'section'   => $this->section,
				'default'   => $this->get_default( 'site_identity_blogname_font' ),
				'priority'  => 120,
				'transport' => 'postMessage',
				'output'    => $outputs,
				'css_vars'  => $this->get_typography_css_vars( 'site_identity_blogname_font' ),
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_site_identity_customizer_blogname_font_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the color in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_blogname_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_site_identity_customizer_blogname_color_elements', array(
				'.grimlock-site_identity .site-title',
				'.grimlock-site_identity .site-title a.site-title-link',
			) );

			$outputs = apply_filters( 'grimlock_site_identity_customizer_blogname_color_outputs', array(
				$this->get_css_var_output( 'site_identity_blogname_color' ),
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'color',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Color', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => 'site_identity_blogname_color',
				'default'   => $this->get_default( 'site_identity_blogname_color' ),
				'choices'   => array(
					'alpha'    => true,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 130,
				'transport' => 'postMessage',
				'output'    => $outputs,
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_site_identity_customizer_blogname_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the color in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_blogname_hover_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_site_identity_customizer_blogname_color_elements', array(
				'.grimlock-site_identity .site-title a.site-title-link:hover',
				'.grimlock-site_identity .site-title a.site-title-link:active',
				'.grimlock-site_identity .site-title a.site-title-link:focus',
			) );

			$outputs = apply_filters( 'grimlock_site_identity_customizer_blogname_color_outputs', array(
				$this->get_css_var_output( 'site_identity_blogname_hover_color' ),
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'color',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Color on Hover', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => 'site_identity_blogname_hover_color',
				'default'   => $this->get_default( 'site_identity_blogname_hover_color' ),
				'choices'   => array(
					'alpha'    => true,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 140,
				'transport' => 'postMessage',
				'output'    => $outputs,
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_site_identity_customizer_blogname_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox field to set the site title display in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_blogname_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki') ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'checkbox',
				'section'  => $this->section,
				'label'    => esc_html__( 'Display site title', 'grimlock' ),
				'settings' => 'site_identity_blogname_displayed',
				'default'  => $this->get_default( 'site_identity_blogname_displayed' ),
				'priority' => 110,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_site_identity_customizer_blogname_displayed_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox field to set the site logo display in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_custom_logo_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki') ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'checkbox',
				'section'  => $this->section,
				'label'    => esc_html__( 'Display logo', 'grimlock' ),
				'settings' => 'site_identity_custom_logo_displayed',
				'default'  => $this->get_default( 'site_identity_custom_logo_displayed' ),
				'priority' => 20,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_site_identity_customizer_custom_logo_displayed_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki slider control to set the for the custom logo.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_custom_logo_size_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_site_identity_customizer_custom_logo_size_elements', array(
				'.grimlock-site_identity .site-logo img',
			) );

			$outputs  = apply_filters( 'grimlock_site_identity_customizer_custom_logo_size_outputs', array(
				$this->get_css_var_output( 'site_identity_custom_logo_size', 'px' ),
				array(
					'element'   => implode( ',', $elements ),
					'property'  => 'max-width',
					'units'     => 'px',
				),
				array(
					'element'   => implode( ',', $elements ),
					'property'  => 'max-height',
					'units'     => 'px',
				),
			), $elements );

			$custom_logo_args = get_theme_support( 'custom-logo' );

			$args = wp_parse_args( $args, array(
				'type'      => 'slider',
				'section'   => $this->section,
				'label'     => esc_attr__( 'Logo Size', 'grimlock' ),
				'settings'  => 'site_identity_custom_logo_size',
				'default'   => $this->get_default( 'site_identity_custom_logo_size' ),
				'choices'   => array(
					'min'   => 0,
					'max'   => isset( $custom_logo_args[0]['width'] ) ? $custom_logo_args[0]['width'] : 125,
					'step'  => 5,
				),
				'priority'  => 10,
				'output'    => $outputs,
				'transport' => 'postMessage',
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_site_identity_customizer_custom_logo_size_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox field to set the site title display in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_blogdescription_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki') ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'checkbox',
				'section'  => $this->section,
				'label'    => esc_html__( 'Display tagline', 'grimlock' ),
				'settings' => 'site_identity_blogdescription_displayed',
				'default'  => $this->get_default( 'site_identity_blogdescription_displayed' ),
				'priority' => 210,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_site_identity_customizer_blogdescription_displayed_field_args', $args ) );
		}
	}
}

return new Grimlock_Site_Identity_Customizer();
