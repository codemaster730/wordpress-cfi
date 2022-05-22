<?php
/**
 * Cera_Grimlock_BuddyPress_BuddyPress_Docs_Archive_BP_Doc_Customizer Class
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
 * The Cera Customizer class for BuddyPress Docs Archive.
 */
class Cera_Grimlock_BuddyPress_BuddyPress_Docs_Archive_BP_Doc_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'grimlock_buddypress_buddypress_docs_archive_bp_doc_customizer_defaults', array( $this, 'change_defaults' ), 10, 1 );
	}

	/**
	 * Change default values and control settings for the Customizer.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $defaults The array of default values for the Customizer controls.
	 *
	 * @return array           The updated array of default values for the Customizer controls.
	 */
	public function change_defaults( $defaults ) {
		$defaults['archive_bp_doc_custom_header_container_layout'] = 'fluid';
		$defaults['archive_bp_doc_container_layout']               = 'fluid';

		return $defaults;
	}
}

return new Cera_Grimlock_BuddyPress_BuddyPress_Docs_Archive_BP_Doc_Customizer();
