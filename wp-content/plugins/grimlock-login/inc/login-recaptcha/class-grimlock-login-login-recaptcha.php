<?php
/**
 * Class Grimlock_Login_Login_Recaptcha
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
 * The main Grimlock Login Login Recaptcha class.
 */
class Grimlock_Login_Login_Recaptcha {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		// Fix Recaptcha in login modal
		if ( ! is_user_logged_in() &&
		     LoginNocaptcha::valid_key_secret( get_option( 'login_nocaptcha_key' ) ) &&
		     LoginNocaptcha::valid_key_secret( get_option( 'login_nocaptcha_secret' ) ) ) {

			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

			add_filter( 'login_form_middle', array( $this, 'add_login_form_recaptcha' ), 20 );
		}
	}

	/**
	 * Enqueue Login Recaptcha scripts
	 */
	public function enqueue_scripts() {
		LoginNocaptcha::enqueue_scripts_css();
		wp_enqueue_script( 'login_nocaptcha_google_api' );
		wp_enqueue_style( 'login_nocaptcha_css' );
	}

	/**
	 * Add recaptcha in login form
	 *
	 * @param string $content Login form "middle" content
	 *
	 * @return string
	 */
	public function add_login_form_recaptcha( $content ) {
		ob_start();
		LoginNocaptcha::nocaptcha_form();
		$content .= ob_get_clean();
		return $content;
	}
}
