<?php
/**
 * Grimlock for bbPress template functions.
 *
 * @package grimlock-bbpress
 */

/**
 * Get the HTML for the more link.
 *
 * @since 1.0.1
 *
 * @return string The more link text.
 */
function grimlock_bbpress_get_more_link_text() {
	$allowed_html = array(
		'span' => array(
			'class' => array(),
		),
	);

	$more_link_text = sprintf(
	/* translators: %s: Name of current post. */
		wp_kses( __( 'Continue reading %1$s %2$s', 'grimlock-bbpress' ), $allowed_html ),
		the_title( '<span class="screen-reader-text sr-only">"', '"</span>', false ),
		'<span class="meta-nav">&rarr;</span>'
	);
	return apply_filters( 'grimlock_bbpress_more_link_text', $more_link_text );
}

/**
 * Output the title of the forum.
 *
 * @since 1.0.1
 */
function grimlock_bbpress_forum_title() {
	if ( function_exists( 'bbp_forum_permalink' ) && function_exists( 'bbp_forum_title' ) ) : ?>
		<a class="entry-title bbp-forum-title bbp-forum-permalink" href="<?php bbp_forum_permalink(); ?>" rel="bookmark"><?php bbp_forum_title(); ?></a>
	<?php
	endif;
}

/**
 * Output the content of the forum.
 *
 * @since 1.0.1
 */
function grimlock_bbpress_forum_content( $more_link_text ) {
	if ( function_exists( 'bbp_forum_content' ) && function_exists( 'bbp_forum_permalink' ) && function_exists( 'bbp_get_forum_title' ) ) : ?>
		<div class="bbp-forum-content"><?php bbp_forum_content(); ?></div>
		<?php
		if ( '' !== $more_link_text ) :
			$allowed_html = array(
				'span' => array(
					'class' => array(),
				),
			); ?>
			<a href="<?php bbp_forum_permalink(); ?>" title="<?php echo esc_attr( bbp_get_forum_title() ); ?>" class="more-link"><?php echo wp_kses( $more_link_text, $allowed_html ); ?></a>
		<?php
		endif;
	endif;
}

/**
 * Output total topic count of a forum.
 *
 * @since 1.0.1
 */
function grimlock_bbpress_forum_topic_count() {
	if ( function_exists( 'bbp_forum_topic_count' ) ) : ?>
		<div class="bbp-forum-topic-count">
			<?php
			$count = bbp_get_forum_topic_count( get_the_ID() );
			if ( $count > 0 ) :
				printf( esc_html( _n( '%s topic', '%s topics', $count, 'grimlock-bbpress' ) ), number_format_i18n( $count ) );
			endif; ?>
		</div>
	<?php
	endif;
}

/**
 * Output total topic count of a forum.
 *
 * @since 1.0.1
 */
function grimlock_bbpress_forum_reply_count() {
	if ( function_exists( 'bbp_show_lead_topic' ) && function_exists( 'bbp_get_forum_reply_count' ) && function_exists( 'bbp_get_forum_post_count' ) ) : ?>
		<div class="bbp-forum-reply-count">
			<?php
			$count = bbp_show_lead_topic() ? bbp_get_forum_reply_count( get_the_ID() ) : bbp_get_forum_post_count( get_the_ID() );
			if ( $count > 0 ) :
				printf( esc_html( _n( '%s post', '%s posts', $count, 'grimlock-bbpress' ) ), number_format_i18n( $count ) );
			endif; ?>
		</div>
	<?php
	endif;
}

/**
 * Output link to the most recent activity inside a forum.
 *
 * Outputs a complete link with attributes and content.
 *
 * @since 1.0.1
 */
function grimlock_bbpress_forum_freshness() {
	if ( function_exists( 'bbp_forum_freshness_link' ) && function_exists( 'bbp_author_link' ) && function_exists( 'bbp_get_forum_last_active_id' ) ) : ?>
		<div class="bbp-forum-freshness">
			<?php bbp_forum_freshness_link( get_the_ID() ); ?>

			<div class="bbp-topic-meta">
				<span class="bbp-topic-freshness-author"><?php bbp_author_link( array( 'post_id' => bbp_get_forum_last_active_id( get_the_ID() ), 'size' => 14 ) ); ?></span>
			</div>
		</div>
	<?php
	endif;
}

/**
 * Output the title of the topic.
 *
 * @since 1.0.1
 */
function grimlock_bbpress_topic_title() {
	if ( function_exists( 'bbp_topic_permalink' ) && function_exists( 'bbp_topic_title' ) ) : ?>
		<a class="entry-title bbp-topic-title bbp-topic-permalink" href="<?php bbp_topic_permalink(); ?>" rel="bookmark"><?php bbp_topic_title(); ?></a>
	<?php
	endif;
}

/**
 * Output the author link of the topic.
 *
 * @since 1.0.1
 */
function grimlock_bbpress_topic_started_by() {
	if ( function_exists( 'bbp_get_topic_author_link' ) ) : ?>
		<div class="bbp-topic-started-by"><?php printf( esc_html__( 'Started by: %1$s', 'grimlock-bbpress' ), bbp_get_topic_author_link( array( 'post_id' => get_the_ID(), 'size' => '14' ) ) ); ?></div>
	<?php
	endif;
}

/**
 * Output total voice count of a topic.
 *
 * @since 1.0.1
 */
