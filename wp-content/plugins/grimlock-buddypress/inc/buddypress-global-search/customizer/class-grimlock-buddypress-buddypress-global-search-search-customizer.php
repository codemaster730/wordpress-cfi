<?php
/**
 * Grimlock_BuddyPress_BuddyPress_Global_Search_Search_Customizer Class
 *
 * @author  Themosaurus
 * @since   1.3.19
 * @package grimlock-buddypress
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The Grimlock BuddyPress Customizer class for the search page.
 */
class Grimlock_BuddyPress_BuddyPress_Global_Search_Search_Customizer {

	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'after_setup_theme', array( $this, 'remove_customizer_fields' ), 30 );
	}

	/**
	 * Remove some customizer fields
	 */
	public function remove_customizer_fields() {
		Kirki::remove_control( 'search_posts_layout' );
		Kirki::remove_control( 'search_posts_height_equalized' );
	}
}

return new Grimlock_BuddyPress_BuddyPress_Global_Search_Search_Customizer();
