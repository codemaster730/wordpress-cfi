<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class Grimlock_Term_Component
 *
 * @author  themosaurus
 * @since   1.0.0
 * @package grimlock/inc/components
 */
class Grimlock_Term_Component extends Grimlock_Component {
	/**
	 * Create a new Grimlock_Component instance.
	 *
	 * @param array $props Array of variables to be used within template.
	 */
	public function __construct( $props = array() ) {
		parent::__construct( wp_parse_args( $props, array(
			'term_thumbnail_displayed' => true,
			'term_thumbnail_size'      => 'medium',
			'term_thumbnail_attr'      => array( 'class' => 'card-img' ),
			'count_displayed'          => false,
			'description_displayed'    => true,
			'more_link_displayed'      => false,
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
			 * Hook: grimlock_term_template
			 *
			 * @hooked grimlock_term_template - 10
			 */
			do_action( 'grimlock_term_template', $this->props );
		}
	}
}