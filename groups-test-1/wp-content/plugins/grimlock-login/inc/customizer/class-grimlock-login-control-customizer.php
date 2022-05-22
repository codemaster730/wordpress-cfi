<?php
/**
 * Grimlock_Login_Control_Customizer Class
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
 * The Grimlock Login Control Customizer class.
 */
class Grimlock_Login_Control_Customizer extends Grimlock_Control_Customizer {
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
		.login form .input,
		.login input[type=\"text\"],
		.login form input[type=\"checkbox\"] {
			border-radius: {$this->get_theme_mod( 'control_border_radius' )}rem;
		    border-width: {$this->get_theme_mod( 'control_border_width' )}px;
		    border-color: {$this->get_theme_mod( 'control_border_color' )};
			background-color: {$this->get_theme_mod( 'control_background_color' )};
			color: {$this->get_theme_mod( 'control_color' )};
		}

		.login form .input:focus,
		.login input[type=\"text\"]:focus,
		.login form input[type=\"checkbox\"]:focus {
			border-color: {$this->get_theme_mod( 'control_focus_border_color' )};
			background-color: {$this->get_theme_mod( 'control_focus_background_color' )};
			color: {$this->get_theme_mod( 'control_focus_color' )};
		}";

		wp_add_inline_style( 'grimlock-login-login', $styles );
	}
}

return new Grimlock_Login_Control_Customizer();
