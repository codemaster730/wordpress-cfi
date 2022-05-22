<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class Grimlock_BuddyPress_BP_Profile_Search
 *
 * @author  themoasaurus
 * @since   1.0.0
 * @package grimlock-buddypress
 */
class Grimlock_BuddyPress_BP_Profile_Search {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'bps_templates',                        array( $this, 'replace_bps_templates' ), 0  );
		add_action( 'wp_ajax_load_member_swap_page',        array( $this, 'load_member_swap_page' ), 10 );
		add_action( 'wp_ajax_nopriv_load_member_swap_page', array( $this, 'load_member_swap_page' ), 10 );
	}

	/**
	 * Remove existing template and create custom form template for BPS.
	 *
	 * @since 1.0.0
	 *
	 * @param array $templates The array of templates.
	 *
	 * @return array           The new array of templates.
	 */
	public function replace_bps_templates( $templates ) {
		return array( 'members/bps-form-directory', 'members/bps-form-home' );
	}


	/**
	 * Load more members for the Member Swap page template.
	 *
	 * @since 1.0.5
	 */
	public function load_member_swap_page() {
		if ( ! empty( intval( $_POST['page'] ) ) ) {
			if ( bp_has_members( bp_ajax_querystring( 'members' ) . '&per_page=20&page=' . intval( wp_unslash( $_POST['page'] ) ) ) ) {
				ob_start();
				grimlock_buddypress_member_swap_loop_template_part();
				wp_send_json_success( ob_get_clean() );
			}
		}
		wp_die();
	}

}
