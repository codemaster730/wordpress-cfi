<?php
/**
 * Grimlock_bbPress_Typography_Customizer Class
 *
 * @author   Themosaurus
 * @since    1.0.0
 * @package grimlock
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The post typography class for the Customizer.
 */
class Grimlock_bbPress_Typography_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'grimlock_typography_customizer_heading_font_outputs', array( $this, 'add_heading_font_outputs' ), 10, 1 );
	}

	/**
	 * Add selectors and properties to the CSS rule-set for the heading font.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $outputs The array of CSS selectors and properties for the heading font.
	 *
	 * @return array          The updated array of CSS selectors for the heading font.
	 */
	public function add_heading_font_outputs( $outputs ) {
		$elements_headings = array(
			'body:not([class*="yz-"]) #bbpress-forums fieldset.bbp-form legend',
		);

		return array_merge( $outputs, array(
			array(
				'element'  => implode( ',', $elements_headings ),
				'property' => 'font-family',
				'choice'   => 'font-family',
			),
			array(
				'element'  => implode( ',', $elements_headings ),
				'property' => 'text-transform',
				'choice'   => 'text-transform',
			),
			array(
				'element'  => implode( ',', $elements_headings ),
				'property' => 'font-weight',
				'choice'   => 'font-weight',
			),
			array(
				'element'  => implode( ',', $elements_headings ),
				'property' => 'font-style',
				'choice'   => 'font-style',
			),
		) );
	}

}

return new Grimlock_bbPress_Typography_Customizer();
