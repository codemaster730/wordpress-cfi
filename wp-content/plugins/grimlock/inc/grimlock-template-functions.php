<?php
/**
 * Grimlock template functions.
 *
 * @package grimlock
 */

/**
 * Prints HTML for the post
 *
 * @param array $args The array of arguments from the component
 */
function grimlock_post_template( $args ) {
	?>
	<div class="card grimlock-post__card post__card">
		<?php
		/**
		 * Hook: grimlock_post_before_card_body.
		 *
		 * @hooked grimlock_post_thumbnail - 10
		 */
		do_action( 'grimlock_post_before_card_body', $args ); ?>

		<div class="card-body">
			<?php
			/**
			 * Hook: grimlock_post_card_body.
			 *
			 * @hooked grimlock_post_format  - 10
			 * @hooked grimlock_post_header  - 20
			 * @hooked grimlock_post_content - 30
			 * @hooked grimlock_post_footer  - 40
			 */
			do_action( 'grimlock_post_card_body', $args ); ?>
		</div><!-- .card-body -->

		<?php
		/**
		 * Hook: grimlock_post_after_card_body.
		 */
		do_action( 'grimlock_post_after_card_body', $args ); ?>
	</div><!-- .card -->
	<?php
}

/**
 * Prints HTML for the post thumbnail :
 *     - For the Video, Audio and Image formats, the post thumbnail is replaced by the media
 *       found in the post content (either the video player, the image or the audio player).
 *     - In any other case, the post thumbnail is displayed if available.
 *
 * @param array $args The array of arguments from the component
 */
function grimlock_post_thumbnail( $args ) {
	if ( ! empty( $args['post_thumbnail_displayed'] ) ) :
		$size = $args['post_thumbnail_size'];
		$attr = $args['post_thumbnail_attr'];

		if ( has_post_format( array( 'video', 'audio', 'gallery' ) ) ) : ?>
			<div class="post-media"><?php the_content(); ?></div>
		<?php
		elseif ( has_post_thumbnail() ) : ?>
			<a href="<?php echo grimlock_get_post_permalink( $args ); ?>" class="post-thumbnail" title="<?php the_title_attribute(); ?>" rel="bookmark" <?php echo grimlock_get_post_link_attributes( $args ); ?>>
				<?php the_post_thumbnail( $size, $attr ); ?>
			</a>
		<?php
		endif;
	endif;
}

/**
 * Get data attributes to print in the <a> tags containing the post permalink
 *
 * @param array $args Optional args of the component calling this function
 *
 * @return string The data attributes that need to be printed in the <a> tag
 */
function grimlock_get_post_link_attributes( $args = array() ) {
	$attributes           = apply_filters( 'grimlock_post_link_attributes', array(), $args );
	$sanitized_attributes = array();

	foreach ( $attributes as $key => $value ) {
		$sanitized_attributes[ esc_html( $key ) ] = esc_html( $key ) . '="' . esc_attr( $value ) . '"';
	}

	return join( ' ', $sanitized_attributes );
}

/**
 * Get the post permalink
 *
 * @param array $args Optional args of the component calling this function
 *
 * @return string The post permalink
 */
function grimlock_get_post_permalink( $args = array() ) {
	return esc_url( apply_filters( 'grimlock_post_permalink', get_permalink(), $args ) );
}

/**
 * Get the post "more" link text
 *
 * @return string The post "more" link text
 */
function grimlock_get_post_more_link_text() {
	$allowed_html = array(
		'span' => array(
			'class' => array(),
		),
	);

	$more_link_text = sprintf(
		/* translators: 1: Name of current post, 2: Right arrow */
		wp_kses( __( 'Continue reading %1$s %2$s', 'grimlock' ), $allowed_html ),
		the_title( '<span class="screen-reader-text sr-only">"', '"</span>', false ),
		'<span class="meta-nav">&rarr;</span>'
	);

	return apply_filters( 'grimlock_post_more_link_text', $more_link_text );
}

/**
 * Prints HTML for the post format as Boostrap label.
 *
 * @param array $args The array of arguments from the component
 */
