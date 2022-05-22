<?php
/**
 * Grimlock_BuddyPress_Youzify_Navigation_Customizer Class
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
 * The navigation class for the Customizer.
 */
class Grimlock_BuddyPress_Youzify_Navigation_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'grimlock_navigation_customizer_sub_menu_item_background_color_outputs', array( $this, 'add_sub_menu_item_background_color_outputs' ), 10, 1 );
		add_filter( 'grimlock_navigation_customizer_sub_menu_item_color_outputs',            array( $this, 'add_sub_menu_item_color_outputs'            ), 10, 1 );
	}

	/**
	 * Add selectors and properties to the CSS rule-set for the sub-menu item background color.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $outputs The array of CSS selectors and properties for the sub-menu item background color.
	 *
	 * @return array          The updated array of CSS selectors for the sub-menu item background color.
	 */
	public function add_sub_menu_item_background_color_outputs( $outputs ) {
		return array_merge( $outputs, array(
			array(
				'element'  => implode( ',', array(
					'.nice-select .list',
				) ),
				'property' => 'background-color',
				'suffix'   => '!important',
			),
		) );
	}

	/**
	 * Add selectors and properties to the CSS rule-set for the sub-menu item color.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $outputs The array of CSS selectors and properties for the sub-menu item color.
	 *
	 * @return array          The updated array of CSS selectors for the sub-menu item color.
	 */
	public function add_sub_menu_item_color_outputs( $outputs ) {
		return array_merge( $outputs, array(
			array(
				'element'  => implode( ',', array(
					'.nice-select .list',
				) ),
				'property' => 'color',
				'suffix'   => '!important',
			),
		) );
	}
}

return new Grimlock_BuddyPress_Youzify_Navigation_Customizer();
