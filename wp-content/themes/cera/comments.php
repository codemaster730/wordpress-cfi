<?php
/**
 * The template for displaying comments.
 *
 * This is the template that displays the area of the page that contains both the current comments
 * and the comment form.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package cera
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}
?>

<div id="comments" class="comments-area">

	<?php
	// You can start editing here -- including this comment!
	if ( have_comments() ) : ?>
		<h2 class="comments-title h3">
			<?php
			// @codingStandardsIgnoreStart
			/* translators: 1: Comment number, 2: Post title */
			printf( // WPCS: XSS OK.
				esc_html( _nx( 'One thought on &ldquo;%2$s&rdquo;', '%1$s thoughts on &ldquo;%2$s&rdquo;', get_comments_number(), 'comments title', 'cera' ) ),
				number_format_i18n( get_comments_number() ),
				'<span>' . get_the_title() . '</span>'
			);
			// @codingStandardsIgnoreEnd ?>
		</h2><!-- .comments-title -->

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // Are there comments to navigate through? ?>
			<nav id="comment-nav-above" class="navigation comment-navigation">
				<h2 class="h5 screen-reader-text sr-only"><?php esc_html_e( 'Comment navigation', 'cera' ); ?></h2>
				<div class="nav-links">
					<ul class="pager">
						<li class="nav-previous pager-prev"><?php previous_comments_link( esc_html__( 'Older Comments', 'cera' ) ); ?></li>
						<li class="nav-next pager-next"><?php next_comments_link( esc_html__( 'Newer Comments', 'cera' ) ); ?></li>
					</ul>
				</div><!-- .nav-links -->
			</nav><!-- #comment-nav-above -->
		<?php endif; // Check for comment navigation. ?>

		<ul class="comment-list list-unstyled">
			<?php
			wp_list_comments( array(
				'style'       => 'ul',
				'short_ping'  => true,
				'avatar_size' => apply_filters( 'cera_comment_avatar_size', 80 ),
				'callback'    => 'cera_comment',
			) ); ?>
		</ul><!-- .comment-list -->

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // Are there comments to navigate through? ?>
			<nav id="comment-nav-below" class="navigation comment-navigation">
				<h2 class="screen-reader-text sr-only"><?php esc_html_e( 'Comment navigation', 'cera' ); ?></h2>
				<div class="nav-links">
					<ul class="pager">
						<li class="nav-previous pager-prev"><?php previous_comments_link( esc_html__( 'Older Comments', 'cera' ) ); ?></li>
						<li class="nav-next pager-next"><?php next_comments_link( esc_html__( 'Newer Comments', 'cera' ) ); ?></li>
					</ul><!-- .pager -->
				</div><!-- .nav-links -->
			</nav><!-- #comment-nav-below -->
		<?php endif; // Check for comment navigation.

	endif; // Check for have_comments().


	// If comments are closed and there are comments, let's leave a little note, shall we?
	if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) : ?>
		<div class="no-comments mt-3 mb-3"><?php esc_html_e( 'Comments are closed.', 'cera' ); ?></div>
	<?php endif; ?>

	<?php comment_form(); ?>

</div><!-- #comments -->
