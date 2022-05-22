<?php

defined( 'ABSPATH' ) || exit();

/**
 * Handle user submission from Help dialog
 */
class EPKB_Help_Dialog_Ctrl_FAQ {

	const OPTION_EPKB_FAQS = 'epkb_help_dialog_faqs';

	public function __construct() {
	
		// Overview tab
		add_action( 'wp_ajax_epkb_enable_help_dialog', array( $this, 'enable_help_dialog' ) );
		add_action( 'wp_ajax_nopriv_epkb_enable_help_dialog', array( $this, 'enable_help_dialog' ) );

		// FAQ tab	
		add_action( 'wp_ajax_epkb_create_faq', array( $this, 'create_faq' ) );
		add_action( 'wp_ajax_nopriv_epkb_create_faq', array( $this, 'create_faq' ) );
		add_action( 'wp_ajax_epkb_get_faq', array( $this, 'get_faq' ) );
		add_action( 'wp_ajax_nopriv_epkb_get_faq', array( $this, 'get_faq' ) );
		add_action( 'wp_ajax_epkb_update_faq', array( $this, 'update_faq' ) );
		add_action( 'wp_ajax_nopriv_epkb_update_faq', array( $this, 'update_faq' ) );
		add_action( 'wp_ajax_epkb_delete_faq', array( $this, 'delete_faq' ) );
		add_action( 'wp_ajax_nopriv_epkb_delete_faq', array( $this, 'delete_faq' ) );
		add_action( 'wp_ajax_epkb_update_faq_list', array( $this, 'update_faq_list' ) );
		add_action( 'wp_ajax_nopriv_epkb_update_faq_list', array( $this, 'update_faq_list' ) );
	}

	public function enable_help_dialog() {
		if ( empty(EPKB_Utilities::post('_wpnonce_epkb_enable_help_dialog')) || ! wp_verify_nonce( EPKB_Utilities::post('_wpnonce_epkb_enable_help_dialog'), '_wpnonce_epkb_enable_help_dialog' ) ) {
			EPKB_Utilities::ajax_show_error_die( __( 'You do not have permission to edit this knowledge base', 'echo-knowledge-base' )  . '(39)');
		}

		// ensure user has correct permissions
		if ( ! current_user_can( EPKB_Utilities::EPKB_ADMIN_CAPABILITY ) ) {
			EPKB_Utilities::ajax_show_error_die(__( 'You do not have permission to change access', 'echo-knowledge-base' ) . ' (29)');
		}

		$help_dialog_enable = EPKB_Utilities::post( 'epkb_help_dialog_enable' );

		$result = epkb_get_instance()->settings_obj->set_value( 'help_dialog_enable', $help_dialog_enable );
		if ( is_wp_error( $result ) ) {
			EPKB_Logging::add_log( 'Could not update', $result );
			EPKB_Utilities::ajax_show_error_die(__( 'Could not update', 'echo-knowledge-base' ) . ' (31)');
		}

		wp_die( json_encode( array( 'status' => 'success', 'message' => __( 'Configuration Saved', 'echo-knowledge-base') ) ) );
	}

	/**
	 * Add a new FAQ
	 */
	public function create_faq() {

		if ( empty(EPKB_Utilities::post('_wpnonce_epkb_create_faq')) || ! wp_verify_nonce( EPKB_Utilities::post('_wpnonce_epkb_create_faq'), '_wpnonce_epkb_create_faq' ) ) {
			EPKB_Utilities::ajax_show_error_die( __( 'You do not have permission to edit this knowledge base', 'echo-knowledge-base' )  . '(34)');
		}

		// ensure user has correct permissions
		if ( ! current_user_can( EPKB_Utilities::EPKB_ADMIN_CAPABILITY ) ) {
			EPKB_Utilities::ajax_show_error_die(__( 'You do not have permission to change access', 'echo-knowledge-base' ) . ' (29)');
		}

		$question = EPKB_Utilities::post( 'question_input' );
		$answer = EPKB_Utilities::post( 'answer_input' );

		// Question & Answer should not be empty
		if ( empty($question) || empty($answer) ) {
			EPKB_Utilities::ajax_show_error_die(__( 'Please add question and answer', 'echo-knowledge-base' ) );
		}

		$faq['id'] = uniqid();
		$faq['question'] = $question;
		$faq['answer'] = $answer;
		$faq['locations'] = [];

		$faqs = EPKB_Utilities::get_wp_option( self::OPTION_EPKB_FAQS, array() );
		if ( is_wp_error( $faqs ) ) {
			EPKB_Logging::add_log( 'Error retrieving Questions', $faqs );
			EPKB_Utilities::ajax_show_error_die(__( 'Error retrieving Questions', 'echo-knowledge-base' ) . ' (27)');
		}

		$faqs[] = $faq;

		$result = EPKB_Utilities::save_wp_option( self::OPTION_EPKB_FAQS, $faqs, true );
		if ( is_wp_error( $result ) ) {
			EPKB_Logging::add_log( 'Could not create the question', $result );
			EPKB_Utilities::ajax_show_error_die(__( 'Could not create the question', 'echo-knowledge-base' ) . ' (31)');
		}

		wp_die( json_encode( array( 'status' => 'success', 'message' => __( 'Configuration Saved', 'echo-knowledge-base') ) ) );
	}

