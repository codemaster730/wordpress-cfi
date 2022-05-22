<?php

defined( 'ABSPATH' ) || exit();

class EPKB_Help_Dialog_Search {

	public function __construct() {

		add_action( 'wp_ajax_epkb_help_dialog_get_category_list', array($this, 'get_category_list') );
		add_action( 'wp_ajax_nopriv_epkb_help_dialog_get_category_list', array($this, 'get_category_list') );

		add_action( 'wp_ajax_epkb_help_dialog_article_detail', array($this, 'get_article_detail' ) );
		add_action( 'wp_ajax_nopriv_epkb_help_dialog_article_detail', array($this, 'get_article_detail' ) );

		add_action( 'wp_ajax_epkb_help_dialog_search_kb', array($this, 'search_kb') );
		add_action( 'wp_ajax_nopriv_epkb_help_dialog_search_kb', array($this, 'search_kb') );
	}

	/**
	 * Get category list
	 */
	public function get_category_list() {

		$settings = epkb_get_instance()->settings_obj->get_settings_or_default();
		$kb_id = epkb_get_instance()->settings_obj->get_value( 'help_dialog_faqs_kb', EPKB_KB_Config_DB::DEFAULT_KB_ID );
		$kb_id = EPKB_Utilities::sanitize_kb_id( $kb_id );
		$kb_categories = [];
		$category_list = '';
		$article_list = '';

		$temp_kb_categories = EPKB_Utilities::get_kb_categories_unfiltered( $kb_id );
		if ( empty($temp_kb_categories) ) {
			wp_die( json_encode( array( 'status' => 'success', 'category_list' => $category_list, 'article_list' => $article_list ) ) );
		}

		foreach( $temp_kb_categories as $kb_category ) {
			$kb_categories[$kb_category->term_id] = $kb_category;
		}

		// get selected categories or all if none selected
		$kb_category_ids = empty($settings['help_dialog_faqs_category_ids']) ? [] : explode(',', $settings['help_dialog_faqs_category_ids']);
		if ( empty($kb_category_ids) ) {
			foreach( $kb_categories as $kb_category ) {
				$kb_category_ids[] = $kb_category->term_id;
			}
		}

		if ( empty($kb_category_ids) ) {
			wp_die( json_encode( array( 'status' => 'success', 'category_list' => $category_list ) ) );
		}

		$displayed_category_ids = [];
		$category_list = "<ul class='epkb-hd_categories'>";
		foreach( $kb_category_ids as $kb_category_id ) {

			if ( empty($kb_category_id) || empty($kb_categories[$kb_category_id]) ) {
				continue;
			}

			$kb_category = $kb_categories[$kb_category_id];
			if ( empty($kb_category->term_id) ) {
				continue;
			}

			$displayed_category_ids[] = $kb_category_id;

			$category_list .= '<li id="epkb-hd_cat-' . $kb_category->term_id . '" class="epkb-hd_cat-item" data-category="' . $kb_category->term_id. '">
					<span class="epkb-hd_cat-name">
						<span class="epkb-hd_cat-name__icon epkbfa epkbfa-folder-o"></span>
						<span class="epkb-hd_cat-name__text">' . $kb_category->name . '</span>
					</span>
			</li>';
		}
		$category_list .= "</ul>";

		if ( empty($displayed_category_ids) ) {
			wp_die( json_encode( array( 'status' => 'success', 'category_list' => $category_list, 'article_list' => $article_list ) ) );
		}

		foreach( $displayed_category_ids as $kb_category_id ) {
			$article_list .= '<div class="epkb-help_dialog_article-box" data-category="' . $kb_category_id . '">';
			$article_list .=  self::article_list_by_category($kb_id, $kb_category_id);
			$article_list .= '</div>';
		}

		wp_die( json_encode( array( 'status' => 'success', 'category_list' => $category_list, 'article_list' => $article_list ) ) );
	}

	/**
	 * Get Article details
	 */
	public function get_article_detail() {

		$article_id = EPKB_Utilities::get( 'article_id' );
		if ( empty($article_id) ) {
			wp_die( json_encode( array( 'status' => 'success', 'search_result' => '' ) ) );
		}

		$data_type = EPKB_Utilities::get( 'type' );
		$read_more = epkb_get_instance()->settings_obj->get_value( 'hd_faqs_read_more_text' );
		$search_result =
			"<div class='epkb-hd_article-item-details epkb-hd_type_" . $data_type . "'>
				<div class='epkb-hd_article-title'>" . get_the_title( $article_id ) . "</div>
				<div class='epkb-hd_article-desc'>" . get_the_excerpt( $article_id ) . "</div>
				<a class='epkb-hd_article-link' href='" . get_permalink( $article_id ) . "' target='_blank'>". $read_more . "</a>
			</div>";

		wp_die( json_encode( array( 'status' => 'success', 'search_result' => $search_result ) ) );
	}

