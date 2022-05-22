<?php
/**
 * Topics Loop - Single
 *
 * @package bbPress
 * @subpackage Theme
 */

// @codingStandardsIgnoreFile
// Allow plugin text domain in theme and unescaped template tags.
?>

<div id="bbp-topic-<?php bbp_topic_id(); ?>" <?php bbp_topic_class( '', array( 'card card-static p-3 p-sm-4 mb-3 ov-v' ) ); ?>>

	<?php if ( bbp_is_user_home() ) : ?>
		<?php if ( bbp_is_favorites() ) : ?>
			<span class="bbp-row-actions">
				<?php do_action( 'bbp_theme_before_topic_favorites_action' ); ?>
				<?php bbp_topic_favorite_link( array( 'before' => '', 'favorite' => '+', 'favorited' => '&times;' ) ); ?>
				<?php do_action( 'bbp_theme_after_topic_favorites_action' ); ?>
			</span>
		<?php elseif ( bbp_is_subscriptions() ) : ?>
			<span class="bbp-row-actions">
				<?php do_action( 'bbp_theme_before_topic_subscription_action' ); ?>
				<?php bbp_topic_subscription_link( array( 'before' => '', 'subscribe' => '+', 'unsubscribe' => '&times;' ) ); ?>
				<?php do_action( 'bbp_theme_after_topic_subscription_action' ); ?>
			</span>
		<?php endif; ?>
	<?php endif; ?>

	<div class="row">

		<div class="col-12 col-md-6 col-lg-8 col-forum-info">

			<div class="topic-title">

				<?php do_action( 'bbp_theme_before_topic_title' ); ?>

				<h2 class="entry-title h4 mt-2 pb-2 pr-5">
					<a class="bbp-topic-permalink" href="<?php bbp_topic_permalink(); ?>"><?php bbp_topic_title(); ?></a>
				</h2>

				<?php do_action( 'bbp_theme_after_topic_title' ); ?>

				<?php bbp_topic_pagination(); ?>

				<?php do_action( 'bbp_theme_before_topic_meta' ); ?>

				<p class="bbp-topic-meta">

					<?php if ( ! bbp_is_single_forum() || ( bbp_get_topic_forum_id() !== bbp_get_forum_id() ) ) : ?>

						<?php do_action( 'bbp_theme_before_topic_started_in' ); ?>

						<span class="bbp-topic-started-in pb-2 d-block">
							<?php
							$allowed_html = array(
								'a' => array(
									'href' => array(),
								),
							);

							/* translators: 1: The forum permalink, 2: The forum title */
							printf( wp_kses( __( 'in: <a href="%1$s">%2$s</a>', 'bbpress' ), $allowed_html ), esc_url( bbp_get_forum_permalink( bbp_get_topic_forum_id() ) ), esc_html( bbp_get_forum_title( bbp_get_topic_forum_id() ) ) ); ?></span>

						<?php do_action( 'bbp_theme_after_topic_started_in' ); ?>

					<?php endif; ?>

					<?php do_action( 'bbp_theme_before_topic_started_by' ); ?>

					<span class="bbp-topic-started-by d-block"><?php echo bbp_get_topic_author_link( array( 'size' => '25' ) ); ?></span>

					<?php do_action( 'bbp_theme_after_topic_started_by' ); ?>

				</p>

				<?php do_action( 'bbp_theme_after_topic_meta' ); ?>

				<?php bbp_topic_row_actions(); ?>

			</div>

		</div>

		<div class="col-12 col-md-6 col-lg-4 col-forum-meta mt-4 mt-md-0">

			<div class="row">
				<div class="col topic-voice-count">
					<div class="small text-uppercase font-weight-bold">
						<?php esc_html_e( 'Voices', 'bbpress' ); ?>
					</div>
					<div class="h3">
						<?php bbp_topic_voice_count(); ?>
					</div>
				</div>
				<div class="col topic-reply-count">
					<div class="small text-uppercase font-weight-bold">
						<?php esc_html_e( 'Replies', 'bbpress' ); ?>
					</div>
					<div class="h3">
						<?php bbp_show_lead_topic() ? bbp_topic_reply_count() : bbp_topic_post_count(); ?>
					</div>
				</div>
			</div>

			<hr class="mb-3 mt-2" />

			<div class="forum-freshness">
				<div class="small text-uppercase font-weight-bold">
					<?php esc_html_e( 'Freshness', 'bbpress' ); ?>
				</div>
				<div class="d-flex mt-2 small align-items-center">
					<?php do_action( 'bbp_theme_before_topic_freshness_author' ); ?>
					<span class="bbp-topic-freshness-author"><?php
							bbp_author_link( array(
								'post_id' => bbp_get_topic_last_active_id(),
								'size'    => '25',
								'type'    => 'avatar'
							) ); ?>
					</span>
					<?php do_action( 'bbp_theme_after_topic_freshness_author' ); ?>
					<?php do_action( 'bbp_theme_before_topic_freshness_link' ); ?>
					<?php bbp_topic_freshness_link(); ?>
					<?php do_action( 'bbp_theme_after_topic_freshness_link' ); ?>
				</div>
			</div>
		</div>
	</div>
</div><!-- #bbp-topic-<?php bbp_topic_id(); ?> -->
