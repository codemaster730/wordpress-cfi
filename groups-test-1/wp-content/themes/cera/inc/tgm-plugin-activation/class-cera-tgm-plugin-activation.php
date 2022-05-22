<?php
/**
 * Cera_TGM_Plugin_Activation Class
 *
 * @package  cera
 * @author   Themosaurus
 * @since    1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Cera_TGM_Plugin_Activation' ) ) :
	/**
	 * The Cera TGM Plugin Activation class
	 */
	class Cera_TGM_Plugin_Activation {
		/**
		 * Setup class.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			add_action( 'tgmpa_register', array( $this, 'register' ) );
		}

		/**
		 * Register the required plugins for this theme.
		 */
		public function register() {
			/*
			 * Array of plugin arrays. Required keys are name and slug.
			 * If the source is NOT from the .org repo, then source is also required.
			 */
			$plugins = apply_filters( 'cera_tgm_plugin_activation_register_plugins', array(

				/*
				 * Plugin from wordpress.org
				 */
				array(
					'name'     => 'Kirki Toolkit', // Name of the plugin.
					'slug'     => 'kirki', // Slug of plugin.
					'required' => true, // True to require, false to recommend.
				),

				/*
				 * Plugins from external source.
				 */
				array(
					'name'         => 'Grimlock',
					'slug'         => 'grimlock',
					'source'       => 'http://files.themosaurus.com/grimlock/grimlock.zip',
					'required'     => true,
					'external_url' => 'https://www.themosaurus.com/',
				),
				array(
					'name'         => 'Grimlock Animate',
					'slug'         => 'grimlock-animate',
					'source'       => 'http://files.themosaurus.com/grimlock-animate/grimlock-animate.zip',
					'required'     => false,
					'external_url' => 'https://www.themosaurus.com/',
				),
				array(
					'name'         => 'Grimlock Login',
					'slug'         => 'grimlock-login',
					'source'       => 'http://files.themosaurus.com/grimlock-login/grimlock-login.zip',
					'required'     => false,
					'external_url' => 'https://www.themosaurus.com/',
				),
				array(
					'name'         => 'Envato Market',
					'slug'         => 'envato-market',
					'source'       => 'https://envato.github.io/wp-envato-market/dist/envato-market.zip',
					'required'     => true,
					'external_url' => 'https://envato.com/market-plugin/',
				),
			) );

			/*
			 * Array of configuration settings.
			 */
			$config = apply_filters( 'cera_tgm_plugin_activation_register_config', array(
				'id'           => 'cera',                  // Unique ID for hashing notices for multiple instances of TGMPA.
				'menu'         => 'tgmpa-install-plugins', // Menu slug.
				'parent_slug'  => 'themes.php',            // Parent menu slug.
				'capability'   => 'edit_theme_options',    // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
				'has_notices'  => true,                    // Show admin notices or not.
				'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
				'is_automatic' => true,                    // Automatically activate plugins after installation or not.
			) );

			tgmpa( $plugins, $config );
		}
	}
endif;

return new Cera_TGM_Plugin_Activation();
