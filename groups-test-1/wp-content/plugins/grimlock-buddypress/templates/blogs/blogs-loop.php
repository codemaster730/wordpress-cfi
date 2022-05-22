<?php
/**
 * BuddyPress - Blogs Loop
 *
 * Querystring is set via AJAX in _inc/ajax.php - bp_dtheme_object_filter()
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 * @version 3.0.0
 */

// @codingStandardsIgnoreFile
// Allow plugin text domain in theme.
?>

<?php do_action( 'bp_before_blogs_loop' ); ?>

<?php if ( bp_has_blogs( bp_ajax_querystring( 'blogs' ) ) ) : ?>

	<?php do_action( 'bp_before_directory_blogs_list' ); ?>

	<ul id="blogs-list" class="bp-card-list bp-card--list-groups loading-list text-center" role="main">

		<?php while ( bp_blogs() ) : bp_the_blog(); ?>

			<li class="bp-card-list__item bp-card--list-groups__item element-animated fade-in short element-animated-delay element-animated-both">

				<div class="card">

					<div class="pt-4">

						<header class="card-body-header entry-header clearfix">
							<div class="item-title"><a href="<?php bp_blog_permalink(); ?>"><?php bp_blog_name(); ?></a></div>
							<div class="item-meta"><span class="activity"><?php bp_blog_last_active(); ?></span></div>
							<?php do_action( 'bp_directory_blogs_item' ); ?>
						</header> <!-- .card-body-header -->

						<div class="card-body-meta">
							<hr />
							<?php bp_blog_latest_post(); ?>
							<hr />
						</div> <!-- .card-body-meta -->

						<?php do_action( 'bp_directory_blogs_actions' ); ?>

					</div> <!-- .card-body-->

				</div> <!-- .card-->

			</li> <!-- .bp-card-list__item-->

		<?php endwhile; ?>

	</ul>

	<?php do_action( 'bp_after_directory_blogs_list' ); ?>

	<?php bp_blog_hidden_fields(); ?>

	<div id="pag-bottom" class="pagination">
		<div class="pag-count" id="blog-dir-count-bottom">
			<?php bp_blogs_pagination_count(); ?>
		</div>
		<div class="pagination-links" id="blog-dir-pag-bottom">
			<?php bp_blogs_pagination_links(); ?>
		</div>
	</div>

<?php else : ?>

	<div id="message" class="info">
		<p><?php esc_html_e( 'Sorry, there were no sites found.', 'buddypress' ); ?></p>
	</div>

<?php endif; ?>

<?php do_action( 'bp_after_blogs_loop' ); ?>
