<?php
/**
 * Grimlock_Echo_Knowledge_Base_Single_EPKB_Post_Type_Customizer Class
 *
 * @author  Themosaurus
 * @since   1.0.0
 * @package grimlock-wp-job-manager
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The Grimlock Customizer class for the single job_listing.
 */
class Grimlock_Echo_Knowledge_Base_Single_EPKB_Post_Type_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'grimlock_custom_header_displayed', array( $this, 'has_custom_header_displayed' ), 20, 1 );
	}

	/**
	 * Check if the current template is the expected template.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True when the template is the expected template, false otherwise.
	 */
	protected function is_template() {
		$is_template = is_singular() && class_exists( 'EPKB_KB_Handler' ) && EPKB_KB_Handler::is_kb_post_type( get_post_type() );
		return apply_filters( 'grimlock_echo_knowledge_base_single_epkb_post_type_customizer_is_template', $is_template );
	}

	/**
	 * Hide custom header on knowledge base articles
	 *
	 * @param bool $default True if custom header should be displayed
	 *
	 * @return bool
	 */
	public function has_custom_header_displayed( $default ) {
		return $default && ! $this->is_template();
	}
}

return new Grimlock_Echo_Knowledge_Base_Single_EPKB_Post_Type_Customizer();
