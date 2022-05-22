<?php
/**
 * Grimlock_Login_Navigation_Customizer Class
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
 * The Grimlock Login Navigation Customizer class.
 */
class Grimlock_Login_Navigation_Customizer extends Grimlock_Navigation_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::__construct();
		add_filter( 'grimlock_login_customizer_defaults', array( $this, 'change_login_customizer_defaults' ), 5 );
	}

	/**
	 * Change login customizer defaults
	 *
	 * @since 1.0.0
	 *
	 * @param array $defaults The default values to modify
	 *
	 * @return array The modified defaults
	 */
	public function change_login_customizer_defaults( $defaults ) {
		$defaults['login_custom_logo_background_color'] = $this->get_theme_mod( 'navigation_background_color' );

		return $defaults;
	}
}

return new Grimlock_Login_Navigation_Customizer();
