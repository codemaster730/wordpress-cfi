<?php
// Exit if accessed directly

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Grimlock_Echo_Knowledge_Base
 *
 * @author  themosaurus
 * @since   1.0.0
 * @package grimlock-echo-knowledge-base/inc
 */
class Grimlock_Echo_Knowledge_Base {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		load_plugin_textdomain( 'grimlock-echo-knowledge-base', false, 'grimlock-echo-knowledge-base/languages' );

		require_once GRIMLOCK_ECHO_KNOWLEDGE_BASE_PLUGIN_DIR_PATH . 'inc/customizer/class-grimlock-echo-knowledge-base-archive-epkb-post-type-customizer.php';
		require_once GRIMLOCK_ECHO_KNOWLEDGE_BASE_PLUGIN_DIR_PATH . 'inc/customizer/class-grimlock-echo-knowledge-base-single-epkb-post-type-customizer.php';

		add_action( 'wp_enqueue_scripts',                 array( $this, 'enqueue_scripts'                    ), 10 );
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'customize_controls_enqueue_scripts' ), 20 );
	}

	/**
	 * Enqueue Grimlock Echo Knowledge Base scripts
	 */
	public function enqueue_scripts() {
		wp_enqueue_style( 'grimlock-echo-knowledge-base', GRIMLOCK_ECHO_KNOWLEDGE_BASE_PLUGIN_DIR_URL . 'assets/css/style.css', array(), GRIMLOCK_ECHO_KNOWLEDGE_BASE_VERSION );

		/*
		 * Load style-rtl.css instead of style.css for RTL compatibility
		 */
		wp_style_add_data( 'grimlock-echo-knowledge-base', 'rtl', 'replace' );
	}

	/**
	 * Enqueue script for customizer controls.
	 */
	public function customize_controls_enqueue_scripts() {
		wp_enqueue_script( 'grimlock-echo-knowledge-base-customizer-controls', GRIMLOCK_ECHO_KNOWLEDGE_BASE_PLUGIN_DIR_URL . 'assets/js/customizer-controls.js', array( 'grimlock-customizer-controls' ), GRIMLOCK_ECHO_KNOWLEDGE_BASE_VERSION, true );
	}
}
