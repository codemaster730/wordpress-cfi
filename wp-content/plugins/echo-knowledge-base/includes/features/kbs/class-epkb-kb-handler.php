<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Handle operations on knowledge base such as adding, deleting and updating KB
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 */
class EPKB_KB_Handler {

	// name of KB shortcode
	const KB_MAIN_PAGE_SHORTCODE_NAME = 'epkb-knowledge-base'; // changing this requires db update
	const KB_BLOCK_NAME = 'echo-document-blocks/knowledge-base';

	// Prefix for custom post type name associated with given KB; this will never change
	const KB_POST_TYPE_PREFIX = 'epkb_post_type_';  // changing this requires db update
	const KB_CATEGORY_TAXONOMY_SUFFIX = '_category';  // changing this requires db update; do not translate
	const KB_TAG_TAXONOMY_SUFFIX = '_tag'; // changing this requires db update; do not translate

	/**
	 * Create a new Knowledge Base using default configuration when:
	 *  a) plugin is installed and activated
	 *  b) user clicks on 'Add Knowledge Base' button (requires Multiple KBs add-on)
	 * First default knowledge base has name 'Knowledge Base' with ID 1
	 * Add New KB will create KB with pre-set name 'Knowledge Base 2' with ID 2 and so on.
	 *
	 * @param int $new_kb_id - ID of the new KB
	 * @param string $new_kb_main_page_title
	 * @param string $new_kb_main_page_slug
	 * @param $kb_main_page_layout
	 * @return array|WP_Error - the new KB configuration or WP_Error
	 */
	public static function add_new_knowledge_base( $new_kb_id, $new_kb_main_page_title='', $new_kb_main_page_slug='', $kb_main_page_layout='' ) {

		// use default KB configuration for a new KB
		EPKB_Logging::disable_logging();

		// use default KB configuration ONLY if none exists
		$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( $new_kb_id );

		// 1. Add first KB Main page if none exists; first KB is just called Knowledge Base
		$kb_main_pages = $kb_config['kb_main_pages'];
		if ( empty( $kb_main_pages ) ) {

			$post = self::create_kb_main_page( $new_kb_id, $new_kb_main_page_title, $new_kb_main_page_slug );
			if ( is_wp_error( $post ) ) {
				return $post;
			}

			$kb_config['kb_name'] = $post->post_title;
			$kb_main_pages[ $post->ID ] = $post->post_title;
			$kb_config['kb_main_pages'] = $kb_main_pages;
			$kb_config['kb_articles_common_path'] = urldecode( sanitize_title_with_dashes( $post->post_name, '', 'save' ) );
			$kb_config['categories_display_sequence'] = 'user-sequenced';
			$kb_config['article_sidebar_component_priority']['nav_sidebar_left'] = $kb_config['article_sidebar_component_priority']['nav_sidebar_left'] == '' ? '1' : $kb_config['article_sidebar_component_priority']['nav_sidebar_left'];  // FUTURE TODO remove
		}

		// 2. create default empty sequence if none exists ( setup wizard can be launched multiple times, no need to overwrite existing)
		$default_categories_data = EPKB_Utilities::get_kb_option( $new_kb_id, EPKB_Categories_Admin::KB_CATEGORIES_SEQ_META, null, true );
		if ( $default_categories_data === null ) {
			EPKB_Utilities::save_kb_option( $new_kb_id, EPKB_Categories_Admin::KB_CATEGORIES_SEQ_META, [], true );
		}

		// create default empty sequence if none exists ( setup wizard can be launched multiple times, no need to overwrite existing)
		$default_articles_data = EPKB_Utilities::get_kb_option( $new_kb_id, EPKB_Articles_Admin::KB_ARTICLES_SEQ_META, null, true );
		if ( $default_articles_data === null ) {
			EPKB_Utilities::save_kb_option( $new_kb_id, EPKB_Articles_Admin::KB_ARTICLES_SEQ_META, [], true );
		}

		// 3. Add a sample category and sub-category with article each if no category exists
		$all_kb_terms = EPKB_Core_Utilities::get_kb_categories_unfiltered( $new_kb_id );
		if ( empty( $all_kb_terms ) ) {
			self::create_sample_categories_and_articles( $new_kb_id, $kb_main_page_layout );
		}

		// 4. save new/updated KB configuration
		$result = epkb_get_instance()->kb_config_obj->update_kb_configuration( $new_kb_id, $kb_config );
		if ( is_wp_error( $result ) ) {
			return $result;
		}

		// let add-ons know we have a new KB; default KB is created when each add-on is activated
		if ( $new_kb_id !== EPKB_KB_Config_DB::DEFAULT_KB_ID ) {
			do_action( 'eckb_new_knowledge_base_added', $new_kb_id );
		}

		// mark the current KB to indicate that Setup Wizard was not completed for it
		EPKB_Utilities::save_wp_option( 'epkb_not_completed_setup_wizard_' . $new_kb_id, true, true );

		return $kb_config;
	}

	/**
	 * Create a new KB main page
	 *
	 * @param $kb_id
	 * @param string $kb_main_page_title
	 * @param string $kb_main_page_slug
	 *
	 * @return WP_Error|WP_Post
	 */
	public static function create_kb_main_page( $kb_id, $kb_main_page_title, $kb_main_page_slug ) {

		// we add new KB Page here so remove hook
		remove_filter('save_post', 'epkb_add_main_page_if_required', 10 );

		// do not process KB shortcode during KB creation
		remove_shortcode( EPKB_KB_Handler::KB_MAIN_PAGE_SHORTCODE_NAME );

		$kb_main_page_title = empty( $kb_main_page_title ) ? __( 'Knowledge Base', 'echo-knowledge-base' ) : $kb_main_page_title;
		$my_post = array(
			'post_title'    => $kb_main_page_title,
			'post_name'     => $kb_main_page_slug,
			'post_type'     => 'page',
			'post_content'  => '[' . self::KB_MAIN_PAGE_SHORTCODE_NAME . ' id=' . $kb_id . ']',
			'post_status'   => 'publish',
			'comment_status' => 'closed'
			// current user or 'post_author'   => 1,
		);
		$post_id = wp_insert_post( $my_post );
		if ( empty( $post_id ) || is_wp_error( $post_id ) ) {
			return is_wp_error( $post_id ) ? $post_id : new WP_Error( 'E01', 'Could not insert KB Main Page with slug ' . $kb_main_page_slug );
		}

		$post = WP_Post::get_instance( $post_id );
		if ( empty( $post ) ) {
			return new WP_Error( 'E02', 'Could not find post with id ' . $post_id );
		}

		return $post;
	}

