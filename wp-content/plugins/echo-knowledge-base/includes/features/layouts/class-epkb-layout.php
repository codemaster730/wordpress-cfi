<?php

/**
 *
 * BASE THEME class that every theme should extend
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 */
abstract class EPKB_Layout {

	protected $kb_config;
	protected $kb_id;
	protected $category_seq_data;
	protected $articles_seq_data;
	protected $is_builder_on = false;
	protected $has_kb_categories = true;
	protected $active_theme = 'unknown';

	/**
	 * Show the KB Main page with list of categories and articles
	 *
	 * @param $kb_config
	 * @param bool $is_builder_on
	 * @param array $article_seq
	 * @param array $categories_seq
	 */
	public function display_kb_main_page( $kb_config, $is_builder_on=false, $article_seq=array(), $categories_seq=array() ) {

		// set initial data
		$this->kb_config = $kb_config;
		$this->kb_id = $kb_config['id'];
		$this->is_builder_on = $is_builder_on;

		// set category and article sequence
		if ( $is_builder_on && ! empty( $article_seq ) && ! empty( $categories_seq ) ) {
			$this->articles_seq_data = $article_seq;
			$this->category_seq_data = $categories_seq;
		} else {
			$this->category_seq_data = EPKB_Utilities::get_kb_option( $this->kb_id, EPKB_Categories_Admin::KB_CATEGORIES_SEQ_META, array(), true );
			$this->articles_seq_data = EPKB_Utilities::get_kb_option( $this->kb_id, EPKB_Articles_Admin::KB_ARTICLES_SEQ_META, array(), true );
		}

		// for WPML filter categories and articles given active language
		if ( EPKB_Utilities::is_wpml_enabled( $kb_config ) ) {
			$this->category_seq_data = EPKB_WPML::apply_category_language_filter( $this->category_seq_data );
			$this->articles_seq_data = EPKB_WPML::apply_article_language_filter( $this->articles_seq_data );
		}

		// check we have categories defined
		$this->has_kb_categories = $this->kb_has_categories();

		// articles with no categories - temporary add one
		if ( isset( $this->articles_seq_data[0] ) ) {
			$this->category_seq_data[0] = array();
		}

		$this->generate_kb_main_page();
	}

	/**
	 * Generate content of the KB main page
	 */
	protected abstract function generate_kb_main_page();

	/**
	 * Display a link to a KB article.
	 *
	 * @param $title
	 * @param $article_id
	 * @param string $link_other
	 * @param string $prefix
	 * @param string $seq_no
	 */
	public function single_article_link( $title , $article_id, $link_other='', $prefix='', $seq_no='' ) {

		if ( empty($article_id) ) {
			return;
		}

		$class1 = $this->get_css_class( 'eckb-article-title' .
										( $this->kb_config['sidebar_article_underline'] == 'on' ? ', article_underline_effect' : '' ) .
										( $this->kb_config['sidebar_article_active_bold'] == 'on' ? ', article_active_bold' : '' )
		);
		$style1 = $this->get_inline_style( 'color:: ' . $prefix . 'article_font_color' );
		$style2 = $this->get_inline_style( 'color:: ' . $prefix . 'article_icon_color' );

		// handle any add-on content
		if ( has_filter( 'eckb_single_article_filter' ) ) {
			$result = apply_filters('eckb_single_article_filter', $article_id, array( $this->kb_id, $title, $class1, $style1, $style2 ) );
			if ( ! empty($result) && $result === true ) {
				return;
			}
		}

		$link = get_permalink( $article_id );
		if ( ! has_filter( 'article_with_seq_no_in_url_enable' ) ) {
			$link = empty( $seq_no ) || $seq_no < 2 ? $link : add_query_arg( 'seq_no', $seq_no, $link );
			$link = empty( $link ) || is_wp_error( $link ) ? '' : $link;
		} ?>

		<a href="<?php echo esc_url( $link ); ?>" <?php echo $link_other; ?>>
			<span <?php echo $class1 . ' ' . $style1; ?> >
				<span class="eckb-article-title__icon ep_font_icon_document" <?php echo $style2; ?>></span>
				<span class="eckb-article-title__text"><?php echo esc_html( $title ); ?></span>
			</span>
		</a> <?php
	}

	/**
	 * Display a search form for core layouts
	 */
	public function get_search_form() {
		EPKB_KB_Search::get_search_form( $this->kb_config );
	}

	/**
	 * Output inline CSS style based on configuration.
	 *
	 * @param string $styles  A list of Configuration Setting styles
	 * @return string
	 */
	public function get_inline_style( $styles ) {
		return EPKB_Utilities::get_inline_style( $styles, $this->kb_config );
	}

	/**
	 * Output CSS classes based on configuration.
	 *
	 * @param $classes
	 * @return string
	 */
	public function get_css_class( $classes ) {
		return EPKB_Utilities::get_css_class( $classes, $this->kb_config );
	}

