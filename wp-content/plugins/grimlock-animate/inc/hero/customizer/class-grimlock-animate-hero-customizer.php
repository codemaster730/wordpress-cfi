<?php
/**
 * Grimlock_Animate_Hero_Customizer Class
 *
 * @author  Themosaurus
 * @since   1.0.0
 * @package grimlock-animate
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The Grimlock Customizer class for the Grimlock Hero Animate add-on.
 */
class Grimlock_Animate_Hero_Customizer extends Grimlock_Base_Customizer {
	/**
	 * Setup class.
	 *
	 * @param string $id The ID of the section in the Customizer.
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->section = 'grimlock_hero_customizer_section';

		add_filter( 'grimlock_hero_args',                   array( $this, 'add_args'                        ), 10, 1 );
		add_filter( 'grimlock_customizer_controls_js_data', array( $this, 'add_customizer_controls_js_data' ), 10, 1 );
		add_action( 'after_setup_theme',                    array( $this, 'add_customizer_fields'           ), 20    );
	}

	/**
	 * Add arguments using theme mods to customize the Hero.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args The default arguments to render the Hero.
	 *
	 * @return array      The arguments to render the Hero.
	 */
	public function add_args( $args ) {
		$args['background_parallax'] = $this->get_theme_mod( 'animate_hero_background_parallax' );
		$args['thumbnail_parallax']  = $this->get_theme_mod( 'animate_hero_thumbnail_parallax' );
		$args['content_parallax']    = $this->get_theme_mod( 'animate_hero_content_parallax' );
		$args['parallax_speed']      = $this->get_theme_mod( 'animate_hero_parallax_speed' );
		$args['thumbnail_reveal']    = $this->get_theme_mod( 'animate_hero_thumbnail_reveal' );
		$args['content_reveal']      = $this->get_theme_mod( 'animate_hero_content_reveal' );
		$args['reveal_reset']        = $this->get_theme_mod( 'animate_hero_reveal_reset' );
		$args['reveal_mobile']       = $this->get_theme_mod( 'animate_hero_reveal_mobile' );

		return $args;
	}

	/**
	 * Add tabs to the Customizer to group controls.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $js_data The array of data for the Customizer controls.
	 *
	 * @return array          The filtered array of data for the Customizer controls.
	 */
	public function add_customizer_controls_js_data( $js_data ) {
		$js_data['tabs'][$this->section][] = array(
			'label'    => esc_html__( 'Animations', 'grimlock-animate' ),
			'class'    => 'hero-animation-tab',
			'controls' => array(
				'animate_hero_background_parallax',
				'animate_hero_thumbnail_parallax',
				'animate_hero_content_parallax',
				'animate_hero_parallax_speed',
				'grimlock_hero_customizer_section_divider_640',
				'animate_hero_thumbnail_reveal',
				'animate_hero_content_reveal',
				'animate_hero_reveal_reset',
				'animate_hero_reveal_mobile',
			),
		);
		return $js_data;
	}

	/**
	 * Add settings and custom controls for the Theme Customizer.
	 *
	 * @since 1.0.0
	 */
	public function add_customizer_fields() {
		$this->defaults = apply_filters( 'grimlock_animate_hero_customizer_defaults', array(
			'animate_hero_background_parallax' => 'none',
			'animate_hero_thumbnail_parallax'  => 'none',
			'animate_hero_content_parallax'    => 'none',
			'animate_hero_parallax_speed'      => 0.2,
			'animate_hero_thumbnail_reveal'    => 'none',
			'animate_hero_content_reveal'      => 'none',
			'animate_hero_reveal_reset'        => false,
			'animate_hero_reveal_mobile'       => false,
		) );

		$this->add_background_parallax_field( array( 'priority' => 600 ) );
		$this->add_thumbnail_parallax_field(  array( 'priority' => 610 ) );
		$this->add_content_parallax_field(    array( 'priority' => 620 ) );
		$this->add_parallax_speed_field(      array( 'priority' => 630 ) );
		$this->add_divider_field(             array( 'priority' => 640 ) );
		$this->add_thumbnail_reveal_field(    array( 'priority' => 640 ) );
		$this->add_content_reveal_field(      array( 'priority' => 650 ) );
		$this->add_reveal_reset_field(        array( 'priority' => 660 ) );
		$this->add_reveal_mobile_field(       array( 'priority' => 670 ) );
	}

