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

		$this->kb_config = $kb_config;
		$this->kb_id = $kb_config['id'];

		// category and article sequence
		if ( $is_builder_on && ! empty($article_seq) && ! empty($categories_seq) ) {
			$this->articles_seq_data = $article_seq;
			$this->category_seq_data = $categories_seq;
		} else {
			$this->category_seq_data = EPKB_Utilities::get_kb_option( $this->kb_id, EPKB_Categories_Admin::KB_CATEGORIES_SEQ_META, array(), true );
			$this->articles_seq_data = EPKB_Utilities::get_kb_option( $this->kb_id, EPKB_Articles_Admin::KB_ARTICLES_SEQ_META, array(), true );
		}

		// for WPML filter categories and articles given active language
		if ( EPKB_Utilities::is_wpml_enabled( $kb_config ) && ! isset($_POST['epkb-wizard-demo-data']) ) {
			$this->category_seq_data = EPKB_WPML::apply_category_language_filter( $this->category_seq_data );
			$this->articles_seq_data = EPKB_WPML::apply_article_language_filter( $this->articles_seq_data );
		}

		// articles with no categories - temporary add one
		if ( isset($this->articles_seq_data[0]) ) {
			$this->category_seq_data[0] = array();
		}

		$this->is_builder_on = $is_builder_on;
		if ( empty($this->category_seq_data) && empty($this->articles_seq_data) ) {
			echo __( 'This Knowledge Base has no categories and no articles.', 'echo-knowledge-base' );
			return;
		}

		// add theme name to Div for specific targeting
		$this->active_theme = 'active_theme_' . EPKB_Utilities::get_wp_option( 'stylesheet', 'unknown' );

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

		$class1 = $this->get_css_class( 'eckb-article-title' . ( $this->kb_config['section_article_underline'] == 'on' ? ', article_underline_effect' : '' ) );
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
        $link = empty($seq_no) || $seq_no < 2 ? $link : add_query_arg( 'seq_no', $seq_no, $link );
		$link = empty($link) || is_wp_error( $link ) ? '' : $link;  ?>

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
	 * Get category icons for layout based on if showing for preset or actual frontend.
	 * @return array
	 */
	protected function get_category_icons() {

		if ( $this->is_builder_on && ! empty($this->kb_config['wizard-icons']) ) {
			return $this->kb_config['wizard-icons'];
		}

		if ( EPKB_Utilities::get( 'epkb-editor-page-loaded' ) == '1' && isset($this->kb_config['theme_presets']) && $this->kb_config['theme_presets'] !== 'current' ) {
			$category_icons = EPKB_Icons::get_demo_category_icons( $this->kb_config, $this->kb_config['theme_presets'] );
			if ( ! empty($category_icons) ) {
				return $category_icons;
			}
		}

		return EPKB_KB_Config_Category::get_category_icons_option( $this->kb_config['id'] );
	}
}