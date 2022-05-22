<?php
/**
 * BuddyPress - Groups Activity
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 * @version 3.0.0
 */

// @codingStandardsIgnoreFile
// Allow plugin text domain in theme.
?>

<?php do_action( 'bp_before_group_activity_post_form' ); ?>

<?php if ( is_user_logged_in() && bp_group_is_member() ) : ?>
	<?php bp_get_template_part( 'activity/post-form' ); ?>
<?php endif; ?>

<?php do_action( 'bp_after_group_activity_post_form' ); ?>
<?php do_action( 'bp_before_group_activity_content' ); ?>

<div class="d-flex flex-column flex-lg-row mb-4 mt-0" id="subnav" aria-label="<?php esc_attr_e( 'Group secondary navigation', 'buddypress' ); ?>" role="navigation">
	<div id="activity-filter-select" class="last ml-md-auto">
		<div class="dir-filter">
			<label class="sr-only" for="activity-filter-by">
				<?php esc_html_e( 'Show:', 'buddypress' ); ?>
			</label><!-- .sr-only -->
			<div class="select-style">
				<select id="activity-filter-by">
					<option value="-1"><?php esc_html_e( '&mdash; Everything &mdash;', 'buddypress' ); ?></option>
					<?php bp_activity_show_filters( 'group' ); ?>
					<?php do_action( 'bp_group_activity_filter_options' ); ?>
				</select>
			</div><!-- .select-style -->
		</div><!-- .dir-filter -->
	</div><!-- #activity-filter-select -->
</div><!-- .item-list-tabs -->

<div class="activity single-group" aria-live="polite" aria-atomic="true" aria-relevant="all">

	<?php bp_get_template_part( 'activity/activity-loop' ); ?>

</div><!-- .activity.single-group -->

<div class="feed d-none">
	<a target="_blank" href="<?php bp_group_activity_feed_link(); ?>" class="bp-tooltip" data-bp-tooltip="<?php esc_attr_e( 'RSS Feed', 'buddypress' ); ?>" aria-label="<?php esc_attr_e( 'RSS Feed', 'buddypress' ); ?>"><?php esc_html_e( 'RSS', 'buddypress' ); ?></a>
	<?php do_action( 'bp_activity_syndication_options' ); ?>
</div> <!-- .feed -->

<?php do_action( 'bp_after_group_activity_content' ); ?>
