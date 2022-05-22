<?php
/**
 * Week View Nav
 * This file loads the week view navigation.
 *
 * Override this template in your own theme by creating a file at [your-theme]/tribe-events/pro/week/nav.php
 *
 * @package TribeEventsCalendar
 */

// @codingStandardsIgnoreFile
// Allow plugin text domain in theme.

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
} ?>

<h3 class="tribe-events-visuallyhidden"><?php esc_html_e( 'Week Navigation', 'the-events-calendar' ); ?></h3>
<ul class="tribe-events-sub-nav nav-links">
	<li class="tribe-events-nav-previous">
		<?php
		// @codingStandardsIgnoreStart
		echo function_exists( 'tribe_events_week_previous_link' ) ? tribe_events_week_previous_link() : '';
		// @codingStandardsIgnoreEnd ?>
	</li><!-- .tribe-events-nav-previous -->
	<li class="tribe-events-nav-next">
		<?php
		// @codingStandardsIgnoreStart
		echo function_exists( 'tribe_events_week_next_link' ) ? tribe_events_week_next_link() : '';
		// @codingStandardsIgnoreEnd ?>
	</li><!-- .tribe-events-nav-next -->
</ul><!-- .tribe-events-sub-nav -->
