<?php
/**
 * Grimlock template functions for BP Member Swipe.
 *
 * @package grimlock-buddypress
 */

if ( ! function_exists( 'grimlock_buddypress_bp_member_swipe_member_xprofile_fields' ) ) :
	/**
	 * Display member xprofile fields
	 */
	function grimlock_buddypress_bp_member_swipe_member_xprofile_fields() {
		?>

		<div class="bp-member-xprofile-custom-fields"><?php do_action( 'grimlock_buddypress_member_xprofile_custom_fields' ); ?></div> <!-- .bp-member-xprofile-custom-fields -->

		<?php
	}

endif;
