<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Grimlock_Animate_Hero
 *
 * @author  Themosaurus
 * @since   1.0.3
 * @package grimlock-animate
 */
class Grimlock_Animate_Hero {
	/**
	 * Setup class.
	 *
	 * @since 1.0.3
	 */
	public function __construct() {
		require_once GRIMLOCK_ANIMATE_PLUGIN_DIR_PATH . 'inc/hero/customizer/class-grimlock-animate-hero-customizer.php';
		require_once GRIMLOCK_ANIMATE_PLUGIN_DIR_PATH . 'inc/hero/component/class-grimlock-animate-hero-component.php';

		global $grimlock_hero;
		remove_action( 'template_redirect', array( $grimlock_hero, 'change_custom_header' ), 10    );
		add_action(    'template_redirect', array( $this,          'change_custom_header' ), 10, 1 );
	}

	/**
	 * Change the Header by the Hero for the front page.
	 *
	 * @since 1.0.3
	 */
	public function change_custom_header() {
		if ( apply_filters( 'grimlock_hero_displayed', is_front_page() ) ) {
			global $grimlock_animate;
			remove_action( 'grimlock_custom_header', array( $grimlock_animate, 'custom_header' ), 10     );
			add_action(    'grimlock_custom_header', array( $this,             'custom_header' ), 10, 1 );
		}
	}

	/**
	 * Display the Hero section in front page instead of the Custom Header.
	 *
	 * @since 1.0.3
	 *
	 * @param array $args The array of arguments for the component.
	 */
	public function custom_header( $args ) {
		$args = apply_filters( 'grimlock_hero_args', wp_parse_args( $args, array(
			'id' => 'hero',
		) ) );
		$component = new Grimlock_Animate_Hero_Component( $args );
		$component->render();
	}
}