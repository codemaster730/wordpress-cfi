<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class Grimlock_Loader_Component
 *
 * @author  themosaurus
 * @since   1.0.0
 * @package grimlock/inc/components
 */
class Grimlock_Loader_Component extends Grimlock_Component {
	/**
	 * Create a new Grimlock_Component instance.
	 *
	 * @since 1.0.0
	 *
	 * @param array $props Array of variables to be used within template.
	 */
	public function __construct( $props = array() ) {
		parent::__construct( wp_parse_args( $props, array(
			'id' => 'loader',
		) ) );
	}

	/**
	 * Render the current component with data.
	 *
	 * @since 1.0.0
	 */
	public function render() {
		if ( $this->is_displayed() ) : ?>
            <div class="grimlock-loader-wrapper">
                <span <?php $this->render_id(); ?> <?php $this->render_class(); ?>></span>
            </div><!-- .grimlock-loader-wrapper -->
		<?php endif;
	}
}
