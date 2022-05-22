<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Grimlock_Animate_Gallery
 *
 * @author  Themosaurus
 * @since   1.0.3
 * @package grimlock-animate
 */
class Grimlock_Animate_Gallery {
	/**
	 * Setup class.
	 *
	 * @since 1.0.3
	 */
	public function __construct() {
		require_once GRIMLOCK_ANIMATE_PLUGIN_DIR_PATH . 'inc/gallery/component/class-grimlock-animate-gallery-section-component.php';
		require_once GRIMLOCK_ANIMATE_PLUGIN_DIR_PATH . 'inc/gallery/widget/fields/class-grimlock-animate-gallery-section-widget-fields.php';
		require_once GRIMLOCK_ANIMATE_PLUGIN_DIR_PATH . 'inc/gallery/block/class-grimlock-animate-gallery-section-block.php';

		global $grimlock_gallery;
		remove_action( 'grimlock_gallery_section', array( $grimlock_gallery, 'gallery_section' ), 10    );
		add_action(    'grimlock_gallery_section', array( $this,             'gallery_section' ), 10, 1 );
	}

	/**
	 * Display the gallery section component.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args The array of arguments for the component.
	 */
	public function gallery_section( $args = array() ) {
		$component = new Grimlock_Animate_Gallery_Section_Component( apply_filters( 'grimlock_gallery_section_args', $args ) );
		$component->render();
	}
}