<?php
/**
 * Grimlock_Login_Global_Customizer Class
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
 * The Grimlock Login Global Customizer class.
 */
class Grimlock_Login_Global_Customizer extends Grimlock_Global_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::__construct();
		add_action( 'login_enqueue_scripts', array( $this, 'enqueue_styles' ), 10 );
	}

	/**
	 * Enqueue custom styles based on theme mods.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_styles() {
		$styles = "
		.login,
		#login,
		.grimlock-login--fullscreen-right:after,
		.grimlock-login--fullscreen-left:after {
			background-color: {$this->get_theme_mod( 'content_background_color' )} !important;
		}";
		wp_add_inline_style( 'grimlock-login-login', $styles );
	}
}

return new Grimlock_Login_Global_Customizer();
