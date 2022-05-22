<?php
/**
 * Cera_Grimlock_Search_Customizer Class
 *
 * @author   Themosaurus
 * @since    1.0.0
 * @package grimlock
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The search page class for the Customizer.
 */
class Cera_Grimlock_Search_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'grimlock_search_customizer_defaults',            array( $this, 'change_defaults'            ), 10, 1 );
		add_filter( 'grimlock_search_customizer_post_thumbnail_size', array( $this, 'change_post_thumbnail_size' ), 10, 2 );
	}

	/**
	 * Change default values and control settings for the Customizer.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $defaults The array of default values for the Customizer controls.
	 *
	 * @return array           The updated array of default values for the Customizer controls.
	 */
	public function change_defaults( $defaults ) {
		$defaults['search_layout']                       = '12-cols-left';
		$defaults['search_container_layout']             = 'classic';
		$defaults['search_custom_header_padding_y']      = CERA_HEADER_PADDING_Y;
		$defaults['search_content_padding_y']            = CERA_CONTENT_PADDING_Y;
		$defaults['search_posts_layout']                 = '4-4-4-cols-classic';
		$defaults['search_posts_height_equalized']       = false;
		$defaults['search_post_padding']                 = 30;
		$defaults['search_post_margin']                  = 15;
		$defaults['search_post_background_color']        = CERA_CARD_BACKGROUND;
		$defaults['search_post_border_radius']           = CERA_CARD_BORDER_RADIUS;
		$defaults['search_post_thumbnail_border_radius'] = CERA_CARD_BORDER_RADIUS;
		$defaults['search_post_border_width']            = CERA_CARD_BORDER_WIDTH;
		$defaults['search_post_border_color']            = CERA_CARD_BORDER_COLOR;
		$defaults['search_post_color']                   = CERA_CARD_COLOR;
		$defaults['search_post_title_color']             = CERA_CARD_TITLE_COLOR;
		$defaults['search_post_link_color']              = CERA_CARD_LINK_COLOR;
		$defaults['search_post_link_hover_color']        = CERA_CARD_LINK_HOVER_COLOR;
		$defaults['search_post_date_displayed']          = true;
		$defaults['search_post_author_displayed']        = true;
		$defaults['search_post_tag_displayed']           = true;
		$defaults['search_category_displayed']           = true;
		$defaults['search_post_format_displayed']        = true;
		$defaults['search_post_thumbnail_displayed']     = true;
		return $defaults;
	}

	/**
	 * Change default post thumbnail sizes for the archive.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $size         The size for the post thumbnail.
	 * @param  string $posts_layout The layout for the archive posts.
	 *
	 * @return string               The updated size for the post thumbnail.
	 */
	public function change_post_thumbnail_size( $size, $posts_layout ) {
		return "thumbnail-{$posts_layout}";
	}
}

return new Cera_Grimlock_Search_Customizer();
