<?php
/**
 * Grimlock BuddyPress template hooks for BP Better Messages.
 *
 * @package grimlock-buddypress
 */

/**
 * Remove the message button that we add when BP Better Message adds its own
 */
if ( BP_Better_Messages()->settings['userListButton'] == '1' ) {
	remove_action( 'bp_directory_members_actions', 'grimlock_buddypress_member_send_message_button', 10 );
}
