<?php

defined( 'ABSPATH' ) || exit();

class EPKB_Help_Dialog_Search {

	const SEARCH_INPUT_LENGTH = 200;  // let's limit the input string

	public function __construct() {

		add_action( 'wp_ajax_epkb_help_dialog_find_location_pages', array( $this, 'find_location_pages' ) );
		add_action( 'wp_ajax_nopriv_epkb_help_dialog_find_location_pages', array( 'EPKB_Utilities', 'user_not_logged_in' ) );

		add_action( 'wp_ajax_epkb_help_dialog_search_kb', array( $this, 'search_kb' ) );
		add_action( 'wp_ajax_nopriv_epkb_help_dialog_search_kb', array( $this, 'search_kb' ) );   // users not logged in should be able to search as well
	}

	/**
	 * When user is defining which posts/pages the Help Dialog for this location will show, this filters posts/pages based on user input
	 */
	public function find_location_pages() {

		// wp_die if nonce invalid or user does not have correct permission
		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die();

		$search_value = EPKB_Utilities::post( 'search_value' );
		$search_post_type = EPKB_Utilities::post( 'search_post_type' );  // what user is filtering: 'post', 'page' or 'cpt'

		$search_post_type_query = $search_post_type;

		// user is looking for Custom Post Types
		if ( $search_post_type == 'cpt' ) {
			$search_post_type_query = get_post_types( [
				'_builtin'  => 0, // not WordPress post types
				'public'    => 1  // only post types for frontend
			] );
		}

		$found_posts = [];

		// if user searches for pages and home page is actual page then include it as first entry by default
		$page_on_front = get_option( 'page_on_front' );
		if ( $search_post_type == 'page' && empty( $page_on_front ) ) {
			$found_posts = [
				[
					'post_id' => 0,
					'title' => __( 'Home Page', 'echo-knowledge-base' ),
					'disabled' => false,
					'type' => $search_post_type
				]
			];
		}

		// search for posts/pages/cpts
		$posts = get_posts( [
			'post_type'              => $search_post_type_query,
			'post_status'            => array('publish', 'private'),
			'numberposts'            => -1,
			's'                      => $search_value
		] );

		foreach ( $posts as $post ) {
			$found_posts[] = [
				'post_id' => $post->ID,
				'title' => get_the_title( $post ),
				'disabled' => false,
				'type' => $search_post_type
			];
		}

		if ( empty( $found_posts ) ) {
			wp_die( json_encode( array( 'status' => 'success', 'message' => '', 'data' => '<li disabled>' . esc_html__( 'No results found', 'echo-knowledge-base' ) . '</li>' ) ) );
		}
		
		// check if the posts already assigned 
		$all_locations = EPKB_FAQ_Utilities::get_help_dialog_location_categories_unfiltered();
		if ( $all_locations === null ) {
			$all_locations = [];
		}
		
		$current_location_id = EPKB_Utilities::post( 'location_id' );
		$current_location = EPKB_Help_Dialog_Handler::get_location_by_id_or_default( $current_location_id );
		
		// show pages not in current location and mark pages in different locations
		foreach ( $found_posts as $key => $found_post ) {

			// do not show pages this location belongs to
			if ( EPKB_FAQ_Utilities::is_page_in_location( $found_post['post_id'], $search_post_type, $current_location ) ) {
				continue;
			}
			
			$found_posts[$key]['disabled'] = EPKB_FAQ_Utilities::is_page_in_locations( $found_post['post_id'], $search_post_type, $all_locations );
		}
		
		$output = '';
		foreach ( $found_posts as $found_post ) {
			
			if ( $found_post['disabled'] ) {
				$output .= '<li data-post-id="' . $found_post['post_id'] . '" disabled>' . $found_post['title'] . ' <small>[' . __( 'Location defined', 'echo-knowledge-base' ) . ']</small></li>';
			} else {
				$output .= '<li data-post-id="' . $found_post['post_id'] . '">' . $found_post['title'] . '</li>';
			}
		}
		
		wp_die( json_encode( array(	'status' => 'success', 'message' => '', 'data' => $output ) ) );
	}

