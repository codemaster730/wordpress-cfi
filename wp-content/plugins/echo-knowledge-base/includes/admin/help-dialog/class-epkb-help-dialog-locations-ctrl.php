<?php

defined( 'ABSPATH' ) || exit();

/**
 * Handle user submission from Help dialog Locations & FAQs
 */
class EPKB_Help_Dialog_Locations_Ctrl {

	const LOCATION_NAME_LENGTH = 80;

	public function __construct() {

		if ( ! EPKB_Help_Dialog_View::is_help_dialog_enabled() ) {
			return;
		}

		add_action( 'wp_ajax_epkb_create_update_location', array( $this, 'create_update_location' ) );
		add_action( 'wp_ajax_nopriv_epkb_create_update_location', array( 'EPKB_Utilities', 'user_not_logged_in' ) );
		
		add_action( 'wp_ajax_epkb_delete_location', array( $this, 'delete_location' ) );
		add_action( 'wp_ajax_nopriv_epkb_delete_location', array( 'EPKB_Utilities', 'user_not_logged_in' ) );

		add_action( 'wp_ajax_epkb_save_help_questions_order', array($this, 'save_questions_order' ) );
		add_action( 'wp_ajax_nopriv_epkb_save_help_questions_order', array( 'EPKB_Utilities', 'user_not_logged_in' ) );
		
		add_action( 'wp_ajax_epkb_save_question_data', array($this, 'save_question_data' ) );
		add_action( 'wp_ajax_nopriv_epkb_save_question_data', array( 'EPKB_Utilities', 'user_not_logged_in' ) );
		
		add_action( 'wp_ajax_epkb_get_question_data', array($this, 'get_question_data' ) );
		add_action( 'wp_ajax_nopriv_epkb_get_question_data', array( 'EPKB_Utilities', 'user_not_logged_in' ) );
		
		add_action( 'wp_ajax_epkb_delete_question', array($this, 'delete_question' ) );
		add_action( 'wp_ajax_nopriv_epkb_delete_question', array( 'EPKB_Utilities', 'user_not_logged_in' ) );
		
		add_action( 'save_post_' . EPKB_Help_Dialog_Handler::get_post_type(), array($this, 'update_question_order_sequence' ) );
	}
	
	/**
	 * Update or add LOCATION
	 */
	public function create_update_location() {

		// wp_die if nonce invalid or user does not have correct permission
		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die();

		// check status name, can be only public or draft
		$new_location_status = EPKB_Utilities::get( 'location_status' );
		if ( $new_location_status !== EPKB_Help_Dialog_Handler::HELP_DIALOG_STATUS_PUBLIC ) {
			$new_location_status = EPKB_Help_Dialog_Handler::HELP_DIALOG_STATUS_DRAFT;
		}

		// get the page for this location
		$selected_pages = EPKB_Utilities::post( 'selected_pages', [], 'db-config' );
		$excluded_pages = EPKB_Utilities::post( 'excluded_pages', [], 'db-config' );
		
		$selected_pages = array_merge( [ 'post' => [], 'page' => [], 'cpt' => [] ], $selected_pages);
		$excluded_pages = array_merge( [ 'post' => [], 'page' => [], 'cpt' => [] ], $excluded_pages);
		
		if ( empty( $selected_pages ) && empty( $excluded_pages ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 434 ) );
		}

		$page_locations = [
			'selected_pages' => $selected_pages,
			'excluded_pages' => $excluded_pages,
		];

