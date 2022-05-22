<?php
/**
 * Grimlock for Yoast SEO template hooks.
 *
 * @package grimlock-wordpress-seo
 */

remove_action( 'grimlock_post_footer',   'grimlock_category_list',               10 );
add_action(    'grimlock_post_footer',   'grimlock_wordpress_seo_category_list', 10, 1 );

remove_action( 'grimlock_single_footer', 'grimlock_category_list',               10 );
add_action(    'grimlock_single_footer', 'grimlock_wordpress_seo_category_list', 10, 1 );
