<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Grimlock_Animate_The_Events_Calendar
 *
 * @author  Themosaurus
 * @since   1.0.3
 * @package grimlock-animate
 */
class Grimlock_Animate_The_Events_Calendar {
	/**
	 * Setup class.
	 *
	 * @since 1.0.3
	 */
	public function __construct() {
		// Initialize widgets
		require_once GRIMLOCK_ANIMATE_PLUGIN_DIR_PATH . 'inc/the-events-calendar/widget/fields/class-grimlock-animate-the-events-calendar-tribe-events-section-widget-fields.php';

		// Initialize blocks
		require_once GRIMLOCK_ANIMATE_PLUGIN_DIR_PATH . 'inc/the-events-calendar/block/class-grimlock-animate-the-events-calendar-tribe-events-section-block.php';
	}
}