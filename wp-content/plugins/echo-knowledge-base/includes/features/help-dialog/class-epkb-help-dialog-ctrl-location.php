<?php

defined( 'ABSPATH' ) || exit();

/**
 * Handle user submission from Help dialog
 */
class EPKB_Help_Dialog_Ctrl_Location {

	const OPTION_EPKB_LOCATIONS = 'epkb_help_dialog_locations';

	public function __construct() {
		add_action( 'wp_ajax_epkb_create_location', array( $this, 'create_location' ) );
		add_action( 'wp_ajax_nopriv_epkb_create_location', array( $this, 'create_location' ) );
		add_action( 'wp_ajax_epkb_get_location', array( $this, 'get_location' ) );
		add_action( 'wp_ajax_nopriv_epkb_get_location', array( $this, 'get_location' ) );
		add_action( 'wp_ajax_epkb_update_location', array( $this, 'update_location' ) );
		add_action( 'wp_ajax_nopriv_epkb_update_location', array( $this, 'update_location' ) );
		add_action( 'wp_ajax_epkb_delete_location', array( $this, 'delete_location' ) );
		add_action( 'wp_ajax_nopriv_epkb_delete_location', array( $this, 'delete_location' ) );
		add_action( 'wp_ajax_epkb_update_location_list', array( $this, 'update_location_list' ) );
		add_action( 'wp_ajax_nopriv_epkb_update_location_list', array( $this, 'update_location_list' ) );

		add_action( 'wp_ajax_epkb_search_posts', array( $this, 'search_posts' ) );
	}

	/**
	 * Add a new LOCATION
	 */
	public function create_location() {

		if ( empty(EPKB_Utilities::post('_wpnonce_epkb_create_location')) || ! wp_verify_nonce( EPKB_Utilities::post('_wpnonce_epkb_create_location'), '_wpnonce_epkb_create_location' ) ) {
			EPKB_Utilities::ajax_show_error_die( __( 'You do not have permission to edit this knowledge base', 'echo-knowledge-base' )  . '(34)');
		}

		// ensure user has correct permissions
		if ( ! current_user_can( EPKB_Utilities::EPKB_ADMIN_CAPABILITY ) ) {
			EPKB_Utilities::ajax_show_error_die(__( 'You do not have permission to change access', 'echo-knowledge-base' ) . ' (29)');
		}

		$location_type = EPKB_Utilities::post( 'location_type' );  // TODO
		$location_id = EPKB_Utilities::post( 'location_id' );

		$selected_questions = [];
		$selected_questions_raw = $_POST['selected_questions'];
		foreach ( $selected_questions_raw as $selected_question ) {
			$selected_questions[] = sanitize_text_field( $selected_question );
		}

		// Location & Answer should not be empty
		if ( empty($location_id) || empty($selected_questions) ) {
			EPKB_Utilities::ajax_show_error_die(__( 'Please add location and choose locations', 'echo-knowledge-base' ) );
		}

		$location['id'] = uniqid();
		$location['location_id'] = $location_id;
		$location['selected_questions'] = $selected_questions;

		$locations = EPKB_Utilities::get_wp_option( self::OPTION_EPKB_LOCATIONS, array() );
		if ( is_wp_error( $locations ) ) {
			EPKB_Logging::add_log( 'Error retrieving Locations', $locations );
			EPKB_Utilities::ajax_show_error_die(__( 'Error retrieving Locations', 'echo-knowledge-base' ) . ' (27)');
		}

		$locations[] = $location;

		$result = EPKB_Utilities::save_wp_option( self::OPTION_EPKB_LOCATIONS, $locations, true );
		if ( is_wp_error( $result ) ) {
			EPKB_Logging::add_log( 'Could not create the location', $result );
			EPKB_Utilities::ajax_show_error_die(__( 'Could not create the location', 'echo-knowledge-base' ) . ' (31)');
		}

		wp_die( json_encode( array( 'status' => 'success', 'message' => __( 'Configuration Saved', 'echo-knowledge-base') ) ) );
	}

