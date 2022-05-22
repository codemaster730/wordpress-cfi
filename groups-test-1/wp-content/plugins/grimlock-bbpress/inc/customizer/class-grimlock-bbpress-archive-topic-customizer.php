<?php
/**
 * Grimlock_bbPress_Archive_Topic_Customizer Class
 *
 * @author  Themosaurus
 * @since   1.0.0
 * @package grimlock
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The Grimlock Customizer class for the posts page and the archive pages.
 */
class Grimlock_bbPress_Archive_Topic_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'grimlock_custom_header_args',             array( $this, 'add_custom_header_args'          ), 30, 1 );
		add_filter( 'grimlock_bbpress_customizer_is_template', array( $this, 'is_template'                     ), 10, 1 );
		add_filter( 'grimlock_archive_customizer_is_template', array( $this, 'archive_customizer_is_template'  ), 10, 1 );
	}

	/**
	 * @param $default
	 *
	 * @return bool
	 */
	public function is_template( $default = false ) {
		return function_exists( 'bbp_is_topic_archive' ) && bbp_is_topic_archive() ||
		       function_exists( 'bbp_is_topic_tag' ) && bbp_is_topic_tag() ||
		       function_exists( 'bbp_is_topic_tag_edit' ) && bbp_is_topic_tag_edit() ||
		       $default;
	}

	/**
	 * Change the title for the Custom Header.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $args The array of arguments for the Custom Header.
	 *
	 * @return array       The filtered array of arguments for the Custom Header.
	 */
	public function add_custom_header_args( $args ) {
		if ( $this->is_template() ) {
			$post_type_obj = get_post_type_object( 'topic' );
			$archive_title = esc_html__( 'Topics', 'grimlock-bbpress' );

			if ( is_object( $post_type_obj ) && isset( $post_type_obj->label ) && $post_type_obj->label !== '' ) {
				$archive_title = $post_type_obj->label;
			}
			$args['title']    = $archive_title;
			$args['subtitle'] = '';
		}
		return $args;
	}

	/**
	 * Disinherit archive customizer settings
	 *
	 * @param bool $default True if we are on a default archive page
	 *
	 * @return bool
	 */
	public function archive_customizer_is_template( $default ) {
		return $default && ! $this->is_template();
	}
}

return new Grimlock_bbPress_Archive_Topic_Customizer();
