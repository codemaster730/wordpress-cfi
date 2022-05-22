<?php
/**
 * BuddyPress - Members
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 * @version 3.0.0
 */

// @codingStandardsIgnoreFile
// Allow plugin text domain in theme.

$default_order = apply_filters( 'grimlock_buddypress_members_default_order', 'active' );

do_action( 'bp_before_directory_members_page' ); ?>

	<div id="buddypress" <?php if ( function_exists( 'bps_templates' ) ): ?>class="bps-active"<?php endif; ?>>

		<?php do_action( 'bp_before_directory_members_content' ); ?>

		<div class="pos-r">

			<?php do_action( 'bp_before_directory_members' ); ?>

			<?php if ( has_filter( 'bp_directory_members_search_form' ) ) : ?>
				<div id="members-dir-search" class="dir-search" role="search">
					<?php bp_directory_members_search_form(); ?>
				</div><!-- #members-dir-search -->
			<?php else : ?>
				<?php bp_get_template_part( 'common/search/dir-search-form' ); ?>
			<?php endif; ?>

			<div class="members-dir-wrapper pos-r">

				<?php do_action( 'bp_before_directory_members_tabs' ); ?>

				<form action="" method="post" id="members-directory-form">

					<div class="row mb-4 mt-md-2 mt-lg-0 directory-form-row">

						<div class="col directory-form-nav">

							<div class="item-list-tabs primary-list-tabs" aria-label="<?php esc_attr_e( 'Members directory main navigation', 'buddypress' ); ?>" role="navigation">

								<ul class="item-list-tabs-ul clearfix">
									<li class="selected" id="members-all">
										<a href="<?php bp_members_directory_permalink(); ?>">
											<?php
											/* translators: %s: Total member count */
											printf( esc_html__( 'All Members %s', 'buddypress' ), '<span>' . esc_html( bp_core_get_active_member_count() ) . '</span>' ); ?>
										</a>
									</li>

									<?php if ( is_user_logged_in() && bp_is_active( 'friends' ) && bp_get_total_friend_count( bp_loggedin_user_id() ) ) : ?>
										<li id="members-personal">
											<a href="<?php echo esc_url( bp_loggedin_user_domain() . bp_get_friends_slug() . '/my-friends/' ); ?>">
												<?php
												/* translators: %s: Total friend count */
												printf( esc_html__( 'My Friends %s', 'buddypress' ), '<span>' . esc_html( bp_get_total_friend_count( bp_loggedin_user_id() ) ) . '</span>' ); ?>
											</a>
										</li>
									<?php endif; ?>

									<?php do_action( 'bp_members_directory_member_types' ); ?>
								</ul>
							</div><!-- .item-list-tabs -->
						</div><!-- .col-* -->

						<div class="col-auto directory-form-filter">
							<ul class="list-inline m-0 dir-filter">
								<?php do_action( 'bp_members_directory_member_sub_types' ); ?>
								<li id="members-order-select" class="last filter">
									<div class="select-style">
										<select id="members-order-by" class="resizing_select">
											<option <?php selected( $default_order, 'active' ); ?> value="active"><?php esc_html_e( 'Last Active', 'buddypress' ); ?></option>
											<option <?php selected( $default_order, 'newest' ); ?> value="newest"><?php esc_html_e( 'Newest Registered', 'buddypress' ); ?></option>

											<?php if ( bp_is_active( 'xprofile' ) ) : ?>
												<option <?php selected( $default_order, 'alphabetical' ); ?> value="alphabetical"><?php esc_html_e( 'Alphabetical', 'buddypress' ); ?></option>
											<?php endif; ?>

											<?php do_action( 'bp_members_directory_order_options' ); ?>
										</select>
									</div> <!-- .select-style -->
								</li>
							</ul> <!-- .dir-filter -->
						</div><!-- .col-* -->

					</div><!-- .row -->

					<h2 class="bp-screen-reader-text">
						<?php esc_html_e( 'Members directory', 'buddypress' ); ?>
					</h2>

					<div id="members-dir-list" class="members dir-list">
						<?php bp_get_template_part( 'members/members-loop' ); ?>
					</div><!-- #members-dir-list -->

					<?php do_action( 'bp_directory_members_content' ); ?>

					<?php wp_nonce_field( 'directory_members', '_wpnonce-member-filter' ); ?>

					<?php do_action( 'bp_after_directory_members_content' ); ?>

				</form><!-- #members-directory-form -->

				<?php do_action( 'bp_after_directory_members' ); ?>

			</div>

		</div>

	</div><!-- #buddypress -->

<?php do_action( 'bp_after_directory_members_page' );
