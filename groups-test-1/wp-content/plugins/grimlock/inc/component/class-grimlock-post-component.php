<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class Grimlock_Post_Component
 *
 * @author  themosaurus
 * @since   1.0.0
 * @package grimlock/inc/components
 */
class Grimlock_Post_Component extends Grimlock_Component {
	/**
	 * Create a new Grimlock_Component instance.
	 *
	 * @param array $props Array of variables to be used within template.
	 */
	public function __construct( $props = array() ) {
		parent::__construct( wp_parse_args( $props, array(
			'post_thumbnail_displayed' => true,
			'post_thumbnail_size'      => 'medium',
			'post_thumbnail_attr'      => array( 'class' => 'card-img' ),
			'post_date_displayed'      => true,
			'post_author_displayed'    => true,
			'post_content_displayed'   => false,
			'post_excerpt_displayed'   => true,
			'post_more_link_displayed' => true,
			'category_displayed'       => true,
			'post_tag_displayed'       => true,
			'post_format_displayed'    => true,
			'comments_link_displayed'  => true,
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
			 * Hook: grimlock_post_template
			 *
			 * @hooked grimlock_post_template - 10
			 */
			do_action( 'grimlock_post_template', $this->props );
		}
	}
}