<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class Grimlock_Isotope
 *
 * @author  themoasaurus
 * @since   1.0.0
 * @package grimlock-isotope/inc
 */
class Grimlock_Isotope {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		load_plugin_textdomain( 'grimlock-isotope', false, 'grimlock-isotope/languages' );

		require_once GRIMLOCK_ISOTOPE_PLUGIN_DIR_PATH . 'inc/component/class-grimlock-isotope-terms-component.php';

		global $grimlock;
		remove_action( 'grimlock_terms',  array( $grimlock, 'terms'          ), 10    );
		add_action(    'grimlock_terms',  array( $this,     'terms'          ), 10, 1 );

		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ), 20    );

		require_once GRIMLOCK_ISOTOPE_PLUGIN_DIR_PATH . 'inc/customizer/class-grimlock-isotope-button-customizer.php';
	}

	/**
	 * Display the list of taxonomy terms.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	public function terms( $args = array() ) {
		$term_nav = new Grimlock_Isotope_Terms_Component( apply_filters( 'grimlock_term_args', $args ) );
		$term_nav->render();
	}

	/**
	 * Enqueue scripts and stylesheets.
	 *
	 * @since 1.0.0
	 */
	public function wp_enqueue_scripts() {
		if ( apply_filters( 'grimlock_isotope_js_enqueued', is_home() ) ) {
			wp_dequeue_script( 'grimlock-grid' );
			wp_enqueue_script( 'jquery-match-height', GRIMLOCK_ISOTOPE_PLUGIN_DIR_URL . 'assets/js/vendor/jquery.matchHeight-min.js', array( 'jquery' ), '0.7.2', true );
			wp_enqueue_script( 'isotope', GRIMLOCK_ISOTOPE_PLUGIN_DIR_URL . 'assets/js/vendor/isotope.pkgd.min.js', array( 'jquery', 'jquery-masonry' ), '3.0.6', true );
			wp_enqueue_script( 'grimlock-isotope', GRIMLOCK_ISOTOPE_PLUGIN_DIR_URL . 'assets/js/main.js', array( 'jquery-match-height', 'imagesloaded', 'isotope' ), GRIMLOCK_ISOTOPE_VERSION, true );

			// Improve Jetpack Infinite Scroll integration.
			if ( wp_script_is( 'grimlock-jetpack-infinite-scroll', 'enqueued' ) ) {
				wp_dequeue_script( 'grimlock-jetpack-infinite-scroll' );
				wp_enqueue_script( 'grimlock-isotope-jetpack-infinite-scroll', GRIMLOCK_ISOTOPE_PLUGIN_DIR_URL . 'assets/js/jetpack/infinite-scroll.js', array( 'grimlock-isotope' ), GRIMLOCK_ISOTOPE_VERSION, true );
			}
		}
	}
}