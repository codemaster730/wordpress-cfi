<?php
/**
 * Photo View Nav
 * This file contains the photo view navigation.
 *
 * Override this template in your own theme by creating a file at [your-theme]/tribe-events/pro/photo/nav.php
 *
 * @package TribeEventsCalendar
 */

// @codingStandardsIgnoreFile
// Allow plugin text domain in theme.

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$events_label_plural = tribe_get_event_label_plural(); ?>

<h3 class="tribe-events-visuallyhidden"><?php
	/* translators: %s: Events plural label */
	printf( esc_html__( '%s List Navigation', 'the-events-calendar' ), esc_html( $events_label_plural ) ); ?>
</h3>

<ul class="tribe-events-sub-nav nav-links">
	<!-- Display Previous Page Navigation -->
	<?php if ( tribe_has_previous_event() ) : ?>
		<li class="tribe-events-nav-previous nav-previous">
			<a href="#" class="tribe_paged"><?php
				/* translators: %s: Events plural label */
				printf( esc_html__( 'Previous %s', 'the-events-calendar' ), esc_html( $events_label_plural ) ); ?></a>
		</li>
	<?php endif; ?>
	<!-- Display Next Page Navigation -->
	<?php if ( tribe_has_next_event() ) : ?>
		<li class="tribe-events-nav-next nav-next">
			<a href="#" class="tribe_paged"><?php
				/* translators: %s: Events plural label */
				printf( esc_html__( 'Next %s', 'the-events-calendar' ), esc_html( $events_label_plural ) ); ?></a>
		</li>
	<?php endif; ?>
</ul>
