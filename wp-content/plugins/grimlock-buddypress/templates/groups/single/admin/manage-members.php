<?php
/**
 * BuddyPress - Groups Admin - Manage Members
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 * @version 3.0.0
 */

// @codingStandardsIgnoreFile
// Allow plugin text domain in theme.
?>

<h2 class="bp-screen-reader-text"><?php esc_html_e( 'Manage Members', 'buddypress' ); ?></h2>

<?php do_action( 'bp_before_group_manage_members_admin' ); ?>

<div aria-live="polite" aria-relevant="all" aria-atomic="true">

	<div class="bp-widget group-members-list group-admins-list">

		<h3 class="section-header mt-4 mb-4"><?php esc_html_e( 'Administrators', 'buddypress' ); ?></h3>

		<?php
		$bp_group_has_members_args = array(
			'per_page'   => 16,
			'group_role' => array( 'admin' ),
			'page_arg'   => 'mlpage-admin',
		);
		if ( bp_group_has_members( $bp_group_has_members_args ) ) : ?>

			<ul id="admins-list" class="bp-card-list bp-card-list--members">

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
									<span class="activity"><?php bp_group_member_joined_since(); ?></span>
									<?php do_action( 'bp_group_manage_members_admin_item', 'admins-list' ); ?>
								</div> <!-- .card-body-meta -->

								<div class="card-body-actions action">
									<?php if ( count( bp_group_admin_ids( false, 'array' ) ) > 1 ) : ?>
										<div class="generic-button">
											<a class="button confirm admin-demote-to-member text-danger" href="<?php bp_group_member_demote_link(); ?>" title="<?php esc_html_e( 'Demote to Member', 'buddypress' ); ?>"><?php esc_html_e( 'Demote to Member', 'buddypress' ); ?></a>
										</div>
									<?php endif; ?>
									<?php do_action( 'bp_group_manage_members_admin_actions', 'admins-list' ); ?>
								</div> <!-- .card-body-actions -->

							</div> <!-- .card-body -->

						</div> <!-- .card -->

					</li> <!-- .bp-card-list__item -->

				<?php endwhile; ?>

			</ul> <!-- .bp-card-list -->

			<?php if ( bp_group_member_needs_pagination() ) : ?>

				<div class="pagination no-ajax">

					<div id="member-admin-pagination" class="pagination-links">
						<?php bp_group_member_admin_pagination(); ?>
					</div>

					<div id="member-count" class="pag-count">
						<?php bp_group_member_pagination_count(); ?>
					</div>

				</div>

			<?php endif; ?>

		<?php else : ?>

			<div id="message" class="info">
				<p><?php esc_html_e( 'No group administrators were found.', 'buddypress' ); ?></p>
			</div>

		<?php endif; ?>
	</div>

	<div class="bp-widget group-members-list group-mods-list">

		<h3 class="section-header mt-4 mb-4"><?php esc_html_e( 'Moderators', 'buddypress' ); ?></h3>

		<?php
		$bp_group_has_members_args = array(
			'per_page'   => 16,
			'group_role' => array( 'mod' ),
			'page_arg'   => 'mlpage-mod',
		);
		if ( bp_group_has_members( $bp_group_has_members_args ) ) : ?>

			<ul id="mods-list" class="bp-card-list bp-card-list--members">

				<?php while ( bp_group_members() ) : bp_group_the_member(); ?>
					<li class="bp-card-list__item bp-card-list--members__item has-post-thumbnail element-animated appear-from-bottom short element-animated-delay element-animated-both">

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
									<span class="activity"><?php bp_group_member_joined_since(); ?></span>
									<?php do_action( 'bp_group_manage_members_admin_item', 'admins-list' ); ?>
								</div> <!-- .card-body-meta -->

								<div class="card-body-actions action">
									<div class="generic-button">
										<a href="<?php bp_group_member_promote_admin_link(); ?>" class="button confirm mod-promote-to-admin text-info" title="<?php esc_html_e( 'Promote to Admin', 'buddypress' ); ?>"><?php esc_html_e( 'Promote to Admin', 'buddypress' ); ?></a>
									</div>
									<div class="generic-button">
										<a class="button confirm mod-demote-to-member text-danger" href="<?php bp_group_member_demote_link(); ?>" title="<?php esc_html_e( 'Demote to Member', 'buddypress' ); ?>"><?php esc_html_e( 'Demote to Member', 'buddypress' ); ?></a>
									</div>
									<?php do_action( 'bp_group_manage_members_admin_actions', 'mods-list' ); ?>
								</div> <!-- .card-body-actions -->

							</div> <!-- .card-body -->

						</div> <!-- .card -->

					</li> <!-- .bp-card-list__item -->

				<?php endwhile; ?>

			</ul> <!-- .bp-card-list -->

			<?php if ( bp_group_member_needs_pagination() ) : ?>

				<div class="pagination no-ajax">

					<div id="member-admin-pagination" class="pagination-links">
						<?php bp_group_member_admin_pagination(); ?>
					</div>

					<div id="member-count" class="pag-count">
						<?php bp_group_member_pagination_count(); ?>
					</div>

				</div>

			<?php endif; ?>

		<?php else : ?>

			<div id="message" class="info">
				<p><?php esc_html_e( 'No group moderators were found.', 'buddypress' ); ?></p>
			</div>

		<?php endif; ?>
	</div>

	<div class="bp-widget group-members-list">

		<h3 class="section-header mt-4 mb-4"><?php esc_html_e( 'Members', 'buddypress' ); ?></h3>

		<?php
		$bp_group_has_members_args = array(
			'per_page'       => 16,
			'exclude_banned' => 0,
		);
		if ( bp_group_has_members( $bp_group_has_members_args ) ) : ?>

			<ul id="members-list" class="bp-card-list bp-card-list--members" aria-live="assertive" aria-relevant="all">
				<?php while ( bp_group_members() ) : bp_group_the_member(); ?>

					<li class="<?php bp_group_member_css_class(); ?> bp-card-list__item bp-card-list--members__item has-post-thumbnail element-animated fade-in short element-animated-delay element-animated-both">

						<div class="card">

							<div class="card-img">
								<a href="<?php bp_group_member_domain(); ?>">
									<?php bp_group_member_avatar_thumb( 'type=full' ); ?>
								</a>
							</div>

							<div class="card-body pt-1 pb-4 pl-2 pr-2">

								<header class="card-body-header entry-header clearfix">
									<h2 class="entry-title">
										<?php bp_group_member_link(); ?>
									</h2> <!-- .entry-title -->
								</header> <!-- .card-body-header -->

								<div class="card-body-meta">
									<div class="bp-member-xprofile-custom-fields"><?php do_action( 'grimlock_buddypress_member_xprofile_custom_fields' ); ?></div> <!-- .bp-member-xprofile-custom-fields -->
									<span class="activity"><?php bp_group_member_joined_since(); ?></span>
									<?php do_action( 'bp_group_manage_members_admin_item', 'admins-list' ); ?>
									<?php
									if ( bp_get_group_member_is_banned() ) {
										echo ' <div class="banned">';
										esc_html_e( '- banned -', 'buddypress' );
										echo '</div>';
									} ?>
								</div> <!-- .card-body-meta -->

								<div class="card-body-actions action">
									<?php if ( bp_get_group_member_is_banned() ) : ?>
										<div class="generic-button">
											<a href="<?php bp_group_member_unban_link(); ?>" class="button confirm member-unban text-success" title="<?php esc_html_e( 'Remove Ban', 'buddypress' ); ?>"><?php esc_html_e( 'Remove Ban', 'buddypress' ); ?></a>
										</div>
									<?php else : ?>
										<div class="generic-button">
											<a href="<?php bp_group_member_ban_link(); ?>" class="button confirm member-ban text-dark" title="<?php esc_html_e( 'Kick &amp; Ban', 'buddypress' ); ?>"><?php esc_html_e( 'Kick &amp; Ban', 'buddypress' ); ?></a>
										</div>
										<div class="generic-button">
											<a href="<?php bp_group_member_promote_mod_link(); ?>" class="button confirm member-promote-to-mod text-info" title="<?php esc_html_e( 'Promote to Mod', 'buddypress' ); ?>"><?php esc_html_e( 'Promote to Mod', 'buddypress' ); ?></a>
										</div>
										<div class="generic-button">
											<a href="<?php bp_group_member_promote_admin_link(); ?>" class="button confirm member-promote-to-admin text-info" title="<?php esc_html_e( 'Promote to Admin', 'buddypress' ); ?>"><?php esc_html_e( 'Promote to Admin', 'buddypress' ); ?></a>
										</div>
									<?php endif; ?>
									<div class="generic-button">
										<a href="<?php bp_group_member_remove_link(); ?>" class="button confirm remove text-danger" title="<?php esc_html_e( 'Remove from group', 'buddypress' ); ?>"><?php esc_html_e( 'Remove from group', 'buddypress' ); ?></a>
									</div>
									<?php do_action( 'bp_group_manage_members_admin_actions', 'members-list' ); ?>
								</div> <!-- .card-body-actions -->

							</div> <!-- .card-body -->

						</div> <!-- .card -->

					</li> <!-- .bp-card-list__item -->

				<?php endwhile; ?>

			</ul> <!-- .bp-card-list -->

			<?php if ( bp_group_member_needs_pagination() ) : ?>

				<div class="pagination no-ajax">

					<div id="member-admin-pagination" class="pagination-links">
						<?php bp_group_member_admin_pagination(); ?>
					</div>

					<div id="member-count" class="pag-count">
						<?php bp_group_member_pagination_count(); ?>
					</div>

				</div>

			<?php endif; ?>

		<?php else : ?>

			<div id="message" class="info">
				<p><?php esc_html_e( 'No group members were found.', 'buddypress' ); ?></p>
			</div>

		<?php endif; ?>
	</div>

</div>

<?php do_action( 'bp_after_group_manage_members_admin' ); ?>
