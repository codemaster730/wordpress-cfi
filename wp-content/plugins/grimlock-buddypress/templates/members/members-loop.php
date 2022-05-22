<?php
/**
 * BuddyPress - Members Loop
 *
 * Querystring is set via AJAX in _inc/ajax.php - bp_legacy_theme_object_filter()
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 * @version 3.0.0
 */

// @codingStandardsIgnoreFile
// Allow plugin text domain in theme and unescaped template tags.

do_action( 'bp_before_members_loop' ); ?>

<?php if ( bp_get_current_member_type() ) : ?>
	<p class="current-member-type"><?php bp_current_member_type_message(); ?></p>
<?php endif; ?>

<?php if ( bp_has_members( bp_ajax_querystring( 'members' ) ) ) : ?>

	<?php do_action( 'bp_before_directory_members_list' ); ?>

	<ul id="members-list" class="bp-card-list bp-card-list--members loading-list" aria-live="assertive" aria-relevant="all">

		<?php while ( bp_members() ) : bp_the_member(); ?>

			<li <?php bp_member_class( array( 'bp-card-list__item bp-card-list--members__item has-post-thumbnail element-animated fade-in short element-animated-delay element-animated-both' ) ); ?>>

				<div class="card">

					<div class="card-before-img">
						<?php do_action( 'bp_directory_members_item' ); ?>
					</div>

					<div class="card-img">
						<a href="<?php bp_member_permalink(); ?>">
							<?php bp_member_avatar( 'type=full' ); ?>
						</a>
					</div> <!-- .card-img -->

					<div class="card-body">

						<header class="card-body-header entry-header clearfix">
							<h2 class="entry-title item-title">
								<a href="<?php bp_member_permalink(); ?>">
									<?php bp_member_name(); ?>
								</a>
							</h2> <!-- .entry-title -->
						</header> <!-- .card-body-header -->

						<div class="card-body-meta">

							<div class="bp-member-xprofile-custom-fields"><?php do_action( 'grimlock_buddypress_member_xprofile_custom_fields' ); ?></div> <!-- .bp-member-xprofile-custom-fields -->

							<div class="card-body-members-item">
								<?php do_action( 'bp_directory_members_item' ); ?>
							</div><!-- .card-body-members-item -->

							<?php if ( bp_get_member_latest_update() && apply_filters( 'grimlock_buddypress_members_last_activity_displayed', true ) ) : ?>
								<div class="card-body-activity">
									<span class="activity" data-livestamp="<?php bp_core_iso8601_date( bp_get_member_last_active( array( 'relative' => false ) ) ); ?>"><?php bp_member_last_active(); ?></span>
									<span class="update"><?php bp_member_latest_update(); ?></span>
								</div><!-- .card-body-activity -->
							<?php endif; ?>

						</div> <!-- .card-body-meta -->

						<?php if ( is_user_logged_in() && ( bp_get_member_user_id() !== bp_loggedin_user_id() ) ) : ?>
							<div class="card-body-actions action">
								<?php do_action( 'bp_directory_members_actions' ); ?>
								<?php grimlock_buddypress_actions_dropdown(); ?>
							</div> <!-- .card-body-actions -->
						<?php endif; ?>

					</div> <!-- .card-body -->

				</div> <!-- .card -->

			</li> <!-- .bp-card-list__item -->

		<?php endwhile; ?>

	</ul> <!-- #members-list -->

	<?php do_action( 'bp_after_directory_members_list' ); ?>

	<?php bp_member_hidden_fields(); ?>

	<div id="pag-bottom" class="pagination">
		<div class="pagination-links" id="member-dir-pag-bottom">
			<?php bp_members_pagination_links(); ?>
		</div> <!-- .pagination-links -->
		<div class="pag-count" id="member-dir-count-bottom">
			<?php bp_members_pagination_count(); ?>
		</div> <!-- .pag-count -->
	</div> <!-- .pagination -->

<?php else : ?>
	<div id="message" class="info">
		<p><?php esc_html_e( 'Sorry, no members were found.', 'buddypress' ); ?></p>
	</div> <!-- #message -->
<?php endif; ?>

<?php do_action( 'bp_after_members_loop' ); ?>
