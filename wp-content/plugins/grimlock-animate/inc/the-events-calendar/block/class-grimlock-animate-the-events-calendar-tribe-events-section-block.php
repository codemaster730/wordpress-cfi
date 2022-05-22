<?php
/**
 * Grimlock_Animate_The_Events_Calendar_Tribe_Events_Section_Block Class
 *
 * @author  Themosaurus
 * @package  grimlock-animate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The class that extends the Events Section block to add animation options
 */
class Grimlock_Animate_The_Events_Calendar_Tribe_Events_Section_Block extends Grimlock_Animate_Query_Section_Block {
	/**
	 * Grimlock_Animate_The_Events_Calendar_Tribe_Events_Section_Block constructor.
	 *
	 * @param string $id_base ID of the extended block
	 */
	public function __construct( $id_base = 'grimlock_the_events_calendar_tribe_events_section_block' ) {
		parent::__construct( $id_base );
	}
}

return new Grimlock_Animate_The_Events_Calendar_Tribe_Events_Section_Block();
