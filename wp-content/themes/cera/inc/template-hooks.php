<?php
/**
 * Cera template hooks.
 *
 * @package cera
 */


/**
 * Before Site Hooks
 *
 * @see cera_header()
 *
 * @since 1.0.0
 */
add_action( 'cera_before_site', 'cera_before_site', 10 );

/**
 * Header Hooks
 *
 * @see cera_header()
 *
 * @since 1.0.0
 */
add_action( 'cera_header', 'cera_header', 10 );

/**
 * Footer Hooks
 *
 * @see cera_footer()
 *
 * @since 1.0.0
 */
add_action( 'cera_footer', 'cera_footer', 10 );

/**
 * Sidebar Hooks
 *
 * @see cera_sidebar_right()
 *
 * @since 1.0.0
 */
add_action( 'cera_sidebar_right',                  'cera_sidebar_right',                  10 );
add_action( 'cera_vertical_navbar_sidebar_top',    'cera_vertical_navbar_sidebar_top',    10 );
add_action( 'cera_vertical_navbar_sidebar_bottom', 'cera_vertical_navbar_sidebar_bottom', 10 );

/**
 * Before Posts Hooks.
 *
 * @see cera_before_posts()
 *
 * @since 1.0.0
 */
add_action( 'cera_before_posts', 'cera_before_posts',  10 );


/**
 * Before Search Posts Hooks.
 *
 * @see cera_before_search_posts()
 *
 * @since 1.0.0
 */
add_action( 'cera_before_search_posts', 'cera_before_search_posts',  10 );

/**
 * After Posts Hooks.
 *
 * @see cera_after_posts()
 * @see the_posts_navigation()
 *
 * @since 1.0.0
 */
add_action( 'cera_after_posts', 'cera_after_posts',   10 );
add_action( 'cera_after_posts', 'the_posts_navigation', 20 );

/**
 * Post Hooks
 *
 * @see cera_post()
 *
 * @since 1.0.0
 */
add_action( 'cera_post', 'cera_post', 10 );

/**
 * Search Post Hooks
 *
 * @see cera_search_post()
 *
 * @since 1.0.0
 */
add_action( 'cera_search_post', 'cera_search_post', 10 );

/**
 * Single Post Hooks
 *
 * @see cera_single()
 *
 * @since 1.0.0
 */
add_action( 'cera_single',              'cera_single',       10 );
add_action( 'cera_the_post_navigation', 'the_post_navigation', 10 );

/**
 * Page Hooks
 *
 * @see cera_page()
 *
 * @since 1.0.0
 */
add_action( 'cera_page', 'cera_page', 10 );

/**
 * 404 Hooks
 *
 * @see cera_404()
 *
 * @since 1.1.8
 */
add_action( 'cera_404', 'cera_404', 10 );

/**
 * Other Hooks
 *
 * @see cera_body_classes()
 * @see cera_nav_menu_css_class()
 * @see cera_get_the_archive_title()
 * @see cera_theme_page_templates()
 *
 * @since 1.0.0
 */
add_filter( 'body_class',            'cera_body_classes',          10, 1 );
add_filter( 'nav_menu_css_class',    'cera_nav_menu_css_class',    10, 4 );
add_filter( 'get_the_archive_title', 'cera_get_the_archive_title', 10, 1 );
add_filter( 'theme_page_templates',  'cera_theme_page_templates',  10, 1 );
