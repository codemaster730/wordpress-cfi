<?php

/**
 * Handle saving feature settings.
 */
class EPKB_Settings_Controller {

	const EPKB_DEBUG = 'epkb_debug';

	public function __construct() {
		add_action( 'admin_init', array( $this, 'download_debug_info' ) );

		add_action( 'wp_ajax_epkb_toggle_debug', array( $this, 'toggle_debug' ) );
		add_action( 'wp_ajax_nopriv_epkb_toggle_debug', array( 'EPKB_Utilities', 'user_not_logged_in' ) );

		add_action( 'wp_ajax_epkb_enable_advanced_search_debug', array( $this, 'enable_advanced_search_debug' ) );
		add_action( 'wp_ajax_nopriv_epkb_enable_advanced_search_debug', array( 'EPKB_Utilities', 'user_not_logged_in' ) );
	}

	/**
	 * Triggered when user clicks to toggle debug.
	 */
	public function toggle_debug() {

		// wp_die if nonce invalid or user does not have correct permission
		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die();

		$is_debug_on = EPKB_Utilities::get_wp_option( EPKB_Settings_Controller::EPKB_DEBUG, false );

		$is_debug_on = empty($is_debug_on) ? 1 : 0;

		EPKB_Utilities::save_wp_option( EPKB_Settings_Controller::EPKB_DEBUG, $is_debug_on, true );

		if ( ! $is_debug_on ) {
			delete_transient( '_epkb_advanced_search_debug_activated' );
		}

		// we are done here
		EPKB_Utilities::ajax_show_info_die( __( 'Debug is now ' . ( $is_debug_on ? 'on' : 'off' ), 'echo-knowledge-base' ) );
	}

	/**
	 * Triggered when user clicks to toggle Advanced Search debug.
	 */
	public function enable_advanced_search_debug() {

		// wp_die if nonce invalid or user does not have correct permission
		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die();

		set_transient( '_epkb_advanced_search_debug_activated', true, HOUR_IN_SECONDS );

		// we are done here
		EPKB_Utilities::ajax_show_info_die( __( 'Debug for Advanced Search temporarily activated.', 'echo-knowledge-base' ) );
	}

	/**
	 * Generates a System Info download file
	 */
	public function download_debug_info() {

		if ( EPKB_Utilities::post('action') != 'epkb_download_debug_info' ) {
			return;
		}

		// wp_die if nonce invalid or user does not have correct permission
		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die( '_wpnonce_epkb_download_debug_info' );

		EPKB_Utilities::save_wp_option( EPKB_Settings_Controller::EPKB_DEBUG, false, true);

		nocache_headers();

		header( 'Content-Type: text/plain' );
		header( 'Content-Disposition: attachment; filename="echo-debug-info.txt"' );

		$output = EPKB_Add_Ons_Page::display_debug_data();
		echo wp_strip_all_tags( $output );

		die();
	}
}