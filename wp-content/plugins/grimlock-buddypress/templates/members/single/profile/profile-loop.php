<?php
/**
 * BuddyPress - Members Profile Loop
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 * @version 3.0.0
 */

/** This action is documented in bp-templates/bp-legacy/buddypress/members/single/profile/profile-wp.php */
do_action( 'bp_before_profile_loop_content' ); ?>

<?php if ( bp_has_profile() ) : ?>

	<ul class="nav nav-pills flex-sm-row flex-column" id="pills-tab" role="tablist">
		<?php $iteration = 0; ?>
		<?php while ( bp_profile_groups() ) : bp_the_profile_group(); $iteration++; ?>
			<li class="nav-item mr-2 mb-1 <?php if ( $iteration == 1 ) echo 'current' ?>">
				<a class="nav-link <?php if ( $iteration == 1 ) echo 'active' ?>" id="<?php echo esc_attr( "grimlock-buddypress-profile-tab-button-{$iteration}" ); ?>" data-toggle="pill" href="#<?php echo esc_attr( "grimlock-buddypress-profile-tab-{$iteration}" ); ?>" role="tab" aria-controls="pills-home" aria-selected="true">
					<?php bp_the_profile_group_name(); ?>
				</a>
			</li>
		<?php endwhile; ?>
	</ul>

	<?php if ( bp_profile_group_has_fields() ) : ?>

		<div class="tab-content" id="pills-tabContent">

			<?php $iteration_pane = 0; ?>

			<?php while ( bp_profile_groups() ) : bp_the_profile_group(); $iteration_pane++; ?>

				<div class="tab-pane fade <?php if ( $iteration_pane == 1 ) echo 'active show' ?>" id="<?php echo esc_attr( "grimlock-buddypress-profile-tab-{$iteration_pane}" ); ?>" role="tabpanel" aria-labelledby="pills-home-tab">

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
do_action( 'bp_after_profile_loop_content' ); ?>
