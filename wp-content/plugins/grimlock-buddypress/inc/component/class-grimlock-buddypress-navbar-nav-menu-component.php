<?php
/**
 * Grimlock_BuddyPress_Navbar_Nav_Menu_Component Class
 *
 * @author  Themosaurus
 * @since   1.0.0
 * @package  grimlock
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Grimlock_BuddyPress_Navbar_Nav_Menu_Component
 */
class Grimlock_BuddyPress_Navbar_Nav_Menu_Component extends Grimlock_Component {

	/**
	 * Get notifications counts for all type of notifications depending on which BuddyPress module is activated
	 *
	 * @return array Array of notifications where the key is the type and the value is the count
	 */
	protected function get_notifications() {
		if ( ! function_exists( 'buddypress' ) ) {
			return array();
		}

		$user_id = bp_loggedin_user_id();

		$notifications = array();
		if ( bp_is_active( 'friends' ) ) {
			$notifications['friendship_requests'] = bp_friend_get_total_requests_count( $user_id );
		}
		if ( bp_is_active( 'groups' ) ) {
			$groups_invites                  = groups_get_invites_for_user( $user_id );
			$notifications['groups_invites'] = $groups_invites['total'];
		}
		if ( bp_is_active( 'notifications' ) ) {
			$notifications['notifications'] = bp_notifications_get_unread_notification_count( $user_id );
		}
		if ( bp_is_active( 'messages' ) ) {
			$notifications['messages'] = messages_get_unread_count( $user_id );
		}

		return $notifications;
	}

	/**
	 * Renders a class for each notification type that has more than one notification
	 *
	 * @param array $notifications Array of notifications counts used to print the classes.
	 */
	protected function render_has_notifications_classes( $notifications ) {
		$classes = array();
		foreach ( $notifications as $type => $count ) {
			if ( intval( $count ) > 0 ) {
				$classes[] = 'has-' . $type;
			}
		}

		echo esc_attr( implode( ' ', $classes ) );
	}

