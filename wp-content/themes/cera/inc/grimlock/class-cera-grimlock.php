<?php
/**
 * Cera Grimlock Class
 *
 * @package  cera
 * @author   Themosaurus
 * @since    1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Cera_Grimlock' ) ) :
	/**
	 * The Cera Grimlock integration class
	 */
	class Cera_Grimlock {
		/**
		 * Setup class.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			add_action( 'init',                               array( $this, 'add_excerpt_support_for_pages'       ), 10    );
			add_action( 'widgets_init',                       array( $this, 'widgets_init'                        ), 10    );
			add_action( 'after_setup_theme',                  array( $this, 'setup'                               ), 10    );
			add_action( 'wp_enqueue_scripts',                 array( $this, 'dequeue_scripts'                     ), 20    );
			add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_customizer_controls_scripts' ), 20    );

			add_filter( 'excerpt_length',      array( $this, 'cera_custom_excerpt_length'),     999   );

			require get_template_directory() . '/inc/grimlock/component/class-cera-grimlock-hamburger-navbar-component.php';
			require get_template_directory() . '/inc/grimlock/component/class-cera-grimlock-vertical-navbar-component.php';

			global $grimlock;
			remove_action( 'grimlock_hamburger_navbar', array( $grimlock, 'hamburger_navbar' ), 10 );
			remove_action( 'grimlock_vertical_navbar',  array( $grimlock, 'vertical_navbar'  ), 10 );

			add_action( 'grimlock_hamburger_navbar', array( $this, 'hamburger_navbar' ), 10, 1 );
			add_action( 'grimlock_vertical_navbar',  array( $this, 'vertical_navbar'  ), 10, 1 );

			require_once get_template_directory() . '/inc/grimlock/customizer/class-cera-grimlock-dashboard-customizer.php';
			require_once get_template_directory() . '/inc/grimlock/customizer/class-cera-grimlock-site-identity-customizer.php';
			require_once get_template_directory() . '/inc/grimlock/customizer/class-cera-grimlock-global-customizer.php';
			require_once get_template_directory() . '/inc/grimlock/customizer/class-cera-grimlock-typography-customizer.php';
			require_once get_template_directory() . '/inc/grimlock/customizer/class-cera-grimlock-navigation-customizer.php';
			require_once get_template_directory() . '/inc/grimlock/customizer/class-cera-grimlock-preheader-customizer.php';
			require_once get_template_directory() . '/inc/grimlock/customizer/class-cera-grimlock-custom-header-customizer.php';
			require_once get_template_directory() . '/inc/grimlock/customizer/class-cera-grimlock-prefooter-customizer.php';
			require_once get_template_directory() . '/inc/grimlock/customizer/class-cera-grimlock-footer-customizer.php';
			require_once get_template_directory() . '/inc/grimlock/customizer/class-cera-grimlock-control-customizer.php';
			require_once get_template_directory() . '/inc/grimlock/customizer/class-cera-grimlock-button-customizer.php';
			require_once get_template_directory() . '/inc/grimlock/customizer/class-cera-grimlock-back-to-top-button-customizer.php';
			require_once get_template_directory() . '/inc/grimlock/customizer/class-cera-grimlock-pagination-customizer.php';
			require_once get_template_directory() . '/inc/grimlock/customizer/class-cera-grimlock-table-customizer.php';
			require_once get_template_directory() . '/inc/grimlock/customizer/class-cera-grimlock-loader-customizer.php';
			require_once get_template_directory() . '/inc/grimlock/customizer/class-cera-grimlock-archive-customizer.php';
			require_once get_template_directory() . '/inc/grimlock/customizer/class-cera-grimlock-search-customizer.php';
			require_once get_template_directory() . '/inc/grimlock/customizer/class-cera-grimlock-single-customizer.php';
			require_once get_template_directory() . '/inc/grimlock/customizer/class-cera-grimlock-page-customizer.php';
			require_once get_template_directory() . '/inc/grimlock/customizer/class-cera-grimlock-404-customizer.php';

			add_filter( 'grimlock_color_field_palettes',                                                array( $this, 'color_field_palettes'                          ), 10, 1 );
			add_filter( 'grimlock_section_widget_defaults',                                             array( $this, 'change_section_defaults'                       ), 10, 1 );
			add_filter( 'grimlock_section_block_defaults',                                              array( $this, 'change_section_defaults'                       ), 10, 1 );
			add_filter( 'grimlock_nav_menu_section_widget_defaults',                                    array( $this, 'change_nav_menu_section_defaults'              ), 10, 1 );
			add_filter( 'grimlock_nav_menu_section_block_defaults',                                     array( $this, 'change_nav_menu_section_defaults'              ), 10, 1 );
			add_filter( 'grimlock_query_section_widget_post_thumbnail_size',                            array( $this, 'change_query_section_post_thumbnail_size'      ), 10, 2 );
			add_filter( 'grimlock_query_section_block_post_thumbnail_size',                             array( $this, 'change_query_section_post_thumbnail_size'      ), 10, 2 );
			add_filter( 'grimlock_posts_section_widget_post_thumbnail_size',                            array( $this, 'change_query_section_post_thumbnail_size'      ), 10, 2 );
			add_filter( 'grimlock_posts_section_block_post_thumbnail_size',                             array( $this, 'change_query_section_post_thumbnail_size'      ), 10, 2 );
			add_filter( 'grimlock_the_events_calendar_tribe_events_section_widget_post_thumbnail_size', array( $this, 'change_query_section_post_thumbnail_size'      ), 10, 2 );
			add_filter( 'grimlock_the_events_calendar_tribe_events_section_block_post_thumbnail_size',  array( $this, 'change_query_section_post_thumbnail_size'      ), 10, 2 );
			add_filter( 'grimlock_term_query_section_widget_term_thumbnail_size',                       array( $this, 'change_term_query_section_term_thumbnail_size' ), 10, 2 );
			add_filter( 'grimlock_term_query_section_block_term_thumbnail_size',                        array( $this, 'change_term_query_section_term_thumbnail_size' ), 10, 2 );
			add_filter( 'grimlock_query_section_widget_defaults',                                       array( $this, 'change_query_section_defaults'                 ), 10, 1 );
			add_filter( 'grimlock_query_section_block_defaults',                                        array( $this, 'change_query_section_defaults'                 ), 10, 1 );


			//add_filter( 'grimlock_section_widget_layout_field_args',                   array( $this, 'change_layout_field_args' ), 10, 1 );
			add_filter( 'grimlock_404_customizer_layout_field_args',                   array( $this, 'change_layout_field_args' ), 10, 1 );
			add_filter( 'grimlock_custom_header_customizer_layout_field_args',         array( $this, 'change_layout_field_args' ), 10, 2 );
			add_filter( 'grimlock_single_customizer_custom_header_layout_field_args',  array( $this, 'change_layout_field_args' ), 10, 2 );
			add_filter( 'grimlock_page_customizer_custom_header_layout_field_args',    array( $this, 'change_layout_field_args' ), 10, 2 );
			add_filter( 'grimlock_archive_customizer_custom_header_layout_field_args', array( $this, 'change_layout_field_args' ), 10, 2 );
			add_filter( 'grimlock_search_customizer_custom_header_layout_field_args',  array( $this, 'change_layout_field_args' ), 10, 2 );

			add_filter( 'grimlock_site_identity_args',      array( $this, 'change_site_identity_args'   ), 10, 1 );
			add_filter( 'grimlock_preheader_args',          array( $this, 'change_preheader_args'       ), 10, 1 );
			add_filter( 'grimlock_prefooter_args',          array( $this, 'change_prefooter_args'       ), 10, 1 );
			add_filter( 'grimlock_custom_header_displayed', array( $this, 'has_custom_header_displayed' ), 10, 1 );
			add_filter( 'grimlock_content_class',           array( $this, 'add_content_classes'         ), 10, 1 );
			add_filter( 'grimlock_page_templates',          array( $this, 'remove_grimlock_templates'   ), 10, 1 );
		}

		/**
		 * Limit excerpt length.
		 *
		 * @since 1.0.0
		 */
		public function cera_custom_excerpt_length( $length ) {
			return 15;
		}

		/**
		 * Remove stylesheets.
		 *
		 * @since 1.0.0
		 */
		public function dequeue_scripts() {

			wp_dequeue_style(    'cera-google-fonts'        );
			wp_deregister_style( 'cera-google-fonts'        );
			wp_dequeue_script(   'cera-navigation-search'   );
			wp_dequeue_script(   'slideout'                 );
			wp_dequeue_script(   'cera-vertical-navigation' );
			wp_dequeue_style(    'cera-skeleton-styles'     );
			wp_deregister_style( 'cera-skeleton-styles'     );

			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			if ( apply_filters( 'cera_grimlock_priority_nav_enqueued', is_home() || is_date() ) ) {
				wp_enqueue_style(  'priority-nav', get_template_directory_uri() . '/assets/css/vendor/priority-nav-core.css', array(), '1.0.12' );
				wp_enqueue_script( 'priority-nav', get_template_directory_uri() . '/assets/js/vendor/priority-nav.min.js', array(), '1.0.12', true );
				wp_enqueue_script( 'cera-home', get_template_directory_uri() . '/assets/js/home' . $suffix . '.js', array( 'cera', 'priority-nav' ), CERA_VERSION, true );
			}

			if ( apply_filters( 'cera_grimlock_dashboard_enqueued', is_page_template( 'template-dashboard.php' ) ) ) {
				wp_enqueue_script( 'jquery-masonry' );
				wp_enqueue_script( 'cera-dashboard', get_template_directory_uri() . '/assets/js/dashboard' . $suffix . '.js', array( 'jquery', 'jquery-masonry' ), CERA_VERSION, true );
			}

			wp_enqueue_style( 'dashicons' );
		}

		/**
		 * Enqueue customizer scripts.
		 *
		 * @since 1.1.2
		 */
		public function enqueue_customizer_controls_scripts() {
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			wp_enqueue_script( 'cera-customizer-controls', get_template_directory_uri() . '/assets/js/customizer-controls' . $suffix . '.js', array( 'customize-controls' ), CERA_VERSION, true );
		}

		/**
		 * Define available colors in the palette of the color picker.
		 *
		 * @since 1.0.0
		 *
		 * @param  array $colors The color palette for the color picker.
		 *
		 * @return array         The updated color palette for the color picker.
		 */
		public function color_field_palettes( $colors ) {
			return array(
				CERA_BRAND_PRIMARY,
				CERA_BRAND_SECONDARY,
				CERA_BODY_COLOR,
				CERA_GRAY_DARK,
				CERA_GRAY_LIGHT,
				CERA_GRAY_LIGHTEST,
				CERA_BRAND_INFO,
				CERA_BRAND_SUCCESS,
			);
		}

		/**
		 * Change the default values for the section widget.
		 *
		 * @since 1.0.0
		 *
		 * @param  array $defaults The array of defaults values for the widget.
		 *
		 * @return array           The updated array of defaults values for the widget.
		 */
		public function change_section_defaults( $defaults ) {
			$defaults['background_color'] = CERA_SECTION_WIDGET_BACKGROUND_COLOR;
			$defaults['padding_y']        = CERA_SECTION_PADDING_Y; // Legacy widget default TODO: remove
			$defaults['padding_top']      = CERA_SECTION_PADDING_Y;
			$defaults['padding_bottom']   = CERA_SECTION_PADDING_Y;
			return $defaults;
		}

		/**
		 * Change the default values for the nav menu section widget.
		 *
		 * @since 1.0.0
		 *
		 * @param  array $defaults The array of defaults values for the widget.
		 *
		 * @return array           The updated array of defaults values for the widget.
		 */
		public function change_nav_menu_section_defaults( $defaults ) {
			$defaults['background_color'] = CERA_SECTION_WIDGET_BACKGROUND_COLOR;
			return $defaults;
		}

		/**
		 * Change the default values for the query section widget.
		 *
		 * @since 1.0.0
		 *
		 * @param  array $defaults The array of defaults values for the widget.
		 *
		 * @return array           The updated array of defaults values for the widget.
		 */
		public function change_query_section_defaults( $defaults ) {
			$defaults['background_color'] = CERA_SECTION_WIDGET_BACKGROUND_COLOR;
			return $defaults;
		}

		/**
		 * Change default layouts.
		 *
		 * @since 1.0.0
		 *
		 * @param array $args The array of layouts. Keys are filenames, values are translated names.
		 *
		 * @return array           The array of layouts.
		 */
		public function change_layout_field_args( $args ) {
			unset( $args['choices']['6-6-cols-left-modern'] );
			unset( $args['choices']['6-6-cols-left-reverse-modern'] );
			unset( $args['choices']['8-4-cols-left-modern'] );
			unset( $args['choices']['8-4-cols-left-reverse-modern'] );
			return $args;
		}

		/**
		 * Enables the Excerpt meta box in Page edit screen.
		 *
		 * @since 1.0.0
		 */
		public function add_excerpt_support_for_pages() {
			add_post_type_support( 'page', 'excerpt' );
		}

		/**
		 * Change props for the Site Identity component to display default logo.
		 *
		 * @since 1.0.0
		 *
		 * @param  array $args The array of props for the component.
		 *
		 * @return array       The filtered array of props for the component.
		 */
		public function change_site_identity_args( $args ) {
			$logo                = '<a href="' . esc_url( home_url( '/' ) ) . '" rel="home"><img src="' . esc_url( get_stylesheet_directory_uri() . '/assets/images/logo.png' ) . '" alt="logo" /></a>';
			$args['custom_logo'] = empty( $args['custom_logo'] ) ? $logo : $args['custom_logo'];
			return $args;
		}

		/**
		 * Change props for the Pre Header component.
		 *
		 * @since 1.0.0
		 *
		 * @param  array $args The array of props for the component.
		 *
		 * @return array       The filtered array of props for the component.
		 */
		public function change_preheader_args( $args ) {
			$args['displayed'] = is_active_sidebar( 'preheader-1' ) || is_active_sidebar( 'preheader-2' ) || is_active_sidebar( 'preheader-3' ) || is_active_sidebar( 'preheader-4' );
			return $args;
		}

		/**
		 * Change props for the Pre Footer component.
		 *
		 * @since 1.0.0
		 *
		 * @param  array $args The array of props for the component.
		 *
		 * @return array       The filtered array of props for the component.
		 */
		public function change_prefooter_args( $args ) {
			$args['displayed'] = is_active_sidebar( 'prefooter-1' ) || is_active_sidebar( 'prefooter-2' ) || is_active_sidebar( 'prefooter-3' ) || is_active_sidebar( 'prefooter-4' );
			return $args;
		}

		/**
		 * Override hamburger navbar component to modify its markups.
		 *
		 * @since 1.0.0
		 *
		 * @param array $args The array of arguments for the post component.
		 */
		public function hamburger_navbar( $args = array() ) {
			$post = new Cera_Grimlock_Hamburger_Navbar_Component( apply_filters( 'grimlock_hamburger_navbar_args', (array) $args ) );
			$post->render();
		}

		/**
		 * Override vertical navbar component to modify its markups.
		 *
		 * @since 1.0.0
		 *
		 * @param array $args The array of arguments for the post component.
		 */
		public function vertical_navbar( $args = array() ) {
			$post = new Cera_Grimlock_Vertical_Navbar_Component( apply_filters( 'grimlock_vertical_navbar_args', (array) $args ) );
			$post->render();
		}

		/**
		 * Sets up theme defaults and registers support for various WordPress features.
		 *
		 * Note that this function is hooked into the after_setup_theme hook, which
		 * runs before the init hook. The init hook is too late for some features, such
		 * as indicating support for post thumbnails.
		 *
		 * @since 1.0.0
		 */
		public function setup() {
			// This theme uses wp_nav_menu() in one location.
			register_nav_menus( apply_filters( 'cera_nav_menus', array(
				'secondary' => esc_html__( 'Secondary', 'cera' ),
			) ) );
		}

		/**
		 * Register widget areas for the front page.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
		 * @since 1.0.0
		 */
		public function widgets_init() {
			$widget_areas = apply_filters( 'cera_grimlock_widget_areas', array(
				'homepage-widget-area-20' => array(
					'id'          => 'homepage-1',
					'name'        => esc_html__( 'Homepage', 'cera' ),
					'description' => esc_html__( 'The area replacing the content for the homepage template.', 'cera' ),
				),
				'widget-area-15' => array(
					'id'          => 'vertical-navbar-1',
					'name'        => esc_html__( 'Vertical Navbar Top', 'cera' ),
					'description' => esc_html__( 'The Vertical Navbar Top Area.', 'cera' ),
				),
				'widget-area-16' => array(
					'id'          => 'vertical-navbar-2',
					'name'        => esc_html__( 'Vertical Navbar Bottom', 'cera' ),
					'description' => esc_html__( 'The Vertical Navbar Bottom Area.', 'cera' ),
				),
			) );

			ksort( $widget_areas );
			foreach ( $widget_areas as $widget_area ) {
				register_sidebar( wp_parse_args( $widget_area, array(
					'name'          => '',
					'id'            => '',
					'description'   => '',
					'before_widget' => '<section id="%1$s" class="widget %2$s">',
					'after_widget'  => '</section>',
					'before_title'  => '<span class="widget-title">',
					'after_title'   => '</span>',
				) ) );
			}
		}

		/**
		 * Add custom classes to content to modify layout.
		 *
		 * @since 1.0.0
		 *
		 * @param  array $classes The array of CSS classes for the content.
		 *
		 * @return array          The updated array of CSS classes for the content.
		 */
		public function add_content_classes( $classes ) {
			if ( is_page() ) {
				$page_template = get_page_template_slug( get_queried_object_id() );

				switch ( $page_template ) {
					case 'template-narrower-12-cols-left.php':
						$classes[] = 'region--12-cols-left';
						$classes[] = 'region--container-narrower';
						break;

					case 'template-narrow-12-cols-left.php':
						$classes[] = 'region--12-cols-left';
						$classes[] = 'region--container-narrow';
						break;

					case 'template-classic-12-cols-left.php':
						$classes[] = 'region--12-cols-left';
						$classes[] = 'region--container-fluid';
						break;

					case 'template-minimal.php':
						$classes[] = 'region--12-cols-left';
						$classes[] = 'region--container-classic';
						break;

					case 'template-classic-9-3-cols-left.php':
						$classes[] = 'region--9-3-cols-left';
						$classes[] = 'region--container-fluid';
						break;

					case 'template-classic-3-9-cols-left.php':
						$classes[] = 'region--3-9-cols-left';
						$classes[] = 'region--container-fluid';
						break;

					default:
						$classes[] = 'region--3-6-3-cols-left';
						$classes[] = 'region--container-fluid';
				}
			}
			return $classes;
		}

		/**
		 * Check if the custom header is displayed or not.
		 *
		 * @since 1.1.9
		 *
		 * @param bool $default True if the custom header would be displayed, false otherwise.
		 *
		 * @return bool True if the custom header is displayed, false otherwise.
		 */
		public function has_custom_header_displayed( $default ) {
			return ! is_page_template( 'template-dashboard.php' ) && ! is_page_template( 'template-minimal.php' ) && ! is_page_template( 'template-homepage-minimal.php' ) && $default;
		}

		/**
		 * Change default post thumbnail sizes for the query section widget.
		 *
		 * @since 1.0.0
		 *
		 * @param string $size         The size for the post thumbnail.
		 * @param string $posts_layout The layout for the query.
		 *
		 * @return string              The updated size for the post thumbnail.
		 */
		public function change_query_section_post_thumbnail_size( $size, $posts_layout ) {
			switch ( $posts_layout ) {
				case '12-cols-overlay':
				case '6-6-cols-overlay':
				case '4-4-4-cols-overlay':
				case '3-3-3-3-cols-overlay':
				case '12-cols-classic-slider':
				case '6-6-cols-classic-slider':
				case '4-4-4-cols-classic-slider':
				case '3-3-3-3-cols-classic-slider':
				case '12-cols-overlay-slider':
				case '6-6-cols-overlay-slider':
				case '4-4-4-cols-overlay-slider':
					$size = str_replace( array('overlay', '-slider'), array('classic', ''), "thumbnail-{$posts_layout}" );
					break;
				case '3-3-3-3-cols-overlay-grid':
				case '4-4-4-cols-overlay-grid':
				case '3-3-3-3-cols-overlay-slider':
					$size = "thumbnail-4-4-4-cols-classic";
					break;
				case '12-cols-lateral-reverse':
					$size = 'thumbnail-12-cols-lateral';
					break;
				case '6-6-cols-lateral-reverse':
				case '6-6-cols-lateral-modern-alternate':
					$size = 'thumbnail-6-6-cols-lateral';
					break;
				default:
					$size = "thumbnail-{$posts_layout}";
			}
			return $size;
		}

		/**
		 * Change default term thumbnail sizes for the query section widget.
		 *
		 * @since 1.0.1
		 *
		 * @param string $size         The size for the term thumbnail.
		 * @param string $terms_layout The layout for the term query.
		 *
		 * @return string              The updated size for the term thumbnail.
		 */
		public function change_term_query_section_term_thumbnail_size( $size, $terms_layout ) {
			return "thumbnail-{$terms_layout}";
		}

		/**
		 * Remove Grimlock page templates except the "Full Width: No Header" template
		 *
		 * @param array $templates Array of templates
		 *
		 * @return array Modified array of templates
		 */
		public function remove_grimlock_templates( $templates ) {
			return array( 'template-full-width-no-header.php' => $templates['template-full-width-no-header.php'] );
		}
	}
endif;

return new Cera_Grimlock();
