<?php
/**
 * Grimlock_bbPress_Search_Customizer Class
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
class Grimlock_bbPress_Search_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'grimlock_custom_header_args',             array( $this, 'add_custom_header_args' ), 30, 1 );
		add_filter( 'grimlock_bbpress_customizer_is_template', array( $this, 'is_template'            ), 10, 1 );
		add_filter( 'grimlock_archive_customizer_is_template', array( $this, 'archive_customizer_is_template'  ), 10, 1 );
	}

	/**
	 * @param $default
	 *
	 * @return bool
	 */
	public function is_template( $default = false ) {
		return function_exists( 'bbp_is_search' ) && bbp_is_search() ||
		       function_exists( 'bbp_is_search_results' ) && bbp_is_search_results() ||
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
			$args['title']    = esc_html__( 'Search Results', 'grimlock-bbpress' );
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

return new Grimlock_bbPress_Search_Customizer();
