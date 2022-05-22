<?php
/**
 * The template for displaying pages with right sidebar.
 *
 * Template Name: Sidebar Template: Right
 *
 * @package grimlock
 */

get_header(); ?>

	<div id="primary" class="content-area region__col region__col--2">
		<main id="main" class="site-main">

			<?php
			/* Start the Loop */
			while ( have_posts() ) : the_post(); ?>

				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<?php
					/**
					 * Functions hooked into grimlock_page action
					 *
					 * @see Grimlock::page()
					 */
					do_action( 'grimlock_page' ); ?>
				</article><!-- #post-## -->

				<?php
				// If comments are open or we have at least one comment, load up the comment template.
				if ( comments_open() || get_comments_number() ) :
					comments_template();
				endif;

			endwhile; // End of the loop.
			?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
/**
 * Functions hooked into grimlock_sidebar_left action
 *
 * @see grimlock_sidebar_right
 */
do_action( 'grimlock_sidebar_right' );
get_footer();
