<?php
/**
 * Grimlock_Custom_Header_Customizer Class
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
 * The Grimlock Customizer class for the Custom Header.
 */
class Grimlock_WordPress_SEO_Breadcrumb_Customizer extends Grimlock_Base_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		// Initialize value using Yoast SEO Breadcrumbs section ID.
		$this->section = 'wpseo_breadcrumbs_customizer_section';

		add_action( 'after_setup_theme',        array( $this, 'add_customizer_fields' ), 20, 1 );
		add_filter( 'grimlock_breadcrumb_args', array( $this, 'add_breadcrumb_args'   ), 10, 1 );
		add_filter( 'body_class',               array( $this, 'add_body_classes'      ), 10, 1 );
	}

	/**
	 * Add arguments using theme mods to customize the breadcrumb component.
	 *
	 * @param array $args The default arguments to render the breadcrumb.
	 *
	 * @return array      The arguments to render the breadcrumb.
	 */
	public function add_breadcrumb_args( $args ) {
		$args['displayed'] = $this->get_theme_mod( 'breadcrumb_custom_header_displayed' );
        return $args;
	}

	/**
	 * Add custom classes to section region in page.
	 *
	 * @param array $classes The array of classes.
	 * @since 1.0.0
	 *
	 * @return array The array of classes
	 */
	public function add_body_classes( $classes ) {
		if ( true == $this->get_theme_mod( 'breadcrumb_custom_header_displayed' ) ) {
			$classes[] = 'grimlock-wordpress-seo--breadcrumb_custom_header_displayed';
		}
		return $classes;
	}

	/**
	 * Register defaults, settings and custom controls for the Theme Customizer
	 * into Yoast SEO Breadcrumbs section.
	 *
	 * @since 1.0.0
	 */
	public function add_customizer_fields() {
		$this->defaults = apply_filters( 'grimlock_wordpress_seo_breadcrumb_customizer_defaults', array(
			'breadcrumb_custom_header_displayed' => false,
			'breadcrumb_color'                   => GRIMLOCK_BODY_COLOR,
			'breadcrumb_link_color'              => GRIMLOCK_LINK_COLOR,
			'breadcrumb_link_hover_color'        => GRIMLOCK_LINK_HOVER_COLOR,
		) );

		$this->add_divider_field(                 array( 'priority' => 200 ) );
		$this->add_heading_field(                 array( 'priority' => 200, 'label' => esc_html__( 'Display', 'grimlock-wordpress-seo' ) ) );
		$this->add_custom_header_displayed_field( array( 'priority' => 200 ) );
		$this->add_color_field(                   array( 'priority' => 210 ) );
		$this->add_link_color_field(              array( 'priority' => 220 ) );
		$this->add_link_hover_color_field(        array( 'priority' => 230 ) );
	}

	/**
	 * Add a Kirki color field to set the color for the breadcrumb in the Customizer.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args
	 */
	protected function add_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_wordpress_seo_breadcrumb_customizer_color_elements', array(
				'.yoast-breadcrumb'
			) );

			$outputs = apply_filters( 'grimlock_wordpress_seo_breadcrumb_customizer_color_outputs', array(
				$this->get_css_var_output( 'breadcrumb_color' ),
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'color',
					'suffix'   => '!important',
				),
			) );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Text Color', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => 'breadcrumb_color',
				'default'   => $this->get_default( 'breadcrumb_color' ),
				'alpha'     => false,
				'priority'  => 10,
				'transport' => 'postMessage',
				'output'    => $outputs,
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_wordpress_seo_breadcrumb_customizer_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the color for the breadcrumb links in the Customizer.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args
	 */
	protected function add_link_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_wordpress_seo_breadcrumb_customizer_link_color_elements', array(
				'.yoast-breadcrumb a'
			) );

			$outputs = apply_filters( 'grimlock_wordpress_seo_breadcrumb_customizer_link_color_outputs', array(
				$this->get_css_var_output( 'breadcrumb_link_color' ),
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'color',
					'suffix'   => '!important',
				),
			) );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Link Color', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => 'breadcrumb_link_color',
				'default'   => $this->get_default( 'breadcrumb_link_color' ),
				'alpha'     => false,
				'priority'  => 10,
				'transport' => 'postMessage',
				'output'    => $outputs,
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_wordpress_seo_breadcrumb_customizer_link_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the color for the breadcrumb hovered links in the Customizer.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args
	 */
	protected function add_link_hover_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_wordpress_seo_breadcrumb_customizer_link_hover_color_elements', array(
				'.yoast-breadcrumb a:hover'
			) );

			$outputs = apply_filters( 'grimlock_wordpress_seo_breadcrumb_customizer_link_hover_color_outputs', array(
				$this->get_css_var_output( 'breadcrumb_link_hover_color' ),
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'color',
					'suffix'   => '!important',
				),
			) );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Link Hover Color', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => 'breadcrumb_link_hover_color',
				'default'   => $this->get_default( 'breadcrumb_link_hover_color' ),
				'alpha'     => false,
				'priority'  => 10,
				'transport' => 'postMessage',
				'output'    => $outputs,
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_wordpress_seo_breadcrumb_customizer_link_hover_color_field_args', $args ) );
		}
	}


	/**
	 * Add a Kirki checkbox field to set the display for Yoast SEO breadcrumb
	 * in the Customizer for the Custom Header.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args
	 */
	protected function add_custom_header_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki') ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'checkbox',
				'section'  => $this->section,
				'label'    => esc_html__( 'Display breadcrumb in header', 'grimlock' ),
				'settings' => 'breadcrumb_custom_header_displayed',
				'default'  => $this->get_default( 'breadcrumb_custom_header_displayed' ),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_wordpress_seo_breadcrumb_customizer_custom_header_displayed_field_args', $args ) );
		}
	}
}

return new Grimlock_WordPress_SEO_Breadcrumb_Customizer();
