<?php
/**
 * Week View Content
 * The content template for the week view. This template is also used for
 * the response that is returned on week view ajax requests.
 *
 * Override this template in your own theme by creating a file at [your-theme]/tribe-events/pro/week/content.php
 *
 * @package TribeEventsCalendar
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
} ?>

<div id="tribe-events-content" class="tribe-events-week-grid tribe-clearfix">

	<!-- Week Title -->
	<?php do_action( 'tribe_events_before_the_title' ); ?>
	<div class="tribe-events-entry-header d-flex align-items-center mb-4">
		<h2 class="tribe-events-page-title"><?php tribe_events_title(); ?></h2>
		<div class="col-auto d-none d-md-block ml-auto pr-0">
			<?php do_action( 'tribe_events_after_footer' ); ?>
		</div>
	</div> <!-- .tribe-events-entry-header -->
	<?php do_action( 'tribe_events_after_the_title' ); ?>

	<!-- Notices -->
	<?php tribe_the_notices(); ?>

	<!-- Calendar Header -->
	<?php do_action( 'tribe_events_before_header' ); ?>
	<div id="tribe-events-header" <?php tribe_events_the_header_attributes(); ?>>

		<!-- Header Navigation -->
		<div class="post-navigation">
			<?php tribe_get_template_part( 'pro/week/nav', 'header' ); ?>
		</div>

	</div><!-- #tribe-events-header -->

	<?php do_action( 'tribe_events_after_header' ); ?>

	<!-- Calendar Grid -->
	<?php tribe_get_template_part( 'pro/week/loop', 'grid' ); ?>

	<!-- Calendar Footer -->
	<?php do_action( 'tribe_events_before_footer' ); ?>
	<div class="post-navigation">

		<!-- Footer Navigation -->
		<?php do_action( 'tribe_events_before_footer_nav' ); ?>
		<?php tribe_get_template_part( 'pro/week/nav', 'footer' ); ?>
	</div><!-- #tribe-events-footer -->
	<?php do_action( 'tribe_events_after_footer_nav' ); ?>

	<?php tribe_get_template_part( 'pro/week/mobile' ); ?>
	<?php tribe_get_template_part( 'pro/week/tooltip' ); ?>

</div><!-- #tribe-events-content -->
