<?php
/**
 * BuddyPress - Groups Create
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 * @version 3.0.0
 */

// @codingStandardsIgnoreFile
// Allow plugin text domain in theme.

do_action( 'bp_before_create_group_page' ); ?>

<div id="buddypress">

	<?php do_action( 'bp_before_create_group_content_template' ); ?>

	<header class="page-header entry-header text-center pt-4 pb-4">
		<h1 class="page-tite entry-title"><?php esc_html_e( 'Create a Group', 'buddypress' ); ?></h1>
	</header>

	<form action="<?php bp_group_creation_form_action(); ?>" method="post" id="create-group-form" class="standard-form" enctype="multipart/form-data">

		<?php do_action( 'bp_before_create_group' ); ?>

		<div class="container pl-0 pr-0">

			<div id="subnav" class="d-flex flex-column flex-lg-row mb-3 mb-md-4 mt-0 text-center" aria-label="<?php esc_attr_e( 'Member secondary navigation', 'buddypress' ); ?>" role="navigation">

				<div class="item-list-tabs primary-list-tabs no-ajax" id="group-create-tabs">
					<ul class="item-list-tabs-ul d-flex justify-content-between w-100 flex-column flex-md-row">
						<?php bp_group_creation_tabs(); ?>
					</ul><!-- .item-list-tabs-ul -->
				</div> <!-- .item-list-tabs -->

			</div> <!-- #subnav -->

		</div> <!-- .container -->

		<div class="container pl-0 pr-0">

			<div id="template-notices" role="alert" aria-atomic="true">
				<?php do_action( 'template_notices' ); ?>
			</div>

			<div class="item-body card card-static p-4" id="group-create-body">

				<?php /* Group creation step 1: Basic group details */ ?>
				<?php if ( bp_is_group_creation_step( 'group-details' ) ) : ?>

					<h2><?php esc_html_e( 'Group Details', 'buddypress' ); ?></h2>

					<?php do_action( 'bp_before_group_details_creation_step' ); ?>

					<label for="group-name"><?php esc_html_e( 'Group Name (required)', 'buddypress' ); ?></label>
					<input type="text" name="group-name" id="group-name" aria-required="true" value="<?php echo esc_attr( bp_get_new_group_name() ); ?>" />

					<label for="group-desc"><?php esc_html_e( 'Group Description (required)', 'buddypress' ); ?></label>
					<textarea name="group-desc" id="group-desc" aria-required="true"><?php bp_new_group_description(); ?></textarea>

					<?php
					do_action( 'bp_after_group_details_creation_step' );
					do_action( 'groups_custom_group_fields_editable' ); // @Deprecated
					wp_nonce_field( 'groups_create_save_group-details' ); ?>

				<?php endif; ?>

				<?php /* Group creation step 2: Group settings */ ?>
				<?php if ( bp_is_group_creation_step( 'group-settings' ) ) : ?>

					<h2><?php esc_html_e( 'Group Settings', 'buddypress' ); ?></h2>

					<?php do_action( 'bp_before_group_settings_creation_step' ); ?>

					<fieldset class="group-create-privacy mt-3">

						<legend><?php esc_html_e( 'Privacy Options', 'buddypress' ); ?></legend>

						<div class="radio">

							<div class="custom-control custom-radio">
								<input type="radio" class="custom-control-input" name="group-status" id="group-status-public" value="public" <?php echo 'public' === bp_get_new_group_status() || ! bp_get_new_group_status() ? 'checked="checked"' : ''; ?> aria-describedby="public-group-description" />
								<label class="custom-control-label" for="group-status-public"><?php esc_html_e( 'This is a public group', 'buddypress' ); ?></label>
							</div> <!-- .custom-control -->

							<ul id="public-group-description">
								<li><?php esc_html_e('Any site member can join this group.', 'buddypress' ); ?></li>
								<li><?php esc_html_e('This group will be listed in the groups directory and in search results.', 'buddypress' ); ?></li>
								<li><?php esc_html_e('Group content and activity will be visible to any site member.', 'buddypress' ); ?></li>
							</ul>

							<div class="custom-control custom-radio">
								<input type="radio" class="custom-control-input" name="group-status" id="group-status-private" value="private" <?php echo 'private' === bp_get_new_group_status() ? 'checked="checked"' : ''; ?> aria-describedby="private-group-description" />
								<label class="custom-control-label" for="group-status-private"><?php esc_html_e('This is a private group', 'buddypress' ); ?></label>
							</div> <!-- .custom-control -->

							<ul id="private-group-description">
								<li><?php esc_html_e('Only users who request membership and are accepted can join the group.', 'buddypress' ); ?></li>
								<li><?php esc_html_e('This group will be listed in the groups directory and in search results.', 'buddypress' ); ?></li>
								<li><?php esc_html_e('Group content and activity will only be visible to members of the group.', 'buddypress' ); ?></li>
							</ul>

							<div class="custom-control custom-radio">
								<input type="radio" class="custom-control-input" name="group-status" id="group-status-hidden" value="hidden" <?php echo 'hidden' === bp_get_new_group_status() ? 'checked="checked"' : ''; ?> aria-describedby="hidden-group-description" />
								<label class="custom-control-label" for="group-status-hidden"><?php esc_html_e('This is a hidden group', 'buddypress' ); ?></label>
							</div> <!-- .custom-control -->

							<ul id="hidden-group-description" class="mb-0">
								<li><?php esc_html_e('Only users who are invited can join the group.', 'buddypress' ); ?></li>
								<li><?php esc_html_e('This group will not be listed in the groups directory or search results.', 'buddypress' ); ?></li>
								<li><?php esc_html_e('Group content and activity will only be visible to members of the group.', 'buddypress' ); ?></li>
							</ul>

						</div> <!-- .radio -->

					</fieldset>

					<?php // Group type selection. ?>
					<?php if ( $group_types = bp_groups_get_group_types( array( 'show_in_create_screen' => true ), 'objects' ) ) : ?>

						<fieldset class="group-create-types mt-4">

							<legend><?php esc_html_e('Group Types', 'buddypress' ); ?></legend>

							<p><?php esc_html_e('Select the types this group should be a part of.', 'buddypress' ); ?></p>

							<?php foreach ( $group_types as $type ) : ?>
								<div class="checkbox">
									<label for="<?php printf( 'group-type-%s', esc_attr( $type->name ) ); ?>"><input type="checkbox" name="group-types[]" id="<?php printf( 'group-type-%s', esc_attr( $type->name ) ); ?>" value="<?php echo esc_attr( $type->name ); ?>" <?php checked( true, ! empty( $type->create_screen_checked ) ); ?> /> <?php echo esc_html( $type->labels['name'] ); ?>
										<?php
										if ( ! empty( $type->description ) ) :
											/* translators: %s: Group description */
											printf( esc_html__( '&ndash; %s', 'buddypress' ), '<span class="bp-group-type-desc">' . esc_html( $type->description ) . '</span>' );
										endif; ?>
									</label>
								</div> <!-- .checkbox -->

							<?php endforeach; ?>

						</fieldset>

					<?php endif; ?>

					<fieldset class="group-create-invitations mt-4">

						<legend><?php esc_html_e('Group Invitations', 'buddypress' ); ?></legend>

						<p><?php esc_html_e('Which members of this group are allowed to invite others?', 'buddypress' ); ?></p>

						<div class="radio">

							<div class="custom-control custom-radio">
								<input type="radio" class="custom-control-input" name="group-invite-status" id="group-invite-status-members" value="members"<?php bp_group_show_invite_status_setting( 'members' ); ?> />
								<label class="custom-control-label mb-2" for="group-invite-status-members"><?php esc_html_e('All group members', 'buddypress' ); ?></label>
							</div> <!-- .custom-control -->

							<div class="custom-control custom-radio">
								<input type="radio" class="custom-control-input" name="group-invite-status" id="group-invite-status-mods" value="mods"<?php bp_group_show_invite_status_setting( 'mods' ); ?> />
								<label class="custom-control-label mb-2" for="group-invite-status-mods"><?php esc_html_e('Group admins and mods only', 'buddypress' ); ?></label>
							</div> <!-- .custom-control -->

							<div class="custom-control custom-radio">
								<input type="radio" class="custom-control-input" name="group-invite-status" id="group-invite-status-admins" value="admins"<?php bp_group_show_invite_status_setting( 'admins' ); ?> />
								<label class="custom-control-label" for="group-invite-status-admins"><?php esc_html_e('Group admins only', 'buddypress' ); ?></label>
							</div> <!-- .custom-control -->

						</div> <!-- .radio -->

					</fieldset>

					<?php do_action( 'bp_after_group_settings_creation_step' ); ?>

					<?php wp_nonce_field( 'groups_create_save_group-settings' ); ?>

				<?php endif; ?>

				<?php /* Group creation step 3: Avatar Uploads */ ?>
				<?php if ( bp_is_group_creation_step( 'group-avatar' ) ) : ?>

					<h2 class="bp-screen-reader-text"><?php
						/* translators: Accessibility text */
						esc_html_e('Group Avatar', 'buddypress' );
						?></h2>

					<?php do_action( 'bp_before_group_avatar_creation_step' ); ?>

					<?php if ( 'upload-image' === bp_get_avatar_admin_step() ) : ?>

						<div class="row mb-2 align-items-center bp-avatar-preview">

							<div class="left-menu col-6 col-sm-3 col-md-2 m-md-0 mb-4 ml-auto mr-auto text-center">
								<div class="avatar-round-ratio big">
									<?php bp_new_group_avatar( 'type=thumb' ); ?>
								</div>
							</div><!-- .left-menu -->

							<div class="col-sm-9 col-md-10 m-0 main-column">

								<div class="bg-black-faded rounded-card p-3">
									<p><?php esc_html_e( 'Upload an image to use as a profile photo for this group. The image will be shown on the main group page, and in search results.', 'buddypress' ); ?></p>
									<p>
										<label for="file" class="bp-screen-reader-text">
											<?php esc_html_e('Select an image', 'buddypress' ); ?>
										</label>
										<input type="file" name="file" id="file" />
										<input type="submit" name="upload" id="upload" value="<?php esc_attr_e( 'Upload Image', 'buddypress' ); ?>" />
										<input type="hidden" name="action" id="action" value="bp_avatar_upload" />
									</p>
									<p><?php esc_html_e( 'To skip the group profile photo upload process, hit the "Next Step" button.', 'buddypress' ); ?></p>
								</div>

							</div><!-- .main-column -->

						</div><!-- .row -->

						<div class="row">
							<div class="col-sm-12 main-column m-0">
								<?php bp_avatar_get_templates(); ?>
							</div>
						</div>

					<?php endif; ?>

					<?php if ( 'crop-image' === bp_get_avatar_admin_step() ) : ?>

						<h4><?php esc_html_e('Crop Group Profile Photo', 'buddypress' ); ?></h4>

						<img src="<?php bp_avatar_to_crop(); ?>" id="avatar-to-crop" class="avatar" alt="<?php esc_attr_e('Profile photo to crop', 'buddypress' ); ?>" />

						<div id="avatar-crop-pane">
							<img src="<?php bp_avatar_to_crop(); ?>" id="avatar-crop-preview" class="avatar" alt="<?php esc_attr_e( 'Profile photo preview', 'buddypress' ); ?>" />
						</div>

						<input type="submit" name="avatar-crop-submit" id="avatar-crop-submit" value="<?php esc_attr_e( 'Crop Image', 'buddypress' ); ?>" />

						<input type="hidden" name="image_src" id="image_src" value="<?php bp_avatar_to_crop_src(); ?>" />
						<input type="hidden" name="upload" id="upload" />
						<input type="hidden" id="x" name="x" />
						<input type="hidden" id="y" name="y" />
						<input type="hidden" id="w" name="w" />
						<input type="hidden" id="h" name="h" />

					<?php endif; ?>

					<?php do_action( 'bp_after_group_avatar_creation_step' ); ?>

					<?php wp_nonce_field( 'groups_create_save_group-avatar' ); ?>

				<?php endif; ?>

				<?php /* Group creation step 4: Cover image */ ?>
				<?php if ( bp_is_group_creation_step( 'group-cover-image' ) ) : ?>

					<h2 class="bp-screen-reader-text">
						<?php esc_html_e( 'Cover Image', 'buddypress' ); ?>
					</h2>

					<?php do_action( 'bp_before_group_cover_image_creation_step' ); ?>

					<div id="header-cover-image"></div>

					<p><?php esc_html_e( 'The Cover Image will be used to customize the header of your group.', 'buddypress' ); ?></p>

					<?php bp_attachments_get_template_part( 'cover-images/index' ); ?>

					<?php do_action( 'bp_after_group_cover_image_creation_step' ); ?>

					<?php wp_nonce_field( 'groups_create_save_group-cover-image' ); ?>

				<?php endif; ?>

				<?php /* Group creation step 5: Invite friends to group */ ?>
				<?php if ( bp_is_group_creation_step( 'group-invites' ) ) : ?>

					<h2 class="bp-screen-reader-text">
						<?php esc_html_e( 'Group Invites', 'buddypress' ); ?>
					</h2>

					<?php do_action( 'bp_before_group_invites_creation_step' ); ?>

					<?php if ( bp_is_active( 'friends' ) && bp_get_total_friend_count( bp_loggedin_user_id() ) ) : ?>

						<div class="row">

							<div class="left-menu col-sm-3 m-0">

								<div id="invite-list">
									<ul>
										<?php bp_new_group_invite_friend_list(); ?>
									</ul>

									<?php wp_nonce_field( 'groups_invite_uninvite_user', '_wpnonce_invite_uninvite_user' ); ?>
								</div>

							</div><!-- .left-menu -->

							<div class="main-column col-sm-9 m-0">

								<div id="message" class="info">
									<p><?php esc_html_e('Select people to invite from your friends list.', 'buddypress' ); ?></p>
								</div>

								<?php /* The ID 'friend-list' is important for AJAX support. */ ?>
								<ul id="friend-list">

									<?php if ( bp_group_has_invites() ) : ?>

										<?php while ( bp_group_invites() ) : bp_group_the_invite(); ?>

											<li id="<?php bp_group_invite_item_id(); ?>" class="bp-card-list__item bp-card-list--members__item col-md-4 col-sm-6 col-12 has-post-thumbnail element-animated fade-in short element-animated-delay element-animated-both">

												<div class="card">

													<div class="card-img">
														<a href="<?php bp_member_permalink(); ?>">
															<?php bp_group_invite_user_avatar( 'type=full' ); ?>
														</a>
													</div><!-- .card-img -->

													<div class="card-body">

														<header class="card-body-header entry-header clearfix">
															<h2 class="entry-title">
																<a href="<?php bp_member_permalink(); ?>">
																	<?php bp_group_invite_user_link(); ?>
																</a>
															</h2><!-- .entry-title -->
														</header><!-- .card-body-header -->

														<div class="card-body-meta">
															<span class="activity"><?php bp_group_invite_user_last_active(); ?></span>
														</div><!-- .card-body-meta -->

														<div class="card-body-actions action">
															<div class="generic-button">
																<a class="button remove remove-invite" href="<?php bp_group_invite_user_remove_invite_url(); ?>" id="<?php bp_group_invite_item_id(); ?>" title="<?php esc_html_e( 'Remove Invite', 'buddypress' ); ?>"><?php esc_html_e( 'Remove Invite', 'buddypress' ); ?></a>
															</div><!-- .generic-button-->
														</div><!-- .card-body-actions-->

													</div><!-- .card-body -->

												</div><!-- .card -->

											</li><!-- .bp-card-list__item -->

										<?php endwhile; ?>

										<?php wp_nonce_field( 'groups_send_invites', '_wpnonce_send_invites' ); ?>

									<?php endif; ?>

								</ul>

							</div><!-- .main-column -->

						</div>

					<?php else : ?>

						<div id="message" class="info">
							<p><?php esc_html_e( 'Once you have built up friend connections you will be able to invite others to your group.', 'buddypress' ); ?></p>
						</div>

					<?php endif; ?>

					<?php wp_nonce_field( 'groups_create_save_group-invites' ); ?>

					<?php do_action( 'bp_after_group_invites_creation_step' ); ?>

				<?php endif; ?>

				<?php do_action( 'groups_custom_create_steps' ); ?>

				<?php do_action( 'bp_before_group_creation_step_buttons' ); ?>

				<?php if ( 'crop-image' !== bp_get_avatar_admin_step() ) : ?>

					<div class="submit" id="previous-next">

						<?php /* Previous Button */ ?>
						<?php if ( ! bp_is_first_group_creation_step() ) : ?>

							<input type="button" value="<?php esc_attr_e( 'Back to Previous Step', 'buddypress' ); ?>" id="group-creation-previous" name="previous" onclick="location.href='<?php bp_group_creation_previous_link(); ?>'" />

						<?php endif; ?>

						<?php /* Next Button */ ?>
						<?php if ( ! bp_is_last_group_creation_step() && ! bp_is_first_group_creation_step() ) : ?>

							<input type="submit" value="<?php esc_attr_e( 'Next Step', 'buddypress' ); ?>" id="group-creation-next" name="save" />

						<?php endif; ?>

						<?php /* Create Button */ ?>
						<?php if ( bp_is_first_group_creation_step() ) : ?>

							<input type="submit" value="<?php esc_attr_e( 'Create Group and Continue', 'buddypress' ); ?>" id="group-creation-create" name="save" />

						<?php endif; ?>

						<?php /* Finish Button */ ?>
						<?php if ( bp_is_last_group_creation_step() ) : ?>

							<input type="submit" value="<?php esc_attr_e( 'Finish', 'buddypress' ); ?>" id="group-creation-finish" name="save" />

						<?php endif; ?>
					</div>

				<?php endif; ?>

				<?php do_action( 'bp_after_group_creation_step_buttons' ); ?>

				<?php /* Don't leave out this hidden field */ ?>
				<input type="hidden" name="group_id" id="group_id" value="<?php bp_new_group_id(); ?>" />

				<?php do_action( 'bp_directory_groups_content' ); ?>

			</div><!-- .item-body -->

		</div><!-- .container -->

		<?php do_action( 'bp_after_create_group' ); ?>

	</form>

	<?php do_action( 'bp_after_create_group_content_template' ); ?>

</div>

<?php do_action( 'bp_after_create_group_page' ); ?>
