<?php
/**
 * BuddyPress Activity templates
 *
 * @since 2.3.0
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 * @version 3.0.0
 */

// @codingStandardsIgnoreFile
// Allow plugin text domain in theme and unescaped template tags.
?>

<?php do_action( 'bp_before_directory_activity' ); ?>

<div id="buddypress">

	<div class="row">

		<div class="col-md-12 col-lg-8 col-xl-9">

			<?php if ( is_user_logged_in() ) : ?>
				<div class="element-animated fade-in short p-0">
					<?php bp_get_template_part( 'activity/post-form' ); ?>
				</div><!-- .element-animated -->
			<?php endif; ?>

			<?php do_action( 'bp_before_directory_activity_content' ); ?>

			<div id="template-notices" role="alert" aria-atomic="true">
				<?php do_action( 'template_notices' ); ?>
			</div>

			<div class="row mb-4">

				<div class="col-md-8 col-lg-9">

					<div class="item-list-tabs activity-type-tabs primary-list-tabs" aria-label="<?php esc_attr_e( 'Sitewide activities navigation', 'buddypress' ); ?>" role="navigation">
						<ul class="item-list-tabs-ul clearfix">
							<?php do_action( 'bp_before_activity_type_tab_all' ); ?>

							<li class="selected" id="activity-all">
								<a href="<?php bp_activity_directory_permalink(); ?>">
									<?php
									/* translators: %s: Total member count */
									printf( esc_html__( 'All Members %s', 'buddypress' ), '<span>' . bp_get_total_member_count() . '</span>' ); ?>
								</a>
							</li>

							<?php if ( is_user_logged_in() ) : ?>

								<?php do_action( 'bp_before_activity_type_tab_friends' ); ?>

								<?php if ( bp_is_active( 'friends' ) ) : ?>

									<?php if ( bp_get_total_friend_count( bp_loggedin_user_id() ) ) : ?>

										<li id="activity-friends">
											<a href="<?php echo esc_url( bp_loggedin_user_domain() . bp_get_activity_slug() . '/' . bp_get_friends_slug() . '/' ); ?>">
												<?php
												/* translators: %s: Total friend count */
												printf( esc_html__( 'My Friends %s', 'buddypress' ), '<span>' . bp_get_total_friend_count( bp_loggedin_user_id() ) . '</span>' ); ?>
											</a>
										</li>

									<?php endif; ?>

								<?php endif; ?>

								<?php do_action( 'bp_before_activity_type_tab_groups' ); ?>

								<?php if ( bp_is_active( 'groups' ) ) : ?>

									<?php if ( bp_get_total_group_count_for_user( bp_loggedin_user_id() ) ) : ?>

										<li id="activity-groups">
											<a href="<?php echo esc_url( bp_loggedin_user_domain() . bp_get_activity_slug() . '/' . bp_get_groups_slug() . '/' ); ?>">
												<?php
												/* translators: %s: Total user group count */
												printf( esc_html__( 'My Groups %s', 'buddypress' ), '<span>' . bp_get_total_group_count_for_user( bp_loggedin_user_id() ) . '</span>' ); ?>
											</a>
										</li>

									<?php endif; ?>

								<?php endif; ?>

								<?php do_action( 'bp_before_activity_type_tab_favorites' ); ?>

								<?php if ( bp_get_total_favorite_count_for_user( bp_loggedin_user_id() ) ) : ?>

									<li id="activity-favorites">
										<a href="<?php echo esc_url( bp_loggedin_user_domain() . bp_get_activity_slug() . '/favorites/' ); ?>">
											<?php
											/* translators: %s: Total user favorite count */
											printf( esc_html__( 'My Favorites %s', 'buddypress' ), '<span>' . bp_get_total_favorite_count_for_user( bp_loggedin_user_id() ) . '</span>' ); ?>
										</a>
									</li>

								<?php endif; ?>

								<?php if ( bp_activity_do_mentions() ) : ?>

									<?php do_action( 'bp_before_activity_type_tab_mentions' ); ?>

									<li id="activity-mentions">
										<a href="<?php echo esc_url( bp_loggedin_user_domain() . bp_get_activity_slug() . '/mentions/' ); ?>">
											<?php
											esc_html_e( 'Mentions', 'buddypress' );
											if ( bp_get_total_mention_count_for_user( bp_loggedin_user_id() ) ) : ?> <strong><span><?php
													/* translators: %s: Total user mention count */
													printf( _nx( '%s new', '%s new', bp_get_total_mention_count_for_user( bp_loggedin_user_id() ), 'Number of new activity mentions', 'buddypress' ), bp_get_total_mention_count_for_user( bp_loggedin_user_id() ) ); ?></span></strong>
												<?php
											endif; ?>
										</a>
									</li>

								<?php endif; ?>

							<?php endif; ?>

							<?php do_action( 'bp_activity_type_tabs' ); ?>
						</ul><!-- .clearfix -->
					</div><!-- .item-list-tabs -->

				</div><!-- .col-* -->

				<div class="col-md-4 col-lg-3 ml-md-auto">

					<ul class="list-inline ml-md-auto dir-filter">
						<li id="activity-filter-select" class="last filter">
							<div class="select-style">
								<select id="activity-filter-by">
									<option value="-1"><?php esc_html_e( '&mdash; Everything &mdash;', 'buddypress' ); ?></option>
									<?php bp_activity_show_filters(); ?>
									<?php do_action( 'bp_activity_filter_options' ); ?>
								</select>
							</div><!-- .select-style -->
						</li><!-- .filter -->
					</ul><!-- .dir-filter -->

				</div><!-- .col-* -->

			</div><!-- .row -->

			<?php do_action( 'bp_before_directory_activity_list' ); ?>

			<div class="loading-list">

				<div class="activity p-0 mb-4 pt-md-2 element-animated fade-in short" aria-live="polite" aria-atomic="true" aria-relevant="all">
					<?php bp_get_template_part( 'activity/activity-loop' ); ?>
				</div><!-- .activity -->

				<?php do_action( 'bp_after_directory_activity_list' ); ?>

				<?php if ( bp_has_activities() ) : ?>
					<div class="feed d-none">
						<a target="_blank" href="<?php bp_sitewide_activity_feed_link(); ?>" class="bp-tooltip" data-bp-tooltip="<?php esc_attr_e( 'RSS Feed', 'buddypress' ); ?>" aria-label="<?php esc_attr_e( 'RSS Feed', 'buddypress' ); ?>"><?php esc_html_e( 'RSS', 'buddypress' ); ?></a>
						<?php do_action( 'bp_activity_syndication_options' ); ?>
					</div><!-- .feed -->
				<?php endif; ?>

			</div><!-- .loading-list-animated -->

			<?php do_action( 'bp_directory_activity_content' ); ?>

			<?php do_action( 'bp_after_directory_activity_content' ); ?>

			<?php do_action( 'bp_after_directory_activity' ); ?>

		</div><!-- .col-* -->

		<?php bp_get_template_part( 'bp-sidebar' ); ?>

	</div><!-- .row -->

</div><!-- #buddypress -->
