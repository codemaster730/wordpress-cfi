<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Grimlock_Animate_Author_Avatars
 *
 * @author  Themosaurus
 * @since   1.0.3
 * @package grimlock-animate
 */
class Grimlock_Animate_Author_Avatars {
	/**
	 * Setup class.
	 *
	 * @since 1.0.3
	 */
	public function __construct() {
		// Initialize components
		require_once GRIMLOCK_ANIMATE_PLUGIN_DIR_PATH . 'inc/author-avatars/component/class-grimlock-animate-author-avatars-section-component.php';

		// Initialize widgets
		require_once GRIMLOCK_ANIMATE_PLUGIN_DIR_PATH . 'inc/author-avatars/widget/fields/class-grimlock-animate-author-avatars-section-widget-fields.php';

		// Initialize blocks
		require_once GRIMLOCK_ANIMATE_PLUGIN_DIR_PATH . 'inc/author-avatars/block/class-grimlock-animate-author-avatars-section-block.php';

		global $grimlock_author_avatars;
		remove_action( 'grimlock_author_avatars_section', array( $grimlock_author_avatars, 'section' ), 10    );
		add_action(    'grimlock_author_avatars_section', array( $this,                    'section' ), 10, 1 );
	}

	/**
	 * Display the author avatars section component.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args The array of arguments for the component.
	 */
	public function section( $args = array() ) {
		$component = new Grimlock_Animate_Author_Avatars_Section_Component( apply_filters( 'grimlock_author_avatars_section_args', $args ) );
		$component->render();
	}
}