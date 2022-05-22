<?php
/**
 * Grimlock_BuddyPress_Youzer Class
 *
 * @author   Themosaurus
 * @since    1.3.0
 * @package  grimlock-buddypress
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Grimlock_BuddyPress_Youzer' ) ) :
	/**
	 * The main Grimlock_BuddyPress_Youzer class
	 */
	class Grimlock_BuddyPress_Youzer {
		/**
		 * Setup class.
		 *
		 * @since 1.3.0
		 */
		public function __construct() {
			add_action( 'bp_init',            array( $this, 'yzc_disable_youzer_template' ), 9   );
			add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts'          ), 20  );
			add_action( 'wp_enqueue_scripts', array( $this, 'dequeue_youzer_scripts'      ), 100 );

			require_once GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_PATH . 'inc/youzer/customizer/class-grimlock-buddypress-youzer-button-customizer.php';
			require_once GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_PATH . 'inc/youzer/customizer/class-grimlock-buddypress-youzer-archive-customizer.php';
			require_once GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_PATH . 'inc/youzer/customizer/class-grimlock-buddypress-youzer-control-customizer.php';
			require_once GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_PATH . 'inc/youzer/customizer/class-grimlock-buddypress-youzer-global-customizer.php';
			require_once GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_PATH . 'inc/youzer/customizer/class-grimlock-buddypress-youzer-navigation-customizer.php';
			require_once GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_PATH . 'inc/youzer/customizer/class-grimlock-buddypress-youzer-table-customizer.php';
			require_once GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_PATH . 'inc/youzer/customizer/class-grimlock-buddypress-youzer-typography-customizer.php';
			require_once GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_PATH . 'inc/youzer/customizer/class-grimlock-buddypress-youzer-customizer.php';
		}

		/**
		 * Disable Youzer template overload.
		 *
		 * @since 1.3.0
		 */
		public function yzc_disable_youzer_template() {
			if ( isset( $_SERVER['HTTP_HOST'] ) && isset( $_SERVER['REQUEST_URI'] ) ) {
				$prefix  = ( isset( $_SERVER['HTTPS'] ) && 'on' === $_SERVER['HTTPS'] ? 'https' : 'http' ) . '://';
				$page_id = url_to_postid( esc_url( $prefix . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ) );
				$page    = get_post( $page_id );

				if ( ! empty( $page->post_content ) && has_shortcode( $page->post_content, 'bps_directory' ) ) {
					remove_filter( 'template_include', 'youzer_template', 99999 );
					remove_action( 'bp_init', 'yz_bp_overload_templates' );
					remove_filter( 'template_include', 'yz_bbp_youzer_template', 999 );
					add_filter( 'yz_deregister_bp_styles', '__return_false' );
				}
			}
		}

		/**
		 * Dequeue Youzer scripts and stylesheets.
		 *
		 * @since 1.3.0
		 */
		public function dequeue_youzer_scripts() {
			$page = get_post( get_queried_object_id() );

			if ( has_shortcode( $page->post_content, 'bps_directory' ) ) {
				wp_dequeue_style( 'yz-headers' );
				wp_dequeue_style( 'yz-directories' );
			}
		}

		/**
		 * Enqueue scripts and stylesheets.
		 *
		 * @since 1.3.0
		 */
		public function wp_enqueue_scripts() {
			wp_enqueue_style( 'grimlock-buddypress-youzer-style', GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_URL . 'assets/css/youzer.css', array(), GRIMLOCK_BUDDYPRESS_VERSION );

			/*
			 * Load youzer-rtl.css instead of style.css for RTL compatibility
			 */
			wp_style_add_data( 'grimlock-buddypress-youzer-style', 'rtl', 'replace' );
		}
	}
endif;

return new Grimlock_BuddyPress_Youzer();
