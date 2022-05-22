<?php

/**
 * Control for KB Configuration admin page
 */
class EPKB_Configuration_Controller {

	public function __construct() {

		add_action( 'wp_ajax_epkb_wpml_enable', array( $this, 'wpml_enable' ) );
		add_action( 'wp_ajax_nopriv_epkb_wpml_enable', array( 'EPKB_Utilities', 'user_not_logged_in' ) );

		add_action( 'wp_ajax_epkb_editor_backend_mode', array( $this, 'editor_backend_mode' ) );
		add_action( 'wp_ajax_nopriv_epkb_editor_backend_mode', array( 'EPKB_Utilities', 'user_not_logged_in' ) );

		add_action( 'wp_ajax_epkb_save_access_control', array( 'EPKB_Admin_UI_Access', 'save_access_control' ) );
		add_action( 'wp_ajax_nopriv_epkb_save_access_control', array( 'EPKB_Utilities', 'user_not_logged_in' ) );

		add_action( 'wp_ajax_epkb_update_kb_name', array( $this, 'update_kb_name' ) );
		add_action( 'wp_ajax_nopriv_epkb_update_kb_name', array( 'EPKB_Utilities', 'user_not_logged_in' ) );

		add_action( 'wp_ajax_epkb_create_kb_demo_data', array( $this, 'create_kb_demo_data' ) );
		add_action( 'wp_ajax_nopriv_epkb_create_kb_demo_data', array( 'EPKB_Utilities', 'user_not_logged_in' ) );
	}

	/**
	 * Triggered when user clicks to toggle wpml setting.
	 */
	public function wpml_enable() {

		// wp_die if nonce invalid or user does not have correct permission
		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die( '_wpnonce_epkb_wpml_enable', 'admin_eckb_access_config_write' );

		// get KB ID
		$kb_id = (int)EPKB_Utilities::post( 'epkb_kb_id', 0 );
		if ( ! EPKB_Utilities::is_positive_int( $kb_id ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 410 ) );
		}

		$wpml_enable = EPKB_Utilities::post( 'wpml_enable' );
		if ( $wpml_enable != 'on' ) {
			$wpml_enable = 'off';
		}

