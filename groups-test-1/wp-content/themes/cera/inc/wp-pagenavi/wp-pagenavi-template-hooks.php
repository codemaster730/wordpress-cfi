<?php
/**
 * Cera template hooks for WP PageNavi.
 *
 * @package cera
 */

/**
 * After posts hooks.
 *
 * @since 1.0.0
 */
remove_action( 'cera_after_posts', 'the_posts_navigation', 20 );
add_action(    'cera_after_posts', 'cera_wp_pagenavi',   10 );
