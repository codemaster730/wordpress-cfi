<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Register a new CUSTOM POST TYPE + category + tag for a given instance of FAQ.
 *
 * This FAQ article will have their post_type set to this newly registered custom post type.
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 */
class EPKB_FAQ_CPT_Setup {

	public function __construct() {
		add_action( 'init', array( $this, 'register_faq_post_types' ), 10 );
	}

	/**
	 * Read configuration and create configured custom post types, each representing an FAQ
	 */
	public function register_faq_post_types() {

		$result = self::register_custom_post_type();
		if ( is_wp_error( $result ) ) {
			EPKB_Logging::add_log("Could not register custom post type.", $result);
		}

		// flush rules on plugin activation after CPTs were registered
		/* $is_flush_rewrite_rules = get_option( 'epkb_flush_rewrite_rules' );
		if ( ! empty($is_flush_rewrite_rules) && ! is_wp_error( $is_flush_rewrite_rules ) ) {
			flush_rewrite_rules( false );
		} */
	}

	/**
	 * Register custom post type, including taxonomies (category, tag) and other constructs.
	 * @return bool|WP_Error
	 */
	public static function register_custom_post_type() {

		$faq_post_type = EPKB_FAQ_Handler::get_post_type();

		/** setup FAQ Shortcodes taxonomy */

		$shortcode_taxonomy_name = EPKB_FAQ_Handler::get_faq_shortcode_taxonomy_name();
		$labels = array(
				'name'              => _x( 'FAQ Page Shortcodes', 'taxonomy general name', 'echo-knowledge-base' ),
				'singular_name'     => _x( 'FAQ Page Shortcode', 'taxonomy singular name', 'echo-knowledge-base' ),
				'search_items'      => __( 'Search FAQ Page Shortcodes', 'echo-knowledge-base' ),
				'all_items'         => __( 'All FAQ Page Shortcodes', 'echo-knowledge-base' ),
				'parent_item'       => __( 'Parent FAQ Page Shortcode', 'echo-knowledge-base' ),
				'parent_item_colon' => __( 'Parent FAQ Page Shortcode:', 'echo-knowledge-base' ),
				'edit_item'         => __( 'Edit FAQ Page Shortcode', 'echo-knowledge-base' ),
				'update_item'       => __( 'Update FAQ Page Shortcode', 'echo-knowledge-base' ),
				'add_new_item'      => __( 'Add New FAQ Page Shortcode', 'echo-knowledge-base' ),
				'new_item_name'     => __( 'New FAQ Page Shortcode Name', 'echo-knowledge-base' ),
				'menu_name'         => __( 'Page FAQ Page Shortcodes', 'echo-knowledge-base' ),
		);
		$args = array(
				'hierarchical'      => true,
				'labels'            => $labels,
				'public'            => false,
				'show_ui'           => true,
				'show_in_menu'      => false,
				'show_admin_column' => true,
				'show_in_nav_menus' => false,
				'publicly_queryable'=> false,
				'exclude_from_search'=> true,
				'has_archive'       => false,
				'query_var'         => false,
				'show_in_rest'      => false,
				'rewrite'           => false
		);
		$result = register_taxonomy( $shortcode_taxonomy_name, array( $faq_post_type ), $args );
		if ( is_wp_error( $result ) ) {
			return $result;
		}

		/** setup Help Dialog Locations taxonomy */

		$locations_taxonomy_name = EPKB_Help_Dialog_Handler::get_help_dialog_location_taxonomy_name();
		$labels = array(
			'name'              => _x( 'Help Dialog Locations', 'taxonomy general name', 'echo-knowledge-base' ),
			'singular_name'     => _x( 'Help Dialog Location', 'taxonomy singular name', 'echo-knowledge-base' ),
			'search_items'      => __( 'Search Help Dialog Locations', 'echo-knowledge-base' ),
			'all_items'         => __( 'All Help Dialog Locations', 'echo-knowledge-base' ),
			'parent_item'       => __( 'Parent Help Dialog Location', 'echo-knowledge-base' ),
			'parent_item_colon' => __( 'Parent Help Dialog Location:', 'echo-knowledge-base' ),
			'edit_item'         => __( 'Edit Help Dialog Location', 'echo-knowledge-base' ),
			'update_item'       => __( 'Update LHelp Dialog ocation', 'echo-knowledge-base' ),
			'add_new_item'      => __( 'Add New Help Dialog Location', 'echo-knowledge-base' ),
			'new_item_name'     => __( 'New Help Dialog Location Name', 'echo-knowledge-base' ),
			'menu_name'         => __( 'Help Dialog Locations', 'echo-knowledge-base' ),
		);
		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'public'            => false,
			'show_ui'           => true,
			'show_in_menu'      => false,
			'show_admin_column' => true,
			'show_in_nav_menus' => true,
			'publicly_queryable'=> false,
			'exclude_from_search'=> true,
			'has_archive'       => false,
			'query_var'         => false,
			'show_in_rest'      => false,
			'rewrite'           => false
		);
		$result = register_taxonomy( $locations_taxonomy_name, array( $faq_post_type ), $args );
		if ( is_wp_error( $result ) ) {
			return $result;
		}

