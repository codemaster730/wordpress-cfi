<?php
/**
 * Class Grimlock_Login_Super_Socializer
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
 * The main Grimlock Login Super Socializer class.
 */
class Grimlock_Login_Super_Socializer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		global $theChampLoginOptions;
		if ( isset( $theChampLoginOptions['enableAtLogin'] ) && $theChampLoginOptions['enableAtLogin'] == 1 ) {
			add_filter( 'login_form_middle', array( $this, 'add_login_form_social_button' ) );
		}
	}

	/**
	 * Add social button to the login form
	 *
	 * @param string $content Login form "middle" content
	 *
	 * @return string
	 */
	public function add_login_form_social_button( $content ) {
		return $content . the_champ_login_button( true );
	}
}
