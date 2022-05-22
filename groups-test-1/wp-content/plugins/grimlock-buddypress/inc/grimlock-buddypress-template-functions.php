<?php
/**
 * Grimlock template functions for BuddyPress.
 *
 * @package grimlock-buddypress
 */

if ( ! function_exists( 'grimlock_buddypress_member_swap_loop_template_part' ) ) :
	/**
	 * Display member swap loop markups.
	 *
	 * @since 1.0.0
	 */
	function grimlock_buddypress_member_swap_loop_template_part() {
		$classes = array(
			'bp-card-list__item',
			'bp-card-list--members__item',
			'has-post-thumbnail',
		);

		if ( wp_doing_ajax() ) :
			$classes[] = 'd-none';
		endif;

		while ( bp_members() ) : bp_the_member(); ?>

			<li <?php bp_member_class( $classes ); ?>>

				<div class="card">

					<div class="card-before-img">
						<?php do_action( 'bp_directory_members_item' ); ?>
					</div>

						<div class="card-img">
							<a href="<?php bp_member_permalink(); ?>">
								<?php bp_member_avatar( 'type=full' ); ?>
							</a>
						</div><!-- .card-img -->

						<div class="card-body">

							<header class="card-body-header entry-header clearfix">
								<h2 class="entry-title item-title">
									<a href="<?php bp_member_permalink(); ?>">
										<?php bp_member_name(); ?>
									</a>
								</h2><!-- .entry-title -->
							</header><!-- .card-body-header -->

							<div class="card-body-meta">

								<div class="bp-member-xprofile-custom-fields"><?php do_action( 'grimlock_buddypress_member_xprofile_custom_fields' ); ?></div> <!-- .bp-member-xprofile-custom-fields -->

								<div class="card-body-members-item">
									<?php do_action( 'bp_directory_members_item' ); ?>
								</div><!-- .card-body-members-item -->

							</div> <!-- .card-body-meta -->

							<?php if ( is_user_logged_in() && ( bp_get_member_user_id() !== bp_loggedin_user_id() ) ) : ?>
								<div class="card-body-actions action">
									<?php do_action( 'bp_directory_members_actions' ); ?>
									<?php grimlock_buddypress_actions_dropdown(); ?>
								</div> <!-- .card-body-actions -->
							<?php endif; ?>

						</div><!-- .card-body -->

				</div><!-- .card -->

			</li><!-- .bp-card-list__item -->
			<?php
			if ( ! in_array( 'd-none', $classes, true ) ) :
				$classes[] = 'd-none';
			endif;
		endwhile;
	}
endif;

if ( ! function_exists( 'grimlock_buddypress_online_badge' ) ) :
	/**
	 * Display member username.
	 *
	 * @since 1.0.0
	 */

	function grimlock_buddypress_online_badge() {
		$displayed_user_id = bp_displayed_user_id();
		if ( bp_has_members( 'type=online&include=' . $displayed_user_id) ) {
			$is_online = true;
		} else {
			$is_online = false;
		}
		if ( $is_online ) { ?>
			<span class="member-badge-state is-online bg-success" data-toggle="tooltip" data-placement="top" title="<?php esc_html_e( 'Online', 'grimlock-buddypress' ); ?>"></span>
		<?php }
	}

endif;

if ( ! function_exists( 'grimlock_buddypress_actions_dropdown' ) ) :
	/**
	 * Output a dropdown with BP actions.
	 *
	 * @since 1.0.0
	 */
	function grimlock_buddypress_actions_dropdown() { ?>
		<div class="dropdown dropdown--more-actions dropdown--more-actions-list generic-button">
			<a href="#" class="dropdown-toggle mr-0" id="dropdownMoreActions" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-offset="0,15">
				<?php echo esc_html_x( 'More', 'Grimlock BuddyPress text button more', 'grimlock-buddypress' ); ?>
			</a> <!-- .dropdown--toggle -->
			<div class="dropdown-menu" aria-labelledby="dropdownMoreActions">
				<?php do_action( 'bp_directory_members_actions' ); ?>
			</div> <!-- .dropdown-menu -->
		</div> <!-- .dropdown--more-actions -->
	<?php }
endif;

if ( ! function_exists( 'grimlock_buddypress_actions_dropdown_profile' ) ) :
	/**
	 * Output a dropdown with BP actions.
	 *
	 * @since 1.0.0
	 */
	function grimlock_buddypress_actions_dropdown_profile() { ?>
		<div class="dropdown dropdown--more-actions dropdown--more-actions-list generic-button">
			<a href="#" class="dropdown-toggle mr-0" id="dropdownMoreActionsProfile" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-offset="0,15">
				<?php echo esc_html_x( 'More', 'Grimlock BuddyPress text button more', 'grimlock-buddypress' ); ?>
			</a> <!-- .dropdown--toggle -->
			<div class="dropdown-menu" aria-labelledby="dropdownMoreActionsProfile">
				<?php do_action( 'bp_member_header_actions' ); ?>
			</div> <!-- .dropdown-menu -->
		</div> <!-- .dropdown--more-actions -->
	<?php }
endif;

