<?php
/**
 * BuddyPress - Members Home
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 * @version 3.0.0
 */

// @codingStandardsIgnoreFile
// Allow plugin text domain in theme. ?>

<div id="buddypress" class="<?php if ( class_exists( 'Mp_BP_Match' ) && ! bp_is_my_profile() ) : ?>bp-match-displayed<?php endif; ?> <?php if ( function_exists( 'bp_follow_init' ) ) : ?>bp-follow-displayed<?php endif; ?> <?php if ( class_exists( 'BP_Featured_Members' ) && ! empty( get_user_meta( bp_displayed_user_id(), '_is_featured', true ) ) ) : ?>is-featured-member<?php endif; ?>">

	<?php do_action( 'bp_before_member_home_content' ); ?>

	<div id="item-header" role="complementary">
		<?php bp_get_template_part( 'members/single/cover-image-header' ); ?>
	</div> <!-- #item-header -->

	<div class="profile-content">

		<div id="profile-content__nav" class="item-list-tabs no-ajax"  aria-label="<?php esc_attr_e( 'Member primary navigation', 'buddypress' ); ?>" role="navigation">
			<div class="container container--medium">
				<div class="row">
					<div class="profile-content__nav-wrapper col-12">
						<ul class="main-nav priority-ul clearfix d-md-inline-block">
							<?php bp_get_displayed_user_nav(); ?>
							<?php do_action( 'bp_member_options_nav' ); ?>
						</ul> <!-- .main-nav -->
						<ul class="main-nav settings-nav d-none d-md-inline-block">
							<?php grimlock_buddypress_get_displayed_user_secondary_nav(); ?>
						</ul> <!-- .main-nav -->
					</div> <!-- .profile-content__nav-wrapper -->
				</div>
			</div> <!-- .container -->
		</div> <!-- #profile-content__nav -->

		<div id="item-body" class="profile-content__body">

			<div class="container container--medium">

				<div class="row">

					<?php $layout = apply_filters( 'grimlock_buddypress_profile_layout', 'inside-9-3-cols-left' ); ?>

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

						<?php do_action( 'bp_before_member_body' );

						if ( bp_is_user_front() ) :
							bp_displayed_user_front_template_part();

						elseif ( bp_is_user_activity() ) :
							bp_get_template_part( 'members/single/activity' );

						elseif ( bp_is_user_blogs() ) :
							bp_get_template_part( 'members/single/blogs'    );

						elseif ( bp_is_user_friends() ) :
							bp_get_template_part( 'members/single/friends'  );

						elseif ( bp_is_user_groups() ) :
							bp_get_template_part( 'members/single/groups'   );

						elseif ( bp_is_user_messages() ) :
							bp_get_template_part( 'members/single/messages' );

						elseif ( bp_is_user_profile() ) :
							bp_get_template_part( 'members/single/profile'  );

						elseif ( bp_is_user_notifications() ) :
							bp_get_template_part( 'members/single/notifications' );

						elseif ( bp_is_user_members_invitations() ) :
							bp_get_template_part( 'members/single/invitations' );

						elseif ( bp_is_user_settings() ) :
							bp_get_template_part( 'members/single/settings' );

						else :
							// If nothing sticks, load a generic template.
							bp_get_template_part( 'members/single/plugins'  );

						endif;

						do_action( 'bp_after_member_body' ); ?>
					</div>

					<?php if ( in_array( $layout, array( 'inside-9-3-cols-left', 'inside-3-6-3-cols-left' ) ) ) : ?>
						<?php bp_get_template_part( 'bp-sidebar-2' ); ?>
					<?php endif; ?>

				</div> <!-- .row -->

			</div> <!-- .container -->

		</div> <!-- #item-body -->

	</div> <!-- .profile-content -->

	<?php do_action( 'bp_after_member_home_content' ); ?>

</div> <!-- #buddypress -->
