<?php
/**
 * The template for displaying the homepage in a minimal style. Without custom header.
 *
 * Template Name: Homepage Template: Minimal
 *
 * @package cera
 */

get_header(); ?>

	<div id="primary">
		<main id="main" class="site-main">

			<?php
			/**
			 * Functions hooked into cera_homepage action
			 *
			 * @hooked cera_grimlock_homepage - 10
			 */
			do_action( 'cera_homepage' ); ?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_footer();
