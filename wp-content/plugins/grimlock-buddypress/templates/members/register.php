<?php
/**
 * BuddyPress - Members Register
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 * @version 3.0.0
 */

// @codingStandardsIgnoreFile
// Allow plugin text domain in theme and unescaped template tags.
?>

<div id="buddypress">

	<?php do_action( 'bp_before_register_page' ); ?>

	<div class="page" id="register-page">

		<ul class="nav nav-pills nav-register mb-4 justify-content-center" role="tablist">
			<li class="nav-item mx-2">
				<a class="btn btn-link active" id="pills-register-tab" data-toggle="pill" href="#pills-register" role="tab"><span class="d-block p-2"><?php esc_html_e( 'Register', 'buddypress' ); ?></span></a>
			</li>
			<li class="nav-item mx-2">
				<a class="btn btn-link" id="pills-login-tab" data-toggle="pill" href="#pills-login" role="tab"><span class="d-block p-2"><?php esc_html_e( 'Log In', 'buddypress' ); ?></span></a>
			</li>
		</ul>

		<div class="tab-content" id="pills-tabContent">

			<div class="tab-pane tab-pane--register fade show active" id="pills-register" role="tabpanel">

				<form action="" name="signup_form" id="signup_form" class="standard-form clearfix" method="post" enctype="multipart/form-data">

					<?php if ( 'registration-disabled' === bp_get_current_signup_step() ) : ?>

						<div class="container container--narrow">

							<div id="template-notices" role="alert" aria-atomic="true">
								<?php do_action( 'template_notices' ); ?>
							</div>

							<?php do_action( 'bp_before_registration_disabled' ); ?>

							<p><?php esc_html_e( 'User registration is currently not allowed.', 'buddypress' ); ?></p>

							<?php do_action( 'bp_after_registration_disabled' ); ?>

						</div>

					<?php endif; ?>

					<?php if ( 'request-details' === bp_get_current_signup_step() ) : ?>

						<div class="container container--narrow">

							<div id="template-notices" role="alert" aria-atomic="true">
								<?php do_action( 'template_notices' ); ?>
							</div>

							<p class="d-none"><?php esc_html_e( 'Registering for this site is easy. Just fill in the fields below, and we\'ll get a new account set up for you in no time.', 'buddypress' ); ?></p>

							<div class="text-center">
								<?php do_action( 'bp_before_account_details_fields' ); ?>
							</div>

							<div class="register-sections">

								<div class="register-section" id="basic-details-section">

									<h4 class="entry-title">
										<?php esc_html_e( 'Account Details', 'buddypress' ); ?>
									</h4>

									<label for="signup_username"><?php esc_html_e( 'Username', 'buddypress' ); ?> <?php esc_html_e( '(required)', 'buddypress' ); ?></label>
									<?php do_action( 'bp_signup_username_errors' ); ?>
									<input type="text" name="signup_username" id="signup_username" value="<?php bp_signup_username_value(); ?>" <?php bp_form_field_attributes( 'username' ); ?>/>

									<label for="signup_email"><?php esc_html_e( 'Email Address', 'buddypress' ); ?> <?php esc_html_e( '(required)', 'buddypress' ); ?></label>
									<?php do_action( 'bp_signup_email_errors' ); ?>
									<input type="email" name="signup_email" id="signup_email" value="<?php bp_signup_email_value(); ?>" <?php bp_form_field_attributes( 'email' ); ?>/>

									<label for="signup_password"><?php esc_html_e( 'Choose a Password', 'buddypress' ); ?> <?php esc_html_e( '(required)', 'buddypress' ); ?></label>
									<?php do_action( 'bp_signup_password_errors' ); ?>
									<input type="password" name="signup_password" id="signup_password" value="" class="password-entry" <?php bp_form_field_attributes( 'password' ); ?>/>
									<div id="pass-strength-result"></div>

									<label for="signup_password_confirm"><?php esc_html_e( 'Confirm Password', 'buddypress' ); ?> <?php esc_html_e( '(required)', 'buddypress' ); ?></label>
									<?php do_action( 'bp_signup_password_confirm_errors' ); ?>
									<input type="password" name="signup_password_confirm" id="signup_password_confirm" value="" class="password-entry-confirm" <?php bp_form_field_attributes( 'password' ); ?>/>

									<?php do_action( 'bp_account_details_fields' ); ?>

								</div><!-- #basic-details-section -->

								<?php do_action( 'bp_after_account_details_fields' ); ?>

								<?php if ( bp_is_active( 'xprofile' ) ) : ?>

									<?php do_action( 'bp_before_signup_profile_fields' ); ?>

									<div class="register-section" id="profile-details-section">

										<h4 class="entry-title">
											<?php esc_html_e( 'Profile Details', 'buddypress' ); ?>
										</h4>

										<?php if ( bp_is_active( 'xprofile' ) ) :

											if ( bp_has_profile( bp_xprofile_signup_args() ) ) :

												while ( bp_profile_groups() ) : bp_the_profile_group();

													while ( bp_profile_fields() ) : bp_the_profile_field(); ?>

														<div<?php bp_field_css_class( 'editfield' ); ?>>
															<fieldset>

																<?php
																$field_type = bp_xprofile_create_field_type( bp_get_the_profile_field_type() );
																$field_type->edit_field_html();

																do_action( 'bp_custom_profile_edit_fields_pre_visibility' );

																if ( bp_current_user_can( 'bp_xprofile_change_field_visibility' ) ) : ?>
																	<p class="field-visibility-settings-toggle" id="field-visibility-settings-toggle-<?php bp_the_profile_field_id(); ?>"><span id="<?php bp_the_profile_field_input_name(); ?>-2">
	                                                                    <?php
	                                                                    /* translators: %s: Profile field visibility level */
	                                                                    printf( esc_html__( 'This field can be seen by: %s', 'buddypress' ), '<span class="current-visibility-level">' . esc_html( bp_get_the_profile_field_visibility_level_label() ) . '</span>' ); ?></span>
																		<button type="button" class="visibility-toggle-link"><?php _ex( 'Change', 'Change profile field visibility level', 'buddypress' ); ?></button>
																	</p>

																	<div class="field-visibility-settings" id="field-visibility-settings-<?php bp_the_profile_field_id(); ?>">
																		<fieldset>
																			<legend><?php esc_html_e( 'Who can see this field?', 'buddypress' ); ?></legend>
																			<?php bp_profile_visibility_radio_buttons(); ?>
																		</fieldset>
																		<button type="button" class="field-visibility-settings-close"><?php esc_html_e( 'Close', 'buddypress' ); ?></button>
																	</div>
																<?php else : ?>
																	<p class="field-visibility-settings-notoggle" id="field-visibility-settings-toggle-<?php bp_the_profile_field_id(); ?>"><?php
																		/* translators: %s: Profile visibility level label */
																		printf( esc_html__( 'This field can be seen by: %s', 'buddypress' ), '<span class="current-visibility-level">' . esc_html( bp_get_the_profile_field_visibility_level_label() ) . '</span>' ); ?></p>
																<?php endif ?>

																<?php do_action( 'bp_custom_profile_edit_fields' ); ?>

															</fieldset>
														</div>

													<?php endwhile; ?>

													<input type="hidden" name="signup_profile_field_ids" id="signup_profile_field_ids" value="<?php bp_the_profile_field_ids(); ?>" />

												<?php endwhile;

											endif;

										endif; ?>

										<?php do_action( 'bp_signup_profile_fields' ); ?>

									</div><!-- #profile-details-section -->

									<?php do_action( 'bp_after_signup_profile_fields' ); ?>

								<?php endif; ?>

								<?php if ( bp_get_blog_signup_allowed() ) : ?>

									<?php do_action( 'bp_before_blog_details_fields' ); ?>

									<?php /***** Blog Creation Details ******/ ?>

									<div class="register-section" id="blog-details-section">

										<h4 class="entry-title">
											<?php esc_html_e( 'Blog Details', 'buddypress' ); ?>
										</h4>

										<p><label for="signup_with_blog"><input type="checkbox" name="signup_with_blog" id="signup_with_blog" value="1"<?php if ( (int) bp_get_signup_with_blog_value() ) : ?> checked="checked"<?php endif; ?> /> <?php esc_html_e( 'Yes, I\'d like to create a new site', 'buddypress' ); ?></label></p>

										<div id="blog-details"<?php if ( (int) bp_get_signup_with_blog_value() ) : ?>class="show"<?php endif; ?>>

											<label for="signup_blog_url"><?php esc_html_e( 'Blog URL', 'buddypress' ); ?> <?php esc_html_e( '(required)', 'buddypress' ); ?></label>
											<?php do_action( 'bp_signup_blog_url_errors' ); ?>

											<?php if ( is_subdomain_install() ) : ?>
												http:// <input type="text" name="signup_blog_url" id="signup_blog_url" value="<?php bp_signup_blog_url_value(); ?>" /> .<?php bp_signup_subdomain_base(); ?>
											<?php else : ?>
												<span class="small-description"><?php echo esc_url( home_url( '/' ) ); ?></span>
												<input type="text" name="signup_blog_url" id="signup_blog_url" value="<?php bp_signup_blog_url_value(); ?>" />
											<?php endif; ?>

											<label for="signup_blog_title"><?php esc_html_e( 'Site Title', 'buddypress' ); ?> <?php esc_html_e( '(required)', 'buddypress' ); ?></label>
											<?php do_action( 'bp_signup_blog_title_errors' ); ?>
											<input type="text" name="signup_blog_title" id="signup_blog_title" value="<?php bp_signup_blog_title_value(); ?>" />

											<fieldset class="register-site">
												<legend class="label"><?php esc_html_e( 'Privacy: I would like my site to appear in search engines, and in public listings around this network.', 'buddypress' ); ?></legend>
												<?php do_action( 'bp_signup_blog_privacy_errors' ); ?>

												<label for="signup_blog_privacy_public">
													<input type="radio" name="signup_blog_privacy" id="signup_blog_privacy_public" value="public" <?php echo 'public' === bp_get_signup_blog_privacy_value() || ! bp_get_signup_blog_privacy_value() ? 'checked="checked"' : ''; ?> /> <?php esc_html_e( 'Yes', 'buddypress' ); ?>
												</label>

												<label for="signup_blog_privacy_private">
													<input type="radio" name="signup_blog_privacy" id="signup_blog_privacy_private" value="private" <?php echo 'private' === bp_get_signup_blog_privacy_value() ? 'checked="checked"' : ''; ?> /> <?php esc_html_e( 'No', 'buddypress' ); ?>
												</label>
											</fieldset>

											<?php do_action( 'bp_blog_details_fields' ); ?>

										</div>

									</div><!-- #blog-details-section -->

									<?php do_action( 'bp_after_blog_details_fields' ); ?>

								<?php endif; ?>

							</div>

							<?php

								/**
								 * Fires before the display of the registration submit buttons.
								 *
								 * @since 1.1.0
								 */
								do_action( 'bp_before_registration_submit_buttons' );

								if ( function_exists( 'bp_get_membership_requests_required' ) && bp_get_membership_requests_required() ) {
									$button_text = __( 'Submit Request', 'buddypress' );
								} else {
									$button_text = __( 'Complete Sign Up', 'buddypress' );
								}
							?>

							<div class="submit">
								<input type="submit" name="signup_submit" id="signup_submit" class="btn-lg col-12 col-sm-auto" value="<?php echo esc_attr( $button_text ); ?>" />
							</div>

						</div> <!-- .container -->

						<?php do_action( 'bp_after_registration_submit_buttons' ); ?>

						<?php wp_nonce_field( 'bp_new_signup' ); ?>

					<?php endif; // request-details signup step. ?>

					<?php if ( 'completed-confirmation' === bp_get_current_signup_step() ) : ?>

						<div class="container container--narrow">

							<div id="template-notices" role="alert" aria-atomic="true">
								<?php do_action( 'template_notices' ); ?>
							</div>

							<?php do_action( 'bp_before_registration_confirmed' ); ?>

							<div id="template-notices" role="alert" aria-atomic="true" class="card card-static p-4 p-md-5">
								<?php if ( function_exists( 'bp_get_membership_requests_required' ) && bp_get_membership_requests_required() ) : ?>
									<p class="m-0"><?php esc_html_e( 'You have successfully submitted your membership request! Our site moderators will review your submission and send you an activation email if your request is approved.', 'buddypress' ); ?></p>
								<?php elseif ( bp_registration_needs_activation() ) : ?>
									<p class="m-0"><?php esc_html_e( 'You have successfully created your account! To begin using this site you will need to activate your account via the email we have just sent to your address.', 'buddypress' ); ?></p>
								<?php else : ?>
									<p class="m-0"><?php esc_html_e( 'You have successfully created your account! Please log in using the username and password you have just created.', 'buddypress' ); ?></p>
								<?php endif; ?>
							</div>

							<?php do_action( 'bp_after_registration_confirmed' ); ?>

						</div> <!-- .container -->

					<?php endif; ?>

					<?php do_action( 'bp_custom_signup_steps' ); ?>

				</form>

			</div><!-- .tab-pane -->

			<div class="tab-pane tab-pane--login fade" id="pills-login" role="tabpanel">
				<?php if ( class_exists( 'LoginWithAjax') ) : ?>
					<?php echo do_shortcode( '[login-with-ajax template="divs-only"]' ); ?>
				<?php else: ?>
					<div class="container container--narrower">
						<div class="login-form-wrapper">
							<?php wp_login_form(); ?>
						</div>
					</div>
				<?php endif; ?>
			</div> <!-- .tab-pane -->

		</div> <!-- .tab-content -->

	</div> <!-- #register-page -->

	<?php do_action( 'bp_after_register_page' ); ?>

</div><!-- #buddypress -->
