<?php
/**
 * Grimlock_bbPress_Button_Customizer Class
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
 * The Grimlock bbPress Customizer style class.
 */
class Grimlock_bbPress_Button_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'grimlock_button_customizer_elements',                          array( $this, 'add_elements'                          ), 10, 1 );
		add_filter( 'grimlock_button_customizer_primary_elements',                  array( $this, 'add_primary_elements'                  ), 10, 1 );
		add_filter( 'grimlock_button_customizer_secondary_elements',                array( $this, 'add_secondary_elements'                ), 10, 1 );
	}

	/**
	 * @param $elements
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function add_elements( $elements ) {
		return array_merge( $elements, array(
			'#bbpress-forums > #subscription-toggle a',
			'.bbp-logged-in .logout-link',
		) );
	}

	/**
	 * @param $elements
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function add_primary_elements( $elements ) {
		return array_merge( $elements, array(
			'#bbpress-forums > #subscription-toggle a',
			'body:not([class*="yz-"]) #bbpress-forums > #subscription-toggle a',
		) );
	}

	/**
	 * @param $elements
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function add_secondary_elements( $elements ) {
		return array_merge( $elements, array(
			'.bbp-logged-in .logout-link',
		) );
	}
}

return new Grimlock_bbPress_Button_Customizer();
