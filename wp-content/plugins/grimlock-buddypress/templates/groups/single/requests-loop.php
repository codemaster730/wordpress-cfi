<?php
/**
 * BuddyPress - Groups Requests Loop
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 * @version 3.0.0
 */

// @codingStandardsIgnoreFile
// Allow plugin text domain in theme.
?>

<?php if ( bp_group_has_membership_requests( bp_ajax_querystring( 'membership_requests' ) ) ) : ?>

	<ul id="request-list" class="bp-card-list bp-card-list--groups">
		<?php while ( bp_group_membership_requests() ) : bp_group_the_membership_request(); ?>

			<li class="group-request-list bp-card-list__item bp-card-list--groups__item has-post-thumbnail element-animated fade-in short element-animated-delay element-animated-both">

				<div class="card">

					<div class="card-img">
						<?php bp_group_request_user_avatar_thumb(); ?>
					</div> <!-- .card-img -->

					<div class="card-body pt-1 pb-4 pl-2 pr-2">

						<header class="card-body-header entry-header clearfix">
							<h2 class="entry-title">
								<?php bp_group_request_user_link(); ?>
							</h2>
						</header>

						<div class="card-body-meta">
							<div class="item-desc"><?php bp_group_request_comment(); ?></div>
							<div class="card-body-activity"><?php bp_group_request_time_since_requested(); ?></div>
							<?php do_action( 'bp_group_membership_requests_admin_item' ); ?>
						</div>

						<div class="card-body-actions action">
							<?php
								// Group membership accept button.
								$accept_button_args = array(
									'id'            => 'group_membership_accept',
									'component'     => 'groups',
									'wrapper_class' => 'accept',
									'link_href'     => bp_get_group_request_accept_link(),
									'link_text'     => esc_html__( 'Accept', 'buddypress' ),
								);
								bp_button( $accept_button_args );

								// Group membership reject button.
								$reject_button_args = array(
									'id'            => 'group_membership_reject',
									'component'     => 'groups',
									'wrapper_class' => 'reject',
									'link_href'     => bp_get_group_request_reject_link(),
									'link_text'     => esc_html__( 'Reject', 'buddypress' ),
								);
								bp_button( $reject_button_args );

								do_action( 'bp_group_membership_requests_admin_item_action' ); ?>
						</div>

					</div>

				</div>

			</li>

		<?php endwhile; ?>
	</ul>

	<div id="pag-bottom" class="pagination">
		<div class="pag-count" id="group-mem-requests-count-bottom">
			<?php bp_group_requests_pagination_count(); ?>
		</div>
		<div class="pagination-links" id="group-mem-requests-pag-bottom">
			<?php bp_group_requests_pagination_links(); ?>
		</div>
	</div>
<?php else : ?>
	<div id="message" class="info">
		<p><?php esc_html_e( 'There are no pending membership requests.', 'buddypress' ); ?></p>
	</div>
<?php endif; ?>
