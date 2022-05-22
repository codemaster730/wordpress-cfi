<?php

/**
 * Shortcode - Lists all KB articles and groups them by Letter, just like an index page.
 *
 * @copyright   Copyright (c) 2018, Echo Plugins
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_Articles_Index_Shortcode {

    const SHORTCODE_NAME = 'epkb-articles-index-directory';

	public function __construct() {
		add_shortcode( self::SHORTCODE_NAME, array( $this, 'output_shortcode' ) );
	}

	public static function get_embed_code( $kb_id=1 ) {
		$shortcode_param = $kb_id == EPKB_KB_Config_DB::DEFAULT_KB_ID ? '' : ' kb_id=' . $kb_id;
		return '[' . self::SHORTCODE_NAME . $shortcode_param . ']';
	}

	public function output_shortcode( $attributes ) {
		global $eckb_kb_id;

		epkb_load_public_resources_enqueue();

		// allows to adjust the widget title
		$title = empty($attributes['title']) ? '' : strip_tags( trim($attributes['title']) );
		$title = ( empty( $title ) ? esc_html__( 'Indexed Articles', 'echo-knowledge-base' ) : esc_html( $title ) );

		// get add-on configuration
		$kb_id = empty( $attributes['kb_id'] ) ? ( empty( $eckb_kb_id ) ? EPKB_KB_Config_DB::DEFAULT_KB_ID : $eckb_kb_id ) : $attributes['kb_id'];
		$kb_id = EPKB_Utilities::sanitize_int( $kb_id, EPKB_KB_Config_DB::DEFAULT_KB_ID );

		$indexed_articles_list = $this->get_indexed_articles_list( $kb_id );

		if ( empty( $indexed_articles_list ) ) {
			ob_start();
			echo esc_html__( 'Articles coming Soon', 'echo-knowledge-base' );
			return ob_get_clean();
		}

		// DISPLAY INDEXED ARTICLES // TODO improve html, add correct classes and ids
		ob_start(); ?>
		<div id="epkb-article-index-dir-container">

            <div class="epkb-aid__header-container">
                <h2 class="epkb-aid__header__title" aria-label="<?php echo esc_html( $title ); ?>"><?php echo esc_html( $title ); ?></h2>
            </div>

            <div class="epkb-aid__body-container">
                <?php foreach ( $indexed_articles_list as $indexed_result ) { ?>

                    <section id="epkb-aid__section-<?php echo esc_html( $indexed_result['index'] ); ?>" class="epkb-aid__section-container"
                             role="contentinfo" aria-label="Article List for Letter <?php echo esc_html( $indexed_result['index'] ); ?>">

                        <div class="epkb-aid-section__header">
                            <div class="epkb-aid-section__header__title"><?php echo esc_html( $indexed_result['index'] ); ?></div>
                        </div>

                        <div class="epkb-aid-section__body">
                            <ul class="epkb-aid-section__body__list-container">  <?php
                                foreach ( $indexed_result['articles'] as $article_id => $article_title ) {
                                    $article_url = get_permalink( $article_id );
                                    if ( empty( $article_url ) || is_wp_error( $article_url ) ) {
                                        continue;
                                    }  ?>
                                    <li id="epkb-aid-article-<?php echo esc_html( $article_id ); ?>" class="epkb-aid-list__item">
                                        <a href="<?php echo esc_url( $article_url ); ?>">
                                            <span class="epkb-aid-list__item__icon">
                                                <span aria-hidden="true" class="epkbfa epkb-aid-article-icon ep_font_icon_document"></span>
                                            </span>
                                            <span class="epkb-aid-list__item__text"><?php echo esc_html( $article_title ); ?></span>
                                        </a>
                                    </li>  <?php
                                } ?>
                            </ul>
                        </div>

                    </section>

                <?php } ?>
            </div>

		</div>  <?php
		return ob_get_clean();
	}

	/**
	 * Get sorted and indexed KB articles
	 *
	 * @param $kb_id
	 *
	 * @return array
	 */
	private function get_indexed_articles_list( $kb_id ) {

		// name for non-alphabetic indexes
		$other_index_char = __( 'Other', 'echo-knowledge-base' );

		$articles_list = $this->get_articles_list( $kb_id );

		asort( $articles_list, SORT_FLAG_CASE | SORT_STRING );

		$indexed_articles_list = array();

		foreach ( $articles_list as $article_id => $article ) {

			// get first char
			$index_char = mb_strtoupper( mb_substr( trim( $article ), 0, 1 ) );
			if ( empty( $index_char ) ) {
				continue;
			}

			// check if alphabet letter
			$is_letter = preg_match( '/[\p{L}]/u', $index_char );
			if ( empty( $is_letter ) ) {
				$index_char = $other_index_char;
			}

			$index_key = array_search( $index_char, array_column( $indexed_articles_list , 'index') );
			if ( false === $index_key ) {
				$indexed_articles_list[] = array(
					'index'    => $index_char,
					'articles' => array()
				);
				$index_key = array_key_last( $indexed_articles_list );
			}

			$indexed_articles_list[$index_key]['articles'][$article_id] = $article;
		}

		// move 'Other' to the end
		$other_index_key = array_search( $other_index_char, array_column( $indexed_articles_list , 'index') );
        if ( false !== $other_index_key ) {
	        $indexed_articles_list[] = $indexed_articles_list[$other_index_key];
	        unset( $indexed_articles_list[$other_index_key] );
        }

		return $indexed_articles_list;
	}

	/**
	 * Get all KB articles
	 *
	 * @param $kb_id
	 *
	 * @return array
	 */
	private function get_articles_list( $kb_id ) {

		$articles_seq_data = EPKB_Utilities::get_kb_option( $kb_id, EPKB_Articles_Admin::KB_ARTICLES_SEQ_META, array(), true );

		$articles_list = array();
		foreach ( $articles_seq_data as $category_id => $category_articles ) {
			foreach ( $category_articles as $post_id => $article ) {
				if ( $post_id > 1 && ! empty( $article ) ) {
					$articles_list[$post_id] = $article;
				}
			}
		}

		return $articles_list;
	}

}
