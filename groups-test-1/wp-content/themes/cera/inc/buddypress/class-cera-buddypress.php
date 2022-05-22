<?php
/**
 * Cera_BuddyPress Class
 *
 * @author   Themosaurus
 * @since    1.0.0
 * @package  cera
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Cera_BuddyPress' ) ) :
	/**
	 * The main Cera_BuddyPress class
	 */
	class Cera_BuddyPress {
		/**
		 * Setup class.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			add_filter( 'bp_core_fetch_avatar_no_grav',                                     '__return_true'                         , 10    );
			add_filter( 'bp_before_groups_cover_image_settings_parse_args',   array( $this, 'change_groups_cover_image_settings'   ), 10, 1 );
			add_filter( 'bp_before_members_cover_image_settings_parse_args',  array( $this, 'change_members_cover_image_settings'  ), 10, 1 );
			add_filter( 'bp_core_default_avatar_group',                       array( $this, 'change_default_avatar_group'          ), 10, 2 );
		}

		/**
		 * Change the default BP group avatar image.
		 *
		 * @param  string $avatar The URL for the default BP group avatar image.
		 * @param  array  $params The array of params for the default BP group avatar image.
		 *
		 * @return string         The updated URL for the BP group avatar image.
		 */
		public function change_default_avatar_group( $avatar, $params ) {
			$avatar = get_stylesheet_directory_uri() . '/assets/images/avatars/user-group.png';
			return $avatar;
		}

		/**
		 * Change the settings for the BuddyPress group cover image.
		 *
		 * @param  array $settings The array of default settings for the BuddyPress cover image.
		 *
		 * @return array           The array of settings for the BuddyPress cover image.
		 */
		public function change_groups_cover_image_settings( $settings = array() ) {
			$settings['default_cover'] = get_stylesheet_directory_uri() . '/assets/images/covers/group-cover.jpg';
			$settings['width']         = get_custom_header()->width;
			$settings['height']        = get_custom_header()->height;
			return $settings;
		}

		/**
		 * Change the settings for the BuddyPress cover image.
		 *
		 * @param array $settings The array of default settings for the BuddyPress cover image.
		 *
		 * @return array           The array of settings for the BuddyPress cover image.
		 */
		public function change_members_cover_image_settings( $settings = array() ) {
			$settings['default_cover'] = get_stylesheet_directory_uri() . '/assets/images/covers/member-cover.jpg';
			$settings['width']         = get_custom_header()->width;
			$settings['height']        = get_custom_header()->height;
			return $settings;
		}
	}
endif;

return new Cera_BuddyPress();
