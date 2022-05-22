<?php
/**
 * BuddyPress - Groups Cover Image Header.
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 * @version 3.0.0
 */

// @codingStandardsIgnoreFile
// Allow plugin text domain in theme.

do_action( 'bp_before_group_header' );
?>

<?php if ( function_exists( 'bp_group_use_cover_image_header' ) && bp_group_use_cover_image_header() ) : ?>
	<div id="header-cover-image" class="element-animated fade-in"></div>
	<?php if ( bp_group_is_admin() || bp_group_is_mod() ) : ?>
		<a href="<?php echo esc_url( bp_get_group_permalink() ) . 'admin/group-cover-image'; ?>" class="cover-btn-edit d-none d-md-inline-block">
			<?php esc_html_e('Edit Cover Image','buddypress'); ?>
		</a>
	<?php endif; ?>
<?php endif; ?>

<div id="profile-header" class="profile-header profile-header--group">

	<div id="profile-header-content">

		<div class="container container--medium h-100">

			<div class="row align-items-start align-items-lg-end h-100 pos-r">

				<?php if ( ! bp_disable_group_avatar_uploads() ) : ?>
					<div class="profile-header__avatar col-auto">

						<?php if ( bp_group_is_admin() || bp_group_is_mod() ) : ?>
							<a href="<?php echo esc_url( bp_get_group_permalink() . 'admin/group-avatar' ); ?>" class="avatar-wrapper avatar-overlay-edit">
								<?php bp_group_avatar( 'type=full' ); ?>
							</a>
						<?php else : ?>
							<a href="<?php echo esc_url( bp_get_group_permalink() ); ?>" class="avatar-wrapper">
								<?php bp_group_avatar( 'type=full' ); ?>
							</a>
						<?php endif; ?>

						<?php if ( ! apply_filters( 'grimlock_buddypress_groups_actions_text_displayed', true ) ) : ?>
							<?php if ( is_user_logged_in() ) : ?>
								<div id="item-buttons" class="action"><?php do_action( 'bp_group_header_actions' ); ?></div> <!-- .action -->
							<?php endif; ?>
						<?php endif; ?>

					</div> <!-- .profile-header__avatar -->
				<?php endif; ?>

				<div class="profile-header__body col-12 col-md pt-4 pt-md-0">

					<div class="item-sumary mb-3">

						<h2 class="user-nicename"><?php bp_group_name(); ?></h2>

						<div class="item-summary__meta">
							<?php do_action( 'bp_before_group_header_meta' ); ?>
							<span class="group-count"><?php bp_group_member_count(); ?></span>
							<div class="clear mb-3 d-md-none"></div>
							<span class="separator text-muted d-none d-md-inline pl-2 pr-2 small">•</span>
							<span class="group-type"><?php bp_group_type(); ?></span>
							<div class="clear mb-3 d-md-none"></div>
							<span class="separator text-muted d-none d-md-inline pl-2 pr-2 small">•</span>
							<?php esc_html_e( 'Active','buddypress' ); ?>
							<span class="activity group-last-activity" data-livestamp="<?php
							$last_group_args = array(
								'relative' => false,
							);
							bp_core_iso8601_date( bp_get_group_last_active( 0, $last_group_args ) ); ?>"><?php echo esc_html( bp_get_group_last_active() ); ?></span>
							<div class="bp-group-custom-fields"><?php do_action( 'grimlock_buddypress_group_custom_fields' ); ?></div> <!-- .bp-group-custom-fields -->
						</div> <!-- .item-summary__meta -->

					</div> <!-- .item-summary -->

					<div class="item-description mb-3">
						<div><?php bp_group_type_list(); ?></div>
						<?php bp_group_description_excerpt(false, 200); ?>
					</div> <!-- .item-description -->

					<div class="item-meta">

						<?php do_action( 'bp_group_header_meta' ); ?>

						<?php if ( apply_filters( 'grimlock_buddypress_groups_actions_text_displayed', true ) ) : ?>
							<?php if ( is_user_logged_in() ) : ?>
								<div id="item-buttons" class="action"><?php do_action( 'bp_group_header_actions' ); ?></div> <!-- .action -->
							<?php endif; ?>
						<?php endif; ?>

						<div class="item-admins">

							<?php if ( bp_disable_group_avatar_uploads() ) : ?>
								<?php do_action( 'bp_group_header_actions' ); ?>
							<?php endif; ?>

							<!-- Button trigger modal -->
							<?php if ( is_user_logged_in() && bp_group_is_visible() ) : ?>
								<button type="button" class="btn btn-outline-secondary btn-sm px-3 py-2" data-toggle="modal" data-target="#adminModal">
									<?php esc_html_e( 'View' , 'buddypress' ); ?> <?php esc_html_e( 'Admins' , 'buddypress' ); ?> <?php if ( bp_group_has_moderators() ) : ?>& <?php esc_html_e( 'Mods' , 'buddypress' ); ?><?php endif; ?>
								</button>
							<?php endif; ?>

						</div> <!-- .item-admins -->

					</div>

				</div> <!-- .profile-header__content -->

			</div> <!-- .row -->

		</div> <!-- .container -->

	</div> <!-- #profile-header-content -->

</div><!-- #profile-header -->

<?php do_action( 'bp_after_group_header' ); ?>
