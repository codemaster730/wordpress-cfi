<?php
/**
 * Cera_Grimlock_Login Class
 *
 * @package  cera
 * @author   Themosaurus
 * @since    1.1.9
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The Cera Grimlock Login integration class.
 */
class Cera_Grimlock_Login {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'grimlock_login_custom_logo_url', array( $this, 'add_custom_logo_url' ), 10, 1 );

		require_once get_template_directory() . '/inc/grimlock-login/customizer/class-cera-grimlock-login-customizer.php';
	}

	/**
	 * Add a custom logo URL when empty.
	 *
	 * @since 1.1.9
	 *
	 * @param  string $url The URL for the custom logo.
	 *
	 * @return string      The updated URL for the custom logo.
	 */
	public function add_custom_logo_url( $url ) {
		return empty( $url ) ? get_stylesheet_directory_uri() . '/assets/images/logo.png' : $url;
	}
}

return new Cera_Grimlock_Login();
