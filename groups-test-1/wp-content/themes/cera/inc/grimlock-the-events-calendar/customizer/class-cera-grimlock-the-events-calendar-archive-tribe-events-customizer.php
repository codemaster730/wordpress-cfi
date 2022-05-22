<?php
/**
 * Cera_Grimlock_The_Events_Calendar_Archive_Tribe_Events_Customizer Class
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
 * The Customizer class for the events archive.
 */
class Cera_Grimlock_The_Events_Calendar_Archive_Tribe_Events_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'grimlock_the_events_calendar_archive_tribe_events_customizer_defaults', array( $this, 'change_defaults' ), 10, 1 );
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
		$defaults['the_events_calendar_title']                          = CERA_THE_EVENTS_CALENDAR_TITLE;
		$defaults['the_events_calendar_description']                    = CERA_THE_EVENTS_CALENDAR_DESCRIPTION;
		$defaults['the_events_calendar_custom_header_displayed']        = CERA_THE_EVENTS_CALENDAR_CUSTOM_HEADER_DISPLAYED;
		$defaults['the_events_calendar_custom_header_layout']           = CERA_THE_EVENTS_CALENDAR_CUSTOM_HEADER_LAYOUT;
		$defaults['the_events_calendar_custom_header_container_layout'] = CERA_THE_EVENTS_CALENDAR_CUSTOM_HEADER_CONTAINER_LAYOUT;
		$defaults['the_events_calendar_custom_header_background_image'] = CERA_THE_EVENTS_CALENDAR_CUSTOM_HEADER_BACKGROUND_IMAGE;
		$defaults['the_events_calendar_custom_header_padding_y']        = CERA_THE_EVENTS_CALENDAR_CUSTOM_HEADER_PADDING_Y;
		$defaults['the_events_calendar_content_padding_y']              = CERA_THE_EVENTS_CALENDAR_CONTENT_PADDING_Y;

		$defaults['the_events_calendar_layout']                         = CERA_ARCHIVE_THE_EVENTS_CALENDAR_LAYOUT;
		$defaults['the_events_calendar_container_layout']               = CERA_THE_EVENTS_CALENDAR_CONTAINER_LAYOUT;
		return $defaults;
	}
}

return new Cera_Grimlock_The_Events_Calendar_Archive_Tribe_Events_Customizer();
