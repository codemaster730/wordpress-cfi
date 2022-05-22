<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Handle FAQ data
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 */
class EPKB_FAQ_Handler {

	// Prefix for custom post type name associated with given FAQ; this will never change
	const FAQ_POST_TYPE = 'epkb_faq';  // changing this requires db update
	const FAQ_SHORTCODE_PREFIX = 'epkb-faq-';

	const FAQ_PAGE_SHORTCODE_TAXONOMY_SUFFIX = '_page_shortcode';  // changing this requires db update; do not translate
	const FAQ_PAGE_SHORTCODE__META = 'epkb_faq_shortcode';

	/**
	 * Create a new Demo FAQ and Help Dialog FAQ
	 *
	 * @return array|WP_Error - the new FAQ configuration or WP_Error
	 */
	public static function setup_demo_faqs() {

		$default_shortcode_id = EPKB_FAQ_Config_DB::DEFAULT_FAQ_SHORTCODE_ID;

		// use default FAQ configuration for a new FAQ
		EPKB_Logging::disable_logging();

		// use default FAQ configuration ONLY if none exists
		/* $faq_config = epkb_get_instance()->faq_config_obj->get_faq_shortcode_config( $default_shortcode_id );
		if ( ! is_wp_error( $faq_config ) && is_array($faq_config) && ! empty($faq_config['id']) ) {
			return $faq_config;
		}*/

		// 1. create default FAQ configuration
		$faq_config = EPKB_FAQ_Config_Specs::get_default_faq_config( $default_shortcode_id );

		// 2. register custom post type for this FAQ
		$error = EPKB_FAQ_CPT_Setup::register_custom_post_type();
		if ( is_wp_error( $error ) ) {
			EPKB_Logging::add_log("Could not register post type when adding a new FAQ", $error);
			// ignore error and try to continue
		}

		// 3. Add demo FAQ questions
		self::create_demo_faq_questions();

		return $faq_config;
	}

	private static function create_demo_faq_questions() {

		$taxonomy = self::get_faq_shortcode_taxonomy_name();

		/*********** FIRST CATEGORY + ARTICLES **********/
		$category_name = __( 'Support', 'echo-knowledge-base' );
		$category_id = self::create_faq_category( $category_name, EPKB_FAQ_Config_DB::DEFAULT_FAQ_SHORTCODE );
		if ( empty($category_id) ) {
			return;
		}

		$article1_title = __( 'How do I Register?', 'echo-knowledge-base' );
		$article1_content = 'Registration is required to post in our forum, and it comes with great benefits and features. Click here to learn more.';
		$article1_id = self:: create_sample_article( $category_id, $article1_title, $article1_content, $taxonomy );
		if ( empty($article1_id) ) {
			return;
		}

		$article2_title = __( 'How do I Update My Personal Information?', 'echo-knowledge-base' );
		$article2_content = 'Use the following steps to update your name, email or password:
				Log into your account.
				Hover over Profile
				Update your information and click Save.';
		$article2_id = self:: create_sample_article( $category_id, $article2_title, $article2_content, $taxonomy );
		if ( empty($article2_id) ) {
			return;
		}

		$article3_title = __( 'How do I Track My Order?', 'echo-knowledge-base' );
		$article3_content = 'Log into your account and use your tracking number to track the status of your package.';
		$article3_id = self:: create_sample_article( $category_id, $article3_title, $article3_content, $taxonomy );
		if ( empty($article3_id) ) {
			return;
		}

		// save articles sequence data
		$articles_array = array(
			$category_id => array( '0' => $category_name, '1' => __( 'Category description', 'echo-knowledge-base' ),
			                        $article1_id => $article1_title, $article2_id => $article2_title, $article3_id => $article3_title),
		);

		// TODO EPKB_FAQ_Utilities::save_faq_option( $new_faq_id, EPKB_Questions_Admin::FAQ_ARTICLES_SEQ_META, $articles_array, true );
	}

