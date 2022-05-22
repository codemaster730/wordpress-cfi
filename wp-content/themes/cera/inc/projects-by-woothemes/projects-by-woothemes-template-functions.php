<?php
/**
 * Cera template functions for Projects by WooThemes.
 *
 * @package cera
 */

if ( ! function_exists( 'cera_projects_output_content_wrapper' ) ) :
	/**
	 * Output the start of the page wrapper.
	 *
	 * @since 1.0.0
	 */
	function cera_projects_output_content_wrapper() {
		?>
		<div id="primary" class="content-area region__col region__col--2">
			<main id="main" class="site-main">
		<?php
	}
endif;


if ( ! function_exists( 'cera_projects_output_content_wrapper_end' ) ) :
	/**
	 * Output the end of the page wrapper.
	 *
	 * @since 1.0.0
	 */
	function cera_projects_output_content_wrapper_end() {
		?>
			</main><!-- #main -->
		</div><!-- #primary -->
		<?php
	}
endif;

if ( ! function_exists( 'cera_projects_template_single_gallery' ) ) {
	/**
	 * Output the project gallery before the single project summary.
	 *
	 * @since 1.0.0
	 */
	function cera_projects_template_single_gallery() {
		if ( function_exists( 'projects_get_gallery_attachment_ids' ) ) :
			$attachment_ids = projects_get_gallery_attachment_ids();
			echo do_shortcode( '[gallery ids="' . implode( ',', $attachment_ids ) . '"]' );
		endif;
	}
}
