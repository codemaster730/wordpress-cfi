<?php
/**
 * Cera_Grimlock_The_Events_Calendar_Single_Tribe_Events_Customizer Class
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
 * The Customizer class for the events single.
 */
class Cera_Grimlock_The_Events_Calendar_Single_Tribe_Events_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'grimlock_the_events_calendar_single_tribe_events_customizer_defaults', array( $this, 'change_defaults' ), 10, 1 );
	}

	/**
	 * Change the default values and control settings for the Customizer.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $defaults The array of default values for the Customizer controls.
	 *
	 * @return array          The updated array of default values for the Customizer controls.
	 */
	public function change_defaults( $defaults ) {
		$defaults['single_tribe_events_custom_header_displayed']        = false;
		$defaults['single_tribe_events_custom_header_layout']           = CERA_THE_EVENTS_CALENDAR_CUSTOM_HEADER_LAYOUT;
		$defaults['single_tribe_events_custom_header_container_layout'] = CERA_THE_EVENTS_CALENDAR_CUSTOM_HEADER_CONTAINER_LAYOUT;

		$defaults['single_tribe_events_custom_header_padding_y']        = CERA_THE_EVENTS_CALENDAR_CUSTOM_HEADER_PADDING_Y;
		$defaults['single_tribe_events_content_padding_y']              = CERA_THE_EVENTS_CALENDAR_CONTENT_PADDING_Y;

		$defaults['single_tribe_events_layout']                        = CERA_SINGLE_THE_EVENTS_CALENDAR_LAYOUT;
		$defaults['single_tribe_events_container_layout']              = CERA_THE_EVENTS_CALENDAR_CONTAINER_LAYOUT;
		return $defaults;
	}
}

return new Cera_Grimlock_The_Events_Calendar_Single_Tribe_Events_Customizer();
