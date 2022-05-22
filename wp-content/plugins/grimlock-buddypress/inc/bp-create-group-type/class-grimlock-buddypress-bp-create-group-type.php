<?php
/**
 * Grimlock_BuddyPress_BP_Create_Group_Type Class
 *
 * @author  Themosaurus
 * @since   1.4.5
 * @package grimlock-buddypress
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Grimlock_BuddyPress_BP_Create_Group_Type
 *
 * @author  themosaurus
 * @since   1.4.5
 * @package grimlock-buddypress
 */
class Grimlock_BuddyPress_BP_Create_Group_Type {
	/**
	 * Setup class.
	 *
	 * @since 1.4.5
	 */
	public function __construct() {
		$bp_create_group_type_public = new Bp_Add_Group_Types_Public( 'bp-add-group-types', '1.0.0' );
		add_action( 'bp_groups_directory_group_filter', array( $bp_create_group_type_public, 'bb_display_directory_tabs' ), 5 );
	}
}
