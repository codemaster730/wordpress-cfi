<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Grimlock_Animate
 *
 * @author  Themosaurus
 * @since   1.0.0
 * @package grimlock-animate
 */
class Grimlock_Animate {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		load_plugin_textdomain( 'grimlock-animate', false, 'grimlock-animate/languages' );

		require_once GRIMLOCK_ANIMATE_PLUGIN_DIR_PATH . 'inc/customizer/class-grimlock-animate-custom-header-customizer.php';

		// Initialize components.
		require_once GRIMLOCK_ANIMATE_PLUGIN_DIR_PATH . 'inc/component/class-grimlock-animate-section-component.php';
		require_once GRIMLOCK_ANIMATE_PLUGIN_DIR_PATH . 'inc/component/class-grimlock-animate-query-section-component.php';
		require_once GRIMLOCK_ANIMATE_PLUGIN_DIR_PATH . 'inc/component/class-grimlock-animate-term-query-section-component.php';
		require_once GRIMLOCK_ANIMATE_PLUGIN_DIR_PATH . 'inc/component/class-grimlock-animate-nav-menu-section-component.php';
		require_once GRIMLOCK_ANIMATE_PLUGIN_DIR_PATH . 'inc/component/class-grimlock-animate-custom-header-component.php';

		global $grimlock;
		remove_action( 'grimlock_section',       array( $grimlock, 'section'       ), 10    );
		add_action(    'grimlock_section',       array( $this,     'section'       ), 10, 1 );

		remove_action( 'grimlock_query_section', array( $grimlock, 'query_section' ), 10    );
		add_action(    'grimlock_query_section', array( $this,     'query_section' ), 10, 1 );

		remove_action( 'grimlock_term_query_section', array( $grimlock, 'term_query_section' ), 10    );
		add_action(    'grimlock_term_query_section', array( $this,     'term_query_section' ), 10, 1 );

		remove_action( 'grimlock_nav_menu_section', array( $grimlock, 'nav_menu_section' ), 10    );
		add_action(    'grimlock_nav_menu_section', array( $this,     'nav_menu_section' ), 10, 1 );

		remove_action( 'grimlock_custom_header', array( $grimlock, 'custom_header' ), 10    );
		add_action(    'grimlock_custom_header', array( $this,     'custom_header' ), 10, 1 );

		// Initialize widgets.
		require_once GRIMLOCK_ANIMATE_PLUGIN_DIR_PATH . 'inc/widget/fields/class-grimlock-animate-section-widget-fields.php';
		require_once GRIMLOCK_ANIMATE_PLUGIN_DIR_PATH . 'inc/widget/fields/class-grimlock-animate-query-section-widget-fields.php';
		require_once GRIMLOCK_ANIMATE_PLUGIN_DIR_PATH . 'inc/widget/fields/class-grimlock-animate-posts-section-widget-fields.php';
		require_once GRIMLOCK_ANIMATE_PLUGIN_DIR_PATH . 'inc/widget/fields/class-grimlock-animate-term-query-section-widget-fields.php';
		require_once GRIMLOCK_ANIMATE_PLUGIN_DIR_PATH . 'inc/widget/fields/class-grimlock-animate-nav-menu-section-widget-fields.php';

		// Initialize blocks.
		require_once GRIMLOCK_ANIMATE_PLUGIN_DIR_PATH . 'inc/block/class-grimlock-animate-section-block.php';
		require_once GRIMLOCK_ANIMATE_PLUGIN_DIR_PATH . 'inc/block/class-grimlock-animate-query-section-block.php';
		require_once GRIMLOCK_ANIMATE_PLUGIN_DIR_PATH . 'inc/block/class-grimlock-animate-posts-section-block.php';
		require_once GRIMLOCK_ANIMATE_PLUGIN_DIR_PATH . 'inc/block/class-grimlock-animate-term-query-section-block.php';
		require_once GRIMLOCK_ANIMATE_PLUGIN_DIR_PATH . 'inc/block/class-grimlock-animate-nav-menu-section-block.php';

		add_action( 'wp_enqueue_scripts',          array( $this, 'enqueue_scripts' ), 10 );
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_scripts' ), 10 );
	}

	/**
	 * Display the section component.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args The array of arguments for the component.
	 */
	public function section( $args = array() ) {
		$component = new Grimlock_Animate_Section_Component( apply_filters( 'grimlock_section_args', $args ) );
		$component->render();
	}

	/**
	 * Display the query section component.
	 *
	 * @since 1.1.0
	 *
	 * @param array $args The array of arguments for the component.
	 */
	public function query_section( $args = array() ) {
		$component = new Grimlock_Animate_Query_Section_Component( apply_filters( 'grimlock_query_section_args', $args ) );
		$component->render();
	}

	/**
	 * Display the term query section component.
	 *
	 * @since 1.1.0
	 *
	 * @param array $args The array of arguments for the component.
	 */
	public function term_query_section( $args = array() ) {
		$component = new Grimlock_Animate_Term_Query_Section_Component( apply_filters( 'grimlock_term_query_section_args', $args ) );
		$component->render();
	}

	/**
	 * Display the nav menu section component.
	 *
	 * @since 1.1.0
	 *
	 * @param array $args The array of arguments for the component.
	 */
	public function nav_menu_section( $args = array() ) {
		$component = new Grimlock_Animate_Nav_Menu_Section_Component( apply_filters( 'grimlock_nav_menu_section_args', $args ) );
		$component->render();
	}

	/**
	 * Display the custom header component.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	public function custom_header( $args = array() ) {
		global $grimlock;
		$args = apply_filters( 'grimlock_custom_header_args', wp_parse_args( $args, array(
			'id'               => 'custom_header',
			'displayed'        => $grimlock->has_custom_header_displayed(),
			'background_image' => get_header_image(),
		) ) );
		$component = new Grimlock_Animate_Custom_Header_Component( $args );
		$component->render();
	}

	/**
	 * Enqueue scripts and stylesheets.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_style( 'grimlock-animate', GRIMLOCK_ANIMATE_PLUGIN_DIR_URL . 'assets/css/style.css', array(), GRIMLOCK_ANIMATE_VERSION );
		/*
		 * Load style-rtl.css instead of style.css for RTL compatibility
		 */
		wp_style_add_data( 'grimlock-animate', 'rtl', 'replace' );

		if ( ! wp_is_mobile() ) {
			wp_enqueue_script( 'parallax.js', GRIMLOCK_ANIMATE_PLUGIN_DIR_URL . 'assets/js/vendor/jquery.parallax.min.js', array( 'jquery' ), '2.0.0-alpha', true );
		}
		wp_enqueue_script( 'scrollreveal', GRIMLOCK_ANIMATE_PLUGIN_DIR_URL . 'assets/js/vendor/scrollreveal.js', array(), '3.4.0', true );
		wp_enqueue_script( 'grimlock-animate', GRIMLOCK_ANIMATE_PLUGIN_DIR_URL . 'assets/js/main.js', array( 'scrollreveal', 'jquery' ), GRIMLOCK_ANIMATE_VERSION, true );
	}
}
