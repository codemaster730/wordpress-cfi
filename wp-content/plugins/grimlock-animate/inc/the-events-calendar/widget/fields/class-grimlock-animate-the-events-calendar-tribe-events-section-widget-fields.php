<?php
/**
 * Grimlock_Animate_The_Events_Calendar_Tribe_Events_Section_Widget_Fields Class
 *
 * @author  Themosaurus
 * @since   1.0.0
 * @package  grimlock
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The class that extends WP_Widget to create a section widget.
 *
 * @see WP_Widget
 */
class Grimlock_Animate_The_Events_Calendar_Tribe_Events_Section_Widget_Fields extends Grimlock_Animate_Query_Section_Widget_Fields {
	/**
	 * Sets up a new Section widget instance.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct( $id_base = 'grimlock_the_events_calendar_tribe_events_section_widget' ) {
		parent::__construct( $id_base );
	}
}

return new Grimlock_Animate_The_Events_Calendar_Tribe_Events_Section_Widget_Fields();
