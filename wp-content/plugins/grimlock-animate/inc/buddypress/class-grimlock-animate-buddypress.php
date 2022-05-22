<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Grimlock_Animate_BuddyPress
 *
 * @author  Themosaurus
 * @since   1.0.3
 * @package grimlock-animate
 */
class Grimlock_Animate_BuddyPress {
	/**
	 * Setup class.
	 *
	 * @since 1.0.3
	 */
	public function __construct() {
		// Initialize components
		require_once GRIMLOCK_ANIMATE_PLUGIN_DIR_PATH . 'inc/buddypress/component/class-grimlock-animate-buddypress-groups-section-component.php';

		// Initialize widgets
		require_once GRIMLOCK_ANIMATE_PLUGIN_DIR_PATH . 'inc/buddypress/widget/fields/class-grimlock-animate-buddypress-groups-section-widget-fields.php';

		// Initialize blocks
		require_once GRIMLOCK_ANIMATE_PLUGIN_DIR_PATH . 'inc/buddypress/block/class-grimlock-animate-buddypress-groups-section-block.php';

		global $grimlock_buddypress;
		remove_action( 'grimlock_buddypress_groups_section', array( $grimlock_buddypress, 'groups_section' ), 10    );
		add_action(    'grimlock_buddypress_groups_section', array( $this,                'groups_section' ), 10, 1 );
	}

	/**
	 * Display the groups section component.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args The array of arguments for the component.
	 */
	public function groups_section( $args = array() ) {
		$component = new Grimlock_Animate_BuddyPress_Groups_Section_Component( apply_filters( 'grimlock_buddypress_groups_section_args', $args ) );
		$component->render();
	}
}