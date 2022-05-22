<?php
/**
 * Grimlock_The_Events_Calendar_Typography_Customizer Class
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
class Grimlock_The_Events_Calendar_Typography_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'grimlock_typography_customizer_heading_font_outputs',  array( $this, 'add_heading_font_outputs'  ), 10, 1 );
		add_filter( 'grimlock_typography_customizer_link_color_elements',   array( $this, 'add_link_color_elements'   ), 10, 1 );
	}

	/**
	 * Add CSS selectors to the array of CSS selectors for the link color.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $elements The array of CSS selectors for the link color.
	 *
	 * @return array           The updated array of CSS selectors for the link color.
	 */
	public function add_link_color_elements( $elements ) {
		return array_merge( $elements, array(
			// TODO: Migrate to Events Tickets/Community SCSS
			'.tribe-community-events-list .edit a:active',
			'.tribe-community-events-list .edit a:hover',
			'.tribe-community-events-list .edit a:visited',
			'.tribe-community-events-list .view a:active',
			'.tribe-community-events-list .view a:hover',
			'.tribe-community-events-list .view a:visited',
			'.tribe-community-events-list td .row-actions a:hover',
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
			// TODO: Migrate to Events Tickets/Community SCSS
			'.tribe-events-tickets header',
			'.tribe-rsvp-list > li.tribe-item .list-attendee',
			'.tribe-tickets-list > li.tribe-item .list-attendee',
			'.tribe-events-style-full #tribe-events h1',
			'.tribe-events-style-full #tribe-events h2',
			'.tribe-events-style-full #tribe-events h3',
			'.tribe-events-style-full #tribe-events h4',
			'.tribe-events-style-full #tribe-events h5',
			'.tribe-events-style-full #tribe-events h6',
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

return new Grimlock_The_Events_Calendar_Typography_Customizer();
