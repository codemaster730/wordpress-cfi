<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Handle Help Dialog data
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 */
class EPKB_Help_Dialog_Handler {

	const FAQ_POST_TYPE = 'epkb_faq';  // changing this requires db update

	const HELP_DIALOG_LOCATION_TAXONOMY_SUFFIX = '_help_dialog_location';  // changing this requires db update; do not translate
	const HELP_DIALOG_LOCATION_META = 'epkb_help_dialog_location';
	const HELP_DILAOG_LOCATION_HOME_PAGE = 'home_page';

	const HELP_DIALOG_STATUS_META = 'epkb_help_dialog_status';
	const HELP_DIALOG_STATUS_PUBLIC = 'published';
	const HELP_DIALOG_STATUS_DRAFT = 'draft';
	const HELP_DIALOG_KB_IDS = 'epkb_help_dialog_kb_ids';

	public static function create_demo_help_dialog() {

		// demo Help Dialog location is Home Page
		$location_name = __( 'Home Page', 'echo-knowledge-base' );
		$location_meta = array( self::get_home_location_page() );
		$location_id = self::create_help_dialog_location( $location_name, $location_meta );
		if ( is_wp_error($location_id) || empty($location_id) ) {
			return;
		}

		$taxonomy = self::get_help_dialog_location_taxonomy_name();

		$article3_title = __( 'Where can I find documentation?', 'echo-knowledge-base' );
		$article3_content = __( '(Example) We have a detailed knowledge base about our product and services', 'echo-knowledge-base' ) .
		                    ' <a href="https://www.echoknowledgebase.com/documentation/" target="_blank">' . __( 'here', 'echo-knowledge-base' ) . '<span class="epkbfa epkbfa-external-link"></span></a>';
		$article3_id = self:: create_sample_article( $location_id, $article3_title, $article3_content, $taxonomy, 2 );
		if ( empty($article3_id) ) {
			return;
		}

		$article2_title = __( 'I am looking for a feature.', 'echo-knowledge-base' );  // or Do you have a list of product features?
		$article2_content = __( '(Example) You can find a list of all of our software features here.', 'echo-knowledge-base' ) .
		                    ' <a href="https://www.echoknowledgebase.com/wordpress-add-ons/" target="_blank"><span class="epkbfa epkbfa-external-link"></span></a>';
		$article2_id = self:: create_sample_article( $location_id, $article2_title, $article2_content, $taxonomy, 1 );
		if ( empty($article2_id) ) {
			return;
		}

		$article1_title = __( 'What payment methods do you accept?', 'echo-knowledge-base' );
		$article1_content = __( '(Example) We accept all main methods of payments: VISA, Mastercard and PayPal.', 'echo-knowledge-base' );
		$article1_id = self:: create_sample_article( $location_id, $article1_title, $article1_content, $taxonomy, 3 );
		if ( empty($article1_id) ) {
			return;
		}
	}

	private static function create_sample_article( $faq_term_id, $post_title, $post_content, $taxonomy, $order_sequence='' ) {

		$my_post = array(
			'post_title'    => $post_title,
			'post_type'     => self::get_post_type(),
			'post_content'  => $post_content,
			'post_status'   => 'publish',
		);

		// create article under category
		$post_id = wp_insert_post( $my_post );
		if ( is_wp_error( $post_id ) || empty($post_id) ) {
			$wp_error = is_wp_error( $post_id ) ? $post_id : new WP_Error(124, "post_id is emtpy");
			EPKB_Logging::add_log( 'Could not insert post for a new FAQ', $wp_error );
			return null;
		}

		if ( ! empty($order_sequence) && $taxonomy == self::get_help_dialog_location_taxonomy_name() ) {
			update_post_meta( $post_id, 'epkb_faq_order_' . $faq_term_id, 99999 );
		}

		$result = wp_set_object_terms( $post_id, $faq_term_id, $taxonomy );
		if ( is_wp_error($result) ) {
			EPKB_Logging::add_log( 'Could not insert default category for a new FAQ. post id: ' . $post_id . ' term id: ' . $faq_term_id . ', FAQ ID: ', $result );
			return null;
		}

		return $post_id;
	}

