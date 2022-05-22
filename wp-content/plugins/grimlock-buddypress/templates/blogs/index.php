<?php
/**
 * BuddyPress - Blogs Directory
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 * @version 3.0.0
 */

// @codingStandardsIgnoreFile
// Allow plugin text domain in theme.

get_header( 'buddypress' ); ?>

<?php do_action( 'bp_before_directory_blogs_page' ); ?>

<div id="content">
	<div class="padder">

		<?php do_action( 'bp_before_directory_blogs' ); ?>

		<form action="" method="post" id="blogs-directory-form" class="dir-form">

			<h3><?php esc_html_e( 'Site Directory', 'buddypress' ); ?><?php if ( is_user_logged_in() && bp_blog_signup_enabled() ) : ?> &nbsp;<a class="button" href="<?php echo esc_url( bp_get_root_domain() . '/' . bp_get_blogs_root_slug() . '/create/' ); ?>"><?php esc_html_e( 'Create a Site', 'buddypress' ); ?></a><?php endif; ?></h3>

			<?php do_action( 'bp_before_directory_blogs_content' ); ?>

			<div id="blog-dir-search" class="dir-search" role="search">

				<?php bp_directory_blogs_search_form(); ?>

			</div><!-- #blog-dir-search -->

			<div class="row mb-4 mt-md-2 mt-lg-0 directory-form-row">

				<div class="col-auto text-lg-center directory-form-nav">

					<div class="item-list-tabs primary-list-tabs" role="navigation">
						<ul class="item-list-tabs-ul clearfix">
							<li class="selected" id="blogs-all">
								<a href="<?php bp_root_domain(); ?>/<?php bp_blogs_root_slug(); ?>">
									<?php
									/* translators: %s: Total blog count */
									printf( esc_html__( 'All Sites <span>%s</span>', 'buddypress' ), esc_html( bp_get_total_blog_count() ) ); ?>
								</a>
							</li>

							<?php if ( is_user_logged_in() && bp_get_total_blog_count_for_user( bp_loggedin_user_id() ) ) : ?>
								<li id="blogs-personal">
									<a href="<?php echo esc_url( bp_loggedin_user_domain() . bp_get_blogs_slug() ); ?>">
										<?php
										/* translators: %s: Total user blog count */
										printf( esc_html__( 'My Sites <span>%s</span>', 'buddypress' ), esc_html( bp_get_total_blog_count_for_user( bp_loggedin_user_id() ) ) ); ?>
									</a>
								</li>
							<?php endif; ?>

							<?php do_action( 'bp_blogs_directory_blog_types' ); ?>
						</ul>
					</div><!-- .item-list-tabs -->
				</div><!-- .col-* -->

				<div class="col-auto directory-form-filter">
					<ul class="list-inline m-0 dir-filter">
						<?php do_action( 'bp_blogs_directory_blog_sub_types' ); ?>

						<li id="blogs-order-select" class="last filter">
							<div class="select-style">
								<select id="blogs-order-by">
									<option value="active"><?php esc_html_e( 'Last Active', 'buddypress' ); ?></option>
									<option value="newest"><?php esc_html_e( 'Newest', 'buddypress' ); ?></option>
									<option value="alphabetical"><?php esc_html_e( 'Alphabetical', 'buddypress' ); ?></option>

									<?php do_action( 'bp_blogs_directory_order_options' ); ?>
								</select>
							</div><!-- .select-style -->
						</li>
					</ul><!-- .dir-filter -->
				</div><!-- .col-* -->
			</div><!-- .row -->

			<div id="blogs-dir-list" class="blogs dir-list">

				<?php locate_template( array( 'blogs/blogs-loop.php' ), true ); ?>

			</div><!-- #blogs-dir-list -->

			<?php do_action( 'bp_directory_blogs_content' ); ?>

			<?php wp_nonce_field( 'directory_blogs', '_wpnonce-blogs-filter' ); ?>

			<?php do_action( 'bp_after_directory_blogs_content' ); ?>

		</form><!-- #blogs-directory-form -->

		<?php do_action( 'bp_after_directory_blogs' ); ?>

	</div><!-- .padder -->
</div><!-- #content -->

<?php do_action( 'bp_after_directory_blogs_page' ); ?>

<?php get_sidebar( 'buddypress' ); ?>
<?php get_footer( 'buddypress' ); ?>
