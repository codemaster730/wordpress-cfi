<?php
/**
 * BuddyPress - Activity Stream Comment
 *
 * This template is used by bp_activity_comments() functions to show
 * each activity.
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 * @version 3.0.0
 */

// @codingStandardsIgnoreFile
// Allow plugin text domain in theme.

/**activity-fav
 * Fires before the display of an activity comment.
 *
 * @since 1.5.0
 */
do_action( 'bp_before_activity_comment' ); ?>

<li id="acomment-<?php bp_activity_comment_id(); ?>">

	<div class="acomment-user">

		<div class="acomment-avatar">
			<a href="<?php bp_activity_comment_user_link(); ?>">
				<?php bp_activity_avatar( 'type=thumb&user_id=' . bp_get_activity_comment_user_id() ); ?>
			</a>
		</div> <!-- .acomment-avatar -->

		<div class="acomment-meta">
			<?php
			$allowed_html = array(
				'em'     => array( 'class'  => array() ),
				'i'      => array( 'class'  => array() ),
				'strong' => array( 'class'  => array() ),
				'ins'    => array( 'class'  => array() ),
				'del'    => array( 'class'  => array() ),
				'br'     => array( 'class'  => array() ),
				'span'   => array( 'class'  => array(), 'data-livestamp' => array() ),
				'a'      => array( 'class' => array(), 'href' => array(), 'title' => array() ),
			);
			/* translators: %1$s: User profile link, %2$s: User name, %3$s: Activity permalink, %4%s: ISO8601 timestamp, %5$s: Activity relative timestamp */
			$replied_timestamp = __( '<a href="%1$s">%2$s</a> replied <a href="%3$s" class="activity-time-since"><span class="time-since" data-livestamp="%4$s">%5$s</span></a>', 'buddypress' );
			printf(
				wp_kses( $replied_timestamp, $allowed_html ),
				esc_url( bp_get_activity_comment_user_link() ),
				wp_kses( bp_get_activity_comment_name(), $allowed_html ),
				esc_url( bp_get_activity_comment_permalink() ),
				esc_attr( bp_core_get_iso8601_date( bp_get_activity_comment_date_recorded() ) ),
				esc_html( bp_get_activity_comment_date_recorded() ) ); ?>
		</div> <!-- .acomment-meta -->

	</div><!-- .acomment-user -->

	<div class="acomment-content"><?php bp_activity_comment_content(); ?></div><!-- .acomment-content -->

	<div class="acomment-options">

		<?php if ( is_user_logged_in() && bp_activity_can_comment_reply( bp_activity_current_comment() ) ) : ?>

			<a href="#acomment-<?php bp_activity_comment_id(); ?>" class="acomment-reply" id="acomment-reply-<?php bp_activity_id(); ?>-from-<?php bp_activity_comment_id(); ?>"><?php esc_html_e( 'Reply', 'buddypress' ); ?></a>

		<?php endif; ?>

		<?php if ( bp_activity_user_can_delete() ) : ?>

			<a href="<?php bp_activity_comment_delete_link(); ?>" class="delete acomment-delete confirm" rel="nofollow"><?php esc_html_e( 'Delete', 'buddypress' ); ?></a>

		<?php endif; ?>

		<?php do_action( 'bp_activity_comment_options' ); ?>

	</div><!-- .acomment-options -->

	<?php bp_activity_recurse_comments( bp_activity_current_comment() ); ?>

</li><!-- .acomment-* -->

<?php do_action( 'bp_after_activity_comment' ); ?>
