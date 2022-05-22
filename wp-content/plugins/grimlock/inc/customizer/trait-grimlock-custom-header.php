<?php
/**
 * Grimlock_Custom_Header Trait
 *
 * @author  themosaurus
 * @since   1.2.10
 * @package grimlock
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The Grimlock trait containing custom header methods
 */
trait Grimlock_Custom_Header {

	/**
	 * Change the title for the Custom Header.
	 *
	 * @since 1.2.10
	 *
	 * @param  array $args The array of arguments for the Custom Header.
	 */
	public function add_custom_header_title( &$args ) {
		if ( $this->is_template() ) {
			$args['title'] = single_post_title( '', false );
		}
	}

	/**
	 * Change the subtitle for the Custom Header.
	 *
	 * @since 1.2.10
	 *
	 * @param  array $args The array of arguments for the Custom Header.
	 */
	public function add_custom_header_subtitle( &$args ) {
		if ( $this->is_template() ) {
			$_post = get_queried_object();

			if ( ! empty( $_post ) && $_post instanceof WP_Post && isset( $_post->ID ) ) {
				$args['subtitle'] = '' !== $_post->post_excerpt ? "<span class='excerpt'>{$_post->post_excerpt}</span>" : '';
			}
		}
	}
}