	public static function create_sample_categories_and_articles( $new_kb_id, $kb_main_page_layout ) {

		$add_ons_text = "<h2>". __( 'Access Manager', 'echo-knowledge-base' ) . "</h2>
			<p>". __( 'Access Manager allows administrators, companies, and organizations to control and restrict access to their private Knowledge Base based on WordPress user accounts. Grant permission using roles and groups.', 'echo-knowledge-base' ) . " 
			<a href='https://www.echoknowledgebase.com/wordpress-plugin/access-manager/?utm_source=plugin&utm_medium=addons&utm_content=home&utm_campaign=access-manager' target='_blank'>". __( 'Learn More', 'echo-knowledge-base' ) . "</a></p>

			<h2>". __( 'Elegant Layouts', 'echo-knowledge-base' ) . "</h2>
			<p>". __( 'Elegant Layouts adds Grid and Sidebar Layouts. Use Grid Layout or Sidebar Layout for KB Main page or combine Basic, Tabs, Grid and Sidebar layouts in a variety ways. ', 'echo-knowledge-base' ) . "
			<a href='https://www.echoknowledgebase.com/wordpress-plugin/elegant-layouts/?utm_source=plugin&utm_medium=addons&utm_content=home&utm_campaign=elegant-layouts' target='_blank'>". __( 'Learn More', 'echo-knowledge-base' ) . "</a></p>
			
			<h2>". __( 'Multiple Knowledge Bases', 'echo-knowledge-base' ) . "</h2>
			<p>". __( 'Create Multiple Knowledge Bases, one for each product, service, topic or department. Each Knowledgebase has separate articles, URLs, KB Main Page and admin screens. ', 'echo-knowledge-base' ) . "
			<a href='https://www.echoknowledgebase.com/wordpress-plugin/multiple-knowledge-bases/?utm_source=plugin&utm_medium=addons&utm_content=home&utm_campaign=multiple-kbs' target='_blank'>". __( 'Learn More', 'echo-knowledge-base' ) . "</a></p>
			
			<h2>". __( 'Advanced Search', 'echo-knowledge-base' ) . "</h2>
			<p>". __( "Enhance users' search experience and view search analytics, including popular searches and no results searches.", 'echo-knowledge-base' ) . "
			<a href='https://www.echoknowledgebase.com/wordpress-plugin/advanced-search/?utm_source=plugin&utm_medium=addons&utm_content=home&utm_campaign=advanced-search' target='_blank'>". __( 'Learn More', 'echo-knowledge-base' ) . "</a></p>
			
			<h2>". __( 'Article Rating and Feedback', 'echo-knowledge-base' ) . "</h2>
			<p>". __( 'Let your readers rate the quality of your articles and submit insightful feedback. Utilize analytics on the most and least rated articles. ', 'echo-knowledge-base' ) . "
			<a href='https://www.echoknowledgebase.com/wordpress-plugin/article-rating-and-feedback/?utm_source=plugin&utm_medium=addons&utm_content=home&utm_campaign=article-rating' target='_blank'>". __( 'Learn More', 'echo-knowledge-base' ) . "</a></p>
			
			<h2>". __( 'Widgets', 'echo-knowledge-base' ) . "</h2>
			<p>". __( 'Add Knowledgebase Search, Most Recent Articles and other Widgets and Shortcodes to your articles, sidebars and pages. ', 'echo-knowledge-base' ) . "
			<a href='https://www.echoknowledgebase.com/wordpress-plugin/widgets/?utm_source=plugin&utm_medium=addons&utm_content=home&utm_campaign=widgets' target='_blank'>". __( 'Learn More', 'echo-knowledge-base' ) . "</a></p>
			
			<h2>". __( 'Links Editor for PDFs and More', 'echo-knowledge-base' ) . "</h2>
			<p>". __( 'Set Articles to links to PDFs, pages, posts and websites. On KB Main Page, choose icons for your articles. ', 'echo-knowledge-base' ) . "
			<a href='https://www.echoknowledgebase.com/wordpress-plugin/links-editor-for-pdfs-and-more/?utm_source=plugin&utm_medium=addons&utm_content=home&utm_campaign=links-editor' target='_blank'>". __( 'Learn More', 'echo-knowledge-base' ) . "</a></p>";

		$demo_category_parent_id = null;
		$category_id_product_a = 0;
		$category_name_product_a = '';
		$category_id_product_b = 0;
		$category_name_product_b = '';
		$articlex_id = 0;
		$articlex_title = '';
		$sub_category_id_product_b = 0;
		$sub_category_name_product_b = '';

		/*********** TABS TOP CATEGORIES **********/
		if ( $kb_main_page_layout == 'Tabs' ) {
			$category_name_product_a = __( 'Product A', 'echo-knowledge-base' );
			$category_id_product_a   = self::create_sample_category( $new_kb_id, $category_name_product_a );
			if ( empty( $category_id_product_a ) ) {
				return;
			}
			$demo_category_parent_id = $category_id_product_a;

			$category_name_product_b = __( 'Product B', 'echo-knowledge-base' );
			$category_id_product_b   = self::create_sample_category( $new_kb_id, $category_name_product_b );
			if ( empty( $category_id_product_b ) ) {
				return;
			}

			$sub_category_name_product_b = __( 'Topic B', 'echo-knowledge-base' );
			$sub_category_id_product_b   = self::create_sample_category( $new_kb_id, $sub_category_name_product_b, $category_id_product_b );
			if ( empty( $sub_category_id_product_b ) ) {
				return;
			}

			$articlex_title = __( 'Article B', 'echo-knowledge-base' );
			$articlex_id = self:: create_sample_article( $new_kb_id, $sub_category_id_product_b, $articlex_title );
			if ( empty($articlex_id) ) {
				return;
			}
		}

		/*********** FIRST CATEGORY + ARTICLES **********/
		$category_name_introduction = __( 'Getting Started', 'echo-knowledge-base' );
		$category_id_introduction = self::create_sample_category( $new_kb_id, $category_name_introduction, $demo_category_parent_id );
		if ( empty($category_id_introduction) ) {
			return;
		}

		// create sub-category
		$category_name_apendix = __( 'Overview', 'echo-knowledge-base' );
		$category_id_apendix = self::create_sample_category( $new_kb_id, $category_name_apendix, $category_id_introduction );
		if ( empty($category_id_apendix) ) {
			return;
		}

		$article1_title = __( 'Quick Start Guide', 'echo-knowledge-base' );
		$article1_id = self:: create_sample_article( $new_kb_id, $category_id_introduction, $article1_title );
		if ( empty($article1_id) ) {
			return;
		}
		$article2_title = __( 'Your Next Steps', 'echo-knowledge-base' );
		$article2_id = self:: create_sample_article( $new_kb_id, $category_id_introduction, $article2_title );
		if ( empty($article2_id) ) {
			return;
		}
		$article3_title = __( 'Additional Information', 'echo-knowledge-base' );
		$article3_id = self:: create_sample_article( $new_kb_id, $category_id_introduction, $article3_title );
		if ( empty($article3_id) ) {
			return;
		}

		// create article for sub-category
		$article4_title = __( 'Category Hierarchy and Tabs Layout', 'echo-knowledge-base' );
		$article4_id = self:: create_sample_article( $new_kb_id, $category_id_apendix, $article4_title , __( 'Tabs Layout uses top categories for its tabs, and therefore, it cannot contain articles. Add your articles to sub-categories.', 'echo-knowledge-base' ) );
		if ( empty($article4_id) ) {
			return;
		}

		/*********** SECOND CATEGORY ************/
		$category_name_faqs = __( 'FAQs', 'echo-knowledge-base' );
		$category_id_faqs = self::create_sample_category( $new_kb_id, $category_name_faqs, $demo_category_parent_id );
		if ( empty($category_id_faqs) ) {
			return;
		}

		// create article for third category
		$article5_title = __( 'About KB Add-ons', 'echo-knowledge-base' );
		$article5_id = self:: create_sample_article( $new_kb_id, $category_id_faqs, $article5_title , $add_ons_text);
		if ( empty($article5_id) ) {
			return;
		}

		/************ THIRD CATEGORY ***********/
		$category_name_other = __( 'Other', 'echo-knowledge-base' );
		$category_id_other = self::create_sample_category( $new_kb_id, $category_name_other, $demo_category_parent_id );
		if ( empty($category_id_other) ) {
			return;
		}

		$article_other_title = __( 'Additional Resources', 'echo-knowledge-base' );
		$article_other_id = self:: create_sample_article( $new_kb_id, $category_id_other, $article_other_title );
		if ( empty($article_other_id) ) {
			return;
		}

		// save articles sequence data
		$articles_array = array(
			$category_id_faqs => array( '0' => $category_name_faqs, '1' => __('Category description', 'echo-knowledge-base'), $article5_id => $article5_title ),
			$category_id_apendix => array( '0' => $category_name_apendix, '1' => __('Category description', 'echo-knowledge-base'), $article4_id => $article4_title),
			$category_id_introduction => array( '0' => $category_name_introduction, '1' => __('Category description', 'echo-knowledge-base'),
			                                    $article1_id => $article1_title, $article2_id => $article2_title, $article3_id => $article3_title),
			$category_id_other => array( '0' => $category_name_other, '1' => __('Category description', 'echo-knowledge-base'), $article_other_id => $article_other_title ),
		);

		if ( $kb_main_page_layout == 'Tabs' ) {

			// sequence of this array has to correspond to sequence of categories
			$articles_array = array(
				$category_id_product_a => array( '0' => $category_name_product_a, '1' => __('Category description', 'echo-knowledge-base') ),
				$category_id_faqs => array( '0' => $category_name_faqs, '1' => __('Category description', 'echo-knowledge-base'), $article5_id => $article5_title ),
				$category_id_apendix => array( '0' => $category_name_apendix, '1' => __('Category description', 'echo-knowledge-base'), $article4_id => $article4_title),
				$category_id_introduction => array( '0' => $category_name_introduction, '1' => __('Category description', 'echo-knowledge-base'),
				                                    $article1_id => $article1_title, $article2_id => $article2_title, $article3_id => $article3_title),
				$category_id_other => array( '0' => $category_name_other, '1' => __('Category description', 'echo-knowledge-base'), $article_other_id => $article_other_title ),
				$category_id_product_b => array( '0' => $category_name_product_b, '1' => __('Category description', 'echo-knowledge-base') ),
				$sub_category_id_product_b => array( '0' => $sub_category_name_product_b, '1' => __('Category description', 'echo-knowledge-base'), $articlex_id => $articlex_title ),
			);
		}

		EPKB_Utilities::save_kb_option( $new_kb_id, EPKB_Articles_Admin::KB_ARTICLES_SEQ_META, $articles_array, true );

		// save category sequence data
		if ( $kb_main_page_layout == 'Tabs' ) {
			$cat_seq_meta = array( $category_id_product_a => array( $category_id_introduction => array( $category_id_apendix => array() ), $category_id_faqs => array(), $category_id_other => array() ),
			                       $category_id_product_b => array( $sub_category_id_product_b => array() ) );
		} else {
			$cat_seq_meta = array( $category_id_introduction => array( $category_id_apendix => array() ), $category_id_faqs => array(), $category_id_other => array() );
		}

		EPKB_Utilities::save_kb_option( $new_kb_id, EPKB_Categories_Admin::KB_CATEGORIES_SEQ_META, $cat_seq_meta, true );

		// save new icons
		$new_categories_icons = array();
		$new_categories_icons[$category_id_faqs] = array(
			'type' => 'font',
			'name' => 'epkbfa-book',
			'image_id' => EPKB_Icons::DEFAULT_CATEGORY_IMAGE_ID,
			'image_size' => EPKB_Icons::DEFAULT_CATEGORY_IMAGE_SIZE,
			'image_thumbnail_url' => Echo_Knowledge_Base::$plugin_url . EPKB_Icons::DEFAULT_IMAGE_SLUG,
			'color' => '#000000'    // FUTURE
		);
		$new_categories_icons[$category_id_introduction] = array(
			'type' => 'font',
			'name' => 'ep_font_icon_gears',
			'image_id' => EPKB_Icons::DEFAULT_CATEGORY_IMAGE_ID,
			'image_size' => EPKB_Icons::DEFAULT_CATEGORY_IMAGE_SIZE,
			'image_thumbnail_url' => Echo_Knowledge_Base::$plugin_url . EPKB_Icons::DEFAULT_IMAGE_SLUG,
			'color' => '#000000'    // FUTURE
		);
		$new_categories_icons[$category_id_other] = array(
			'type' => 'font',
			'name' => 'epkbfa-cube',
			'image_id' => EPKB_Icons::DEFAULT_CATEGORY_IMAGE_ID,
			'image_size' => EPKB_Icons::DEFAULT_CATEGORY_IMAGE_SIZE,
			'image_thumbnail_url' => Echo_Knowledge_Base::$plugin_url . EPKB_Icons::DEFAULT_IMAGE_SLUG,
			'color' => '#000000'    // FUTURE
		);
		$result = EPKB_Utilities::save_kb_option( $new_kb_id, EPKB_Icons::CATEGORIES_ICONS, $new_categories_icons, true );
		if ( is_wp_error( $result ) ) {
			return;
		}
	}