function grimlock_post_format( $args ) {

	if ( true == $args['post_format_displayed'] && 'post' === get_post_type() &&  ( has_post_format() || is_sticky() ) ) : ?>

		<div class="entry-labels">

			<?php
			if ( is_sticky() ) : ?>
				<span class="badge badge-primary post-sticky"  data-toggle="tooltip" data-placement="top" title="<?php esc_attr_e( 'Sticky', 'grimlock' ); ?>"><span class="badge__name"><?php esc_html_e( 'Sticky', 'grimlock' ); ?></span></span>
			<?php
			endif;

			$post_format = get_post_format();
			if ( false !== $post_format ) :
				$post_format_link_title = sprintf(
				/* translators: %s: The post format name */
					esc_html__( 'View posts formatted as %s', 'grimlock' ),
					esc_attr( strtolower( get_post_format_string( $post_format ) ) )
				); ?>
				<a href="<?php echo esc_url( get_post_format_link( $post_format ) ); ?>" data-toggle="tooltip" data-placement="top" title="<?php echo esc_attr( get_post_format_string( $post_format ) ); ?>" class="badge badge-primary post-format post-format--<?php echo esc_attr( $post_format ); ?>"><span class="badge__name"><?php echo esc_html( get_post_format_string( $post_format ) ); ?></span></a>
			<?php
			endif; ?>

		</div>

	<?php endif;
}

/**
 * Prints HTML for the post header
 *
 * @param array $args The array of arguments from the component
 */
function grimlock_post_header( $args ) {
	?>
	<header class="entry-header clearfix">
		<?php
		/**
		 * Hook: grimlock_post_header.
		 *
		 * @hooked grimlock_post_title     - 10
		 * @hooked grimlock_category_list  - 20
		 * @hooked grimlock_post_meta      - 30
		 */
		do_action( 'grimlock_post_header', $args ); ?>
	</header><!-- .entry-header -->
	<?php
}

/**
 * Prints HTML for the post title
 */
function grimlock_post_title( $args ) {
	the_title( '<h2 class="entry-title"><a href="' . grimlock_get_post_permalink( $args ) . '" rel="bookmark" ' . grimlock_get_post_link_attributes( $args ) . '>', '</a></h2>' );
}

/**
 * Prints HTML for the post date and author
 *
 * @param array $args The array of arguments from the component
 */
function grimlock_post_meta( $args ) {
	if ( 'post' === get_post_type() ) : ?>
		<div class="entry-meta">
			<?php
			if ( ! empty( $args['post_date_displayed'] ) && 'post' === get_post_type() || 'attachment' === get_post_type() ) {
				$allowed_html = array(
					'time' => array(
						'class'    => true,
						'datetime' => true,
					),
				);

				$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';

				if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
					$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
				}

				$time_string = sprintf( $time_string,
					esc_attr( get_the_date( 'c' ) ),
					esc_html( get_the_date() ),
					esc_attr( get_the_modified_date( 'c' ) ),
					esc_html( get_the_modified_date() )
				);

				printf(
					'<span class="posted-on"><span class="posted-on-label">' . esc_html__( 'Posted on', 'grimlock' ) . ' </span>%s</span>',
					'<a href="' . grimlock_get_post_permalink( $args ) . '" rel="bookmark" ' . grimlock_get_post_link_attributes( $args ) . '>' . wp_kses( $time_string, $allowed_html ) . '</a>'
				);
			}

			if ( ! empty( $args['post_author_displayed'] ) && 'post' === get_post_type() ) {
				printf(
					'<span class="byline author"><span class="byline-label">' . esc_html__( 'by', 'grimlock' ) . ' </span>%1$s %2$s</span>',
					'<span class="author-avatar"><span class="avatar-round-ratio medium"><a href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . get_avatar( get_the_author_meta( 'ID' ), 52 ) . '</a></span></span>',
					'<span class="author-vcard vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
				);
			} ?>
		</div><!-- .entry-meta -->
	<?php
	endif;
}

/**
 * Prints HTML for the post content and post "more" link
 *
 * @param array $args The array of arguments from the component
 */
function grimlock_post_content ( $args ) {

	if ( ! empty( $args['post_content_displayed'] ) ) :

		$more_link_text = grimlock_get_post_more_link_text(); ?>

		<div class="entry-content clearfix">
			<?php
			the_content( $more_link_text );
			wp_link_pages( array(
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'grimlock' ),
				'after'  => '</div>',
			) );
			?>
		</div><!-- .entry-content -->

	<?php endif;
}

/**
 * Prints HTML for the post excerpt and post "more" link
 *
 * @param array $args The array of arguments from the component
 */
