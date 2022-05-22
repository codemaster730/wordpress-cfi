<?php
/**
 * Grimlock_BuddyPress_BuddyPress_Docs Class
 *
 * @package  grimlock-buddypress
 * @author   Themosaurus
 * @since    1.1.4
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The BP Docs integration class
 */
class Grimlock_BuddyPress_BuddyPress_Docs{
	/**
	 * Setup class.
	 */
	public function __construct() {
		add_filter( 'bp_docs_locate_template', array( $this, 'locate_template' ), 10, 2 );

		require_once GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_PATH . 'inc/buddypress-docs/customizer/class-grimlock-buddypress-buddypress-docs-archive-bp-doc-customizer.php';
		require_once GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_PATH . 'inc/buddypress-docs/customizer/class-grimlock-buddypress-buddypress-docs-single-bp-doc-customizer.php';
	}

	/**
	 * Try to locate BuddyPress Docs templates in Grimlock BuddyPress
	 *
	 * @param string $template_path The template path
	 * @param string $template The template file name
	 *
	 * @return string The new template path
	 */
	public function locate_template( $template_path, $template ) {
		if ( file_exists( get_stylesheet_directory() . '/docs/' . $template ) ) {
			$template_path = get_stylesheet_directory() . '/docs/' . $template;
		}
		elseif ( file_exists( get_template_directory() . '/docs/' . $template ) ) {
			$template_path = get_template_directory() . '/docs/' . $template;
		}
		elseif ( file_exists( GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_PATH . 'templates/docs/' . $template ) ) {
			$template_path = GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_PATH . 'templates/docs/' . $template;
		}
		else {
			$template_path = BP_DOCS_INCLUDES_PATH . 'templates/docs/' . $template;
		}

		return $template_path;
	}
}

return new Grimlock_BuddyPress_BuddyPress_Docs();
