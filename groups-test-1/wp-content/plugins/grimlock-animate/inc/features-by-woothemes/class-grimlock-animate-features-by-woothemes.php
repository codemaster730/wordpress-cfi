<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Grimlock_Animate_Features_By_WooThemes
 *
 * @author  Themosaurus
 * @since   1.0.3
 * @package grimlock-animate
 */
class Grimlock_Animate_Features_By_WooThemes {
	/**
	 * Setup class.
	 *
	 * @since 1.0.3
	 */
	public function __construct() {
		require_once GRIMLOCK_ANIMATE_PLUGIN_DIR_PATH . 'inc/features-by-woothemes/widget/fields/class-grimlock-animate-features-by-woothemes-features-section-widget-fields.php';
	}
}