<?php
/**
 * Grimlock_Jetpack_Testimonial_Customizer Class
 *
 * @author   Themosaurus
 * @since 1.0.9
 * @package grimlock-jetpack
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The Grimlock Customizer class for Jetpack.
 */
class Grimlock_Jetpack_Testimonial_Customizer extends Grimlock_Base_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.9
	 */
	public function __construct() {
		$this->id      = 'jetpack_testimonial';
		$this->section = 'grimlock_jetpack_testimonial_section';
		$this->title   = esc_html__( 'Testimonials', 'grimlock-jetpack' );

		add_action( 'after_setup_theme', array( $this, 'add_customizer_fields' ), 20 );

		add_filter( 'body_class', array( $this, 'add_body_class' ), 10, 1 );
	}

	/**
	 * Register default values, settings and custom controls for the Theme Customizer.
	 *
	 * @since 1.0.9
	 */
	public function add_customizer_fields() {
		$this->defaults = apply_filters( 'grimlock_jetpack_testimonial_customizer_defaults', array(
			'jetpack_testimonial_permalink_enabled' => true,
		) );

		$this->add_section();

		$this->add_permalink_enabled_field( array( 'priority' => 10 ) );
	}

	/**
	 * Add a Kirki checkbox field in the Customizer to set whether the permalink is enabled on testimonials
	 *
	 * @since 1.0.9
	 *
	 * @param array $args The array of arguments for the Kirki field.
	 */
	protected function add_permalink_enabled_field( $args = array() ) {
		if ( class_exists( 'Kirki') ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'checkbox',
				'section'  => $this->section,
				'label'    => esc_html__( 'Enable Permalinks on Testimonials', 'grimlock-jetpack' ),
				'description' => esc_html__( "Uncheck if you don't want to be able to click on a testimonial to see the single testimonial page.", 'grimlock-jetpack' ),
				'settings' => 'jetpack_testimonial_permalink_enabled',
				'default'  => $this->get_default( 'jetpack_testimonial_permalink_enabled' ),
				'priority' => 20,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_jetpack_testimonial_permalink_enabled_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki section in the Customizer.
	 *
	 * @since 1.0.9
	 *
	 * @param array $args The array of arguments for the Kirki section.
	 */
	protected function add_section( $args = array() ) {
		if ( class_exists( 'Kirki' ) && class_exists( 'Jetpack_Testimonial' ) ) {
			$jetpack_testimonial = Jetpack_Testimonial::init();

			if ( $jetpack_testimonial->site_supports_custom_post_type() ) {
				Kirki::add_panel( 'grimlock_jetpack_customizer_panel', array(
					'priority' => 120,
					'title'    => esc_html__( 'Jetpack', 'grimlock-jetpack' ),
				) );

				Kirki::add_section( $this->section, apply_filters( "{$this->section}_args", array(
					'title'    => $this->title,
					'priority' => isset( $args['priority'] ) ? $args['priority'] : 10,
					'panel'    => 'grimlock_jetpack_customizer_panel',
				) ) );
			}
		}
	}

	/**
	 * Add body classes depending on customizer settings
	 *
	 * @param array $classes Array of body classes
	 *
	 * @return array
	 */
	public function add_body_class( $classes ) {
		if ( class_exists( 'Jetpack_Testimonial' ) ) {
			$jetpack_testimonial = Jetpack_Testimonial::init();

			if ( $jetpack_testimonial->site_supports_custom_post_type() && $this->get_theme_mod( 'jetpack_testimonial_permalink_enabled' ) ) {
				$classes[] = 'grimlock-jetpack--testimonial-permalink-enabled';
			}
		}
		return $classes;
	}
}

return new Grimlock_Jetpack_Testimonial_Customizer();
