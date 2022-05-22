<?php
/**
 * Cera_Merlin Class
 *
 * @author  Themosaurus
 * @since   1.0.0
 * @package cera
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Cera_Merlin' ) ) :
	/**
	 * The Cera Merlin Activation class
	 */
	class Cera_Merlin extends Themosaurus_Merlin {
		/**
		 * Get the demo setups arguments that will be sent to Merlin WP
		 *
		 * @return array
		 */
		protected function get_demo_setups_args() {
			return apply_filters( 'cera_demo_setups_args', array(
				'intranet'      => array(
					'import_file_name'           => 'Cera Intranet Demo',
					'import_file_url'            => 'https://files.themosaurus.com/cera/demos/intranet/demo-content.xml',
					'import_widget_file_url'     => 'https://files.themosaurus.com/cera/demos/intranet/widgets.wie',
					'import_customizer_file_url' => 'https://files.themosaurus.com/cera/demos/intranet/customizer.dat',
					'import_file_screenshot'     => get_template_directory_uri() . '/assets/images/screenshots/intranet.jpg',
					'import_notice'              => esc_html__( 'Visit doc.themosaurus.com to get the full documentation for the theme', 'cera' ),
					'before_import_method'       => 'before_import',
					'after_import_method'        => 'after_import_intranet',
					'preview_url'                => 'http://intranet.cera-theme.com/',
					'tgmpa'                      => array(
						array(
							'name'     => 'Author Avatars List',
							'slug'     => 'author-avatars',
							'required' => false,
						),
						array(
							'name'     => 'bbP Topic Count',
							'slug'     => 'bbp-topic-count',
							'required' => false,
						),
						array(
							'name'     => 'bbPress',
							'slug'     => 'bbpress',
							'required' => false,
						),
						array(
							'name'     => 'BP Better Messages',
							'slug'     => 'bp-better-messages',
							'required' => false,
						),
						array(
							'name'     => 'BP Birthday Greetings',
							'slug'     => 'bp-birthday-greetings',
							'required' => false,
						),
						array(
							'name'     => 'BP Profile Search',
							'slug'     => 'bp-profile-search',
							'required' => false,
						),
						array(
							'name'     => 'BuddyPress',
							'slug'     => 'buddypress',
							'required' => true,
						),
						array(
							'name'     => 'BuddyPress Docs',
							'slug'     => 'buddypress-docs',
							'required' => false,
						),
						array(
							'name'     => 'BuddyPress Global Search',
							'slug'     => 'buddypress-global-search',
							'required' => false,
						),
						array(
							'name'     => 'BuddyPress Notifications Widget',
							'slug'     => 'buddypress-notifications-widget',
							'required' => false,
						),
						array(
							'name'     => 'CoBlocks',
							'slug'     => 'coblocks',
							'required' => false,
						),
						array(
							'name'     => 'Jetpack – WP Security, Backup, Speed, & Growth',
							'slug'     => 'jetpack',
							'required' => false,
						),
						array(
							'name'     => 'Knowledge Base for Documents and FAQs',
							'slug'     => 'echo-knowledge-base',
							'required' => false,
						),
						array(
							'name'     => 'Member Swipe for BuddyPress',
							'slug'     => 'bp-member-swipe',
							'required' => false,
						),
						array(
							'name'     => 'Menu Image',
							'slug'     => 'menu-image',
							'required' => false,
						),
						array(
							'name'     => 'Paid Memberships Pro',
							'slug'     => 'paid-memberships-pro',
							'required' => false,
						),
						array(
							'name'     => 'Paid Memberships Pro - BuddyPress Add On',
							'slug'     => 'pmpro-buddypress',
							'required' => false,
						),
						array(
							'name'     => 'rtMedia for WordPress, BuddyPress and bbPress',
							'slug'     => 'buddypress-media',
							'required' => false,
						),
						array(
							'name'     => 'Social Articles',
							'slug'     => 'social-articles',
							'required' => false,
						),
						array(
							'name'     => 'The Events Calendar',
							'slug'     => 'the-events-calendar',
							'required' => false,
						),
						array(
							'name'     => 'WordPress Popular Posts',
							'slug'     => 'wordpress-popular-posts',
							'required' => false,
						),
						array(
							'name'     => 'Yoast SEO',
							'slug'     => 'wordpress-seo',
							'required' => false,
						),
						array(
							'name'         => 'Grimlock for Author Avatars List',
							'slug'         => 'grimlock-author-avatars',
							'source'       => 'http://files.themosaurus.com/grimlock-author-avatars/grimlock-author-avatars.zip',
							'required'     => false,
							'external_url' => 'https://www.themosaurus.com/',
						),
						array(
							'name'         => 'Grimlock for bbPress',
							'slug'         => 'grimlock-bbpress',
							'source'       => 'http://files.themosaurus.com/grimlock-bbpress/grimlock-bbpress.zip',
							'required'     => false,
							'external_url' => 'https://www.themosaurus.com/',
						),
						array(
							'name'         => 'Grimlock for BuddyPress',
							'slug'         => 'grimlock-buddypress',
							'source'       => 'http://files.themosaurus.com/grimlock-buddypress/grimlock-buddypress.zip',
							'required'     => true,
							'external_url' => 'https://www.themosaurus.com/',
						),
						array(
							'name'         => 'Grimlock for Jetpack',
							'slug'         => 'grimlock-jetpack',
							'source'       => 'http://files.themosaurus.com/grimlock-jetpack/grimlock-jetpack.zip',
							'required'     => false,
							'external_url' => 'https://www.themosaurus.com/',
						),
						array(
							'name'         => 'Grimlock for Knowledge Base for Documents and FAQs',
							'slug'         => 'grimlock-echo-knowledge-base',
							'source'       => 'http://files.themosaurus.com/grimlock-echo-knowledge-base/grimlock-echo-knowledge-base.zip',
							'required'     => false,
							'external_url' => 'https://www.themosaurus.com/',
						),
						array(
							'name'         => 'Grimlock for The Events Calendar',
							'slug'         => 'grimlock-the-events-calendar',
							'source'       => 'http://files.themosaurus.com/grimlock-the-events-calendar/grimlock-the-events-calendar.zip',
							'required'     => false,
							'external_url' => 'https://www.themosaurus.com/',
						),
						array(
							'name'         => 'Grimlock for Yoast SEO',
							'slug'         => 'grimlock-wordpress-seo',
							'source'       => 'http://files.themosaurus.com/grimlock-wordpress-seo/grimlock-wordpress-seo.zip',
							'required'     => false,
							'external_url' => 'https://www.themosaurus.com/',
						),
						array(
							'name'         => 'Grimlock Isotope',
							'slug'         => 'grimlock-isotope',
							'source'       => 'http://files.themosaurus.com/grimlock-isotope/grimlock-isotope.zip',
							'required'     => false,
							'external_url' => 'https://www.themosaurus.com/',
						),
						array(
							'name'         => 'Grimlock for Paid Memberships Pro',
							'slug'         => 'grimlock-paid-memberships-pro',
							'source'       => 'http://files.themosaurus.com/grimlock-paid-memberships-pro/grimlock-paid-memberships-pro.zip',
							'required'     => false,
							'external_url' => 'https://www.themosaurus.com/',
						),
					),
				),
				'intranet_dark' => array(
					'import_file_name'           => 'Cera Intranet Dark Demo',
					'import_file_url'            => 'https://files.themosaurus.com/cera/demos/intranet-dark/demo-content.xml',
					'import_widget_file_url'     => 'https://files.themosaurus.com/cera/demos/intranet-dark/widgets.wie',
					'import_customizer_file_url' => 'https://files.themosaurus.com/cera/demos/intranet-dark/customizer.dat',
					'import_file_screenshot'     => get_template_directory_uri() . '/assets/images/screenshots/intranet-dark.jpg',
					'import_notice'              => esc_html__( 'Visit doc.themosaurus.com to get the full documentation for the theme', 'cera' ),
					'before_import_method'       => 'before_import',
					'after_import_method'        => 'after_import_intranet',
					'preview_url'                => 'http://intranet.cera-theme.com/',
					'tgmpa'                      => array(
						array(
							'name'     => 'Author Avatars List',
							'slug'     => 'author-avatars',
							'required' => false,
						),
						array(
							'name'     => 'bbP Topic Count',
							'slug'     => 'bbp-topic-count',
							'required' => false,
						),
						array(
							'name'     => 'bbPress',
							'slug'     => 'bbpress',
							'required' => false,
						),
						array(
							'name'     => 'BP Better Messages',
							'slug'     => 'bp-better-messages',
							'required' => false,
						),
						array(
							'name'     => 'BP Birthday Greetings',
							'slug'     => 'bp-birthday-greetings',
							'required' => false,
						),
						array(
							'name'     => 'BP Profile Search',
							'slug'     => 'bp-profile-search',
							'required' => false,
						),
						array(
							'name'     => 'BuddyPress',
							'slug'     => 'buddypress',
							'required' => true,
						),
						array(
							'name'     => 'BuddyPress Docs',
							'slug'     => 'buddypress-docs',
							'required' => false,
						),
						array(
							'name'     => 'BuddyPress Global Search',
							'slug'     => 'buddypress-global-search',
							'required' => false,
						),
						array(
							'name'     => 'BuddyPress Notifications Widget',
							'slug'     => 'buddypress-notifications-widget',
							'required' => false,
						),
						array(
							'name'     => 'CoBlocks',
							'slug'     => 'coblocks',
							'required' => false,
						),
						array(
							'name'     => 'Jetpack – WP Security, Backup, Speed, & Growth',
							'slug'     => 'jetpack',
							'required' => false,
						),
						array(
							'name'     => 'Knowledge Base for Documents and FAQs',
							'slug'     => 'echo-knowledge-base',
							'required' => false,
						),
						array(
							'name'     => 'Member Swipe for BuddyPress',
							'slug'     => 'bp-member-swipe',
							'required' => false,
						),
						array(
							'name'     => 'Menu Image',
							'slug'     => 'menu-image',
							'required' => false,
						),
						array(
							'name'     => 'Paid Memberships Pro',
							'slug'     => 'paid-memberships-pro',
							'required' => false,
						),
						array(
							'name'     => 'Paid Memberships Pro - BuddyPress Add On',
							'slug'     => 'pmpro-buddypress',
							'required' => false,
						),
						array(
							'name'     => 'rtMedia for WordPress, BuddyPress and bbPress',
							'slug'     => 'buddypress-media',
							'required' => false,
						),
						array(
							'name'     => 'Social Articles',
							'slug'     => 'social-articles',
							'required' => false,
						),
						array(
							'name'     => 'The Events Calendar',
							'slug'     => 'the-events-calendar',
							'required' => false,
						),
						array(
							'name'     => 'WordPress Popular Posts',
							'slug'     => 'wordpress-popular-posts',
							'required' => false,
						),
						array(
							'name'     => 'Yoast SEO',
							'slug'     => 'wordpress-seo',
							'required' => false,
						),
						array(
							'name'         => 'Grimlock for Author Avatars List',
							'slug'         => 'grimlock-author-avatars',
							'source'       => 'http://files.themosaurus.com/grimlock-author-avatars/grimlock-author-avatars.zip',
							'required'     => false,
							'external_url' => 'https://www.themosaurus.com/',
						),
						array(
							'name'         => 'Grimlock for bbPress',
							'slug'         => 'grimlock-bbpress',
							'source'       => 'http://files.themosaurus.com/grimlock-bbpress/grimlock-bbpress.zip',
							'required'     => false,
							'external_url' => 'https://www.themosaurus.com/',
						),
						array(
							'name'         => 'Grimlock for BuddyPress',
							'slug'         => 'grimlock-buddypress',
							'source'       => 'http://files.themosaurus.com/grimlock-buddypress/grimlock-buddypress.zip',
							'required'     => true,
							'external_url' => 'https://www.themosaurus.com/',
						),
						array(
							'name'         => 'Grimlock for Jetpack',
							'slug'         => 'grimlock-jetpack',
							'source'       => 'http://files.themosaurus.com/grimlock-jetpack/grimlock-jetpack.zip',
							'required'     => false,
							'external_url' => 'https://www.themosaurus.com/',
						),
						array(
							'name'         => 'Grimlock for Knowledge Base for Documents and FAQs',
							'slug'         => 'grimlock-echo-knowledge-base',
							'source'       => 'http://files.themosaurus.com/grimlock-echo-knowledge-base/grimlock-echo-knowledge-base.zip',
							'required'     => false,
							'external_url' => 'https://www.themosaurus.com/',
						),
						array(
							'name'         => 'Grimlock for The Events Calendar',
							'slug'         => 'grimlock-the-events-calendar',
							'source'       => 'http://files.themosaurus.com/grimlock-the-events-calendar/grimlock-the-events-calendar.zip',
							'required'     => false,
							'external_url' => 'https://www.themosaurus.com/',
						),
						array(
							'name'         => 'Grimlock for Yoast SEO',
							'slug'         => 'grimlock-wordpress-seo',
							'source'       => 'http://files.themosaurus.com/grimlock-wordpress-seo/grimlock-wordpress-seo.zip',
							'required'     => false,
							'external_url' => 'https://www.themosaurus.com/',
						),
						array(
							'name'         => 'Grimlock Isotope',
							'slug'         => 'grimlock-isotope',
							'source'       => 'http://files.themosaurus.com/grimlock-isotope/grimlock-isotope.zip',
							'required'     => false,
							'external_url' => 'https://www.themosaurus.com/',
						),
						array(
							'name'         => 'Grimlock for Paid Memberships Pro',
							'slug'         => 'grimlock-paid-memberships-pro',
							'source'       => 'http://files.themosaurus.com/grimlock-paid-memberships-pro/grimlock-paid-memberships-pro.zip',
							'required'     => false,
							'external_url' => 'https://www.themosaurus.com/',
						),
					),
				),
				'youzify'       => array(
					'import_file_name'           => 'Cera Youzify Demo',
					'import_file_url'            => 'https://files.themosaurus.com/cera/demos/youzify/demo-content.xml',
					'import_widget_file_url'     => 'https://files.themosaurus.com/cera/demos/youzify/widgets.wie',
					'import_customizer_file_url' => 'https://files.themosaurus.com/cera/demos/youzify/customizer.dat',
					'import_file_screenshot'     => get_template_directory_uri() . '/assets/images/screenshots/youzify.jpg',
					'import_notice'              => esc_html__( 'Visit doc.themosaurus.com to get the full documentation for the theme', 'cera' ),
					'before_import_method'       => 'before_import',
					'after_import_method'        => 'after_import_youzify',
					'preview_url'                => 'http://youzify.cera-theme.com/',
					'tgmpa'                      => array(
						array(
							'name'     => 'Author Avatars List',
							'slug'     => 'author-avatars',
							'required' => false,
						),
						array(
							'name'     => 'bbP Topic Count',
							'slug'     => 'bbp-topic-count',
							'required' => false,
						),
						array(
							'name'     => 'bbPress',
							'slug'     => 'bbpress',
							'required' => false,
						),
						array(
							'name'     => 'BP Better Messages',
							'slug'     => 'bp-better-messages',
							'required' => false,
						),
						array(
							'name'     => 'BP Birthday Greetings',
							'slug'     => 'bp-birthday-greetings',
							'required' => false,
						),
						array(
							'name'     => 'BP Profile Search',
							'slug'     => 'bp-profile-search',
							'required' => false,
						),
						array(
							'name'     => 'BuddyPress',
							'slug'     => 'buddypress',
							'required' => true,
						),
						array(
							'name'     => 'BuddyPress Global Search',
							'slug'     => 'buddypress-global-search',
							'required' => false,
						),
						array(
							'name'     => 'BuddyPress Notifications Widget',
							'slug'     => 'buddypress-notifications-widget',
							'required' => false,
						),
						array(
							'name'     => 'CoBlocks',
							'slug'     => 'coblocks',
							'required' => false,
						),
						array(
							'name'     => 'Jetpack – WP Security, Backup, Speed, & Growth',
							'slug'     => 'jetpack',
							'required' => false,
						),
						array(
							'name'     => 'Knowledge Base for Documents and FAQs',
							'slug'     => 'echo-knowledge-base',
							'required' => false,
						),
						array(
							'name'     => 'Member Swipe for BuddyPress',
							'slug'     => 'bp-member-swipe',
							'required' => false,
						),
						array(
							'name'     => 'Menu Image',
							'slug'     => 'menu-image',
							'required' => false,
						),
						array(
							'name'     => 'Paid Memberships Pro',
							'slug'     => 'paid-memberships-pro',
							'required' => false,
						),
						array(
							'name'     => 'Paid Memberships Pro - BuddyPress Add On',
							'slug'     => 'pmpro-buddypress',
							'required' => false,
						),
						array(
							'name'     => 'WordPress Popular Posts',
							'slug'     => 'wordpress-popular-posts',
							'required' => false,
						),
						array(
							'name'     => 'Yoast SEO',
							'slug'     => 'wordpress-seo',
							'required' => false,
						),
						array(
							'name'     => 'Youzify',
							'slug'     => 'youzify',
							'required' => false,
						),
						array(
							'name'         => 'Grimlock for Author Avatars List',
							'slug'         => 'grimlock-author-avatars',
							'source'       => 'http://files.themosaurus.com/grimlock-author-avatars/grimlock-author-avatars.zip',
							'required'     => false,
							'external_url' => 'https://www.themosaurus.com/',
						),
						array(
							'name'         => 'Grimlock for bbPress',
							'slug'         => 'grimlock-bbpress',
							'source'       => 'http://files.themosaurus.com/grimlock-bbpress/grimlock-bbpress.zip',
							'required'     => false,
							'external_url' => 'https://www.themosaurus.com/',
						),
						array(
							'name'         => 'Grimlock for BuddyPress',
							'slug'         => 'grimlock-buddypress',
							'source'       => 'http://files.themosaurus.com/grimlock-buddypress/grimlock-buddypress.zip',
							'required'     => true,
							'external_url' => 'https://www.themosaurus.com/',
						),
						array(
							'name'         => 'Grimlock for Jetpack',
							'slug'         => 'grimlock-jetpack',
							'source'       => 'http://files.themosaurus.com/grimlock-jetpack/grimlock-jetpack.zip',
							'required'     => false,
							'external_url' => 'https://www.themosaurus.com/',
						),
						array(
							'name'         => 'Grimlock for Knowledge Base for Documents and FAQs',
							'slug'         => 'grimlock-echo-knowledge-base',
							'source'       => 'http://files.themosaurus.com/grimlock-echo-knowledge-base/grimlock-echo-knowledge-base.zip',
							'required'     => false,
							'external_url' => 'https://www.themosaurus.com/',
						),
						array(
							'name'         => 'Grimlock for Yoast SEO',
							'slug'         => 'grimlock-wordpress-seo',
							'source'       => 'http://files.themosaurus.com/grimlock-wordpress-seo/grimlock-wordpress-seo.zip',
							'required'     => false,
							'external_url' => 'https://www.themosaurus.com/',
						),
						array(
							'name'         => 'Grimlock Isotope',
							'slug'         => 'grimlock-isotope',
							'source'       => 'http://files.themosaurus.com/grimlock-isotope/grimlock-isotope.zip',
							'required'     => false,
							'external_url' => 'https://www.themosaurus.com/',
						),
						array(
							'name'         => 'Grimlock for Paid Memberships Pro',
							'slug'         => 'grimlock-paid-memberships-pro',
							'source'       => 'http://files.themosaurus.com/grimlock-paid-memberships-pro/grimlock-paid-memberships-pro.zip',
							'required'     => false,
							'external_url' => 'https://www.themosaurus.com/',
						),
					),
				),
				'learn'         => array(
					'import_file_name'           => 'Cera Learn Demo',
					/* translators: 1: Opening <a> tag 2: Closing <a> tag 3: Opening <strong> tag 4: Closing <strong> tag */
					'import_file_warning'        => '<strong>' . esc_html__( 'Heads up!', 'cera' ) . '</strong><p>' . sprintf( esc_html__( 'This demo is based on the %1$sLearnDash%2$s plugin. This is a paid plugin and it is not included with Cera. We recommend that you manually install it %3$sbefore%4$s importing this demo.', 'gwangi' ), '<a target="_blank" href="https://www.learndash.com/">', '</a>', '<strong>', '</strong>' ) . '</p>',
					'import_file_url'            => 'https://files.themosaurus.com/cera/demos/learn/demo-content.xml',
					'import_widget_file_url'     => 'https://files.themosaurus.com/cera/demos/learn/widgets.wie',
					'import_customizer_file_url' => 'https://files.themosaurus.com/cera/demos/learn/customizer.dat',
					'import_file_screenshot'     => get_template_directory_uri() . '/assets/images/screenshots/learn.jpg',
					'import_notice'              => esc_html__( 'Visit doc.themosaurus.com to get the full documentation for the theme', 'cera' ),
					'import_finished_warning'    => '<strong>' . esc_html__( 'Heads up!', 'cera' ) . '</strong><p>' . sprintf( esc_html__( 'To complete this demo setup, don\'t forget to install the %1$sLearnDash%2$s plugin if you haven\'t already.', 'cera' ), '<a href="https://www.learndash.com/" target="_blank">', '</a>' ) . '</p>',
					'before_import_method'       => 'before_import',
					'after_import_method'        => 'after_import_learn',
					'preview_url'                => 'http://learn.cera-theme.com/',
					'tgmpa'                      => array(
						array(
							'name'     => 'Author Avatars List',
							'slug'     => 'author-avatars',
							'required' => false,
						),
						array(
							'name'     => 'bbP Topic Count',
							'slug'     => 'bbp-topic-count',
							'required' => false,
						),
						array(
							'name'     => 'bbPress',
							'slug'     => 'bbpress',
							'required' => false,
						),
						array(
							'name'     => 'BP Better Messages',
							'slug'     => 'bp-better-messages',
							'required' => false,
						),
						array(
							'name'     => 'BP Profile Search',
							'slug'     => 'bp-profile-search',
							'required' => false,
						),
						array(
							'name'     => 'BuddyPress',
							'slug'     => 'buddypress',
							'required' => true,
						),
						array(
							'name'     => 'BuddyPress Docs',
							'slug'     => 'buddypress-docs',
							'required' => false,
						),
						array(
							'name'     => 'BuddyPress for LearnDash',
							'slug'     => 'buddypress-learndash',
							'required' => false,
						),
						array(
							'name'     => 'BuddyPress Global Search',
							'slug'     => 'buddypress-global-search',
							'required' => false,
						),
						array(
							'name'     => 'BuddyPress Notifications Widget',
							'slug'     => 'buddypress-notifications-widget',
							'required' => false,
						),
						array(
							'name'     => 'CoBlocks',
							'slug'     => 'coblocks',
							'required' => false,
						),
						array(
							'name'     => 'Jetpack – WP Security, Backup, Speed, & Growth',
							'slug'     => 'jetpack',
							'required' => false,
						),
						array(
							'name'     => 'Knowledge Base for Documents and FAQs',
							'slug'     => 'echo-knowledge-base',
							'required' => false,
						),
						array(
							'name'     => 'Menu Image',
							'slug'     => 'menu-image',
							'required' => false,
						),
						array(
							'name'     => 'Paid Memberships Pro',
							'slug'     => 'paid-memberships-pro',
							'required' => false,
						),
						array(
							'name'     => 'Paid Memberships Pro - BuddyPress Add On',
							'slug'     => 'pmpro-buddypress',
							'required' => false,
						),
						array(
							'name'     => 'rtMedia for WordPress, BuddyPress and bbPress',
							'slug'     => 'buddypress-media',
							'required' => false,
						),
						array(
							'name'     => 'WordPress Popular Posts',
							'slug'     => 'wordpress-popular-posts',
							'required' => false,
						),
						array(
							'name'     => 'Yoast SEO',
							'slug'     => 'wordpress-seo',
							'required' => false,
						),
						array(
							'name'         => 'Grimlock for Author Avatars List',
							'slug'         => 'grimlock-author-avatars',
							'source'       => 'http://files.themosaurus.com/grimlock-author-avatars/grimlock-author-avatars.zip',
							'required'     => false,
							'external_url' => 'https://www.themosaurus.com/',
						),
						array(
							'name'         => 'Grimlock for bbPress',
							'slug'         => 'grimlock-bbpress',
							'source'       => 'http://files.themosaurus.com/grimlock-bbpress/grimlock-bbpress.zip',
							'required'     => false,
							'external_url' => 'https://www.themosaurus.com/',
						),
						array(
							'name'         => 'Grimlock for BuddyPress',
							'slug'         => 'grimlock-buddypress',
							'source'       => 'http://files.themosaurus.com/grimlock-buddypress/grimlock-buddypress.zip',
							'required'     => true,
							'external_url' => 'https://www.themosaurus.com/',
						),
						array(
							'name'         => 'Grimlock for Jetpack',
							'slug'         => 'grimlock-jetpack',
							'source'       => 'http://files.themosaurus.com/grimlock-jetpack/grimlock-jetpack.zip',
							'required'     => false,
							'external_url' => 'https://www.themosaurus.com/',
						),
						array(
							'name'         => 'Grimlock for Knowledge Base for Documents and FAQs',
							'slug'         => 'grimlock-echo-knowledge-base',
							'source'       => 'http://files.themosaurus.com/grimlock-echo-knowledge-base/grimlock-echo-knowledge-base.zip',
							'required'     => false,
							'external_url' => 'https://www.themosaurus.com/',
						),
						array(
							'name'         => 'Grimlock for Yoast SEO',
							'slug'         => 'grimlock-wordpress-seo',
							'source'       => 'http://files.themosaurus.com/grimlock-wordpress-seo/grimlock-wordpress-seo.zip',
							'required'     => false,
							'external_url' => 'https://www.themosaurus.com/',
						),
						array(
							'name'         => 'Grimlock Isotope',
							'slug'         => 'grimlock-isotope',
							'source'       => 'http://files.themosaurus.com/grimlock-isotope/grimlock-isotope.zip',
							'required'     => false,
							'external_url' => 'https://www.themosaurus.com/',
						),
						array(
							'name'         => 'Grimlock for Paid Memberships Pro',
							'slug'         => 'grimlock-paid-memberships-pro',
							'source'       => 'http://files.themosaurus.com/grimlock-paid-memberships-pro/grimlock-paid-memberships-pro.zip',
							'required'     => false,
							'external_url' => 'https://www.themosaurus.com/',
						),
					),
				),
			) );
		}

		public function before_import() {
			$this->setup_bp_components( array(
				'friends'  => true,
				'groups'   => true,
				'messages' => true,
			) );
		}

		/**
		 * After import logic for the Intranet demo
		 */
		public function after_import_intranet() {
			$this->update_front_and_blog_page_by_title( 'Dashboard', 'News' );
			$this->delete_duplicate_pages();
			$this->convert_menu_items_relative_urls_to_absolute_urls();

			$this->assign_menus_to_locations( array(
				'primary'         => 'Primary',
				'user_logged_in'  => 'User - Logged In',
				'user_logged_out' => 'User - Logged Out',
			) );

			$this->fix_menu_items_hierarchy( 'Primary', array(
				'Members'       => array(
					'Members directory',
					'Swipe Members',
					'Search members',
				),
				'Documents New' => array(
					'All Documents',
					'+ Add New Documents',
				),
				'Pages'         => array(
					'Home',
					'Password Protected Page',
					'404 page',
					'Gutenberg Blocks',
				),
			) );

			$primary_menu_icons_class = $_GET['demo'] === 1 ? 'cera-icon' : 'cera-icon text-primary';

			$this->fix_menu_items_titles( 'Primary', array(
				'Dashboard'          => '<i class="' . $primary_menu_icons_class . ' cera-grid"></i> <span>Dashboard</span>',
				'Homepage'           => '<i class="' . $primary_menu_icons_class . ' cera-layout"></i> <span>Homepage</span>',
				'Social wall'        => '<i class="' . $primary_menu_icons_class . ' cera-heart"></i> <span>Social wall</span>',
				'Members'            => '<i class="' . $primary_menu_icons_class . ' cera-globe"></i> <span>Members</span>',
				'Groups'             => '<i class="' . $primary_menu_icons_class . ' cera-users"></i> <span>Groups</span>',
				'Forums'             => '<i class="' . $primary_menu_icons_class . ' cera-message-square"></i> <span>Forums</span>',
				'Documents New'      => '<i class="' . $primary_menu_icons_class . ' cera-folder"></i> <span>Documents</span> <ins>New</ins>',
				'Calendar'           => '<i class="' . $primary_menu_icons_class . ' cera-calendar"></i> <span>Calendar</span>',
				'Today\'s events'    => '<i class="' . $primary_menu_icons_class . ' cera-sun"></i> <span>Today\'s events</span>',
				'Events List'        => '<i class="' . $primary_menu_icons_class . ' cera-list"></i> <span>Events List</span>',
				'Pricing plans New'  => '<i class="' . $primary_menu_icons_class . ' cera-dollar-sign"></i> <span>Pricing plans</span><ins class="bg-pink">New</ins>',
				'Restricted content' => '<i class="' . $primary_menu_icons_class . ' cera-x-octagon"></i> <span>Restricted content</span>',
				'News Hot'           => '<i class="' . $primary_menu_icons_class . ' cera-alert-circle"></i> <span>News</span><ins class="bg-success">Hot</ins>',
				'Wiki New'           => '<i class="' . $primary_menu_icons_class . ' cera-life-buoy"></i> <span>Wiki</span> <ins>New</ins>',
				'Pages'              => '<i class="' . $primary_menu_icons_class . ' cera-file"></i> <span>Pages</span>',
				'Log out'            => '<i class="' . $primary_menu_icons_class . ' cera-log-out"></i> <span>Log out</span>',
			) );

			$this->fix_menu_items_titles( 'Features', array(
				'Your team gathered'    => '<span class="icon-wrapper"><i class="cera-icon cera-users"></i><span class="h5">Your team gathered</span></span>',
				'Share documents'       => '<span class="icon-wrapper"><i class="cera-icon cera-hard-drive"></i><span class="h5">Share documents</span></span>',
				'Discuss your projects' => '<span class="icon-wrapper"><i class="cera-icon cera-message-circle"></i><span class="h5">Discuss your projects</span></span>',
			) );

			$this->fix_menu_items_titles( 'Footer', array(
				'https://facebook.com/' => '<i class="fa fa-facebook"></i>',
				'https://twitter.com/'  => '<i class="fa fa-twitter"></i>',
				'https://slack.com/'    => '<i class="fa fa-slack"></i>',
			), 'url' );

			$this->fix_menu_items_titles( 'User - Logged In', array(
				'Dashboard'     => '<i class="cera-icon cera-grid"></i> Dashboard',
				'Activity'      => '<i class="cera-icon cera-alert-circle"></i> Activity',
				'Notifications' => '<i class="cera-icon cera-bell"></i> Notifications',
				'Messages'      => '<i class="cera-icon cera-message-circle"></i> Messages',
				'Friends'       => '<i class="cera-icon cera-heart"></i> Friends',
				'Groups'        => '<i class="cera-icon cera-users"></i> Groups',
				'Forums'        => '<i class="cera-icon cera-message-square"></i> Forums',
				'Articles'      => '<i class="cera-icon cera-edit-2"></i> Articles',
				'Profile'       => '<i class="cera-icon cera-user"></i> Profile',
				'Settings'      => '<i class="cera-icon cera-settings"></i> Settings',
				'Log out'       => '<i class="cera-icon cera-log-out"></i> Log out',
			) );

			$this->set_custom_logo( 'logo' );

			// BuddyPress adjustments
			if ( class_exists( 'BuddyPress' ) ) {
				$this->enable_user_registrations();

				$this->enable_bp_legacy();

				$this->generate_base_xprofile_fields();

				$this->generate_details_profile_fields();

				// Fix members directory search form
				$this->fix_bps_form( 'Search directory', array(
					'Name',
					'any',
					'Position',
					'Birthdate',
				), 'Members' );

				// Fix home search form
				$this->fix_bps_form( 'Search home', array(
					'Name',
					'Birthdate',
					'Position',
				), 'Members' );
			}

			$this->setup_epkb();

			$this->assign_pmpro_pages();

			$this->adjust_yoast_settings();

			$this->activate_jetpack_modules( array(
				'carousel',
				'infinite-scroll',
				'tiled-gallery',
			) );
		}

		/**
		 * After import logic for the Youzify demo
		 */
		public function after_import_youzify() {
			$this->update_front_and_blog_page_by_title( 'Home', 'News' );
			$this->delete_duplicate_pages();
			$this->convert_menu_items_relative_urls_to_absolute_urls();

			// Assign menus to their locations.
			$this->assign_menus_to_locations( array(
				'primary'         => 'Primary',
				'user_logged_in'  => 'User - Logged In',
				'user_logged_out' => 'User - Logged Out',
			) );

			$this->fix_menu_items_hierarchy( 'Primary', array(
				'Home' => array(
					'Homepage',
					'Dashboard',
				),
				'Community' => array(
					'Social wall',
					'Members',
					'Groups',
					'Forums',
				),
				'Members' => array(
					'Members directory',
					'Swipe Members',
					'Search members',
				),
				'Membership' => array(
					'Pricing plans',
					'Restricted content',
				),
				'More' => array(
					'Dashboard page',
					'Wiki',
					'News',
					'Password Protected Page',
					'Gutenberg Blocks',
					'404 page',
				),
			) );

			$this->fix_menu_items_titles( 'Primary', array(
				'Social wall'        => '<i class="cera-icon cera-heart text-primary"></i> <span>Social wall</span>',
				'Members'            => '<i class="cera-icon cera-globe text-primary"></i> <span>Members</span>',
				'Groups'             => '<i class="cera-icon cera-users text-primary"></i> <span>Groups</span>',
				'Forums'             => '<i class="cera-icon cera-message-square text-primary"></i> <span>Forums</span>',
				'Pricing plans'      => '<i class="cera-icon cera-dollar-sign text-primary"></i> <span>Pricing plans</span>',
				'Restricted content' => '<i class="cera-icon cera-x-octagon text-primary"></i> <span>Restricted content</span>',
			) );

			$this->fix_menu_items_titles( 'Features', array(
				'Bring members together' => '<span class="icon-wrapper"><i class="cera-icon cera-users"></i><span class="h5">Bring members together</span></span>',
				'Share documents'        => '<span class="icon-wrapper"><i class="cera-icon cera-hard-drive"></i><span class="h5">Share documents</span></span>',
				'Create relationships'   => '<span class="icon-wrapper"><i class="cera-icon cera-message-circle"></i><span class="h5">Create relationships</span></span>',
			) );

			$this->fix_menu_items_titles( 'Social', array(
				'https://www.instagram.com/' => '<i class="fa fa-instagram"></i>',
				'https://twitter.com/'       => '<i class="fa fa-twitter"></i>',
				'https://www.facebook.com/'  => '<i class="fa fa-facebook"></i>',
			), 'url' );

			$this->fix_menu_items_titles( 'User - Logged In', array(
				'Dashboard'     => '<i class="cera-icon cera-grid"></i> Dashboard',
				'Activity'      => '<i class="cera-icon cera-alert-circle"></i> Activity',
				'Notifications' => '<i class="cera-icon cera-bell"></i> Notifications',
				'Messages'      => '<i class="cera-icon cera-message-circle"></i> Messages',
				'Friends'       => '<i class="cera-icon cera-heart"></i> Friends',
				'Groups'        => '<i class="cera-icon cera-users"></i> Groups',
				'Forums'        => '<i class="cera-icon cera-message-square"></i> Forums',
				'Articles'      => '<i class="cera-icon cera-edit-2"></i> Articles',
				'Profile'       => '<i class="cera-icon cera-user"></i> Profile',
				'Settings'      => '<i class="cera-icon cera-settings"></i> Settings',
				'Log out'       => '<i class="cera-icon cera-log-out"></i> Log out',
			) );

			$this->set_custom_logo( 'logo' );

			// BuddyPress adjustments
			if ( class_exists( 'BuddyPress' ) ) {
				$this->enable_user_registrations();

				$this->enable_bp_legacy();

				$this->generate_base_xprofile_fields();

				$this->generate_details_profile_fields();

				// Fix members directory search form
				$this->fix_bps_form( 'Search directory', array(
					'Name',
					'any',
					'Position',
					'Birthdate',
				), 'Members' );

				// Fix home search form
				$this->fix_bps_form( 'Search home', array(
					'Name',
					'Birthdate',
					'Position',
				), 'Members' );
			}

			$this->setup_epkb();

			$this->assign_pmpro_pages();

			$this->adjust_yoast_settings();

			$this->activate_jetpack_modules( array(
				'tiled-gallery',
				'infinite-scroll',
				'carousel',
			) );
		}

		/**
		 * After import logic for the Learn demo
		 */
		public function after_import_learn() {
			$this->update_front_and_blog_page_by_title( 'Home', 'News' );
			$this->delete_duplicate_pages();
			$this->convert_menu_items_relative_urls_to_absolute_urls();

			$this->assign_menus_to_locations( array(
				'primary'         => 'Primary',
				'secondary'       => 'Secondary',
				'user_logged_in'  => 'User - Logged In',
				'user_logged_out' => 'User - Logged Out',
			) );

			$this->fix_menu_items_hierarchy( 'Secondary', array(
				'E-Learning' => array(
					'All Courses',
					'All Lessons',
					'All Topics',
					'All Quizzes',
				),
				'Community' => array(
					'Docs',
					'Members',
					'Groups',
					'Forums',
					'Social Feed',
				),
				'Pages' => array(
					'Membership Levels',
					'Restricted content',
					'News',
					'Home',
					'Gutenberg Blocks',
					'404 page',
				),
			) );

			$this->fix_menu_items_titles( 'Primary', array(
				'My Dashboard'    => '<i class="cera-icon cera-grid"></i> <span>My Dashboard</span>',
				'My Courses'      => '<i class="cera-icon cera-feather"></i> <span>My Courses</span>',
				'My Docs New'     => '<i class="cera-icon cera-folder"></i> <span>My Docs</span> <ins class="bg-info">New</ins>',
				'My Feed'         => '<i class="cera-icon cera-rss"></i> <span>My Feed</span>',
				'My Messages'     => '<i class="cera-icon cera-message-circle"></i> <span>My Messages</span>',
				'My Forums'       => '<i class="cera-icon cera-message-square"></i> <span>My Forums</span>',
				'My Groups Ready' => '<i class="cera-icon cera-users"></i> <span>My Groups</span> <ins class="bg-pink">Ready</ins>',
				'My Connections'  => '<i class="cera-icon cera-heart"></i> <span>My Connections</span>',
				'Log out'         => '<i class="cera-icon cera-log-out"></i> <span>Log out</span>',
			) );

			$this->fix_menu_items_titles( 'Features', array(
				'Learning Community'    => '<span class="icon-wrapper"><i class="cera-icon cera-users"></i><span class="h5">Learning Community</span></span>',
				'Share documents'       => '<span class="icon-wrapper"><i class="cera-icon cera-hard-drive"></i><span class="h5">Share documents</span></span>',
				'Discuss your projects' => '<span class="icon-wrapper"><i class="cera-icon cera-message-circle"></i><span class="h5">Discuss your projects</span></span>',
			) );

			$this->fix_menu_items_titles( 'Footer Social', array(
				'https://facebook.com/' => '<i class="fa fa-facebook"></i> Facebook',
				'https://twitter.com/'  => '<i class="fa fa-twitter"></i> Twitter',
				'https://slack.com/'    => '<i class="fa fa-slack"></i> Slack',
			), 'url' );

			$this->fix_menu_items_titles( 'User - Logged In', array(
				'Notifications' => '<i class="cera-icon cera-bell"></i> Notifications',
				'Profile'       => '<i class="cera-icon cera-user"></i> Profile',
				'Settings'      => '<i class="cera-icon cera-settings"></i> Settings',
				'Log out'       => '<i class="cera-icon cera-log-out"></i> Log out',
			) );

			$this->set_custom_logo( 'logo' );

			// BuddyPress adjustments
			if ( class_exists( 'BuddyPress' ) ) {
				$this->enable_user_registrations();

				$this->enable_bp_legacy();

				$this->generate_base_xprofile_fields();

				$this->generate_details_profile_fields();

				// Fix members directory search form
				$this->fix_bps_form( 'Search directory', array(
					'Name',
					'any',
					'Position',
					'Birthdate',
				), 'Members' );

				// Fix home search form
				$this->fix_bps_form( 'Search home', array(
					'Name',
					'Birthdate',
					'Position',
				), 'Members' );
			}

			$this->setup_epkb();

			$this->assign_pmpro_pages();

			// Change BuddyPress for LearnDash option
			$bp_lms_options = get_site_option( 'buddypress_learndash_plugin_options', array() );
			$bp_lms_options['courses_visibility'] = 'on';
			update_site_option( 'buddypress_learndash_plugin_options', $bp_lms_options );

			$this->adjust_yoast_settings();

			$this->activate_jetpack_modules( array(
				'carousel',
				'infinite-scroll',
				'tiled-gallery',
			) );
		}

		/**
		 * Generate profile fields in the "Base" tab
		 */
		private function generate_base_xprofile_fields() {
			// Generate First and Last name fields
			$this->generate_xprofile_field( array(
				'name'     => 'First Name',
				'type'     => 'textbox',
				'required' => true,
			) );
			$this->generate_xprofile_field( array(
				'name'     => 'Last Name',
				'type'     => 'textbox',
				'required' => true,
			) );

			// Generate Birthdate field.
			$this->generate_xprofile_field( array(
				'name'     => 'Birthdate',
				'type'     => 'datebox',
				'required' => true,
			) );

			// Generate Gender field.
			$this->generate_xprofile_field( array(
				'name'     => 'Gender',
				'type'     => 'selectbox',
				'required' => true,
				'choices'  => array(
					'Male',
					'Female',
					'Other',
				),
			) );
		}

		/**
		 * Generate profile fields in the "Details" tab
		 */
		private function generate_details_profile_fields() {
			$this->generate_xprofile_field_group( array(
				'id'   => 2,
				'name' => 'Details',
			) );

			$this->generate_xprofile_field( array(
				'name'        => 'Biographical Info',
				'type'        => 'textarea',
				'required'    => true,
				'field_group' => 2,
			) );

			$this->generate_xprofile_field( array(
				'name'        => 'Passion',
				'type'        => 'selectbox',
				'required'    => false,
				'choices'     => array(
					'Sport',
					'Travel',
					'Cooking',
					'Cinema',
					'Music',
					'Tatoo',
					'Books',
					'Gaming',
					'History',
				),
				'field_group' => 2,
			) );

			$this->generate_xprofile_field( array(
				'name'        => 'Position',
				'type'        => 'selectbox',
				'required'    => false,
				'choices'     => array(
					'Account manager',
					'Business analyst',
					'Chief brand officer',
					'Managing director',
					'Founder CEO',
					'Corporate development',
					'Chief solutions officer',
					'Systems analyst',
					'Purchasing manager',
				),
				'field_group' => 2,
			) );
		}

		/**
		 * Setup Knowledge Base for Documents and FAQs
		 */
		private function setup_epkb() {
			if ( class_exists( 'Echo_Knowledge_Base' ) ) {
				// Update epkb categories meta
				global $eckb_kb_id;
				$eckb_kb_id = 1;
				$category_admin = new EPKB_Categories_Admin();
				$category_admin->update_categories_sequence();
				$kb_page = get_page_by_path( 'knowledge-base' );

				// Assign icons to epkb categories
				$categories_icons = EPKB_Utilities::get_kb_option( EPKB_KB_Config_DB::DEFAULT_KB_ID, EPKB_Icons::CATEGORIES_ICONS, array(), true );

				/* @var WP_Term[] $epkb_categories */
				$epkb_categories = get_terms( array( 'taxonomy' => 'epkb_post_type_1_category', 'hide_empty' => false ) );
				foreach ( $epkb_categories as $epkb_category ) {
					switch( $epkb_category->slug ) {
						case 'business':
							$icon_name = 'epkbfa-street-view';
							break;
						case 'faqs':
							$icon_name = 'epkbfa-book';
							break;
						case 'introduction':
							$icon_name = 'ep_font_icon_gears';
							break;
						case 'other':
							$icon_name = 'epkbfa-cube';
							break;
						case 'pre-sale':
							$icon_name = 'ep_font_icon_book';
							break;
						case 'setup':
							$icon_name = 'epkbfa-upload';
							break;
						case 'topic-b':
							$icon_name = 'epkbfa-black-tie';
							break;
						case 'topic-c':
							$icon_name = 'epkbfa-bell';
							break;
						default:
							$icon_name = 'ep_font_icon_document';
							break;
					}

					$categories_icons[ $epkb_category->term_id ] = array(
						'type' => 'font',
						'name' => $icon_name,
						'image_id' => EPKB_Icons::DEFAULT_CATEGORY_IMAGE_ID,
						'image_size' => '',
						'image_thumbnail_url' => '',
						'color' => '#000000',
					);
				}

				EPKB_Utilities::save_kb_option( EPKB_KB_Config_DB::DEFAULT_KB_ID, EPKB_Icons::CATEGORIES_ICONS, $categories_icons, true );

				$epkb_config = get_option( EPKB_KB_Config_DB::KB_CONFIG_PREFIX . EPKB_KB_Config_DB::DEFAULT_KB_ID );
				if ( empty( $epkb_config ) ) {
					$epkb_config = array();
				}
				// Mark default knowledge base as "published" in epkb config to disable the wizard
				$epkb_config['status'] = EPKB_KB_Status::PUBLISHED;

				// Apply the "Organized" theme
				$epkb_themes = EPKB_KB_Wizard_Themes::$themes;
				if ( ! empty( $epkb_themes['theme_organized'] ) ) {
					$epkb_config = array_merge( $epkb_config, $epkb_themes['theme_organized'] );
				}

				$epkb_config['show_articles_before_categories'] = 'off';

				// Save knowledge base page id in epkb config
				if ( ! empty( $kb_page ) ) {
					$epkb_config['kb_main_pages'] = array( "{$kb_page->ID}" => $kb_page->post_title );
				}

				update_option( EPKB_KB_Config_DB::KB_CONFIG_PREFIX . EPKB_KB_Config_DB::DEFAULT_KB_ID, $epkb_config );
			}
		}
	}
endif;

return new Cera_Merlin();
