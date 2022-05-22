<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class Grimlock_Navigation_Component
 *
 * @author  themosaurus
 * @since   1.0.0
 * @package grimlock/inc/components
 */
class Grimlock_Navigation_Component extends Grimlock_Component {
	/**
	 * Render the current component with props data on page.
	 *
	 * @since 1.0.0
	 */
	public function render() {
		if ( $this->is_displayed() ) {
			if ( isset( $this->props['layout'] ) ) {
				switch ( $this->props['layout'] ) {
					case '' :
					case 'classic-left' :
					case 'classic-center' :
					case 'classic-right' :
					case 'bottom-left' :
					case 'bottom-center' :
					case 'bottom-right' :
					case 'fat-left' :
					case 'fat-center' :
						do_action( 'grimlock_navbar', $this->props );
						break;

					case 'hamburger-right' :
					case 'hamburger-left' :
					case 'fixed-right' :
					case 'fixed-left' :
						do_action( 'grimlock_hamburger_navbar', $this->props );
						break;
				}
			}
		}
	}
}
