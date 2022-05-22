<?php
/**
 * Grimlock BuddyPress template functions for Youzer.
 *
 * @package grimlock-buddypress
 */

/**
 * Display the login form in a modal
 *
 * @param array $args The array of arguments from the component
 */
function grimlock_buddypress_youzer_grimlock_login_form_modal( $args ) {
	if ( ! is_user_logged_in() ) : ?>

		<div class="modal fade" id="grimlock-login-form-modal" tabindex="-1" role="dialog" aria-labelledby="grimlock-login-form-modal-title" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
					<div class="modal-header p-0 m-0">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<?php echo do_shortcode( '[youzer_login]' ); ?>
					</div>
				</div>
			</div>
		</div>

	<?php endif;
}
