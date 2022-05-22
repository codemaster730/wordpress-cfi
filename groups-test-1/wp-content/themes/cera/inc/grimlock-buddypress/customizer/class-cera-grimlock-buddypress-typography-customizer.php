<?php
/**
 * Cera_Grimlock_BuddyPress_Typography_Customizer Class
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
class Cera_Grimlock_BuddyPress_Typography_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'grimlock_typography_customizer_heading_color_outputs', array( $this, 'add_heading_color_outputs' ), 10, 1 );
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
					'.widget:not(.widget-content) div.item-options a',
				) ),
				'property' => 'color',
			),
			array(
				'element'  => implode( ',', array(
					'.widget:not(.widget-content) div.item-options a',
				) ),
				'property' => 'border-color',
			),
			array(
				'element'  => implode( ',', array(
					'.widget:not(.widget-content) ul.item-list > li div.item .item-title a:not(:hover)',
				) ),
				'property' => 'color',
				'suffix'   => '!important',
			),
		) );
	}
}

return new Cera_Grimlock_BuddyPress_Typography_Customizer();
