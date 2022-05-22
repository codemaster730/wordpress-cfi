<?php
/**
 * The template for displaying the dashboard.
 *
 * Template Name: Dashboard Template
 *
 * @package cera
 */

get_header(); ?>

	<div id="primary">
		<main id="main" class="site-main">

			<?php
				/* Start the Loop */
				while ( have_posts() ) : the_post();

					get_template_part( 'template-parts/content', 'page' );

				endwhile; // End of the loop.
			?>

			<div class="position-relative">

				<div class="widget-area">
					<?php if ( is_active_sidebar( 'dashboard' ) ) :
						dynamic_sidebar( 'dashboard' );
					endif; ?>
					<div class="grid-sizer"></div>
					<div class="gutter-sizer"></div>
				</div>

				<div class="dashboard--fake">
					<div class="dashboard--fake__item"></div>
					<div class="dashboard--fake__item"></div>
					<div class="dashboard--fake__item"></div>
					<div class="dashboard--fake__item"></div>
					<div class="dashboard--fake__item"></div>
					<div class="dashboard--fake__item"></div>
					<div class="dashboard--fake__item"></div>
					<div class="dashboard--fake__item"></div>
					<div class="dashboard--fake__item"></div>
					<div class="dashboard--fake__item"></div>
					<div class="dashboard--fake__item"></div>
					<div class="dashboard--fake__item"></div>
				</div>

			</div>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_footer();