	/**
	 * Get LOCATION to edit
	 */
	public function get_location() {

		if ( empty(EPKB_Utilities::post('_wpnonce_epkb_location')) || ! wp_verify_nonce( EPKB_Utilities::post('_wpnonce_epkb_location'), '_wpnonce_epkb_location' ) ) {
			EPKB_Utilities::ajax_show_error_die( __( 'You do not have permission to edit this knowledge base', 'echo-knowledge-base' )  . '(34)');
		}

		// ensure user has correct permissions
		if ( ! current_user_can( EPKB_Utilities::EPKB_ADMIN_CAPABILITY ) ) {
			EPKB_Utilities::ajax_show_error_die(__( 'You do not have permission to change access', 'echo-knowledge-base' ) . ' (29)');
		}

		$location_id = EPKB_Utilities::post( 'location_id' );
		if ( empty($location_id) ) {
			EPKB_Utilities::ajax_show_error_die(__( 'Invalid input', 'echo-knowledge-base' ) );
		}

		$locations = EPKB_Utilities::get_wp_option( self::OPTION_EPKB_LOCATIONS, array() );
		if ( is_wp_error( $locations ) ) {
			EPKB_Logging::add_log( 'Error retrieving Locations', $locations );
			EPKB_Utilities::ajax_show_error_die(__( 'Error retrieving Locations', 'echo-knowledge-base' ) . ' (27)');
		}

		// update the LOCATION
		$found_location = [];
		foreach( $locations as $ix => $location ) {
			if ( $location['id'] == $location_id ) {
				$found_location = $location;
				break;
			}
		}

		if ( empty($found_location) ) {
			EPKB_Logging::add_log( 'Error retrieving Locations', $locations );
			EPKB_Utilities::ajax_show_error_die(__( 'Error retrieving Locations', 'echo-knowledge-base' ) . ' (271)');
		}

		wp_die( json_encode( array( 'status' => 'success', 'message' => '', 'location' => $found_location ) ) );
	}

	/**
	 * Update LOCATION
	 */
	public function update_location() {

		if ( empty(EPKB_Utilities::post('_wpnonce_epkb_location')) || ! wp_verify_nonce( EPKB_Utilities::post('_wpnonce_epkb_location'), '_wpnonce_epkb_location' ) ) {
			EPKB_Utilities::ajax_show_error_die( __( 'You do not have permission to edit this knowledge base', 'echo-knowledge-base' )  . '(34)');
		}

		// ensure user has correct permissions
		if ( ! current_user_can( EPKB_Utilities::EPKB_ADMIN_CAPABILITY ) ) {
			EPKB_Utilities::ajax_show_error_die(__( 'You do not have permission to change access', 'echo-knowledge-base' ) . ' (29)');
		}

		$location_id = EPKB_Utilities::post( 'location_id' );
		if ( empty($location_id) ) {
			EPKB_Utilities::ajax_show_error_die(__( 'Invalid input', 'echo-knowledge-base' ) );
		}

		$location = EPKB_Utilities::post( 'location_input' );
		$answer = EPKB_Utilities::post( 'answer_input' );

		// Location & Answer should not be empty
		if ( empty($location) || empty($answer) ) {
			EPKB_Utilities::ajax_show_error_die(__( 'Please add location and answer', 'echo-knowledge-base' ) );
		}

		$locations = EPKB_Utilities::get_wp_option( self::OPTION_EPKB_LOCATIONS, array() );
		if ( is_wp_error( $locations ) ) {
			EPKB_Logging::add_log( 'Error retrieving Locations', $locations );
			EPKB_Utilities::ajax_show_error_die(__( 'Error retrieving Locations', 'echo-knowledge-base' ) . ' (27)');
		}

		// update the LOCATION
		foreach( $locations as $ix => $location ) {
			if ( $location['id'] == $location_id ) {
				$locations[$ix]['location'] = $location;
				$locations[$ix]['answer'] = $answer;
				break;
			}
		}

		$result = EPKB_Utilities::save_wp_option( self::OPTION_EPKB_LOCATIONS, $locations, true );
		if ( is_wp_error( $result ) ) {
			EPKB_Logging::add_log( 'Could not update the location', $result );
			EPKB_Utilities::ajax_show_error_die(__( 'Could not update the location', 'echo-knowledge-base' ) . ' (31)');
		}

		wp_die( json_encode( array( 'status' => 'success', 'message' => __( 'Configuration Saved', 'echo-knowledge-base') ) ) );
	}

	/**
	 * Remove LOCATION
	 */
	public function delete_location() {

		if ( empty(EPKB_Utilities::post('_wpnonce_epkb_location')) || ! wp_verify_nonce( EPKB_Utilities::post('_wpnonce_epkb_location'), '_wpnonce_epkb_location' ) ) {
			EPKB_Utilities::ajax_show_error_die( __( 'You do not have permission to edit this knowledge base', 'echo-knowledge-base' )  . ' (34)');
		}

		// ensure user has correct permissions
		if ( ! current_user_can( EPKB_Utilities::EPKB_ADMIN_CAPABILITY ) ) {
			EPKB_Utilities::ajax_show_error_die(__( 'You do not have permission to change access', 'echo-knowledge-base' ) . ' (29)');
		}

		$location_id = EPKB_Utilities::post( 'location_id' );
		if ( empty($location_id) ) {
			EPKB_Utilities::ajax_show_error_die(__( 'Invalid input', 'echo-knowledge-base' ) );
		}

		$locations = EPKB_Utilities::get_wp_option( self::OPTION_EPKB_LOCATIONS, array() );
		if ( is_wp_error( $locations ) ) {
			EPKB_Logging::add_log( 'Error retrieving Locations', $locations );
			EPKB_Utilities::ajax_show_error_die(__( 'Error retrieving Locations', 'echo-knowledge-base' ) . ' (27)');
		}

		foreach( $locations as $ix => $location ) {
			if ( $location['id'] == $location_id ) {
				unset($locations[$ix]);
				break;
			}
		}

		$result = EPKB_Utilities::save_wp_option( self::OPTION_EPKB_LOCATIONS, $locations, true );
		if ( is_wp_error( $result ) ) {
			EPKB_Logging::add_log( 'Could not delete LOCATION', $result );
			EPKB_Utilities::ajax_show_error_die(__( 'Could not delete LOCATION', 'echo-knowledge-base' ) . ' (31)');
		}

		wp_die( json_encode( array( 'status' => 'success', 'message' => __( 'Configuration Saved', 'echo-knowledge-base') ) ) );
	}

