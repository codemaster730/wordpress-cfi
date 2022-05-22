<?php
/**
 * Grimlock_Author_Avatars_Modal_User_Component Class
 *
 * @author  Themosaurus
 * @package  grimlock-author-avatars
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The class to generate user modal content.
 */
class Grimlock_Author_Avatars_Modal_User_Component extends Grimlock_Component {

	/**
	 * Retrieve the classes for the component as an array.
	 *
	 * @since 1.0.0
	 *
	 * @param string|array $class One or more classes to add to the class list.
	 * @return array Array of classes.
	 */
	public function get_class( $class = '' ) {
		$classes   = parent::get_class( $class );
		$classes[] = 'grimlock-author-avatars-modal-user';
		return array_unique( $classes );
	}

	/**
	 * Render the profile fields of the current user
	 */
	protected function render_profile_fields() {
		?>
		<div class="member-profile-fields">

			<?php if ( bp_is_active( 'xprofile' ) ) {
				$this->render_xprofile_fields();
			}
			else {
				$this->render_wp_profile_fields();
			} ?>

		</div>
		<?php
	}

	/**
	 * Render the xProfile fields of the current user
	 */
	protected function render_xprofile_fields() {
		/** This action is documented in bp-templates/bp-legacy/buddypress/members/single/profile/profile-wp.php */
		do_action( 'bp_before_profile_loop_content' ); ?>

		<?php if ( bp_has_profile( array( 'user_id' => $this->props['user_id'] ) ) ) : ?>

			<ul class="nav nav-pills flex-sm-row flex-column" id="pills-tab" role="tablist">
				<?php $iteration = 0;
				$tab_id = uniqid(); ?>
				<?php while ( bp_profile_groups() ) : bp_the_profile_group(); $iteration++; ?>
					<li class="nav-item mr-2 mb-1 <?php if ( $iteration == 1 ) echo 'current' ?>">
						<a class="nav-link <?php if ( $iteration == 1 ) echo 'active' ?>" id="<?php echo esc_attr( "grimlock-buddypress-profile-tab-button-{$tab_id}-{$iteration}" ); ?>" data-toggle="pill" href="#<?php echo esc_attr( "grimlock-buddypress-profile-tab-{$tab_id}-{$iteration}" ); ?>" role="tab" aria-controls="pills-home" aria-selected="true">
							<?php bp_the_profile_group_name(); ?>
						</a>
					</li>
				<?php endwhile; ?>
			</ul>

			<?php if ( bp_profile_group_has_fields() ) : ?>

				<div class="tab-content" id="pills-tabContent">

					<?php $iteration_pane = 0; ?>

					<?php while ( bp_profile_groups() ) : bp_the_profile_group(); $iteration_pane++; ?>

						<div class="tab-pane fade <?php if ( $iteration_pane == 1 ) echo 'active show' ?>" id="<?php echo esc_attr( "grimlock-buddypress-profile-tab-{$tab_id}-{$iteration_pane}" ); ?>" role="tabpanel" aria-labelledby="pills-home-tab">

							<?php do_action( 'bp_before_profile_field_content' ); ?>

							<div class="bp-widget <?php bp_the_profile_group_slug(); ?>">

								<table class="profile-fields">

									<?php while ( bp_profile_fields() ) : bp_the_profile_field(); ?>

										<?php if ( bp_field_has_data() ) : ?>

											<tr<?php bp_field_css_class(); ?>>
												<td class="label"><?php bp_the_profile_field_name(); ?></td>
												<td class="data"><?php bp_the_profile_field_value(); ?></td>
											</tr>

										<?php endif; ?>

										<?php do_action( 'bp_profile_field_item' ); ?>

									<?php endwhile; ?>

								</table>
							</div>

							<?php
							/** This action is documented in bp-templates/bp-legacy/buddypress/members/single/profile/profile-wp.php */
							do_action( 'bp_after_profile_field_content' ); ?>
						</div>

					<?php endwhile; ?>

				</div>

			<?php endif; ?>

			<?php do_action( 'bp_profile_field_buttons' ); ?>

		<?php endif; ?>

		<?php

		/** This action is documented in bp-templates/bp-legacy/buddypress/members/single/profile/profile-wp.php */
		do_action( 'bp_after_profile_loop_content' );
	}