	/**
	 * Render the current component with props data on page.
	 *
	 * @since 1.0.0
	 */
	public function render() {
		if ( function_exists( 'buddypress' ) ) :

			$tooltips_enabled = apply_filters( 'grimlock_buddypress_navbar_nav_menu_tooltips_enabled', false );
			$notifications    = $this->get_notifications(); ?>

			<ul class="nav navbar-nav navbar-nav--buddypress <?php echo is_user_logged_in() ? 'logged-in' : 'logged-out'; ?>">

				<?php
				if ( apply_filters( 'grimlock_buddypress_navbar_nav_menu_item_friends_displayed', true ) && bp_is_active( 'friends' ) && is_user_logged_in() ) : ?>

					<li class="menu-item ml-0 menu-item--friends">
						<?php if ( ( $notifications['friendship_requests'] > 0 ) && is_user_logged_in() ) : ?>
							<span class="bubble-count friendship-requests-count"><?php echo esc_html( $notifications['friendship_requests'] ); ?></span>
						<?php endif; ?>
						<a href="<?php echo esc_url( is_user_logged_in() ? bp_loggedin_user_domain() . BP_FRIENDS_SLUG : wp_registration_url() ); ?>" <?php if ( $tooltips_enabled ) : ?>data-toggle="tooltip" data-placement="bottom" title="<?php esc_attr_e( 'Friends', 'grimlock-buddypress' ); ?>"<?php endif; ?>>
							<span class="sr-only"><?php
								/* translators: %s: Friends slug */
								printf( esc_html__( 'My %s', 'grimlock-buddypress' ), esc_html( BP_FRIENDS_SLUG ) ); ?></span>
						</a>
					</li>

				<?php
				endif;

				if ( apply_filters( 'grimlock_buddypress_navbar_nav_menu_item_groups_displayed', true ) && bp_is_active( 'groups' ) && is_user_logged_in() ) : ?>

					<li class="menu-item ml-0 menu-item--groups">
						<?php if ( ( $notifications['groups_invites'] > 0 ) && is_user_logged_in() ) : ?>
							<span class="bubble-count group-invites-count"><?php echo esc_html( $notifications['groups_invites'] ); ?></span>
						<?php endif; ?>
						<a href="<?php echo esc_url( is_user_logged_in() ? bp_loggedin_user_domain() . BP_GROUPS_SLUG : wp_registration_url() ); ?>" <?php if ( $tooltips_enabled ) : ?>data-toggle="tooltip" data-placement="bottom" title="<?php esc_attr_e( 'Groups', 'grimlock-buddypress' ); ?>"<?php endif; ?>>
							<span class="sr-only"><?php
								/* translators: %s: Groups slug */
								printf( esc_html__( 'My %s', 'grimlock-buddypress' ), esc_html( BP_GROUPS_SLUG ) ); ?></span>
						</a>
					</li>

				<?php
				endif;

				if ( apply_filters( 'grimlock_buddypress_navbar_nav_menu_item_notifications_displayed', true ) && bp_is_active( 'notifications' ) && is_user_logged_in() ) : ?>

					<li class="menu-item ml-0 menu-item--notifications">
						<?php if ( ( $notifications['notifications'] > 0 ) && is_user_logged_in() ) : ?>
							<span class="bubble-count notifications-count"><?php echo esc_html( $notifications['notifications'] ); ?></span>
						<?php endif; ?>
						<a href="<?php echo esc_url( is_user_logged_in() ? bp_loggedin_user_domain() . BP_NOTIFICATIONS_SLUG : wp_registration_url() ); ?>" <?php if ( $tooltips_enabled ) : ?>data-toggle="tooltip" data-placement="bottom" title="<?php esc_attr_e( 'Notifications', 'grimlock-buddypress' ); ?>"<?php endif; ?>>
							<span class="sr-only"><?php
								/* translators: %s: Notifications slug */
								printf( esc_html__( 'My %s', 'grimlock-buddypress' ), esc_html( BP_NOTIFICATIONS_SLUG ) ); ?></span>
						</a>
						<?php if ( apply_filters( 'grimlock_buddypress_navbar_nav_menu_item_notifications_list_displayed', true ) && class_exists( 'BuddyDev_BP_Notifications_Widget_Helper' ) && is_user_logged_in() ) : ?>
							<div class="sub-menu sub-menu--notifications-list">
								<?php if ( $notifications['notifications'] > 0 ) : ?>
									<?php echo do_shortcode( '[buddydev_bp_notification title="" show_count="0" show_count_in_title="0" show_empty="0"]' ); ?>
								<?php else : ?>
									<?php esc_html_e( 'You have no new notification', 'grimlock-buddypress' ); ?>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					</li>

				<?php
				endif;

				if ( apply_filters( 'grimlock_buddypress_navbar_nav_menu_item_messages_displayed', true ) && bp_is_active( 'messages' ) && is_user_logged_in() ) : ?>

					<li class="menu-item ml-0 menu-item--messages">
						<?php if ( ( $notifications['messages'] > 0 ) && is_user_logged_in() ) : ?>
							<span class="bubble-count messages-count"><?php echo esc_html( $notifications['messages'] ); ?></span>
						<?php endif; ?>
						<a href="<?php echo esc_url( is_user_logged_in() ? bp_loggedin_user_domain() . BP_MESSAGES_SLUG : wp_registration_url() ); ?>" <?php if ( $tooltips_enabled ) : ?>data-toggle="tooltip" data-placement="bottom" title="<?php esc_attr_e( 'Messages', 'grimlock-buddypress' ); ?>"<?php endif; ?>>
							<span class="sr-only"><?php
								/* translators: %s: Messages slug */
								printf( esc_html__( 'My %s', 'grimlock-buddypress' ), esc_html( BP_MESSAGES_SLUG ) ); ?></span>
						</a>
					</li>

				<?php
				endif;

				if ( apply_filters( 'grimlock_buddypress_navbar_nav_menu_item_settings_displayed', true ) && bp_is_active( 'settings' ) && is_user_logged_in() ) : ?>

					<li class="menu-item ml-0 menu-item--settings">
						<a href="<?php echo esc_url( is_user_logged_in() ? bp_loggedin_user_domain() . BP_SETTINGS_SLUG : wp_registration_url() ); ?>" <?php if ( $tooltips_enabled ) : ?>data-toggle="tooltip" data-placement="bottom" title="<?php esc_attr_e( 'Settings', 'grimlock-buddypress' ); ?>"<?php endif; ?>>
						</a>
					</li>

				<?php
				endif; ?>

				<li class="menu-item <?php echo has_nav_menu( 'user_logged_in' ) || has_nav_menu( 'user_logged_out' ) ? 'menu-item-has-children' : ''; ?> ml-0 menu-item--profile <?php $this->render_has_notifications_classes( $notifications ); ?>">
					<?php if ( is_user_logged_in() ) : ?>
						<a href="<?php echo esc_url( bp_loggedin_user_domain() ); ?>" class="menu-item--profile__link <?php echo ! has_nav_menu( 'user_logged_in' ) ? 'no-toggle' : ''; ?>">
							<span class="avatar-round-ratio"><span class="d-block pos-r h-100"><?php bp_loggedin_user_avatar( 'width=' . bp_core_avatar_thumb_width() / 2.5 . '&height=' . bp_core_avatar_thumb_height() / 2.5 ); ?></span></span>
							<span class="menu-item--profile__name ml-3 d-lg-none"><?php echo esc_html( bp_core_get_user_displayname( bp_loggedin_user_id() ) ); ?></span>
						</a>

						<?php
						if ( has_nav_menu( 'user_logged_in' ) ) :
							wp_nav_menu( array(
								'theme_location' => 'user_logged_in',
								'container'      => false,
								'menu_class'     => 'sub-menu',
							) );
						endif;
					else : ?>

						<a href="<?php echo esc_url( wp_registration_url() ); ?>" class="menu-item--profile__link d-none d-lg-block <?php echo ! has_nav_menu( 'user_logged_in' ) ? 'no-toggle' : ''; ?>">
							<span class="avatar-round-ratio"><span class="d-block pos-r h-100"><img class="img-fluid" src="<?php echo esc_url( bp_core_avatar_default_thumb() ); ?>" alt=" <?php esc_html_e( 'avatar', 'grimlock-buddypress' ); ?>" /></span></span>
						</a>

						<?php
						if ( has_nav_menu( 'user_logged_out' ) ) :
							wp_nav_menu( array(
								'theme_location' => 'user_logged_out',
								'container'      => false,
								'menu_class'     => 'sub-menu',
							) );
						endif;
					endif; ?>
				</li>
			</ul>
		<?php endif;
	}
}
