<?php
/**
 * Cera_Grimlock_BuddyPress_Button_Customizer Class
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
 * The button class for the Customizer.
 */
class Cera_Grimlock_BuddyPress_Button_Customizer {

	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'grimlock_button_customizer_primary_elements',                  array( $this, 'remove_primary_elements'                  ), 10, 1 );
		add_filter( 'grimlock_button_customizer_primary_background_color_elements', array( $this, 'add_primary_background_color_elements'    ), 10, 1 );
		add_filter( 'grimlock_button_customizer_primary_background_color_elements', array( $this, 'remove_primary_background_color_elements' ), 10, 1 );
		add_filter( 'grimlock_button_customizer_primary_background_color_outputs',  array( $this, 'add_primary_background_color_outputs'     ), 10, 1 );

	}

	/**
	 * Remove CSS selectors to the array of CSS selectors for the primary button.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $elements The array of CSS selectors for the primary button.
	 *
	 * @return array           The updated array of CSS selectors for the primary button.
	 */
	public function remove_primary_elements( $elements ) {
		$elements[] = ".fake-cera-selector";
		return array_diff( $elements, array(
			'body:not([class*="yz-"]) .widget #groups-list-options a:focus',
			'body:not([class*="yz-"]) .widget #groups-list-options a:active',
			'body:not([class*="yz-"]) .widget #groups-list-options a.selected',
			'body:not([class*="yz-"]) .widget #groups-list-options a.loading',
			'body:not([class*="yz-"]) .widget #members-list-options a:focus',
			'body:not([class*="yz-"]) .widget #members-list-options a:active',
			'body:not([class*="yz-"]) .widget #members-list-options a.selected',
			'body:not([class*="yz-"]) .widget #members-list-options a.loading',
			'body:not([class*="yz-"]) .widget #friends-list-options a:focus',
			'body:not([class*="yz-"]) .widget #friends-list-options a:active',
			'body:not([class*="yz-"]) .widget #friends-list-options a.selected',
			'body:not([class*="yz-"]) .widget #friends-list-options a.loading',
			'.widget:not(.widget-content) #groups-list-options a:focus',
			'.widget:not(.widget-content) #groups-list-options a:active',
			'.widget:not(.widget-content) #groups-list-options a.selected',
			'.widget:not(.widget-content) #groups-list-options a.loading',
			'.widget:not(.widget-content) #members-list-options a:focus',
			'.widget:not(.widget-content) #members-list-options a:active',
			'.widget:not(.widget-content) #members-list-options a.selected',
			'.widget:not(.widget-content) #members-list-options a.loading',
			'.widget:not(.widget-content) #friends-list-options a:focus',
			'.widget:not(.widget-content) #friends-list-options a:active',
			'.widget:not(.widget-content) #friends-list-options a.selected',
			'.widget:not(.widget-content) #friends-list-options a.loading',
		) );
	}


	/**
	 * Add CSS selectors to the array of CSS selectors for the primary button background color.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $elements The array of CSS selectors for the primary button background color.
	 *
	 * @return array           The updated array of CSS selectors for the primary button background color.
	 */
	public function add_primary_background_color_elements( $elements ) {
		return array_merge( $elements, array(
			'body:not([class*="yz-"]) .widget div.item-options a:focus:before',
			'body:not([class*="yz-"]) .widget div.item-options a:active:before',
			'body:not([class*="yz-"]) .widget div.item-options a.selected:before',
			'body:not([class*="yz-"]) .widget div.item-options a:hover:before',
		) );
	}

	/**
	 * Remove CSS selectors from the array of CSS selectors for the primary button background color.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $elements The array of CSS selectors for the primary button background color.
	 *
	 * @return array           The updated array of CSS selectors for the primary button background color.
	 */
	public function remove_primary_background_color_elements( $elements ) {
		$elements[] = ".fake-cera-selector";
		return array_diff( $elements, array(
			'.widget:not(.widget-content) #groups-list-options a.loading:before',
			'.widget:not(.widget-content) #members-list-options a.loading:before',
			'.widget:not(.widget-content) #friends-list-options a.loading:before',
		) );
	}

	/**
	 * Add selectors and properties to the CSS rule-set for the primary button background color.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $outputs The array of CSS selectors and properties for the primary button background color.
	 *
	 * @return array          The updated array of CSS selectors for the primary button background color.
	 */
	public function add_primary_background_color_outputs( $outputs ) {
		foreach ( $outputs as $key => $output ) {
			if ( isset( $output['property'] ) && $output['property'] === 'border-color' ) {
				$elements = explode( ',', $output['element'] );
				$elements[] = ".fake-cera-selector";
				$elements = array_diff( $elements, array(
					'body:not([class*="yz-"]) .widget #friends-list-options a.selected',
					'body:not([class*="yz-"]) .widget #members-list-options a.selected',
					'body:not([class*="yz-"]) .widget #groups-list-options a.selected',
					'.widget:not(.widget-content) #groups-list-options a:hover',
					'.widget:not(.widget-content) #members-list-options a:hover',
					'.widget:not(.widget-content) #friends-list-options a:hover',
					'body:not([class*="yz-"]) .widget #groups-list-options a:hover',
					'body:not([class*="yz-"]) .widget #members-list-options a:hover',
					'body:not([class*="yz-"]) .widget #friends-list-options a:hover',
				) );
				$outputs[ $key ]['element'] = implode( ',', $elements );
			}

			if ( isset( $output['property'] ) && $output['property'] === 'color' ) {
				$elements = explode( ',', $output['element'] );
				$elements[] = ".fake-cera-selector";
				$elements = array_diff( $elements, array(
					'div.widget:not(.widget-content) div.item-options a:hover',
					'body:not([class*="yz-"]) .widget #groups-list-options a:hover',
					'body:not([class*="yz-"]) .widget #members-list-options a:hover',
					'body:not([class*="yz-"]) .widget #friends-list-options a:hover',
					'.widget:not(.widget-content) #groups-list-options a:hover',
					'.widget:not(.widget-content) #members-list-options a:hover',
					'.widget:not(.widget-content) #friends-list-options a:hover',
				) );
				$outputs[ $key ]['element'] = implode( ',', $elements );
			}
		}

		return array_merge( $outputs, array(
			array(
				'element'  => implode( ',', array(
					'body:not([class*="yz-"]) .widget div.item-options a:hover',
				) ),
				'property' => 'border-color',
			),
			array(
				'element'  => implode( ',', array(
					'body:not([class*="yz-"]) .widget div.item-options a:hover',
					'body:not([class*="yz-"]) .widget div.item-options a:focus',
					'body:not([class*="yz-"]) .widget div.item-options a:active',
					'body:not([class*="yz-"]) .widget div.item-options a.selected',
				) ),
				'property' => 'color',
				'suffix' => '!important',
			),
		) );
	}

	/**
	 * Add selectors and properties to the CSS rule-set for the button font.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $outputs The array of CSS selectors and properties for the button font.
	 *
	 * @return array          The updated array of CSS selectors for the button font.
	 */
	public function add_font_outputs( $outputs ) {
		foreach ( $outputs as $key => $output ) {
			if ( isset( $output['property'] ) && $output['property'] === 'font-weight' ) {
				$elements = explode( ',', $output['element'] );
				$elements[] = ".fake-cera-selector";
				$elements = array_diff( $elements, array(
					'body:not([class*="yz-"]) .widget #groups-list-options a',
					'body:not([class*="yz-"]) .widget #members-list-options a',
					'body:not([class*="yz-"]) .widget #friends-list-options a',
				) );
				$outputs[ $key ]['element'] = implode( ',', $elements );
			}

			if ( isset( $output['property'] ) && $output['property'] === 'text-transform' ) {
				$elements = explode( ',', $output['element'] );
				$elements[] = ".fake-cera-selector";
				$elements = array_diff( $elements, array(
					'.widget:not(.widget-content) #groups-list-options a',
					'.widget:not(.widget-content) #members-list-options a',
					'.widget:not(.widget-content) #friends-list-options a',
				) );
				$outputs[ $key ]['element'] = implode( ',', $elements );
			}
		}
	}
}

return new Cera_Grimlock_BuddyPress_Button_Customizer();