		$result = epkb_get_instance()->kb_config_obj->set_value( $kb_id, 'wpml_is_enabled', $wpml_enable );
		if ( is_wp_error( $result ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 412, $result ) );
		}

		// we are done here
		if ( $wpml_enable == 'on' ) {
			EPKB_Utilities::ajax_show_info_die( __( 'WPML enabled', 'echo-knowledge-base' ) );
		} else {
			EPKB_Utilities::ajax_show_info_die( __( 'WPML disabled', 'echo-knowledge-base' ) );
		}
	}

	/**
	 * Triggered when user clicks to toggle editor backend mode.
	 */
	public function editor_backend_mode() {

		// wp_die if nonce invalid or user does not have correct permission
		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die( '_wpnonce_epkb_ajax_action', 'admin_eckb_access_frontend_editor_write' );

		// check addons that are updated
		$issues_found = EPKB_Core_Utilities::is_backend_editor_hidden();
		if ( $issues_found ) {
			EPKB_Core_Utilities::update_kb_flag( 'editor_backend_mode', false );
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( '', $issues_found ) );
		}

		$editor_backend_mode = EPKB_Utilities::post( 'editor_backend_mode' ) == '1' ? true : false;

		$result = EPKB_Core_Utilities::update_kb_flag( 'editor_backend_mode', $editor_backend_mode );
		if ( is_wp_error( $result ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 417, $result ) );
		}

		// we are done here
		if ( $editor_backend_mode == 'on' ) {
			EPKB_Utilities::ajax_show_info_die( __( 'Backend visual Editor enabled', 'echo-knowledge-base' ) );
		} else {
			EPKB_Utilities::ajax_show_info_die( __( 'Frontend visual Editor enabled', 'echo-knowledge-base' ) );
		}
	}

	/**
	 * Handle update for KB Nickname
	 */
	public function update_kb_name() {

		// wp_die if nonce invalid or user does not have correct permission
		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die( '_wpnonce_epkb_ajax_action', 'admin_eckb_access_config_write' );

		$kb_id = (int)EPKB_Utilities::post( 'epkb_kb_id', 0 );
		if ( ! EPKB_Utilities::is_positive_int( $kb_id ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 414 ) );
		}

		$new_kb_name = EPKB_Utilities::post( 'epkb_kb_name_input' );

		$result = epkb_get_instance()->kb_config_obj->set_value( $kb_id, 'kb_name', $new_kb_name );
		if ( is_wp_error( $result ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 415 ) );
			return;
		}

		// we are done here
		EPKB_Utilities::ajax_show_info_die( __( 'KB Name Updated', 'echo-knowledge-base' ) );
	}

	/**
	 * Create demo data for KB
	 */
	public function create_kb_demo_data() {

		// wp_die if nonce invalid or user does not have correct permission
		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die( '_wpnonce_epkb_ajax_action', 'admin_eckb_access_frontend_editor_write' );

		// retrieve current KB id
		$kb_id = (int)EPKB_Utilities::post( 'epkb_kb_id', 0 );
		if ( ! EPKB_Utilities::is_positive_int( $kb_id ) ){
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 420 ) );
		}

		// retrieve current KB configuration
		$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config( $kb_id );

		// create demo data for the current KB if no categories exist yet
		EPKB_KB_Handler::create_sample_categories_and_articles( $kb_id, $kb_config['kb_main_page_layout'] );

		// we are done here
		EPKB_Utilities::ajax_show_info_die( __( 'Demo categories and articles were created. The page will reload.', 'echo-knowledge-base' ) );
	}

	/**
	 * Handle actions that need reload of the page - KB Configuration page and other from addons
	 */
	public static function handle_form_actions() {

		$action = EPKB_Utilities::post( 'action' );
		if ( empty( $action ) ) {
			return [];
		}

		// verify that request is authentic
		if ( ! isset( $_REQUEST['_wpnonce_manage_kbs'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce_manage_kbs'], '_wpnonce_manage_kbs' ) ) {
			return [ 'error' => EPKB_Utilities::report_generic_error( 1 ) ];
		}

		// only admin user can handle these actions
		if ( ! current_user_can( 'manage_options' ) ) {
			return [ 'error' => __( 'You do not have permission.', 'echo-knowledge-base' ) ];
		}

		if ( $action == 'enable_editor_backend_mode' ) {

			// check addons that are updated
			$issues_found = EPKB_Core_Utilities::is_backend_editor_hidden();
			if ( $issues_found ) {
				EPKB_Core_Utilities::update_kb_flag( 'editor_backend_mode', false );
				return [ 'error' => EPKB_Utilities::report_generic_error( '', $issues_found ) ];
			}

			$result = EPKB_Core_Utilities::update_kb_flag( 'editor_backend_mode', true );
			if ( is_wp_error( $result ) ) {
				return [ 'error' => EPKB_Utilities::report_generic_error( 1 ) ];
			}

			return [ 'success' => __( 'Backend visual Editor enabled', 'echo-knowledge-base' ) ];
		}

		// retrieve KB ID we are saving
		$kb_id = empty( $_REQUEST['emkb_kb_id'] ) ? '' : EPKB_Utilities::sanitize_get_id( $_REQUEST['emkb_kb_id'] );
		if ( empty( $kb_id ) || is_wp_error( $kb_id ) ) {
			EPKB_Logging::add_log( "received invalid kb_id when archiving/deleting KB", $kb_id );
			return [ 'error' => EPKB_Utilities::report_generic_error( 2 ) ];
		}

		// retrieve current KB configuration
		$current_config = epkb_get_instance()->kb_config_obj->get_kb_config( $kb_id, true );
		if ( is_wp_error( $current_config ) ) {
			EPKB_Logging::add_log("Could not retrieve KB config when manage KB", $kb_id );
			return [ 'error' => EPKB_Utilities::report_generic_error( 5, $current_config ) ];
		}

		// handle user interactions
		if ( $action == 'epkb_update_article_v2' ) {
			return self::switch_user_to_article_v2( $current_config );
		}

		// EXPORT CONFIG
		if ( $action == 'epkb_export_knowledge_base' ) {
			$export = new EPKB_Export_Import();
			$message = $export->download_export_file( $kb_id );

			// stop php because we sent the file
			if ( empty( $message ) ) {
				exit;
			}
			return $message;
		}

		// IMPORT CONFIG
		if ( $action == 'epkb_import_knowledge_base' ) {
			$import = new EPKB_Export_Import();
			return $import->import_kb_config( $kb_id );
		}

		$message = apply_filters( 'eckb_handle_manage_kb_actions', [], $kb_id, $current_config );

		return is_array( $message ) ? $message : [];
	}

	/***
	 * Handle Form Action
	 *
	 * @param $kb_config
	 * @return array
	 */
	private static function switch_user_to_article_v2( $kb_config ) {

		// convert article structure to version 2
		$result = epkb_get_instance()->kb_config_obj->set_value( $kb_config['id'], 'article-structure-version', 'version-2' );
		if ( is_wp_error( $result ) ) {
			return [ 'error' => __( 'Something went wrong', 'echo-knowledge-base' ) . ' (64)' ];
		}

		if ( $kb_config['article_toc_enable'] == 'on' ) {

			if ( $kb_config['article_toc_position'] == 'left' ) {
				$kb_config['article_sidebar_component_priority']['toc_left'] = 1;
				$kb_config['article-right-sidebar-toggle'] = 'on';
			} else if ( $kb_config['article_toc_position'] == 'right' ) {
				$kb_config['article_sidebar_component_priority']['toc_right'] = 1;
				$kb_config['article-right-sidebar-toggle'] = 'on';
			} else if ( $kb_config['article_toc_position'] == 'middle' ) {
				$kb_config['article_sidebar_component_priority']['toc_content'] = 1;
				$kb_config['article-right-sidebar-toggle'] = 'on';
			}
		}

		$kb_config['article-structure-version'] = 'version-2';

		$new_config = EPKB_Editor_Controller::reset_layout( $kb_config, $kb_config );
		$result = epkb_get_instance()->kb_config_obj->update_kb_configuration( $new_config['id'], $new_config );
		if ( is_wp_error( $result ) ) {

			/* @var $result WP_Error */
			$message = $result->get_error_message();
			if ( empty($message) ) {
				return [ 'error' => __( 'Could not save the new configuration', 'echo-knowledge-base' ) . '(3)' ];
			} else {
				return [ 'error' => __( 'Configuration NOT saved due to following problem:' . $message, 'echo-knowledge-base' ) ];
			}
		}

		return [];
	}
}