	/**
	 * Render the WP profile fields of the current user
	 */
	protected function render_wp_profile_fields() {
		/**
		 * Fires before the display of member profile loop content.
		 */
		do_action( 'bp_before_profile_loop_content' ); ?>

		<?php $ud = get_userdata( $this->props['user_id'] ); ?>

		<?php
		/**
		 * Fires before the display of member profile field content.
		 */
		do_action( 'bp_before_profile_field_content' ); ?>

		<div class="bp-widget wp-profile">
			<table class="wp-profile-fields">

				<?php if ( $ud->display_name ) : ?>

					<tr id="wp_displayname">
						<td class="label"><?php esc_html_e( 'Name', 'buddypress' ); ?></td>
						<td class="data"><?php echo esc_html( $ud->display_name ); ?></td>
					</tr>

				<?php endif; ?>

				<?php if ( $ud->user_description ) : ?>

					<tr id="wp_desc">
						<td class="label"><?php esc_html_e( 'About Me', 'buddypress' ); ?></td>
						<td class="data"><?php echo esc_html( $ud->user_description ); ?></td>
					</tr>

				<?php endif; ?>

				<?php if ( $ud->user_url ) : ?>

					<tr id="wp_website">
						<td class="label"><?php esc_html_e( 'Website', 'buddypress' ); ?></td>
						<td class="data"><?php echo esc_html( make_clickable( $ud->user_url ) ); ?></td>
					</tr>

				<?php endif; ?>

				<?php if ( $ud->jabber ) : ?>

					<tr id="wp_jabber">
						<td class="label"><?php esc_html_e( 'Jabber', 'buddypress' ); ?></td>
						<td class="data"><?php echo esc_html( $ud->jabber ); ?></td>
					</tr>

				<?php endif; ?>

				<?php if ( $ud->aim ) : ?>

					<tr id="wp_aim">
						<td class="label"><?php esc_html_e( 'AOL Messenger', 'buddypress' ); ?></td>
						<td class="data"><?php echo esc_html( $ud->aim ); ?></td>
					</tr>

				<?php endif; ?>

				<?php if ( $ud->yim ) : ?>

					<tr id="wp_yim">
						<td class="label"><?php esc_html_e( 'Yahoo Messenger', 'buddypress' ); ?></td>
						<td class="data"><?php echo esc_html( $ud->yim ); ?></td>
					</tr>

				<?php endif; ?>

			</table>
		</div>

		<?php

		/**
		 * Fires after the display of member profile field content.
		 */
		do_action( 'bp_after_profile_field_content' ); ?>

		<?php

		/**
		 * Fires and displays the profile field buttons.
		 */
		do_action( 'bp_profile_field_buttons' ); ?>

		<?php

		/**
		 * Fires after the display of member profile loop content.
		 */
		do_action( 'bp_after_profile_loop_content' );
	}

	/**
	 * Display the current component with props data on page.
	 *
	 * @since 1.0.0
	 */
	public function render() {
		if ( $this->is_displayed() && ! empty( $this->props['user_id'] ) ) :
			$user_id = (int) $this->props['user_id']; ?>

			<<?php $this->render_el(); ?> <?php $this->render_id(); ?> <?php $this->render_class(); ?> <?php $this->render_style(); ?> <?php $this->render_role(); ?> <?php $this->render_data_attributes(); ?>>

			<?php if ( function_exists( 'buddypress' ) ) : ?>

				<?php if ( bp_has_members( array( 'include' => array( $user_id ) ) ) ) : ?>

					<div id="buddypress">

						<?php while ( bp_members() ) : bp_the_member(); ?>

							<div <?php bp_member_class(); ?>>

								<?php
								$cover_image_css = '';

								if ( ! bp_disable_cover_image_uploads() ) {
									$params          = bp_attachments_get_cover_image_settings( 'members' );
									$cover_image_url = bp_attachments_get_attachment( 'url', array(
										'object_dir' => 'members',
										'item_id'    => $user_id,
									) );

									if ( empty( $cover_image_url ) ) {
										if ( ! empty( $params['default_cover'] ) ) {
											$cover_image_url = $params['default_cover'];
										}
									}
									$cover_image_css = 'background-image: url(' . esc_url( $cover_image_url ) . ');';
								}
								?>

								<header class="member-cover-image entry-header" style="<?php echo esc_attr( $cover_image_css ); ?>">
									<div class="member-header">
										<a href="<?php bp_member_permalink(); ?>">
											<?php bp_member_avatar( array( 'type' => 'full' ) ); ?>
										</a>

										<div>
											<h2 class="entry-title item-title">
												<a href="<?php bp_member_permalink(); ?>">
													<?php bp_member_name(); ?>
												</a>
											</h2>
											<div class="bp-member-xprofile-custom-fields"><?php do_action( 'grimlock_buddypress_member_xprofile_custom_fields' ); ?></div> <!-- .bp-member-xprofile-custom-fields -->
										</div>
									</div> <!-- .card-img -->
								</header>

								<div class="member-body">

									<?php if ( is_user_logged_in() && ( bp_get_member_user_id() !== bp_loggedin_user_id() ) ) : ?>
<!--										<div class="member-actions action">-->
<!--											--><?php //do_action( 'bp_directory_members_actions' ); ?>
<!--											--><?php //grimlock_buddypress_actions_dropdown(); ?>
<!--										</div>-->
									<?php endif; ?>

									<?php $this->render_profile_fields(); ?>

								</div> <!-- .card-body -->

							</div> <!-- .bp-card-list__item -->

						<?php endwhile; ?>

					</div>

				<?php endif; ?>

			<?php else :
				$userdata = get_userdata( $user_id ); ?>

				<div class="member-wp-profile">

					<header class="member-cover-image entry-header">
						<div class="member-header">
							<a href="<?php echo esc_url( get_author_posts_url( $user_id ) ); ?>">
								<?php echo get_avatar( $user_id, 120 ); ?>
							</a>

							<div>
								<h2 class="entry-title item-title">
									<a href="<?php echo esc_url( get_author_posts_url( $user_id ) ); ?>">
										<?php echo esc_html( $userdata->display_name ); ?>
									</a>
								</h2>
							</div>
						</div> <!-- .card-img -->
					</header>

					<div class="member-body">

						<?php $this->render_wp_profile_fields(); ?>

					</div> <!-- .card-body -->
				</div>

			<?php endif; ?>

			</<?php $this->render_el(); ?>>

			<?php
		endif;
	}
}
