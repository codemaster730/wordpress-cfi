<?php
/**
 * Grimlock template hooks for BuddyPress.
 *
 * @package grimlock-buddypress
 */

if ( bp_is_active( 'messages' ) ) :
	add_action( 'bp_directory_members_actions', 'grimlock_buddypress_member_send_message_button', 10 );
endif;

add_action( 'grimlock_buddypress_member_xprofile_name', 'grimlock_buddypress_member_xprofile_name', 10 );

add_action( 'grimlock_buddypress_member_swap_loop', 'grimlock_buddypress_member_swap_loop_template_part', 10 );
