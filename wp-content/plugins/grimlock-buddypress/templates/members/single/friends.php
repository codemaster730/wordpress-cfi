<?php
/**
 * BuddyPress - Users Friends
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
				<ul class="item-list-tabs-ul clearfix pos-r">
					<?php
					bp_get_options_nav();
					$user_id = bp_loggedin_user_id();
					$notifications_friend_request = bp_friend_get_total_requests_count( $user_id );
					if ( bp_is_active( 'friends' ) && $notifications_friend_request > 0 ): ?>
						<li class="item-notification-friend-request"></li>
					<?php
					endif; ?>
				</ul>
			<?php endif; ?>
		</div>

		<?php if ( ! bp_is_current_action( 'requests' ) ) : ?>

			<div id="members-order-select" class="last filter ml-md-auto">

				<div class="dir-filter">

					<label for="members-friends" class="sr-only">
						<?php esc_html_e( 'Order By:', 'buddypress' ); ?>
					</label>

					<div class="select-style">
						<select id="members-friends">
							<option value="active"><?php esc_html_e( 'Last Active', 'buddypress' ); ?></option>
							<option value="newest"><?php esc_html_e( 'Newest Registered', 'buddypress' ); ?></option>
							<option value="alphabetical"><?php esc_html_e( 'Alphabetical', 'buddypress' ); ?></option>
							<?php do_action( 'bp_member_friends_order_options' ); ?>
						</select>
					</div>

				</div>
			</div>

		<?php endif; ?>

	</div>

<?php
switch ( bp_current_action() ) :

	case 'my-friends':
		// Home/My Friends.
		do_action( 'bp_before_member_friends_content' ); ?>

		<?php if ( is_user_logged_in() ) : ?>
		<h2 class="bp-screen-reader-text">
			<?php esc_html_e( 'My friends', 'buddypress' ); ?>
		</h2>
	<?php else : ?>
		<h2 class="bp-screen-reader-text">
			<?php esc_html_e( 'Friends', 'buddypress' ); ?>
		</h2>
	<?php endif; ?>

		<div class="members friends">
			<?php bp_get_template_part( 'members/members-loop' ); ?>
		</div><!-- .members.friends -->

		<?php
		do_action( 'bp_after_member_friends_content' );
		break;

	case 'requests':
		bp_get_template_part( 'members/single/friends/requests' );
		break;

	default:
		// Any other.
		bp_get_template_part( 'members/single/plugins' );
		break;
endswitch;
