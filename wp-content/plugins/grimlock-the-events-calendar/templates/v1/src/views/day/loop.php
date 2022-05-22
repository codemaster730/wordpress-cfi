<?php
/**
 * Day View Loop
 * This file sets up the structure for the day loop
 *
 * Override this template in your own theme by creating a file at [your-theme]/tribe-events/loop.php
 *
 * @version 4.4
 * @package TribeEventsCalendar
 */

// @codingStandardsIgnoreFile
// Allow plugin text domain in theme and unescaped template tags.

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

global $more, $post, $wp_query;
$more             = false;
$current_timeslot = null;
?>

<div id="tribe-events-day" class="tribe-events-loop">

	<?php while ( have_posts() ) : the_post(); ?>
		<?php do_action( 'tribe_events_inside_before_loop' ); ?>
		<?php if ( $current_timeslot !== $post->timeslot ) :
			$current_timeslot = $post->timeslot; ?>
			<div class="tribe-events-day-time-slot">
				<span><?php echo esc_html( $current_timeslot ); ?></span>
			</div>
		<?php endif; ?>
		<!-- Event  -->
		<div id="post-<?php the_ID(); ?>" class="<?php tribe_events_event_classes(); ?> <?php if ( has_post_thumbnail() ) : ?>has-post-thumbnail<?php endif; ?>">
			<?php
			$event_type = tribe( 'tec.featured_events' )->is_featured( $post->ID ) ? 'featured' : 'event';

			/**
			 * Filters the event type used when selecting a template to render
			 *
			 * @param $event_type
			 */
			$event_type = apply_filters( 'tribe_events_day_view_event_type', $event_type );

			tribe_get_template_part( 'day/single', $event_type );
			?>
		</div>
		<?php do_action( 'tribe_events_inside_after_loop' ); ?>
	<?php endwhile; ?>
</div><!-- .tribe-events-loop -->
