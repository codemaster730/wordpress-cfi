<?php
/**
 * Cera_Grimlock_The_Events_Calendar Class
 *
 * @package  cera
 * @author   Themosaurus
 * @since    1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The Cera Grimlock integration class for The Events Calendar
 */
class Cera_Grimlock_The_Events_Calendar {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		require_once get_template_directory() . '/inc/grimlock-the-events-calendar/customizer/class-cera-grimlock-the-events-calendar-archive-tribe-events-customizer.php';
		require_once get_template_directory() . '/inc/grimlock-the-events-calendar/customizer/class-cera-grimlock-the-events-calendar-single-tribe-events-customizer.php';
		require_once get_template_directory() . '/inc/grimlock-the-events-calendar/customizer/class-cera-grimlock-the-events-calendar-single-tribe-venue-customizer.php';
		require_once get_template_directory() . '/inc/grimlock-the-events-calendar/customizer/class-cera-grimlock-the-events-calendar-single-tribe-organizer-customizer.php';
	}
}

return new Cera_Grimlock_The_Events_Calendar();
