<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class Grimlock_WordPress_SEO_Breadcrumb_Component
 *
 * @author  themosaurus
 * @since   1.0.0
 * @package grimlock/inc/components
 */
class Grimlock_WordPress_SEO_Breadcrumb_Component extends Grimlock_Component {
	/**
	 * Render the current component with props data on page.
	 *
	 * @since 1.0.0
	 */
	public function render() {
		if ( $this->is_displayed() && function_exists( 'yoast_breadcrumb' ) ) :
            yoast_breadcrumb( '<div class="breadcrumb yoast-breadcrumb">', '</div>' );
        endif;
	}
}
