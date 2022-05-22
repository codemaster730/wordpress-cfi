<?php
/**
 * Grimlock_BuddyPress_BP_Featured_Members Class
 *
 * @author  Themosaurus
 * @since   1.3.11
 * @package grimlock-buddypress
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Grimlock_BuddyPress_BP_Featured_Members
 *
 * @author  themosaurus
 * @since   1.3.11
 * @package grimlock-buddypress
 */
class Grimlock_BuddyPress_BP_Featured_Members {
	/**
	 * Setup class.
	 *
	 * @since 1.3.11
	 */
	public function __construct() {
		add_filter( 'bp_get_member_class', array( $this, 'add_featured_member_class' ), 10, 1 );
		add_filter( 'body_class', array( $this, 'add_single_featured_member_class' ), 10, 1 );
	}

	/**
	 * Add a class on featured members
	 *
	 * @since 1.3.11
	 * @param array $classes The member classes
	 *
	 * @return array The modified array of classes
	 */
	public function add_featured_member_class( $classes ) {
		global $members_template;

		$user_id = $members_template->member->id;

		$is_featured = get_user_meta( $user_id, '_is_featured', true );

		if ( ! in_array( 'is-featured-member', $classes ) && ! empty( $is_featured ) ) {
			$classes[] = 'is-featured-member';
		}

		return $classes;
	}

	/**
	 * Add a class on the page body for featured members
	 *
	 * @since 1.3.11
	 * @param array $classes The body classes
	 *
	 * @return array The modified array of classes
	 */
	public function add_single_featured_member_class( $classes ) {
//		if ( ! is_ )

		$user_id = bp_displayed_user_id();

		$is_featured = get_user_meta( $user_id, '_is_featured', true );

		if ( ! in_array( 'is-featured-member', $classes ) && ! empty( $is_featured ) ) {
			$classes[] = 'is-featured-member';
		}

		return $classes;
	}
}
