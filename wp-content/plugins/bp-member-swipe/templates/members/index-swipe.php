<?php
/**
 * BuddyPress - Members
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 * @version 3.0.0
 */

// @codingStandardsIgnoreFile
// Allow plugin text domain in theme.

$members_list_container_classes = apply_filters( 'bp_member_swipe_members_list_container_classes', array(
	'members',
	'dir-list',
	'swiper-container',
) );

do_action( 'bp_before_directory_members_page' ); ?>

	<div id="buddypress" class="buddypress-member-swipe-wrapper">

		<?php do_action( 'bp_before_directory_members_content' ); ?>

		<div class="buddypress-member-swipe-content">

			<?php do_action( 'bp_before_directory_members' ); ?>

			<?php do_action( 'bp_before_directory_members_tabs' ); ?>

			<form action="" method="post" id="members-directory-form">

				<?php if ( function_exists( 'pp_mm_members_tab' ) && bp_is_members_directory() ) : // BP Maps for Members compatibility
					$bp_maps_settings = get_site_option( 'bp-member-map-all-settings' );

					if ( ! isset( $bp_maps_settings['map_skip_all'] ) ) : ?>

						<div class="item-list-tabs primary-list-tabs" aria-label="<?php esc_attr_e( 'Members directory main navigation', 'buddypress' ); ?>" role="navigation">
							<ul class="item-list-tabs-ul">
								<li class="selected" id="members-all">
									<a href="<?php bp_members_directory_permalink(); ?>">
										<?php
										/* translators: %s: Total member count */
										printf( esc_html__( 'All Members %s', 'buddypress' ), '<span>' . esc_html( bp_core_get_active_member_count() ) . '</span>' ); ?>
									</a>
								</li>

								<?php pp_mm_members_tab(); ?>
							</ul>
						</div><!-- .item-list-tabs -->

					<?php endif;
				endif;?>

				<h2 class="bp-screen-reader-text">
					<?php esc_html_e( 'One Member', 'buddypress' ); ?>
				</h2>

				<div id="members-index-swipe">
					<div id="members-dir-list" class="<?php echo esc_attr( join( ' ', array_unique( $members_list_container_classes ) ) ); ?>" data-bp-list>
						<?php bp_get_template_part( 'members/members-swipe-loop' ); ?>
					</div><!-- #members-dir-list -->
				</div>

				<?php do_action( 'bp_directory_members_content' ); ?>

				<?php wp_nonce_field( 'directory_members', '_wpnonce-member-filter' ); ?>

				<?php do_action( 'bp_after_directory_members_content' ); ?>

			</form><!-- #members-directory-form -->

			<?php do_action( 'bp_after_directory_members' ); ?>

		</div>

	</div><!-- #buddypress -->

<?php do_action( 'bp_after_directory_members_page' );
