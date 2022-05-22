<?php

defined( 'ABSPATH' ) || exit();

/**
 * Handle user submission from Help dialog
 */
class EPKB_Help_Dialog_Ctrl {

	public function __construct() {

		add_action( 'wp_ajax_epkb_help_dialog_contact', array($this, 'submit_contact_form' ) );
		add_action( 'wp_ajax_nopriv_epkb_help_dialog_contact', array($this, 'submit_contact_form' ) );
	}

	/**
	 * Contact Form Submission
	 */
	public function submit_contact_form() {

		// verify that request is authentic
		if ( empty( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce'], '_epkb_help_dialog_contact_form_nonce' ) ) {
			wp_send_json_error( __( 'Error occurred. Please try again later.', 'echo-knowledge-base' ) );
		}

		// ensure that user has correct permissions
		if ( ! is_admin() || ! current_user_can( EPKB_Utilities::EPKB_ADMIN_CAPABILITY ) ) {
			wp_send_json_error( __( 'You do not have permission to send email', 'echo-knowledge-base' ) );
		}

		if ( ! did_action( 'init' ) && ! did_action( 'admin_init' ) ) {
			wp_send_json_error( __( 'Error occurred. Please try again later.', 'echo-knowledge-base' ) . ' (01)' );
		}

		// get user submission
		$reply_to_email = EPKB_Utilities::post( 'email' );
		if ( empty( $reply_to_email ) ) {
			wp_send_json_error( __( 'Please enter your email address.', 'echo-knowledge-base' ) );
		}

		$reply_to_name = EPKB_Utilities::post( 'user_first_name' );
		$subject = EPKB_Utilities::post( 'subject' );
		$message = EPKB_Utilities::post( 'comment', '' ,false );

		$message = sanitize_textarea_field( $message );  // do not strip line breaks

		$epkb_settings = epkb_get_instance()->settings_obj->get_settings_or_default();

		$message =  __( 'Name', 'echo-knowledge-base' ) . ': ' . $reply_to_name . ' <br/>' .
			        __( 'Email', 'echo-knowledge-base' ) . ': ' . $reply_to_email . ' <br/>' .
		            __( 'Subject', 'echo-knowledge-base' ) . ': ' . $subject . ' <br/>' .
			        __( 'Message', 'echo-knowledge-base' ) . ': ' . $message;

		// send the email
		$result = EPKB_Utilities::send_email( $message, $epkb_settings['help_dialog_contact_submission_email'], $reply_to_email, $reply_to_name, $subject, __( 'New KB Help Request', 'echo-knowledge-base' ) );
		if ( empty( $result ) ) {
			wp_send_json_success( $epkb_settings['help_dialog_contact_success_message'] );
		} else {
			wp_send_json_error( $result );
		}
	}
}