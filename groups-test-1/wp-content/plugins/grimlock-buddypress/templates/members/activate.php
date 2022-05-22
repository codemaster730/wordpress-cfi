<?php
/**
 * BuddyPress - Members Activate
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 * @version 3.2.0
 */

// @codingStandardsIgnoreFile
// Allow plugin text domain in theme.
?>

<div id="buddypress" class="m-0 p-0">
	<?php
	/**
	 * Fires before the display of the member activation page.
	 *
	 * @since 1.1.0
	 */
	do_action( 'bp_before_activation_page' ); ?>

	<div class="page" id="activate-page">

		<div class="container container--narrower">

			<div id="template-notices" role="alert" aria-atomic="true">
				<?php do_action( 'template_notices' ); ?>
			</div>

			<div class="card card-static p-4 p-md-5">

				<?php do_action( 'bp_before_activate_content' ); ?>

				<?php if ( bp_account_was_activated() ) : ?>

					<?php if ( isset( $_GET['e'] ) ) : ?>
						<p><?php esc_html_e( 'Your account was activated successfully! Your account details have been sent to you in a separate email.', 'buddypress' ); ?></p>
					<?php else : ?>
						<p><?php
							$allowed_html = array(
								'em'     => array( 'class'  => array() ),
								'i'      => array( 'class'  => array() ),
								'strong' => array( 'class'  => array() ),
								'ins'    => array( 'class'  => array() ),
								'del'    => array( 'class'  => array() ),
								'br'     => array( 'class'  => array() ),
								'span'   => array( 'class'  => array(), 'data-livestamp' => array() ),
								'a'      => array( 'class' => array(), 'href' => array(), 'title' => array() ),
							);
							/* translators: %s: Admin login URL */
							printf(
								wp_kses( __( 'Your account was activated successfully! You can now <a href="%s">log in</a> with the username and password you provided when you signed up.', 'buddypress' ), $allowed_html ),
								esc_url( wp_login_url( bp_get_root_domain() ) ) );
							?></p>
					<?php endif; ?>

					<?php
						printf(
							'<p><a class="btn btn-primary mt-2 mb-2" href="%1$s">%2$s</a></p>',
							esc_url( wp_login_url( bp_get_root_domain() ) ),
							esc_html__( 'Log In', 'buddypress' )
						);
					?>

				<?php else : ?>

					<p><?php esc_html_e( 'Please provide a valid activation key.', 'buddypress' ); ?></p>

					<form action="" method="post" class="standard-form" id="activation-form">

						<label for="key"><?php esc_html_e( 'Activation Key:', 'buddypress' ); ?></label>
						<input type="text" name="key" id="key" value="<?php echo esc_attr( bp_get_current_activation_key() ); ?>" />

						<p class="submit m-0 p-0">
							<input class="col-12 col-sm-auto" type="submit" name="submit" value="<?php esc_attr_e( 'Activate', 'buddypress' ); ?>" />
						</p>

					</form>

				<?php endif; ?>

				<?php do_action( 'bp_after_activate_content' ); ?>

			</div>

		</div>

	</div><!-- .page -->

	<?php

	/**
	 * Fires after the display of the member activation page.
	 *
	 * @since 1.1.0
	 */
	do_action( 'bp_after_activation_page' ); ?>

</div><!-- #buddypress -->
