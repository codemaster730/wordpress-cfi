<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class Grimlock_Jetpack_Jetpack_Testimonial_Component
 *
 * @author  themosaurus
 * @since   1.0.0
 * @package grimlock/inc/components
 */
class Grimlock_Jetpack_Jetpack_Testimonial_Component extends Grimlock_Component {
	/**
	 * Create a new Grimlock_Component instance.
	 *
	 * @param array $props Array of variables to be used within template.
	 */
	public function __construct( $props = array() ) {
		parent::__construct( wp_parse_args( $props, array(
			'post_thumbnail_displayed'  => true,
			'post_thumbnail_size'       => 'thumbnail',
			'post_thumbnail_attr'       => array( 'class' => 'card-img' ),
			'post_content_displayed'    => true,
			'post_excerpt_displayed'    => false,
		) ) );
	}

	/**
	 * Render the current component with props data on page.
	 *
	 * @since 1.0.0
	 */
	public function render() {
		/**
		 * Hook: grimlock_jetpack_jetpack_testimonial_template
		 *
		 * @hooked grimlock_jetpack_jetpack_testimonial_template - 10
		 */
		do_action( 'grimlock_jetpack_jetpack_testimonial_template', $this->props );
	}
}
