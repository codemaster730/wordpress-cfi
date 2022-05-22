<?php
/**
 * Grimlock_Login_Table_Customizer Class
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
 * The Grimlock Login Table Customizer class.
 */
class Grimlock_Login_Table_Customizer extends Grimlock_Table_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::__construct();
		add_action( 'login_enqueue_scripts', array( $this, 'enqueue_styles' ), 10    );
	}

	/**
	 * Enqueue custom styles based on theme mods.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_styles() {
		$styles    = "

		.login #nav a:hover,
		.login #nav a:active,
		.login #nav a:focus {
			background-color: {$this->get_theme_mod( 'table_striped_background_color' )};
		}

		";
		wp_add_inline_style( 'grimlock-login-login', $styles );
	}
}

return new Grimlock_Login_Table_Customizer();
