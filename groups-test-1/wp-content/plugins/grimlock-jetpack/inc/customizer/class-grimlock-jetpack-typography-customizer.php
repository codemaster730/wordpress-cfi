<?php
/**
 * Grimlock_Jetpack_Typography_Customizer Class
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
 * The typography class for the Customizer.
 */
class Grimlock_Jetpack_Typography_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.5
	 */
	public function __construct() {
		add_filter( 'grimlock_typography_customizer_text_color_outputs', array( $this, 'add_text_color_outputs' ), 10, 1 );
	}

	/**
	 * Add selectors and properties to the CSS rule-set for the text color.
	 *
	 * @since 1.0.5
	 *
	 * @param  array $outputs The array of CSS selectors and properties for the text color.
	 *
	 * @return array          The updated array of CSS selectors for the text color.
	 */
	public function add_text_color_outputs( $outputs ) {
		return array_merge( $outputs, array(
			array(
				'element'  => implode( ',', array(
					'#infinite-handle span',
				) ),
				'property' => 'background-color',
			),
		) );
	}
}

return new Grimlock_Jetpack_Typography_Customizer();
