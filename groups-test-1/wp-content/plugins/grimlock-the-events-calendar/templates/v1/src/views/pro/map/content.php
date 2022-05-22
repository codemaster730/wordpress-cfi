<?php
/**
 * Map View Content
 * The content template for the map view of events. This template is also used for
 * the response that is returned on map view ajax requests.
 *
 * Override this template in your own theme by creating a file at [your-theme]/tribe-events/pro/map/content.php
 *
 * @package TribeEventsCalendar
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
} ?>

<div id="tribe-events-content" class="tribe-events-list tribe-events-type-list">

	<!-- List Title -->
	<?php do_action( 'tribe_events_before_the_title' ); ?>
	<div class="tribe-events-entry-header d-flex align-items-center mb-4">
		<h2 class="tribe-events-page-title text-left m-0">
			<?php
			// @codingStandardsIgnoreStart
			echo tribe_get_events_title();
			// @codingStandardsIgnoreEnd ?>
		</h2>
		<div class="col-auto d-none d-md-block ml-auto pr-0">
			<?php do_action( 'tribe_events_after_footer' ); ?>
		</div>
	</div>
	<?php do_action( 'tribe_events_after_the_title' ); ?>

	<!-- Notices -->
	<?php tribe_the_notices(); ?>

	<!-- Events Loop -->
	<?php if ( have_posts() ) : ?>
		<?php do_action( 'tribe_events_before_loop' ); ?>
		<div class="tribe-events-loop">
			<?php tribe_get_template_part( 'pro/map/loop' ); ?>
		</div> <!-- #tribe-geo-results -->
		<?php do_action( 'tribe_events_after_loop' ); ?>
	<?php endif; ?>

	<!-- List Footer -->
	<?php do_action( 'tribe_events_before_footer' ); ?>
	<div class="post-navigation">

		<!-- Footer Navigation -->
		<?php do_action( 'tribe_events_before_footer_nav' ); ?>
		<?php tribe_get_template_part( 'pro/map/nav', 'footer' ); ?>
		<?php do_action( 'tribe_events_after_footer_nav' ); ?>

	</div>
	<!-- #tribe-events-footer -->

</div><!-- #tribe-events-content -->
