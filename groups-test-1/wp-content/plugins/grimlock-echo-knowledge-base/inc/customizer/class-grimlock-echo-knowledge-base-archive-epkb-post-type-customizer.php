<?php
/**
 * Grimlock_Echo_Knowledge_Base_Archive_EPKB_Post_Type_Customizer Class
 *
 * @author  Themosaurus
 * @since   1.0.0
 * @package grimlock-wp-job-manager
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The Grimlock Customizer class for the job listing archive pages.
 */
class Grimlock_Echo_Knowledge_Base_Archive_EPKB_Post_Type_Customizer {

	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'grimlock_archive_customizer_is_template', array( $this, 'archive_customizer_is_template' ), 10,  1 );
	}

	/**
	 * Check if the current template is the expected template, the jobs page or a similar template.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True when the template is the expected template, false otherwise.
	 */
	protected function is_template() {
		$is_template = is_archive() && class_exists( 'EPKB_KB_Handler' ) && EPKB_KB_Handler::is_kb_post_type( get_post_type() );
		return apply_filters( 'grimlock_echo_knowledge_base_archive_epkb_post_type_customizer_is_template', $is_template );
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

return new Grimlock_Echo_Knowledge_Base_Archive_EPKB_Post_Type_Customizer();