function grimlock_bbpress_topic_voice_count() {
	if ( function_exists( 'bbp_get_topic_voice_count' ) ) : ?>
		<div class="bbp-topic-voice-count">
			<?php
			$count = bbp_get_topic_voice_count( get_the_ID() );
			if ( $count > 0 ) :
				printf( esc_html( _n( '%s voice', '%s voices', $count, 'grimlock-bbpress' ) ), number_format_i18n( $count ) );
			endif; ?>
		</div>
	<?php
	endif;
}

/**
 * Output total topic count of a topic.
 *
 * @since 1.0.1
 */
function grimlock_bbpress_topic_reply_count() {
	if ( function_exists( 'bbp_show_lead_topic' ) && function_exists( 'bbp_forum_reply_count' ) && function_exists( 'bbp_forum_post_count' ) ) : ?>
		<div class="bbp-topic-reply-count">
			<?php
			$count = bbp_show_lead_topic() ? bbp_get_topic_reply_count( get_the_ID() ) : bbp_get_topic_post_count( get_the_ID() );
			if ( $count > 0 ) :
				printf( esc_html( _n( '%s post', '%s posts', $count, 'grimlock-bbpress' ) ), number_format_i18n( $count ) );
			endif; ?>
		</div>
	<?php
	endif;
}

/**
 * Output link to the most recent activity inside a topic.
 *
 * Outputs a complete link with attributes and content.
 *
 * @since 1.0.1
 */
function grimlock_bbpress_topic_freshness() {
	if ( function_exists( 'bbp_topic_freshness_link' ) && function_exists( 'bbp_author_link' ) && function_exists( 'bbp_get_topic_last_active_id' ) ) : ?>
		<div class="bbp-topic-freshness">
			<?php bbp_topic_freshness_link( get_the_ID() ); ?>

			<div class="bbp-topic-meta">
				<span class="bbp-topic-freshness-author"><?php bbp_author_link( array( 'post_id' => bbp_get_topic_last_active_id( get_the_ID() ), 'size' => 14 ) ); ?></span>
			</div>
		</div>
	<?php
	endif;
}

/**
 * Output the more link for the topic.
 *
 * @since 1.0.1
 */
function grimlock_bbpress_topic_more_link( $more_link_text ) {
	if ( function_exists( 'bbp_forum_permalink' ) && function_exists( 'bbp_get_forum_title' ) ) :
		if ( '' !== $more_link_text ) :
			$allowed_html = array(
				'span' => array(
					'class' => array(),
				),
			); ?>
			<a href="<?php bbp_forum_permalink(); ?>" title="<?php echo esc_attr( bbp_get_forum_title() ); ?>" class="more-link"><?php echo wp_kses( $more_link_text, $allowed_html ); ?></a>
		<?php
		endif;
	endif;
}

/**
 * Output the title of the reply.
 *
 * @since 1.0.1
 */
function grimlock_bbpress_reply_title() {
	if ( function_exists( 'bbp_topic_permalink' ) && function_exists( 'bbp_topic_title' ) && function_exists( 'bbp_get_reply_topic_id' ) ) : ?>
		<div class="entry-title">
			<span><?php esc_html_e( 'In reply to: ', 'grimlock-bbpress' ); ?></span>
			<a class="bbp-topic-title bbp-topic-permalink" href="<?php bbp_topic_permalink( bbp_get_reply_topic_id() ); ?>" rel="bookmark"><?php bbp_topic_title( bbp_get_reply_topic_id() ); ?></a>
		</div>
	<?php
	endif;
}

/**
 * Output the date of the reply.
 *
 * @since 1.0.1
 */
function grimlock_bbpress_reply_date() {
	if ( function_exists( 'bbp_reply_post_date' ) ) : ?>
		<div class="bbp-reply-post-date"><?php bbp_reply_post_date(); ?></div>
	<?php
	endif;
}

/**
 * Output the permalink of the reply.
 *
 * @since 1.0.1
 */
function grimlock_bbpress_reply_permalink() {
	if ( function_exists( 'bbp_reply_url' ) && function_exists( 'bbp_reply_id' ) ) : ?>
		<a href="<?php bbp_reply_url( get_the_ID() ); ?>" class="bbp-reply-permalink">#<?php bbp_reply_id( get_the_ID() ); ?></a>
	<?php
	endif;
}

/**
 * Output the author of the reply.
 *
 * @since 1.0.1
 */
function grimlock_bbpress_reply_author() {
	if ( function_exists( 'bbp_reply_author_link' ) ) : ?>
		<div class="bbp-reply-author">
			<?php bbp_reply_author_link( array( 'post_id' => get_the_ID(), 'sep' => '<br />', 'show_role' => true, 'size' => 14 ) ); ?>
		</div>
	<?php
	endif;
}

/**
 * Output the content of the reply.
 *
 * @since 1.0.1
 */
function grimlock_bbpress_reply_content( $more_link_text ) {
	if ( function_exists( 'bbp_reply_content' ) && function_exists( 'bbp_reply_permalink' ) && function_exists( 'bbp_get_reply_title' ) ) : ?>
		<div class="bbp-reply-content"><?php bbp_reply_content(); ?></div>
		<?php
		if ( '' !== $more_link_text ) :
			$allowed_html = array(
				'span' => array(
					'class' => array(),
				),
			); ?>
			<a href="<?php bbp_reply_permalink(); ?>" title="<?php echo esc_attr( bbp_get_reply_title() ); ?>" class="more-link"><?php echo wp_kses( $more_link_text, $allowed_html ); ?></a>
		<?php
		endif;
	endif;
}
