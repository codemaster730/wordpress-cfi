<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class Grimlock_Page_Component
 *
 * @author  themosaurus
 * @since   1.0.0
 * @package grimlock/inc/components
 */
class Grimlock_Page_Component extends Grimlock_Component {
	/**
	 * Create a new Grimlock_Component instance.
	 *
	 * @param array $props Array of variables to be used within template.
	 */
	public function __construct( $props = array() ) {
		parent::__construct( wp_parse_args( $props, array(
			'post_thumbnail_displayed' => true,
		) ) );
	}

	/**
	 * Render the current component with props data on page.
	 *
	 * @since 1.0.0
	 */
	public function render() {
		if ( $this->is_displayed() ) {
			/**
			 * Hook: grimlock_page_template
			 *
			 * @hooked grimlock_singular_thumbnail - 10
			 * @hooked grimlock_page_header        - 20
			 * @hooked grimlock_page_content       - 30
			 * @hooked grimlock_page_footer        - 40
			 */
			do_action( 'grimlock_page_template', $this->props );
		}
	}
}
