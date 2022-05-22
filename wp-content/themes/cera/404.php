<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package cera
 */

get_header(); ?>

	<div id="primary" class="region__col region__col--2 content-area">
		<main id="main" class="site-main" role="main">

			<?php
			/**
			 * Functions hooked into cera_404 action
			 *
			 * @hooked cera_404          - 10
			 * @hooked cera_grimlock_404 - 10
			 */
			do_action( 'cera_404' ); ?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_footer();
