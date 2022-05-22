<?php
/**
 * BuddyPress - Members Single Profile Edit
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 * @version 3.0.0
 */

// @codingStandardsIgnoreFile
// Allow plugin text domain in theme.
do_action( 'bp_before_profile_edit_content' );

if ( bp_has_profile( 'profile_group_id=' . bp_get_current_profile_group_id() ) ) :

	while ( bp_profile_groups() ) : bp_the_profile_group(); ?>

		<form action="<?php bp_the_profile_group_edit_form_action(); ?>" method="post" id="profile-edit-form" class="standard-form <?php bp_the_profile_group_slug(); ?>">

			<?php do_action( 'bp_before_profile_field_content' ); ?>

			<div class="row">

				<?php if ( bp_profile_has_multiple_groups() ) : ?>
					<div class="col-sm-auto col-lg-3 col-profile-edit-nav">
						<ul class="button-nav clearfix" aria-label="<?php esc_attr_e( 'Profile field groups', 'buddypress' ); ?>" role="navigation">
							<?php bp_profile_group_tabs(); ?>
						</ul>
					</div> <!-- .col-profile-edit-nav -->
				<?php endif; ?>

				<div class="col col-profile-edit-fields">

					<div class="card card-static">

						<h2 class="screen-profile__title h3">
							<?php
							/* translators: %s: Profile group name */
							printf( esc_html__( 'Editing "%s" Profile Group', 'buddypress' ), esc_html( bp_get_the_profile_group_name() ) ); ?>
						</h2>

						<?php while ( bp_profile_fields() ) : bp_the_profile_field(); ?>

							<div<?php bp_field_css_class( 'editfield' ); ?>>
								<fieldset>

									<?php
									$field_type = bp_xprofile_create_field_type( bp_get_the_profile_field_type() );
									$field_type->edit_field_html();
									do_action( 'bp_custom_profile_edit_fields_pre_visibility' ); ?>

									<?php if ( bp_current_user_can( 'bp_xprofile_change_field_visibility' ) ) : ?>
										<p class="field-visibility-settings-toggle" id="field-visibility-settings-toggle-<?php bp_the_profile_field_id(); ?>">
											<span id="<?php bp_the_profile_field_input_name(); ?>-2">
											<?php
											printf(
												/* translators: %s: Profile field visibility level label */
												esc_html__( 'This field can be seen by: %s', 'buddypress' ),
												'<span class="current-visibility-level">' . esc_html( bp_get_the_profile_field_visibility_level_label() ) . '</span>'
											); ?>
											</span>
											<button type="button" class="visibility-toggle-link" aria-describedby="<?php bp_the_profile_field_input_name(); ?>-2" aria-expanded="false"><?php echo esc_html_x( 'Change', 'Change profile field visibility level', 'buddypress' ); ?></button>
										</p>

										<div class="field-visibility-settings" id="field-visibility-settings-<?php bp_the_profile_field_id(); ?>">
											<fieldset>
												<legend><?php esc_html_e( 'Who can see this field?', 'buddypress' ); ?></legend>

												<?php bp_profile_visibility_radio_buttons(); ?>

											</fieldset>
											<button type="button" class="field-visibility-settings-close"><?php esc_html_e( 'Close', 'buddypress' ); ?></button>
										</div>
									<?php else : ?>
										<div class="field-visibility-settings-notoggle" id="field-visibility-settings-toggle-<?php bp_the_profile_field_id(); ?>">
											<?php
											printf(
												/* translators: %s: Profile field visibility level label */
												esc_html__( 'This field can be seen by: %s', 'buddypress' ),
												'<span class="current-visibility-level">' . esc_html( bp_get_the_profile_field_visibility_level_label() ) . '</span>'
											); ?>
										</div>
									<?php endif ?>

									<?php do_action( 'bp_custom_profile_edit_fields' ); ?>

								</fieldset>
							</div>

						<?php endwhile; ?>

						<?php do_action( 'bp_after_profile_field_content' ); ?>

						<div class="submit">
							<input type="submit" name="profile-group-edit-submit" id="profile-group-edit-submit" value="<?php esc_attr_e( 'Save Changes', 'buddypress' ); ?> " />
						</div>

						<input type="hidden" name="field_ids" id="field_ids" value="<?php bp_the_profile_field_ids(); ?>" />

						<?php wp_nonce_field( 'bp_xprofile_edit' ); ?>

					</div> <!-- .card-->

				</div><!-- .col-profile-edit-fields -->

			</div> <!-- .row -->

		</form>

		<?php
	endwhile;

endif; ?>

<?php do_action( 'bp_after_profile_edit_content' ); ?>