	/**
	 * Search Help dialog KB for articles within defined categories
	 */
	public function search_kb() {

		$kb_id = epkb_get_instance()->settings_obj->get_value( 'help_dialog_faqs_kb', EPKB_KB_Config_DB::DEFAULT_KB_ID );
		$kb_id = EPKB_Utilities::sanitize_kb_id( $kb_id );

		$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( $kb_id );

		// remove question marks
		$search_terms = EPKB_Utilities::get( 'search_terms' );
		$search_terms = stripslashes( $search_terms );
		$search_terms = str_replace('?', '', $search_terms);
		$search_terms = str_replace( array( "\r", "\n" ), '', $search_terms );

		// require minimum size of search word(s)
		if ( empty($search_terms) ) {
			wp_die( json_encode( array( 'status' => 'success', 'search_result' => esc_html( $kb_config['min_search_word_size_msg'] ) ) ) );
		}

		// search for given keyword(s)
		$result = $this->execute_search( $kb_id, $search_terms );
		if ( empty($result) ) {
			$search_result = $kb_config['no_results_found'];
			wp_die( json_encode( array( 'status' => 'success', 'search_result' => $search_result ) ) );
		}

		$search_result = '<ul>';

		// display one line for each search result
		foreach ( $result as $post ) {

			$article_url = get_permalink( $post->ID );
			if ( empty($article_url) || is_wp_error( $article_url )) {
				continue;
			}

			// linked articles have their own icon
			$article_title_icon = 'ep_font_icon_document';
			if ( has_filter( 'eckb_single_article_filter' ) ) {
				$article_title_icon = apply_filters( 'eckb_article_icon_filter', $article_title_icon, $post->ID );
				$article_title_icon = empty( $article_title_icon ) ? 'epkbfa-file-text-o' : $article_title_icon;
			}

			$article_data = 'data-kb-article-id=' . $post->ID . ' data-kb-id=' . $kb_id;

			$search_result .=
				'<li data-type="search" class="epkb-hd_article-item" ' . $article_data . ' >  
											
					<span class="epkb-hd_article-item__name">
						<span class="epkb-hd_article-item__icon ' . $article_title_icon . '"></span>
						<span class="epkb-hd_article-item__text">' . esc_html($post->post_title) . '</span>
					</span>
						
				</li>';
		}

		$search_result .= '</ul>';

		wp_die( json_encode( array( 'status' => 'success', 'search_result' => $search_result ) ) );
	}

	/**
	 * Call WP query to get matching terms (any term OR match)
	 *
	 * @param $kb_id
	 * @param $search_terms
	 * @return array
	 */
	private function execute_search( $kb_id, $search_terms ) {

		// add-ons can adjust the search
		if ( has_filter( 'eckb_execute_search_filter' ) ) {
			$result = apply_filters('eckb_execute_search_filter', '', $kb_id, $search_terms );
			if ( is_array($result) ) {
				return $result;
			}
		}

		$post_status_search = class_exists('AM'.'GR_Access_Utilities', false) ? array('publish', 'private') : array('publish');

		$result = array();
		$search_params = array(
			's' => $search_terms,
			'post_type' => EPKB_KB_Handler::get_post_type( $kb_id ),
			'post_status' => $post_status_search,
			'ignore_sticky_posts' => true,  // sticky posts will not show at the top
			'posts_per_page' => 20,         // limit search results
			'no_found_rows' => true,        // query only posts_per_page rather than finding total nof posts for pagination etc.
			'cache_results' => false,       // don't need that for mostly unique searches
			'orderby' => 'relevance'
		);

		$found_posts_obj = new WP_Query( $search_params );
		if ( ! empty($found_posts_obj->posts) ) {
			$result = $found_posts_obj->posts;
			wp_reset_postdata();
		}

		return $result;
	}

	/**
	 * Get Article list from a given category
	 *
	 * @param $kb_id
	 * @param $kb_category_id
	 * @return string
	 */
	private static function article_list_by_category( $kb_id, $kb_category_id ) {

		$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( $kb_id );

		// article sequence
		$articles_seq_data = EPKB_Utilities::get_kb_option( $kb_id, EPKB_Articles_Admin::KB_ARTICLES_SEQ_META, array(), true );
		if ( empty($articles_seq_data) ) {
			return '';
		}

		// for WPML filter categories and articles given active language
		if ( EPKB_Utilities::is_wpml_enabled( $kb_config ) ) {
			$articles_seq_data = EPKB_WPML::apply_article_language_filter( $articles_seq_data );
		}

		$articles_list = array();
		$search_result = '';
		if ( isset($articles_seq_data[ $kb_category_id]) ) {
			$articles_list = $articles_seq_data[ $kb_category_id];
			$search_result .= '<div class="epkb-hd__category_title">' . $articles_list[0] . '</div>';
			unset($articles_list[0]);
			unset($articles_list[1]);
		}

		if ( empty($articles_list) ) {
			$search_result .= epkb_get_instance()->kb_config_obj->get_value( 'category_empty_msg', $kb_id );
			return $search_result;
		}

		$search_result .= '<ul class="epkb-hd__article_list">';
		foreach ( $articles_list as $article_id => $article_title ) {

			$article_data = 'data-kb-article-id=' . $article_id . ' data-kb-id=' . $kb_id;

			$search_result .=
				'<li data-type="category" class="epkb-hd_article-item" ' . $article_data . ' >  
					<span class="epkb-hd_article-item__name">
						<span class="epkb-hd_article-item__icon ep_font_icon_document"></span>
						<span class="epkb-hd_article-item__text">' . $article_title . '</span>
					</span>
			   	</li>';
		}
		$search_result .= '</ul>';

		return $search_result;
	}
}