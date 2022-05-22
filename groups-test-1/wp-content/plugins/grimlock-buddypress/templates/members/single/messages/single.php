<?php
/**
 * BuddyPress - Members Single Message
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 * @version 3.0.0
 */

// @codingStandardsIgnoreFile
// Allow plugin text domain in theme.
?>

<div id="message-thread">

	<?php do_action( 'bp_before_message_thread_content' ); ?>

	<?php if ( bp_thread_has_messages() ) : ?>

		<header class="message-header card card-static p-4 mb-4">

			<div class="row align-items-center">
				<div class="col-sm-9">
					<h3 id="message-subject" class="m-0"><?php bp_the_thread_subject(); ?></h3>
					<p id="message-recipients" class="m-0">
						<span class="highlight">
						<strong>
						<?php if ( bp_get_thread_recipients_count() <= 1 ) : ?>
							<?php esc_html_e( 'You are alone in this conversation.', 'buddypress' ); ?>
						<?php elseif ( bp_get_max_thread_recipients_to_list() <= bp_get_thread_recipients_count() ) : ?>
							<?php
							/* translators: %s: Thread recipients count */
							printf( esc_html__( 'Conversation between %s recipients.', 'buddypress' ), number_format_i18n( bp_get_thread_recipients_count() ) ); ?>
						<?php else : ?>
							<?php
							/* translators: %s: Thread recipients list */
							printf( esc_html__( 'Conversation between %s.', 'buddypress' ), bp_get_thread_recipients_list() ); ?>
						<?php endif; ?>
						</strong>
						</span>
					</p>
				</div>
				<div class="col-sm-3">
					<a class="btn btn-danger btn-sm confirm float-left float-sm-right" href="<?php bp_the_thread_delete_link(); ?>"><i class="fa fa-trash fa-lg"></i></a>
				</div>
			</div>
			<?php do_action( 'bp_after_message_thread_recipients' ); ?>
		</header>

		<?php do_action( 'bp_before_message_thread_list' ); ?>

		<div class="card card-static">

			<?php while ( bp_thread_messages() ) : bp_thread_the_message(); ?>
				<?php bp_get_template_part( 'members/single/messages/message' ); ?>
			<?php endwhile; ?>

			<?php do_action( 'bp_after_message_thread_list' ); ?>

			<?php do_action( 'bp_before_message_thread_reply' ); ?>

			<form id="send-reply" action="<?php bp_messages_form_action(); ?>" method="post">

				<div class="message-box">

					<div class="message-metadata">

						<?php do_action( 'bp_before_message_meta' ); ?>

						<div class="avatar-box d-flex align-items-center">
							<?php bp_loggedin_user_avatar( 'type=thumb&height=40&width=40' ); ?>
							<h4 class="h6 m-0 p-0"><?php esc_html_e( 'Send a Reply', 'buddypress' ); ?></h4>
						</div>

						<?php do_action( 'bp_after_message_meta' ); ?>

					</div><!-- .message-metadata -->

					<?php do_action( 'bp_before_message_reply_box' ); ?>

					<label for="message_content" class="bp-screen-reader-text">
						<?php esc_html_e( 'Reply to Message', 'buddypress' ); ?>
					</label>

					<textarea name="content" id="message_content" rows="15" cols="40"></textarea>

					<?php do_action( 'bp_after_message_reply_box' ); ?>

					<div class="submit mt-3">
						<input type="submit" name="send" value="<?php esc_attr_e( 'Send Reply', 'buddypress' ); ?>" id="send_reply_button"/>
					</div>

					<input type="hidden" id="thread_id" name="thread_id" value="<?php bp_the_thread_id(); ?>" />
					<input type="hidden" id="messages_order" name="messages_order" value="<?php bp_thread_messages_order(); ?>" />
					<?php wp_nonce_field( 'messages_send_message', 'send_message_nonce' ); ?>

				</div><!-- .message-box -->

			</form><!-- #send-reply -->

		</div><!-- .card -->

		<?php do_action( 'bp_after_message_thread_reply' ); ?>

	<?php endif; ?>

	<?php do_action( 'bp_after_message_thread_content' ); ?>

</div><!-- #message-thread -->
