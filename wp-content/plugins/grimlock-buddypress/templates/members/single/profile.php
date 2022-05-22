<?php
/**
 * BuddyPress - Users Profile
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 * @version 3.0.0
 */

// @codingStandardsIgnoreFile
// Allow plugin text domain in theme.
?>
<?php if ( bp_is_my_profile() ) : ?>
	<div id="subnav" aria-label="<?php esc_attr_e( 'Member secondary navigation', 'buddypress' ); ?>" role="navigation" class="d-flex flex-column flex-lg-row mb-4 mt-0">
		<div class="item-list-tabs primary-list-tabs no-ajax">
			<ul class="item-list-tabs-ul clearfix"><?php bp_get_options_nav(); ?></ul>
		</div>
	</div>
<?php endif; ?>

<?php do_action( 'bp_before_profile_content' ); ?>

<div class="screen-profile card card-static">

	<?php
	switch ( bp_current_action() ) :

		// Edit.
		case 'edit':
			bp_get_template_part( 'members/single/profile/edit' );
			break;

		// Change Avatar.
		case 'change-avatar':
			bp_get_template_part( 'members/single/profile/change-avatar' );
			break;

		// Change Cover Image.
		case 'change-cover-image':
			bp_get_template_part( 'members/single/profile/change-cover-image' );
			break;

		// Compose.
		case 'public':
			if ( bp_is_active( 'xprofile' ) ) :
				// Display XProfile.
				bp_get_template_part( 'members/single/profile/profile-loop' );

			else :
				// Display WordPress profile (fallback).
				bp_get_template_part( 'members/single/profile/profile-wp' );
			endif;
			break;

		// Any other.
		default:
			bp_get_template_part( 'members/single/plugins' );
			break;

	endswitch; ?>
</div><!-- .profile -->

<?php do_action( 'bp_after_profile_content' ); ?>
