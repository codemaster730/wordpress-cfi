<?php
/**
 * Grimlock_BuddyPress_BuddyPress_Docs_Single_BP_Docs_Customizer Class
 *
 * @author  Themosaurus
 * @since   1.0.0
 * @package grimlock-buddypress
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The Grimlock Customizer class for the docs single pages.
 */
class Grimlock_BuddyPress_BuddyPress_Docs_Single_BP_Docs_Customizer {

	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'grimlock_single_customizer_is_template', array( $this, 'single_customizer_is_template' ), 10,  1 );
	}

	/**
	 * Check if the current template is the expected template.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True when the template is the expected template, false otherwise.
	 */
	protected function is_template() {
		$is_template = is_singular( 'bp_doc' );
		return apply_filters( 'grimlock_buddypress_buddypress_docs_single_bp_doc_customizer_is_template', $is_template );
	}

	/**
	 * Disinherit single customizer settings
	 *
	 * @param bool $default True if we are on a default single page
	 *
	 * @return bool
	 */
	public function single_customizer_is_template( $default ) {
		return $default && ! $this->is_template();
	}
}

return new Grimlock_BuddyPress_BuddyPress_Docs_Single_BP_Docs_Customizer();