	/**
	 * Creates a new Help Dialog category which represents a location. Returns the category id.
	 *
	 * @param $location_name
	 * @param $page_locations
	 * @param string $location_status
	 * @param array $location_kb_ids
	 *
	 * @return int|WP_Error - location category id or error
	 */
	public static function create_help_dialog_location( $location_name, $page_locations, $location_status=self::HELP_DIALOG_STATUS_DRAFT, $location_kb_ids = [] ) {

		$category_description = __('Help Dialog Location', 'echo-knowledge-base');
		$args = array( 'description' => $category_description );
		$taxonomy = self::get_help_dialog_location_taxonomy_name();

		// insert the new Location
		$new_term = wp_insert_term( $location_name, $taxonomy, $args );
		if ( is_wp_error( $new_term ) ) {
			EPKB_Logging::add_log( 'Failed to insert category for a new FAQ. cat name: ' . $location_name, $new_term );
			return $new_term;
		}
		if ( ! isset( $new_term['term_id'] ) ) {
			EPKB_Logging::add_log( 'Failed to insert category for a new FAQ. cat name: ' . $location_name );
			return new WP_Error( 'create-hd', 'Failed to insert category for new FAQ. cat name: ' . $location_name );
		}

		$result = add_term_meta( $new_term['term_id'], self::HELP_DIALOG_LOCATION_META, $page_locations, true );
		if ( empty($result) || is_wp_error($result) ) {
			EPKB_Logging::add_log( 'Error adding term meta', $result );
			return empty($result) ? new WP_Error( 'create-hd', 'Error adding term meta' ) : $result;
		}

		// location starts with Draft status
		$result = add_term_meta( $new_term['term_id'], self::HELP_DIALOG_STATUS_META, $location_status, true );
		if ( empty($result) || is_wp_error($result) ) {
			EPKB_Logging::add_log( 'Error adding term meta', $result );
			return empty($result) ? new WP_Error( 'create-hd', 'Error adding term meta' ) : $result;
		}
		
		$result = add_term_meta( $new_term['term_id'], self::HELP_DIALOG_KB_IDS, $location_kb_ids, true );
		if ( empty($result) || is_wp_error($result) ) {
			EPKB_Logging::add_log( 'Error adding term meta', $result );
			return empty($result) ? new WP_Error( 'create-hd', 'Error adding term meta' ) : $result;
		}
		
		return $new_term['term_id'];
	}

	/**
	 * WordPress does not distinguish between DB error and updating existing value that is the same as in DB so need wrapper.
	 * @param $category_id
	 * @param $meta_key
	 * @param $meta_value
	 * @param bool $is_array
	 *
	 * @return bool
	 */
	public static function update_term_meta( $category_id, $meta_key, $meta_value, $is_array=false ) {

		// 1. get current meta data from DB. Can be empty
		$term_value = get_term_meta( $category_id, $meta_key, true );

		if ( ! empty( $term_value ) && $is_array && ! is_array($term_value) ) {
			return false;
		}

		// all good if database already contains our value
		if ( ! empty( $term_value ) && ! $is_array && $term_value == $meta_value ) {
			return true;
		}
		
		if ( ! empty( $term_value ) && $is_array && ! array_diff( $term_value, $meta_value ) && ! array_diff( $meta_value, $term_value ) ) {
			return true;
		}

		$result = update_term_meta( $category_id, $meta_key, $meta_value );
		if ( is_wp_error($result) ) {
			return false;
		}

		return true;
	}

	/**
	 * Is this Help Dialog Locatin taxonomy?
	 *
	 * @param $taxonomy
	 * @return bool
	 */
	public static function is_help_dialog_location_taxonomy( $taxonomy ) {
		if ( empty($taxonomy) || ! is_string($taxonomy) ) {
			return false;
		}

		// we are only interested in FAQ articles
		return strncmp($taxonomy, self::HELP_DIALOG_LOCATION_TAXONOMY_SUFFIX, strlen(self::HELP_DIALOG_LOCATION_TAXONOMY_SUFFIX)) == 0;
	}

	/**
	 * Retrieve FAQ post type name e.g. ep epkb_faq_1
	 * @return string
	 */
	public static function get_post_type() {
		return EPKB_Help_Dialog_Handler::FAQ_POST_TYPE;
	}

	/**
	 * Return category name
	 * @return string
	 */
	public static function get_help_dialog_location_taxonomy_name() {
		return self::get_post_type() . self::HELP_DIALOG_LOCATION_TAXONOMY_SUFFIX;
	}
	
	/**
	 * return Location for Home Page. format: home_page:post_id
	 */
	public static function get_home_location_page() {
		// get home page id 
		$home_page_id = get_option( 'page_on_front' );
		return self::HELP_DILAOG_LOCATION_HOME_PAGE . ':' . ( empty( $home_page_id ) ? 0 : $home_page_id );
	}

	/**
	 * Return location object or default 
	 *
	 * @param $location_id
	 * @return stdClass
	 */
	public static function get_location_by_id_or_default( $location_id = 0 ) {
		$location = new stdClass();
		$location->location_id = 0;
		$location->locations = [
			'selected_pages' => [],
			'excluded_pages' => []
		];
		$location->name = '';
		$location->status = EPKB_Help_Dialog_Handler::HELP_DIALOG_STATUS_DRAFT;
		$location->kb_ids = [];
		
		if ( empty( $location_id ) ) {
			return $location;
		}
		
		$all_locations = EPKB_FAQ_Utilities::get_help_dialog_location_categories_unfiltered();
		if ( empty( $all_locations ) || empty( $all_locations[$location_id] ) ) {
			return $location;
		}
	
		return $all_locations[ $location_id ];
	}
}
