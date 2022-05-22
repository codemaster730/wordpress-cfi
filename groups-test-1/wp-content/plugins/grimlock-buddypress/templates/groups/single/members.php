<?php
/**
 * BuddyPress - Groups Members
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 * @version 3.0.0
 */

// @codingStandardsIgnoreFile
// Allow plugin text domain in theme.
?>

<?php if ( bp_group_has_members( bp_ajax_querystring( 'group_members' ) ) ) : ?>

	<?php do_action( 'bp_before_group_members_content' ); ?>

	<?php do_action( 'bp_before_group_members_list' ); ?>

	<ul id="members-list" class="bp-card-list bp-card-list--members loading-list">

		<?php while ( bp_group_members() ) : bp_group_the_member(); ?>

			<li class="bp-card-list__item bp-card-list--members__item has-post-thumbnail element-animated fade-in short element-animated-delay element-animated-both">

				<div class="card">

					<div class="card-img">
						<a href="<?php bp_group_member_domain(); ?>">
							<?php bp_group_member_avatar_thumb( 'type=full' ); ?>
						</a>
					</div> <!-- .card-img -->

					<div class="card-body pt-1 pb-4 pl-2 pr-2">

						<header class="card-body-header entry-header clearfix">
							<h2 class="entry-title">
								<?php bp_group_member_link(); ?>
							</h2> <!-- .entry-title -->
						</header> <!-- .card-body-header -->

						<div class="card-body-meta">

							<div class="bp-member-xprofile-custom-fields"><?php do_action( 'grimlock_buddypress_member_xprofile_custom_fields' ); ?></div> <!-- .bp-member-xprofile-custom-fields -->

							<div class="card-body-activity">
								<?php
								$group_member_joined_since_args = array(
									'relative' => false,
								); ?>
								<span class="activity" data-livestamp="<?php bp_core_iso8601_date( bp_get_group_member_joined_since( $group_member_joined_since_args ) ); ?>"><?php bp_group_member_joined_since(); ?></span>
							</div><!-- .card-body-activity -->

						</div> <!-- .card-body-meta -->

						<?php if ( bp_is_active( 'friends' ) ) : ?>
							<div class="card-body-actions action">
								<?php bp_add_friend_button( bp_get_group_member_id(), bp_get_group_member_is_friend() ); ?>
								<?php do_action( 'bp_group_members_list_item_action' ); ?>
							</div> <!-- .card-body-actions -->
						<?php endif; ?>

					</div> <!-- .card-body -->

				</div> <!-- .card -->

				<?php do_action( 'bp_group_members_list_item' ); ?>

			</li> <!-- .bp-card-list__item -->

		<?php endwhile; ?>

	</ul> <!-- .bp-card-list -->

	<?php do_action( 'bp_after_group_members_list' ); ?>

	<div id="pag-bottom" class="pagination">
		<div class="pagination-links" id="member-pag-bottom">
			<?php bp_members_pagination_links(); ?>
		</div>
		<div class="pag-count" id="member-count-bottom">
			<?php bp_members_pagination_count(); ?>
		</div>
	</div>

	<?php do_action( 'bp_after_group_members_content' ); ?>

<?php else : ?>

	<div id="message" class="info">
		<p><?php esc_html_e( 'No members were found.', 'buddypress' ); ?></p>
	</div> <!-- #message -->

<?php endif; ?>
