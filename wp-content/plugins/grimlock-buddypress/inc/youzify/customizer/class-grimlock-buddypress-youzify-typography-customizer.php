<?php
/**
 * Grimlock_BuddyPress_Youzify_Typography_Customizer Class
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
 * The typography class for the Customizer.
 */
class Grimlock_BuddyPress_Youzify_Typography_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'grimlock_typography_customizer_text_color_outputs',    array( $this, 'add_text_color_outputs'    ), 10, 1 );
		add_filter( 'grimlock_typography_customizer_heading_color_outputs', array( $this, 'add_heading_color_outputs' ), 10, 1 );
		add_filter( 'grimlock_typography_customizer_text_font_outputs',     array( $this, 'add_text_font_outputs'     ), 10, 1 );
		add_filter( 'grimlock_typography_customizer_heading_font_outputs',  array( $this, 'add_heading_font_outputs'  ), 10, 1 );
	}

	/**
	 * Add selectors and properties to the CSS rule-set for the heading color.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $outputs The array of CSS selectors and properties for the heading color.
	 *
	 * @return array          The updated array of CSS selectors for the heading color.
	 */
	public function add_heading_color_outputs( $outputs ) {
		return array_merge( $outputs, array(
			array(
				'element'  => implode( ',', array(
					'.youzify-hdr-v7 .youzify-name h2',
					'.youzify-hdr-v7 .youzify-snumber',
				) ),
				'property' => 'color',
			),
		) );
	}

	/**
	 * Add selectors and properties to the CSS rule-set for the text color.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $outputs The array of CSS selectors and properties for the text color.
	 *
	 * @return array          The updated array of CSS selectors for the text color.
	 */
	public function add_text_color_outputs( $outputs ) {
		return array_merge( $outputs, array(
			array(
				'element'  => implode( ',', array(
					'#youzify-members-directory .bps_filters',

				) ),
				'property' => 'background-color',
			),
		) );
	}

	/**
	 * Add selectors and properties to the CSS rule-set for the text font.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $outputs The array of CSS selectors and properties for the text font.
	 *
	 * @return array          The updated array of CSS selectors for the text font.
	 */
	public function add_text_font_outputs( $outputs ) {
		return array_merge( $outputs, array(
			array(
				'element'  => implode( ',', array(
					'#buddypress.youzify',
					'body .youzify',
					'body .youzify button',
					'body .youzify h3',
					'body .youzify input',
				) ),
				'property' => 'font-family',
				'choice'   => 'font-family',
			),
		) );
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
			'.youzify-widget .youzify-widget-title',
			'.youzify-hdr-v7 .youzify-name h2',
			'.youzify-hdr-v7 .youzify-snumber',
			'#youzify-members-list .youzify-fullname',
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

return new Grimlock_BuddyPress_Youzify_Typography_Customizer();
