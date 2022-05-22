<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Grimlock_Paid_Memberships_Pro
 *
 * @author  octopix
 * @since   1.0.0
 * @package grimlock-paid-memberships-pro/inc
 */
class Grimlock_Paid_Memberships_Pro {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		load_plugin_textdomain( 'grimlock-paid-memberships-pro', false, 'grimlock-paid-memberships-pro/languages' );

		add_filter( 'pmpro_pages_custom_template_path', array( $this, 'add_custom_template_path' ), 10, 5 );
		add_action( 'wp_enqueue_scripts',               array( $this, 'enqueue_scripts'          ), 10    );

		require_once GRIMLOCK_PAID_MEMBERSHIPS_PRO_PLUGIN_DIR_PATH . 'inc/customizer/class-grimlock-paid-memberships-pro-button-customizer.php';
		require_once GRIMLOCK_PAID_MEMBERSHIPS_PRO_PLUGIN_DIR_PATH . 'inc/customizer/class-grimlock-paid-memberships-pro-archive-customizer.php';
	}

	/**
	 * Allow overriding PMPRO templates from the "templates" directory of this plugin
	 *
	 * @param array  $default_templates Template hierarchy array
	 * @param string $page_name Name of the page/template
	 * @param string $type Type of template
	 * @param string $where `local` or `url` (whether to load from FS or over http)
	 * @param string $ext File extension ('php', 'html', 'htm', etc)
	 *
	 * @return mixed
	 */
	public function add_custom_template_path( $default_templates, $page_name, $type, $where, $ext ) {
		if ( $where == 'local' ) {
			array_splice( $default_templates, 1, 0, GRIMLOCK_PAID_MEMBERSHIPS_PRO_PLUGIN_DIR_PATH . "templates/{$type}/{$page_name}.{$ext}" );
		}
		elseif ( $where == 'url' ) {
			array_splice( $default_templates, 1, 0, GRIMLOCK_PAID_MEMBERSHIPS_PRO_PLUGIN_DIR_URL . "templates/{$type}/{$page_name}.{$ext}" );
		}

		return $default_templates;
	}

	/**
	 * Enqueue Grimlock for Paid Memberships Pro scripts
	 */
	public function enqueue_scripts() {
		wp_enqueue_style( 'grimlock-paid-memberships-pro', GRIMLOCK_PAID_MEMBERSHIPS_PRO_PLUGIN_DIR_URL . 'assets/css/style.css', array(), GRIMLOCK_PAID_MEMBERSHIPS_PRO_VERSION );

		/*
		 * Load style-rtl.css instead of style.css for RTL compatibility
		 */
		wp_style_add_data( 'grimlock-paid-memberships-pro', 'rtl', 'replace' );
	}

}