	/**
	 * Get FAQ to edit
	 */
	public function get_faq() {

		if ( empty(EPKB_Utilities::post('_wpnonce_epkb_faq')) || ! wp_verify_nonce( EPKB_Utilities::post('_wpnonce_epkb_faq'), '_wpnonce_epkb_faq' ) ) {
			EPKB_Utilities::ajax_show_error_die( __( 'You do not have permission to edit this knowledge base', 'echo-knowledge-base' )  . '(34)');
		}

		// ensure user has correct permissions
		if ( ! current_user_can( EPKB_Utilities::EPKB_ADMIN_CAPABILITY ) ) {
			EPKB_Utilities::ajax_show_error_die(__( 'You do not have permission to change access', 'echo-knowledge-base' ) . ' (29)');
		}

		$faq_id = EPKB_Utilities::post( 'faq_id' );
		if ( empty($faq_id) ) {
			EPKB_Utilities::ajax_show_error_die(__( 'Invalid input', 'echo-knowledge-base' ) );
		}

		$faqs = EPKB_Utilities::get_wp_option( self::OPTION_EPKB_FAQS, array() );
		if ( is_wp_error( $faqs ) ) {
			EPKB_Logging::add_log( 'Error retrieving Questions', $faqs );
			EPKB_Utilities::ajax_show_error_die(__( 'Error retrieving Questions', 'echo-knowledge-base' ) . ' (27)');
		}

		// update the FAQ
		$found_faq = [];
		foreach( $faqs as $ix => $faq ) {
			if ( $faq['id'] == $faq_id ) {
				$found_faq = $faq;
				break;
			}
		}

		if ( empty($found_faq) ) {
			EPKB_Logging::add_log( 'Error retrieving Questions', $faqs );
			EPKB_Utilities::ajax_show_error_die(__( 'Error retrieving Questions', 'echo-knowledge-base' ) . ' (271)');
		}

		wp_die( json_encode( array( 'status' => 'success', 'message' => '', 'faq' => $found_faq ) ) );
	}

	/**
	 * Update FAQ
	 */
	public function update_faq() {

		if ( empty(EPKB_Utilities::post('_wpnonce_epkb_faq')) || ! wp_verify_nonce( EPKB_Utilities::post('_wpnonce_epkb_faq'), '_wpnonce_epkb_faq' ) ) {
			EPKB_Utilities::ajax_show_error_die( __( 'You do not have permission to edit this knowledge base', 'echo-knowledge-base' )  . '(34)');
		}

		// ensure user has correct permissions
		if ( ! current_user_can( EPKB_Utilities::EPKB_ADMIN_CAPABILITY ) ) {
			EPKB_Utilities::ajax_show_error_die(__( 'You do not have permission to change access', 'echo-knowledge-base' ) . ' (29)');
		}

		$faq_id = EPKB_Utilities::post( 'faq_id' );
		if ( empty($faq_id) ) {
			EPKB_Utilities::ajax_show_error_die(__( 'Invalid input', 'echo-knowledge-base' ) );
		}

		$question = EPKB_Utilities::post( 'question_input' );
		$answer = EPKB_Utilities::post( 'answer_input' );

		// Question & Answer should not be empty
		if ( empty($question) || empty($answer) ) {
			EPKB_Utilities::ajax_show_error_die(__( 'Please add question and answer', 'echo-knowledge-base' ) );
		}

		$faqs = EPKB_Utilities::get_wp_option( self::OPTION_EPKB_FAQS, array() );
		if ( is_wp_error( $faqs ) ) {
			EPKB_Logging::add_log( 'Error retrieving Questions', $faqs );
			EPKB_Utilities::ajax_show_error_die(__( 'Error retrieving Questions', 'echo-knowledge-base' ) . ' (27)');
		}

		// update the FAQ
		foreach( $faqs as $ix => $faq ) {
			if ( $faq['id'] == $faq_id ) {
				$faqs[$ix]['question'] = $question;
				$faqs[$ix]['answer'] = $answer;
				break;
			}
		}

		$result = EPKB_Utilities::save_wp_option( self::OPTION_EPKB_FAQS, $faqs, true );
		if ( is_wp_error( $result ) ) {
			EPKB_Logging::add_log( 'Could not update the question', $result );
			EPKB_Utilities::ajax_show_error_die(__( 'Could not update the question', 'echo-knowledge-base' ) . ' (31)');
		}

		wp_die( json_encode( array( 'status' => 'success', 'message' => __( 'Configuration Saved', 'echo-knowledge-base') ) ) );
	}

