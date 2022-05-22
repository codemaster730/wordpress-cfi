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

global $members_template;
do_action( 'bp_before_members_loop' ); ?>

<?php if ( bp_get_current_member_type() ) : ?>
	<p class="current-member-type"><?php bp_current_member_type_message(); ?></p>
<?php endif; ?>

<?php if ( bp_has_members( bp_ajax_querystring( 'members' ) . '&per_page=20' ) ) : ?>

	<?php do_action( 'bp_before_directory_members_list' ); ?>

	<ul id="members-list" class="bp-card-list bp-card-list--members loading-list" aria-live="assertive" aria-relevant="all">
		<?php do_action( 'grimlock_buddypress_member_swap_loop' ); ?>
	</ul> <!-- #members-list -->

	<?php do_action( 'bp_after_directory_members_list' ); ?>

	<?php bp_member_hidden_fields(); ?>

	<div id="pag-bottom" class="bp-swap-pagination">
		<div class="pagination-links" id="member-dir-pag-bottom" data-max-page="<?php echo ceil( (int) $members_template->total_member_count / (int) $members_template->pag_num ); ?>">
			<button class="prev page-numbers d-none"></button>
			<button class="next page-numbers"></button>
		</div>
		<div class="pag-count" id="member-dir-count-bottom">
			<?php bp_members_pagination_count(); ?>
		</div>
	</div>

<?php else : ?>
	<div id="message" class="info">
		<p><?php esc_html_e( 'Sorry, no members were found.', 'buddypress' ); ?></p>
	</div>
<?php endif; ?>

<?php do_action( 'bp_after_members_loop' ); ?>
