<?php
/**
 * Cera_BP_Profile_Search_Grimlock_Hero_Customizer Class
 *
 * @author   Themosaurus
 * @since    1.0.0
 * @package grimlock
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The hero class for the Customizer.
 */
class Cera_BP_Profile_Search_Grimlock_Hero_Customizer extends Grimlock_Hero_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::__construct();
		add_filter( 'body_class', array( $this, 'add_body_classes' ), 10, 1 );
	}

	/**
	 * Add CSS classes to the body.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $classes The array of CSS classes to the body.
	 *
	 * @return array          The updated array of CSS classes to the body.
	 */
	public function add_body_classes( $classes ) {
		$form_color_scheme = $this->get_theme_mod( 'hero_form_color_scheme' );
		$classes[]         = "bps-form-home-{$form_color_scheme}";
		return $classes;
	}

	/**
	 * Register default values, settings and custom controls for the Theme Customizer.
	 *
	 * @since 1.0.0
	 */
	public function add_customizer_fields() {
		parent::add_customizer_fields();
		$this->defaults = apply_filters( 'cera_bp_profile_search_grimlock_hero_customizer_defaults', array_merge( $this->defaults, array(
			'hero_form_color_scheme' => CERA_HERO_COLOR_SCHEME,
		) ) );

		$this->add_divider_field(           array( 'priority' => 382 ) );
		$this->add_form_color_scheme_field( array( 'priority' => 382 ) );
	}

	/**
	 * Add tabs to the Customizer to group controls.
	 *
	 * @param  array $js_data The array of data for the Customizer controls.
	 *
	 * @return array          The filtered array of data for the Customizer controls.
	 */
	public function add_customizer_controls_js_data( $js_data ) {
		$js_data['tabs'][ $this->section ][2]['controls'][] = 'hero_form_color_scheme';
		return $js_data;
	}

	/**
	 * Add a Kirki radio-buttonset field to set the color scheme of the hero form in the Customizer.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args The array of arguments for the Kirki field.
	 */
	protected function add_form_color_scheme_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'radio-buttonset',
				'section'  => $this->section,
				'label'    => esc_html__( 'Form Color Scheme', 'cera' ),
				'settings' => 'hero_form_color_scheme',
				'default'  => $this->get_default( 'hero_form_color_scheme' ),
				'choices'  => array(
					'light' => esc_attr__( 'Light', 'cera' ),
					'dark'  => esc_attr__( 'Dark', 'cera' ),
					'none'  => esc_attr__( 'None', 'cera' ),
				),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'cera_grimlock_hero_customizer_form_color_scheme_field_args', $args ) );
		}
	}
}

return new Cera_BP_Profile_Search_Grimlock_Hero_Customizer();
