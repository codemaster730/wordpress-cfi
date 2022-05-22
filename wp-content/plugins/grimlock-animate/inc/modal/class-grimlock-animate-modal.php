<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Grimlock_Animate_Modal
 *
 * @author  Themosaurus
 * @since   1.0.3
 * @package grimlock-animate
 */
class Grimlock_Animate_Modal {
	/**
	 * Setup class.
	 *
	 * @since 1.0.3
	 */
	public function __construct() {
		require_once GRIMLOCK_ANIMATE_PLUGIN_DIR_PATH . 'inc/modal/component/class-grimlock-animate-modal-query-section-component.php';

		global $grimlock_animate;
		global $grimlock_modal;
		remove_action( 'grimlock_query_section', array( $grimlock_modal,   'query_section' ), 10    );
		remove_action( 'grimlock_query_section', array( $grimlock_animate, 'query_section' ), 10    );
		add_action(    'grimlock_query_section', array( $this,             'query_section' ), 10, 1 );
	}

	/**
	 * Display the query section component.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	public function query_section( $args = array() ) {
		$component = new Grimlock_Animate_Modal_Query_Section_Component( apply_filters( 'grimlock_query_section_args', (array) $args ) );
		$component->render();
	}
}