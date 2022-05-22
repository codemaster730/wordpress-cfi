<?php
/**
 * The template for displaying the homepage.
 *
 * Template Name: Homepage Template
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
