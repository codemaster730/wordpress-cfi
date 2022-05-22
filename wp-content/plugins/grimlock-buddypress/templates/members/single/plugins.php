<?php
/**
 * BuddyPress - Users Plugins Template
 *
 * 3rd-party plugins should use this template to easily add template
 * support to their plugins for the members component.
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 * @version 3.0.0
 */

// @codingStandardsIgnoreFile
// Allow plugin text domain in theme.
do_action( 'bp_before_member_plugin_template' ); ?>

<?php if ( ! bp_is_current_component_core() ) : ?>
	<div id="subnav" aria-label="<?php esc_attr_e( 'Member secondary navigation', 'buddypress' ); ?>" role="navigation" class="d-flex flex-column flex-lg-row mt-0">
		<div class="item-list-tabs primary-list-tabs no-ajax">
			<ul class="item-list-tabs-ul clearfix mb-4"><?php bp_get_options_nav(); ?><?php do_action( 'bp_member_plugin_options_nav' ); ?></ul>
		</div>
	</div><!-- .item-list-tabs -->
<?php endif; ?>

<?php if ( has_action( 'bp_template_title' ) ) : ?>
	<h3><?php do_action( 'bp_template_title' ); ?></h3>
<?php endif; ?>

<?php do_action( 'bp_template_content' ); ?>

<?php do_action( 'bp_after_member_plugin_template' ); ?>
