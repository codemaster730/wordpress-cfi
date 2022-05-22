<?php
/**
 * Single Organizer Template
 * The template for an organizer. By default it displays organizer information and lists
 * events that occur with the specified organizer.
 *
 * This view contains the filters required to create an effective single organizer view.
 *
 * You can recreate an ENTIRELY new single organizer view by doing a template override, and placing
 * a Single_Organizer.php file in a tribe-events/pro/ directory within your theme directory, which
 * will override the /views/pro/single_organizer.php.
 *
 * You can use any or all filters included in this file or create your own filters in
 * your functions.php. In order to modify or extend a single filter, please see our
 * readme on templates hooks and filters (TO-DO)
 *
 * @package TribeEventsCalendarPro
 *
 * @version 4.3
 */

// @codingStandardsIgnoreFile
// Allow plugin text domain in theme and unescaped template tags.

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$organizer_id = get_the_ID();
?>

<?php while ( have_posts() ) : the_post(); ?>
	<div class="tribe-events-organizer">

	<div class="single-post-back tribe-events-back">
		<a href="<?php echo esc_url( tribe_get_events_link() ); ?>">
			<?php
			/* translators: %s: Events plural label */
			printf( esc_html__( 'Back to %s', 'the-events-calendar' ), tribe_get_event_label_plural() ); ?>
		</a>
		<span class="single-post-back__active"><?php the_title(); ?></span>
	</div>

	<?php do_action( 'tribe_events_single_organizer_before_organizer' ); ?>

	<div class="tribe-clearfix">

		<div class="card p-3 mb-4 card-single-organizer">

			<div class="d-flex mb-2 align-items-center">
				<div class="col-organizer-image mr-3">
					<?php if ( has_post_thumbnail( $organizer_id ) ) : ?>
						<?php
						$post_thumbnail_attr = array(
							'class' => 'img-fluid rounded-circle d-inline-block',
						);
						echo get_the_post_thumbnail( $organizer_id, array( 80, 80 ), $post_thumbnail_attr ); ?>
					<?php else : ?>
						<img class="img-fluid rounded-circle d-inline-block" width="80" height="80" src="<?php echo esc_url( apply_filters( 'grimlock_the_events_calendar_organizer_default_avatar', GRIMLOCK_THE_EVENTS_CALENDAR_PLUGIN_DIR_URL . 'assets/images/default-avatar.png' ) ); ?>" alt=" <?php esc_html_e( 'avatar', 'the-events-calendar' ); ?>" />
					<?php endif; ?>
				</div>
				<div>
					<!-- Organizer Title -->
					<?php do_action( 'tribe_events_single_organizer_before_title' ); ?>
					<h3 class="tribe-organizer-name"><?php echo tribe_get_organizer( $organizer_id ); ?></h3>
					<?php do_action( 'tribe_events_single_organizer_after_title' ); ?>
					<!-- Organizer Meta -->
					<?php do_action( 'tribe_events_single_organizer_before_the_meta' ); ?>
					<?php echo tribe_get_organizer_details(); ?>
					<?php do_action( 'tribe_events_single_organizer_after_the_meta' ); ?>
				</div>
			</div>

		</div>

		<!-- Organizer Content -->
		<?php if ( get_the_content() ) { ?>
			<div class="tribe-organizer-description tribe-events-content mb-5">
				<?php the_content(); ?>
			</div>
		<?php } ?>

		<?php do_action( 'tribe_events_single_organizer_after_organizer' ); ?>

		<!-- Upcoming event list -->
		<?php do_action( 'tribe_events_single_organizer_before_upcoming_events' ); ?>

		<?php
		// Use the tribe_events_single_organizer_posts_per_page to filter the number of events to get here.
		echo tribe_organizer_upcoming_events( $organizer_id ); ?>

		<?php do_action( 'tribe_events_single_organizer_after_upcoming_events' ); ?>

	</div><!-- .tribe-events-organizer -->
	<?php
	do_action( 'tribe_events_single_organizer_after_template' );
endwhile;
