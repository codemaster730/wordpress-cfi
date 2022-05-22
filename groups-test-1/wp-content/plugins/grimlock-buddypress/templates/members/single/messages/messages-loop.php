<?php
/**
 * BuddyPress - Members Messages Loop
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 * @version 3.0.0
 */

// @codingStandardsIgnoreFile
// Allow plugin text domain in theme.
$bp_message_thread_avatar_args = array(
	'width'  => bp_core_avatar_thumb_width() / 7,
	'height' => bp_core_avatar_thumb_height() / 7,
);

do_action( 'bp_before_member_messages_loop' ); ?>

<?php if ( bp_has_message_threads( bp_ajax_querystring( 'messages' ) ) ) : ?>

	<h2 class="bp-screen-reader-text"><?php
		/* translators: Accessibility text */
		esc_html_e( 'Starred messages', 'buddypress' );
		?></h2>

	<?php do_action( 'bp_after_member_messages_pagination' ); ?>

	<?php do_action( 'bp_before_member_messages_threads' ); ?>

	<form action="<?php echo esc_url( bp_displayed_user_domain() . bp_get_messages_slug() . '/' . bp_current_action() . '/bulk-manage/' ); ?>" method="post" id="messages-bulk-management">

		<div class="card card-static mb-3">

			<table id="message-threads" class="messages-notices m-0">

				<thead>
				<tr>
					<th scope="col" class="thread-checkbox bulk-select-all">
						<div class="custom-control custom-checkbox m-0">
							<input id="select-all-messages" type="checkbox" class="custom-control-input">
							<label class="custom-control-label" for="select-all-messages">
								<span class="bp-screen-reader-text"><?php esc_html_e( 'Select all', 'buddypress' ); ?></span>
							</label>
						</div>
					</th>

					<th scope="col" class="thread-from"><?php esc_html_e( 'From', 'buddypress' ); ?></th>
					<th scope="col" class="thread-info"><?php esc_html_e( 'Subject', 'buddypress' ); ?></th>

					<?php do_action( 'bp_messages_inbox_list_header' ); ?>

					<?php if ( bp_is_active( 'messages', 'star' ) ) : ?>
						<th scope="col" class="thread-star"><span class="message-action-star d-none"><span class="icon"></span> <span class="screen-reader-text"><?php
							/* translators: Accessibility text */
							esc_html_e( 'Star', 'buddypress' );
							?></span></span></th>
					<?php endif; ?>

					<th scope="col" class="thread-options"><span class="d-none"><?php esc_html_e( 'Actions', 'buddypress' ); ?></span></th>
				</tr>
				</thead>

				<tbody>

				<?php while ( bp_message_threads() ) : bp_message_thread(); ?>

					<tr id="m-<?php bp_message_thread_id(); ?>" class="<?php bp_message_css_class(); ?><?php echo bp_message_thread_has_unread() ? ' unread' : ' read'; ?>">
						<td class="bulk-select-check">
							<div class="custom-control custom-checkbox m-0">
								<input id="bp-message-thread-<?php bp_message_thread_id(); ?>" type="checkbox" class="custom-control-input message-check mr-0" name="message_ids[]" value="<?php bp_message_thread_id(); ?>">
								<label class="custom-control-label" for="bp-message-thread-<?php bp_message_thread_id(); ?>">
									<span class="bp-screen-reader-text"><?php esc_html_e( 'Select this message', 'buddypress' ); ?></span>
								</label>
							</div>
						</td>

						<?php if ( 'sentbox' !== bp_current_action() ) : ?>
							<td class="thread-from">
								<?php
								bp_message_thread_avatar( $bp_message_thread_avatar_args ); ?>
								<span class="from"><?php esc_html_e( 'From:', 'buddypress' ); ?></span> <?php bp_message_thread_from(); ?>
								<?php bp_message_thread_total_and_unread_count(); ?>
								<div>
									<span class="activity"><?php bp_message_thread_last_post_date(); ?></span>
								</div>
							</td>
						<?php else : ?>
							<td class="thread-from">
								<?php bp_message_thread_avatar( $bp_message_thread_avatar_args ); ?>
								<span class="to"><?php esc_html_e( 'To:', 'buddypress' ); ?></span> <?php bp_message_thread_to(); ?>
								<?php bp_message_thread_total_and_unread_count(); ?>
								<div>
									<span class="activity"><?php bp_message_thread_last_post_date(); ?></span>
								</div>
							</td>
						<?php endif; ?>

						<td class="thread-info">
							<p><a href="<?php bp_message_thread_view_link( bp_get_message_thread_id(), bp_displayed_user_id() ); ?>" class="bp-tooltip" data-bp-tooltip="<?php esc_attr_e( 'View Message', 'buddypress' ); ?>" aria-label="<?php esc_attr_e( 'View Message', 'buddypress' ); ?>"><?php bp_message_thread_subject(); ?></a></p>
							<p class="thread-excerpt"><?php bp_message_thread_excerpt(); ?></p>
						</td>

						<?php do_action( 'bp_messages_inbox_list_item' ); ?>

						<?php if ( bp_is_active( 'messages', 'star' ) ) : ?>
							<td class="thread-star pr-0">
								<div class="icon-state d-flex w-100">
									<?php
									bp_the_message_star_action_link( array(
										'thread_id' => bp_get_message_thread_id(),
									) ); ?>
								</div>
							</td>
						<?php endif; ?>

						<td class="thread-options">
							<?php if ( bp_message_thread_has_unread() ) : ?>
								<a class="read bp-tooltip" data-bp-tooltip="<?php esc_html_e( 'Mark as read', 'buddypress' ); ?>" href="<?php bp_the_message_thread_mark_read_url( bp_displayed_user_id() ); ?>"><?php esc_html_e( 'Read', 'buddypress' ); ?></a>
							<?php else : ?>
								<a class="unread bp-tooltip" data-bp-tooltip="<?php esc_html_e( 'Mark as unread', 'buddypress' ); ?>" href="<?php bp_the_message_thread_mark_unread_url( bp_displayed_user_id() ); ?>"><?php esc_html_e( 'Unread', 'buddypress' ); ?></a>
							<?php endif; ?>
							|
							<a class="delete bp-tooltip" data-bp-tooltip="<?php esc_html_e( 'Delete', 'buddypress' ); ?>" href="<?php bp_message_thread_delete_link( bp_displayed_user_id() ); ?>"><?php esc_html_e( 'Delete', 'buddypress' ); ?></a>

							<?php do_action( 'bp_messages_thread_options' ); ?>
						</td>
					</tr>

				<?php endwhile; ?>

				</tbody>

			</table><!-- #message-threads -->

		</div>

		<div class="table-footer pos-r">
			<div class="messages-options-nav">
				<?php bp_messages_bulk_management_dropdown(); ?>
			</div><!-- .messages-options-nav -->

			<?php wp_nonce_field( 'messages_bulk_nonce', 'messages_bulk_nonce' ); ?>
		</div>

	</form>

	<div class="pagination no-ajax" id="user-pag">

		<div class="pag-count" id="messages-dir-count">
			<?php bp_messages_pagination_count(); ?>
		</div>

		<div class="pagination-links" id="messages-dir-pag">
			<?php bp_messages_pagination(); ?>
		</div>

	</div><!-- .pagination -->

	<?php do_action( 'bp_after_member_messages_threads' ); ?>

	<?php do_action( 'bp_after_member_messages_options' ); ?>

<?php else : ?>

	<div id="message" class="info">
		<p><?php esc_html_e( 'Sorry, no messages were found.', 'buddypress' ); ?></p>
	</div>

<?php endif; ?>

<?php do_action( 'bp_after_member_messages_loop' ); ?>
