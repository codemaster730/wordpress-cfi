<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class Grimlock_Back_To_Top_Button_Component
 *
 * @author  themosaurus
 * @since   1.0.0
 * @package grimlock/inc/components
 */
class Grimlock_Back_To_Top_Button_Component extends Grimlock_Component {
	/**
	 * Create a new Grimlock_Component instance.
	 *
	 * @param array $props Array of variables to be used within template.
	 */
	public function __construct( $props = array() ) {
	    parent::__construct( wp_parse_args( $props, array(
            'id'   => 'back_to_top_button',
		    'href' => '#site',
        ) ) );
	}

	/**
	 * Render the current component with props data on page.
	 *
	 * @since 1.0.0
	 */
	public function render() {
		if ( $this->is_displayed() ) : ?>
            <a <?php $this->render_id(); ?> <?php $this->render_class( array( 'btn-back-to-top' ) ); ?> href="<?php echo $this->props['href']; ?>"><i class="fa fa-angle-up fa-2x"></i></a>
			<?php
		endif;
	}
}
