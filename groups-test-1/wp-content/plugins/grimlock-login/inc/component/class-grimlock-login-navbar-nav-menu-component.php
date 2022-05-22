<?php
/**
 * Grimlock_Login_Navbar_Nav_Menu_Component Class
 *
 * @author  Themosaurus
 * @since   1.0.3
 * @package  grimlock-login
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Grimlock_Login_Navbar_Nav_Menu_Component
 */
class Grimlock_Login_Navbar_Nav_Menu_Component extends Grimlock_Component {

	/**
	 * Grimlock_Login_Navbar_Nav_Menu_Component constructor.
	 *
	 * @param array $props
	 */
	public function __construct( $props = array() ) {
		parent::__construct( wp_parse_args( $props, array(
			'login_button_display_modal' => true,
		) ) );
	}

	/**
	 * Render the current component with props data on page.
	 *
	 * @since 1.0.0
	 */
	public function render() {
		if ( $this->is_displayed() ) {
			$this->props['class'] = $this->get_class();

			/**
			 * Hook: grimlock_login_navbar_nav_menu_template
			 *
			 * @see grimlock_login_navbar_nav_menu_login_register_buttons - 10
			 */
			do_action( 'grimlock_login_navbar_nav_menu_template', $this->props );
		}
	}
}
