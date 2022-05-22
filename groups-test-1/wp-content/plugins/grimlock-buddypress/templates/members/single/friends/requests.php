<?php
/**
 * BuddyPress - Members Friends Requests
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 * @version 3.0.0
 */

// @codingStandardsIgnoreFile
// Allow plugin text domain in theme.
do_action( 'bp_before_member_friend_requests_content' ); ?>

<?php if ( bp_has_members( 'type=alphabetical&include=' . bp_get_friendship_requests() ) ) : ?>

	<h2 class="bp-screen-reader-text">
		<?php esc_html_e( 'Friendship requests', 'buddypress' ); ?>
	</h2>

	<ul id="friend-list" class="bp-card-list bp-card-list--members">

		<?php while ( bp_members() ) : bp_the_member(); ?>

			<li id="friendship-<?php bp_friend_friendship_id(); ?>" class="bp-card-list__item bp-card-list--members__item has-post-thumbnail element-animated fade-in short element-animated-delay element-animated-both">

				<div class="card">

					<div class="card-img">
						<a href="<?php bp_member_link(); ?>">
							<?php bp_member_avatar( 'type=full' ); ?>
						</a>
					</div> <!-- .card-img -->

					<div class="card-body pt-1 pb-4 pl-2 pr-2">

						<header class="card-body-header entry-header clearfix">
							<h2 class="entry-title">
								<span class="item-title"><a href="<?php bp_member_link(); ?>"><?php bp_member_name(); ?></a></span>
							</h2>
						</header>

						<div class="card-body-meta">
							<div class="last-activity">
								<?php bp_member_last_active(); ?>
							</div>
							<div class="bp_friend_requests_item mt-2">
								<em><?php do_action( 'bp_friend_requests_item' ); ?></em>
							</div>
						</div> <!-- .card-body-meta -->

						<div class="card-body-actions action">
							<a class="button accept friendship-button" href="<?php bp_friend_accept_request_link(); ?>" title="<?php esc_html_e( 'Accept', 'buddypress' ); ?>"><?php esc_html_e( 'Accept', 'buddypress' ); ?></a>
							<a class="button reject" href="<?php bp_friend_reject_request_link(); ?>" title="<?php esc_html_e( 'Reject', 'buddypress' ); ?>"><?php esc_html_e( 'Reject', 'buddypress' ); ?></a>
							<?php do_action( 'bp_friend_requests_item_action' ); ?>
						</div> <!-- .card-body-actions -->

					</div> <!-- .card-body -->

				</div> <!-- .card -->

			</li> <!-- .bp-card-list__item -->

		<?php endwhile; ?>
	</ul> <!-- .bp-card-list -->

	<?php do_action( 'bp_friend_requests_content' ); ?>

	<div id="pag-bottom" class="pagination no-ajax">
		<div class="pag-count" id="member-dir-count-bottom">
			<?php bp_members_pagination_count(); ?>
		</div> <!-- .pag-count -->
		<div class="pagination-links" id="member-dir-pag-bottom">
			<?php bp_members_pagination_links(); ?>
		</div> <!-- .pagination-links -->
	</div> <!-- .pagination -->

<?php else : ?>

	<div id="message" class="info">
		<p><?php esc_html_e( 'You have no pending friendship requests.', 'buddypress' ); ?></p>
	</div> <!-- #message -->

<?php endif; ?>

<?php do_action( 'bp_after_member_friend_requests_content' ); ?>
