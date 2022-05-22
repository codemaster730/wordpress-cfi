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

<?php
$current_user_id = get_current_user_id();

$query_args = array();
wp_parse_str( bp_ajax_querystring( 'members' ), $query_args );
$query_args['per_page'] = 24;

// Remove current user from query
if ( ! empty( $current_user_id ) ) {
	if ( empty( $query_args['exclude'] ) ) {
		$query_args['exclude'] = array();
	}
	elseif ( is_string( $query_args['exclude'] ) ) {
		$query_args['exclude'] = explode( ',', $query_args['exclude'] );
	}

	if ( ! in_array( $current_user_id, $query_args['exclude'] ) ) {
		$query_args['exclude'][] = $current_user_id;
	}
}

$query_args = apply_filters( 'bp_member_swipe_loop_query_args', $query_args );

if ( $query_args['type'] === 'random' ) {
	// Generate random seed for random query type to keep the avoid repetition between pages
	global $bp_member_swipe;
	$bp_member_swipe->bp_user_query_random_seed = rand();
	$query_args['random_seed'] = $bp_member_swipe->bp_user_query_random_seed;
}

$members_list_classes = apply_filters( 'bp_member_swipe_members_list_classes', array(
	'bp-member-swipe-list',
	'swiper-wrapper',
) );
?>

<?php if ( bp_has_members( $query_args ) ) : ?>

	<?php do_action( 'bp_before_directory_members_list' ); ?>

	<ul id="members-list" class="<?php echo esc_attr( join( ' ', array_unique( $members_list_classes ) ) ); ?>" aria-live="assertive" aria-relevant="all" data-query-args="<?php echo esc_attr( wp_json_encode( $query_args ) ); ?>">
		<?php bp_get_template_part( 'members/members-swipe-loop-items' ); ?>
	</ul> <!-- #members-swipe-list -->

	<?php do_action( 'bp_after_directory_members_list' ); ?>

	<?php bp_member_hidden_fields(); ?>

	<div class="bp-member-swipe-pagination">
		<div class="pagination-links bp-member-swipe-pagination__links" data-max-page="<?php echo esc_attr( ceil( (int) $members_template->total_member_count / (int) $members_template->pag_num ) ); ?>">
			<button class="bp-member-swipe-pagination__link bp-member-swipe-pagination__link--prev prev page-numbers" style="display: none;"><i class="dashicons dashicons-arrow-left-alt2"></i></button>
			<button class="bp-member-swipe-pagination__link bp-member-swipe-pagination__link--next next page-numbers"><i class="dashicons dashicons-arrow-right-alt2"></i></button>
		</div> <!-- .bp-member-swipe-pagination -->
	</div> <!-- .bp-member-swipe-pagination -->

<?php else : ?>
	<div id="message" class="info">
		<p><?php esc_html_e( 'Sorry, no members were found.', 'buddypress' ); ?></p>
	</div>
<?php endif; ?>

<?php do_action( 'bp_after_members_loop' ); ?>
