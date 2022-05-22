<?php
/**
 * Grimlock_Paid_Memberships_Pro_Archive_Customizer Class
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
 * The post archive page class for the Customizer.
 */
class Grimlock_Paid_Memberships_Pro_Archive_Customizer {

	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'grimlock_archive_customizer_elements', array( $this, 'add_elements' ), 10, 1 );
	}

	/**
	 * Add CSS selectors from the array of CSS selectors for the archive post.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $elements The array of CSS selectors for the archive post.
	 *
	 * @return array           The updated array of CSS selectors for the archive post.
	 */
	public function add_elements( $elements ) {
		return array_merge( $elements, array(
			'#pmpro_account > .pmpro_box',
			'#pmpro_cancel',
			'.pmpro-invoice #main .entry-content',
			'.pmpro-confirmation #main .entry-content',
			'#pmpro_form .pmpro_checkout-fields',
			'.pmpro-body-level-required .entry-content .pmpro_content_message',
		) );
	}
}

return new Grimlock_Paid_Memberships_Pro_Archive_Customizer();