	private static function create_sample_category( $new_kb_id, $category_name, $parent_id=null ) {

		$args = empty($parent_id) ? array('description' => __('Category description', 'echo-knowledge-base') )
								  : array( 'parent' => $parent_id, 'description' => __('Category description', 'echo-knowledge-base') );

		// insert category
		$term_id_array = wp_insert_term( $category_name, self::get_category_taxonomy_name( $new_kb_id ), $args );
		if ( is_wp_error( $term_id_array ) || ! isset( $term_id_array['term_id'] ) ) {
			return null;
		}

		return $term_id_array['term_id'];
	}

	public static function get_sample_post_content() {
		return '
			<h2>' . __( 'This is a H2 heading', 'echo-knowledge-base' ) . '</h2>
			<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Id eu nisl nunc mi. Sed nisi lacus sed viverra tellus in hac habitasse platea. Quam elementum pulvinar etiam non quam lacus suspendisse faucibus. Eleifend donec pretium vulputate sapien nec. Neque aliquam vestibulum morbi blandit cursus risus. Ultrices dui sapien eget mi proin sed. Massa massa ultricies mi quis hendrerit dolor. Ullamcorper malesuada proin libero nunc consequat interdum varius sit. Risus feugiat in ante metus dictum at tempor. Massa sapien faucibus et molestie ac feugiat sed lectus vestibulum. Risus nullam eget felis eget nunc lobortis. Malesuada nunc vel risus commodo viverra. Amet commodo nulla facilisi nullam. Vel risus commodo viverra maecenas accumsan lacus vel facilisis volutpat. Urna condimentum mattis pellentesque id nibh. Aliquam purus sit amet luctus. Vestibulum lorem sed risus ultricies.</p>
			<p><em>' . __( 'This is an un-ordered list', 'echo-knowledge-base' ) . '</em></p>
			<ul>
				<li>Lorem ipsum dolor sit amet, consectetuer adipiscing elit.</li>
				<li>Aliquam tincidunt mauris eu risus.</li>
				<li>Vestibulum auctor dapibus neque.</li>
			</ul>
			<p>Sit amet luctus venenatis lectus magna fringilla urna. Arcu cursus euismod quis viverra. Dignissim diam quis enim lobortis scelerisque fermentum dui faucibus. Integer vitae justo eget magna fermentum iaculis eu non diam. Sit amet consectetur adipiscing elit ut aliquam purus sit amet. Quisque sagittis purus sit amet volutpat consequat mauris. Nunc faucibus a pellentesque sit. Eu non diam phasellus vestibulum lorem sed risus ultricies. Lobortis scelerisque fermentum dui faucibus in ornare quam. Libero justo laoreet sit amet cursus sit amet dictum. Sit amet mattis vulputate enim. Sit amet nisl suscipit adipiscing bibendum est ultricies. Euismod lacinia at quis risus sed vulputate odio ut enim. At tellus at urna condimentum mattis pellentesque id nibh. Sit amet nulla facilisi morbi tempus. Commodo quis imperdiet massa tincidunt nunc pulvinar sapien et ligula. Senectus et netus et malesuada fames. Orci porta non pulvinar neque laoreet.</p>
			<p>Risus quis varius quam quisque. Egestas dui id ornare arcu odio ut sem nulla pharetra. Porta lorem mollis aliquam ut porttitor. Quam nulla porttitor massa id neque aliquam vestibulum morbi blandit. Egestas purus viverra accumsan in nisl. Fermentum odio eu feugiat pretium nibh ipsum consequat nisl. Integer vitae justo eget magna fermentum iaculis eu. Accumsan in nisl nisi scelerisque. Id venenatis a condimentum vitae. Sed sed risus pretium quam vulputate dignissim suspendisse. Pellentesque diam volutpat commodo sed egestas egestas.</p>
			<h3>' . __( 'This is a H3 heading', 'echo-knowledge-base' ) . '</h3>
			<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Id eu nisl nunc mi. Sed nisi lacus sed viverra tellus in hac habitasse platea. Quam elementum pulvinar etiam non quam lacus suspendisse faucibus. Eleifend donec pretium vulputate sapien nec. Neque aliquam vestibulum morbi blandit cursus risus. Ultrices dui sapien eget mi proin sed. Massa massa ultricies mi quis hendrerit dolor. Ullamcorper malesuada proin libero nunc consequat interdum varius sit. Risus feugiat in ante metus dictum at tempor. Massa sapien faucibus et molestie ac feugiat sed lectus vestibulum. Risus nullam eget felis eget nunc lobortis. Malesuada nunc vel risus commodo viverra. Amet commodo nulla facilisi nullam. Vel risus commodo viverra maecenas accumsan lacus vel facilisis volutpat. Urna condimentum mattis pellentesque id nibh. Aliquam purus sit amet luctus. Vestibulum lorem sed risus ultricies.</p>
			<h4>' . __( 'This is a H4 heading', 'echo-knowledge-base' ) . '</h4>
			<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Id eu nisl nunc mi. Sed nisi lacus sed viverra tellus in hac habitasse platea. Quam elementum pulvinar etiam non quam lacus suspendisse faucibus. Eleifend donec pretium vulputate sapien nec. Neque aliquam vestibulum morbi blandit cursus risus. Ultrices dui sapien eget mi proin sed. Massa massa ultricies mi quis hendrerit dolor. Ullamcorper malesuada proin libero nunc consequat interdum varius sit. Risus feugiat in ante metus dictum at tempor. Massa sapien faucibus et molestie ac feugiat sed lectus vestibulum. Risus nullam eget felis eget nunc lobortis. Malesuada nunc vel risus commodo viverra. Amet commodo nulla facilisi nullam. Vel risus commodo viverra maecenas accumsan lacus vel facilisis volutpat. Urna condimentum mattis pellentesque id nibh. Aliquam purus sit amet luctus. Vestibulum lorem sed risus ultricies.</p>
			<p><strong>' . __( 'This is an ordered list', 'echo-knowledge-base' ) . '</strong></p>
			<ol>
				<li>Lorem ipsum dolor sit amet, consectetuer adipiscing elit.</li>
				<li>Aliquam tincidunt mauris eu risus.</li>
				<li>Vestibulum auctor dapibus neque.</li>
			</ol>';
	}

