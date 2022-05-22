<?php

defined( 'ABSPATH' ) || exit();

/**
 * Handle user submission from Help dialog
 */
class EPKB_Help_Dialog_Admin_Ctrl {

	const SAVE_SETTINGS_ACTION = 'epkb_save_help_dialog_settings';

	public function __construct() {
		
		add_action( 'wp_ajax_' . self::SAVE_SETTINGS_ACTION, array( $this, 'save_help_dialog_settings' ) );
		add_action( 'wp_ajax_nopriv_' . self::SAVE_SETTINGS_ACTION, array( 'EPKB_Utilities', 'user_not_logged_in' ) );
	}

	/**
	 * User updated Help Dialog Settings
	 */
	public function save_help_dialog_settings() {

		// wp_die if nonce invalid or user does not have correct permission
		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die();

		$this->save_help_dialog_on_off();

		$hd_settings_specs = EPKB_Help_Dialog_Settings_Specs::get_fields_specification();

		// OPTION: email to receive contact form submissions
		$contact_submission_email = EPKB_Utilities::post(
				'help_dialog_contact_submission_email',
				'',
				$hd_settings_specs['help_dialog_contact_submission_email']['type'],
				intval( $hd_settings_specs['help_dialog_contact_submission_email']['max'] ) );

		// if entered email address is not valid, then let user know and exit
		if ( ! is_email( $contact_submission_email ) ) {
			EPKB_Utilities::ajax_show_error_die( __( 'Please enter a valid email address.', 'echo-knowledge-base' ) );
		}

		// save option
		$result = epkb_get_instance()->help_dialog_settings_obj->set_value( 'help_dialog_contact_submission_email', $contact_submission_email );
		if ( is_wp_error( $result ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 31, $result ) );
		}

		$response = array( 'status' => 'success', 'message' => __( 'Configuration Saved', 'echo-knowledge-base') );
		wp_die( json_encode( $response ) );
	}

	/**
	 * User Enabled or Disabled Help Dialog feature - call from kb config
	 */
	private function save_help_dialog_on_off() {

		$help_dialog_enable = EPKB_Utilities::post( 'epkb_help_dialog_enable' );
		if ( empty( $help_dialog_enable ) ) {
			return;
		}

		$help_dialog_enable = $help_dialog_enable != 'on' ? 'off' : 'on';

		$result = epkb_get_instance()->help_dialog_settings_obj->set_value( 'help_dialog_enable', $help_dialog_enable );
		if ( is_wp_error( $result ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 31 ) );
		}

		$response = array( 'status' => 'success', 'message' => __( 'Configuration Saved', 'echo-knowledge-base') );

		// after the Help Dialog was enabled
		if ( $help_dialog_enable == 'on' ) {

			// create DB table if does not exist
			$handler = new EPKB_Help_Dialog_Submissions_DB();
			$handler->create_table_if_not_exists();

			// redirect to help config
			$response['redirect'] = admin_url( 'admin.php?page=epkb-help-dialog-config#site-settings' );
		}

		wp_die( json_encode( $response ) );
	}
}