		/** setup Custom Post Type */
		$post_type_name = _x( 'Frequently Asked Questions', 'post type general name', 'echo-knowledge-base' );
		$post_type_name = empty($post_type_name) ? _x( 'FAQs and Help', 'post type general name', 'echo-knowledge-base' ) : $post_type_name;
		$labels = array(
			'name'               => $post_type_name,
			'singular_name'      => $post_type_name . ' - ' . _x( 'Question', 'post type singular name', 'echo-knowledge-base' ),
			'add_new'            => _x( 'Add New Question', 'Questions', 'echo-knowledge-base' ),
			'add_new_item'       => __( 'Add New Question', 'echo-knowledge-base' ),
			'edit_item'          => __( 'Edit Question', 'echo-knowledge-base' ),
			'new_item'           => __( 'New Question', 'echo-knowledge-base' ),
			'all_items'          => __( 'All Questions', 'echo-knowledge-base' ),
			'view_item'          => __( 'View Question', 'echo-knowledge-base' ),
			'search_items'       => __( 'Search Questions', 'echo-knowledge-base' ),
			'not_found'          => __( 'No Question found', 'echo-knowledge-base' ),
			'not_found_in_trash' => __( 'No Question found in Trash', 'echo-knowledge-base' ),
			'parent_item_colon'  => '',
			'menu_name'          => _x( 'FAQ / Help Dialog', 'admin menu', 'echo-knowledge-base' )
		);
		$args = array(
			'labels'             => $labels,
			'public'             => false,
			'exclude_from_search'=> true,
			'show_ui'            => false,
			'show_in_menu'       => true,
			'publicly_queryable' => false,
			'query_var'          => false,
			'capability_type'    => 'post',
			'map_meta_cap'       => true,
			'has_archive'        => false,
			'hierarchical'       => false,
			'show_in_rest'       => false,
			'menu_position'      => 5.1,    // below Posts menu
			'menu_icon'          => 'dashicons-welcome-learn-more',
			'supports'           => array('title')
		);
		$result = register_post_type( $faq_post_type, $args );
		if ( is_wp_error( $result ) ) {
			return $result;
		}

		/** tie taxonomies to the post type */

		$result = register_taxonomy_for_object_type( $shortcode_taxonomy_name, $faq_post_type );
		if ( ! $result ) {
			return new WP_Error( 'register_object_for_tax_failed', "Failed to register taxonomy '$shortcode_taxonomy_name' for post type '$faq_post_type'" );
		}

		$result = register_taxonomy_for_object_type( $locations_taxonomy_name, $faq_post_type );
		if ( ! $result ) {
			return new WP_Error( 'register_object_for_tax_failed', "Failed to register taxonomy '$locations_taxonomy_name' for post type '$faq_post_type'" );
		}

		return true;
	}
}

