<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class BP_Member_Swipe_Directory_Shortcode
 *
 * @author  themosaurus
 * @since   1.0.0
 * @package bp-member-swipe/inc
 */
class BP_Member_Swipe_Directory_Shortcode {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_shortcode( 'bms_directory', array( $this, 'bms_directory' ) );
	}

	/**
	 * Add a [bms_directory] shortcode.
	 *
	 * @param $atts
	 * @param $content
	 *
	 * @return string
	 */
	public function bms_directory( $atts, $content ) {
		// Enqueue vendor scripts for shortcode
		wp_enqueue_script( 'swiper' );

		// Enqueue directory script
		wp_enqueue_script( apply_filters( 'bp_member_swipe_directory_shortcode_js_handle', 'bp-member-swipe-directory-swiper' ) );

		// Get shortcode output
		ob_start();
		bp_get_template_part( 'members/index-swipe' );
		return ob_get_clean();
	}
}

return new BP_Member_Swipe_Directory_Shortcode();
