<?php
/**
 * Grimlock_BuddyPress_Social_Articles Class
 *
 * @author   Themosaurus
 * @since    1.3.5
 * @package  grimlock-buddypress
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Grimlock_BuddyPress_Social_Articles
 *
 * @author  themosaurus
 * @since   1.3.5
 * @package grimlock-buddypress
 */
class Grimlock_BuddyPress_Social_Articles {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'author_link', array( $this, 'change_author_link_to_profile_link' ), 10, 3 );
	}

	/**
	 * Replace the author link by the BP profile link
	 *
	 * @param string $link The url to the author page
	 * @param int $author_id The id of the author user
	 * @param string $author_nicename The name of the author
	 *
	 * @return string The modified author link
	 */
	public function change_author_link_to_profile_link( $link, $author_id, $author_nicename ) {
		if ( empty( $author_id ) ) {
			return $link;
		}

		$profile_link = bp_core_get_user_domain( $author_id );

		if ( defined( 'SA_SLUG' ) ) {
			$profile_link .= SA_SLUG;
		}

		if ( empty( $profile_link ) ) {
			return $link;
		}

		return $profile_link;
	}
}

return new Grimlock_BuddyPress_Social_Articles();
