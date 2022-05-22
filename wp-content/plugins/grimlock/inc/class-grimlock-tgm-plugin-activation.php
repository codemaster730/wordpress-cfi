<?php
/**
 * Grimlock_TGM_Plugin_Activation Class
 *
 * @package  grimlock
 * @author   Themosaurus
 * @since    1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Grimlock_TGM_Plugin_Activation' ) ) :
	/**
	 * The Grimlock TGM Plugin Activation class
	 */
	class Grimlock_TGM_Plugin_Activation {
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
			$plugins = array(
				/*
				 * Plugin from wordpress.org
				 */
				array(
					'name'     => 'Kirki Toolkit', // Name of the plugin.
					'slug'     => 'kirki', // Slug of plugin.
					'required' => true, // True to require, false to recommend.
				),
			);

			// Compatibility with Author Avatars
			if ( class_exists( 'AuthorAvatars' ) ) {
				$plugins[] = array(
					'name'         => 'Grimlock for Author Avatars List',
					'slug'         => 'grimlock-author-avatars',
					'source'       => 'http://files.themosaurus.com/grimlock-author-avatars/grimlock-author-avatars.zip',
					'required'     => false,
					'external_url' => 'https://www.themosaurus.com/',
				);
			}

			// Compatibility with Autocomplete for Relevanssi
			if ( function_exists( 'afr_load_textdomain' ) ) {
				$plugins[] = array(
					'name'         => 'Grimlock for Autocomplete for Relevanssi',
					'slug'         => 'grimlock-autocomplete-for-relevanssi',
					'source'       => 'http://files.themosaurus.com/grimlock-autocomplete-for-relevanssi/grimlock-autocomplete-for-relevanssi.zip',
					'required'     => false,
					'external_url' => 'https://www.themosaurus.com/',
				);
			}

			// Compatibility with bbPress
			if ( function_exists( 'bbpress' ) ) {
				$plugins[] = array(
					'name'         => 'Grimlock for bbPress',
					'slug'         => 'grimlock-bbpress',
					'source'       => 'http://files.themosaurus.com/grimlock-bbpress/grimlock-bbpress.zip',
					'required'     => false,
					'external_url' => 'https://www.themosaurus.com/',
				);
			}

			// Compatibility with BuddyPress
			if ( function_exists( 'buddypress' ) ) {
				$plugins[] = array(
					'name'         => 'Grimlock for BuddyPress',
					'slug'         => 'grimlock-buddypress',
					'source'       => 'http://files.themosaurus.com/grimlock-buddypress/grimlock-buddypress.zip',
					'required'     => false,
					'external_url' => 'https://www.themosaurus.com/',
				);
			}

			// Compatibility with Charitable
			if ( class_exists( 'Charitable' ) ) {
				$plugins[] = array(
					'name'         => 'Grimlock for Charitable',
					'slug'         => 'grimlock-charitable',
					'source'       => 'http://files.themosaurus.com/grimlock-charitable/grimlock-charitable.zip',
					'required'     => false,
					'external_url' => 'https://www.themosaurus.com/',
				);
			}

			// Compatibility with Elementor
			if ( class_exists( 'Elementor\Plugin' ) ) {
				$plugins[] = array(
					'name'         => 'Grimlock for Elementor',
					'slug'         => 'grimlock-elementor',
					'source'       => 'http://files.themosaurus.com/grimlock-elementor/grimlock-elementor.zip',
					'required'     => false,
					'external_url' => 'https://www.themosaurus.com/',
				);
			}

			// Compatibility with Features by WooThemes
			if ( class_exists( 'Woothemes_Features' ) ) {
				$plugins[] = array(
					'name'         => 'Grimlock for Features by WooThemes',
					'slug'         => 'grimlock-features-by-woothemes',
					'source'       => 'http://files.themosaurus.com/grimlock-features-by-woothemes/grimlock-features-by-woothemes.zip',
					'required'     => false,
					'external_url' => 'https://www.themosaurus.com/',
				);
			}

			// Compatibility with GamiPress
			if ( class_exists( 'GamiPress' ) ) {
				$plugins[] = array(
					'name'         => 'Grimlock for GamiPress',
					'slug'         => 'grimlock-gamipress',
					'source'       => 'http://files.themosaurus.com/grimlock-gamipress/grimlock-gamipress.zip',
					'required'     => false,
					'external_url' => 'https://www.themosaurus.com/',
				);
			}

			// Compatibility with Geo My WP
			if ( class_exists( 'GEO_MY_WP' ) ) {
				$plugins[] = array(
					'name'         => 'Grimlock for Geo My WP',
					'slug'         => 'grimlock-geo-my-wp',
					'source'       => 'http://files.themosaurus.com/grimlock-geo-my-wp/grimlock-geo-my-wp.zip',
					'required'     => false,
					'external_url' => 'https://www.themosaurus.com/',
				);
			}

			// Compatibility with Getwid
			if ( class_exists( 'Getwid\Getwid' ) ) {
				$plugins[] = array(
					'name'         => 'Grimlock for Getwid',
					'slug'         => 'grimlock-getwid',
					'source'       => 'http://files.themosaurus.com/grimlock-getwid/grimlock-getwid.zip',
					'required'     => false,
					'external_url' => 'https://www.themosaurus.com/',
				);
			}

			// Compatibility with Jetpack
			if ( class_exists( 'Jetpack' ) ) {
				$plugins[] = array(
					'name'         => 'Grimlock for Jetpack',
					'slug'         => 'grimlock-jetpack',
					'source'       => 'http://files.themosaurus.com/grimlock-jetpack/grimlock-jetpack.zip',
					'required'     => false,
					'external_url' => 'https://www.themosaurus.com/',
				);
			}

			// Compatibility with Echo Knowledge Base
			if ( class_exists( 'Echo_Knowledge_Base' ) ) {
				$plugins[] = array(
					'name'         => 'Grimlock for Knowledge Base for Documents and FAQs',
					'slug'         => 'grimlock-echo-knowledge-base',
					'source'       => 'http://files.themosaurus.com/grimlock-echo-knowledge-base/grimlock-echo-knowledge-base.zip',
					'required'     => false,
					'external_url' => 'https://www.themosaurus.com/',
				);
			}

			// Compatibility with LearnDash
			if ( class_exists( 'SFWD_LMS' ) ) {
				$plugins[] = array(
					'name'         => 'Grimlock for LearnDash',
					'slug'         => 'grimlock-learndash',
					'source'       => 'http://files.themosaurus.com/grimlock-learndash/grimlock-learndash.zip',
					'required'     => false,
					'external_url' => 'https://www.themosaurus.com/',
				);
			}

			// Compatibility with LearnPress
			if ( class_exists( 'LearnPress' ) ) {
				$plugins[] = array(
					'name'         => 'Grimlock for LearnPress',
					'slug'         => 'grimlock-learnpress',
					'source'       => 'http://files.themosaurus.com/grimlock-learnpress/grimlock-learnpress.zip',
					'required'     => false,
					'external_url' => 'https://www.themosaurus.com/',
				);
			}

			// Compatibility with Our Team by WooThemes
			if ( class_exists( 'Woothemes_Our_Team' ) ) {
				$plugins[] = array(
					'name'         => 'Grimlock for Our Team by WooThemes',
					'slug'         => 'grimlock-our-team-by-woothemes',
					'source'       => 'http://files.themosaurus.com/grimlock-our-team-by-woothemes/grimlock-our-team-by-woothemes.zip',
					'required'     => false,
					'external_url' => 'https://www.themosaurus.com/',
				);
			}

			// Compatibility with Paid Memberships Pro by WooThemes
			if ( function_exists( 'pmpro_gateways' ) ) {
				$plugins[] = array(
					'name'         => 'Grimlock for Paid Memberships Pro',
					'slug'         => 'grimlock-paid-memberships-pro',
					'source'       => 'http://files.themosaurus.com/grimlock-paid-memberships-pro/grimlock-paid-memberships-pro.zip',
					'required'     => false,
					'external_url' => 'https://www.themosaurus.com/',
				);
			}

			// Compatibility with Projects by WooThemes
			if ( class_exists( 'Projects' ) ) {
				$plugins[] = array(
					'name'         => 'Grimlock for Projects by WooThemes',
					'slug'         => 'grimlock-projects-by-woothemes',
					'source'       => 'http://files.themosaurus.com/grimlock-projects-by-woothemes/grimlock-projects-by-woothemes.zip',
					'required'     => false,
					'external_url' => 'https://www.themosaurus.com/',
				);
			}

			// Compatibility with Revolution Slider
			if ( class_exists( 'RevSliderFront' ) ) {
				$plugins[] = array(
					'name'         => 'Grimlock for Revolution Slider',
					'slug'         => 'grimlock-revslider',
					'source'       => 'http://files.themosaurus.com/grimlock-revslider/grimlock-revslider.zip',
					'required'     => false,
					'external_url' => 'https://www.themosaurus.com/',
				);
			}

			// Compatibility with Soliloquy
			if ( class_exists( 'Soliloquy' ) ) {
				$plugins[] = array(
					'name'         => 'Grimlock for Soliloquy',
					'slug'         => 'grimlock-soliloquy',
					'source'       => 'http://files.themosaurus.com/grimlock-soliloquy/grimlock-soliloquy.zip',
					'required'     => false,
					'external_url' => 'https://www.themosaurus.com/',
				);
			}

			// Compatibility with SportsPress
			if ( function_exists( 'SP' ) || class_exists( 'SportsPress_Pro' ) ) {
				$plugins[] = array(
					'name'         => 'Grimlock for SportsPress',
					'slug'         => 'grimlock-sportspress',
					'source'       => 'http://files.themosaurus.com/grimlock-sportspress/grimlock-sportspress.zip',
					'required'     => false,
					'external_url' => 'https://www.themosaurus.com/',
				);
			}

			// Compatibility with Tutor
			if ( function_exists( 'tutor' ) ) {
				$plugins[] = array(
					'name'         => 'Grimlock for Tutor LMS',
					'slug'         => 'grimlock-tutor',
					'source'       => 'http://files.themosaurus.com/grimlock-tutor/grimlock-tutor.zip',
					'required'     => false,
					'external_url' => 'https://www.themosaurus.com/',
				);
			}

			// Compatibility with Testimonials by WooThemes
			if ( class_exists( 'Woothemes_Testimonials' ) ) {
				$plugins[] = array(
					'name'         => 'Grimlock for Testimonials by WooThemes',
					'slug'         => 'grimlock-testimonials-by-woothemes',
					'source'       => 'http://files.themosaurus.com/grimlock-testimonials-by-woothemes/grimlock-testimonials-by-woothemes.zip',
					'required'     => false,
					'external_url' => 'https://www.themosaurus.com/',
				);
			}

			// Compatibility with The Events Calendar
			if ( class_exists( 'Tribe__Events__Main' ) ) {
				$plugins[] = array(
					'name'         => 'Grimlock for The Events Calendar',
					'slug'         => 'grimlock-the-events-calendar',
					'source'       => 'http://files.themosaurus.com/grimlock-the-events-calendar/grimlock-the-events-calendar.zip',
					'required'     => false,
					'external_url' => 'https://www.themosaurus.com/',
				);
			}

			// Compatibility with WooCommerce
			if ( function_exists( 'WC' ) ) {
				$plugins[] = array(
					'name'         => 'Grimlock for WooCommerce',
					'slug'         => 'grimlock-woocommerce',
					'source'       => 'http://files.themosaurus.com/grimlock-woocommerce/grimlock-woocommerce.zip',
					'required'     => false,
					'external_url' => 'https://www.themosaurus.com/',
				);
			}

			// Compatibility with WooCommerce Subscriptions
			if ( method_exists( 'WC_Subscriptions', 'init' ) ) {
				$plugins[] = array(
					'name'         => 'Grimlock for WooCommerce Subscriptions',
					'slug'         => 'grimlock-woocommerce-subscriptions',
					'source'       => 'http://files.themosaurus.com/grimlock-woocommerce-subscriptions/grimlock-woocommerce-subscriptions.zip',
					'required'     => false,
					'external_url' => 'https://www.themosaurus.com/',
				);
			}

			// Compatibility with WP Job Manager
			if ( function_exists( 'WPJM' ) ) {
				$plugins[] = array(
					'name'         => 'Grimlock for WP Job Manager',
					'slug'         => 'grimlock-wp-job-manager',
					'source'       => 'http://files.themosaurus.com/grimlock-wp-job-manager/grimlock-wp-job-manager.zip',
					'required'     => false,
					'external_url' => 'https://www.themosaurus.com/',
				);
			}

			// Compatibility with WP Pagenavi
			if ( function_exists( 'wp_pagenavi' ) ) {
				$plugins[] = array(
					'name'         => 'Grimlock for WP Pagenavi',
					'slug'         => 'grimlock-wp-pagenavi',
					'source'       => 'http://files.themosaurus.com/grimlock-wp-pagenavi/grimlock-wp-pagenavi.zip',
					'required'     => false,
					'external_url' => 'https://www.themosaurus.com/',
				);
			}

			// Compatibility with WPML
			if ( class_exists( 'SitePress' ) ) {
				$plugins[] = array(
					'name'         => 'Grimlock for WPML',
					'slug'         => 'grimlock-wpml',
					'source'       => 'http://files.themosaurus.com/grimlock-wpml/grimlock-wpml.zip',
					'required'     => false,
					'external_url' => 'https://www.themosaurus.com/',
				);
			}

			// Compatibility with Yoast SEO
			if ( function_exists( 'yoast_breadcrumb' ) ) {
				$plugins[] = array(
					'name'         => 'Grimlock for Yoast SEO',
					'slug'         => 'grimlock-wordpress-seo',
					'source'       => 'http://files.themosaurus.com/grimlock-wordpress-seo/grimlock-wordpress-seo.zip',
					'required'     => false,
					'external_url' => 'https://www.themosaurus.com/',
				);
			}

			$plugins = apply_filters( 'grimlock_tgm_plugin_activation_register_plugins', $plugins );

			/*
			 * Array of configuration settings.
			 */
			$config = apply_filters( 'grimlock_tgm_plugin_activation_register_config', array(
				'id'           => 'grimlock',              // Unique ID for hashing notices for multiple instances of TGMPA.
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

return new Grimlock_TGM_Plugin_Activation();
