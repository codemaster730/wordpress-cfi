<?php
/**
 * Cera template functions for Yoast SEO.
 *
 * @package cera
 */

if ( ! function_exists( 'cera_yoast_breadcrumb' ) ) :
	/**
	 * Display the WordPress SEO breadcrumb before the content.
	 *
	 * @since 1.0.0
	 */
	function cera_yoast_breadcrumb() {
		yoast_breadcrumb( '<div class="breadcrumb yoast-breadcrumb">', '</div>' );
	}
endif;
