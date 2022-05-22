<?php
/**
 * Grimlock_BuddyPress_BuddyPress_Docs_Archive_BP_Docs_Customizer Class
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
 * The Grimlock Customizer class for the docs archive pages.
 */
class Grimlock_BuddyPress_BuddyPress_Docs_Archive_BP_Docs_Customizer {

	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'grimlock_archive_customizer_is_template', array( $this, 'archive_customizer_is_template' ), 10,  1 );
	}

	/**
	 * Check if the current template is the expected template, the docs page or a similar template.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True when the template is the expected template, false otherwise.
	 */
	protected function is_template() {
		$is_template = is_post_type_archive( 'bp_doc' ) || is_tax( 'bp_docs_tag' );
		return apply_filters( 'grimlock_buddypress_buddypress_docs_archive_bp_doc_customizer_is_template', $is_template );
	}

	/**
	 * Disinherit archive customizer settings
	 *
	 * @param bool $default True if we are on a default archive page
	 *
	 * @return bool
	 */
	public function archive_customizer_is_template( $default ) {
		return $default && ! $this->is_template();
	}
}

return new Grimlock_BuddyPress_BuddyPress_Docs_Archive_BP_Docs_Customizer();
