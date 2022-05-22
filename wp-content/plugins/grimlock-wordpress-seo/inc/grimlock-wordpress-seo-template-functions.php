<?php
/**
 * Grimlock for Yoast SEO template functions.
 *
 * @package grimlock-wordpress-seo
 */

/**
 * Returns the primary term for the chosen taxonomy set by Yoast SEO or the first term selected.
 *
 * @since 1.0.3
 *
 * @param integer        $post     The post id.
 * @param string         $taxonomy The taxonomy to query. Defaults to category.
 *
 * @return WP_Term|false           The term with keys of 'title', 'slug', and 'url'.
 */
function grimlock_wordpress_seo_get_primary_term( $post = 0, $taxonomy = 'category' ) {
	if ( ! $post ) {
		$post = get_the_ID();
	}

	$terms = get_the_terms( $post, $taxonomy );

	if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
		if ( class_exists( 'WPSEO_Primary_Term' ) ) {
			$wpseo_primary_term = new WPSEO_Primary_Term( $taxonomy, $post );
			$wpseo_primary_term = $wpseo_primary_term->get_primary_term();
			$term               = get_term( $wpseo_primary_term, $taxonomy );

			if ( ! empty( $term ) && ! is_wp_error( $term ) ) {
				return $term;
			}
		}
		return $terms[0];
	}
	return false;
}

/**
 * * Prints HTML for the primary category
 *
 * @param array $args The array of arguments from the component
 */
function grimlock_wordpress_seo_category_list( $args ) {
	if ( ! empty( $args['category_displayed'] ) && 'post' === get_post_type() ) :
		$primary_cat      = grimlock_wordpress_seo_get_primary_term();
		$primary_cat_link = '<a href="' . esc_url( get_category_link( $primary_cat ) ) . '" rel="category tag">' . $primary_cat->name . '</a>';
		if ( $primary_cat ) {
			printf( '<span class="cat-links"><span class="cat-links-label">' . esc_html__( 'Posted in', 'grimlock' ) . ' </span>%1$s </span>', $primary_cat_link ); // WPCS: XSS OK.
		}
	endif;
}
