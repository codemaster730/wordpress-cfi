<?php
/**
 * Grimlock_BuddyPress_Typography_Customizer Class
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
 * The typography class for the Customizer.
 */
class Grimlock_BuddyPress_Typography_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'grimlock_typography_customizer_text_color_elements',            array( $this, 'add_text_color_elements'            ), 10, 1 );
		add_filter( 'grimlock_typography_customizer_text_color_outputs',             array( $this, 'add_text_color_outputs'             ), 10, 1 );
		add_filter( 'grimlock_typography_customizer_heading_color_outputs',          array( $this, 'add_heading_color_outputs'          ), 10, 1 );
		add_filter( 'grimlock_typography_customizer_display_heading_font_elements',  array( $this, 'add_display_heading_font_elements'  ), 10, 1 );
		add_filter( 'grimlock_typography_customizer_heading_font_outputs',           array( $this, 'add_heading_font_outputs'           ), 10, 1 );
	}

	/**
	 * Add selectors and properties to the CSS rule-set for the heading color.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $outputs The array of CSS selectors and properties for the heading color.
	 *
	 * @return array          The updated array of CSS selectors for the heading color.
	 */
	public function add_heading_color_outputs( $outputs ) {
		return array_merge( $outputs, array(
			array(
				'element'  => implode( ',', array(
					'#groups-list-options a',
					'#friends-list-options a',
					'#members-list-options a',
					'#bp-group-rating-list-options a',
					'#bp-member-rating-list-options a',
					'ul#groups-list.item-list > li div.item .item-title a',
					'ul#members-list.item-list > li div.item .item-title a',
					'ul#friends-list.item-list > li div.item .item-title a',
					'ul#bp-group-rating.item-list > li div.item .item-title a',
					'ul.featured-members-list.item-list > li div.item .item-title a',
				) ),
				'property' => 'color',
			),
			array(
				'element'  => implode( ',', array(
					'#groups-list-options a',
					'#friends-list-options a',
					'#members-list-options a',
					'#bp-group-rating-list-options a',
					'#bp-member-rating-list-options a',
				) ),
				'property' => 'border-color',
			),
			array(
				'element'  => implode( ',', array(
					'#members-dir-map',
				) ),
				'property' => 'background-color',
			),
		) );
	}

	/**
	 * Add CSS selectors to the array of CSS selectors for the display heading font.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $elements The array of CSS selectors for the display heading font.
	 *
	 * @return array           The updated array of CSS selectors for the display heading font.
	 */
	public function add_display_heading_font_elements( $elements ) {
		return array_merge( $elements, array(
			'#buddypress:not(.youzer).bmf-white-popup header',
		) );
	}

	/**
	 * Add CSS selectors to the array of CSS selectors for the text color.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $elements The array of CSS selectors for the text color.
	 *
	 * @return array           The updated array of CSS selectors for the text color.
	 */
	public function add_text_color_elements( $elements ) {
		return array_merge( $elements, array(
			'.vex.vex-theme-flat-attack .vex-content',
			'#buddypress:not(.youzer).bmf-white-popup',
			'.buddypress .padder > #buddypress.buddypress-wrap',
		) );
	}

	/**
	 * Add selectors and properties to the CSS rule-set for the text color.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $outputs The array of CSS selectors and properties for the text color.
	 *
	 * @return array          The updated array of CSS selectors for the text color.
	 */
	public function add_text_color_outputs( $outputs ) {
		return array_merge( $outputs, array(
			array(
				'element'  => implode( ',', array(
					'#buddypress:not(.youzer) #activity-stream.grimlock-buddypress-activity-list .activity-item:before',
					'#buddypress:not(.youzer) #activity-stream.grimlock-buddypress-activity-list:before',
				) ),
				'property' => 'background-color',
			),
		) );
	}

	/**
	 * Add selectors and properties to the CSS rule-set for the heading font.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $outputs The array of CSS selectors and properties for the heading font.
	 *
	 * @return array          The updated array of CSS selectors for the heading font.
	 */
	public function add_heading_font_outputs( $outputs ) {

		$elements_headings = array(
			'.c100.hmk-percentage',
			'#buddypress:not(.youzer) #profile-content__nav ul li > a',
			'#buddypress:not(.youzer) #profile-content__nav .priority-nav__dropdown-toggle',
			'#buddypress:not(.youzer) div.item-list-tabs.primary-list-tabs > ul > li > a',
			'#buddypress:not(.youzer) div.item-list-tabs.bp-navs > ul > li > a',
			'#buddypress:not(.youzer) div.dir-search > form input[type="text"]',
			'#buddypress:not(.youzer) div.dir-search > form input[type="search"]',
			'#buddypress:not(.youzer) div.message-search > form input[type="text"]',
			'#buddypress:not(.youzer) div.message-search > form input[type="search"]',
			'.profile-header__body',
			'[class*="widget_recent_bp_docs"] ul li a',
			'#buddypress ul.item-list li div.item-title',
			'#buddypress ul.item-list li h3',
			'#buddypress ul.item-list li h4',
			'span#reply-title',
		);

		return array_merge( $outputs, array(
			array(
				'element'  => implode( ',', $elements_headings ),
				'property' => 'font-family',
				'choice'   => 'font-family',
			),
			array(
				'element'  => implode( ',', $elements_headings ),
				'property' => 'text-transform',
				'choice'   => 'text-transform',
			),
			array(
				'element'  => implode( ',', $elements_headings ),
				'property' => 'font-weight',
				'choice'   => 'font-weight',
			),
			array(
				'element'  => implode( ',', $elements_headings ),
				'property' => 'font-style',
				'choice'   => 'font-style',
			),
		) );
	}
}

return new Grimlock_BuddyPress_Typography_Customizer();
