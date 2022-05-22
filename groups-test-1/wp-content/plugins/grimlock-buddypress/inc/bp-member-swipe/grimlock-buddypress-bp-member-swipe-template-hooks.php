<?php
/**
 * Grimlock template hooks for BP Member Swipe.
 *
 * @package grimlock-buddypress
 */

add_action( 'bp_member_swipe_member_before_meta', 'grimlock_buddypress_bp_member_swipe_member_xprofile_fields', 10 );

add_action( 'bp_member_swipe_member_after_actions', 'grimlock_buddypress_actions_dropdown', 10 );
