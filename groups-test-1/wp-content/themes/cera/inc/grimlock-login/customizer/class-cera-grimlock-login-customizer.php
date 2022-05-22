<?php
/**
 * Cera_Grimlock_Login_Customizer Class
 *
 * @author  Themosaurus
 * @since   1.0.3
 * @package grimlock
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The Grimlock Customizer class for the single product.
 */
class Cera_Grimlock_Login_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.3
	 */
	public function __construct() {
		add_filter( 'grimlock_login_customizer_defaults', array( $this, 'change_defaults' ), 20, 1 );
	}

	/**
	 * Add arguments using theme mods to display login and register buttons in the navbar and login form modal when clicking the login button
	 *
	 * @since 1.0.0
	 *
	 * @param array $defaults The default arguments to render the component
	 *
	 * @return array      The arguments to render the component.
	 */
	public function change_defaults( $defaults ) {
		$defaults['navbar_nav_menu_login_register_buttons_displayed'] = true;
		$defaults['login_layout'] = 'fullscreen-left';
		$defaults['login_background_image'] = get_stylesheet_directory_uri() . '/assets/images/pages/page-404.jpg';
		return $defaults;
	}

}

return new Cera_Grimlock_Login_Customizer();