	/**
	 * Remove FAQ
	 */
	public function delete_faq() {

		if ( empty(EPKB_Utilities::post('_wpnonce_epkb_faq')) || ! wp_verify_nonce( EPKB_Utilities::post('_wpnonce_epkb_faq'), '_wpnonce_epkb_faq' ) ) {
			EPKB_Utilities::ajax_show_error_die( __( 'You do not have permission to edit this knowledge base', 'echo-knowledge-base' )  . ' (34)');
		}

		// ensure user has correct permissions
		if ( ! current_user_can( EPKB_Utilities::EPKB_ADMIN_CAPABILITY ) ) {
			EPKB_Utilities::ajax_show_error_die(__( 'You do not have permission to change access', 'echo-knowledge-base' ) . ' (29)');
		}

		$faq_id = EPKB_Utilities::post( 'faq_id' );
		if ( empty($faq_id) ) {
			EPKB_Utilities::ajax_show_error_die(__( 'Invalid input', 'echo-knowledge-base' ) );
		}

		$faqs = EPKB_Utilities::get_wp_option( self::OPTION_EPKB_FAQS, array() );
		if ( is_wp_error( $faqs ) ) {
			EPKB_Logging::add_log( 'Error retrieving Questions', $faqs );
			EPKB_Utilities::ajax_show_error_die(__( 'Error retrieving Questions', 'echo-knowledge-base' ) . ' (27)');
		}

		foreach( $faqs as $ix => $faq ) {
			if ( $faq['id'] == $faq_id ) {
				unset($faqs[$ix]);
				break;
			}
		}

		$result = EPKB_Utilities::save_wp_option( self::OPTION_EPKB_FAQS, $faqs, true );
		if ( is_wp_error( $result ) ) {
			EPKB_Logging::add_log( 'Could not delete FAQ', $result );
			EPKB_Utilities::ajax_show_error_die(__( 'Could not delete FAQ', 'echo-knowledge-base' ) . ' (31)');
		}

		wp_die( json_encode( array( 'status' => 'success', 'message' => __( 'Configuration Saved', 'echo-knowledge-base') ) ) );
	}

	/**
	 * Refresh list of current FAQs
	 */
	public function update_faq_list() {

		if ( empty(EPKB_Utilities::post('_wpnonce_epkb_create_faq')) || ! wp_verify_nonce( EPKB_Utilities::post('_wpnonce_epkb_create_faq'), '_wpnonce_epkb_create_faq' ) ) {
			EPKB_Utilities::ajax_show_error_die( __( 'You do not have permission to edit this knowledge base', 'echo-knowledge-base' )  . ' (34)');
		}

		// ensure user has correct permissions
		if ( ! current_user_can( EPKB_Utilities::EPKB_ADMIN_CAPABILITY ) ) {
			EPKB_Utilities::ajax_show_error_die(__( 'You do not have permission to change access', 'echo-knowledge-base' ) . ' (29)');
		}

		// Get updated questions list
		ob_start();
		$result = EPKB_Help_Dialog_View_FAQ::display_list_of_records();
		$output = ob_get_clean();

		if ( ! $result ) {
			EPKB_Utilities::ajax_show_error_die(__( 'Internal error occurred', 'echo-knowledge-base' ) . ' (23)');
		}

		EPKB_Utilities::ajax_show_content( $output );
	}
}