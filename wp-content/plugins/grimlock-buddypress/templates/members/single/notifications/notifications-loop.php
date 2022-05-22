<?php
/**
 * BuddyPress - Members Notifications Loop
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 * @version 3.0.0
 */

// @codingStandardsIgnoreFile
// Allow plugin text domain in theme.
?>

<form action="" method="post" id="notifications-bulk-management">
	<div class="card card-static mb-3">
		<table class="notifications m-0">
			<thead>
			<tr>
				<th class="icon"></th>
				<th class="bulk-select-all">
					<div class="custom-control custom-checkbox m-0">
						<input id="select-all-notifications" type="checkbox" class="custom-control-input">
						<label class="custom-control-label" for="select-all-notifications">
							<span class="bp-screen-reader-text"><?php esc_html_e( 'Select all', 'buddypress' ); ?></span>
						</label>
					</div>
				</th>
				<th class="title"><?php esc_html_e( 'Notification', 'buddypress' ); ?></th>
				<th class="date"><?php esc_html_e( 'Date Received', 'buddypress' ); ?></th>
				<th class="actions"><?php esc_html_e( 'Actions',    'buddypress' ); ?></th>
			</tr>
			</thead>

			<tbody>

			<?php while ( bp_the_notifications() ) : bp_the_notification(); ?>

				<tr>
					<td></td>
					<td class="bulk-select-check">
						<div class="custom-control custom-checkbox m-0">
							<input id="<?php bp_the_notification_id(); ?>" type="checkbox" class="custom-control-input notification-check" name="notifications[]" value="<?php bp_the_notification_id(); ?>">
							<label class="custom-control-label" for="<?php bp_the_notification_id(); ?>">
								<span class="bp-screen-reader-text"><?php esc_html_e( 'Select this notification', 'buddypress' ); ?></span>
							</label>
						</div>
					</td>
					<td class="notification-description"><?php bp_the_notification_description(); ?></td>
					<td class="notification-since"><?php bp_the_notification_time_since(); ?></td>
					<td class="notification-actions"><?php bp_the_notification_action_links(); ?></td>
				</tr>

			<?php endwhile; ?>

			</tbody>
		</table>
	</div>

	<div class="table-footer pos-r">
		<div class="notifications-options-nav">
			<?php bp_notifications_bulk_management_dropdown(); ?>
		</div><!-- .notifications-options-nav -->
		<?php wp_nonce_field( 'notifications_bulk_nonce', 'notifications_bulk_nonce' ); ?>
	</div>

</form>
