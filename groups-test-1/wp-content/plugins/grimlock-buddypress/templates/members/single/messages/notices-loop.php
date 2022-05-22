<?php
/**
 * BuddyPress - Members Single Messages Notice Loop
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 * @version 3.0.0
 */

// @codingStandardsIgnoreFile
// Allow plugin text domain in theme.

/**
 * Fires before the members notices loop.
 *
 * @since 1.2.0
 */
do_action( 'bp_before_notices_loop' ); ?>

<?php if ( bp_has_message_threads() ) : ?>

	<?php do_action( 'bp_after_notices_pagination' ); ?>
	<?php do_action( 'bp_before_notices' ); ?>

	<div class="card card-static mb-3">
		<table id="message-threads" class="messages-notices sitewide-notices m-0">
			<?php while ( bp_message_threads() ) : bp_message_thread(); ?>
				<tr id="notice-<?php bp_message_notice_id(); ?>" class="<?php bp_message_css_class(); ?> notice-<?php bp_message_activate_deactivate_text(); ?>">
					<td>
						<div class="p-md-4 w-100">
							<strong><?php bp_message_notice_subject(); ?></strong>
							<div class="small">
								<?php bp_message_notice_text(); ?>
							</div>
							<?php if ( bp_messages_is_active_notice() ) : ?>
								<strong><?php bp_messages_is_active_notice(); ?></strong>
							<?php endif; ?>
							<em class="activity"><?php esc_html_e( 'Sent:', 'buddypress' ); ?> <?php bp_message_notice_post_date(); ?></em>
						</div>
					</td>
					<?php do_action( 'bp_notices_list_item' ); ?>
					<td class="notice-actions text-right">
						<a class="confirm <?php bp_message_activate_deactivate_text(); ?>" href="<?php bp_message_activate_deactivate_link(); ?>" ><?php bp_message_activate_deactivate_text(); ?></a>
						<a class="confirm delete" href="<?php bp_message_notice_delete_link(); ?>" aria-label="<?php esc_attr_e( 'Delete Message', 'buddypress' ); ?>">x</a>
					</td>
				</tr>
			<?php endwhile; ?>
		</table><!-- #message-threads -->
	</div>

	<?php do_action( 'bp_after_notices' ); ?>

<?php else : ?>

	<div id="message" class="info">
		<p><?php esc_html_e( 'Sorry, no notices were found.', 'buddypress' ); ?></p>
	</div>

<?php endif; ?>

<?php do_action( 'bp_after_notices_loop' ); ?>
