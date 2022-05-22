<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Grimlock_Animate_WooCommerce_Subscriptions
 *
 * @author  Themosaurus
 * @since   1.0.3
 * @package grimlock-animate
 */
class Grimlock_Animate_WooCommerce_Subscriptions {
	/**
	 * Setup class.
	 *
	 * @since 1.0.3
	 */
	public function __construct() {
		require_once GRIMLOCK_ANIMATE_PLUGIN_DIR_PATH . 'inc/woocommerce-subscriptions/widget/fields/class-grimlock-animate-woocommerce-subscriptions-products-section-widget-fields.php';
	}
}