		$new_location_kb_ids = EPKB_Utilities::post( 'kb_ids', [] );
		if ( ! is_array( $new_location_kb_ids ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 68 ) );
		}

		$location_id = (int)EPKB_Utilities::get( 'location_id' );
		$location_name = EPKB_Utilities::get( 'location_name' );

		// create a new location
		if ( empty( $location_id ) ) {
			$location_id = EPKB_Help_Dialog_Handler::create_help_dialog_location( $location_name, $page_locations, $new_location_status, $new_location_kb_ids );
			if ( is_wp_error($location_id) || empty($location_id) ) {
				
				if ( ! empty( $location_id->errors['term_exists'] ) ) {
					EPKB_Utilities::ajax_show_error_die( __( 'The location with this name already exists. Please choose another name.', 'echo-knowledge-base' ), '', 'term_exists' );
				}

				EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 31, $location_id ) );
			}
			wp_die( json_encode( array( 'status' => 'success', 'message' => __( 'Configuration Saved', 'echo-knowledge-base'), 'location_id' => $location_id ) ) );
		}

		// get the location category
		$location = get_term( $location_id );
		if ( empty( $location ) || is_wp_error( $location ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 411, $location ) );
		}

		// update the location name if it changed
		if ( $location_name !== $location->name ) {
			$result = wp_update_term( $location_id, EPKB_Help_Dialog_Handler::get_help_dialog_location_taxonomy_name(), array(
										'name' => sanitize_text_field( $location_name ),
										'slug' => sanitize_title( $location_name )
									) );
			if ( is_wp_error( $result ) ) {
				EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 435, $result ) );
			}

			// for now we keep historical data
			// let submissions for the current location update location name field - ignore errors (DB class already write errors, better to continue execution here)
			// $submissions_handler = new EPKB_Help_Dialog_Submissions_DB();
			//$submissions_handler->update_location_name( $location->name, $location_name );
		}

		// update LOCATION PAGES
		$result = update_term_meta( $location_id, EPKB_Help_Dialog_Handler::HELP_DIALOG_LOCATION_META, $page_locations );
		if ( is_wp_error( $result )  ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 436 ) );
		}

		// update LOCATION STATUS
		$result = EPKB_Help_Dialog_Handler::update_term_meta( $location_id, EPKB_Help_Dialog_Handler::HELP_DIALOG_STATUS_META, $new_location_status );
		if ( empty( $result ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 437 ) );
		}

		// update LOCATION KBs
		$result = EPKB_Help_Dialog_Handler::update_term_meta( $location_id, EPKB_Help_Dialog_Handler::HELP_DIALOG_KB_IDS, $new_location_kb_ids, true );
		if ( empty( $result ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 438 ) );
		}

		$location = EPKB_Help_Dialog_Handler::get_location_by_id_or_default( $location_id );
		$location_url = EPKB_FAQ_Utilities::get_first_location_page_url( $location );
		$location_url = empty( $location_url ) ? '' : $location_url;

		wp_die( json_encode( array( 'status' => 'success', 'message' => __( 'Configuration Saved', 'echo-knowledge-base'), 'location_id' => $location_id,
		                            'url' => $location_url, 'editor_url' => add_query_arg( [ 'action' => 'epkb_load_editor', 'epkb_editor_type' => 'help-dialog', 'preopen_zone' => 'help_dialog' ], $location_url ) ) ) );
	}

	/**
	 * Delete LOCATION
	 */
	public function delete_location() {

		// wp_die if nonce invalid or user does not have correct permission
		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die();

		$location_id = (int)EPKB_Utilities::get( 'location_id' );
		if ( empty( $location_id ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 38 ) );
		}
		
		// get the location category
		$term = get_term( $location_id );
		if ( empty( $term ) || is_wp_error( $term ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 39, $term ) );
		}

		// remove the location (category) and its ordering (in term meta)
		$result = wp_delete_term( $location_id, EPKB_Help_Dialog_Handler::get_help_dialog_location_taxonomy_name() );
		if ( empty( $result ) || is_wp_error( $result ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 40, $result ) );
		}
		
		// remove all posts meta attached to the location
		global $wpdb;
		$wpdb->delete( $wpdb->prefix . 'postmeta', [ 'meta_key' => 'epkb_faq_order_' . $location_id ] ); // ignore result as postmeta will be cleared later if necessary
		
		wp_die( json_encode( array( 'status' => 'success', 'message' => __( 'Location removed', 'echo-knowledge-base'), 'location_id' => $location_id ) ) );
	}
	
	/**
	 * Save new order of questions
	 */
	public function save_questions_order() {

		// wp_die if nonce invalid or user does not have correct permission
		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die();

		// check location
		$location_id = EPKB_Utilities::get( 'location' );
		if ( empty( $location_id ) || ! is_numeric($location_id) ) {
			wp_send_json_error( EPKB_Utilities::report_generic_error( 1 ) );
		}

		// get the location category
		$term = get_term( $location_id );
		if ( empty($term) || is_wp_error( $term ) ) {
			wp_send_json_error( EPKB_Utilities::report_generic_error( 2 ) );
		}

		// check article order array
		$questions_order = EPKB_Utilities::post( 'questions_order', [] );
		if ( ! is_array( $questions_order ) ) {
			wp_send_json_error( EPKB_Utilities::report_generic_error( 3 ) );
		}

		$i = 1;
		foreach ( $questions_order as $question_id ) {
			update_post_meta( $question_id, 'epkb_faq_order_' . $location_id, $i++ );
			
			// check if question have right location 
			$question_terms = wp_get_post_terms( $question_id, EPKB_Help_Dialog_Handler::get_help_dialog_location_taxonomy_name(), array( 'fields' => 'ids' ) );
			if ( is_wp_error( $question_terms ) ) {
				EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 39, $question_terms ) );
			}
			
			// location does not have this question then add it
			if ( ! in_array( $location_id, $question_terms ) ) {
				$question_terms[] = (int) $location_id;

				$result = wp_set_object_terms( $question_id, $question_terms,  EPKB_Help_Dialog_Handler::get_help_dialog_location_taxonomy_name(), false );
				if ( is_wp_error( $result ) ) {
					EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 60, $result ) );
				}

				$result = $this->update_question_order_sequence( $question_id );
				if ( empty( $result ) ) {
					EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 61 ) );
				}
			}
		}
		
		// remove location in old posts that was removed to All questions list 
		$old_questions = get_posts( array(
			'post_type' => EPKB_Help_Dialog_Handler::get_post_type(),
			'tax_query' => array(
				array(
					'taxonomy' => EPKB_Help_Dialog_Handler::get_help_dialog_location_taxonomy_name(),
					'terms' => $location_id
				)
			),
			'order' => 'ASC',
			'posts_per_page' => -1,
			'post__not_in' => $questions_order
		) );
		
		foreach ( $old_questions as $old_question ) {
			$old_question_terms = wp_get_post_terms( $old_question->ID, EPKB_Help_Dialog_Handler::get_help_dialog_location_taxonomy_name(), array( 'fields' => 'ids' ) );
			if ( is_wp_error( $old_question_terms ) ) {
				EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 60, $old_question_terms ) );
			}

			if ( ( $key = array_search( $location_id, $old_question_terms ) ) !== false ) {
				unset( $old_question_terms[$key] );
			}
			
			$result = wp_set_object_terms( $old_question->ID, $old_question_terms,  EPKB_Help_Dialog_Handler::get_help_dialog_location_taxonomy_name(), false );
			if ( is_wp_error( $result ) ) {
				EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 62, $result ) );
			}

			$result = $this->update_question_order_sequence( $old_question->ID );
			if ( empty( $result ) ) {
				EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 63 ) );
			}
		}

		wp_die( json_encode( array( 'status' => 'success', 'message' => __( 'Location Saved', 'echo-knowledge-base') ) ) );
	}

	/**
	 * Edit Question dialog: user added a new question or updated existing one.
	 */
	public function save_question_data() {

		// wp_die if nonce invalid or user does not have correct permission
		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die();

		$form = EPKB_Utilities::post( 'form', [
										'epkb_help_location' => 0,
										'epkb_help_question_id' => 0,
										'epkb_help_question' => '',
										'epkb_help_editor' => ''
									], 'form' );
		
		$location_id = (int)$form['epkb_help_location'];
		$question_id = (int)$form['epkb_help_question_id'];
		$question_title = (string)$form['epkb_help_question'];
		$question_content = (string)$form['epkb_help_editor'];
		
		if ( empty( $location_id ) || empty( $question_title ) ) {  // size handled in JS
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 29 ) );
		}

		// update current post 
		if ( $question_id ) {
			$post_id = wp_update_post( wp_slash( array(
				'ID' => $question_id,
				'post_content' => $question_content,
				'post_title' => $question_title,
				'post_status' => 'publish'
			) ), true );
		} else {
			$post_id = wp_insert_post( wp_slash( array(
				'post_type' => EPKB_Help_Dialog_Handler::get_post_type(),
				'post_content' => $question_content,
				'post_title' => $question_title,
				'post_status' => 'publish'
			) ), true );
		}

		$error_msg = empty($question_id) ? __( 'Cannot insert the new question.', 'echo-knowledge-base' ) : __( 'Cannot update the question', 'echo-knowledge-base' );

		if ( is_wp_error( $post_id ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 1, $post_id ) );
		}

		// should never be true. True if there is no taxonomy
		$post_terms = wp_get_post_terms( $post_id, EPKB_Help_Dialog_Handler::get_help_dialog_location_taxonomy_name(), array( 'fields' => 'ids' ));
		if ( is_wp_error( $post_terms ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 58, $post_terms ) );
		}

		// location does not have this question then add it
		if ( ! in_array( $location_id, $post_terms ) ) {
			$post_terms[] = $location_id;

			$result = wp_set_object_terms( $post_id, $post_terms,  EPKB_Help_Dialog_Handler::get_help_dialog_location_taxonomy_name(), false );
			if ( is_wp_error( $result ) ) {
				EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 58, $result ) );
			}

			$result = $this->update_question_order_sequence( $post_id );
			if ( empty( $result ) ) {
				EPKB_Utilities::ajax_show_error_die( $error_msg . ' (61) ' );
			}
		}
		
		$post = get_post( $post_id );
		if ( empty($post) ) {
			EPKB_Utilities::ajax_show_error_die( $error_msg . ' (59)' );
		}
		
		ob_start();
		EPKB_Help_Dialog_Locations_Page::display_single_article( array(
			'container_ID' => $post->ID,
			'name' => $post->post_title,
			'modified' => strtotime ($post->post_modified_gmt)
		) );
		$html = ob_get_clean();
		
		wp_die( json_encode( array( 'status' => 'success', 'message' => __( 'Question Saved', 'echo-knowledge-base') , 'data' => array(
									'title' => $post->post_title, 'id' => $post->ID, 'html' => $html
		) ) ) );
	}

	/**
	 * Retrieve question to show to user for edit in the dialog
	 */
	public function get_question_data() {

		// wp_die if nonce invalid or user does not have correct permission
		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die();
		
		$question_id = (int)EPKB_Utilities::post( 'question_id' );
		$question = get_post( $question_id );
		if ( empty($question) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 881 ) );
		}
		
		wp_die( json_encode( array( 'status' => 'success', 'message' => '', 'data' => array(
			'title' => $question->post_title,
			'content' => $question->post_content,
		) ) ) );
	}
	
	/**
	 * Delete Question. It will be in the trash just in case 30 days, but user can't know this.
	 */
	public function delete_question() {

		// wp_die if nonce invalid or user does not have correct permission
		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die();
		
		$post_id = (int) EPKB_Utilities::post( 'id' );
		if ( ! $post_id ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 30 ) );
		}
		
		if ( ! get_post( $post_id ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 31 ) );
		}
		
		if ( ! wp_delete_post( $post_id ) ) {
			EPKB_Utilities::ajax_show_error_die( __( 'Cannot delete the question.', 'echo-knowledge-base' ) . ' (32)');
		}
		
		wp_die( json_encode( array( 'status' => 'success', 'message' => __( 'Question Deleted', 'echo-knowledge-base') ) ) );
	}

	/**
	 * When a Question is saved ensure its meta data has default order sequence
	 * Help Dialog questions (posts) have order sequence number for each location in which they are used. Format: 'meta_key' => 'epkb_faq_order_' . $location_id
	 *
	 * @param $post_ID
	 * @return bool
	 */
	public function update_question_order_sequence( $post_ID ) {
		/** @var wpdb $wpdb */
		global $wpdb;

		// 1. get Help Dialog Locations (categories) this question (post) is used in
		$post_term_ids = wp_get_post_terms( $post_ID, EPKB_Help_Dialog_Handler::get_help_dialog_location_taxonomy_name(), array('fields' => 'ids') );
		if ( is_wp_error($post_term_ids) ) {
			return false;
		}

		// 2. get ordering sequence number for this question for each location
		$sequence_num_locations = $wpdb->get_results( "SELECT * 
										FROM {$wpdb->prefix}postmeta 
										WHERE meta_key LIKE 'epkb_faq_order_%' AND post_id = {$post_ID}	" );
		if ( ! is_array($sequence_num_locations) ) {
			return false;
		}

		// 3. remove sequence number meta for locations not used any more
		$excluded_meta_ids = [];

		foreach ( $sequence_num_locations as $sequence_num_location ) {
			$result_term_id = (int) str_replace( 'epkb_faq_order_', '', $sequence_num_location->meta_key );
			if ( ! in_array( $result_term_id, $post_term_ids ) ) {
				$excluded_meta_ids[] = absint( $sequence_num_location->meta_id );
			}
		}

		if ( ! empty( $excluded_meta_ids ) ) {
			$wpdb->query( "DELETE FROM {$wpdb->prefix}postmeta WHERE meta_id IN (" . implode( ',', $excluded_meta_ids ) . ")" );
		}

		// check if the post have meta and place it at the end of questions list
		foreach ( $post_term_ids as $term_id ) {
			$sequence = get_post_meta( $post_ID, 'epkb_faq_order_' . $term_id, true );
			if ( empty( $sequence ) ) {
				update_post_meta( $post_ID, 'epkb_faq_order_' . $term_id, 99999 );
			}
		}

		return true;
	}

}
