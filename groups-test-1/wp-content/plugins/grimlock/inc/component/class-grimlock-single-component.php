<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class Grimlock_Single_Component
 *
 * @author  themosaurus
 * @since   1.0.0
 * @package grimlock/inc/components
 */
class Grimlock_Single_Component extends Grimlock_Component {
	/**
	 * Create a new Grimlock_Component instance.
	 *
	 * @param array $props Array of variables to be used within template.
	 */
	public function __construct( $props = array() ) {
		parent::__construct( wp_parse_args( $props, array(
			'post_thumbnail_displayed'        => true,
			'post_date_displayed'             => true,
			'post_author_displayed'           => true,
			'post_author_biography_displayed' => true,
			'category_displayed'              => true,
			'post_tag_displayed'              => true,
			'post_format_displayed'           => true,
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
			 * Hook: grimlock_single_template
			 *
			 * @hooked grimlock_single_thumbnail - 10
			 * @hooked grimlock_single_header    - 20
			 * @hooked grimlock_single_content   - 30
			 * @hooked grimlock_single_footer    - 40
			 */
			do_action( 'grimlock_single_template', $this->props );
		}
	}
}