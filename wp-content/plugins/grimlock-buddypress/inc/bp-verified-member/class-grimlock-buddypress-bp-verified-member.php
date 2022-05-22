<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class Grimlock_BuddyPress_BP_Verified_Member
 *
 * @author  themoasaurus
 * @since   1.4.2
 * @package grimlock-buddypress
 */
class Grimlock_BuddyPress_BP_Verified_Member {
	/**
	 * Setup class.
	 *
	 * @since 1.4.2
	 */
	public function __construct() {
		require GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_PATH . 'inc/bp-verified-member/customizer/class-grimlock-buddypress-bp-verified-member-customizer.php';
	}
}
