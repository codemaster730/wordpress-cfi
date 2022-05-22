<?php
/**
 * Cera_Grimlock_LearnDash_Customizer Class
 *
 * @author  Themosaurus
 * @since   1.0.0
 * @package grimlock
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The Cera Customizer class for BuddyPress.
 */
class Cera_Grimlock_LearnDash_Customizer extends Grimlock_LearnDash_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::__construct();
		add_filter( 'grimlock_learndash_customizer_defaults',  array( $this, 'change_defaults' ), 20, 1 );
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
		$defaults['learndash_layout']                          = CERA_LEARNDASH_LAYOUT;
		$defaults['learndash_container_layout']                = CERA_LEARNDASH_CONTAINER_LAYOUT;
		$defaults['learndash_custom_header_layout']            = CERA_LEARNDASH_CUSTOM_HEADER_LAYOUT;
		$defaults['learndash_custom_header_container_layout']  = CERA_LEARNDASH_CUSTOM_HEADER_CONTAINER_LAYOUT;
		return $defaults;
	}

}

return new Cera_Grimlock_LearnDash_Customizer();