function grimlock_post_excerpt( $args ) {
	$more_link_text = '';
	if ( ! empty( $args['post_more_link_displayed'] ) ) :
		$more_link_text = grimlock_get_post_more_link_text();
	endif;

	if ( ! empty( $args['post_excerpt_displayed'] ) ) :
		$more_link = '';
		if ( '' !== $more_link_text ) :
			$more_link = '<a href="' . grimlock_get_post_permalink( $args ) . '" title="' . the_title_attribute( array( 'echo' => false ) ) . '" class="more-link btn btn-link" ' . grimlock_get_post_link_attributes( $args ) . '>' . $more_link_text . '</a>';
		endif;

		$allowed_html = array(
			'a'    => array(
				'href'  => array(),
				'title' => array(),
				'class' => array(),
				'data-toggle' => array(),
			),
			'span' => array(
				'class' => array(),
			),
		);

		if ( has_post_format( array( 'link', 'quote' ) ) ) : ?>
			<div class="entry-content clearfix">
				<?php
				the_content();
				echo $more_link; ?>
			</div><!-- .entry-content -->
		<?php
		else : ?>
			<div class="entry-summary clearfix">
				<?php
				the_excerpt();
				echo $more_link; ?>
			</div><!-- .entry-summary -->
		<?php
		endif;
	endif;
}

/**
 * Prints HTML for the post footer
 *
 * @param array $args The array of arguments from the component
 */
function grimlock_post_footer( $args ) {
	ob_start();

	/**
	 * Hook: grimlock_post_footer.
	 *
	 * @hooked grimlock_tag_list       - 20
	 * @hooked grimlock_comments_link  - 30
	 * @hooked grimlock_edit_post_link - 40
	 */
	do_action( 'grimlock_post_footer', $args );

	$footer_content = ob_get_clean();

	if ( ! empty( trim( $footer_content ) ) ) : ?>
		<footer class="entry-footer clearfix">
			<?php echo $footer_content; ?>
		</footer><!-- .entry-footer -->
	<?php endif;
}

/**
 * * Prints HTML for the post categories
 *
 * @param array $args The array of arguments from the component
 */
function grimlock_category_list( $args ) {
	if ( ! empty( $args['category_displayed'] ) && 'post' === get_post_type() ) :
		/* translators: used between list items, there is a space after the comma */
		$categories_list = get_the_category_list( '<span class="cat-links-separator">' . esc_html__( ', ', 'grimlock' ) . '</span>' );
		if ( $categories_list ) {
			printf( '<span class="cat-links"><span class="cat-links-label">' . esc_html__( 'Posted in', 'grimlock' ) . ' </span>%1$s </span>', $categories_list ); // WPCS: XSS OK.
		}
	endif;
}

/**
 * Prints HTML for the post tags
 *
 * @param array $args The array of arguments from the component
 */
function grimlock_tag_list( $args ) {
	if ( ! empty( $args['post_tag_displayed'] ) && 'post' === get_post_type() && has_tag() ) :
		/* translators: used between list items, there is a space after the comma */
		$tags_list = get_the_tag_list( '', '<span class="tag-links-separator">' . esc_html__( ', ', 'grimlock' ) . '</span>' );
		if ( $tags_list ) {
			printf( '<span class="tags-links"><span class="tag-links-label">' . esc_html__( 'Tagged', 'grimlock' ) . ' </span>%1$s </span>', $tags_list ); // WPCS: XSS OK.
		}
	endif;
}

/**
 * Prints HTML for the post comments link
 *
 * @param array $args The array of arguments from the component
 */
function grimlock_comments_link( $args ) {
	if ( ! empty( $args['comments_link_displayed'] ) ) :
		$has_comments_link = ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() );
		if ( apply_filters( 'grimlock_has_comments_link', $has_comments_link ) ) { ?>
			<span class="comments-link <?php if ( ! have_comments() ): ?>has-no-comment<?php endif; ?>">
				<?php comments_popup_link( '0', '1', '%' ); ?>
			</span>
		<?php }
	endif;
}

/**
 * Prints HTML for the post "edit" link shown to users that have rights to edit the post
 */
function grimlock_edit_post_link() {
	if ( get_edit_post_link() ) :
		edit_post_link(
			sprintf(
			/* translators: %s: Name of current post */
				esc_html__( 'Edit %s', 'grimlock' ),
				the_title( '<span class="screen-reader-text sr-only">"', '"</span>', false )
			),
			'<span class="edit-link">',
			'</span>'
		);
	endif;
}

