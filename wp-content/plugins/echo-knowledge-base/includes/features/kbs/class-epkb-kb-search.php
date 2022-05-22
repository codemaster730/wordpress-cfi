<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Search Knowledge Base
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 */
class EPKB_KB_Search {

	public function __construct() {
		add_action( 'wp_ajax_epkb-search-kb', array( $this, 'search_kb' ) );
		add_action( 'wp_ajax_nopriv_epkb-search-kb', array( $this, 'search_kb' ) );  // users not logged-in should be able to search as well
	}

	/**
	 * Process AJAX search request
	 */
	public function search_kb() {

		// we don't need nonce and permission check here

		$kb_id = EPKB_Utilities::sanitize_get_id( $_GET['epkb_kb_id'] );
		if ( is_wp_error( $kb_id ) ) {
			wp_die( json_encode( array( 'status' => 'success', 'search_result' => EPKB_Utilities::report_generic_error( 5 ) ) ) );
		}

		$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( $kb_id );

		// remove question marks
		$search_terms = EPKB_Utilities::get( 'search_words' );
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

			$not_found_count = EPKB_Utilities::get_kb_option( $kb_id, 'epkb_miss_search_counter', 0 );
			EPKB_Utilities::save_kb_option( $kb_id, 'epkb_miss_search_counter', $not_found_count + 1, true );

			wp_die( json_encode( array( 'status' => 'success', 'search_result' => $search_result ) ) );
		}

		// ensure that links have https if the current schema is https
		set_current_screen('front');

		$prefix = EPKB_Core_Utilities::is_kb_main_page() ? '' : 'article_';
		$search_result = '<div class="epkb-search-results-message">' . esc_html( $kb_config[$prefix . 'search_results_msg'] ) . ' ' . $search_terms . '</div>';
		$search_result .= '<ul>';

		$title_style = '';
		$icon_style  = '';
		if ( $kb_config['search_box_results_style'] == 'on' ) {
			$color_prefix = EPKB_Utilities::is_elegant_layouts_enabled() ? 'sidebar_' : '';  // are we taking color from Sidebar ?
			$title_style = EPKB_Utilities::get_inline_style( 'color:: ' . $color_prefix . 'article_font_color' , $kb_config);
			$icon_style = EPKB_Utilities::get_inline_style( 'color:: ' . $color_prefix . 'article_icon_color' , $kb_config);
		}

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

			// linked articles have the open in new tab option
			$new_tab = '';
			if ( class_exists( 'KBLK_Utilities' ) && method_exists( 'KBLK_Utilities', 'get_postmeta' ) ) {
				$link_editor_config = KBLK_Utilities::get_postmeta( $post->ID, 'kblk-link-editor-data', [], true );
				$new_tab            = empty( $link_editor_config['open-new-tab'] ) ? '' : 'target="_blank"';
			}

