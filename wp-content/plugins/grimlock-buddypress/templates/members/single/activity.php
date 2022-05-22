<?php
/**
 * BuddyPress - Users Activity
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 * @version 3.0.0
 */

// @codingStandardsIgnoreFile
// Allow plugin text domain in theme.
?>

<?php do_action( 'bp_before_member_activity_post_form' ); ?>

<?php
if ( is_user_logged_in() && bp_is_my_profile() && ( ! bp_current_action() || bp_is_current_action( 'just-me' ) ) ) :
	bp_get_template_part( 'activity/post-form' );
endif;

do_action( 'bp_after_member_activity_post_form' );
do_action( 'bp_before_member_activity_content' ); ?>

<div id="subnav" class="d-flex flex-column flex-lg-row mb-4 mt-0" aria-label="<?php esc_attr_e( 'Member secondary navigation', 'buddypress' ); ?>" role="navigation">
	<div class="item-list-tabs primary-list-tabs no-ajax mb-3 mb-md-0">
		<ul class="item-list-tabs-ul clearfix">
			<?php bp_get_options_nav(); ?>
		</ul>
	</div>
	<div id="activity-filter-select" class="last ml-md-auto">
		<div class="dir-filter">
			<label class="sr-only" for="activity-filter-by">
				<?php esc_html_e( 'Show:', 'buddypress' ); ?>
			</label><!-- .sr-only -->
			<div class="select-style">
				<select id="activity-filter-by">
					<option value="-1"><?php esc_html_e( '&mdash; Everything &mdash;', 'buddypress' ); ?></option>
					<?php bp_activity_show_filters(); ?>
					<?php do_action( 'bp_member_activity_filter_options' ); ?>
				</select>
			</div><!-- .select-style -->
		</div><!-- .dir-filter -->
	</div><!-- #activity-filter-select -->
</div><!-- .item-list-tabs -->

<div class="loading-list">
	<div class="activity" aria-live="polite" aria-atomic="true" aria-relevant="all">
		<?php bp_get_template_part( 'activity/activity-loop' ); ?>
	</div><!-- .activity -->
</div><!-- .loading-list -->

<?php do_action( 'bp_after_member_activity_content' ); ?>
