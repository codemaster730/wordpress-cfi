<?php
/**
 * Related Events Template
 * The template for displaying related events on the single event page.
 *
 * You can recreate an ENTIRELY new related events view by doing a template override, and placing
 * a related-events.php file in a tribe-events/pro/ directory within your theme directory, which
 * will override the /views/pro/related-events.php.
 *
 * You can use any or all filters included in this file or create your own filters in
 * your functions.php. In order to modify or extend a single filter, please see our
 * readme on templates hooks and filters
 *
 * @package TribeEventsCalendarPro
 */

// @codingStandardsIgnoreFile
// Allow plugin text domain in theme and unescaped template tags.

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$posts = function_exists( 'tribe_get_related_posts' ) ? tribe_get_related_posts() : array();

if ( is_array( $posts ) && ! empty( $posts ) ) : ?>

	<h3 class="mb-3 mt-4">
		<?php
		/* translators: %s: Events plural label */
		printf( esc_html__( 'Related %s', 'the-events-calendar' ), tribe_get_event_label_plural() ); ?>
	</h3>

	<div class="tribe-events-list tribe-events-photo">

		<ul class="tribe-related-events tribe-clearfix tribe-events-loop mt-2 mb-0" id="tribe-events-photo-events">
			<?php foreach ( $posts as $post ) : ?>
				<li class="type-tribe_events pt-0 mb-4 border-0">
					<div class="card h-100">
						<?php $thumb = ( has_post_thumbnail( $post->ID ) ) ? get_the_post_thumbnail( $post->ID, 'thumbnail-4-4-4-cols-classic' ) : '<img src="' . esc_url( trailingslashit( Tribe__Events__Pro__Main::instance()->pluginUrl ) . 'src/resources/images/tribe-related-events-placeholder.png' ) . '" alt="' . esc_attr( get_the_title( $post->ID ) ) . '" />'; ?>
						<div class="tribe-related-events-thumbnail">
							<a href="<?php echo esc_url( tribe_get_event_link( $post ) ); ?>" class="url" rel="bookmark"><?php echo wp_kses_post( $thumb ); ?></a>
						</div>
						<div class="card-body tribe-events-event-details tribe-clearfix">
							<div class="card-body-header">
								<h2 class="card-body-title h5"><a href="<?php echo tribe_get_event_link( $post ); ?>" class="tribe-event-url" rel="bookmark"><?php echo get_the_title( $post->ID ); ?></a></h2>
								<div class="card-body-meta mb-0 small">
									<div class="tribe-event-schedule-details">
										<?php
										if ( Tribe__Events__Main::POSTTYPE === $post->post_type ) :
											echo tribe_events_event_schedule_details( $post );
										endif; ?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>

	</div>
	<?php
endif;
