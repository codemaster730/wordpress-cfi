<?php
/**
 * Single Venue Template
 * The template for a venue. By default it displays venue information and lists
 * events that occur at the specified venue.
 *
 * This view contains the filters required to create an effective single venue view.
 *
 * You can recreate an ENTIRELY new single venue view by doing a template override, and placing
 * a single-venue.php file in a tribe-events/pro/ directory within your theme directory, which
 * will override the /views/pro/single-venue.php.
 *
 * You can use any or all filters included in this file or create your own filters in
 * your functions.php. In order to modify or extend a single filter, please see our
 * readme on templates hooks and filters (TO-DO)
 *
 * @package TribeEventsCalendarPro
 *
 * @version 4.3.2
 */

// @codingStandardsIgnoreFile
// Allow plugin text domain in theme and unescaped template tags.

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$venue_id     = get_the_ID();
$full_address = tribe_get_full_address();
$telephone    = tribe_get_phone();
$website_link = tribe_get_venue_website_link();
global $wp_query;
?>

<?php while ( have_posts() ) : the_post(); ?>
	<div class="tribe-events-venue">

		<div class="single-post-back tribe-events-back">
			<a href="<?php echo esc_url( tribe_get_events_link() ); ?>">
				<?php
				/* translators: %s: Events plural label */
				printf( esc_html__( 'Back to %s', 'the-events-calendar' ), tribe_get_event_label_plural() ); ?>
			</a>
			<span class="single-post-back__active"><?php the_title(); ?></span>
		</div>

		<div class="tribe-clearfix mb-4">

			<div class="card p-3 mb-4 card-single-venue">

				<div class="row align-items-center">
					<?php if ( has_post_thumbnail( $venue_id ) ) : ?>
						<div class="mb-2 mb-sm-0 col-sm-5 col-md-4">
							<?php
							$the_post_thumbnail_attr = array(
								'class' => 'rounded-card img-fluid d-inline-block',
							);
							echo get_the_post_thumbnail( $venue_id, 'large', $the_post_thumbnail_attr ); ?>
						</div>
					<?php else: ?>
						<div class="col-venue-image col-auto d-none d-md-flex">
							<div class="bg-gray-100 p-2 p-md-5 rounded-card d-flex align-items-center justify-content-center no-thumbnail"></div>
						</div>
					<?php endif; ?>
					<div class="col-sm-7 col-md-8">
						<?php do_action( 'tribe_events_single_venue_before_title' ); ?>
						<h2 class="tribe-venue-name"><?php echo tribe_get_venue( $venue_id ); ?></h2>
						<?php do_action( 'tribe_events_single_venue_after_title' ); ?>

						<!-- Venue Meta -->
						<?php do_action( 'tribe_events_single_venue_before_the_meta' ); ?>

						<div class="venue-address mt-1">

							<?php if ( $full_address ) : ?>
								<address class="tribe-events-address">
									<span class="location"><?php echo wp_kses_post( $full_address ); ?></span>
							<?php endif; ?>

							<?php if ( tribe_show_google_map_link() && tribe_address_exists() ) : ?>
								<?php echo tribe_get_map_link_html(); ?>
							<?php endif; ?>

							<?php if ( $full_address ) : ?>
								</address>
							<?php endif; ?>

							<?php if ( $telephone ) : ?>
								<div class="tel">
									<?php echo esc_html( $telephone ); ?>
								</div>
							<?php endif; ?>

							<?php if ( $website_link ) : ?>
								<div class="url">
									<?php echo wp_kses( $website_link, array(
										'a' => array(
											'href'   => true,
											'class'  => true,
											'target' => true,
										),
									) ); ?>
								</div>
							<?php endif; ?>

						</div><!-- .venue-address -->

						<?php do_action( 'tribe_events_single_venue_after_the_meta' ); ?>

					</div>
				</div>

			</div>

			<!-- Venue Description -->
			<?php if ( get_the_content() ) : ?>
				<div class="tribe-venue-description tribe-events-content">
					<?php the_content(); ?>
				</div>
			<?php endif; ?>

			<?php if ( tribe_embed_google_map() && tribe_address_exists() ) : ?>
				<div class="card p-3 mb-4 mt-3">
					<?php echo tribe_get_embedded_map( $venue_id, '100%', '300px' ); ?>
				</div>
			<?php endif; ?>

		</div><!-- .tribe-events-venue-meta -->

		<!-- Upcoming event list -->
		<?php do_action( 'tribe_events_single_venue_before_upcoming_events' ); ?>

		<?php
		if ( function_exists( 'tribe_venue_upcoming_events' ) ) :
			// Use the `tribe_events_single_venue_posts_per_page` to filter the number of events to get here.
			echo tribe_venue_upcoming_events( $venue_id, $wp_query->query_vars );
		endif; ?>

		<?php do_action( 'tribe_events_single_venue_after_upcoming_events' ); ?>

	</div><!-- .tribe-events-venue -->
	<?php
endwhile;
