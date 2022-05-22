<?php
/**
 * Grimlock_Paid_Memberships_Pro_Button_Customizer Class
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
class Grimlock_Paid_Memberships_Pro_Button_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'grimlock_button_customizer_elements',                          array( $this, 'add_elements'                         ), 10, 1 );
		add_filter( 'grimlock_button_customizer_primary_elements',                  array( $this, 'add_primary_elements'                 ), 10, 1 );
		add_filter( 'grimlock_button_customizer_primary_background_color_outputs',  array( $this, 'add_primary_background_color_outputs' ), 10, 1 );
		add_filter( 'grimlock_button_customizer_secondary_elements',                array( $this, 'add_secondary_elements'               ), 10, 1 );
		add_filter( 'grimlock_button_customizer_sm_elements',                       array( $this, 'add_sm_elements'                      ), 10, 1 );
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
			'#nav-below a',
			'.pmpro_box > .pmpro_actionlinks > a',
			'#pmpro_cancel > .pmpro_actionlinks > a',
			'.entry-content a.pmpro_a-print',
			'.pmpro-body-level-required .entry-content .pmpro_content_message a',
			'#site a.pmpro_btn',
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
			'#nav-below a',
			'.pmpro_box > .pmpro_actionlinks > a',
			'.entry-content a.pmpro_a-print',
			'.pmpro-body-level-required .entry-content .pmpro_content_message a',
			'#site a.pmpro_btn',
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
		return array_merge( $outputs, array(
			array(
				'element'  => implode( ',', array(
					'#pmpro_levels_table .level-short-price strong',
					'#pmpro_levels_table .level-description ol li:before',
					'#pmpro_levels_table .level-description ul li:before',
				) ),
				'property' => 'color',
			),
			array(
				'element'  => implode( ',', array(
					'#pmpro_levels_table .card:hover',
				) ),
				'property'     => 'box-shadow',
				'value_pattern' => '0 0 0 3px $',
			),
		) );
	}


	/**
	 * Add CSS selectors to the array of CSS selectors for the secondary button.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $elements The array of CSS selectors for the secondary button.
	 *
	 * @return array           The updated array of CSS selectors for the secondary button.
	 */
	public function add_secondary_elements( $elements ) {
		return array_merge( $elements, array(
			'input[type="button"].pmpro_btn.pmpro_btn-cancel',
		) );
	}

	/**
	 * Add CSS selectors to the array of CSS selectors for the small button.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $elements The array of CSS selectors for the small button.
	 *
	 * @return array           The updated array of CSS selectors for the small button.
	 */
	public function add_sm_elements( $elements ) {
		return array_merge( $elements, array(
			'.pmpro_box > .pmpro_actionlinks > a',
			'#pmpro_cancel > .pmpro_actionlinks > a',
		) );
	}
}

return new Grimlock_Paid_Memberships_Pro_Button_Customizer();
