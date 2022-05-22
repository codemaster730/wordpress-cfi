<?php
/**
 * BuddyPress - Groups Home
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 * @version 3.0.0
 */

// @codingStandardsIgnoreFile
// Allow plugin text domain in theme.
?>

<div id="buddypress" <?php if ( ! bp_disable_group_avatar_uploads() ) : ?>class="bp-disable-group-avatar-uploads"<?php endif; ?>>

	<?php
	if ( bp_has_groups() ) :

		while ( bp_groups() ) : bp_the_group(); ?>

			<?php do_action( 'bp_before_group_home_content' ); ?>

			<div id="item-header" role="complementary">
				<?php bp_get_template_part( 'groups/single/cover-image-header' ); ?>
			</div> <!-- #item-header -->

			<div class="profile-content">

				<div id="profile-content__nav" class="item-list-tabs no-ajax" aria-label="<?php esc_attr_e( 'Group primary navigation', 'buddypress' ); ?>" role="navigation">
					<div class="container container--medium">
						<div class="row">
							<div class="profile-content__nav-wrapper col-12">
								<ul id="object-nav" class="main-nav priority-ul clearfix d-md-inline-block">
									<?php bp_get_options_nav(); ?>
									<?php do_action( 'bp_group_options_nav' ); ?>
								</ul>
							</div> <!-- .profile-content__nav-wrapper -->
						</div>
					</div> <!-- .container -->
				</div> <!-- #profile-content__nav -->

				<div id="item-body" class="profile-content__body">

					<div class="container container--medium">

						<div class="row">

							<?php $layout = apply_filters( 'grimlock_buddypress_group_layout', 'inside-9-3-cols-left' ); ?>

							<?php if ( in_array( $layout, array( 'inside-3-9-cols-left', 'inside-3-6-3-cols-left' ) ) ) : ?>
								<?php bp_get_template_part( 'bp-sidebar-1' ); ?>
							<?php endif; ?>

							<?php if ( in_array( $layout, array( 'inside-3-9-cols-left', 'inside-9-3-cols-left' ) ) ) {
								$col_classes = 'col-lg-9';
							}
							else if ( $layout === 'inside-3-6-3-cols-left' ) {
								$col_classes = 'col-lg-6';
							} ?>
							<div class="col-md-12 <?php echo esc_attr( $col_classes ); ?>">

								<div id="template-notices" role="alert" aria-atomic="true">
									<?php do_action( 'template_notices' ); ?>
								</div>

								<?php do_action( 'bp_before_group_body' );

								// Looking at home location.
								if ( bp_is_group_home() ) :

									if ( bp_group_is_visible() ) :

										if ( bp_is_active( 'activity' ) ) :
											bp_get_template_part( 'groups/single/activity' );
										elseif ( bp_is_active( 'members' ) ) :
											//bp_groups_members_template_part();
											grimlock_buddypress_groups_members_template_part();
										endif;

									else :

										do_action( 'bp_before_group_status_message' ); ?>

										<div id="message" class="info">
											<p><?php bp_group_status_message(); ?></p>
										</div>

										<?php do_action( 'bp_after_group_status_message' );

									endif;

								else :

									// Not looking at home.
									if ( bp_is_group_admin_page() ) :

										// Group Admin.
										bp_get_template_part( 'groups/single/admin' );

									elseif ( bp_is_group_activity() ) :

										// Group Activity.
										bp_get_template_part( 'groups/single/activity' );

									elseif ( bp_is_group_members() ) :

										// Group Members.
										grimlock_buddypress_groups_members_template_part();

									elseif ( bp_is_group_invites() ) :

										// Group Invitations.
										bp_get_template_part( 'groups/single/send-invites' );

									elseif ( bp_is_group_membership_request() ) :

										// Membership request.
										bp_get_template_part( 'groups/single/request-membership' );

									else :

										// Anything else (plugins mostly).
										bp_get_template_part( 'groups/single/plugins' );

									endif;

								endif;

								do_action( 'bp_after_group_body' ); ?>

							</div> <!-- .col-* -->

							<?php if ( in_array( $layout, array( 'inside-9-3-cols-left', 'inside-3-6-3-cols-left' ) ) ) : ?>
								<?php bp_get_template_part( 'bp-sidebar-2' ); ?>
							<?php endif; ?>

						</div> <!-- .row -->

					</div> <!-- .container -->

				</div> <!-- #item-body -->

				<?php do_action( 'bp_after_group_home_content' ); ?>

			</div>

			<?php
		endwhile;

	endif; ?>

</div> <!-- #buddypress -->

<!-- Modal admins -->
<?php bp_get_template_part( 'bp-group-admins' ); ?>
