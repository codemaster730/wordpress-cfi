<?php
/**
 * Grimlock_Singular_Template_Customizer Class
 *
 * @author  Themosaurus
 * @since   1.0.0
 * @package grimlock
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The Grimlock Customizer class for the templates.
 */
abstract class Grimlock_Singular_Template_Customizer extends Grimlock_Template_Customizer {
	use Grimlock_Custom_Header;

	/**
	 * Add arguments using theme mods to customize the Custom Header.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args The default arguments to render the Custom Header.
	 *
	 * @return array      The arguments to render the Custom Header.
	 */
	public function add_custom_header_args( $args ) {
		$args = parent::add_custom_header_args( $args );

		if ( $this->is_template() ) {
			$this->add_custom_header_title( $args );

			$header_image_id          = attachment_url_to_postid( get_header_image() );
			$header_layout            = $this->get_theme_mod( "{$this->id}_custom_header_layout" );
			$background_image_size    = apply_filters( "grimlock_{$this->id}_customizer_custom_header_background_image_size", 'custom-header', $header_layout );
			$thumbnail_size           = apply_filters( "grimlock_{$this->id}_customizer_custom_header_thumbnail_size", "thumbnail-{$header_layout}", $header_layout );

			$args['background_image'] = wp_get_attachment_image_url( $header_image_id, $background_image_size );
			$_post                    = get_queried_object();

			if ( ! empty( $_post ) && $_post instanceof WP_Post && isset( $_post->ID ) ) {
				$this->add_custom_header_subtitle( $args );
				$post_thumbnail_id = get_post_thumbnail_id( $_post->ID );

				if ( ! empty( $post_thumbnail_id ) ) {
					$background_image         = wp_get_attachment_image_url( $post_thumbnail_id, $background_image_size );
					$args['background_image'] = $background_image;

					$thumbnail = wp_get_attachment_image_url( $post_thumbnail_id, $thumbnail_size );
					$args['thumbnail'] = $thumbnail;
				}
			}
		}
		return $args;
	}
}