	private static function create_sample_article( $faq_term_id, $post_title, $post_content, $taxonomy ) {

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
			EPKB_Logging::add_log( 'Could not insert post for new FAQ', $wp_error );
			return null;
		}

		$result = wp_set_object_terms( $post_id, $faq_term_id, $taxonomy );
		if ( is_wp_error($result) ) {
			EPKB_Logging::add_log( 'Could not insert default category for new FAQ. post id: ' . $post_id . ' term id: ' . $faq_term_id . ', FAQ ID: ', $result );
			return null;
		}

		return $post_id;
	}

	public static function create_faq_category( $category_name, $meta_value ) {

		$taxonomy = self::get_faq_shortcode_taxonomy_name();
		$category_description = __('Help Dialog Location', 'echo-knowledge-base');

		$args = array('description' => $category_description );

		// insert category
		$new_term = wp_insert_term( $category_name, $taxonomy, $args );
		if ( is_wp_error( $new_term ) ) {
			EPKB_Logging::add_log( 'Failed to insert category for new FAQ. cat name: ' . $category_name, $new_term );
			return $new_term;
		}
		if ( ! isset( $new_term['term_id'] ) ) {
			EPKB_Logging::add_log( 'Failed to insert category for new FAQ. cat name: ' . $category_name );
			return null;
		}

		$result = add_term_meta( $new_term['term_id'], self::FAQ_PAGE_SHORTCODE__META, $meta_value, true );
		if ( empty($result) || is_wp_error($result) ) {
			EPKB_Logging::add_log( 'Error adding term meta', $result );
			return $result;
		}

		return $new_term['term_id'];
	}

	/**
	 * Is this FAQ post type?
	 *
	 * @param $post_type
	 * @return bool
	 */
	public static function is_faq_post_type( $post_type ) {
		if ( empty($post_type) || ! is_string($post_type)) {
			return false;
		}
		// we are only interested in FAQ articles
		return strncmp($post_type, self::FAQ_POST_TYPE, strlen(self::FAQ_POST_TYPE)) == 0;
	}

	/**
	 * Is this FAQ Shortcode taxonomy?
	 *
	 * @param $taxonomy
	 * @return bool
	 */
	public static function is_faq_shortcode_taxonomy( $taxonomy ) {
		if ( empty($taxonomy) || ! is_string($taxonomy) ) {
			return false;
		}

		// we are only interested in FAQ articles
		return strncmp($taxonomy, self::FAQ_PAGE_SHORTCODE_TAXONOMY_SUFFIX, strlen(self::FAQ_PAGE_SHORTCODE_TAXONOMY_SUFFIX)) == 0;
	}

	/**
	 * Does request have FAQ taxonomy or post type ?
	 *
	 * @return bool
	 */
	public static function is_faq_request() {

		$faq_post_type = empty($_REQUEST['post_type']) ? '' : preg_replace('/[^A-Za-z0-9 \-_]/', '', $_REQUEST['post_type']);
		$is_faq_post_type = empty($faq_post_type) ? false : self::is_faq_post_type( $faq_post_type );
		if ( $is_faq_post_type ) {
			return true;
		}

		$faq_taxonomy = empty($_REQUEST['taxonomy']) ? '' : preg_replace('/[^A-Za-z0-9 \-_]/', '', $_REQUEST['taxonomy']);
		$is_faq_taxonomy = empty($faq_taxonomy) ? false : self::is_faq_shortcode_taxonomy( $faq_taxonomy ) || EPKB_Help_Dialog_Handler::is_help_dialog_location_taxonomy( $faq_taxonomy );

		return $is_faq_taxonomy;
	}

	/**
	 * Retrieve FAQ post type name e.g. ep epkb_faq_1
	 * @return string
	 */
	public static function get_post_type() {
		return self::FAQ_POST_TYPE;
	}

	/**
	 * Return category name
	 * @return string
	 */
	public static function get_faq_shortcode_taxonomy_name() {
		return self::get_post_type() . self::FAQ_PAGE_SHORTCODE_TAXONOMY_SUFFIX;
	}
}
