<?php

defined( 'ABSPATH' ) || exit();

/**
 * FRONTNED Help Dialog controller
 */
class EPKB_Help_Dialog_Front_Ctrl {

	public function __construct() {
		add_action( 'wp_ajax_epkb_help_dialog_contact', array( $this, 'submit_contact_form' ) );
		add_action( 'wp_ajax_nopriv_epkb_help_dialog_contact', array( $this, 'submit_contact_form' ) );
	}

	/**
	 * Contact Form Submission
	 */
	public function submit_contact_form() {

		// verify that request is authentic
		if ( empty( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce'], '_wpnonce_epkb_ajax_action' ) ) {
			wp_send_json_error( __( 'Error occurred. Please try again later.', 'echo-knowledge-base' ) );
		}

		// prevent direct access ?
		if ( ! did_action( 'init' ) && ! did_action( 'admin_init' ) ) {
			wp_send_json_error( __( 'Error occurred. Please try again later.', 'echo-knowledge-base' ) . ' (01)' );
		}

		// Spam checking
		// 1. Fake input field - do not proceed if is filled, return generic response
		if ( ! empty( $_REQUEST['catch_details'] ) ) {
			wp_send_json_success( __( 'Thank you. We will get back to you soon.', 'echo-knowledge-base' ) );
		}
		// 2. Check additional parameter that is set by our JS - do not proceed if is missed
		if ( empty( $_REQUEST['jsnonce'] ) || ! wp_verify_nonce( $_REQUEST['jsnonce'], '_wpnonce_epkb_ajax_action' ) ) {
			wp_send_json_success( __( 'Thank you. We will get back to you soon.', 'echo-knowledge-base' ) );
		}

		// get user submission
		$reply_to_email = EPKB_Utilities::post( 'email', '', 'email', EPKB_Help_Dialog_Submissions_DB::USER_EMAIL_LENGTH );
		if ( empty( $reply_to_email ) || ! is_email( $reply_to_email ) ) {
			wp_send_json_error( __( 'Please enter a valid email address.', 'echo-knowledge-base' ) );
		}

		$reply_to_name = EPKB_Utilities::post( 'user_first_name', '', 'text', EPKB_Help_Dialog_Submissions_DB::USER_NAME_LENGTH );
		$subject = EPKB_Utilities::post( 'subject', '', 'text', EPKB_Help_Dialog_Submissions_DB::SUBJECT_LENGTH );
		$message = EPKB_Utilities::post( 'comment', '', 'text-area', EPKB_Help_Dialog_Submissions_DB::COMMENT_LENGTH );
		$location_name = EPKB_Utilities::post( 'location_name', '', 'text', EPKB_Help_Dialog_Submissions_DB::LOCATION_NAME_LENGTH );
		$notification_status = '';
		$notification_details = '';

		// write record to the DataBse table
		$handler = new EPKB_Help_Dialog_Submissions_DB();
		$inserted_submission_id = $handler->insert_submission(
			$location_name,
			date( 'Y-m-d H:i:s' ),
			$reply_to_name,
			$reply_to_email,
			$subject,
			$message,
			$handler::STATUS_NEW,
			$notification_status,
			$notification_details,
			'',
			EPKB_FAQ_Utilities::get_ip_address(),
			''
		);
		if ( is_wp_error( $inserted_submission_id ) ) {
			wp_send_json_error( 'TODO' );  // TODO we will need to show user custom error message
		}

		$epkb_settings = epkb_get_instance()->help_dialog_settings_obj->get_settings_or_default();

		// send the email if user defined one
		if ( ! empty( $epkb_settings['help_dialog_contact_submission_email'] ) ) {

			$email_message =  __( 'Name', 'echo-knowledge-base' ) . ': ' . $reply_to_name . ' \r\n' .
			                  __( 'Email', 'echo-knowledge-base' ) . ': ' . $reply_to_email . ' \r\n' .
			                  __( 'Subject', 'echo-knowledge-base' ) . ': ' . $subject . ' \r\n' .
			                  __( 'Message', 'echo-knowledge-base' ) . ': ' . $message;
			$notification_status = 'sent';

			$subject = __( 'Help Dialog Submission', 'echo-knowledge-base' ) . ': ' . $subject;
			$send_result = EPKB_Utilities::send_email( $email_message, $epkb_settings['help_dialog_contact_submission_email'], $reply_to_email, $reply_to_name, $subject );
			if ( ! empty( $send_result ) ) {
				$notification_status = 'error';
				$notification_details = substr( $send_result, 0, EPKB_Help_Dialog_Submissions_DB::NOTIFICATION_DETAILS_LENGTH );
			}

			$update_result = $handler->update_submission(
				$inserted_submission_id,
				$notification_status,
				$notification_details
			);

			if ( is_wp_error( $update_result ) ) {
				EPKB_Logging::add_log( 'Failed update submission after sending email', $update_result );
			}

			if ( ! empty( $send_result ) ) {
				wp_send_json_error( 'TODO' );  // TODO we will need to show user custom error message
			}
		}

		wp_send_json_success( $epkb_settings['help_dialog_contact_success_message'] );
	}
}