	/**
	 * Refresh list of current LOCATIONs
	 */
	public function update_location_list() {

		if ( empty(EPKB_Utilities::post('_wpnonce_epkb_location')) || ! wp_verify_nonce( EPKB_Utilities::post('_wpnonce_epkb_location'), '_wpnonce_epkb_location' ) ) {
			EPKB_Utilities::ajax_show_error_die( __( 'You do not have permission to edit this knowledge base', 'echo-knowledge-base' )  . ' (34)');
		}

		// ensure user has correct permissions
		if ( ! current_user_can( EPKB_Utilities::EPKB_ADMIN_CAPABILITY ) ) {
			EPKB_Utilities::ajax_show_error_die(__( 'You do not have permission to change access', 'echo-knowledge-base' ) . ' (29)');
		}

		// Get updated locations list
		ob_start();
		$result = EPKB_Help_Dialog_View_Location::display_list_of_records();
		$output = ob_get_clean();

		if ( ! $result ) {
			EPKB_Utilities::ajax_show_error_die(__( 'Internal error occurred', 'echo-knowledge-base' ) . ' (23)');
		}

		EPKB_Utilities::ajax_show_content( $output );
	}
	
	/**
	 * Find posts/pages matching user input
	 */
	public function search_posts() {

		// verify that request is authentic
		if ( empty(EPKB_Utilities::post('_wpnonce_epkb_post_search')) || ! wp_verify_nonce( EPKB_Utilities::post('_wpnonce_epkb_post_search'), '_wpnonce_epkb_post_search' ) ) {
			EPKB_Utilities::ajax_show_error_die( __( 'You do not have permission to edit this knowledge base', 'echo-knowledge-base' )  . '(34)');
		}

		// ensure user has correct permissions
		if ( ! current_user_can( EPKB_Utilities::EPKB_ADMIN_CAPABILITY ) ) {
			EPKB_Utilities::ajax_show_error_die(__( 'You do not have permission to change access', 'echo-knowledge-base' ) . ' (29)');
		}

		$page = EPKB_Utilities::post( 'search_page' );
		$search_value = EPKB_Utilities::post( 'search_value' );
		$search_post_type = EPKB_Utilities::post( 'search_post_type' );

		$output = $page == 1 ? '<ul class="epkb_search_results">' : '';

		$results = $this->posts_query( $search_value, $page );
		if ( empty( $results ) ) {
			if ( $page == 1 ) {
				$output .= '<li class="epkb__no_res">'. esc_html__( 'No results found', 'echo-knowledge-base' ) . '</li>';
			}
		} else {
			foreach( $results as $single_post ) {
				$output .= '<li data-post_id="' . esc_attr( $single_post['id'] ). '">' . esc_html( $single_post['post_type'] ) . ' - ' . esc_html( $single_post['title'] ) . '</li>';
			}
		}

		$output .= $page == 1 ? '</ul>' : '';

		wp_die( json_encode( array( 'status' => 'success', 'message' => '', 'page' => $page, 'data' => $output ) ) );
	}

	/**
	 * Find matching posts/pages.
	 *
	 * @param $search_value
	 * @param $page
	 * @param $search_post_type
	 * @return array|false
	 */
	private function posts_query( $search_value, $page, $search_post_type='page' ) {

		$page = empty($page) ? 1 : absint($page);

		$query = array(
			'post_type'              => $search_post_type,
			'suppress_filters'       => true,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
			'post_status'            => array('publish', 'private'),
			'posts_per_page'         => 20,
			's'                      => $search_value,
			'offset'                 => $page > 1 ? 20 * ( $page - 1 ) : 0
		);

		$get_posts = new WP_Query;
		$posts = $get_posts->query( $query );
		if ( empty($posts) || ! $get_posts->post_count ) {
			return false;
		}

		$results = array();
		foreach ( $posts as $post ) {
			$results[] = array(
				'id'        => (int) $post->ID,
				'title'     => trim( esc_html( strip_tags( get_the_title( $post ) ) ) ),
				'post_type' => $post->post_type,
			);
		}

		return $results;
	}
}