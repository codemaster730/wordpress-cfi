<?php
/**
 * Replies Loop - Single Reply
 *
 * @package bbPress
 * @subpackage Theme
 */

// @codingStandardsIgnoreFile
// Allow plugin text domain in theme and unescaped template tags.
?>

<div class="card card-static mb-3 ov-v">

	<div class="card-body p-2 p-md-3 ov-v" id="post-<?php bbp_reply_id(); ?>">

		<div <?php bbp_reply_class('', array('class'=>'ov-v')); ?>>

			<div class="row">

				<div class="col-12">

					<div class="bbp-list-author row align-items-center mb-3">

						<div class="col-auto pr-0">

							<?php do_action( 'bbp_theme_before_reply_author_details' ); ?>

							<div class="d-flex d-sm-block align-items-center align-items-md-start bbp-list-author-avatar">
								<a href="<?php bbp_reply_author_url(); ?>">
									<?php bbp_reply_author_avatar(); ?>
								</a>
							</div>

						</div>

						<div class="col">

							<div class="bbp-list-author-meta">

								<div class="bbp-list-author-name d-flex align-items-center">
									<?php bbp_reply_author_link( array( 'sep' => '', 'show_role' => true, 'type' => 'name' ) ); ?>
									<?php do_action( 'grimlock_bbpress_after_reply_author_link' ); ?>
								</div>

								<div class="bbp-list-author-info d-flex align-items-center mt-2">
									<?php do_action( 'bbp_theme_after_reply_author_details' ); ?>
								</div>

							</div>

						</div>

						<?php if ( bbp_current_user_can_access_create_reply_form() ): ?>
							<div class="col-auto pl-0 d-none d-md-block">
								<div class="dropdown dropdown-bbp-post-actions dropdown-bbp-post-actions--top" data-toggle="tooltip" data-placement="top" title="<?php esc_html_e('More actions', 'grimlock-bbpress'); ?>">
									<a class="dropdown-toggle" href="#" role="button" id="dropdownPostActions_<?php bbp_reply_id(); ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
										<span class="sr-only"><?php esc_html_e('Post actions', 'grimlock-bbpress'); ?></span>
									</a>
									<div class="dropdown-menu" aria-labelledby="dropdownPostActions_<?php bbp_reply_id(); ?>">
										<?php do_action( 'bbp_theme_before_reply_admin_links' ); ?>
										<?php bbp_reply_admin_links(); ?>
										<?php do_action( 'bbp_theme_after_reply_admin_links' ); ?>
									</div>
								</div>
							</div>
						<?php endif; ?>

					</div>

					<?php do_action( 'bbp_theme_before_reply_content' ); ?>

					<div class="bg-black-faded p-3 p-sm-4 rounded reply-content-bubble">

						<?php bbp_reply_content(); ?>

						<?php do_action( 'bbp_theme_after_reply_content' ); ?>

					</div>

				</div><!-- .bbp-reply-content -->

			</div>

			<div class="bbp-meta-footer">

				<span class="bbp-reply-post-date"><?php bbp_reply_post_date(); ?></span>

				<?php if ( bbp_is_user_keymaster() ) : ?>

					<?php do_action( 'bbp_theme_before_reply_author_admin_details' ); ?>

					<div class="bbp-reply-ip d-none d-sm-inline-flex ml-1"><?php bbp_author_ip( bbp_get_reply_id() ); ?></div>

					<?php do_action( 'bbp_theme_after_reply_author_admin_details' ); ?>

				<?php endif; ?>

				<?php if ( bbp_is_single_user_replies() ) : ?>

					<span class="bbp-header"><?php esc_html_e( 'in reply to: ', 'bbpress' ); ?><a class="bbp-topic-permalink" href="<?php bbp_topic_permalink( bbp_get_reply_topic_id() ); ?>"><?php bbp_topic_title( bbp_get_reply_topic_id() ); ?></a></span>

				<?php endif; ?>

				<a href="<?php bbp_reply_url(); ?>" class="bbp-reply-permalink">#<?php bbp_reply_id(); ?></a>

			</div><!-- .bbp-meta -->

		</div> <!-- .reply -->

	</div> <!-- .card-body -->

	<?php if ( bbp_current_user_can_access_create_reply_form() ): ?>
		<div class="d-block d-md-none">
			<hr class="m-0" />
			<div class="dropdown dropdown-bbp-post-actions dropdown-bbp-post-actions--bottom">
				<a class="d-flex align-items-center justify-content-center dropdown-toggle px-0 bg-black-faded border-0" href="#" role="button" id="dropdownPostActions_<?php bbp_reply_id(); ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<span class="sr-only"><?php esc_html_e('More actions', 'grimlock-bbpress'); ?></span>
				</a>
				<div class="dropdown-menu w-100" aria-labelledby="dropdownPostActions_<?php bbp_reply_id(); ?>">
					<?php do_action( 'bbp_theme_before_reply_admin_links' ); ?>
					<?php bbp_reply_admin_links(); ?>
					<?php do_action( 'bbp_theme_after_reply_admin_links' ); ?>
				</div>
			</div>
		</div>
	<?php endif; ?>

</div>  <!-- .card -->
