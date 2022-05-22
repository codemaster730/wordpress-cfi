<?php
/**
 * BuddyPress - Groups Loop
 *
 * Querystring is set via AJAX in _inc/ajax.php - bp_legacy_theme_object_filter().
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 * @version 3.0.0
 */

// @codingStandardsIgnoreFile
// Allow plugin text domain in theme and unescaped template tags.
?>

<?php do_action( 'bp_before_groups_loop' ); ?>

<?php if ( bp_get_current_group_directory_type() ) : ?>
	<p class="current-group-type"><?php bp_current_group_directory_type_message(); ?></p>
<?php endif; ?>

<?php if ( bp_has_groups( bp_ajax_querystring( 'groups' ) ) ) : ?>

	<?php do_action( 'bp_before_directory_groups_list' ); ?>

	<ul id="groups-list" class="bp-card-list bp-card-list--groups loading-list" aria-live="assertive" aria-atomic="true" aria-relevant="all">

		<?php while ( bp_groups() ) : bp_the_group(); ?>

			<li <?php bp_group_class( array( 'bp-card-list__item bp-card-list--groups__item has-post-thumbnail element-animated fade-in short element-animated-delay element-animated-both' ) ); ?>>

				<div class="card ov-h">

					<?php if ( ! bp_disable_group_cover_image_uploads() ) : ?>
						<?php $group_cover_image_url = bp_attachments_get_attachment( 'url', array(
							'object_dir' => 'groups',
							'item_id'    => bp_get_group_id(),
						) );

						$group_cover_params = bp_attachments_get_cover_image_settings( 'groups' );

						if ( empty( $group_cover_image_url ) && ! empty( $group_cover_params ) ) {
							$group_cover_image_url = $group_cover_params['default_cover'];
						}

						$group_cover_style = '';
						if ( ! empty( $group_cover_image_url ) ) {
							$group_cover_style = "background-image: url('" . esc_url( $group_cover_image_url ) . "');";
						} ?>
					<?php endif; ?>

					<a href="<?php bp_group_permalink(); ?>">
						<div class="card-img card-img--cover" <?php if ( ! bp_disable_group_cover_image_uploads() ) : ?>style="<?php echo esc_attr( $group_cover_style ); ?>"<?php endif; ?>>

							<?php if ( ! bp_disable_group_avatar_uploads() ) : ?>

								<div class="card-img__avatar">
									<?php bp_group_avatar( 'type=thumb&width=' . bp_core_avatar_thumb_width() . '&height=' . bp_core_avatar_thumb_height() ); ?>
								</div><!-- .card-img__avatar -->

							<?php endif; ?>

						</div> <!-- .card-img -->
					</a>

					<div class="card-body">

						<header class="card-body-header entry-header clearfix pt-2">
							<h2 class="entry-title item-title">
								<?php bp_group_link(); ?>
							</h2>
						</header> <!-- .card-body-header -->

						<div class="card-body-meta">

							<?php bp_group_type(); ?> <span class="separator small text-muted pr-1 pl-1">•</span> <?php bp_group_member_count(); ?>

							<div class="bp-group-custom-fields"><?php do_action( 'grimlock_buddypress_group_custom_fields' ); ?></div> <!-- .bp-group-custom-fields -->

							<?php do_action( 'bp_directory_groups_item' ); ?>

							<div class="item-desc"><?php bp_group_description_excerpt( false, 100 ); ?></div>

							<div class="card-body-activity">
								<strong>
									<?php esc_html( 'Active', 'buddypress' ); ?>
									<span class="activity" data-livestamp="<?php bp_core_iso8601_date( bp_get_group_last_active( 0, array( 'relative' => false ) ) ); ?>">
										<?php
											/* translators: %s: Last group active */
											printf( esc_html__( 'Active %s', 'buddypress' ), esc_html( bp_get_group_last_active() ) ); ?>
									</span>
								</strong>
							</div><!-- .card-body-activity -->

						</div> <!-- .card-body-meta -->

						<div class="card-body-actions action group-action"><?php do_action( 'bp_directory_groups_actions' ); ?></div> <!-- .card-body-actions -->

					</div> <!-- .card-body -->

				</div> <!-- .card -->

			</li> <!-- .bp-card-list__item -->

		<?php endwhile; ?>

	</ul> <!-- .bp-card-list -->

	<?php do_action( 'bp_after_directory_groups_list' ); ?>

	<div id="pag-bottom" class="pagination">
		<div class="pagination-links" id="group-dir-pag-bottom">
			<?php bp_groups_pagination_links(); ?>
		</div> <!-- .pagination-links -->
		<div class="pag-count" id="group-dir-count-bottom">
			<?php bp_groups_pagination_count(); ?>
		</div> <!-- .pag-count -->
	</div> <!-- .pagination -->

<?php else : ?>

	<div id="message" class="info">
		<p><?php esc_html_e( 'There were no groups found.', 'buddypress' ); ?></p>
	</div> <!-- #message -->

<?php endif; ?>

<?php do_action( 'bp_after_groups_loop' ); ?>
