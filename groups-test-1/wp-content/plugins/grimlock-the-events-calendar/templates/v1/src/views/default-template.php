<?php
/**
 * Default Events Template
 * This file is the basic wrapper template for all the views if 'Default Events Template'
 * is selected in Events -> Settings -> Display -> Events Template.
 *
 * Override this template in your own theme by creating a file at [your-theme]/tribe-events/default-template.php
 *
 * @package TribeEventsCalendar
 * @version 4.6.23
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

get_header();
get_sidebar( 'left' ); ?>

	<div id="primary" class="content-area region__col region__col--2">
		<main id="main" class="site-main">

			<?php tribe_events_before_html(); ?>
			<?php tribe_get_view(); ?>
			<?php tribe_events_after_html(); ?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_sidebar( 'right' );
get_footer();
