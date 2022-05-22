<?php
/**
 * Cera_Child Class
 *
 * @author  Themosaurus
 * @since   1.0.0
 * @package  cera
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The main Cera_Child class
 */
class Cera_Child {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ), 10 );
		add_action( 'after_setup_theme',  array( $this, 'setup'              ), 20 );
	}

	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 *
	 * @since 1.0.0
	 */
	public function setup() {
		/*
		 * Make child theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on cera-child, use a find and replace
		 * to change 'cera-child' to the name of your theme in all the template files.
		 */
		load_child_theme_textdomain( 'cera', get_stylesheet_directory() . '/languages' );
	}

	/**
	 * Enqueue scripts and stylesheets.
	 *
	 * @since 1.0.0
	 */
	public function wp_enqueue_scripts() {
		/**
		 * Enqueue styles.
		 */
		wp_enqueue_style( 'cera-style', get_template_directory_uri() . '/style.css', array(), CERA_CHILD_VERSION );
		wp_enqueue_style( 'cera-child-style', get_stylesheet_uri(), array( 'cera-style' ), CERA_CHILD_VERSION );

		/**
		 * Enqueue scripts.
		 */
		wp_enqueue_script( 'cera-child', get_stylesheet_directory_uri() . '/assets/js/main.js', array( 'jquery' ), CERA_CHILD_VERSION, true );
	}
}

return new Cera_Child();
