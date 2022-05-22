<?php
/**
 * BuddyPress - Groups
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 * @version 3.0.0
 */

// @codingStandardsIgnoreFile
// Allow plugin text domain in theme.

do_action( 'bp_before_directory_groups_page' ); ?>

	<div id="buddypress">

		<div id="template-notices" role="alert" aria-atomic="true">
			<?php do_action( 'template_notices' ); ?>
		</div>

		<?php do_action( 'bp_before_directory_groups' ); ?>
		<?php do_action( 'bp_before_directory_groups_content' ); ?>

		<div class="pos-r">

			<?php if ( has_filter( 'bp_directory_groups_search_form' ) ) : ?>
				<div id="group-dir-search" class="dir-search" role="search">
					<?php bp_directory_groups_search_form(); ?>
				</div><!-- #group-dir-search -->
			<?php else : ?>
				<?php bp_get_template_part( 'common/search/dir-search-form' ); ?>
			<?php endif; ?>

			<form action="" method="post" id="groups-directory-form">

				<div class="row mb-4 mt-3 mt-lg-0 directory-form-row">

					<div class="col directory-form-nav">
						<div class="item-list-tabs primary-list-tabs" aria-label="<?php esc_attr_e( 'Groups directory main navigation', 'buddypress' ); ?>">
							<ul class="item-list-tabs-ul clearfix">
								<li class="selected" id="groups-all">
									<a href="<?php bp_groups_directory_permalink(); ?>">
										<?php
										/* translators: %s: Total group count */
										printf( esc_html__( 'All Groups %s', 'buddypress' ), '<span>' . esc_html( bp_get_total_group_count() ) . '</span>' ); ?>
									</a>
								</li>

								<?php if ( is_user_logged_in() && bp_get_total_group_count_for_user( bp_loggedin_user_id() ) ) : ?>
									<li id="groups-personal">
										<a href="<?php echo esc_url( bp_loggedin_user_domain() . bp_get_groups_slug() . '/my-groups/' ); ?>">
											<?php
											/* translators: %s: Total user group count */
											printf( esc_html__( 'My Groups %s', 'buddypress' ), '<span>' . esc_html( bp_get_total_group_count_for_user( bp_loggedin_user_id() ) ) . '</span>' ); ?>
										</a>
									</li>
								<?php endif; ?>

								<?php do_action( 'bp_groups_directory_group_filter' ); ?>
							</ul>
						</div><!-- .item-list-tabs -->
					</div><!-- .col-* -->

					<div class="col-auto directory-form-filter">
						<ul class="list-inline m-0 dir-filter">
							<?php do_action( 'bp_groups_directory_group_types' ); ?>
							<li id="groups-order-select" class="last filter">
								<label for="groups-order-by" class="sr-only"><?php esc_html_e( 'Order By:', 'buddypress' ); ?></label>
								<div class="select-style">
									<select id="groups-order-by">
										<option value="active"><?php esc_html_e( 'Last Active', 'buddypress' ); ?></option>
										<option value="popular"><?php esc_html_e( 'Most Members', 'buddypress' ); ?></option>
										<option value="newest"><?php esc_html_e( 'Newly Created', 'buddypress' ); ?></option>
										<option value="alphabetical"><?php esc_html_e( 'Alphabetical', 'buddypress' ); ?></option>
										<?php do_action( 'bp_groups_directory_order_options' ); ?>
									</select>
								</div>
							</li>
						</ul>
					</div><!-- .col-* -->

				</div><!-- .row -->

				<h2 class="bp-screen-reader-text">
					<?php esc_html_e( 'Groups directory', 'buddypress' ); ?>
				</h2>

				<div id="groups-dir-list" class="groups dir-list">
					<?php bp_get_template_part( 'groups/groups-loop' ); ?>
				</div><!-- #groups-dir-list -->

				<?php do_action( 'bp_directory_groups_content' ); ?>

				<?php wp_nonce_field( 'directory_groups', '_wpnonce-groups-filter' ); ?>

				<?php do_action( 'bp_after_directory_groups_content' ); ?>

			</form><!-- #groups-directory-form -->

			<?php do_action( 'bp_after_directory_groups' ); ?>

		</div><!-- .pos-r -->

	</div><!-- #buddypress -->

<?php do_action( 'bp_after_directory_groups_page' );
