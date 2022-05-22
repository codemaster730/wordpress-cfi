<?php
/**
 * BuddyPress - Users Blogs
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 * @version 3.0.0
 */

// @codingStandardsIgnoreFile
// Allow plugin text domain in theme.
?>

	<div id="subnav" aria-label="<?php esc_attr( 'Member secondary navigation', 'buddypress' ); ?>" role="navigation" class="d-flex flex-column flex-lg-row mb-4 mt-0">

		<div class="item-list-tabs primary-list-tabs no-ajax">
			<?php if ( bp_is_my_profile() ) : ?>
				<ul class="item-list-tabs-ul clearfix">
					<?php bp_get_options_nav(); ?>
				</ul>
			<?php endif; ?>
		</div>

		<div id="members-order-select" class="last filter ml-md-auto">

			<div class="dir-filter">

				<label for="members-friends" class="sr-only">
					<?php esc_html_e( 'Order By:', 'buddypress' ); ?>
				</label>

				<div class="select-style">
					<select id="blogs-order-by">
						<option value="active"><?php esc_html_e( 'Last Active', 'buddypress' ); ?></option>
						<option value="newest"><?php esc_html_e( 'Newest', 'buddypress' ); ?></option>
						<option value="alphabetical"><?php esc_html_e( 'Alphabetical', 'buddypress' ); ?></option>
						<?php do_action( 'bp_member_blog_order_options' ); ?>
					</select>
				</div>

			</div>
		</div>

	</div><!-- .item-list-tabs -->

<?php
switch ( bp_current_action() ) :

	// Home/My Blogs.
	case 'my-sites':
		do_action( 'bp_before_member_blogs_content' ); ?>

		<div class="blogs myblogs">

			<?php bp_get_template_part( 'blogs/blogs-loop' ); ?>

		</div><!-- .blogs.myblogs -->

		<?php do_action( 'bp_after_member_blogs_content' );
		break;

	// Any other.
	default:
		bp_get_template_part( 'members/single/plugins' );
		break;
endswitch;
