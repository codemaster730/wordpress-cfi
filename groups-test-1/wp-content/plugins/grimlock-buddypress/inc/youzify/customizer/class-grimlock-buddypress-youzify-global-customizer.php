<?php
/**
 * Grimlock_BuddyPress_Youzify_Global_Customizer Class
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
 * The background image class for the Customizer.
 */
class Grimlock_BuddyPress_Youzify_Global_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'grimlock_global_customizer_content_background_color_outputs', array( $this, 'add_content_background_color_outputs' ), 10, 1 );
	}

	/**
	 * Add selectors and properties to the CSS rule-set for the content background color.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $outputs The array of CSS selectors and properties for the content background color.
	 *
	 * @return array          The updated array of CSS selectors for the content background color.
	 */
	public function add_content_background_color_outputs( $outputs ) {
		return array_merge( $outputs, array(
			array(
				'element'  => implode( ',', array(
					'#youzify-members-directory .bps_filters',

				) ),
				'property' => 'color',
			),
			array(
				'element'  => implode( ',', array(
					'.youzify-account-settings-menu .youzify-account-menu',

				) ),
				'property' => 'border-bottom-color',
			),
		) );
	}
}

return new Grimlock_BuddyPress_Youzify_Global_Customizer();