	/**
	 * Retrieve category icons.
	 * @return array|string|null
	 */
	protected function get_category_icons() {

		if ( EPKB_Utilities::get( 'epkb-editor-page-loaded' ) == '1' && isset($this->kb_config['theme_presets']) && $this->kb_config['theme_presets'] !== 'current' ) {
			$category_icons = EPKB_Icons::get_demo_category_icons( $this->kb_config, $this->kb_config['theme_presets'] );
			if ( ! empty($category_icons) ) {
				return $category_icons;
			}
		}

		return EPKB_KB_Config_Category::get_category_icons_option( $this->kb_config['id'] );
	}

	/**
	 * Detect whether the current KB has any category
	 *
	 * @return bool
	 */
	private function kb_has_categories() {

		// if non-empty categories sequence in DB then nothing to do
		if ( ! empty( $this->category_seq_data ) && is_array( $this->category_seq_data ) ) {
			return true;
		}

		// if no categories in the sequence then query DB directly; return if error
		$category_seq_data = EPKB_KB_Handler::get_refreshed_kb_categories( $this->kb_id, $this->category_seq_data );
		if ( $category_seq_data === null || ! is_array( $category_seq_data ) ) {
			return true;
		}

		// re-populate the class
		$this->category_seq_data = $category_seq_data;
		$this->articles_seq_data = EPKB_Utilities::get_kb_option( $this->kb_id, EPKB_Articles_Admin::KB_ARTICLES_SEQ_META, array(), true );

		// for WPML filter categories and articles given active language
		if ( EPKB_Utilities::is_wpml_enabled( $this->kb_config ) ) {
			$this->category_seq_data = EPKB_WPML::apply_category_language_filter( $this->category_seq_data );
			$this->articles_seq_data = EPKB_WPML::apply_article_language_filter( $this->articles_seq_data );
		}

		return ! empty( $this->category_seq_data );
	}

	/**
	 * Show message that KB does not have any categories
	 */
	public function show_categories_missing_message() {

		$kb_post_type = EPKB_KB_Handler::get_post_type( $this->kb_id );
		$kb_category_taxonomy_name = EPKB_KB_Handler::get_category_taxonomy_name( $this->kb_id );
		$manage_articles_url = admin_url( 'edit-tags.php?taxonomy=' . $kb_category_taxonomy_name . '&post_type=' . $kb_post_type );
		$import_url = EPKB_Utilities::is_export_import_enabled() ?
								admin_url( '/edit.php?post_type=' . EPKB_KB_Handler::get_post_type( $this->kb_id ) . '&page=ep'.'kb-kb-configuration#tools__import' )
								: 'https://www.echoknowledgebase.com/wordpress-plugin/kb-articles-import-export/';     ?>

		<section class="eckb-kb-no-content">   <?php

			// for users with at least Author access
			if ( current_user_can( EPKB_Admin_UI_Access::get_author_capability() ) ) {    ?>
				<h2 class="eckb-kb-no-content-title"><?php esc_html_e( 'You do not have any KB categories. What would you like to do?', 'echo-knowledge-base' ); ?></h2>  <?php

				// for users with at least Editor access
				if ( EPKB_Admin_UI_Access::is_user_access_to_context_allowed( 'admin_eckb_access_frontend_editor_write' ) ) {   ?>
					<div class="eckb-kb-no-content-body">
						<p><a id="eckb-kb-create-demo-data" class="eckb-kb-no-content-btn" href="#" data-id="<?php echo esc_attr( $this->kb_id ); ?>"><?php esc_html_e( 'Generate Demo Categories and Articles', 'echo-knowledge-base' ); ?></a></p>
						<p><a class="eckb-kb-no-content-btn" href="<?php echo esc_url( $manage_articles_url ); ?>" target="_blank"><?php esc_html_e( 'Create Categories', 'echo-knowledge-base' ); ?></a></p>
						<p><a class="eckb-kb-no-content-btn" href="<?php echo esc_url( $import_url ); ?>" target="_blank"><?php esc_html_e( 'Import Articles and Categories', 'echo-knowledge-base' ); ?></a></p>
					</div><?php

					EPKB_HTML_Forms::dialog_confirm_action( array(
						'id'                => 'epkb-created-kb-content',
						'title'             => 'Notice',
						'body'              => 'Demo categories and articles were created. The page will reload.',
						'accept_label'      => 'Ok',
						'accept_type'       => 'primary',
						'show_cancel_btn'   => 'no',
						'show_close_btn'    => 'no',
					) );

				}   ?>

				<div class="eckb-kb-no-content-footer">
					<p><?php esc_html_e( 'Ensure all articles are assigned to categories.', 'echo-knowledge-base' ); ?></p>
					<p>
						<span><?php esc_html_e( 'If you need help, please contact us', 'echo-knowledge-base' ); ?></span>
						<a href="https://www.echoknowledgebase.com/technical-support/" target="_blank"> <?php esc_html_e( 'here', 'echo-knowledge-base' ); ?></a>
					</p>
				</div>  <?php

			// for other users
			} else {    ?>
				<h2 class="eckb-kb-no-content-title"><?php echo esc_html( $this->kb_config['category_empty_msg'] ); ?></h2>     <?php
			}   ?>

		</section>      <?php
	}
}