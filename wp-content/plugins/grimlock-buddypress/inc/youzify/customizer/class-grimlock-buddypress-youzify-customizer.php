<?php
/**
 * Grimlock_BuddyPress_Youzify_Customizer Class
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
 * The background image class for the Customizer.
 */
class Grimlock_BuddyPress_Youzify_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		global $grimlock_buddypress_members_customizer;
		remove_action( 'grimlock_buddypress_member_xprofile_custom_fields', array( $grimlock_buddypress_members_customizer, 'add_member_custom_fields' ), 10    );
		add_action( 'grimlock_buddypress_member_xprofile_custom_fields',    array( $this,                                   'add_member_custom_fields' ), 10, 1 );

		add_action( 'after_setup_theme', array( $this, 'remove_customizer_fields' ), 30 );

		add_filter( 'grimlock_template_sidebar_left_displayed',  array( $this, 'hide_sidebars' ), 100 );
		add_filter( 'grimlock_template_sidebar_right_displayed', array( $this, 'hide_sidebars' ), 100 );
	}

	/**
	 * Display xprofile fields in members using youzify function
	 *
	 * @param int $user_id The id of the user.
	 */
	public function add_member_custom_fields( $user_id ) {
		if ( empty( $user_id ) ) {
			$user_id = bp_get_member_user_id();
		}

		if ( empty( $user_id ) ) {
			$user_id = bp_displayed_user_id();
		}

		if ( function_exists( 'youzify_get_md_user_meta' ) ) {
			echo wp_kses( youzify_get_md_user_meta( $user_id ), array_merge( wp_kses_allowed_html( 'user_description' ), array( 'i' => array( 'class' => true ) ) ) );
		}
	}

	/**
	 * Remove Customizer fields
	 */
	public function remove_customizer_fields() {
		if ( class_exists( 'Kirki') ) {
			Kirki::remove_control( 'members_displayed_profile_fields' );
		}

		if ( class_exists( 'Kirki' ) && class_exists( 'Grimlock_Login' ) ) {
			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_buddypress_youzify_login_customizer_heading_1_args', array(
				'type'        => 'heading',
				'settings'    => 'grimlock_buddypress_youzify_login_customizer_heading_1',
				'section'     => 'grimlock_login_customizer_section',
				'description' => esc_html__( "Heads Up ! We have detected that you are using Youzify. Please note that this Customizer panel doesn't modify the Youzify login form, it will only modify the default WordPress login.", 'grimlock-buddypress' ),
				'priority'    => 1,
			) ) );
		}
	}

	/**
	 * Hide sidebars in youzify directories
	 *
	 * @param bool $displayed Whether the sidebar should be displayed
	 *
	 * @return bool
	 */
	public function hide_sidebars( $displayed ) {
		if ( bp_is_directory() || bp_is_activity_directory() || bp_is_groups_directory() ) {
			return false;
		}

		return $displayed;
	}
}

return new Grimlock_BuddyPress_Youzify_Customizer();
