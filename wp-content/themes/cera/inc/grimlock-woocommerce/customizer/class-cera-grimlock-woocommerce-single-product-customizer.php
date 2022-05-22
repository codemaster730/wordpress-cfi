<?php
/**
 * Cera_Grimlock_WooCommerce_Single_Product_Customizer Class
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
 * The post single page class for the Customizer.
 */
class Cera_Grimlock_WooCommerce_Single_Product_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'grimlock_woocommerce_single_product_customizer_defaults', array( $this, 'add_defaults' ), 10, 1 );
	}

	/**
	 * Add default values and control settings for the Customizer.
	 *
	 * @since 1.0.0
	 *
	 * @param array $defaults The array of default values for the Customizer controls.
	 *
	 * @return array          The updated array of default values for the Customizer controls.
	 */
	public function add_defaults( $defaults ) {

		$defaults['single_product_container_layout']  = CERA_SINGLE_PRODUCT_CONTAINER_LAYOUT;
		$defaults['single_product_content_padding_y'] = CERA_CONTENT_PADDING_Y;

		return $defaults;
	}
}

return new Cera_Grimlock_WooCommerce_Single_Product_Customizer();
