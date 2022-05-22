<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class Grimlock_BuddyPress_BP_Activity_Shortcode
 *
 * @author  themoasaurus
 * @since   1.4.0
 * @package grimlock-buddypress
 */
class Grimlock_BuddyPress_BP_Activity_Shortcode {
	/**
	 * Setup class.
	 *
	 * @since 1.4.0
	 */
	public function __construct() {
		add_filter( 'bpas_activity_list_classes', array( $this, 'add_activity_list_classes' ) );
	}

	/**
	 * Add a custom class to the activity list classes in BP Activity Shortcode
	 *
	 * @param string $classes The activity list classes
	 *
	 * @return string The activity list classes
	 */
	public function add_activity_list_classes( $classes ) {
		$classes .= ' grimlock-buddypress-activity-list';
		return $classes;
	}
}
