<?php
/**
 * BuddyPress - Users Forums
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 * @version 3.0.0
 */

// @codingStandardsIgnoreFile
// Allow plugin text domain in theme.
?>

<div id="subnav" aria-label="<?php esc_attr_e( 'Member secondary navigation', 'buddypress' ); ?>" role="navigation" class="d-flex flex-column flex-lg-row mb-4 mt-0">

	<div class="item-list-tabs primary-list-tabs no-ajax">
		<ul class="item-list-tabs-ul clearfix">
			<?php bp_get_options_nav(); ?>
		</ul>
	</div>

	<div id="forums-order-select" class="last filter ml-md-auto">
		<label for="forums-order-by"><?php esc_html_e( 'Order By:', 'buddypress' ); ?></label>
		<select id="forums-order-by">
			<option value="active"><?php esc_html_e( 'Last Active', 'buddypress' ); ?></option>
			<option value="popular"><?php esc_html_e( 'Most Posts', 'buddypress' ); ?></option>
			<option value="unreplied"><?php esc_html_e( 'Unreplied', 'buddypress' ); ?></option>
			<?php do_action( 'bp_forums_directory_order_options' ); ?>
		</select>
	</div>

</div><!-- .item-list-tabs -->

<?php

if ( bp_is_current_action( 'favorites' ) ) :

	bp_get_template_part( 'members/single/forums/topics' );

else :

	/**
	 * Fires before the display of member forums content.
	 *
	 * @since 1.5.0
	 */
	do_action( 'bp_before_member_forums_content' ); ?>

	<div class="forums myforums">
		<?php bp_get_template_part( 'forums/forums-loop' ); ?>
	</div>

	<?php
	/**
	 * Fires after the display of member forums content.
	 *
	 * @since 1.5.0
	 */
	do_action( 'bp_after_member_forums_content' ); ?>

<?php endif; ?>
