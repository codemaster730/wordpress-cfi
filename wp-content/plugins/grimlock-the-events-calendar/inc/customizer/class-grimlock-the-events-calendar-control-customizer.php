<?php
/**
 * Grimlock_The_Events_Calendar_Control_Customizer Class
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
 * The control class for the Customizer.
 */
class Grimlock_The_Events_Calendar_Control_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'grimlock_control_customizer_elements',                    array( $this, 'add_elements'                    ), 10, 1 );
		add_filter( 'grimlock_control_customizer_border_color_outputs',        array( $this, 'add_border_color_outputs'        ), 10, 1 );
	}

	/**
	 * Add CSS selectors to the array of CSS selectors for the controls.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $elements The array of CSS selectors for the controls.
	 *
	 * @return array           The updated array of CSS selectors for the controls.
	 */
	public function add_elements( $elements ) {
		return array_merge( $elements, array(
			// TODO: Migrate to Events Tickets/Community SCSS
			'.tribe-community-events input[type=color]',
			'.tribe-community-events input[type=date]',
			'.tribe-community-events input[type=datetime-local]',
			'.tribe-community-events input[type=datetime]',
			'.tribe-community-events input[type=email]',
			'.tribe-community-events input[type=month]',
			'.tribe-community-events input[type=number]',
			'.tribe-community-events input[type=password]',
			'.tribe-community-events input[type=search]',
			'.tribe-community-events input[type=tel]',
			'.tribe-community-events input[type=text]',
			'.tribe-community-events input[type=time]',
			'.tribe-community-events input[type=url]',
			'.tribe-community-events input[type=week]',
			'.tribe-community-events textarea',
			'.tribe-community-events-content .tribe-event-list-search input[type=search]',
			'.eventForm input[type=color]',
			'.eventForm input[type=date]',
			'.eventForm input[type=datetime-local]',
			'.eventForm input[type=datetime]',
			'.eventForm input[type=email]',
			'.eventForm input[type=month]',
			'.eventForm input[type=number]',
			'.eventForm input[type=password]',
			'.eventForm input[type=search]',
			'.eventForm input[type=tel]',
			'.eventForm input[type=text]',
			'.eventForm input[type=time]',
			'.eventForm input[type=url]',
			'.eventForm input[type=week]',
			'.tribe-dropdown.select2-container-multi .select2-choices',
			'.tribe-ea-dropdown.select2-container-multi .select2-choices',
			'.tribe-events-tickets td.quantity input[type="number"]',
			'.tribe-events-tickets td.woocommerce input[type="number"]',
			'.tribe-events-tickets input[type="date"]',
			'.tribe-events-tickets input[type="time"]',
			'.tribe-events-tickets input[type="datetime-local"]',
			'.tribe-events-tickets input[type="week"]',
			'.tribe-events-tickets input[type="month"]',
			'.tribe-events-tickets input[type="text"]',
			'.tribe-events-tickets input[type="email"]',
			'.tribe-events-tickets input[type="url"]',
			'.tribe-events-tickets input[type="password"]',
			'.tribe-events-tickets input[type="search"]',
			'.tribe-events-tickets input[type="tel"]',
			'.tribe-events-tickets input[type="number"]',
			'.tribe-events-tickets textarea',
			'.tribe-events-tickets select',
		) );
	}

	/**
	 * Add selectors and properties to the CSS rule-set for the content background color.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $outputs The array of CSS selectors and properties for the content background color.
	 *
	 * @return array          The updated array of CSS selectors for the content background color.
	 */
	public function add_border_color_outputs( $outputs ) {
		return array_merge( $outputs, array(
			array(
				'element'  => implode( ',', array(
					// TODO: Migrate to Events Tickets/Community SCSS
					'.tribe-community-events .tribe-section',
				) ),
				'property' => 'border-color',
				'suffix'   => '!important',
			),
			array(
				'element'  => implode( ',', array(
					// TODO: Migrate to Events Tickets/Community SCSS
					'.tribe-community-events .tribe-section .tribe-section-header',
				) ),
				'property' => 'border-bottom-color',
			),
		) );
	}
}

return new Grimlock_The_Events_Calendar_Control_Customizer();
