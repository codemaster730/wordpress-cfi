<?php
/**
 * BuddyPress - Groups Admin
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 * @version 3.0.0
 */

// @codingStandardsIgnoreFile
// Allow plugin text domain in theme.
?>
<div id="subnav" class="d-flex flex-column flex-lg-row mb-4 mt-0" aria-label="<?php esc_attr_e( 'Group secondary navigation', 'buddypress' ); ?>" role="navigation">
	<div class="item-list-tabs primary-list-tabs no-ajax mb-3 mb-md-0">
		<ul class="item-list-tabs-ul clearfix">
			<?php bp_group_admin_tabs(); ?>
		</ul>
	</div>
</div><!-- .item-list-tabs -->

<?php do_action( 'bp_before_group_admin_form' ); ?>

<form action="<?php bp_group_admin_form_action(); ?>" name="group-settings-form" id="group-settings-form" class="standard-form" method="post" enctype="multipart/form-data">

	<?php do_action( 'bp_before_group_admin_content' ); ?>

	<?php if ( bp_is_group_admin_screen( bp_action_variable() ) ) : ?>

		<?php bp_get_template_part( 'groups/single/admin/' . bp_action_variable() ); ?>

	<?php endif; ?>

	<?php do_action( 'groups_custom_edit_steps' ); ?>

	<?php do_action( 'bp_after_group_admin_content' ); ?>

</form><!-- #group-settings-form -->

<?php do_action( 'bp_after_group_admin_form' ); ?>
