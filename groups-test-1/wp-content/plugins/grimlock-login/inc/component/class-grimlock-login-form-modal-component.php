<?php
/**
 * Grimlock_Login_Form_Modal_Component Class
 *
 * @author  Themosaurus
 * @since   1.0.3
 * @package  grimlock-login
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Grimlock_Login_Form_Modal_Component
 */
class Grimlock_Login_Form_Modal_Component extends Grimlock_Component {
	/**
	 * Render the current component with props data on page.
	 *
	 * @since 1.0.0
	 */
	public function render() {
		if ( $this->is_displayed() ) {
			/**
			 * Hook: grimlock_login_form_modal_template
			 *
			 * @hooked grimlock_login_form_modal - 10
			 */
			do_action( 'grimlock_login_form_modal_template', $this->props );
		}
	}
}
