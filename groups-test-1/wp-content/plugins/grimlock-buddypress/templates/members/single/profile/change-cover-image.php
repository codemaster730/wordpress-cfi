<?php
/**
 * BuddyPress - Members Profile Change Cover Image
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 * @version 3.0.0
 */

// @codingStandardsIgnoreFile
// Allow plugin text domain in theme.
?>

<h2 class="h3"><?php esc_html_e( 'Change Cover Image', 'buddypress' ); ?></h2>

<?php do_action( 'bp_before_profile_edit_cover_image' ); ?>

<p><?php esc_html_e( 'Your Cover Image will be used to customize the header of your profile.', 'buddypress' ); ?></p>

<?php bp_attachments_get_template_part( 'cover-images/index' ); ?>

<?php do_action( 'bp_after_profile_edit_cover_image' ); ?>
