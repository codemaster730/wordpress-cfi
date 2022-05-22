<?php
/**
 * Grimlock_Isotope_Button_Customizer Class
 *
 * @author  Themosaurus
 * @since   1.0.0
 * @package grimlock
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The Grimlock Isotope Customizer style class.
 */
class Grimlock_Isotope_Button_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'grimlock_button_customizer_card_primary_elements', array( $this, 'add_card_primary_elements' ), 10, 1 );
	}

	/**
	 * @param $elements
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function add_card_primary_elements( $elements ) {
		return array_merge( $elements, array(
			'.posts-filters .posts-filter a.nav-link.control.active',
			'.posts-filters .posts-filter a.nav-link.control.active:hover',
			'.posts-filters .posts-filter a.nav-link.control.active:active',
			'.posts-filters .posts-filter a.nav-link.control.active:focus',
		) );
	}
}

return new Grimlock_Isotope_Button_Customizer();
