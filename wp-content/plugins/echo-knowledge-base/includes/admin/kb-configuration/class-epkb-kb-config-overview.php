<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Display Overview information that is displayed with KB Configuration page
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_KB_Config_Overview {

	/**
	 * Return KB status line
	 *
	 * @param $kb_config
	 * @param $chosen_layout - layout user just switched to or empty
	 * @return string
	 */
	public static function get_kb_status_line( $kb_config, $chosen_layout='' ) {

		$status = self::get_kb_status_code( $kb_config, $chosen_layout );
		$status_tab_url = 'edit.php?post_type=' . EPKB_KB_Handler::get_post_type( $kb_config['id'] ) . '&page=epkb-kb-configuration';
		$status_class = $status == 'OK' ? 'kb_status_success' : ( $status == 'Warning' ? 'kb_status_warning' : 'kb_status_error error_pulse' );

		$output = '<div id="status_line" class="kb_status ' . $status_class . '">';

		$status_msg = '';
		switch( $status ) {
			case 'OK':
				$status_msg = __( 'KB Status: OK', 'echo-knowledge-base' );
				break;
			case 'Warning':
				$status_msg = __( 'KB Status: OK', 'echo-knowledge-base' );
				break;
			case 'ERROR':
				$status_msg = __( 'KB Status: Error', 'echo-knowledge-base' );
				break;
		}

		$output .= '<strong>' . esc_html( $status_msg ) . '</strong>';

		if ( $status != 'OK' ) {
			$output .= " - <strong><a href='$status_tab_url'>" . esc_html__( 'Learn More', 'echo-knowledge-base' ) . "</a></strong>";
		}

		$output .= '</div>';

		return $output;
	}

	private static function get_kb_status_code( $kb_config, $chosen_layout ) {
		$add_on_messages = apply_filters( 'epkb_add_on_license_message', array() );
		if ( ! empty($add_on_messages) ) {
			return 'ERROR';
		}

		$warning_msg = self::get_kb_status( $kb_config, $chosen_layout );
		if ( ! empty($warning_msg) ) {
			return 'Warning';
		}

		return 'OK';
	}

	/**
	 * Show status of current Knowledge Base
	 *
	 * @param $kb_config
	 * @param string $chosen_layout - layout user just switched to or empty
	 * @param array $articles_seq_data
	 * @param array $category_seq_data
	 * @return array
	 */
	private static function get_kb_status( $kb_config, $chosen_layout='', $articles_seq_data=array(), $category_seq_data=array() ) {

		$message = array();
		$kb_id = $kb_config['id'];
		$current_layout =  empty($chosen_layout) ? EPKB_KB_Config_Layouts::get_kb_main_page_layout_name( $kb_config ) : $chosen_layout;

		// 1. ensure we have KB pages with KB shortcode
		$kb_main_pages = $kb_config['kb_main_pages'];
		$kb_main_page_found = false;
		foreach( $kb_main_pages as $post_id => $post_title ) {
			$post_status = get_post_status( $post_id );
			if ( ! empty($post_status) && in_array( $post_status, array( 'publish', 'future', 'private' ) ) ) {
				$kb_main_page_found = true;
				break;
			}
		}

		if ( ! $kb_main_page_found ) {
			/* translators: refers to Knowledge Base main page that shows all links to articles */
			$i18_KB_Main = '<strong>' . esc_html__( 'Knowledge Base Main', 'echo-knowledge-base' ) . '</strong>';
			$i18_KB_shortcode = '<strong>' . esc_html__( 'KB shortcode', 'echo-knowledge-base' ) . '</strong>';
			/* translators: first %s will be replaced with the word 'Knowledge Base Main' (in bold) and the second %s will be replaced with 'KB shortcode' (also in bold). */
			$message[] = '<div class="status_group"><p>' .
			            sprintf( __( 'Did not find active %s page. Only page with %s will display KB Main page. If you do have a KB shortcode on a page, ' .
			                         'save that page and this message should disappear.', 'echo-knowledge-base' ), $i18_KB_Main, $i18_KB_shortcode ) . '</p></div>';
		}

		$i18_articles = '<strong>' . esc_html__( 'articles', 'echo-knowledge-base' ) . '</strong>';
		$i18_edit_word = esc_html__( 'Edit', 'echo-knowledge-base' );
		$i18_category = '<strong>' . esc_html__(  _x( 'category', 'taxonomy singular name', 'echo-knowledge-base' ), 'echo-knowledge-base' ) . '</strong>';

		// 2. check orphan articles
		$article_db = new EPKB_Articles_DB();
		$orphan_articles = $article_db->get_orphan_published_articles( $kb_id );
		if ( ! empty($orphan_articles) ) {
			$temp = '';
			foreach( $orphan_articles as $orphan_article ) {
				$temp = '<li>' . $orphan_article->post_title . ' &nbsp;&nbsp;' . '<a href="' .  get_edit_post_link( $orphan_article->ID ) . '" target="_blank">' . $i18_edit_word . '</a></li>';
			}

			$message[] = '<div class="status_group">' .
							/* translators: the %s will be replaced with the word 'articles' (in bold) */
							'<p>' . sprintf( esc_html__( 'The following %s have no categories assigned:', 'echo-knowledge-base' ), $i18_articles ) . '</p>' .
							'<ul>' . $temp . '</ul>' .
						 '</div>';
		}

		if ( empty($articles_seq_data) || empty($category_seq_data) ) {
			// ensure category hierarchy is up to date
			$category_admin = new EPKB_Categories_Admin();
			$category_admin->update_categories_sequence();

			// ensure articles assignment to categories is up to date
			$article_admin = new EPKB_Articles_Admin();
			$article_admin->update_articles_sequence( $kb_id );

			// category and article sequence
			$articles_seq_data = EPKB_Utilities::get_kb_option( $kb_id, EPKB_Articles_Admin::KB_ARTICLES_SEQ_META, array(), true );
			$category_seq_data = EPKB_Utilities::get_kb_option( $kb_id, EPKB_Categories_Admin::KB_CATEGORIES_SEQ_META, array(), true );
		}

		// 3. check if this is Tabs layout and there are articles attached to the top level category
		//    AND do not have any other non-top category, report them
		if ( $current_layout == EPKB_KB_Config_Layout_Tabs::LAYOUT_NAME ) {

			// 3.1 retrieve top-level categories and attached articles
			$top_level_categories = array();
			$top_level_category_articles = array();
			foreach ( $category_seq_data as $category_id => $subcategories ) {
				$top_level_categories[] = $category_id;

				// ignore empty category
				if ( $category_id == 0 || empty($articles_seq_data[$category_id]) || count($articles_seq_data[$category_id]) < 3 ) {
					continue;
				}

				$top_level_category_articles += $articles_seq_data[$category_id];
				unset($top_level_category_articles[0]);
				unset($top_level_category_articles[1]);
			}

			// 3.2 remove top-level articles that are also attached sub-catagories
			foreach ( $articles_seq_data as $category_id => $sub_category_article_list ) {
				// skip top level categories
				if ( in_array($category_id, $top_level_categories) || $category_id == 0 ) {
					continue;
				}
				// does sub-category have top-level article as well?
				unset($sub_category_article_list[0]);
				unset($sub_category_article_list[1]);
				foreach ( $top_level_category_articles as $top_level_article_id => $top_level_article_title ) {
					if ( in_array($top_level_article_id, array_keys($sub_category_article_list) ) ) {
						unset($top_level_category_articles[$top_level_article_id]);
					}
				}
			}

			// 3.3 output articles that are only on top-level
			$top_level_msg = '';
			$ix = 0;
			$top_level_category_articles = array_unique( $top_level_category_articles );
			foreach( $top_level_category_articles as $top_level_article_id => $top_level_article_title ) {
				$ix++;
				$top_level_msg .= '<li>' . $top_level_article_title . ' &nbsp;&nbsp;' . '<a href="' .  get_edit_post_link( $top_level_article_id ) . '" target="_blank">' . $i18_edit_word . '</a></li>';
			}

			if (  !empty($top_level_msg) ) {
				$i18_layout = '<strong>' . esc_html__( 'Layout', 'echo-knowledge-base' ) . '</strong>';
				$i18_tabs = '<strong>' . esc_html__( 'Tabs', 'echo-knowledge-base' ) . '</strong>';

				/* translators: the first %s will be replaced with the word 'Layout' (in bold) and the second %s will replaced with 'Tabs' word (in bold) */
				$msg1 = sprintf( esc_html__( 'Current %s is set to %s.', 'echo-knowledge-base' ), $i18_layout, $i18_tabs );
				/* translators: the %s will be replaced with the word 'category' (in bold) */
				$msg2 = sprintf( esc_html(_n( 'The following article has only top-level %s and will not be displayed' .
				                              ' on KB Main page. In the Tab layout, this article needs to be assigned to a sub-category.',
						'The following articles have only top-level %s and will not be displayed' .
						' on KB Main page. In the Tab layout, these articles need to be assigned to a sub-category.', $ix, 'echo-knowledge-base')), $i18_category );

				$message[] = '<div class="status_group">'.
				                '<p>'. $msg1 .'</p>'.
				                '<p>' . $msg2 . '</p>
			                    <ul>'. $top_level_msg . '</ul>
			                </div>';
			}
		}

		$stored_ids_obj = new EPKB_Categories_Array( $category_seq_data ); // normalizes the array as well
		$category_ids_levels = $stored_ids_obj->get_all_keys();


		// 4. check if user does not have too many levels of categories; these categories and articles within them
		//    will not show; ignore empty categories
		add_filter( 'epkb_max_layout_level', array( 'EPKB_KB_Config_Layouts', 'get_max_layout_level') );
		$max_category_level = apply_filters( 'epkb_max_layout_level', $current_layout );
		$max_category_level = EPKB_Utilities::is_positive_or_zero_int( $max_category_level ) ? $max_category_level : 6;
		if ( $max_category_level > 0 ) {

			// 4.1 get all visible articles
			$visible_articles = array();
			foreach ( $category_ids_levels as $category_id => $level ) {
				if ( $level <= $max_category_level && ! empty( $articles_seq_data[ $category_id ] ) && count( $articles_seq_data[ $category_id ] ) > 2 ) {
					$visible_articles += $articles_seq_data[ $category_id ];
					unset( $visible_articles[0] );
					unset( $visible_articles[1] );
				}
			}

			// 4.2 get invisible subcategories (these categories are too deep)
			$invisible_articles = array();
			$invisible_cat_msg  = '';
			foreach ( $category_ids_levels as $category_id => $level ) {
				if ( $level > $max_category_level && ! empty( $articles_seq_data[ $category_id ] ) ) {
					$invisible_cat_msg .= '<li>' . $articles_seq_data[ $category_id ][0] . ' &nbsp;&nbsp;' . '<a href="' .
					                      get_edit_term_link( $category_id, EPKB_KB_Handler::get_category_taxonomy_name( $kb_id ), EPKB_KB_Handler::get_post_type( $kb_id ) ) .
					                      '" target="_blank">' . $i18_edit_word . '</a></li>';
					$invisible_articles += $articles_seq_data[ $category_id ];
					unset( $invisible_articles[0] );
					unset( $invisible_articles[1] );
				}
			}

			// 4.3 list any articles that are NOT in other visible categories
			$invisible_articles_msg = '';
			foreach( $invisible_articles as $article_id => $article_title ) {
				if ( in_array( $article_id, $visible_articles) ) {
					continue;
				}
				$invisible_articles_msg .= '<li>' . $article_title . ' &nbsp;&nbsp;' . '<a href="' .  get_edit_post_link( $article_id ) . '" target="_blank">' . $i18_edit_word . '</a></li>';
			}
		}

		$i18_categories = '<strong>' . esc_html__( 'categories', 'echo-knowledge-base' ) . '</strong>';

		if ( ! empty($invisible_cat_msg) ) {
			/* translators: the first %s will be replaced with the word 'categories' (in bold) and the second %s will replaced with 'basic' or 'tabs' word (in bold) */
			$msg3 = sprintf( esc_html__( 'The following %s are nested too deeply to be visible with the selected %s layout:', 'echo-knowledge-base' ), $i18_categories, $current_layout );
			$message[] = '<div class="status_group"><p>' . $msg3 . '</p><ul>' . $invisible_cat_msg . '</ul><p>' .
			                 esc_html__( 'You can move the categories and/or switch layout.', 'echo-knowledge-base' ) . '</p></div>';
		}
		if ( ! empty($invisible_articles_msg) ) {

			/* translators: the first %s will be replaced with the word 'articles' (in bold) and the second %s will replaced with 'basic' or 'tabs' word (in bold) */
			$msg4 = sprintf( esc_html__( 'The following %s are assigned to categories not visible so they will not be visible with the selected %s layout:', 'echo-knowledge-base' ),
					$i18_articles, $current_layout );
			$message[] = '<div class="status_group"><p>' . $msg4 . '</p><ul>' . $invisible_articles_msg . '</ul>' .
			              '<p>' . esc_html__( 'You can either assign the article(s) to different categories and/or move categories.', 'echo-knowledge-base' ) . '</p></div>';
		}

		// 5. show empty categories; do not count categories containing other categories
		$empty_cat_msg = '';
		foreach( $stored_ids_obj->get_all_leafs() as $category_id ) {
			if ( isset($articles_seq_data[$category_id]) && count($articles_seq_data[$category_id]) < 3 ) {
				$empty_cat_msg .= '<li>' . $articles_seq_data[$category_id][0] . ' &nbsp;&nbsp;' . '<a href="' .
				                  get_edit_term_link( $category_id, EPKB_KB_Handler::get_category_taxonomy_name( $kb_id ), EPKB_KB_Handler::get_post_type( $kb_id) ) .
				                  '" target="_blank">' . $i18_edit_word. '</a></li>';
			}
		}
		if ( ! empty($empty_cat_msg) ) {
			/* translators: the first %s will be replaced with the word 'articles' (in bold) and the second %s will replaced with 'basic' or 'tabs' word (in bold) */
			$msg5 = sprintf( esc_html__( 'The following %s have no articles:', 'echo-knowledge-base' ), $i18_categories );
			$message[] = '<div class="status_group"><p>' . $msg5 . '</p><ul>' . $empty_cat_msg . '</ul></div>';
		}

		return $message;
	}


}
