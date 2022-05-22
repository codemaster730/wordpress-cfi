<?php
/**
 * Cera_Grimlock_Loader_Customizer Class
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
 * The loader class for the Customizer.
 */
class Cera_Grimlock_Loader_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'grimlock_loader_customizer_defaults', array( $this, 'change_defaults' ), 10, 1 );
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
		return array_merge( $defaults, array(
			'loader_fadein_displayed'          => true,
			'loader_fadein_animation_duration' => 500,
			'loader_displayed'                 => false,
			'loader_animation_duration'        => 1500,
			'loader_background_color'          => CERA_LOADER_BACKGROUND_COLOR,
			'loader_color'                     => CERA_LOADER_COLOR,
		) );
	}
}

return new Cera_Grimlock_Loader_Customizer();