/**
 * Prints HTML for the taxonomy term
 *
 * @param array $args The array of arguments from the component
 */
function grimlock_term_template( $args ) {
	?>
	<div class="card term__card">
		<?php
		/**
		 * Hook: grimlock_term_before_card_body.
		 *
		 * @hooked grimlock_term_thumbnail - 10
		 */
		do_action( 'grimlock_term_before_card_body', $args ); ?>

		<div class="card-body">
			<?php
			/**
			 * Hook: grimlock_term_card_body.
			 *
			 * @hooked grimlock_term_header       - 10
			 * @hooked grimlock_term_description  - 20
			 * @hooked grimlock_term_more_link    - 30
			 * @hooked grimlock_term_footer       - 40
			 */
			do_action( 'grimlock_term_card_body', $args ); ?>
		</div><!-- .card-body -->

		<?php
		/**
		 * Hook: grimlock_term_after_card_body.
		 */
		do_action( 'grimlock_term_after_card_body', $args ); ?>
	</div><!-- .card -->
	<?php
}

/**
 * Prints HTML for the term thumbnail
 *
 * @param array $args The array of arguments from the component
 */
function grimlock_term_thumbnail( $args ) {
	if ( ! empty( $args['term_thumbnail_displayed'] ) && function_exists( 'get_term_meta' ) ) :
		$thumbnail_id = grimlock_get_term_thumbnail_id( $args['term_id'] );
		if ( ! empty( $thumbnail_id ) ) : ?>
			<div class="card-img">
				<a href="<?php echo esc_url( get_term_link( $args['term_id'] ) ); ?>" title="<?php echo $args['name']; ?>" rel="bookmark">
					<?php echo wp_get_attachment_image( $thumbnail_id, $args['term_thumbnail_size'], false, $args['term_thumbnail_attr'] ); ?>
				</a>
			</div>
		<?php
		endif;
	endif;
}

/**
 * Prints HTML for the term icon
 *
 * @param array $args The array of arguments from the component
 */
function grimlock_term_icon( $args ) {
	$term_icon = grimlock_get_term_icon( $args['term_id'] );
	if ( ! empty( $term_icon ) ) : ?>
		<div class="grimlock--term-icon">
			<?php echo $term_icon; ?>
		</div>
	<?php endif;
}

/**
 * Prints HTML for the term header
 *
 * @param array $args The array of arguments from the component
 */
function grimlock_term_header( $args ) {
	?>
	<header class="entry-header clearfix">
		<?php
		/**
		 * Hook: grimlock_term_header.
		 *
		 * @hooked grimlock_term_title - 10
		 */
		do_action( 'grimlock_term_header', $args ); ?>
	</header><!-- .entry-header -->
	<?php
}

/**
 * Prints HTML for the term title
 *
 * @param array $args The array of arguments from the component
 */
function grimlock_term_title( $args ) {
	?>
	<h2 class="entry-title">
		<a href="<?php echo esc_url( get_term_link( $args['term_id'] ) ); ?>" title="<?php echo esc_attr( $args['name'] ); ?>" rel="bookmark">
			<?php
			echo esc_html( $args['name'] );

			if ( ! empty( $args['count_displayed'] ) ) :
				echo esc_html( " ({$args['count']})" );
			endif; ?>
		</a>
	</h2><!-- .entry-title -->
	<?php
}

/**
 * Prints HTML for the term description
 *
 * @param array $args The array of arguments from the component
 */
function grimlock_term_description( $args ) {
	if ( ! empty( $args['description_displayed'] ) ) : ?>
		<div class="entry-summary"><?php echo $args['description']; ?></div>
	<?php endif;
}

/**
 * Prints HTML for the term more link
 *
 * @param array $args The array of arguments from the component
 */
function grimlock_term_more_link( $args ) {
	if ( ! empty( $args['more_link_displayed'] ) ) : ?>
		<a href="<?php echo esc_url( get_term_link( $args['term_id'] ) ); ?>" class="more-link btn btn-link">
			<?php esc_html_e( 'Read more', 'grimlock' ); ?>
		</a>
	<?php endif;
}

/**
 * Prints HTML for the term footer
 *
 * @param array $args The array of arguments from the component
 */
