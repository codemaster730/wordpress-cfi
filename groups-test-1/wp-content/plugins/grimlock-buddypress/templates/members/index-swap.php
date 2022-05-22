<?php
/**
 * BuddyPress - Members
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 * @version 3.0.0
 */

// @codingStandardsIgnoreFile
// Allow plugin text domain in theme.

do_action( 'bp_before_directory_members_page' ); ?>

	<div id="buddypress" class="buddypress-members-index-swap">

		<?php do_action( 'bp_before_directory_members_content' ); ?>

		<div class="buddypress-members-index-swap__content">

			<?php do_action( 'bp_before_directory_members' ); ?>

			<?php do_action( 'bp_before_directory_members_tabs' ); ?>

			<form action="" method="post" id="members-directory-form" class="pt-4 pt-sm-0">

				<h2 class="bp-screen-reader-text">
					<?php esc_html_e( 'One Member', 'buddypress' ); ?>
				</h2>

				<div id="members-index-swap">
					<div id="members-dir-list" class="members dir-list">
						<?php bp_get_template_part( 'members/members-loop-swap' ); ?>
					</div><!-- #members-dir-list -->
				</div>

				<?php do_action( 'bp_directory_members_content' ); ?>

				<?php wp_nonce_field( 'directory_members', '_wpnonce-member-filter' ); ?>

				<?php do_action( 'bp_after_directory_members_content' ); ?>

			</form><!-- #members-directory-form -->

			<?php do_action( 'bp_after_directory_members' ); ?>

		</div>

	</div><!-- #buddypress -->

<?php do_action( 'bp_after_directory_members_page' );
