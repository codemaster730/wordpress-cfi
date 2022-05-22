<?php
/**
 * The template for displaying search results pages.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package cera
 */

get_header();
get_sidebar( 'left' ); ?>

	<div id="primary" class="content-area region__col region__col--2">
		<main id="main" class="site-main">

			<?php
			if ( have_posts() ) : ?>

				<header class="grimlock--page-header entry-header">
					<h1 class="page-title entry-title">
						<?php
						/* translators: %s: The search query */
						printf( esc_html__( 'Search Results for: %s', 'cera' ), '<span>' . get_search_query() . '</span>' ); ?>
					</h1>
					<?php do_action( 'cera_breadcrumb' ); ?>
				</header><!-- .grimlock--page-header -->

				<?php
				do_action( 'cera_before_search_posts' ); ?>

				<?php /* Start the Loop */
				while ( have_posts() ) : the_post();

					/**
					 * Run the loop for the search to output the results.
					 * If you want to overload this in a child theme then include a file
					 * called content-search.php and that will be used instead.
					 */
					get_template_part( 'template-parts/content', 'search' );

				endwhile; // End of the loop.

				do_action( 'cera_after_posts' );

				else :

					get_template_part( 'template-parts/content', 'none' );

			endif; ?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_sidebar( 'right' );
get_footer();