function grimlock_term_footer( $args ) {
	ob_start();

	/**
	 * Hook: grimlock_term_footer.
	 */
	do_action( 'grimlock_term_footer', $args );

	$footer_content = ob_get_clean();

	if ( ! empty( trim( $footer_content ) ) ) : ?>
		<footer class="entry-footer clearfix">
			<?php echo $footer_content; ?>
		</footer>
	<?php endif;
}

/**
 * Prints HTML for the thumbnail on singular items (page and single)
 *
 * @param array $args The array of arguments from the component
 */
function grimlock_singular_thumbnail( $args ) {
	if ( ! empty( $args['post_thumbnail_displayed'] ) ) :
		if ( has_post_thumbnail() ) : ?>
			<div class="post-thumbnail">
				<?php the_post_thumbnail( 'large', array( 'class' => 'img-fluid' ) ); ?>
			</div><!-- .post-thumbnail -->
		<?php
		endif;
	endif;
}

/**
 * Prints HTML for the post header
 *
 * @param array $args The array of arguments from the component
 */
/**
 * Prints HTML for the single header
 *
 * @param array $args The array of arguments from the component
 */
function grimlock_single_header( $args ) {
	?>
	<header class="grimlock--single-header grimlock--page-header entry-header">
		<?php
		/**
		 * Hook: grimlock_single_header.
		 *
		 * @hooked grimlock_breadcrumb     - 10
		 * @hooked grimlock_singular_title - 20
		 * @hooked grimlock_post_meta      - 30
		 */
		do_action( 'grimlock_single_header', $args ); ?>
	</header><!-- .entry-header -->
	<?php
}

/**
 * Prints HTML for the breadcrumb
 */
function grimlock_breadcrumb() {
	do_action( 'grimlock_breadcrumb' );
}

/**
 * Prints HTML for the title on singular items (single and page)
 *
 * @param array $args The array of arguments from the component
 */
function grimlock_singular_title() {
	the_title( '<h1 class="single-title page-title entry-title">', '</h1>' );
}

/**
 * Prints HTML for the single content
 *
 * @param array $args The array of arguments from the component
 */
function grimlock_single_content( $args ) {
	?>
	<div class="grimlock--single-content grimlock--page-content entry-content">

		<?php
		/**
		 * Hook: grimlock_single_content.
		 *
		 * @hooked the_content                      - 10
		 * @hooked grimlock_single_link_pages       - 20
		 * @hooked grimlock_single_author_biography - 30
		 */
		do_action( 'grimlock_single_content', $args ); ?>

	</div><!-- .entry-content -->
	<?php
}

/**
 * Prints HTML for the single pages links
 */
function grimlock_single_link_pages() {
	wp_link_pages( array(
		'before'      => '<div class="page-links"><span class="page-links-title">' . esc_html__( 'Pages:', 'grimlock' ) . '</span>',
		'after'       => '</div>',
		'link_before' => '<span>',
		'link_after'  => '</span>',
		'pagelink'    => '<span class="screen-reader-text sr-only">' . esc_html__( 'Page', 'grimlock' ) . ' </span>%',
		'separator'   => '<span class="screen-reader-text sr-only">, </span>',
	) );
}

/**
 * Prints HTML for the single author biography

 * @param array $args The array of arguments from the component
 */
function grimlock_single_author_biography( $args ) {
	if ( ! empty( $args['post_author_biography_displayed'] ) && '' !== get_the_author_meta( 'description' ) && 'post' === get_post_type() ) : ?>
		<hr />
		<div class="media author-info">
			<?php
			$args = array(
				'class' => array( 'd-flex', 'align-self-start', 'mr-3' ),
			);
			echo get_avatar( get_the_author_meta( 'user_email' ), 128, '', '', $args ); ?>
			<div class="author-description media-body">
				<h4 class="author-title media-heading"><span class="author-heading"><?php esc_html_e( 'By', 'grimlock' ); ?></span> <a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>"><?php echo get_the_author(); ?></a></h4>
				<div class="author-bio">
					<?php the_author_meta( 'description' ); ?>
					<div class="mt-1">
						<a class="author-link" href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" rel="author">
							<?php
							// translators: %s: The post author name.
							printf( esc_html__( 'View all posts by %s', 'grimlock' ), get_the_author() ); ?>
						</a>
					</div>
				</div><!-- .author-bio -->
			</div><!-- .author-description -->
		</div><!-- .author-info -->
		<hr />
	<?php endif;
}