	/**
	 * Search Help dialog KB for articles within defined categories
	 *
	 * @param bool $is_front - set current screen to ensure links have https if needed
	 */
	public function search_kb( $is_front=true ) {

		// remove question marks
		$search_terms = EPKB_Utilities::get( 'search_terms', '', 'text', self::SEARCH_INPUT_LENGTH );
		$search_terms = stripslashes( $search_terms );
		$search_terms = str_replace('?', '', $search_terms);
		$search_terms = str_replace( array( "\r", "\n" ), '', $search_terms );

		// require minimum size of search word(s)
		if ( empty( $search_terms ) ) {
			wp_die( json_encode( array( 'status' => 'success', 'no_results' => __( 'No Matches Found', 'echo-knowledge-base' ) ) ) );
		}

		$kb_ids = EPKB_Utilities::get( 'kb_ids' );
		$kb_ids_array = empty( $kb_ids) ? [] : explode( ',', $kb_ids );

		// search for given keyword(s)
		$article_results = array();

		foreach ( $kb_ids_array as $kb_id ) {
			$article_results = array_merge( $article_results, $this->execute_search( EPKB_Core_Utilities::sanitize_kb_id( $kb_id ), $search_terms, 'article' ) );
		}
		
		// this search don't use kb_ids, but filters need it, so will be default
		$faq_results = $this->execute_search( EPKB_KB_Config_DB::DEFAULT_KB_ID, $search_terms, 'faq' );

		if ( empty( $faq_results ) && empty( $article_results ) ) {
			wp_die( json_encode( array( 'status' => 'success', 'no_results' => __( 'No Matches Found', 'echo-knowledge-base' ) ) ) );
		}

		// ensure that links have https if the current schema is https
		if ( $is_front ) {
			set_current_screen( 'front' );
		}

		if ( empty( $article_results ) ) {
			$article_search_result = __( 'No Matches Found', 'echo-knowledge-base' );
		} else {

			$article_search_result = '<ul>';

			// display one line for each search result
			foreach ( $article_results as $post ) {

				$article_url = get_permalink( $post->ID );
				if ( empty( $article_url ) || is_wp_error( $article_url ) ) {
					continue;
				}

				// linked articles have their own icon
				$article_title_icon = 'ep_font_icon_document';
				if ( has_filter( 'eckb_single_article_filter' ) ) {
					$article_title_icon = apply_filters( 'eckb_article_icon_filter', $article_title_icon, $post->ID );
					$article_title_icon = empty( $article_title_icon ) ? 'epkbfa-file-text-o' : $article_title_icon;
				}

				$article_data = 'data-kb-article-id="' . $post->ID . '" data-url="' . $article_url . '"';

				$article_search_result .=
					'<li data-type="search" class="epkb-hd_article-item" ' . $article_data . ' >  
												
						<span class="epkb-hd_article-item__name">
							<span class="epkb-hd_article-item__icon ' . $article_title_icon . '"></span>
							<span class="epkb-hd_article-item__text">' . esc_html( $post->post_title ) . '</span>
						</span>
							
					</li>';
			}

			$article_search_result .= '</ul>';
		}

		if ( empty( $faq_results ) ) {
			$faq_search_result = __( 'No Matches Found', 'echo-knowledge-base' );
		} else {

			$faq_search_result = '';

			// display one line for each search result
			foreach ( $faq_results as $post ) {

				$faq_url = get_permalink( $post->ID );
				if ( empty( $faq_url ) || is_wp_error( $faq_url ) ) {
					continue;
				}

				$faq_search_result .= EPKB_HTML_Forms::get_faq_item_html( $post->post_title, $post->post_content );
			}
		}

		wp_die( json_encode( array( 'status' => 'success', 'article_results' => $article_search_result, 'faq_results' => $faq_search_result, 'no_results' => '' ) ) );
	}

	/**
	 * Call WP query to get matching terms (any term OR match)
	 *
	 * @param $kb_id
	 * @param $search_terms
	 * @param string $type
	 * @return array
	 */
	private function execute_search( $kb_id, $search_terms, $type = 'article' ) {

		// add-ons can adjust the search
		if ( $type == 'article' && has_filter( 'eckb_execute_search_filter' ) ) {
			$result = apply_filters('eckb_execute_search_filter', '', $kb_id, $search_terms );
			if ( is_array($result) ) {
				return $result;
			}
		}

		$result = array();
		$post_type = $type == 'article' ? EPKB_KB_Handler::get_post_type( $kb_id ) : EPKB_Help_Dialog_Handler::get_post_type();

		$search_params = array(
				's' => $search_terms,
				'post_type' => $post_type,
				'ignore_sticky_posts' => true,  // sticky posts will not show at the top
				'posts_per_page' => 15,         // limit search results
				'no_found_rows' => true,        // query only posts_per_page rather than finding total nof posts for pagination etc.
				'cache_results' => false,       // don't need that for mostly unique searches
				'orderby' => 'relevance'
		);

		// define post_status only for older versions of KB
		/* FUTURE TO DO if ( ! EPKB_Utilities::is_new_user( '8.0.0' ) ) {
			$search_params['post_status'] = EPKB_Utilities::is_amag_on( true ) ? array('publish', 'private') : array('publish');
		} */

		$found_posts_obj = new WP_Query( $search_params );
		if ( ! empty($found_posts_obj->posts) ) {
			$result = $found_posts_obj->posts;
			wp_reset_postdata();
		}

		return $result;
	}
}