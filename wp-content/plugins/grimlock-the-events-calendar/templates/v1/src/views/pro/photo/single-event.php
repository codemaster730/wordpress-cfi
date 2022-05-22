<?php
/**
 * Photo View Single Event
 * This file contains one event in the photo view
 *
 * Override this template in your own theme by creating a file at [your-theme]/tribe-events/pro/photo/single-event.php
 *
 * @package TribeEventsCalendar
 * @version 4.4.8
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

global $post;

$venue = tribe_get_venue( get_the_ID() );
?>

<div class="card h-100">

	<?php
	// @codingStandardsIgnoreStart
	echo tribe_event_featured_image( null, 'large' );
	// @codingStandardsIgnoreEnd ?>

	<div class="card-body tribe-events-event-details tribe-clearfix">

		<?php if ( ! is_null( $venue ) ) : ?>
			<!-- Event labels -->
			<div class="card-body-labels entry-labels mb-2">
				<div class="tribe-venue badge badge-primary"><?php echo esc_html( tribe_get_venue() ); ?></div>
			</div>
		<?php endif; ?>

		<header class="card-body-header">

			<!-- Event Title -->
			<?php do_action( 'tribe_events_before_the_event_title' ); ?>
			<h2 class="card-body-title h4">
				<a class="tribe-event-url" href="<?php echo esc_url( tribe_get_event_link() ); ?>" title="<?php the_title(); ?>" rel="bookmark">
					<?php the_title(); ?>
				</a>
			</h2>
			<?php do_action( 'tribe_events_after_the_event_title' ); ?>

			<!-- Event Meta -->
			<?php do_action( 'tribe_events_before_the_meta' ); ?>
			<div class="card-body-meta">
				<div class="tribe-event-schedule-details">
					<?php
					// @codingStandardsIgnoreStart
					if ( function_exists( 'tribe_get_distance_with_unit' ) && ! empty( $post->distance ) ) : ?>
						<strong>[<?php echo tribe_get_distance_with_unit( $post->distance ); ?>]</strong>
					<?php endif; ?>
					<?php echo tribe_events_event_schedule_details();
					// @codingStandardsIgnoreEnd ?>
				</div>
			</div><!-- .card-body-meta -->
			<?php do_action( 'tribe_events_after_the_meta' ); ?>

		</header>

		<!-- Event Content -->
		<?php do_action( 'tribe_events_before_the_content' ); ?>
		<div class="tribe-events-list-photo-description tribe-events-content">
			<?php
			// @codingStandardsIgnoreStart
			echo tribe_events_get_the_excerpt();
			// @codingStandardsIgnoreEnd ?>
		</div>
		<?php do_action( 'tribe_events_after_the_content' ); ?>

	</div><!-- /.tribe-events-event-details -->

</div><!-- /.tribe-events-photo-event-wrap -->
