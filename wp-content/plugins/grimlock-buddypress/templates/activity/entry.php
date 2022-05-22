<?php
/**
 * BuddyPress - Activity Stream (Single Item)
 *
 * This template is used by activity-loop.php and AJAX functions to show
 * each activity.
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 * @version 3.0.0
 */

// @codingStandardsIgnoreFile
// Allow plugin text domain in theme.

do_action( 'bp_before_activity_entry' ); ?>

<li class="<?php bp_activity_css_class(); ?>" id="activity-<?php bp_activity_id(); ?>">

	<div class="activity-user w-100 pr-md-5">

		<div class="activity-avatar">

			<a href="<?php bp_activity_user_link(); ?>">
				<?php bp_activity_avatar( 'width=' . bp_core_avatar_thumb_width() . '&height=' . bp_core_avatar_thumb_height() ); ?>
			</a>

		</div> <!-- .activity-avatar -->

		<div class="activity-header entry-title h5 mb-0">
			<?php bp_activity_action(); ?>
		</div> <!-- .activity-header -->

	</div> <!-- .activity-user -->

	<div class="activity-content">

		<?php if ( version_compare( bp_get_version(), '10.0', '>=' ) ) : ?>
			<?php if ( bp_activity_has_content() ) : ?><div class="activity-inner"><?php bp_get_template_part( 'activity/type-parts/content',  bp_activity_type_part() ); ?></div> <!-- .activity-inner --><?php endif; ?>
		<?php else : ?>
			<?php if ( bp_activity_has_content() ) : ?><div class="activity-inner"><?php bp_activity_content_body(); ?></div> <!-- .activity-inner --><?php endif; ?>
		<?php endif; ?>

		<?php do_action( 'bp_activity_entry_content' ); ?>

		<?php if ( is_user_logged_in() ) : ?>

			<div class="activity-options">

				<?php if ( 'activity_comment' === bp_get_activity_type() ) : ?>
					<a href="<?php bp_activity_thread_permalink(); ?>" class="acomment-view">
						<?php esc_html_e( 'View Conversation', 'buddypress' ); ?>
					</a>
				<?php endif; ?>

				<?php if ( bp_activity_can_comment() ) : ?>
					<a href="<?php bp_activity_comment_link(); ?>" class="acomment-reply" id="acomment-comment-<?php bp_activity_id(); ?>">
						<?php
						/* translators: %s: Comment count */
						printf( esc_html__( 'Comment %s', 'buddypress' ), '<span>' . esc_html( bp_activity_get_comment_count() ) . '</span>' ); ?>
					</a>
				<?php endif; ?>

				<div class="dropdown dropdown-activity dropdown--inverted" data-toggle="tooltip" data-placement="top" title="<?php esc_html_e('More actions', 'grimlock-buddypress'); ?>">

					<a href="#" class="dropdown-toggle no-toggle mr-0" id="dropdownActivity" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-offset="0,15"></a> <!-- .dropdown--toggle -->

					<div class="dropdown-menu" aria-labelledby="dropdownActivity">
						<?php if ( bp_activity_can_favorite() ) : ?>
							<?php if ( ! bp_get_activity_is_favorite() ) : ?>
								<a href="<?php bp_activity_favorite_link(); ?>" class="fav">
									<?php esc_html_e( 'Favorite', 'buddypress' ); ?>
								</a>
							<?php else : ?>
								<a href="<?php bp_activity_unfavorite_link(); ?>" class="unfav">
									<?php esc_html_e( 'Remove Favorite', 'buddypress' ); ?>
								</a>
							<?php endif; ?>
						<?php endif; ?>
						<?php if ( bp_activity_user_can_delete() ) : bp_activity_delete_link(); endif; ?>
					</div> <!-- .dropdown-menu -->
				</div> <!-- .dropdown-activity -->

				<?php do_action( 'bp_activity_entry_meta' ); ?>

			</div> <!-- .activity-options -->

		<?php endif; ?>

		<?php do_action( 'bp_before_activity_entry_comments' ); ?>

		<?php if ( ( bp_activity_get_comment_count() || bp_activity_can_comment() ) || bp_is_single_activity() ) : ?>

			<div class="activity-comments">

				<?php bp_activity_comments(); ?>

				<?php if ( is_user_logged_in() && bp_activity_can_comment() ) : ?>

					<form action="<?php bp_activity_comment_form_action(); ?>" method="post" id="ac-form-<?php bp_activity_id(); ?>" class="ac-form element-animated fade-in short"<?php bp_activity_comment_form_nojs_display(); ?>>
						<div class="ac-form-content d-flex">
							<div class="ac-reply-avatar d-none d-md-flex align-self-start mr-3"><?php bp_loggedin_user_avatar( 'width=' . bp_core_avatar_full_width() / 5 . '&height=' . bp_core_avatar_full_height() / 5 . '&type=full' ); ?></div> <!-- .ac-reply-avatar -->
							<div class="media-body">
								<div class="ac-textarea">
									<label for="ac-input-<?php bp_activity_id(); ?>" class="bp-screen-reader-text"><?php
										/* translators: Accessibility text */
										esc_html_e( 'Comment', 'buddypress' );
										?></label>
									<textarea id="ac-input-<?php bp_activity_id(); ?>" class="ac-input bp-suggestions" name="ac_input_<?php bp_activity_id(); ?>"></textarea>
								</div>
								<input type="submit" name="ac_form_submit" value="<?php esc_attr_e( 'Post', 'buddypress' ); ?>" /> <a href="#" class="ac-reply-cancel btn btn-secondary"><?php esc_html_e( 'Cancel', 'buddypress' ); ?></a>
								<input type="hidden" name="comment_form_id" value="<?php bp_activity_id(); ?>" />
								<?php do_action( 'bp_activity_entry_comments' ); ?>
								<?php wp_nonce_field( 'new_activity_comment', '_wpnonce_new_activity_comment_' . bp_get_activity_id() ); ?>
							</div> <!-- .media-body -->
						</div> <!-- .media -->
					</form>

				<?php endif; ?>

			</div> <!-- .activity-comments -->

		<?php endif; ?>

		<?php do_action( 'bp_after_activity_entry_comments' ); ?>

	</div> <!-- .activity-content -->

</li> <!-- .activity-item -->

<?php do_action( 'bp_after_activity_entry' ); ?>