/**
 * Prints HTML for the single footer
 *
 * @param array $args The array of arguments from the component
 */
function grimlock_single_footer( $args ) {
	ob_start();

	/**
	 * Hook: grimlock_single_footer.
	 *
	 * @hooked grimlock_category_list  - 10
	 * @hooked grimlock_tag_list       - 20
	 * @hooked grimlock_post_format    - 30
	 * @hooked grimlock_edit_post_link - 40
	 */
	do_action( 'grimlock_single_footer', $args );

	$footer_content = ob_get_clean();

	if ( ! empty( trim( $footer_content ) ) ) : ?>
		<footer class="grimlock--single-footer entry-footer">
			<?php echo $footer_content; ?>
		</footer><!-- .entry-footer -->
	<?php endif;
}

/**
 * Prints HTML for the page header
 *
 * @param array $args The array of arguments from the component
 */
function grimlock_page_header( $args ) {
	?>
	<header class="grimlock--page-header entry-header">
		<?php
		/**
		 * Hook: grimlock_page_header.
		 *
		 * @hooked grimlock_breadcrumb     - 10
		 * @hooked grimlock_singular_title - 20
		 */
		do_action( 'grimlock_page_header', $args ); ?>
	</header><!-- .entry-header -->
	<?php
}

/**
 * Prints HTML for the page content
 *
 * @param array $args The array of arguments from the component
 */
function grimlock_page_content() {
	?>
	<div class="grimlock--page-content entry-content clearfix">
		<?php
		the_content();
		wp_link_pages( array(
			'before'      => '<div class="page-links"><span class="page-links-title">' . esc_html__( 'Pages:', 'grimlock' ) . '</span>',
			'after'       => '</div>',
			'link_before' => '<span>',
			'link_after'  => '</span>',
			'pagelink'    => '<span class="screen-reader-text sr-only">' . esc_html__( 'Page', 'grimlock' ) . ' </span>%',
			'separator'   => '<span class="screen-reader-text sr-only">, </span>',
		) ); ?>
	</div><!-- .entry-content -->
	<?php
}

/**
 * Prints HTML for the page footer
 *
 * @param array $args The array of arguments from the component
 */
function grimlock_page_footer( $args ) {
	ob_start();

	/**
	 * Hook: grimlock_page_footer
	 *
	 * @hooked: grimlock_edit_post_link - 10
	 */
	do_action( 'grimlock_page_footer', $args );

	$footer_content = ob_get_clean();

	if ( ! empty( trim( $footer_content ) ) ) : ?>
		<footer class="grimlock--page-footer entry-footer">
			<?php echo $footer_content; ?>
		</footer><!-- .entry-footer -->
	<?php endif;
}

/**
 * Prints HTML before the custom header title
 *
 * @param array $args The array of arguments from the component
 *
 * @deprecated To be removed in Grimlock 2.0
 */
function grimlock_custom_header_before_title( $args ) {
	echo wp_kses_post( $args['before_title'] );
}

/**
 * Prints HTML after the custom header subtitle
 *
 * @param array $args The array of arguments from the component
 *
 * @deprecated To be removed in Grimlock 2.0
 */
function grimlock_custom_header_after_subtitle( $args ) {
	echo wp_kses_post( $args['after_subtitle'] );
}

/**
 * Prints HTML for the tag list in the custom header
 *
 * @param array $args The array of arguments from the component
 */
function grimlock_single_custom_header_tag_list( $args ) {
	if ( ! empty( $args['single_tag_displayed'] ) && ! empty( $args['post'] ) ) {
		printf(
			'<div class="tag-links">%1$s</div>',
			get_the_tag_list( '', ' ', '', $args['post']->ID )
		); // WPCS: XSS OK.
	}
}

/**
 * Prints HTML for the category list in the custom header
 *
 * @param array $args The array of arguments from the component
 */
function grimlock_single_custom_header_category_list( $args ) {
	if ( ! empty( $args['single_category_displayed'] ) && ! empty( $args['post'] ) ) {
		printf(
			'<div class="cat-links">%1$s</div>',
			get_the_category_list( ' ', ' ', $args['post']->ID )
		); // WPCS: XSS OK.
	}
}

/**
 * Prints HTML for the post format in the custom header
 *
 * @param array $args The array of arguments from the component
 */
