<?php
/**
 * Grimlock_Home_Customizer Class
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
class Grimlock_Home_Customizer {
	use Grimlock_Custom_Header;

	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'grimlock_archive_customizer_is_template', array( $this, 'is_template'            ), 10, 1 );
		add_filter( 'grimlock_custom_header_args',             array( $this, 'add_custom_header_args' ), 30, 1 );
	}

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
		if ( is_home() && is_front_page() ) {
			$args['title']    = get_bloginfo( 'site_title' );
			$args['subtitle'] = get_bloginfo( 'description' );
		} elseif ( is_home() && ! is_front_page() ) {
			$this->add_custom_header_title( $args );
			$this->add_custom_header_subtitle( $args );
		}
		return $args;
	}

	/**
	 * Check if template is the template for the Home or Blog page.
	 *
	 * @since 1.0.0
	 *
	 * @param  bool $default The default value for the check
	 *
	 * @return bool          The filtered value for the check
	 */
	public function is_template( $default = false ) {
		return is_home() || $default;
	}
}

return new Grimlock_Home_Customizer();
