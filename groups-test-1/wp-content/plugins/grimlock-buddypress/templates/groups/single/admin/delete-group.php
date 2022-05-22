<?php
/**
 * BuddyPress - Groups Admin - Delete Group
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 * @version 3.0.0
 */

// @codingStandardsIgnoreFile
// Allow plugin text domain in theme.
?>

<h2 class="bp-screen-reader-text"><?php esc_html_e( 'Delete Group', 'buddypress' ); ?></h2>

<?php do_action( 'bp_before_group_delete_admin' ); ?>

<div id="message" class="info">
	<p><?php esc_html_e( 'WARNING: Deleting this group will completely remove ALL content associated with it. There is no way back. Please be careful with this option.', 'buddypress' ); ?></p>
</div>

<label for="delete-group-understand"><input type="checkbox" name="delete-group-understand" id="delete-group-understand" value="1" onclick="if(this.checked) { document.getElementById('delete-group-button').disabled = ''; } else { document.getElementById('delete-group-button').disabled = 'disabled'; }" /> <?php esc_html_e( 'I understand the consequences of deleting this group.', 'buddypress' ); ?></label>

<?php do_action( 'bp_after_group_delete_admin' ); ?>

<div class="submit">
	<input type="submit" disabled="disabled" value="<?php esc_attr_e( 'Delete Group', 'buddypress' ); ?>" id="delete-group-button" name="delete-group-button" />
</div>

<?php wp_nonce_field( 'groups_delete_group' ); ?>
