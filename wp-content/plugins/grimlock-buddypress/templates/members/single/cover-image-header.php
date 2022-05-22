<?php
/**
 * BuddyPress - Users Cover Image Header
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 * @version 3.0.0
 */

// @codingStandardsIgnoreFile
// Allow plugin text domain in theme.

do_action( 'bp_before_member_header' );
$user_id = get_current_user_id();
?>

<?php if ( function_exists( 'bp_displayed_user_use_cover_image_header' ) && bp_displayed_user_use_cover_image_header() ) : ?>
	<div id="header-cover-image" class="element-animated fade-in"></div>
	<?php if ( bp_is_my_profile() ) : ?>
		<a href="<?php echo esc_url( bp_get_displayed_user_link() . 'profile/change-cover-image' ); ?>" class="cover-btn-edit d-none d-md-inline-block">
			<?php esc_html_e( 'Edit Cover Image','buddypress' ); ?>
		</a>
	<?php endif; ?>
<?php endif; ?>

<div id="profile-header" class="profile-header profile-header--member">

	<div id="profile-header-content">

		<div class="container container--medium h-100">

			<div class="row align-items-start align-items-lg-end h-100 pos-r">

				<div class="profile-header__avatar col-auto">

					<div class="pos-r d-inline-block">

						<?php grimlock_buddypress_online_badge(); ?>

						<?php if ( bp_is_my_profile() && ! (int) bp_get_option( 'bp-disable-avatar-uploads' ) ) : ?>
							<a href="<?php echo esc_url( bp_get_displayed_user_link() . 'profile/change-avatar' ); ?>" class="avatar-overlay-edit avatar-wrapper">
								<?php bp_displayed_user_avatar( 'type=full' ); ?>
							</a>
						<?php else : ?>
							<a href="<?php echo esc_url( bp_get_displayed_user_link() ); ?>" class="avatar-wrapper">
								<?php bp_displayed_user_avatar( 'type=full' ); ?>
							</a>
						<?php endif; ?>

						<?php if ( ! apply_filters( 'grimlock_buddypress_members_actions_text_displayed', true ) && ! bp_is_my_profile() ) : ?>
							<?php if ( is_user_logged_in() ) : ?>
								<div id="item-buttons" class="action"><?php do_action( 'bp_member_header_actions' ); grimlock_buddypress_actions_dropdown_profile(); ?></div> <!-- .action -->
							<?php endif; ?>
						<?php endif; ?>

						<?php if ( bp_is_my_profile() && ( bp_is_active( 'settings' ) || bp_is_active( 'messages' ) || bp_is_active( 'notifications' ) ) ) : ?>
							<div class="quick-settings-actions d-flex justify-content-center align-items-center d-md-none">
								<div class="card card-static ov-v">
									<div class="row m-0">
										<?php if ( bp_is_active( 'notifications' ) ) : ?>
											<?php
											$count_notifications = 0;
											$count_notifications = bp_notifications_get_unread_notification_count( $user_id );
											?>
											<div class="col p-0">
												<a class="d-block px-3 py-2 text-center item--notifications" href="<?php echo esc_url( bp_get_displayed_user_link() . BP_NOTIFICATIONS_SLUG ); ?>" title="<?php esc_html_e( 'Notifications', 'buddypress' ); ?>">
													<?php if ( $count_notifications > 0 ) : ?>
														<span class="bubble-count notification-count"><?php echo esc_html( $count_notifications ); ?></span>
													<?php endif; ?>
												</a>
											</div>
										<?php endif; ?>
										<?php if ( bp_is_active( 'messages' ) ) : ?>
											<?php
											$count_messages = 0;
											$count_messages = messages_get_unread_count( $user_id );
											?>
											<div class="col p-0">
												<a class="d-block px-3 py-2 text-center item--messages" href="<?php echo esc_url( bp_get_displayed_user_link() . BP_MESSAGES_SLUG ); ?>" title="<?php esc_html_e( 'Messages', 'buddypress' ); ?>">
													<?php if ( $count_messages > 0 ) : ?>
														<span class="bubble-count message-count"><?php echo esc_html( $count_messages ); ?></span>
													<?php endif; ?>
												</a>
											</div>
										<?php endif; ?>
										<?php if ( bp_is_active( 'settings' ) ) : ?>
											<div class="col p-0">
												<a class="d-block px-3 py-2 text-center item--settings" href="<?php echo esc_url( bp_get_displayed_user_link() . BP_SETTINGS_SLUG ); ?>" title="<?php esc_html_e( 'Settings', 'buddypress' ); ?>"></a>
											</div>
										<?php endif; ?>
									</div>
								</div>
							</div>
						<?php endif; ?>

					</div> <!-- .pos-r -->

				</div> <!-- .profile-header__avatar -->

				<div class="profile-header__body col-12 col-md pt-4 pt-md-0">

					<div class="row align-items-lg-end">

						<?php do_action( 'grimlock_buddypress_member_featured_media' ); ?>

						<div class="col-12 col-lg order-2">

							<div class="item-sumary">

								<?php do_action( 'grimlock_buddypress_member_xprofile_name' ); ?>

								<div class="item-summary__meta">
									<div class="bp-member-xprofile-custom-fields"><?php do_action( 'grimlock_buddypress_member_xprofile_custom_fields' ); ?></div> <!-- .bp-member-xprofile-custom-fields -->
								</div> <!-- .item-summary__meta -->

							</div> <!-- .item-summary -->

							<?php do_action( 'bp_profile_header_meta' ); ?>

							<div class="profile-header__before-meta mt-3"><?php do_action( 'bp_before_member_header_meta' ); ?></div> <!-- .profile-header__before-meta -->

							<?php if ( apply_filters( 'grimlock_buddypress_member_header_author_bio_displayed', false ) ) : ?>
								<div class="item-description"><?php echo wp_kses_post( wp_trim_words( get_the_author_meta( 'description', bp_displayed_user_id() ), 30 ) ); ?></div>
							<?php else : ?>
								<div class="item-activity">

									<span class="activity" data-livestamp="<?php bp_core_iso8601_date( bp_get_user_last_activity( bp_displayed_user_id() ) ); ?>"><?php bp_last_activity( bp_displayed_user_id() ); ?></span>

									<?php if ( bp_is_active( 'activity' ) ) : ?>
										<div id="latest-update">
											<?php bp_activity_latest_update( bp_displayed_user_id() ); ?>
										</div> <!-- #latest-update -->
									<?php endif; ?>

								</div> <!-- .item-activity -->
							<?php endif; ?>

							<?php if ( apply_filters( 'grimlock_buddypress_members_actions_text_displayed', true )  && ! bp_is_my_profile() ) : ?>
								<?php if ( is_user_logged_in() ) : ?>
									<div id="item-buttons" class="action"><?php do_action( 'bp_member_header_actions' ); grimlock_buddypress_actions_dropdown_profile(); ?></div> <!-- .item-buttons.action -->
								<?php endif; ?>
							<?php endif; ?>

						</div> <!-- .col -->

					</div> <!-- .align-items-end -->

				</div> <!-- .profile-header__body -->

				<?php if ( class_exists( 'Mp_BP_Match' ) && ! bp_is_my_profile() ) : ?>

					<?php if ( is_user_logged_in() ) : ?>

						<?php do_shortcode( '[mp_match_percentage]' ); ?>

					<?php else : ?>

						<a href="<?php echo esc_url( wp_registration_url() ); ?>" class="c100 p90 small hmk-percentage fake blue" data-toggle="tooltip" data-placement="top" title="<?php esc_html_e( 'You must login to see the percentage match', 'grimlock-buddypress' ); ?>">
							<span class="hmk-match-inside"><?php esc_html_e( 'Match', 'grimlock-buddypress' ); ?></span>
							<span>65%</span>
							<div class="slice">
								<div class="bar"></div>
								<div class="fill"></div>
							</div>
						</a>

					<?php endif; ?>

				<?php endif; ?>

			</div> <!-- .row -->

		</div> <!-- .container -->

	</div> <!-- #profile-header-content -->

</div> <!-- #profile-header -->

<?php do_action( 'bp_after_member_header' ); ?>
