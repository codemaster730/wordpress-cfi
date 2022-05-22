<?php

defined( 'ABSPATH' ) || exit();

/**
 * Handle user entries from Help Dialog Submissions
 */
class EPKB_Help_Dialog_Submissions_Ctrl {

	const DELETE_ACTION = 'epkb_help_dialog_submission_delete';
	const DELETE_ALL_ACTION = 'epkb_help_dialog_submissions_delete_all';
	const LOAD_MORE_ACTION = 'epkb_help_dialog_submissions_load_more';

	public function __construct() {

		if ( ! EPKB_Help_Dialog_View::is_help_dialog_enabled() ) {
			return;
		}

		// Delete single submission
		add_action( 'wp_ajax_' . self::DELETE_ACTION, array( $this, 'delete_submission' ) );
		add_action( 'wp_ajax_nopriv_' . self::DELETE_ACTION, array( 'EPKB_Utilities', 'user_not_logged_in' ) );

		// Delete all submissions
		add_action( 'wp_ajax_' . self::DELETE_ALL_ACTION, array( $this, 'delete_all_submissions' ) );
		add_action( 'wp_ajax_nopriv_' . self::DELETE_ALL_ACTION, array( 'EPKB_Utilities', 'user_not_logged_in' ) );

		// Load more submissions
		add_action( 'wp_ajax_' . self::LOAD_MORE_ACTION, array( $this, 'load_more_submissions' ) );
		add_action( 'wp_ajax_nopriv_' . self::LOAD_MORE_ACTION, array( 'EPKB_Utilities', 'user_not_logged_in' ) );
	}

	/**
	 * Delete Submission
	 */
	public function delete_submission() {

		// wp_die if nonce invalid or user does not have correct permission
		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die();

		// get submission id
		$submission_id = (int)EPKB_Utilities::post( 'item_id' );
		if ( empty( $submission_id ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 38 ) );
		}

		// remove the submission
		$handler = new EPKB_Help_Dialog_Submissions_DB();
		$result = $handler->delete_submission( $submission_id );
		if ( empty( $result ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 40 ) );
		}

		wp_die( json_encode( array(
			'status'        => 'success',
			'message'       => __( 'Submission removed.', 'echo-knowledge-base' ),
			'per_page'      => EPKB_Help_Dialog_Submissions_DB::PER_PAGE,
			'total_number'  => $handler->get_total_number_of_submissions(),
		) ) );
	}

	/**
	 * Delete all Submissions
	 */
	public function delete_all_submissions() {

		// wp_die if nonce invalid or user does not have correct permission
		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die();

		// remove the submission
		$handler = new EPKB_Help_Dialog_Submissions_DB();
		$result = $handler->delete_all_submissions();
		if ( empty( $result ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 41 ) );
		}

		wp_die( json_encode( array( 'status' => 'success', 'message' => __( 'All submissions removed.', 'echo-knowledge-base') ) ) );
	}

	/**
	 * Load more Submissions
	 */
	public function load_more_submissions() {

		// wp_die if nonce invalid or user does not have correct permission
		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die();

		$page_number = (int)EPKB_Utilities::post( 'page_number', 1 );

		$handler = new EPKB_Help_Dialog_Submissions_DB();
		$submissions = $handler->get_submissions( $page_number );
		if ( is_wp_error($submissions) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 411, $submissions ) );
		}

		wp_die( json_encode( array(
			'status'        => 'success',
			'message'       => __( 'Submission loaded.', 'echo-knowledge-base'),
			'per_page'      => EPKB_Help_Dialog_Submissions_DB::PER_PAGE,
			'total_number'  => $handler->get_total_number_of_submissions(),
			'items'         => EPKB_HTML_Forms::get_html_table_rows(
									$submissions,
									EPKB_Help_Dialog_Submissions_DB::PRIMARY_KEY,
									EPKB_Help_Dialog_Submissions_DB::get_submission_column_fields(),
									EPKB_Help_Dialog_Submissions_DB::get_submission_row_fields(),
									EPKB_Help_Dialog_Submissions_DB::get_submission_optional_row_fields(),
									EPKB_Help_Dialog_Submissions_Ctrl::DELETE_ACTION,
									count( EPKB_Help_Dialog_Submissions_DB::get_submission_column_fields() ) + 1
							)
		) ) );
	}
}
