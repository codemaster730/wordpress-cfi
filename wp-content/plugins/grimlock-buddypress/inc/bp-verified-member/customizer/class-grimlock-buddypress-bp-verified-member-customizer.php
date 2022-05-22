<?php
/**
 * Grimlock_BuddyPress_BP_Verified_Member_Customizer Class
 *
 * @author  Themosaurus
 * @since   1.4.2
 * @package grimlock-buddypress
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The Grimlock Customizer class for the docs single pages.
 */
class Grimlock_BuddyPress_BP_Verified_Member_Customizer extends Grimlock_Base_Customizer {

	protected $id;

	/**
	 * Setup class.
	 *
	 * @since 1.4.2
	 */
	public function __construct() {
		$this->id      = 'bp_verified_member';
		$this->section = 'grimlock_buddypress_bp_verified_member_customizer_section';
		$this->title   = esc_html__( 'Verified Member', 'grimlock-buddypress' );

		add_action( 'after_setup_theme', array( $this, 'add_customizer_fields' ), 20 );
	}

	/**
	 * Register default values, settings and custom controls for the Theme Customizer.
	 *
	 * @since 1.4.2
	 */
	public function add_customizer_fields() {
		if ( class_exists( 'Kirki' ) ) {
			Kirki::add_config( 'grimlock_buddypress_bp_verified_member', array(
				'option_type' => 'option',
				'capability'  => 'edit_theme_options',
			) );
		}

		$this->defaults = apply_filters( 'grimlock_buddypress_bp_verified_member_customizer_defaults', array(
			'bp_verified_member_badge_color'            => '#1DA1F2',
			'bp_verified_member_unverified_badge_color' => '#DD9933',
		) );

		$this->add_section();
		$this->add_verified_badge_color_field(   array( 'priority' => 100 ) );
		$this->add_unverified_badge_color_field( array( 'priority' => 110 ) );
	}

	/**
	 * Add a Kirki section in the Customizer.
	 *
	 * @since 1.4.2
	 *
	 * @param array $args
	 */
	protected function add_section( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			Kirki::add_section( $this->section, apply_filters( "{$this->section}_args", array(
				'title'    => $this->title,
				'priority' => 30,
				'panel'    => 'grimlock_buddypress_customizer_panel',
			) ) );
		}
	}

	/**
	 * Add a Kirki color field in the Customizer to set the color of the verified badge
	 *
	 * @since 1.4.2
	 * @param array $args
	 */
	protected function add_verified_badge_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Verified badge color', 'grimlock-buddypress' ),
				'section'   => $this->section,
				'settings'  => "{$this->id}_badge_color",
				'default'   => $this->get_default( "{$this->id}_badge_color" ),
				'choices'   => array(
					'alpha'    => true,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
			) );

			Kirki::add_field( 'grimlock_buddypress_bp_verified_member', apply_filters( "grimlock_{$this->id}_customizer_badge_color_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki color field in the Customizer to set the color of the unverified badge
	 *
	 * @since 1.4.2
	 * @param array $args
	 */
	protected function add_unverified_badge_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Unverified badge color', 'grimlock-buddypress' ),
				'section'   => $this->section,
				'settings'  => "{$this->id}_unverified_badge_color",
				'default'   => $this->get_default( "{$this->id}_unverified_badge_color" ),
				'choices'   => array(
					'alpha'    => true,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
			) );

			Kirki::add_field( 'grimlock_buddypress_bp_verified_member', apply_filters( "grimlock_{$this->id}_customizer_unverified_badge_color_field_args", $args ) );
		}
	}
}

return new Grimlock_BuddyPress_BP_Verified_Member_Customizer();
