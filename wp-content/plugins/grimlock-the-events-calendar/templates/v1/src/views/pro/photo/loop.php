<?php
/**
 * Photo View Loop
 * This file sets up the structure for the photo view events loop
 *
 * Override this template in your own theme by creating a file at [your-theme]/tribe-events/pro/photo/loop.php
 *
 * @version 4.4.2
 * @package TribeEventsCalendar
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

// @codingStandardsIgnoreStart
global $more;
$more = false;
// @codingStandardsIgnoreEnd ?>

<div class="tribe-events-loop tribe-clearfix" id="tribe-events-photo-events">

	<div class="row">
		<?php while ( have_posts() ) : the_post(); ?>

			<?php do_action( 'tribe_events_inside_before_loop' ); ?>

			<!-- Event  -->
			<div class="col-xl-4 col-sm-6 col-12 mb-gutter-width">
				<div id="post-<?php the_ID(); ?>" class="h-100 <?php if ( has_post_thumbnail() ) : ?>has-post-thumbnail<?php endif; ?>">
					<?php tribe_get_template_part( 'pro/photo/single', 'event' ); ?>
				</div>
			</div>

			<?php do_action( 'tribe_events_inside_after_loop' ); ?>

		<?php endwhile; ?>
	</div>
</div><!-- .tribe-events-loop -->