if ( ! function_exists( 'grimlock_buddypress_get_displayed_user_secondary_nav' ) ) :
	/**
	 * Display a secondary nav on member profiles containing the "notifications", "messages" and "settings" icons
	 *
	 * @since 1.0.0
	 */
	function grimlock_buddypress_get_displayed_user_secondary_nav() {
		$bp = buddypress();

		foreach ( $bp->members->nav->get_primary() as $user_nav_item ) {
			$items_to_display = array(
				'notifications',
				'messages',
				'bp_better_messages_tab',
				'settings',
			);
			if ( ! in_array( $user_nav_item->css_id, $items_to_display, true ) ) {
				continue;
			}
			if ( empty( $user_nav_item->show_for_displayed_user ) && ! bp_is_my_profile() ) {
				continue;
			}

			$selected = '';
			if ( bp_is_current_component( $user_nav_item->slug ) ) {
				$selected = ' class="current selected"';
			}

			if ( bp_loggedin_user_domain() ) {
				$link = str_replace( bp_loggedin_user_domain(), bp_displayed_user_domain(), $user_nav_item->link );
			} else {
				$link = trailingslashit( bp_displayed_user_domain() . $user_nav_item->link );
			}

			/**
			 * Filters the navigation markup for the displayed user.
			 *
			 * This is a dynamic filter that is dependent on the navigation tab component being rendered.
			 *
			 * @since 1.1.0
			 *
			 * @param string $value         Markup for the tab list item including link.
			 * @param array  $user_nav_item Array holding parts used to construct tab list item.
			 *                              Passed by reference.
			 */
			echo wp_kses( apply_filters_ref_array( 'bp_get_displayed_user_nav_' . $user_nav_item->css_id, array( '<li id="' . $user_nav_item->css_id . '-personal-li" ' . $selected . '><a id="user-' . $user_nav_item->css_id . '" href="' . $link . '">' . $user_nav_item->name . '</a></li>', &$user_nav_item ) ), array(
				'li'   => array(
					'id' => array(),
				),
				'a'    => array(
					'id'   => array(),
					'href' => array(),
				),
				'span' => array(
					'id'    => array(),
					'class' => array(),
				),
			) );
		}
	}
endif;

if ( ! function_exists( 'grimlock_buddypress_member_xprofile_name' ) ) :
	/**
	 * Output the Grimlock Post Component in `content` template part.
	 *
	 * @since 1.0.0
	 */
	function grimlock_buddypress_member_xprofile_name() {
		echo '<h2 class="user-nicename">';

		$username = bp_get_displayed_user_username();
		if ( bp_is_active( 'activity' ) && bp_activity_do_mentions() ) {
			$username = '<span class="user-nicename-at">@</span>' . bp_get_displayed_user_mentionname();
		}

		switch ( apply_filters( 'grimlock_buddypress_member_displayed_name', 'username' ) ) {
			case 'fullname':
				bp_displayed_user_fullname();
				break;

			case 'fullname_username':

				echo '<div class="user-nicename-small mb-1 small">' . wp_kses_post( $username ) . '</div>';

				bp_displayed_user_fullname();

				break;

			case 'username':
			default:
				echo wp_kses_post( $username );
				break;
		}

		echo '</h2>';
	}
endif;

if ( ! function_exists( 'grimlock_buddypress_groups_members_template_part' ) ) :
	/**
	 * Override "bp_groups_members_template_part" BuddyPress function
	 *
	 * @since 1.0.0
	 */
	function grimlock_buddypress_groups_members_template_part() {
		?>
		<div id="subnav" class="d-flex flex-column flex-lg-row mb-4 mt-0 pos-r" aria-label="<?php esc_attr_e( 'Group secondary navigation', 'grimlock-buddypress' ); ?>" role="navigation">
			<div class="groups-members-search dir-search dir-filter-search" role="search">
				<?php bp_directory_members_search_form(); ?>
			</div>
			<div class="dir-filter ml-md-auto mt-2 mt-md-0">
				<div class="select-style">
					<?php bp_groups_members_filter(); ?>
				</div>
				<?php do_action( 'bp_members_directory_member_sub_types' ); ?>
			</div>
		</div>

		<h2 class="bp-screen-reader-text">
			<?php esc_html_e( 'Members', 'grimlock-buddypress' ); ?>
		</h2>

		<div id="members-group-list" class="group_members dir-list">
			<?php bp_get_template_part( 'groups/single/members' ); ?>
		</div>
		<?php
	}
endif;

if ( ! function_exists( 'grimlock_buddypress_member_send_message_button' ) ) :
	/**
	 * Print the HTML for the BP button for the private messaging form.
	 *
	 * @since 1.0.0
	 */
	function grimlock_buddypress_member_send_message_button() {
		if ( is_user_logged_in() ) :
			$user_id = bp_get_member_user_id();

			if ( false !== $user_id && bp_loggedin_user_id() !== $user_id ) :
				bp_button( grimlock_buddypress_member_get_send_message_button_args() );
			endif;
		endif;
	}
endif;

if ( ! function_exists( 'grimlock_buddypress_member_get_send_message_button_args' ) ) :
	/**
	 * Get the arguments for the BP send message button.
	 *
	 * @since 1.1.10
	 */
	function grimlock_buddypress_member_get_send_message_button_args() {
		$user_id   = bp_get_member_user_id();
		$link_href = apply_filters( 'grimlock_buddypress_member_send_message_button_url', wp_nonce_url( bp_loggedin_user_domain() . bp_get_messages_slug() . '/compose/?r=' . bp_core_get_username( $user_id ) ), $user_id );
		return apply_filters( 'bp_get_send_message_button_args', array(
			'id'                => "private_message-{$user_id}",
			'component'         => 'messages',
			'must_be_logged_in' => true,
			'block_self'        => true,
			'wrapper_id'        => "send-private-message-{$user_id}",
			'wrapper_class'     => 'send-private-message',
			'link_href'         => $link_href,
			'link_text'         => esc_html__( 'Private Message', 'grimlock-buddypress' ),
			'link_class'        => 'send-message',
		) );
	}
endif;
