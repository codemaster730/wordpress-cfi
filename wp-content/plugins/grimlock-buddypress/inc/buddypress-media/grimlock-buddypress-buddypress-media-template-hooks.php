<?php
/**
 * Grimlock BuddyPress template hooks for BuddyPress media (Rtmedia).
 *
 * @package grimlock-buddypress
 */

/**
 * @see grimlock_buddypress_buddypress_media_member_featured_media()
 */
add_action( 'grimlock_buddypress_member_featured_media', 'grimlock_buddypress_buddypress_media_member_featured_media' );
