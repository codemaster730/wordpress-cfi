<?php
/**
 * Class Grimlock_Login_WordPress_Social_Login
 *
 * @author  Themosaurus
 * @since   1.0.0
 * @package grimlock-login
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The main Grimlock Login WordPress Social Login class.
 */
class Grimlock_Login_WordPress_Social_Login {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'login_form_middle', array( $this, 'add_login_form_social_button' ) );
	}

	/**
	 * Add social button to the login form
	 *
	 * @param string $content Login form "middle" content
	 *
	 * @return string
	 */
	public function add_login_form_social_button( $content ) {
		ob_start();
		wsl_render_auth_widget_in_wp_login_form();
		$social_login = ob_get_clean();
		return $content . $social_login;
	}
}
