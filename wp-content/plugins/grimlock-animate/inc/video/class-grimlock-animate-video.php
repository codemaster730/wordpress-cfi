<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Grimlock_Animate_Video
 *
 * @author  Themosaurus
 * @since   1.0.3
 * @package grimlock-animate
 */
class Grimlock_Animate_Video {
	/**
	 * Setup class.
	 *
	 * @since 1.0.3
	 */
	public function __construct() {
		require_once GRIMLOCK_ANIMATE_PLUGIN_DIR_PATH . 'inc/video/component/class-grimlock-animate-video-section-component.php';

		global $grimlock_video;
		global $grimlock_animate;
		remove_action( 'grimlock_section', array( $grimlock_video,   'section' ), 10    );
		remove_action( 'grimlock_section', array( $grimlock_animate, 'section' ), 10    );
		add_action(    'grimlock_section', array( $this,             'section' ), 10, 1 );
	}

	/**
	 * Display the query section component.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	public function section( $args = array() ) {
		$component = new Grimlock_Animate_Video_Section_Component( apply_filters( 'grimlock_section_args', (array) $args ) );
		$component->render();
	}
}