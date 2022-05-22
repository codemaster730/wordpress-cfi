<?php
/**
 * Cera_Grimlock_Page_Customizer Class
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
 * The single page class for the Customizer.
 */
class Cera_Grimlock_Page_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'grimlock_page_customizer_defaults',                  array( $this, 'change_defaults'               ), 10, 1 );
		add_filter( 'grimlock_page_customizer_content_padding_y_outputs', array( $this, 'add_content_padding_y_outputs' ), 10, 1 );
	}

	/**
	 * Change default values and control settings for the Customizer.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $defaults The array of default values for the Customizer controls.
	 *
	 * @return array           The updated array of default values for the Customizer controls.
	 */
	public function change_defaults( $defaults ) {
		return array_merge( $defaults, array(
			'page_custom_header_displayed'        => false,

			'page_custom_header_layout'           => '12-cols-center',
			'page_custom_header_container_layout' => 'fluid',

			'page_sidebar_mobile_displayed'       => true,

			'page_custom_header_padding_y'        => CERA_HEADER_PADDING_Y,
			'page_content_padding_y'              => CERA_CONTENT_PADDING_Y,
		) );
	}

	/**
	 * Add selectors and properties to the CSS rule-set for the padding page content.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $outputs The array of CSS selectors and properties for the primary button color.
	 *
	 * @return array          The updated array of CSS selectors for the primary button color.
	 */
	public function add_content_padding_y_outputs( $outputs ) {
		return array_merge( $outputs, array(
			array(
				'element'  => implode( ',', array(
					'.tribe_community_edit .site-content',
					'.tribe_community_list .site-content',
					'.tribe_community_edit .site-content',
				) ),
				'property' => 'padding-top',
				'units'    => '%',
			),
			array(
				'element'  => implode( ',', array(
					'.tribe_community_edit .site-content',
					'.tribe_community_list .site-content',
					'.tribe_community_edit .site-content',
				) ),
				'property' => 'padding-bottom',
				'units'    => '%',
			),
		) );
	}
}

return new Cera_Grimlock_Page_Customizer();
