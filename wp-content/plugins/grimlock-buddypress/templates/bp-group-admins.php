<?php
/**
 * BuddyPress - Group Admins
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 * @version 3.0.0
 */

// @codingStandardsIgnoreFile
// Allow plugin text domain in theme.
?>

<div class="modal fade" id="adminModal" tabindex="-1" role="dialog" aria-labelledby="groups-admin-modal" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="groups-admin-modal">
					<?php esc_html_e( 'Admins' , 'buddypress' ); ?> <?php if ( bp_group_has_moderators() ) : ?>& <?php esc_html_e( 'Mods' , 'buddypress' ); ?><?php endif; ?> <?php esc_html_e( 'of' , 'buddypress' ); ?> <?php bp_group_name(); ?>
				</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<?php if ( bp_group_is_visible() ) : ?>

					<h5 class="mb-1"><?php esc_html_e( 'Group Admins', 'buddypress' ); ?></h5>
					<hr class="mt-0"/>

					<?php bp_group_list_admins(); ?>

					<?php do_action( 'bp_after_group_menu_admins' ); ?>

					<?php if ( bp_group_has_moderators() ) : ?>

						<?php do_action( 'bp_before_group_menu_mods' ); ?>

						<h5 class="mb-1"><?php esc_html_e( 'Group Mods' , 'buddypress' ); ?></h5>
						<hr class="mt-0"/>

						<?php bp_group_list_mods(); ?>

						<?php do_action( 'bp_after_group_menu_mods' ); ?>

					<?php endif; ?>

				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
