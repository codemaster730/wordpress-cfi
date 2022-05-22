<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Grimlock_WordPress_SEO
 *
 * @author  themosaurus
 * @since   1.0.0
 * @package grimlock-wordpress-seo
 */
class Grimlock_WordPress_SEO {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		load_plugin_textdomain( 'grimlock-wordpress-seo', false, 'grimlock-wordpress-seo/languages' );

		require_once GRIMLOCK_WORDPRESS_SEO_PLUGIN_DIR_PATH . 'inc/grimlock-wordpress-seo-template-functions.php';
		require_once GRIMLOCK_WORDPRESS_SEO_PLUGIN_DIR_PATH . 'inc/grimlock-wordpress-seo-template-hooks.php';

		require_once GRIMLOCK_WORDPRESS_SEO_PLUGIN_DIR_PATH . 'inc/component/class-grimlock-wordpress-seo-breadcrumb-component.php';
		require_once GRIMLOCK_WORDPRESS_SEO_PLUGIN_DIR_PATH . 'inc/customizer/class-grimlock-wordpress-seo-breadcrumb-customizer.php';

		global $grimlock;
		remove_action( 'grimlock_breadcrumb', array( $grimlock, 'breadcrumb' ), 10    );
		add_action(    'grimlock_breadcrumb', array( $this,     'breadcrumb' ), 10, 1 );
	}

	/**
	 * Display the breadcrumb component.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	public function breadcrumb( $args = array() ) {
		$breadcrumb = new Grimlock_WordPress_SEO_Breadcrumb_Component( apply_filters( 'grimlock_breadcrumb_args', (array) $args ) );
		$breadcrumb->render();
	}
}