<?php
/**
 * BuddyPress - Members Profile Change Avatar
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 * @version 3.0.0
 */

// @codingStandardsIgnoreFile
// Allow plugin text domain in theme.
?>

<h2 class="h3"><?php esc_html_e( 'Change Profile Photo', 'buddypress' ); ?></h2>

<?php do_action( 'bp_before_profile_avatar_upload_content' ); ?>

<?php if ( ! (int) bp_get_option( 'bp-disable-avatar-uploads' ) ) : ?>

	<div id="message" class="info">
		<p><?php esc_html_e( 'Your profile photo will be used on your profile and throughout the site.', 'grimlock-buddypress' ); ?></p>
	</div>

	<form action="" method="post" id="avatar-upload-form" class="standard-form" enctype="multipart/form-data">

		<?php if ( 'upload-image' === bp_get_avatar_admin_step() ) : ?>

			<?php wp_nonce_field( 'bp_avatar_upload' ); ?>
			<p><?php esc_html_e( 'Click below to select a JPG, GIF or PNG format photo from your computer and then click \'Upload Image\' to proceed.', 'buddypress' ); ?></p>

			<p id="avatar-upload">
				<label for="file" class="bp-screen-reader-text">
					<?php esc_html_e( 'Select an image', 'buddypress' ); ?>
				</label>
				<input type="file" name="file" id="file" />
				<input type="submit" name="upload" id="upload" value="<?php esc_attr_e( 'Upload Image', 'buddypress' ); ?>" />
				<input type="hidden" name="action" id="action" value="bp_avatar_upload" />
			</p>

			<?php if ( bp_get_user_has_avatar() ) : ?>
				<p><?php esc_html_e( "If you'd like to delete your current profile photo but not upload a new one, please use the delete profile photo button.", 'buddypress' ); ?></p>
				<p><a class="button edit" href="<?php bp_avatar_delete_link(); ?>"><?php esc_html_e( 'Delete My Profile Photo', 'buddypress' ); ?></a></p>
			<?php endif; ?>

		<?php endif; ?>

		<?php if ( 'crop-image' === bp_get_avatar_admin_step() ) : ?>

			<h5><?php esc_html_e( 'Crop Your New Profile Photo', 'buddypress' ); ?></h5>

			<img src="<?php bp_avatar_to_crop(); ?>" id="avatar-to-crop" data-no-lazy="1" class="avatar skip-lazy" alt="<?php esc_attr_e( 'Profile photo to crop', 'buddypress' ); ?>" />

			<div id="avatar-crop-pane">
				<img src="<?php bp_avatar_to_crop(); ?>" id="avatar-crop-preview" data-no-lazy="1" class="avatar skip-lazy" alt="<?php esc_attr_e( 'Profile photo preview', 'buddypress' ); ?>" />
			</div>

			<input type="submit" name="avatar-crop-submit" id="avatar-crop-submit" value="<?php esc_attr_e( 'Crop Image', 'buddypress' ); ?>" />

			<input type="hidden" name="image_src" id="image_src" value="<?php bp_avatar_to_crop_src(); ?>" />
			<input type="hidden" id="x" name="x" />
			<input type="hidden" id="y" name="y" />
			<input type="hidden" id="w" name="w" />
			<input type="hidden" id="h" name="h" />

			<?php wp_nonce_field( 'bp_avatar_cropstore' ); ?>

		<?php endif; ?>

	</form>

	<?php bp_avatar_get_templates(); ?>

<?php else : ?>

	<p><?php esc_html_e( 'Your profile photo will be used on your profile and throughout the site. To change your profile photo, please create an account with <a href="https://gravatar.com">Gravatar</a> using the same email address as you used to register with this site.', 'buddypress' ); ?></p>

<?php endif; ?>

<?php do_action( 'bp_after_profile_avatar_upload_content' ); ?>
