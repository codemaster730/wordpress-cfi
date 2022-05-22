<?php
/**
 * BuddyPress - Membership Invitations Loop
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 * @version 8.0.0
 */

?>
<form action="" method="post" id="invitations-bulk-management">
	<div class="card card-static mb-3">
		<table class="invitations">
			<thead>
				<tr>
					<th class="bulk-select-all">
						<div class="custom-control custom-checkbox m-0">
							<input id="select-all-invitations" type="checkbox" class="custom-control-input">
							<label class="custom-control-label" for="select-all-invitations">
								<span class="bp-screen-reader-text"><?php esc_html_e( 'Select all', 'buddypress' ); ?></span>
							</label>
						</div>
					</th>
					<th class="title"><?php esc_html_e( 'Invitee', 'buddypress' ); ?> & <?php esc_html_e( 'Message', 'buddypress' ); ?></th>
					<th class="date"><?php esc_html_e( 'Date Modified', 'buddypress' ); ?></th>
					<th class="accepted text-center"><?php esc_html_e( 'Accepted', 'buddypress' ); ?></th>
					<th class="actions"><?php esc_html_e( 'Actions', 'buddypress' ); ?></th>
				</tr>
			</thead>

			<tbody>

				<?php while ( bp_the_members_invitations() ) : bp_the_members_invitation(); ?>

					<tr>
						<td class="bulk-select-check">
							<div class="custom-control custom-checkbox m-0">
								<input id="<?php bp_the_members_invitation_property( 'id', 'attribute' ); ?>" type="checkbox" name="members_invitations[]" value="<?php bp_the_members_invitation_property( 'id', 'attribute' ); ?>" class="custom-control-input invitation-check">
								<label class="custom-control-label" for="<?php bp_the_members_invitation_property( 'id', 'attribute' ); ?>">
									<span class="bp-screen-reader-text"><?php esc_html_e( 'Select this invitation', 'buddypress' ); ?></span>
								</label>
							</div>
						</td>
						<td class="invitation-invitee"><strong class="d-block mb-2"><?php bp_the_members_invitation_property( 'invitee_email' );  ?></strong><p class="small"><?php bp_the_members_invitation_property( 'content' );  ?></p></td>
						<td class="invitation-date-modified"><?php bp_the_members_invitation_property( 'date_modified' );   ?></td>
						<td class="invitation-accepted text-center">
							<span class="d-md-none"><?php esc_html_e( 'Accepted', 'buddypress' ); ?> :</span>
							<?php if ( bp_get_the_members_invitation_property( 'accepted' ) ): ?>
								<span class="icon-invitation-accepted"></span>
							<?php else: ?>
								<span class="icon-invitation-not-accepted"></span>
							<?php endif; ?>
						</td>
						<td class="invitation-actions"><?php bp_the_members_invitation_action_links(); ?></td>
					</tr>

				<?php endwhile; ?>

			</tbody>
		</table>
	</div>

	<div class="table-footer pos-r">
		<div class="invitations-options-nav">
			<?php bp_members_invitations_bulk_management_dropdown(); ?>
		</div><!-- .invitations-options-nav -->
		<?php wp_nonce_field( 'invitations_bulk_nonce', 'invitations_bulk_nonce' ); ?>
	</div>

</form>
