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

$member_classes = apply_filters( 'bp_member_swipe_member_classes', array(
	'bp-member-swipe-list__item',
	'swiper-slide',
	'has-post-thumbnail',
) );

global $members_template;

while ( bp_members() ) : bp_the_member();
$z_index = - ( ( $members_template->pag_num * ( $members_template->pag_page - 1 ) ) + $members_template->current_member ); ?>

	<li <?php bp_member_class( $member_classes ); ?> style="z-index: <?php echo esc_attr( $z_index ); ?>;" data-bp-item-id="<?php bp_member_user_id(); ?>" data-bp-item-component="members">

		<div class="bp-member-swipe-card card list-wrap">

			<div class="bp-member-swipe-card__avatar">
				<a href="<?php bp_member_permalink(); ?>">
					<?php bp_member_avatar( 'type=full' ); ?>
				</a>
			</div><!-- .bp-member-swipe-card__avatar -->

			<div class="bp-member-swipe-card__body">

				<div class="bp-member-swipe-card__title item-title">
					<a href="<?php bp_member_permalink(); ?>"><?php bp_member_name(); ?></a>
				</div><!-- .bp-member-swipe-card__title -->

				<div class="bp-member-swipe-card__meta">

					<?php do_action( 'bp_member_swipe_member_before_meta' ); ?>

					<span class="activity" data-livestamp="<?php bp_core_iso8601_date( bp_get_member_last_active( array( 'relative' => false ) ) ); ?>"><?php bp_member_last_active(); ?></span>

					<?php do_action( 'bp_member_swipe_member_after_meta' ); ?>

				</div><!-- .bp-member-swipe-card__meta -->

				<div class="bp-member-swipe-card__item"><?php do_action( 'bp_directory_members_item' ); ?></div><!-- .bp-member-swipe-card__item -->

				<div class="bp-member-swipe-card__action action">
					<?php do_action( 'bp_member_swipe_member_before_actions' ); ?>

					<?php
					if ( bp_get_theme_package_id() === 'nouveau' && function_exists( 'bp_nouveau_members_loop_buttons' ) ) :
						bp_nouveau_members_loop_buttons(
							array(
								'container'      => 'div',
								'button_element' => 'a',
							)
						);
					else :
						do_action( 'bp_directory_members_actions' );
					endif; ?>

					<?php do_action( 'bp_member_swipe_member_after_actions' ); ?>
				</div><!-- .bp-member-swipe-card__action -->

			</div><!-- .bp-member-swipe-card__body -->

		</div><!-- .bp-member-swipe-card -->

	</li>

	<?php
endwhile;
