<?php
/**
 * Grimlock_Jetpack_Button_Customizer Class
 *
 * @author   Themosaurus
 * @since    1.0.5
 * @package grimlock
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The background image class for the Customizer.
 */
class Grimlock_Jetpack_Button_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.5
	 */
	public function __construct() {
		add_filter( 'grimlock_button_customizer_elements',         array( $this, 'add_elements'         ), 10, 1 );
		add_filter( 'grimlock_button_customizer_primary_elements', array( $this, 'add_primary_elements' ), 10, 1 );
	}

	/**
	 * Add CSS selectors to the array of CSS selectors for the button.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $elements The array of CSS selectors for the button.
	 *
	 * @return array           The updated array of CSS selectors for the button.
	 */
	public function add_elements( $elements ) {
		return array_merge( $elements, array(
			'div#infinite-handle span',
			'#jp-carousel-comment-form-button-submit',
			'.jp-carousel-light #carousel-reblog-box input#carousel-reblog-submit',
		) );
	}

	/**
	 * Add CSS selectors to the array of CSS selectors for the primary button.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $elements The array of CSS selectors for the primary button.
	 *
	 * @return array           The updated array of CSS selectors for the primary button.
	 */
	public function add_primary_elements( $elements ) {
		return array_merge( $elements, array(
			'div#infinite-handle span',
			'#jp-carousel-comment-form-button-submit',
			'.jp-carousel-light #carousel-reblog-box input#carousel-reblog-submit',
		) );
	}
}

return new Grimlock_Jetpack_Button_Customizer();