	/**
	 * Add a Kirki select control to set the parallax direction for the background in the Customizer.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args The array of arguments for the Kirki field.
	 */
	protected function add_background_parallax_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'      => 'select',
				'section'   => $this->section,
				'label'     => esc_attr__( 'Background Parallax', 'grimlock-animate' ),
				'settings'  => 'animate_hero_background_parallax',
				'default'   => $this->get_default( 'animate_hero_background_parallax' ),
				'choices'   => array(
					'none'     => esc_attr__( 'None', 'grimlock-animate' ),
					'natural'  => esc_attr__( 'Natural', 'grimlock-animate' ),
					'inverted' => esc_attr__( 'Inverted', 'grimlock-animate' ),
				),
				'priority'  => 10,
				'transport' => 'refresh',
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_animate_hero_customizer_background_parallax_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki select control to set the parallax direction for the featured image in the Customizer.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args The array of arguments for the Kirki field.
	 */
	protected function add_thumbnail_parallax_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'      => 'select',
				'section'   => $this->section,
				'label'     => esc_attr__( 'Thumbnail Parallax', 'grimlock-animate' ),
				'settings'  => 'animate_hero_thumbnail_parallax',
				'default'   => $this->get_default( 'animate_hero_thumbnail_parallax' ),
				'choices'   => array(
					'none'     => esc_attr__( 'None', 'grimlock-animate' ),
					'natural'  => esc_attr__( 'Natural', 'grimlock-animate' ),
					'inverted' => esc_attr__( 'Inverted', 'grimlock-animate' ),
				),
				'priority'  => 10,
				'transport' => 'refresh',
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_animate_hero_customizer_thumbnail_parallax_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki select control to set the parallax direction for the content in the Customizer.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args The array of arguments for the Kirki field.
	 */
	protected function add_content_parallax_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'      => 'select',
				'section'   => $this->section,
				'label'     => esc_attr__( 'Content Parallax', 'grimlock-animate' ),
				'settings'  => 'animate_hero_content_parallax',
				'default'   => $this->get_default( 'animate_hero_content_parallax' ),
				'choices'   => array(
					'none'     => esc_attr__( 'None', 'grimlock-animate' ),
					'natural'  => esc_attr__( 'Natural', 'grimlock-animate' ),
					'inverted' => esc_attr__( 'Inverted', 'grimlock-animate' ),
				),
				'priority'  => 10,
				'transport' => 'refresh',
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_animate_hero_customizer_content_parallax_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki slider control to set the parallax speed in the Customizer.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args The array of arguments for the Kirki field.
	 */
	protected function add_parallax_speed_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'      => 'slider',
				'section'   => $this->section,
				'label'     => esc_attr__( 'Parallax Speed', 'grimlock-animate' ),
				'settings'  => 'animate_hero_parallax_speed',
				'default'   => $this->get_default( 'animate_hero_parallax_speed' ),
				'choices'   => array(
					'min'   => 0,
					'max'   => 0.9,
					'step'  => .1,
				),
				'priority'  => 10,
				'transport' => 'refresh',
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_animate_hero_customizer_parallax_speed_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki select control to set the thumbnail reveal direction in the Customizer.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args The array of arguments for the Kirki field.
	 */
	protected function add_thumbnail_reveal_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'      => 'select',
				'section'   => $this->section,
				'label'     => esc_attr__( 'Thumbnail reveal', 'grimlock-animate' ),
				'settings'  => 'animate_hero_thumbnail_reveal',
				'default'   => $this->get_default( 'animate_hero_thumbnail_reveal' ),
				'choices'   => array(
					'none'   => esc_html__( 'None', 'grimlock-animate' ),
					'bottom' => esc_html__( 'Bottom', 'grimlock-animate' ),
					'top'    => esc_html__( 'Top', 'grimlock-animate' ),
					'left'   => esc_html__( 'Left', 'grimlock-animate' ),
					'right'  => esc_html__( 'Right', 'grimlock-animate' ),
					'fade'   => esc_html__( 'Fade', 'grimlock-animate' ),
				),
				'priority'  => 10,
				'transport' => 'refresh',
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_animate_hero_customizer_thumbnail_reveal_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki select control to set the content reveal direction in the Customizer.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args The array of arguments for the Kirki field.
	 */
	protected function add_content_reveal_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'      => 'select',
				'section'   => $this->section,
				'label'     => esc_attr__( 'Content reveal', 'grimlock-animate' ),
				'settings'  => 'animate_hero_content_reveal',
				'default'   => $this->get_default( 'animate_hero_content_reveal' ),
				'choices'   => array(
					'none'   => esc_html__( 'None', 'grimlock-animate' ),
					'bottom' => esc_html__( 'Bottom', 'grimlock-animate' ),
					'top'    => esc_html__( 'Top', 'grimlock-animate' ),
					'left'   => esc_html__( 'Left', 'grimlock-animate' ),
					'right'  => esc_html__( 'Right', 'grimlock-animate' ),
					'fade'   => esc_html__( 'Fade', 'grimlock-animate' ),
				),
				'priority'  => 10,
				'transport' => 'refresh',
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_animate_hero_customizer_content_reveal_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox field to set whether the reveal animation resets when the hero is out of the viewport in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_reveal_reset_field( $args = array() ) {
		if ( class_exists( 'Kirki') ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'checkbox',
				'section'  => $this->section,
				'label'    => esc_html__( 'Play reveal animations every time the element becomes visible', 'grimlock-animate' ),
				'settings' => 'animate_hero_reveal_reset',
				'default'  => $this->get_default( 'animate_hero_reveal_reset' ),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_animate_hero_customizer_reveal_reset_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox field to set whether the reveal animation plays on mobile in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_reveal_mobile_field( $args = array() ) {
		if ( class_exists( 'Kirki') ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'checkbox',
				'section'  => $this->section,
				'label'    => esc_html__( 'Play reveal animations on mobile', 'grimlock-animate' ),
				'settings' => 'animate_hero_reveal_mobile',
				'default'  => $this->get_default( 'animate_hero_reveal_mobile' ),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_animate_hero_customizer_reveal_mobile_field_args', $args ) );
		}
	}
}

return new Grimlock_Animate_Hero_Customizer();
