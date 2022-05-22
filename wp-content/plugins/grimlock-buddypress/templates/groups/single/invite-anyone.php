<?php

/**
 * This template, which powers the group Send Invites tab when IA is enabled, can be overridden
 * with a template file at groups/single/invite-anyone.php
 *
 * @package Invite Anyone
 * @since 0.8.5
 */

?>

<?php do_action( 'bp_before_group_send_invites_content' ) ?>

<?php if ( invite_anyone_access_test() && !bp_is_group_create() ) : ?>
	<div class="card card-static p-3 mb-3"><div><?php esc_html_e( 'Want to invite someone to the group who is not yet a member of the site?', 'invite-anyone' ) ?> <a href="<?php echo bp_loggedin_user_domain() . BP_INVITE_ANYONE_SLUG . '/invite-new-members/group-invites/' . bp_get_group_id() ?>"><?php esc_html_e( 'Send invitations by email.', 'invite-anyone' ) ?></a></div></div>
<?php endif; ?>

<?php if ( !bp_get_new_group_id() ) : ?>
	<div class="screen-profile card card-static">
	<form action="<?php invite_anyone_group_invite_form_action() ?>" method="post" id="send-invite-form" class="standard-form m-0">
<?php endif; ?>

	<div class="row <?php if ( bp_get_new_group_id() ) : ?>align-items-start<?php endif; ?>">

		<div class="col-sm-auto col-lg-3 col-profile-edit-nav">

			<div class="col-profile-edit-nav__sticky-wrapper">

				<label><?php esc_html_e("Search for members to invite:", 'invite-anyone') ?></label>

				<ul class="first acfb-holder ml-0 pl-0">
					<li>
						<input type="text" name="send-to-input" class="send-to-input" id="send-to-input" />
						<span class="send-to-input-loading"></span>
					</li>
				</ul>

				<?php wp_nonce_field( 'groups_invite_uninvite_user', '_wpnonce_invite_uninvite_user' ) ?>

				<?php if ( ! invite_anyone_is_large_network( 'users' ) ) : ?>
					<label><?php esc_html_e( 'Select members from the directory:', 'invite-anyone' ) ?></label>

					<div id="invite-anyone-member-list">
						<ul class="button-nav clearfix">
							<?php bp_new_group_invite_member_list() ?>
						</ul>
					</div>
				<?php endif ?>

				<?php if ( ! bp_get_new_group_id() ) : ?>
					<div class="submit">
						<input type="submit" name="submit" id="submit" class="w-100 mt-3" value="<?php esc_html_e( 'Send Invites', 'invite-anyone' ) ?>" />
					</div>
				<?php endif; ?>

			</div> <!-- .col-profile-edit-nav__sticky-wrapper -->

		</div> <!-- .col-profile-edit-nav -->

		<div class="col col-profile-edit-fields col-members-invite-anyone">

			<div class="card card-static">

				<div id="message" class="info">
					<p><?php esc_html_e('Select people to invite from your friends list.', 'invite-anyone'); ?></p>
				</div>

				<?php do_action( 'bp_before_group_send_invites_list' ) ?>

				<?php /* The ID 'friend-list' is important for AJAX support. */ ?>
				<ul id="invite-anyone-invite-list" class="bp-card-list bp-card-list--members bp-card-list--3 loading-list">

					<?php if ( bp_group_has_invites() ) : ?>

					<?php while ( bp_group_invites() ) : bp_group_the_invite(); ?>

						<li id="<?php bp_group_invite_item_id() ?>">

							<?php bp_group_invite_user_avatar() ?>

							<h4><?php bp_group_invite_user_link() ?></h4>

							<span class="activity"><?php bp_group_invite_user_last_active() ?></span>

							<?php do_action( 'bp_group_send_invites_item' ) ?>

							<div class="action">
								<a class="remove" href="<?php bp_group_invite_user_remove_invite_url() ?>" id="<?php bp_group_invite_item_id() ?>"><?php _e( 'Remove Invite', 'invite-anyone' ) ?></a>
								<?php do_action( 'bp_group_send_invites_item_action' ) ?>
							</div>

						</li>

					<?php endwhile; ?>

				<?php endif; ?>

				</ul><!-- #invite-anyone-invite-list -->

				<?php do_action( 'bp_after_group_send_invites_list' ) ?>

				<?php wp_nonce_field( 'groups_send_invites', '_wpnonce_send_invites') ?>

				<!-- Don't leave out this sweet field -->
				<?php
				if ( !bp_get_new_group_id() ) {
					?><input type="hidden" name="group_id" id="group_id" value="<?php bp_group_id() ?>" /><?php
				} else {
					?><input type="hidden" name="group_id" id="group_id" value="<?php bp_new_group_id() ?>" /><?php
				}
				?>

			</div> <!-- .card-->

		</div><!-- .col-profile-edit-fields -->

	</div> <!-- .row -->

<?php if ( !bp_get_new_group_id() ) : ?>
	</form>
	</div> <!-- .screen-profile -->
<?php endif; ?>


<?php do_action( 'bp_after_group_send_invites_content' ) ?>