			$search_result .=
				'<li>' .
					'<a href="' .  esc_url( $article_url ) . '" ' . $new_tab . ' class="epkb-ajax-search" data-kb-article-id="' . $post->ID . '">' .
						'<span class="epkb_search_results__article-title" ' . $title_style . '>' .
                            '<span class="epkb_search_results__article-title__icon epkbfa ' . esc_attr($article_title_icon) . ' ' . $icon_style . '"></span>' .
							'<span class="epkb_search_results__article-title__text">' . esc_html($post->post_title) . '</span>' .
						'</span>' .
					'</a>' .
				'</li>';
		}
		$search_result .= '</ul>';

		$serach_count = EPKB_Utilities::get_kb_option( $kb_id, 'epkb_hit_search_counter', 0 );
		EPKB_Utilities::save_kb_option( $kb_id, 'epkb_hit_search_counter', $serach_count + 1, true );

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

		$result = array();
		$search_params = array(
				's' => $search_terms,
				'post_type' => EPKB_KB_Handler::get_post_type( $kb_id ),
				'ignore_sticky_posts' => true,  // sticky posts will not show at the top
				'posts_per_page' => 20,         // limit search results
				'no_found_rows' => true,        // query only posts_per_page rather than finding total nof posts for pagination etc.
				'cache_results' => false,       // don't need that for mostly unique searches
				'orderby' => 'relevance'
		);

		// OLD installation or Access Manager
		$search_params['post_status'] = array( 'publish' );
		if ( EPKB_Utilities::is_amag_on() ) {
			$search_params['post_status'] = array( 'publish', 'private' );
		} else if ( EPKB_Utilities::is_new_user( '7.4.0' ) && is_user_logged_in() ) {
			$search_params['post_status'] = array( 'publish', 'private' );
		}

		$found_posts_obj = new WP_Query( $search_params );
		if ( ! empty($found_posts_obj->posts) ) {
			$result = $found_posts_obj->posts;
			wp_reset_postdata();
		}

		return $result;
	}

	/**
	 * Display a search form for core layouts
	 *
	 * @param $kb_config
	 */
	public static function get_search_form( $kb_config ) {
	   global $eckb_is_kb_main_page;

		if ( EPKB_Utilities::is_advanced_search_enabled( $kb_config ) ) {
			do_action( 'eckb_advanced_search_box', $kb_config );
			return;
		}

		$prefix = EPKB_Core_Utilities::is_kb_main_page() ? '' : 'article_';

		// no search box configured or required
		if ( $kb_config[$prefix . 'search_layout'] == 'epkb-search-form-0' ) {
			return;
		}

		$style1 = self::get_inline_style( $kb_config,
			'background-color:: ' . $prefix . 'search_background_color,
			 padding-top:: ' . $prefix . 'search_box_padding_top,
			 padding-right:: ' . $prefix . 'search_box_padding_right,
			 padding-bottom:: ' . $prefix . 'search_box_padding_bottom,
			 padding-left:: ' . $prefix . 'search_box_padding_left,
			 margin-top:: ' . $prefix . 'search_box_margin_top,
			 margin-bottom:: ' . $prefix . 'search_box_margin_bottom,
			 ');

		$style2 = self::get_inline_style( $kb_config,
			'background-color:: ' . $prefix . 'search_btn_background_color,
			 background:: ' . $prefix . 'search_btn_background_color, 
			 border-color:: ' . $prefix . 'search_btn_border_color'
			 );
		$style3 = self::get_inline_style( $kb_config, 'color:: ' . $prefix . 'search_title_font_color, typography:: ' . $prefix . 'search_title_typography');
		$style4 = self::get_inline_style( $kb_config, 'border-width:: ' . $prefix . 'search_input_border_width, border-color:: ' . $prefix . 'search_text_input_border_color,
											background-color:: ' . $prefix . 'search_text_input_background_color, background:: ' . $prefix . 'search_text_input_background_color' );
		$class1 = self::get_css_class( $kb_config, 'epkb-search, :: ' . $prefix . 'search_layout' );

		$search_title_tag = empty($kb_config[$prefix . 'search_title_html_tag']) ? 'div' : $kb_config[$prefix . 'search_title_html_tag'];
		$search_input_width = $kb_config[$prefix . 'search_box_input_width'];
		$form_style = self::get_inline_style( $kb_config, 'width:' . $search_input_width . '%' );

	   $main_page_indicator = empty($eckb_is_kb_main_page) ? '' : 'eckb_search_on_main_page';    ?>

		<div class="epkb-doc-search-container <?php echo $main_page_indicator; ?>" <?php echo $style1; ?> >

			<<?php echo $search_title_tag; ?> class="epkb-doc-search-container__title" <?php echo $style3; ?>> <?php echo esc_html( $kb_config[$prefix . 'search_title'] ); ?></<?php echo $search_title_tag; ?>>
			<form id="epkb_search_form" <?php echo $form_style . ' ' . $class1; ?> method="get" action="">

				<div class="epkb-search-box">
					<input type="text" <?php echo $style4; ?> id="epkb_search_terms" aria-label="<?php echo esc_attr( $kb_config[$prefix . 'search_box_hint'] ); ?>" name="epkb_search_terms" value="" placeholder="<?php echo esc_attr( $kb_config[$prefix . 'search_box_hint'] ); ?>" />
					<input type="hidden" id="epkb_kb_id" value="<?php echo $kb_config['id']; ?>"/>
					<div class="epkb-search-box_button-wrap">
						<button type="submit" id="epkb-search-kb" <?php echo $style2; ?>><?php echo esc_html( $kb_config[$prefix . 'search_button_name'] ); ?> </button>
					</div>
					<div class="loading-spinner"></div>
				</div>
				<div id="epkb_search_results"></div>

			</form>

		</div>  <?php
	}

	/**
	 * Output inline CSS style based on configuration.
	 *
	 * @param $kb_config
	 * @param string $styles A list of Configuration Setting styles
	 *
	 * @return string
	 */
	public static function get_inline_style( $kb_config, $styles ) {
		return EPKB_Utilities::get_inline_style( $styles, $kb_config );
	}

	/**
	 * Output CSS classes based on configuration.
	 *
	 * @param $kb_config
	 * @param $classes
	 *
	 * @return string
	 */
	public static function get_css_class( $kb_config, $classes ) {
		return EPKB_Utilities::get_css_class( $classes, $kb_config );
	}
}
