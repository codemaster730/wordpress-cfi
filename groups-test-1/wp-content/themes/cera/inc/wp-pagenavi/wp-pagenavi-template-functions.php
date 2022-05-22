<?php
/**
 * Cera template functions for WP PageNavi.
 *
 * @package cera
 */

if ( ! function_exists( 'cera_wp_pagenavi' ) ) :
	/**
	 * Display posts navigation using native feature or WP PageNavi plugin.
	 *
	 * @since 1.0.0
	 */
	function cera_wp_pagenavi() {
		if ( function_exists( 'wp_pagenavi' ) ) {
			wp_pagenavi();
		}
	}
endif;
