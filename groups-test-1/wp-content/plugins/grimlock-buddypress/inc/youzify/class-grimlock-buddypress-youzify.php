<?php
/**
 * Grimlock_BuddyPress_Youzify Class
 *
 * @author   Themosaurus
 * @since    1.3.0
 * @package  grimlock-buddypress
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Grimlock_BuddyPress_Youzify' ) ) :
	/**
	 * The main Grimlock_BuddyPress_Youzify class
	 */
	class Grimlock_BuddyPress_Youzify {
		/**
		 * Setup class.
		 *
		 * @since 1.3.0
		 */
		public function __construct() {
			add_action( 'bp_init',            array( $this, 'yzc_disable_youzify_template' ), 9   );
			add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts'          ), 20  );
			add_action( 'wp_enqueue_scripts', array( $this, 'dequeue_youzify_scripts'      ), 100 );

			require_once GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_PATH . 'inc/youzify/customizer/class-grimlock-buddypress-youzify-button-customizer.php';
			require_once GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_PATH . 'inc/youzify/customizer/class-grimlock-buddypress-youzify-archive-customizer.php';
			require_once GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_PATH . 'inc/youzify/customizer/class-grimlock-buddypress-youzify-control-customizer.php';
			require_once GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_PATH . 'inc/youzify/customizer/class-grimlock-buddypress-youzify-global-customizer.php';
			require_once GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_PATH . 'inc/youzify/customizer/class-grimlock-buddypress-youzify-navigation-customizer.php';
			require_once GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_PATH . 'inc/youzify/customizer/class-grimlock-buddypress-youzify-table-customizer.php';
			require_once GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_PATH . 'inc/youzify/customizer/class-grimlock-buddypress-youzify-typography-customizer.php';
			require_once GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_PATH . 'inc/youzify/customizer/class-grimlock-buddypress-youzify-customizer.php';
		}

		/**
		 * Disable Youzify template overload.
		 *
		 * @since 1.3.0
		 */
		public function yzc_disable_youzify_template() {
			if ( isset( $_SERVER['HTTP_HOST'] ) && isset( $_SERVER['REQUEST_URI'] ) ) {
				$prefix  = ( isset( $_SERVER['HTTPS'] ) && 'on' === $_SERVER['HTTPS'] ? 'https' : 'http' ) . '://';
				$page_id = url_to_postid( esc_url( $prefix . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ) );
				$page    = get_post( $page_id );

				if ( ! empty( $page->post_content ) && has_shortcode( $page->post_content, 'bps_directory' ) ) {
					remove_filter( 'template_include', 'youzify_template', 99999 );
					remove_action( 'bp_init', 'youzify_bp_overload_templates' );
					remove_filter( 'template_include', 'youzify_bbp_youzify_template', 999 );
					add_filter( 'youzify_deregister_bp_styles', '__return_false' );
				}
			}
		}

		/**
		 * Dequeue Youzify scripts and stylesheets.
		 *
		 * @since 1.3.0
		 */
		public function dequeue_youzify_scripts() {
			$page = get_post( get_queried_object_id() );

			if ( has_shortcode( $page->post_content, 'bps_directory' ) ) {
				wp_dequeue_style( 'youzify-headers' );
				wp_dequeue_style( 'youzify-directories' );
			}
		}

		/**
		 * Enqueue scripts and stylesheets.
		 *
		 * @since 1.3.0
		 */
		public function wp_enqueue_scripts() {
			wp_enqueue_style( 'grimlock-buddypress-youzify-style', GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_URL . 'assets/css/youzify.css', array(), GRIMLOCK_BUDDYPRESS_VERSION );

			/*
			 * Load youzify-rtl.css instead of style.css for RTL compatibility
			 */
			wp_style_add_data( 'grimlock-buddypress-youzify-style', 'rtl', 'replace' );
		}
	}
endif;

return new Grimlock_BuddyPress_Youzify();
