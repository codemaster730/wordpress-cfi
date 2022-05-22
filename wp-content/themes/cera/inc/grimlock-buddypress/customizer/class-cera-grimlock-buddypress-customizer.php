<?php
/**
 * Cera_Grimlock_BuddyPress_Customizer Class
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
 * The Cera Customizer class for BuddyPress.
 */
class Cera_Grimlock_BuddyPress_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'grimlock_buddypress_customizer_defaults', array( $this, 'change_defaults'                                  ), 10, 1 );
		add_action( 'after_setup_theme',                       array( $this, 'remove_customizer_fields'                         ), 30, 0 );
		add_action( 'after_setup_theme',                       array( $this, 'apply_opacity_to_profile_header_background_color' ), 10, 0 );
	}

	/**
	 * Change default values and control settings for the Customizer.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $defaults The array of default values for the Customizer controls.
	 *
	 * @return array           The updated array of default values for the Customizer controls.
	 */
	public function change_defaults( $defaults ) {
		$defaults['default_profile_cover_image'] = CERA_DEFAULT_PROFILE_COVER_IMAGE;
		$defaults['default_group_cover_image']   = CERA_DEFAULT_GROUP_COVER_IMAGE;

		$defaults['friend_icons']                                  = 'heart';
		$defaults['member_actions_button_background_color']        = CERA_BUTTON_ACTION_BACKGROUND_COLOR;
		$defaults['friend_button_background_color']                = CERA_BUTTON_ACTION_LOVE_COLOR;
		$defaults['message_button_background_color']               = CERA_BUTTON_ACTION_MESSAGE_COLOR;
		$defaults['success_button_background_color']               = CERA_BUTTON_ACTION_SUCCESS_COLOR;
		$defaults['follow_button_background_color']                = CERA_BUTTON_ACTION_SUCCESS_COLOR;
		$defaults['delete_button_background_color']                = CERA_BUTTON_ACTION_DANGER_COLOR;
		$defaults['miscellaneous_actions_button_background_color'] = CERA_BUTTON_ACTION_MISC_COLOR;

		$defaults['members_actions_text_displayed'] = false;
		$defaults['groups_actions_text_displayed'] = false;

		$defaults['profile_header_background_color'] = version_compare( GRIMLOCK_BUDDYPRESS_VERSION, '1.4.0', '>=' ) ? CERA_PROFILE_HEADER_BACKGROUND_COLOR : CERA_GRAY_DARKEST;

		return $defaults;
	}

	/**
	 * Remove Customizer fields
	 */
	public function remove_customizer_fields() {
		if ( class_exists( 'Kirki' ) ) {
			Kirki::remove_control( 'friend_icons' );
			Kirki::remove_control( 'follow_icons' );
		}
	}

	/**
	 * Apply opacity to profile header background color for compatibility with the new Grimlock for BuddyPress version
	 *
	 * TODO: Remove this in future version
	 */
	public function apply_opacity_to_profile_header_background_color() {
		$profile_header_background_color_opacity_patch_applied = get_theme_mod( 'profile_header_background_color_opacity_patch_applied' );

		// Do this only once and only if the new Grimlock for BuddyPress version is installed
		if ( ! $profile_header_background_color_opacity_patch_applied && version_compare( GRIMLOCK_BUDDYPRESS_VERSION, '1.4.0', '>=' ) && class_exists( 'ariColor' ) ) {
			$profile_header_background_color = get_theme_mod( 'profile_header_background_color', CERA_PROFILE_HEADER_BACKGROUND_COLOR );

			// Set flag to avoid applying the patch more than once
			set_theme_mod( 'profile_header_background_color_opacity_patch_applied', true );

			// Apply the patch only if the value isn't default
			if ( $profile_header_background_color !== CERA_PROFILE_HEADER_BACKGROUND_COLOR ) {
				$profile_header_background_color_instance  = ariColor::new_color( $profile_header_background_color );
				$profile_header_background_color_new_alpha = round( $profile_header_background_color_instance->alpha * 0.45, 2 );
				$profile_header_background_color           = $profile_header_background_color_instance->get_new( 'alpha', $profile_header_background_color_new_alpha )->to_css( 'rgba' );
				set_theme_mod( 'profile_header_background_color', $profile_header_background_color );
			}
		}
	}
}

return new Cera_Grimlock_BuddyPress_Customizer();