function grimlock_single_custom_header_post_format( $args ) {
	if ( ! empty( $args['single_post_format_displayed'] ) && ! empty( $args['post'] ) ) {
		$post_format = get_post_format( $args['post']->ID );

		if ( empty( $post_format ) ) {
			return;
		}

		printf(
			'<div class="post-format-links"><a href="%1$s" rel="bookmark" class="badge badge-primary post-format post-format--' . esc_attr( $post_format ) . '"><span class="badge__name">%2$s</span></a></div>',
			esc_attr( get_post_format_link( $post_format ) ),
			get_post_format_string( $post_format )
		); // WPCS: XSS OK.
	}
}

/**
 * Prints HTML for the post date in the custom header
 *
 * @param array $args The array of arguments from the component
 */
function grimlock_single_custom_header_post_date( $args ) {
	if ( ! empty( $args['single_post_date_displayed'] ) && ! empty( $args['post'] ) ) {
		$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time>';

		$time_string = sprintf( $time_string,
			esc_attr( get_the_date( 'c', $args['post']->ID ) ),
			esc_html( get_the_date( '', $args['post']->ID ) )
		); // WPCS: XSS OK.

		printf(
			'<div class="posted-on"><span class="posted-on-label">' . esc_html__( 'Posted on', 'grimlock' ) . ' </span>%s</div>',
			'<a href="' . esc_url( get_permalink( $args['post']->ID ) ) . '" rel="bookmark">' . $time_string . '</a>'
		); // WPCS: XSS OK.
	}
}

/**
 * Prints HTML for the post author in the custom header
 *
 * @param array $args The array of arguments from the component
 */
function grimlock_single_custom_header_post_author( $args ) {
	if ( ! empty( $args['single_post_author_displayed'] ) && ! empty( $args['post'] ) ) {
		$byline = sprintf(
			'<span class="byline-label">' . esc_html__( 'by', 'grimlock' ) . ' </span>%1$s %2$s',
			'<span class="avatar-round-ratio medium"><span class="author-avatar">' . '<a class="url fn n" href="' . esc_url( get_author_posts_url( $args['post']->post_author ) ) . '">' . get_avatar( $args['post']->post_author, 52 ) . '</a></span></span>',
			'<span class="author-vcard vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( $args['post']->post_author ) ) . '">' .
			esc_html( get_the_author_meta( 'display_name', $args['post']->post_author ) ) .
			'</a></span>'
		);

		echo '<div class="byline author"> ' . $byline . '</div>'; // WPCS: XSS OK.
	}
}

/**
 * Prints HTML for the custom header breadcrumb
 *
 * @param array $args The array of arguments from the component
 *
 * @deprecated To be removed in Grimlock 2.0
 */
function grimlock_custom_header_breadcrumb( $args ) {
	do_action( 'grimlock_breadcrumb', $args );
}

/**
 * Prints left sidebar in page.
 */
function grimlock_sidebar_left_widget_area() {
	$sidebar_id = apply_filters( 'grimlock_template_sidebar_left_id', 'sidebar-1' );
	if ( apply_filters( 'grimlock_template_sidebar_left_displayed', true ) && is_active_sidebar( $sidebar_id ) ) :
		?>
		<aside id="secondary-left" class="widget-area sidebar grimlock-region__col grimlock-region__col--1 region__col region__col--1">
			<?php dynamic_sidebar( $sidebar_id ); ?>
		</aside><!-- #secondary-left -->
	<?php
	endif;
}

/**
 * Prints right sidebar in page.
 */
function grimlock_sidebar_right_widget_area() {
	$sidebar_id = apply_filters( 'grimlock_template_sidebar_right_id', 'sidebar-2' );
	if ( apply_filters( 'grimlock_template_sidebar_right_displayed', true ) && is_active_sidebar( $sidebar_id ) ) :
		?>
		<aside id="secondary-right" class="widget-area sidebar grimlock-region__col grimlock-region__col--3 region__col region__col--3">
			<?php dynamic_sidebar( $sidebar_id ); ?>
		</aside><!-- #secondary-right -->
		<?php
	endif;
}

/**
 * Prints the search modal in the wp footer
 */