	private static function create_sample_article( $new_kb_id, $kb_term_id, $post_title, $post_content='' ) {

		$post_content = ! empty($post_content) ? $post_content : self::get_sample_post_content();

		$post_excerpt = __( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Id eu nisl nunc mi. Sed nisi lacus sed viverra tellus in hac habitasse platea. Quam elementum pulvinar etiam non quam lacus suspendisse faucibus. Eleifend donec pretium vulputate sapien nec.', 'echo-knowledge-base' );

		$my_post = array(
			'post_title'    => $post_title,
			'post_type'     => self::get_post_type( $new_kb_id ),
			'post_content'  => $post_content,
			'post_excerpt'  => $post_excerpt,
			'post_status'   => 'publish',
			// current user or 'post_author'   => 1,
		);

		// create article under category
		$post_id = wp_insert_post( $my_post );
		if ( is_wp_error( $post_id ) || empty( $post_id ) ) {
			return null;
		}

		$result = wp_set_object_terms( $post_id, $kb_term_id, self::get_category_taxonomy_name( $new_kb_id ) );
		if ( is_wp_error($result) ) {
			return null;
		}

		return $post_id;
	}

	/**
	 * Get KB slug based on default KB name and ID. Default KB has slug without ID.
	 *
	 * @param $kb_id
	 *
	 * @return string
	 */
	public static function get_default_slug( $kb_id ) {
		/* translators: do NOT change this translation again. It will break links !!! */
		return sanitize_title_with_dashes( _x( 'Knowledge Base', 'slug', 'echo-knowledge-base' ) . ( $kb_id == EPKB_KB_Config_DB::DEFAULT_KB_ID ? '' : '-' . $kb_id ) );
	}

	/**
	 * Retrieve current KB ID based on post_type value in URL based on user request etc.
	 *
	 * @return String empty if not found
	 */
	public static function get_current_kb_id() {
		global $eckb_kb_id;

		if ( ! empty( $eckb_kb_id ) ) {
			return $eckb_kb_id;
		}

		// 1. retrieve current post being used and if user selected a tab for specific KB
		$kb_id = self::find_current_kb_id();
		if ( empty( $kb_id ) || ( $kb_id instanceof WP_Error ) || ! EPKB_Utilities::is_positive_int( $kb_id ) ) {
			return '';
		}

		// 2. check if the "current id" belongs to one of the existing KBs
		if ( $kb_id != EPKB_KB_Config_DB::DEFAULT_KB_ID ) {
			$db_kb_config = new EPKB_KB_Config_DB();
			$kb_ids = $db_kb_config->get_kb_ids();
			if ( ! in_array( $kb_id, $kb_ids ) ) {
				return '';
			}
		}

		$eckb_kb_id = $kb_id;

		return $kb_id;
	}

	/**
	 * Find current KB ID
	 *
	 * @return string | int | WP_Error
	 */
	private static function find_current_kb_id() {
		global $current_screen;

		// try to find KB ID from post_type
		$kb_post_type = empty( $_REQUEST['post_type'] ) ? '' : preg_replace( '/[^A-Za-z0-9 \-_]/', '', EPKB_Utilities::request_key( 'post_type' ) );
		if ( ! empty($kb_post_type) && strpos( $kb_post_type, self::KB_POST_TYPE_PREFIX ) !== false ) {
			return self::get_kb_id_from_post_type( $kb_post_type );
		}

		// try to find KB ID from taxonomy
		$epkb_taxonomy = empty( $_REQUEST['taxonomy'] ) ? '' : preg_replace( '/[^A-Za-z0-9 \-_]/', '', EPKB_Utilities::request_key( 'taxonomy' ) );
		if ( ! empty($epkb_taxonomy) && strpos( $epkb_taxonomy, self::KB_POST_TYPE_PREFIX ) !== false ) {

			// if is KB category
			if ( strpos( $epkb_taxonomy, self::KB_CATEGORY_TAXONOMY_SUFFIX ) !== false ) {
				return self::get_kb_id_from_category_taxonomy_name( $epkb_taxonomy );
			}

			// if is KB tag
			if ( strpos( $epkb_taxonomy, self::KB_TAG_TAXONOMY_SUFFIX ) !== false ) {
				return self::get_kb_id_from_tag_taxonomy_name( $epkb_taxonomy );
			}
		}

		// try to find KB ID from post_type in the current screen
		if ( ! empty($current_screen->post_type) && strpos( $current_screen->post_type, self::KB_POST_TYPE_PREFIX ) !== false ) {
			return self::get_kb_id_from_post_type( $current_screen->post_type );
		}

		// try to find KB ID from action - e.g. when adding category within KB article
		$epkb_action = empty( $_REQUEST['action'] ) ? '' : preg_replace( '/[^A-Za-z0-9 \-_]/', '', EPKB_Utilities::request_key( 'action' ) );
		if ( ! empty( $epkb_action ) && strpos( $epkb_action, self::KB_POST_TYPE_PREFIX ) !== false ) {
			$found_taxonomy_name = str_replace( 'add-', '', $epkb_action );
			$found_kb_id = EPKB_KB_Handler::get_kb_id_from_category_taxonomy_name( $found_taxonomy_name );
			if ( ! is_wp_error( $found_kb_id ) ) {
				return $found_kb_id;
			}
		}

		// try to find KB ID from epkb_kb_id
		$epkb_kb_id = empty( $_REQUEST['epkb_kb_id'] ) ? '' : preg_replace( '/\D/', '', EPKB_Utilities::request_key( 'epkb_kb_id' ) );
		if ( ! empty( $epkb_kb_id ) && EPKB_Utilities::is_positive_int( $epkb_kb_id )) {
			return $epkb_kb_id;
		}

		// try to find KB ID from Setup Wizard call (on apply changes)
		$epkb_wizard_kb_id = empty( $_REQUEST['epkb_wizard_kb_id'] ) ? '' : preg_replace( '/\D/', '', EPKB_Utilities::request_key( 'epkb_wizard_kb_id' ) );
		if ( ! empty( $epkb_wizard_kb_id ) && EPKB_Utilities::is_positive_int( $epkb_wizard_kb_id ) ) {
			return $epkb_wizard_kb_id;
		}

		// try to find KB ID from the Editor AJAX call (on apply changes)
		$epkb_editor_kb_id = empty( $_REQUEST['epkb_editor_kb_id'] ) ? '' : preg_replace( '/\D/', '', EPKB_Utilities::request_key( 'epkb_editor_kb_id' ) );
		if ( ! empty( $epkb_editor_kb_id ) && EPKB_Utilities::is_positive_int( $epkb_editor_kb_id ) ) {
			return $epkb_editor_kb_id;
		}

		// try to find KB ID from post - when editing article
		$epkb_post_id = empty( $_REQUEST['post'] ) ? '' : preg_replace( '/\D/', '', EPKB_Utilities::request_key( 'post' ) );
		if ( ! empty( $epkb_action ) && $epkb_action == 'edit' && ! empty( $epkb_post_id ) && EPKB_Utilities::is_positive_int( $epkb_post_id ) ) {
			$post = EPKB_Core_Utilities::get_kb_post_secure( $epkb_post_id );
			if ( ! empty( $post ) ) {
				return self::get_kb_id_from_post_type( $post->post_type );
			}
		}

		// REST API
		if ( ! empty( $_SERVER['REQUEST_URI'] ) && strpos($_SERVER['REQUEST_URI'], '/wp-json/wp/') !== false && strpos( $_SERVER['REQUEST_URI'], '/' . self::KB_POST_TYPE_PREFIX ) !== false ) {
			return self::get_kb_id_from_rest_endpoint( $_SERVER['REQUEST_URI'] );
		}

		return '';
	}

	/**
	 * Is this KB post type?
	 *
	 * @param $post_type
	 * @return bool
	 */
	public static function is_kb_post_type( $post_type ) {
		if ( empty($post_type) || ! is_string($post_type)) {
			return false;
		}
		// we are only interested in KB articles
		return strncmp($post_type, self::KB_POST_TYPE_PREFIX, strlen(self::KB_POST_TYPE_PREFIX)) == 0;
	}

	/**
	 * Is this KB taxonomy?
	 *
	 * @param $taxonomy
	 * @return bool
	 */
	public static function is_kb_taxonomy( $taxonomy ) {
		if ( empty($taxonomy) || ! is_string($taxonomy) ) {
			return false;
		}

		// we are only interested in KB articles
		return strncmp($taxonomy, self::KB_POST_TYPE_PREFIX, strlen(self::KB_POST_TYPE_PREFIX)) == 0;
	}

	/**
	 * Is this KB Category taxonomy?
	 *
	 * @param $taxonomy
	 * @return bool
	 */
	public static function is_kb_category_taxonomy( $taxonomy ) {
		if ( empty( $taxonomy ) || ! is_string( $taxonomy ) ) {
			return false;
		}

		// we are only interested in KB articles
		return strncmp( $taxonomy, self::KB_POST_TYPE_PREFIX, strlen( self::KB_POST_TYPE_PREFIX ) ) == 0 && strpos( $taxonomy, self::KB_CATEGORY_TAXONOMY_SUFFIX ) !== false;
	}

	/**
	 * Does request have KB taxonomy or post type ?
	 *
	 * @return bool
	 */
	public static function is_kb_request() {

		$kb_post_type = empty( $_REQUEST['post_type'] ) ? '' : preg_replace( '/[^A-Za-z0-9 \-_]/', '', EPKB_Utilities::request_key( 'post_type' ) );
		$is_kb_post_type = empty($kb_post_type) ? false : self::is_kb_post_type( $kb_post_type );
		if ( $is_kb_post_type ) {
			return true;
		}

		$kb_taxonomy = empty( $_REQUEST['taxonomy'] ) ? '' : preg_replace( '/[^A-Za-z0-9 \-_]/', '', EPKB_Utilities::request_key( 'taxonomy' ) );
		$is_kb_taxonomy = empty($kb_taxonomy) ? false : self::is_kb_taxonomy( $kb_taxonomy );

		return $is_kb_taxonomy;
	}

	/**
	 * Retrieve current KB post type based on post_type value in URL based on user request etc.
	 *
	 * @return String empty if valid post type not found
	 */
	public static function get_current_kb_post_type() {
		$kb_id = self::get_current_kb_id();
		if ( empty( $kb_id ) ) {
			return '';
		}
		return self::get_post_type( $kb_id );
	}

	/**
	 * Retrieve KB post type name e.g. ep kb_post_type_1
	 *
	 * @param $kb_id - assumed valid id
	 *
	 * @return string
	 */
	public static function get_post_type( $kb_id ) {
		$kb_id = EPKB_Utilities::sanitize_int($kb_id, EPKB_KB_Config_DB::DEFAULT_KB_ID );
		return self::KB_POST_TYPE_PREFIX . $kb_id;
	}

	/**
	 * Retrieve KB post type name e.g. <post type>_1
	 *
	 * @return string empty when kb id cannot be determined
	 */
	public static function get_post_type2() {
		$kb_id = self::get_current_kb_id();
		if ( empty( $kb_id ) ) {
			return '';
		}
		return self::KB_POST_TYPE_PREFIX . $kb_id;
	}

	/**
	 * Return category name e.g. ep kb_post_type_1_category
	 *
	 * @param $kb_id - assumed valid id
	 *
	 * @return string
	 */
	public static function get_category_taxonomy_name( $kb_id ) {
		return self::get_post_type( $kb_id ) . self::KB_CATEGORY_TAXONOMY_SUFFIX;
	}

	/**
	 * Return category name e.g. <post type>_1_category
	 *
	 * @return string empty when kb id cannot be determined
	 */
	public static function get_category_taxonomy_name2() {
		$kb_id = self::get_current_kb_id();
		if ( empty( $kb_id ) ) {
			return '';
		}
		return self::get_post_type( $kb_id ) . self::KB_CATEGORY_TAXONOMY_SUFFIX;
	}

	/**
	 * Return tag name e.g. ep kb_post_type_1_tag
	 *
	 * @param $kb_id - assumed valid id
	 *
	 * @return string
	 */
	public static function get_tag_taxonomy_name( $kb_id ) {
		return self::get_post_type( $kb_id ) . self::KB_TAG_TAXONOMY_SUFFIX;
	}

	/**
	 * Retrieve KB ID from tag or category taxonomy.
	 * @param $taxonomy
	 * @return bool|int|WP_Error
	 */
	public static function get_kb_id_from_any_taxonomy( $taxonomy ) {
		$kb_id = self::get_kb_id_from_category_taxonomy_name( $taxonomy );
		if ( is_wp_error($kb_id) ) {
			$kb_id = self::get_kb_id_from_tag_taxonomy_name( $taxonomy );
			if ( is_wp_error($kb_id) ) {
				return new WP_Error('49', "kb_id not found");
			}
		}

		return $kb_id;
	}

	/**
	 * Retrieve KB ID from category taxonomy name
	 *
	 * @param $category_name
	 *
	 * @return int | WP_Error
	 */
	public static function get_kb_id_from_category_taxonomy_name( $category_name ) {

		if ( empty($category_name) || ! is_string($category_name)
			|| strpos( $category_name, self::KB_POST_TYPE_PREFIX ) === false || strpos( $category_name, self::KB_CATEGORY_TAXONOMY_SUFFIX ) === false ) {
			return new WP_Error('40', "kb_id not found");
		}

		$kb_id = str_replace( array(self::KB_POST_TYPE_PREFIX, self::KB_CATEGORY_TAXONOMY_SUFFIX ), '', $category_name );

		if ( empty($kb_id) ) {
			return new WP_Error('41', "kb_id not found");
		}

		if ( ! EPKB_Utilities::is_positive_int( $kb_id ) ) {
			return new WP_Error('42', "kb_id not valid");
		}

		return $kb_id;
	}

	/**
	 * Retrieve KB ID from tag taxonomy name
	 *
	 * @param $tag_name
	 *
	 * @return int | WP_Error
	 */
	public static function get_kb_id_from_tag_taxonomy_name( $tag_name ) {

		if ( empty($tag_name) || ! is_string($tag_name)
			|| strpos( $tag_name, self::KB_POST_TYPE_PREFIX ) === false || strpos( $tag_name, self::KB_TAG_TAXONOMY_SUFFIX ) === false ) {
			return new WP_Error('50', "kb_id not found");
		}

		$kb_id = str_replace( array( self::KB_POST_TYPE_PREFIX, self::KB_TAG_TAXONOMY_SUFFIX ), '', $tag_name );

		if ( empty($kb_id) ) {
			return new WP_Error( '51', "kb_id not found" );
		}

		if ( ! EPKB_Utilities::is_positive_int( $kb_id ) ) {
			return new WP_Error( '52', "kb_id not valid" );
		}

		return $kb_id;
	}

	/**
	 * Retrieve KB ID from article type name
	 *
	 * @param String $post_type is post or post type
	 *
	 * @return int | WP_Error if no kb_id found
	 */
	public static function get_kb_id_from_post_type( $post_type ) {

		if ( empty($post_type) || ! is_string($post_type) || strpos( $post_type, self::KB_POST_TYPE_PREFIX ) === false ) {
			return new WP_Error('35', "kb_id not found");
		}

		$kb_id = str_replace( self::KB_POST_TYPE_PREFIX, '', $post_type );
		if ( ! EPKB_Utilities::is_positive_int( $kb_id ) ) {
			return new WP_Error('36', "kb_id not valid");
		}

		return $kb_id;
	}

	/**
	 * Retrieve KB ID from REST API
	 * @param $endpoint
	 * @return int|WP_Error
	 */
	public static function get_kb_id_from_rest_endpoint( $endpoint ) {

		$parts = explode('?', $endpoint);
		if ( empty($parts) ) {
			return new WP_Error('37', "kb_id not valid");
		}

		$parts = explode('/', $parts[0]);
		if ( empty($parts) ) {
			return new WP_Error('37', "kb_id not valid");
		}

		$kb_id = new WP_Error('38', "kb_id not valid");
		foreach( $parts as $part ) {
			if ( ! self::is_kb_post_type( $part ) ) {
				continue;
			}

			if ( strpos( $part, self::KB_CATEGORY_TAXONOMY_SUFFIX ) !== false ) {
				$kb_id = self::get_kb_id_from_category_taxonomy_name( $part );
				break;
			} else if ( strpos( $part, self::KB_TAG_TAXONOMY_SUFFIX ) !== false ) {
				$kb_id = self::get_kb_id_from_tag_taxonomy_name( $part );
				break;
			} else {
				$kb_id = self::get_kb_id_from_post_type( $part );
				break;
			}
		}

		return $kb_id;
	}

	/**
	 * Determine if the current page is KB main page i.e. it contains KB shortcode and return its KB ID if any
	 * @param null $the_post - either pass post to the method or use current post
	 * @return int|null return KB ID if current page is KB main page otherwise null
	 */
	public static function get_kb_id_from_kb_main_shortcode( $the_post=null ) {
		/** @var $wpdb Wpdb */
		global $wpdb;

		// ensure WP knows about the shortcode
		add_shortcode( self::KB_MAIN_PAGE_SHORTCODE_NAME, array( 'EPKB_Layouts_Setup', 'output_kb_page_shortcode' ) );

		$global_post = empty( $GLOBALS['post'] ) ? '' : $GLOBALS['post'];
		$apost = empty( $the_post ) ? $global_post : $the_post;
		if ( empty( $apost ) || empty( $apost->post_content ) || empty( $apost->ID ) ) {
			return null;
		}

		// determine whether this page contains this plugin shortcode
		$content = '';

		// search GT for KB Main Page block
		if ( function_exists( 'parse_blocks' ) ) { // added  in wp 5.0

			$blocks = parse_blocks( $apost->post_content );

			if ( is_array($blocks) && count($blocks) ) {
				foreach ( $blocks as $block ) {
					if ( $block['blockName'] == self::KB_BLOCK_NAME ) {
						if ( ! empty( $block['attrs']['kb_id'] ) ) {
							return $block['attrs']['kb_id'];
						} else {
							return EPKB_KB_Config_DB::DEFAULT_KB_ID;
						}
					}
				}
			}
		}

		// find shortcode in post content or meta data
		if ( has_shortcode( $apost->post_content, self::KB_MAIN_PAGE_SHORTCODE_NAME ) ) {
			$content = $apost->post_content;
		} else if ( isset($apost->ID) ) {
			$content = $wpdb->get_var( "SELECT meta_value FROM $wpdb->postmeta " .
			                           "WHERE post_id = {$apost->ID} and meta_value LIKE '%%" . self::KB_MAIN_PAGE_SHORTCODE_NAME . "%%'" );
		}

		return self::get_kb_id_from_shortcode( $content );
	}

	/**
	 * Retrieve KB ID from post content - shortcode
	 *
	 * @param String $content should have the shortcode with KB ID
	 *
	 * @return int|null returns KB ID if found
	 */
	private static function get_kb_id_from_shortcode( $content ) {

		if ( empty($content) || ! is_string($content) ) {
			return null;
		}

		$start = strpos($content, self::KB_MAIN_PAGE_SHORTCODE_NAME);
		if ( empty($start) || $start < 0 ) {
			return null;
		}

		$end = strpos($content, ']', $start);
		if ( empty($start) || $start < 1 ) {
			return null;
		}

		$shortcode = substr($content, $start, $end);
		if ( empty($shortcode) || strlen($shortcode) < strlen(self::KB_MAIN_PAGE_SHORTCODE_NAME)) {
			return null;
		}

		preg_match_all('!\d+!', $shortcode, $number);
		$number = empty($number[0][0]) ? 0 : $number[0][0];
		if ( ! EPKB_Utilities::is_positive_int( $number ) ) {
			return null;
		}

		return (int)$number;
	}

	/**
	 * Return all KB Main pages that we know about. Also remove old ones.
	 *
	 * @param $kb_config
	 * @return array a list of KB Main Pages titles and links
	 */
	public static function get_kb_main_pages( $kb_config ) {

		$kb_main_pages = $kb_config['kb_main_pages'];
		$kb_main_pages_info = array();
		foreach ( $kb_main_pages as $post_id => $post_title ) {

			$post_status = get_post_status( $post_id );

			// remove previous page versions
			if ( empty( $post_status ) || $post_status == 'inherit' || $post_status == 'trash' ) {
				unset( $kb_main_pages[ $post_id ] );
				continue;
			}

			$post = get_post( $post_id );
			if ( empty( $post ) || is_array( $post ) || ! $post instanceof WP_Post ) {
				unset( $kb_main_pages[ $post_id ] );
				continue;
			}

			// remove page that does not contain KB shortcode any more
			$kb_id = self::get_kb_id_from_kb_main_shortcode( $post );
			if ( empty( $kb_id ) || $kb_id != $kb_config['id'] ) {
				unset( $kb_main_pages[ $post_id ] );
				continue;
			}

			$kb_post_slug = get_page_uri($post_id);  // includes PARENT directory slug
			if ( is_wp_error( $kb_post_slug ) || empty($kb_post_slug) || is_array($kb_post_slug) ) {
				$kb_post_slug = EPKB_KB_Handler::get_default_slug( $kb_id );
			}

			$kb_main_pages_info[$post_id] = array( 'post_title' => $post_title, 'post_status' => EPKB_Utilities::get_post_status_text( $post_status ), 'post_slug' => urldecode($kb_post_slug) );
		}

		// we need to remove pages that are revisions
		if ( count( $kb_config['kb_main_pages'] ) != count($kb_main_pages) ) {
			$kb_config['kb_main_pages'] = $kb_main_pages;
			epkb_get_instance()->kb_config_obj->update_kb_configuration( $kb_config['id'], $kb_config );  // ignore error for now
		}

		return $kb_main_pages_info;
	}

	/**
	 * Find KB Main Page that is not in trash and get its URL (page that matches kb_articles_common_path in KB config or first main page URL).
	 *
	 * @param $kb_config
	 * @return string
	 */
	public static function get_first_kb_main_page_url( $kb_config ) {

		$first_page_url = '';
		$kb_main_pages = $kb_config['kb_main_pages'];

		foreach ( $kb_main_pages as $post_id => $post_title ) {

			if ( empty( $post_id ) ) {
				continue;
			}

			$post = get_post( $post_id );
			if ( ! empty( $post ) && ! is_array( $post ) ) {

				$main_page_url = get_permalink( $post_id );
				if ( ! empty( $main_page_url ) && ! is_wp_error( $main_page_url ) ) {

					$main_page_path = urldecode( sanitize_title_with_dashes( $post->post_name, '', 'save' ) );
					if ( $main_page_path == $kb_config['kb_articles_common_path'] ) {
						return $main_page_url;
					}
					$first_page_url = empty( $first_page_url ) ?  $main_page_url : $first_page_url;
				}
			}
		}

		return $first_page_url;
	}

	/**
	 * Find KB Main Page ID (page that matches kb_articles_common_path in KB config or first main page id).
	 *
	 * @param $kb_config
	 * @return string
	 */
	public static function get_first_kb_main_page_id( $kb_config ) {

		$first_post_id = '';
		$kb_main_pages = $kb_config['kb_main_pages'];

		foreach ( $kb_main_pages as $post_id => $post_title ) {

			if ( count($kb_main_pages) == 1 ) {
				return $post_id;
			}

			$post = get_post( $post_id );
			if ( ! empty( $post ) && ! is_array( $post ) ) {

				$main_page_path = urldecode( sanitize_title_with_dashes( $post->post_name, '', 'save' ) );
				if ( $main_page_path == $kb_config['kb_articles_common_path'] ) {
					return $post_id;
				}
				$first_post_id = empty( $first_post_id ) ?  $post_id : $first_post_id;
			}
		}

		return $first_post_id;
	}

	/**
	 * Find first article url.
	 *
	 * @param $kb_config
	 * @return string
	 */
	public static function get_first_kb_article_url( $kb_config ) {

		$custom_categories_data = EPKB_Utilities::get_kb_option( $kb_config['id'], EPKB_Categories_Admin::KB_CATEGORIES_SEQ_META, null, true );
		if ( empty($custom_categories_data) ) {
			return '';
		}

		$articles_seq_data = EPKB_Utilities::get_kb_option( $kb_config['id'], EPKB_Articles_Admin::KB_ARTICLES_SEQ_META, array(), true );
		if ( empty($articles_seq_data) ) {
			return '';
		}

		$category_seq_array = EPKB_Articles_Setup::epkb_get_array_keys_multiarray( $custom_categories_data, $kb_config );
		foreach( $category_seq_array as $cat_seq_id ) {

			if ( empty($articles_seq_data[$cat_seq_id]) || sizeof($articles_seq_data[$cat_seq_id]) < 3 ) {
				continue;
			}

			$data = array_keys($articles_seq_data[$cat_seq_id]);

			for( $i=2; $i < count($data); $i++ ) {

				if ( empty($data[$i]) ) {
					continue;
				}

				$article_id = $data[$i];
				if ( ! EPKB_Utilities::is_link_editor_enabled() ) {
					return get_permalink( $article_id );
				}

				$post = get_post( $article_id );

				if ( empty( $post ) ) {
					continue;
				}

				// check if KB Article is linked
				if ( EPKB_Utilities::is_link_editor( $post ) ) {
					continue;
				}

				return get_permalink( $article_id );
			}

		}

		return '';
	}

	/**
	 * Find category with most articles
	 *
	 * @param $kb_config
	 * @return string
	 */
	public static function get_kb_category_with_most_articles_url( $kb_config ) {

		$articles_seq_data = EPKB_Utilities::get_kb_option( $kb_config['id'], EPKB_Articles_Admin::KB_ARTICLES_SEQ_META, array(), true );
		if ( empty($articles_seq_data) ) {
			return '';
		}

		$articles_seq_data_length = array_map('sizeof', $articles_seq_data);
		arsort($articles_seq_data_length);
		$category_id = key($articles_seq_data_length); // the top is the category with most articles

		if ( empty($category_id) ) {
			return '';
		}

		return get_category_link( $category_id );
	}

	/**
	 * Get relevant KB id
	 *
	 * @return int
	 */
	public static function get_relevant_kb_id() {

		// define relevant KB id
		$kb_id = self::get_current_kb_id();
		if ( empty( $kb_id ) ) {
			$kb_id = EPKB_KB_Config_DB::DEFAULT_KB_ID;
		}

		return (int)$kb_id;
	}

	/**
	 * Regenerate KB sequence for Categories and Articles if missing
	 *
	 * @param int $kb_id
	 * @param array $category_seq_data
	 *
	 * @return array|string|null
	 */
	public static function get_refreshed_kb_categories( $kb_id, $category_seq_data=null ) {

		if ( $category_seq_data === null ) {
			$category_seq_data = EPKB_Utilities::get_kb_option( $kb_id, EPKB_Categories_Admin::KB_CATEGORIES_SEQ_META, array(), true );
		}

		// if non-empty categories sequence in DB then nothing to do
		if ( ! empty( $category_seq_data ) && is_array( $category_seq_data ) ) {
			return $category_seq_data;
		}

		// determine why categories are missing - get categories from DB
		$all_terms = EPKB_Core_Utilities::get_kb_categories_unfiltered( $kb_id );
		// if error then nothing to do
		if ( $all_terms === null ) {
			return null;
		}
		// if empty then that is expected
		if ( empty( $all_terms ) ) {
			return [];
		}

		// regenerate articles
		$article_admin = new EPKB_Articles_Admin();
		$is_articles_updated = $article_admin->update_articles_sequence( $kb_id ); // ignore result as we are focusing on categories
		if ( ! $is_articles_updated ) {
			EPKB_Logging::add_log( 'Could not update article sequence for KB ' . $kb_id );
		}

		// regenerate categories
		$category_admin = new EPKB_Categories_Admin();
		$category_taxonomy_slug = EPKB_KB_Handler::get_category_taxonomy_name( $kb_id );
		$result = $category_admin->update_categories_sequence( 0, 0, $category_taxonomy_slug );
		if ( empty( $result ) ) {
			EPKB_Logging::add_log( 'Could not update category sequence for KB ' . $kb_id );
			return null;
		}

		return EPKB_Utilities::get_kb_option( $kb_id, EPKB_Categories_Admin::KB_CATEGORIES_SEQ_META, array(), true );
	}
}
