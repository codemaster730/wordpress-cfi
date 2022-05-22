<?php
/**
 * Grimlock_The_Events_Calendar_Table_Customizer Class
 *
 * @author  Themosaurus
 * @since   1.0.0
 * @package grimlock
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The Grimlock WooCommerce Customizer style class.
 */
class Grimlock_The_Events_Calendar_Table_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'grimlock_table_customizer_striped_background_color_elements', array( $this, 'add_striped_background_color_elements' ), 10, 1 );
		add_filter( 'grimlock_table_customizer_striped_background_color_outputs',  array( $this, 'add_striped_background_color_outputs'  ), 10, 1 );
	}

	/**
	 * @param $elements
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function add_striped_background_color_elements( $elements ) {
		return array_merge( $elements, array(
			'.tribe-grid-header',
			'.tribe-events-grid',
			'#tribe-bar-form #tribe-bar-collapse-toggle',
			'#tribe-events-content table.tribe-events-calendar th',
			'#tribe-events-content table.tribe-events-calendar td:hover',
			'#tribe-events-content table.tribe-events-calendar div[id*=tribe-events-daynum-]',
			'#tribe-events-content table.tribe-events-calendar div[id*=tribe-events-daynum-] a',
			'#tribe-events-content table.tribe-events-calendar td.tribe-events-present div[id*=tribe-events-daynum-] > a',
			'#tribe-events-content table.tribe-events-calendar div[id*=tribe-events-event-]:hover',
			'.tribe-events-list .tribe-events-loop .tribe-event-featured .tribe-events-event-cost .ticket-cost',
			'.tribe-events-list .tribe-events-loop .type-tribe_events .tribe-events-event-cost span',
			'#tribe-bar-views .tribe-bar-views-list .tribe-bar-views-option a:hover',
			'#tribe-bar-views .tribe-bar-views-list .tribe-bar-views-option.tribe-bar-active a:hover',
			'.tribe-events-style-full #tribe-events-content form.cart .tribe-events-tickets .tribe-event-tickets-plus-meta-attendee',
			'.tribe-events-grid .tribe-grid-content-wrap .column',
			'.tribe-grid-allday .tribe-events-week-allday-single',
			'#tribe-bar-views-toggle:focus, #tribe-bar-views-toggle:hover',
			'.tribe-link-view-attendee',
			'.tribe-attendees-list-container',
			'.tribe-tickets-meta-row .tribe-tickets-attendees',
			'.tribe-tickets-attendees-list-optout',
			'#tribe-attendees-summary',
			'.tribe-scrollable-table',
		) );
	}

	/**
	 * Add selectors and properties to the CSS rule-set for the striped table row background color.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $outputs The array of CSS selectors and properties for the striped table row background color.
	 *
	 * @return array          The updated array of CSS selectors for the striped table row background color.
	 */
	public function add_striped_background_color_outputs( $outputs ) {
		return array_merge( $outputs, array(
			array(
				'element'  => implode( ',', array(
					'#tribe-events-content table.tribe-events-calendar td',
					'#tribe-events-content table.tribe-events-calendar',
					'.tribe-events-grid',
					'.tribe-grid-allday .tribe-events-week-allday-single',
					'.tribe-community-events-list',
				) ),
				'property' => 'border-color',
			),
			array(
				'element'  => implode( ',', array(
					'#tribe-events-content table.tribe-events-calendar div[id*=tribe-events-event-]',
					'.tribe-week-grid-block div',
					'.tribe-grid-allday',
					'.tribe-grid-header',
					'#tribe-bar-form input[type="text"]',
					'.tribe-community-events-list th',
					'.tribe-community-events-list td',
				) ),
				'property' => 'border-bottom-color',
			),
			array(
				'element'  => implode( ',', array(
					'.tribe-week-grid-block div',
				) ),
				'property' => 'border-top-color',
			),
			array(
				'element'  => implode( ',', array(
					'.tribe-events-grid .tribe-grid-content-wrap .column',
				) ),
				'property' => 'border-left-color',
			),
		) );
	}
}

return new Grimlock_The_Events_Calendar_Table_Customizer();