function grimlock_search_modal() {
	if ( apply_filters( 'grimlock_search_modal_displayed', false ) ) :
		?>
		<div class="modal grimlock-modal-search modal-search fade" id="grimlock-modal-search" tabindex="-1" role="dialog" aria-hidden="true">

			<button type="button" class="grimlock-navbar-search__close navbar-search__close" data-dismiss="modal" aria-label="Close">
				<i class="fa fa-lg fa-times-circle-o"></i>
			</button> <!-- .navbar-search__close -->

			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content bg-transparent">
					<div class="modal-body">
						<?php do_action( 'grimlock_search_modal_content' ); ?>
					</div> <!-- .modal-body -->
				</div> <!-- .modal-content -->
			</div> <!-- .modal-dialog -->

		</div> <!-- .modal -->
		<?php
	endif;
}

/**
 * Prints the search form in the search modal
 */
function grimlock_search_modal_search_form() {
	?>
	<div class="container container--narrow">
		<?php get_search_form(); ?>
	</div> <!-- .container -->
	<?php
}

/**
 * Prints the widget area in the search modal
 */
function grimlock_search_modal_widget_area() {
	$sidebar_id = apply_filters( 'grimlock_template_sidebar_navbar_search_id', 'navbar-search-1' );
	if ( is_active_sidebar( $sidebar_id ) ) : ?>
		<aside id="sidebar-navbar-search" class="widget-area">
			<?php dynamic_sidebar( $sidebar_id ); ?>
		</aside> <!-- #sidebar-navbar-search -->
	<?php endif;
}

/**
 * Display navigation to next/previous post.
 */
function grimlock_the_post_navigation() {
	if ( apply_filters( 'grimlock_single_post_navigation_displayed', true ) ) {
		the_post_navigation( apply_filters( 'grimlock_the_post_navigation_args', array() ) );
	}
}

/**
 * Get term thumbnail id
 *
 * @param int $term_id ID of the term
 *
 * @return int|bool|null ID of the term thumbnail
 */
function grimlock_get_term_thumbnail_id( $term_id ) {
	if ( empty( $term_id ) ) {
		return false;
	}

	$term_thumbnail_id = false;

	// WooCommerce
	if ( function_exists( 'WC' ) ) {
		$term_thumbnail_id = get_term_meta( $term_id, 'thumbnail_id', true );
	}

	// Taxonomy Thumbnail
	if ( empty( $term_thumbnail_id ) && function_exists( 'get_term_thumbnail_id' ) ) {
		$term_thumbnail_id = get_term_thumbnail_id( $term_id );
	}

	if ( empty( $term_thumbnail_id ) && function_exists( '_wp_term_images' ) ) {
		$term_thumbnail_id = get_term_meta( $term_id, 'image', true );
	}

	// Categories Images
	if ( empty( $term_thumbnail_id ) && function_exists( 'z_taxonomy_image_url' ) ) {
		$term_thumbnail_url = get_option( 'z_taxonomy_image' . $term_id );
		$term_thumbnail_id  = attachment_url_to_postid( $term_thumbnail_url );
	}

	// Category and Taxonomy Image
	if ( empty( $term_thumbnail_id ) && function_exists( 'get_wp_term_image' ) ) {
		$term_thumbnail_url = get_wp_term_image( $term_id );
		$term_thumbnail_id  = attachment_url_to_postid( $term_thumbnail_url );
	}

	// Custom Category Image
	if ( empty( $term_thumbnail_id ) && class_exists( 'MGC_Custom_Category_Image' ) ) {
		$term_thumbnail_id = get_term_meta( $term_id, '_category_image_id', true );
	}

	// Taxonomy Images
	if ( empty( $term_thumbnail_id ) && class_exists( 'Taxonomy_Images_Term' ) ) {
		$t                 = new Taxonomy_Images_Term( $term_id );
		$term_thumbnail_id = $t->get_image_id();
	}

	return $term_thumbnail_id;
}

/**
 * Get term icon
 *
 * @param int $term_id ID of the term
 *
 * @return string|bool HTML for the term icon
 */
function grimlock_get_term_icon( $term_id ) {
	if ( empty( $term_id ) ) {
		return false;
	}

	$term_icon = false;

	// WP Term Icons
	if ( class_exists( 'WP_Term_Icons' ) ) {
		$term_icon_class = get_term_meta( $term_id, 'icon', true );
		$term_icon       = '<i class="dashicons ' . esc_html( $term_icon_class ) . '"></i>';
	}

	// Taxonomy Icons
	if ( function_exists( 'tax_icons_output_term_icon' ) ) {
		$term_icon = tax_icons_output_term_icon( $term_id );
	}

	return $term_icon;
}
