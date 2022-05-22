<?php
/**
 * Grimlock template hooks.
 *
 * @package grimlock
 */

/**
 * Post component hooks
 *
 * @see grimlock_post_template
 *
 * @see grimlock_post_thumbnail
 *
 * @see grimlock_post_header
 * @see grimlock_post_format
 * @see grimlock_post_content
 * @see grimlock_post_excerpt
 * @see grimlock_post_footer
 *
 * @see grimlock_post_title
 * @see grimlock_post_meta
 *
 * @see grimlock_category_list
 * @see grimlock_tag_list
 * @see grimlock_comments_link
 * @see grimlock_edit_post_link
 */
add_action( 'grimlock_post_template',         'grimlock_post_template',  10, 1 );

add_action( 'grimlock_post_before_card_body', 'grimlock_post_thumbnail', 10, 1 );

add_action( 'grimlock_post_card_body',        'grimlock_post_format',    10, 1 );
add_action( 'grimlock_post_card_body',        'grimlock_post_header',    20, 1 );
add_action( 'grimlock_post_card_body',        'grimlock_post_content',   30, 1 );
add_action( 'grimlock_post_card_body',        'grimlock_post_excerpt',   40, 1 );
add_action( 'grimlock_post_card_body',        'grimlock_post_footer',    50, 1 );

add_action( 'grimlock_post_header',           'grimlock_post_title',     10, 1 );
add_action( 'grimlock_post_header',           'grimlock_category_list',  20, 1 );
add_action( 'grimlock_post_header',           'grimlock_post_meta',      30, 1 );

add_action( 'grimlock_post_footer',           'grimlock_tag_list',       20, 1 );
add_action( 'grimlock_post_footer',           'grimlock_comments_link',  30, 1 );
add_action( 'grimlock_post_footer',           'grimlock_edit_post_link', 40, 1 );

/**
 * Term component hooks
 *
 * @see grimlock_term_template
 *
 * @see grimlock_term_thumbnail
 *
 * @see grimlock_term_header
 * @see grimlock_term_description
 * @see grimlock_term_more_link
 * @see grimlock_term_footer
 *
 * @see grimlock_term_title
 */
add_action( 'grimlock_term_template',         'grimlock_term_template',    10, 1 );

add_action( 'grimlock_term_before_card_body', 'grimlock_term_thumbnail',   10, 1 );

add_action( 'grimlock_term_card_body',        'grimlock_term_header',      10, 1 );
add_action( 'grimlock_term_card_body',        'grimlock_term_description', 20, 1 );
add_action( 'grimlock_term_card_body',        'grimlock_term_more_link',   30, 1 );
add_action( 'grimlock_term_card_body',        'grimlock_term_footer',      40, 1 );

add_action( 'grimlock_term_header',           'grimlock_term_icon',        5,  1 );
add_action( 'grimlock_term_header',           'grimlock_term_title',       10, 1 );

/**
 * Single component hooks
 *
 * @see grimlock_singular_thumbnail
 * @see grimlock_single_header
 * @see grimlock_single_content
 * @see grimlock_single_footer
 *
 * @see grimlock_breadcrumb
 * @see grimlock_singular_title
 * @see grimlock_post_meta
 *
 * @see the_content
 * @see grimlock_single_link_pages
 * @see grimlock_single_author_biography
 *
 * @see grimlock_category_list
 * @see grimlock_tag_list
 * @see grimlock_post_format
 * @see grimlock_edit_post_link
 */
add_action( 'grimlock_single_template', 'grimlock_singular_thumbnail',      10, 1 );
add_action( 'grimlock_single_template', 'grimlock_single_header',           20, 1 );
add_action( 'grimlock_single_template', 'grimlock_single_content',          30, 1 );
add_action( 'grimlock_single_template', 'grimlock_single_footer',           40, 1 );

add_action( 'grimlock_single_header',   'grimlock_breadcrumb',              10, 1 );
add_action( 'grimlock_single_header',   'grimlock_singular_title',          20, 1 );
add_action( 'grimlock_single_header',   'grimlock_post_meta',               30, 1 );

add_action( 'grimlock_single_content',  'the_content',                      10, 1 );
add_action( 'grimlock_single_content',  'grimlock_single_link_pages',       20, 1 );
add_action( 'grimlock_single_content',  'grimlock_single_author_biography', 30, 1 );

add_action( 'grimlock_single_footer',   'grimlock_category_list',           10, 1 );
add_action( 'grimlock_single_footer',   'grimlock_tag_list',                20, 1 );
add_action( 'grimlock_single_footer',   'grimlock_post_format',             30, 1 );
add_action( 'grimlock_single_footer',   'grimlock_edit_post_link',          40, 1 );

/**
 * Page component hooks
 *
 * @see grimlock_singular_thumbnail
 * @see grimlock_page_header
 * @see grimlock_page_content
 * @see grimlock_page_footer
 *
 * @see grimlock_breadcrumb
 * @see grimlock_singular_title
 *
 * @see grimlock_edit_post_link
 */
add_action( 'grimlock_page_template', 'grimlock_singular_thumbnail', 10, 1 );
add_action( 'grimlock_page_template', 'grimlock_page_header',        20, 1 );
add_action( 'grimlock_page_template', 'grimlock_page_content',       30, 1 );
add_action( 'grimlock_page_template', 'grimlock_page_footer',        40, 1 );

add_action( 'grimlock_page_header',   'grimlock_breadcrumb',         10, 1 );
add_action( 'grimlock_page_header',   'grimlock_singular_title',     20, 1 );

add_action( 'grimlock_page_footer',   'grimlock_edit_post_link',     10, 1 );

/**
 * Custom Header component hooks
 *
 * @see grimlock_custom_header_before_title
 * @see grimlock_single_custom_header_tag_list
 * @see grimlock_single_custom_header_category_list
 * @see grimlock_single_custom_header_post_format
 *
 * @see grimlock_custom_header_after_subtitle
 * @see grimlock_single_custom_header_post_date
 * @see grimlock_single_custom_header_post_author
 * @see grimlock_custom_header_breadcrumb
 */
add_action( 'grimlock_custom_header_before_title',   'grimlock_custom_header_before_title',         10, 1 );
add_action( 'grimlock_custom_header_before_title',   'grimlock_single_custom_header_tag_list',      15, 1 );
add_action( 'grimlock_custom_header_before_title',   'grimlock_single_custom_header_category_list', 20, 1 );
add_action( 'grimlock_custom_header_before_title',   'grimlock_single_custom_header_post_format',   30, 1 );

add_action( 'grimlock_custom_header_after_subtitle', 'grimlock_custom_header_after_subtitle',       10, 1 );
add_action( 'grimlock_custom_header_after_subtitle', 'grimlock_single_custom_header_post_date',     20, 1 );
add_action( 'grimlock_custom_header_after_subtitle', 'grimlock_single_custom_header_post_author',   30, 1 );
add_action( 'grimlock_custom_header_after_subtitle', 'grimlock_custom_header_breadcrumb',           40, 1 );

/**
 * Sidebars hooks
 */
add_action( 'grimlock_sidebar_left',  'grimlock_sidebar_left_widget_area',  10 );
add_action( 'grimlock_sidebar_right', 'grimlock_sidebar_right_widget_area', 10 );

/**
 * Search modal hooks
 */
add_action( 'wp_footer',                     'grimlock_search_modal',             0  );
add_action( 'grimlock_search_modal_content', 'grimlock_search_modal_search_form', 10 );
add_action( 'grimlock_search_modal_content', 'grimlock_search_modal_widget_area', 20 );
