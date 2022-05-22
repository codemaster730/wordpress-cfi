<?php
/*
 * Plugin name: Grimlock for The Events Calendar
 * Plugin URI:  http://www.themosaurus.com
 * Description: Adds integration features for Grimlock and The Events Calendar.
 * Author:      Themosaurus
 * Author URI:  http://www.themosaurus.com
 * Version:     1.2.8
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: grimlock-the-events-calendar
 * Domain Path: /languages
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'GRIMLOCK_THE_EVENTS_CALENDAR_VERSION',              '1.2.8' );
define( 'GRIMLOCK_THE_EVENTS_CALENDAR_MIN_GRIMLOCK_VERSION', '1.3.8' );
define( 'GRIMLOCK_THE_EVENTS_CALENDAR_PLUGIN_FILE',          __FILE__ );
define( 'GRIMLOCK_THE_EVENTS_CALENDAR_PLUGIN_DIR_PATH',      plugin_dir_path( __FILE__ ) );
define( 'GRIMLOCK_THE_EVENTS_CALENDAR_PLUGIN_DIR_URL',       plugin_dir_url( __FILE__ ) );

// Initialize update checker
require 'libs/plugin-update-checker/plugin-update-checker.php';
Puc_v4_Factory::buildUpdateChecker(
	'http://files.themosaurus.com/grimlock-the-events-calendar/version.json',
	__FILE__,
	'grimlock-the-events-calendar'
);

/**
 * Display notice if Grimlock version doesn't match minimum requirement
 */
function grimlock_the_events_calendar_dependency_notice() {
	$url = self_admin_url( 'update-core.php?action=do-plugin-upgrade&plugins=' ) . urlencode( plugin_basename( GRIMLOCK_PLUGIN_FILE ) );
	$url = wp_nonce_url( $url, 'upgrade-core' ); ?>
	<div class="notice notice-error">
		<p><?php printf( esc_html__( '%1$sGrimlock for The Events Calendar%2$s requires %1$sGrimlock %3$s%2$s to continue running properly. Please %4$supdate Grimlock.%5$s', 'grimlock-the-events-calendar' ), '<strong>', '</strong>', GRIMLOCK_THE_EVENTS_CALENDAR_MIN_GRIMLOCK_VERSION, '<a href="' . esc_url( $url ) . '">', "</a>" ); ?></p>
	</div>
	<?php
}

/**
 * Load plugin.
 */
function grimlock_the_events_calendar_loaded() {
	if ( version_compare( GRIMLOCK_VERSION, GRIMLOCK_THE_EVENTS_CALENDAR_MIN_GRIMLOCK_VERSION, '<' ) ) {
		add_action( 'admin_notices', 'grimlock_the_events_calendar_dependency_notice' );
		return;
	}

	if ( class_exists( 'Tribe__Events__Main' ) ) {
		require_once 'inc/class-grimlock-the-events-calendar.php';

		global $grimlock_the_events_calendar;
		$grimlock_the_events_calendar = new Grimlock_The_Events_Calendar();

		do_action( 'grimlock_the_events_calendar_loaded' );
	}
}
add_action( 'grimlock_loaded', 'grimlock_the_events_calendar_loaded' );

// Disable The Events Calendar Customizer features.
add_filter( 'tribe_customizer_is_active', '__return_false', 10, 1 );

/**
 * Force some TEC Display options for maximum compatibility
 *
 * Note: Unfortunately we can't move this hook to the main class because
 * it is too late in the page load for this filter to work properly.
 *
 * @param mixed $option_value Value of the option being called
 * @param string $option_name Name of the option being called
 *
 * @return mixed Potentially modified value of the option being called
 */
function grimlock_the_events_calendar_force_events_display_options( $option_value, $option_name ) {
	if ( $option_name === 'tribeEventsTemplate' ) {
		return '';
	}

	if ( $option_name === 'stylesheet_mode' || $option_name === 'stylesheetOption' ) {
		if ( function_exists( 'tribe_events_views_v2_is_enabled' ) && tribe_events_views_v2_is_enabled() ) {
			return 'skeleton';
		}
		else {
			return 'full';
		}
	}

	return $option_value;
}
add_filter( 'tribe_get_option', 'grimlock_the_events_calendar_force_events_display_options', 10, 2 );
