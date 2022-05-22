<?php
/**
 * Cera template hooks for Projects by WooThemes.
 *
 * @package cera
 */

/**
 * Content wrapper hooks.
 *
 * @since 1.0.0
 */
remove_action( 'projects_before_main_content', 'projects_output_content_wrapper',            10 );
remove_action( 'projects_after_main_content',  'projects_output_content_wrapper_end',        10 );
add_action(    'projects_before_main_content', 'cera_projects_output_content_wrapper',     10 );
add_action(    'projects_after_main_content',  'cera_projects_output_content_wrapper_end', 10 );

/**
 * Sidebar hooks.
 *
 * @since 1.0.0
 */
remove_action( 'projects_sidebar', 'projects_get_sidebar', 10 );

/**
 * Before single projects hooks.
 *
 * @since 1.0.0
 */
remove_action( 'projects_before_single_project_summary', 'projects_template_single_gallery',        40 );
add_action(    'projects_before_single_project_summary', 'cera_projects_template_single_gallery', 40 );
