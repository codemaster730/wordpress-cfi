<?php
/**
 * Search Loop - Single Forum
 *
 * @package bbPress
 * @subpackage Theme
 */

// @codingStandardsIgnoreFile
// Allow plugin text domain in theme.
?>

<div id="bbp-forum-<?php bbp_forum_id(); ?>" <?php bbp_forum_class( '', array( 'card card-static p-3 p-sm-4 mb-3 ov-v' ) ); ?>>

	<?php if ( bbp_is_user_home() && bbp_is_subscriptions() ) : ?>
		<span class="bbp-row-actions">
			<?php do_action( 'bbp_theme_before_forum_subscription_action' ); ?>
			<?php bbp_forum_subscription_link( array( 'before' => '', 'subscribe' => '+', 'unsubscribe' => '&times;' ) ); ?>
			<?php do_action( 'bbp_theme_after_forum_subscription_action' ); ?>
		</span>
	<?php endif; ?>

	<div class="row">

		<div class="col-12 col-md-6 col-lg-8 col-forum-info">

			<div class="row d-flex align-items-center align-items-md-start pr-sm-5">

				<div class="col-auto pr-0 topic-img-row">
					<a href="<?php bbp_forum_permalink(); ?>">
						<?php if ( has_post_thumbnail() ) : ?>
							<?php the_post_thumbnail( 'thumbnail', array( 'class' => 'img-fluid' ) ); ?>
						<?php else: ?>
							<div class="topic-icon"></div>
						<?php endif; ?>
					</a>
				</div>

				<div class="col">
					<?php do_action( 'bbp_theme_before_forum_title' ); ?>
					<h2 class="entry-title h5 mt-2 mb-2">
						<a class="bbp-forum-title" href="<?php bbp_forum_permalink(); ?>"><?php bbp_forum_title(); ?></a>
					</h2>
					<?php do_action( 'bbp_theme_after_forum_title' ); ?>
					<?php do_action( 'bbp_theme_before_forum_description' ); ?>
					<div class="forum-content d-none d-md-block mb-0 pr-0 pr-sm-5"><?php the_excerpt(); ?></div>
					<?php do_action( 'bbp_theme_after_forum_description' ); ?>

					<?php do_action( 'bbp_theme_before_forum_sub_forums' ); ?>

					<?php
						$sub_forums = bbp_forum_get_subforums();
						if ( ! empty( $sub_forums ) ) : ?>
							<div class="dropdown dropdown-classic dropdown-subforum">
								<a class="dropdown-toggle" href="#" role="button" id="dropdownMenuForums_id<?php bbp_forum_id(); ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									<?php esc_html_e('Forums list', 'grimlock-bbpress'); ?>
								</a>
								<div class="dropdown-menu" aria-labelledby="dropdownMenuForums_id<?php bbp_forum_id(); ?>">
									<?php bbp_list_forums( array(
										'before'      => '',
										'after'       => '',
										'link_before' => '',
										'link_after'  => '',
										'separator'   => '',
									) ); ?>
								</div>
							</div>
						<?php
						endif; ?>

					<?php do_action( 'bbp_theme_after_forum_sub_forums' ); ?>

				</div>

			</div>

			<?php bbp_forum_row_actions(); ?>

		</div>

		<div class="col-12 col-md-6 col-lg-4 col-forum-meta mt-4 mt-md-0">

			<div class="row">
				<div class="col forum-topic-count">
					<div class="small text-uppercase font-weight-bold">
						<?php esc_html_e( 'Topics', 'bbpress' ); ?>
					</div>
					<div class="h3">
						<?php bbp_forum_topic_count(); ?>
					</div>
				</div>
				<div class="col forum-reply-count">
					<div class="small text-uppercase font-weight-bold">
						<?php esc_html_e( 'Replies', 'bbpress' ); ?>
					</div>
					<div class="h3">
						<?php bbp_show_lead_topic() ? bbp_forum_reply_count() : bbp_forum_post_count(); ?>
					</div>
				</div>
			</div>

			<hr class="mb-3 mt-2" />

			<div class="forum-freshness">
				<div class="small text-uppercase font-weight-bold">
					<?php esc_html_e( 'Freshness', 'bbpress' ); ?>
				</div>
				<div class="d-flex mt-2 small align-items-center">
					<?php do_action( 'bbp_theme_before_topic_author' ); ?>
					<span class="bbp-topic-freshness-author"><?php
							bbp_author_link( array(
								'post_id' => bbp_get_forum_last_active_id(),
								'size'    => '25',
								'type'    => 'avatar'
							) ); ?>
					</span>
					<?php do_action( 'bbp_theme_after_topic_author' ); ?>
					<?php do_action( 'bbp_theme_before_forum_freshness_link' ); ?>
					<?php bbp_forum_freshness_link(); ?>
					<?php do_action( 'bbp_theme_after_forum_freshness_link' ); ?>
				</div>
			</div>

		</div>

	</div>

</div><!-- #bbp-forum-<?php bbp_forum_id(); ?> -->
