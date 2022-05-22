<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class Grimlock
 *
 * @author  themosaurus
 * @since   1.0.0
 * @package grimlock/inc
 */
class Grimlock {
	/**
	 * Setup plugin class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		load_plugin_textdomain( 'grimlock', false, 'grimlock/languages' );

		// Initialize template hooks and functions
		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/grimlock-template-functions.php';
		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/grimlock-template-hooks.php';

		// Initialize components.
		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/component/class-grimlock-component.php';
		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/component/class-grimlock-region-component.php';
		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/component/class-grimlock-site-identity-component.php';
		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/component/class-grimlock-navbar-component.php';
		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/component/class-grimlock-vertical-navbar-component.php';
		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/component/class-grimlock-hamburger-navbar-component.php';
		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/component/class-grimlock-section-component.php';
		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/component/class-grimlock-nav-menu-section-component.php';
		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/component/class-grimlock-custom-header-component.php';
		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/component/class-grimlock-loader-component.php';
		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/component/class-grimlock-back-to-top-button-component.php';
		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/component/class-grimlock-navigation-component.php';
		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/component/class-grimlock-vertical-navigation-component.php';
		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/component/class-grimlock-post-component.php';
		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/component/class-grimlock-page-component.php';
		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/component/class-grimlock-404-component.php';
		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/component/class-grimlock-single-component.php';
		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/component/class-grimlock-query-section-component.php';
		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/component/class-grimlock-query-section-component.php';
		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/component/class-grimlock-terms-component.php';
		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/component/class-grimlock-term-component.php';
		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/component/class-grimlock-term-query-section-component.php';
		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/component/class-grimlock-divider-component.php';

		add_action( 'grimlock_region',                  array( $this, 'region'                         ), 10, 1 );
		add_action( 'grimlock_preheader',               array( $this, 'preheader'                      ), 10, 1 );
		add_action( 'grimlock_header',                  array( $this, 'header'                         ), 10, 1 );
		add_action( 'grimlock_prefooter',               array( $this, 'prefooter'                      ), 10, 1 );
		add_action( 'grimlock_footer',                  array( $this, 'footer'                         ), 10, 1 );
		add_action( 'grimlock_site_identity',           array( $this, 'site_identity'                  ), 10, 1 );
		add_action( 'grimlock_navigation',              array( $this, 'navigation'                     ), 10, 1 );
		add_action( 'grimlock_vertical_navigation',     array( $this, 'vertical_navigation'            ), 10, 1 );
		add_action( 'grimlock_navbar',                  array( $this, 'navbar'                         ), 10, 1 );
		add_action( 'grimlock_hamburger_navbar',        array( $this, 'hamburger_navbar'               ), 10, 1 );
		add_action( 'grimlock_vertical_navbar',         array( $this, 'vertical_navbar'                ), 10, 1 );
		add_action( 'grimlock_section',                 array( $this, 'section'                        ), 10, 1 );
		add_action( 'grimlock_nav_menu_section',        array( $this, 'nav_menu_section'               ), 10, 1 );
		add_action( 'grimlock_custom_header',           array( $this, 'custom_header'                  ), 10, 1 );
		add_action( 'grimlock_loader',                  array( $this, 'loader'                         ), 10, 1 );
		add_action( 'grimlock_back_to_top_button',      array( $this, 'back_to_top_button'             ), 10, 1 );

		add_action( 'grimlock_post',                    array( $this, 'post'                           ), 10, 1 );
		add_action( 'grimlock_search_post',             array( $this, 'search_post'                    ), 10, 1 );
		add_action( 'grimlock_single',                  array( $this, 'single'                         ), 10, 1 );
		add_action( 'grimlock_page',                    array( $this, 'page'                           ), 10, 1 );
		add_action( 'grimlock_404',                     array( $this, '_404'                           ), 10, 1 );
		add_action( 'grimlock_query_section',           array( $this, 'query_section'                  ), 10, 1 );
		add_action( 'grimlock_query_post',              array( $this, 'query_post'                     ), 10, 1 );
		add_action( 'grimlock_terms',                   array( $this, 'terms'                          ), 10, 1 );
		add_action( 'grimlock_term',                    array( $this, 'term'                           ), 10, 1 );
		add_action( 'grimlock_term_query_category',     array( $this, 'term_query_category'            ), 10, 1 );
		add_action( 'grimlock_term_query_section',      array( $this, 'term_query_section'             ), 10, 1 );
		add_action( 'grimlock_divider',                 array( $this, 'divider'                        ), 10, 1 );

		add_filter( 'body_class',                       array( $this, 'body_class'                     ), 10, 1 );
		add_filter( 'grimlock_posts_class',             array( $this, 'posts_class'                    ), 10, 1 );
		add_filter( 'walker_nav_menu_start_el',         array( $this, 'nav_menu_description'           ), 10, 4 );

		// Add custom page templates
		add_filter( 'theme_page_templates',             array( $this, 'add_page_templates'             ), 10,  1 );
		add_filter( 'template_include',                 array( $this, 'get_page_template_path'         ), 10,  1 );
		add_filter( 'grimlock_content_class',           array( $this, 'add_content_classes'            ), 100, 1 );
		add_filter( 'grimlock_custom_header_displayed', array( $this, 'change_custom_header_displayed' ), 100, 1 );

		// Hide components when using the "Canvas" page template
		add_filter( 'grimlock_vertical_navigation_args', array( $this, 'hide_component_in_canvas_template' ), 100, 1 );
		add_filter( 'grimlock_preheader_args',           array( $this, 'hide_component_in_canvas_template' ), 100, 1 );
		add_filter( 'grimlock_header_args',              array( $this, 'hide_component_in_canvas_template' ), 100, 1 );
		add_filter( 'grimlock_navigation_args',          array( $this, 'hide_component_in_canvas_template' ), 100, 1 );
		add_filter( 'grimlock_prefooter_args',           array( $this, 'hide_component_in_canvas_template' ), 100, 1 );
		add_filter( 'grimlock_footer_args',              array( $this, 'hide_component_in_canvas_template' ), 100, 1 );

		// Allow posts html in menu item description
		remove_filter( 'nav_menu_description',                 'strip_tags'                       );
		add_filter(    'wp_setup_nav_menu_item', array( $this, 'sanitize_menu_item_description' ) );

		// Initialize WP-AJAX features.
		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/class-grimlock-ajax.php';

		// Initialize widgets.
		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/widget/class-grimlock-base-widget.php';
		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/widget/fields/class-grimlock-base-widget-fields.php';
		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/widget/class-grimlock-section-widget.php';
		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/widget/fields/class-grimlock-section-widget-fields.php';
		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/widget/class-grimlock-query-section-widget.php';
		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/widget/fields/class-grimlock-query-section-widget-fields.php';
		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/widget/class-grimlock-posts-section-widget.php';
		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/widget/fields/class-grimlock-posts-section-widget-fields.php';
		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/widget/class-grimlock-nav-menu-section-widget.php';
		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/widget/fields/class-grimlock-nav-menu-section-widget-fields.php';
		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/widget/class-grimlock-term-query-section-widget.php';
		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/widget/fields/class-grimlock-term-query-section-widget-fields.php';

		// Initialize blocks.
		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/block/class-grimlock-base-block.php';
		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/block/class-grimlock-section-block.php';
		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/block/class-grimlock-query-section-block.php';
		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/block/class-grimlock-posts-section-block.php';
		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/block/class-grimlock-nav-menu-section-block.php';
		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/block/class-grimlock-term-query-section-block.php';
		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/block/class-grimlock-divider-block.php';

		add_action( 'widgets_init',                       array( $this, 'widgets_init'                ), 10 );
		add_action( 'wp_enqueue_scripts',                 array( $this, 'enqueue_scripts'             ), 10 );
		add_action( 'admin_enqueue_scripts',              array( $this, 'admin_enqueue_scripts'       ), 10 );
		add_action( 'enqueue_block_editor_assets',        array( $this, 'enqueue_block_editor_assets' ), 10 );

		// Extend Customizer features.
		add_action( 'after_setup_theme',                  array( $this, 'add_customizer_fields'                 ), 20    );
		add_action( 'after_setup_theme',                  array( $this, 'add_image_sizes'                       ), 20    );
		add_filter( 'image_size_names_choose',            array( $this, 'add_image_size_names'                  ), 20, 1 );
		add_filter( 'kirki/config',                       array( $this, 'change_kirki_config'                   ), 10, 1 );
		add_action( 'customize_register',                 array( $this, 'register_kirki_installer'              ), 10, 1 );
		add_action( 'customize_register',                 array( $this, 'register_controls'                     ), 10, 1 );
		add_action( 'customize_preview_init',             array( $this, 'customize_preview_init'                ), 20    );
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'customize_controls_enqueue_scripts'    ), 20    );
		add_action( 'init',                               array( $this, 'disable_kirki_inline_styles'           ), 9     );
		add_filter( 'grimlock_color_field_palettes',      array( $this, 'color_field_palettes'                  ), 10    );
		add_filter( 'kirki_default_color_swatches',       array( $this, 'kirki_default_color_swatches'          ), 10    );
		add_filter( 'kirki_get_value',                    array( $this, 'kirki_fix_typography_values'           ), 20, 4 );

		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/customizer/trait-grimlock-custom-header.php';
		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/customizer/class-grimlock-base-customizer.php';
		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/customizer/class-grimlock-site-identity-customizer.php';
		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/customizer/class-grimlock-global-customizer.php';
		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/customizer/class-grimlock-typography-customizer.php';
		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/customizer/class-grimlock-navigation-customizer.php';
		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/customizer/class-grimlock-region-customizer.php';
		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/customizer/class-grimlock-preheader-customizer.php';
		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/customizer/class-grimlock-custom-header-customizer.php';
		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/customizer/class-grimlock-prefooter-customizer.php';
		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/customizer/class-grimlock-footer-customizer.php';
		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/customizer/class-grimlock-control-customizer.php';
		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/customizer/class-grimlock-button-customizer.php';
		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/customizer/class-grimlock-back-to-top-button-customizer.php';
		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/customizer/class-grimlock-pagination-customizer.php';
		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/customizer/class-grimlock-table-customizer.php';
		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/customizer/class-grimlock-loader-customizer.php';
		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/customizer/class-grimlock-template-customizer.php';
		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/customizer/class-grimlock-grid-template-customizer.php';
		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/customizer/class-grimlock-singular-template-customizer.php';
		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/customizer/class-grimlock-archive-customizer.php';
		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/customizer/class-grimlock-home-customizer.php';
		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/customizer/class-grimlock-search-customizer.php';
		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/customizer/class-grimlock-single-customizer.php';
		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/customizer/class-grimlock-page-customizer.php';
		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/customizer/class-grimlock-static-front-page-customizer.php';
		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/customizer/class-grimlock-404-customizer.php';
	}

	/**
	 * Adds custom classes to the array of body classes.
	 *
	 * @since 1.0.0
	 * @param array $classes Classes for the body element.
	 *
	 * @return array
	 */
	public function body_class( $classes ) {
		$classes[] = 'grimlock';

		if ( $this->has_custom_header_displayed() ) {
			$classes[] = 'grimlock--custom_header-displayed';

			// TODO: Remove deprecated classes.
			$classes[] = 'grimlock--custom_header-title-displayed';
			$classes[] = 'grimlock--custom_header-subtitle-displayed';
		}
		return $classes;
	}

	/**
	 * Adds custom classes for the #posts div.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $classes One or more classes to add to the class list.
	 * @return array          Array of classes.
	 */
	public function posts_class( $classes ) {
		$classes[] = 'posts';

		if ( is_home() ) {
			$classes[] = 'blog-posts';
		} elseif ( is_archive() ) {
			$classes[] = 'archive-posts';
		} elseif ( is_search() ) {
			$classes[] = 'search-posts';
		}
		return $classes;
	}

	/**
	 * Add menu item description to nav menu output
	 *
	 * @param $item_output string Nav menu output
	 * @param $item WP_Post Menu item object
	 * @param $depth int Depth of the menu item
	 * @param $args stdClass Object containing wp_nav_menu() arguments
	 *
	 * @return string Nav menu output
	 */
	function nav_menu_description( $item_output, $item, $depth, $args ) {
		if ( !empty( $item->description ) ) {
			$item_output = str_replace( $args->link_after . '</a>', '<div class="menu-item-description">' . $item->description . '</div>' . $args->link_after . '</a>', $item_output );
		}
		return $item_output;
	}

	/**
	 * Get the list of Grimlock page templates
	 *
	 * @return array The list of page templates added by Grimlock
	 */
	public function get_page_templates() {
		return apply_filters( 'grimlock_page_templates', array(
			'template-sidebar-left.php'         => esc_html__( 'Sidebar Left', 'grimlock' ),
			'template-sidebar-right.php'        => esc_html__( 'Sidebar Right', 'grimlock' ),
			'template-full-width.php'           => esc_html__( 'Full Width', 'grimlock' ),
			'template-full-width-narrow.php'    => esc_html__( 'Full Width: Narrow', 'grimlock' ),
			'template-full-width-narrower.php'  => esc_html__( 'Full Width: Narrower', 'grimlock' ),
			'template-full-width-no-header.php' => esc_html__( 'Full Width: No Header', 'grimlock' ),
			'template-canvas.php'               => esc_html__( 'Canvas', 'grimlock' ),
		) );
	}

	/**
	 * Add custom page templates to the list of theme page templates
	 *
	 * @param array $page_templates The array containing all the theme page templates
	 *
	 * @return array The modified array of page templates
	 */
	public function add_page_templates( $page_templates ) {
		$grimlock_page_templates = $this->get_page_templates();

		if ( ! empty( $grimlock_page_templates ) && is_array( $grimlock_page_templates ) ) {
			$page_templates = array_merge( $page_templates, $grimlock_page_templates );
		}

		return $page_templates;
	}

	/**
	 * Get the real page template path when one of the custom page templates is being used
	 *
	 * @param string $template The template path
	 *
	 * @return string The modified template path
	 */
	public function get_page_template_path( $template ) {
		global $post;

		if ( empty( $post ) || ! is_singular() ) {
			return $template;
		}

		$page_template           = get_post_meta( $post->ID, '_wp_page_template', true );
		$grimlock_page_templates = $this->get_page_templates();

		if ( ! isset( $grimlock_page_templates[ $page_template ] ) ) {
			return $template;
		}

		$new_template = $this->locate_template( 'page-templates/' . $page_template );

		if ( empty( $new_template ) ) {
			return $template;
		}

		return $new_template;
	}

	/**
	 * Locate a template and return the path for inclusion.
	 *
	 * This is the load order:
	 *
	 * themes/yourtheme/grimlock/$template_name
	 * plugins/grimlock/$template_name
	 *
	 * @param string $template_name Template name.
	 * @return string
	 */
	public static function locate_template( $template_name ) {
		// Look within passed path within the theme - this is priority.
		$template = locate_template( array( 'grimlock/' . $template_name ) );

		// Get default template.
		if ( ! $template ) {
			$template = GRIMLOCK_PLUGIN_DIR_PATH . 'templates/' . $template_name;
		}

		// Return what we found.
		return apply_filters( 'grimlock_locate_template', $template, $template_name );
	}

	/**
	 * Add content classes
	 *
	 * @param array $classes Array of classes
	 *
	 * @return array Modified array of classes
	 */
	public function add_content_classes( $classes ) {
		if ( is_page() ) {
			$page_template = get_page_template_slug( get_queried_object_id() );

			switch ( $page_template ) {
				case 'template-sidebar-left.php':
					$classes[] = 'region--container-classic';
					$classes[] = 'region--3-9-cols-left';
					$classes = array_diff( $classes, array( 'region--3-6-3-cols-left' ) );
					break;
				case 'template-sidebar-right.php':
					$classes[] = 'region--container-classic';
					$classes[] = 'region--9-3-cols-left';
					$classes = array_diff( $classes, array( 'region--3-6-3-cols-left' ) );
					break;
				case 'template-full-width.php':
				case 'template-full-width-no-header.php':
					$classes[] = 'region--container-classic';
					$classes = array_diff( $classes, array( 'region--3-6-3-cols-left' ) );
					break;
				case 'template-full-width-narrow.php':
					$classes[] = 'region--container-narrow';
					$classes = array_diff( $classes, array( 'region--3-6-3-cols-left', 'region--container-classic' ) );
					break;
				case 'template-full-width-narrower.php':
					$classes[] = 'region--container-narrower';
					$classes = array_diff( $classes, array( 'region--3-6-3-cols-left', 'region--container-classic' ) );
					break;
				case 'template-canvas.php':
					$classes[] = 'region--container-fluid';
					$classes = array_diff( $classes, array( 'region--3-6-3-cols-left', 'region--container-classic' ) );
					break;
			}
		}

		return $classes;
	}

	/**
	 * Change whether the custom header is displayed
	 *
	 * @since 1.3.5
	 *
	 * @param bool $default True if the custom header would be displayed, false otherwise.
	 *
	 * @return bool True if the custom header is displayed, false otherwise.
	 */
	public function change_custom_header_displayed( $default ) {
		$is_no_header_page_template = is_page_template( 'template-full-width-no-header.php' ) || is_page_template( 'template-canvas.php' );

		if ( $is_no_header_page_template ) {
			remove_action( 'grimlock_page_template', 'grimlock_page_header', 20 );
		}

		return ! $is_no_header_page_template && $default;
	}

	/**
	 * Hide a component when using the "Canvas" template
	 *
	 * @param array $args Component args
	 *
	 * @return array Modified component args
	 */
	public function hide_component_in_canvas_template( $args ) {
		if ( is_page_template( 'template-canvas.php' ) ) {
			$args['displayed'] = false;
		}

		return $args;
	}

	/**
	 * Sanitize menu item description to allow posts html.
	 *
	 * @param WP_Post $menu_item Menu item object.
	 *
	 * @return WP_Post Nav menu item.
	 */
	public function sanitize_menu_item_description( $menu_item ) {
		if ( 'nav_menu_item' === $menu_item->post_type ) {
			$menu_item->description = apply_filters( 'nav_menu_description', wp_kses_post( trim( $menu_item->post_content ) ) );
		}
		return $menu_item;
	}

	/**
	 * Display the back to top button component.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	public function back_to_top_button( $args = array() ) {
		$component = new Grimlock_Back_To_Top_Button_Component( apply_filters( 'grimlock_back_to_top_button_args', (array) $args ) );
		$component->render();
	}

	/**
	 * Display the loader component.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	public function loader( $args = array() ) {
		$component = new Grimlock_Loader_Component( apply_filters( 'grimlock_loader_args', (array) $args ) );
		$component->render();
	}

	/**
	 * Display the region component.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	public function region( $args = array() ) {
		$component = new Grimlock_Region_Component( apply_filters( 'grimlock_region_args', (array) $args ) );
		$component->render();
	}

	/**
	 * Display the preheader region component.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	public function preheader( $args = array() ) {
		$args = apply_filters( 'grimlock_preheader_args', wp_parse_args( $args, array(
			'id'    => 'preheader',
			'class' => 'site-preheader',
		) ) );
		$component = new Grimlock_Region_Component( $args );
		$component->render();
	}

	/**
	 * Display the header region component.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	public function header( $args = array() ) {
		$args = apply_filters( 'grimlock_header_args', wp_parse_args( $args, array(
			'el'    => 'header',
			'id'    => 'header',
			'class' => 'site-header',
		) ) );
		$component = new Grimlock_Region_Component( $args );
		$component->render();
	}

	/**
	 * Display the prefooter region component.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	public function prefooter( $args = array() ) {
		$args = apply_filters( 'grimlock_prefooter_args', wp_parse_args( $args, array(
			'id'    => 'prefooter',
			'class' => 'site-prefooter d-print-none',
		) ) );
		$component = new Grimlock_Region_Component( $args );
		$component->render();
	}

	/**
	 * Display the footer region component.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	public function footer( $args = array() ) {
		$args = apply_filters( 'grimlock_footer_args', wp_parse_args( $args, array(
			'el'    => 'footer',
			'id'    => 'footer',
			'class' => 'site-footer d-print-none',
		) ) );
		$component = new Grimlock_Region_Component( $args );
		$component->render();
	}

	/**
	 * Display the navbar component.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	public function navbar( $args = array() ) {
		$component = new Grimlock_Navbar_Component( apply_filters( 'grimlock_navbar_args', (array) $args ) );
		$component->render();
	}

	/**
	 * Display the hamburger navbar component.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	public function hamburger_navbar( $args = array() ) {
		$component = new Grimlock_Hamburger_Navbar_Component( apply_filters( 'grimlock_hamburger_navbar_args', (array) $args ) );
		$component->render();
	}


	/**
	 * Display the aside navbar component.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	public function vertical_navbar( $args = array() ) {
		$component = new Grimlock_Vertical_Navbar_Component( apply_filters( 'grimlock_vertical_navbar_args', (array) $args ) );
		$component->render();
	}

	/**
	 * Display the navigation component.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	public function navigation( $args = array() ) {
		$args = apply_filters( 'grimlock_navigation_args', wp_parse_args( $args, array(
			'el'    => 'nav',
			'id'    => 'navigation',
			'class' => array( 'site-navigation', 'main-navigation' ),
		) ) );
		$component = new Grimlock_Navigation_Component( $args );
		$component->render();
	}

	/**
	 * Display the vertical navigation component.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	public function vertical_navigation( $args = array() ) {
		$args = apply_filters( 'grimlock_vertical_navigation_args', wp_parse_args( $args, array(
			'el'    => 'nav',
			'id'    => 'vertical-navigation',
			'class' => 'main-navigation',
		) ) );
		$component = new Grimlock_Vertical_Navigation_Component( $args );
		$component->render();
	}

	/**
	 * Display the site identity component.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	public function site_identity( $args = array() ) {
		$args = apply_filters( 'grimlock_site_identity_args', wp_parse_args( $args, array(
			'id' => 'site_identity',
		) ) );
		$component = new Grimlock_Site_Identity_Component( $args );
		$component->render();
	}

	/**
	 * Display the section component.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	public function section( $args = array() ) {
		$component = new Grimlock_Section_Component( apply_filters( 'grimlock_section_args', (array) $args ) );
		$component->render();
	}

	/**
	 * Display the nav menu section component.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	public function nav_menu_section( $args = array() ) {
		$component = new Grimlock_Nav_Menu_Section_Component( apply_filters( 'grimlock_nav_menu_section_args', (array) $args ) );
		$component->render();
	}

	/**
	 * Display the custom header component.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	public function custom_header( $args = array() ) {
		$args = apply_filters( 'grimlock_custom_header_args', wp_parse_args( $args, array(
			'id'               => 'custom_header',
			'displayed'        => $this->has_custom_header_displayed(),
			'background_image' => get_header_image(),
		) ) );
		$component = new Grimlock_Custom_Header_Component( $args );
		$component->render();
	}

	/**
	 * Check if the custom header is displayed or not.
	 *
	 * @since 1.0.7
	 *
	 * @return bool True if the custom header is displayed, false otherwise.
	 */
	public function has_custom_header_displayed() {
		return apply_filters( 'grimlock_custom_header_displayed', has_header_image() );
	}

	/**
	 * Display the list of taxonomy terms as nav.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	public function terms( $args = array() ) {
		$component = new Grimlock_Terms_Component( apply_filters( 'grimlock_terms_args', (array) $args ) );
		$component->render();
	}

	/**
	 * Display the page component.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	public function page( $args = array() ) {
		$component = new Grimlock_Page_Component( apply_filters( 'grimlock_page_args', (array) $args ) );
		$component->render();
	}

	/**
	 * Display the 404 page component.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	public function _404( $args = array() ) {
		$component = new Grimlock_404_Component( apply_filters( 'grimlock_404_args', (array) $args ) );
		$component->render();
	}

	/**
	 * Display the post component.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	public function post( $args = array() ) {
		$component = new Grimlock_Post_Component( apply_filters( 'grimlock_post_args', (array) $args ) );
		$component->render();
	}

	/**
	 * Display the search post component.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	public function search_post( $args = array() ) {
		$component = new Grimlock_Post_Component( apply_filters( 'grimlock_search_post_args', (array) $args ) );
		$component->render();
	}

	/**
	 * Display the query post component.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	public function query_post( $args = array() ) {
		$component = new Grimlock_Post_Component( apply_filters( 'grimlock_query_post_args', (array) $args ) );
		$component->render();
	}

	/**
	 * Display the single component.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	public function single( $args = array() ) {
		$component = new Grimlock_Single_Component( apply_filters( 'grimlock_single_args', (array) $args ) );
		$component->render();
	}

	/**
	 * Display the query section component.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	public function query_section( $args = array() ) {
		$component = new Grimlock_Query_Section_Component( apply_filters( 'grimlock_query_section_args', (array) $args ) );
		$component->render();
	}

	/**
	 * Display the term query section component.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	public function term_query_section( $args = array() ) {
		$component = new Grimlock_Term_Query_Section_Component( apply_filters( 'grimlock_term_query_section_args', (array) $args ) );
		$component->render();
	}

	/**
	 * Display the term component.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	public function term( $args = array() ) {
		$component = new Grimlock_Term_Component( apply_filters( 'grimlock_term_args', (array) $args ) );
		$component->render();
	}

	/**
	 * Display the term component for an unspecified taxonomy.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	public function term_query_category( $args = array() ) {
		$component = new Grimlock_Term_Component( apply_filters( 'grimlock_term_query_category_args', (array) $args ) );
		$component->render();
	}

	/**
	 * Display the divider component.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	public function divider( $args = array() ) {
		$component = new Grimlock_Divider_Component( apply_filters( 'grimlock_divider_args', (array) $args ) );
		$component->render();
	}

	/**
	 * Enqueue scripts
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_style( 'grimlock', GRIMLOCK_PLUGIN_DIR_URL . 'assets/css/style.css', array(), GRIMLOCK_VERSION );

		/*
		 * Load style-rtl.css instead of style.css for RTL compatibility
		 */
		wp_style_add_data( 'grimlock', 'rtl', 'replace' );
	}

	/**
	 * Enqueue scripts and stylesheets in admin pages for the widgets.
	 *
	 * @since 1.0.0
	 */
	public function admin_enqueue_scripts() {

		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style( 'grimlock-widgets', GRIMLOCK_PLUGIN_DIR_URL . 'assets/css/widgets.css' );

		// Enqueue scripts.
		wp_enqueue_media();
		wp_enqueue_script( 'jquery-ui-button' );

     	wp_enqueue_script( 'wp-color-picker-alpha', GRIMLOCK_PLUGIN_DIR_URL . 'assets/js/vendor/wp-color-picker-alpha.min.js', array( 'wp-color-picker' ), '2.1.4', true );

     	wp_enqueue_script( 'select2', GRIMLOCK_PLUGIN_DIR_URL . 'assets/js/vendor/select2.min.js', array( 'jquery' ), '4.0.13', true );
     	wp_enqueue_style( 'select2', GRIMLOCK_PLUGIN_DIR_URL . 'assets/css/vendor/select2.min.css', array(), '4.0.13' );

		wp_enqueue_script( 'grimlock-widgets', GRIMLOCK_PLUGIN_DIR_URL . 'assets/js/widgets.js', array( 'jquery', 'jquery-ui-button' ), GRIMLOCK_VERSION, true );
		wp_localize_script( 'grimlock-widgets', 'grimlock_widgets', apply_filters( 'grimlock_widgets_js_data', array(
			'frame_title' => esc_html__( 'Select Image', 'grimlock' ),
			'button_text' => esc_html__( 'Select', 'grimlock' ),
			'ajax_nonce'  => wp_create_nonce( 'grimlock_ajax_terms' ),
			'rest_url'    => get_rest_url( null, '/wp/v2/' ),
		) ) );
	}

	/**
	 * Enqueue scripts and stylesheets in the block editor
	 *
	 * @since 1.0.0
	 */
	public function enqueue_block_editor_assets() {
		wp_register_style( 'grimlock-blocks', GRIMLOCK_PLUGIN_DIR_URL . "assets/css/blocks-editor-styles.css", array( 'wp-edit-blocks' ), GRIMLOCK_VERSION );

		// Automatically load script dependencies and version from webpack compiled file
		$asset_file = include( GRIMLOCK_PLUGIN_DIR_PATH . 'assets/js/block/build/blocks.asset.php' );
		wp_register_script( 'grimlock-blocks', GRIMLOCK_PLUGIN_DIR_URL . 'assets/js/block/build/blocks.js', array_merge( $asset_file['dependencies'], array( 'wp-edit-post' ) ), GRIMLOCK_VERSION, true );
		wp_localize_script( 'grimlock-blocks', 'grimlock_blocks', apply_filters( 'grimlock_blocks_js_data', array( 'blocks' => array() ) ) );
		wp_enqueue_script( 'grimlock-blocks' );
	}

	/**
	 * Register the custom widgets.
	 *
	 * @since 1.0.0
	 */
	public function widgets_init() {
		register_widget( 'Grimlock_Section_Widget' );
		register_widget( 'Grimlock_Nav_Menu_Section_Widget' );
		register_widget( 'Grimlock_Query_Section_Widget' );
		register_widget( 'Grimlock_Posts_Section_Widget' );
		register_widget( 'Grimlock_Term_Query_Section_Widget' );

		$widget_areas = apply_filters( 'grimlock_widget_areas', array(
			'widget-area-01' => array(
				'id'          => 'preheader-1',
				'name'        => esc_html__( 'Pre Header 1', 'grimlock' ),
				'description' => esc_html__( 'The first area before the header of all pages.', 'grimlock' ),
			),
			'widget-area-02' => array(
				'id'          => 'preheader-2',
				'name'        => esc_html__( 'Pre Header 2', 'grimlock' ),
				'description' => esc_html__( 'The second area before the header of all pages.', 'grimlock' ),
			),
			'widget-area-03' => array(
				'id'          => 'preheader-3',
				'name'        => esc_html__( 'Pre Header 3', 'grimlock' ),
				'description' => esc_html__( 'The third area before the header of all pages.', 'grimlock' ),
			),
			'widget-area-04' => array(
				'id'          => 'preheader-4',
				'name'        => esc_html__( 'Pre Header 4', 'grimlock' ),
				'description' => esc_html__( 'The fourth area before the header of all pages.', 'grimlock' ),
			),
			'widget-area-10' => array(
				'id'          => 'before-content-1',
				'name'        => esc_html__( 'Before Content', 'grimlock' ),
				'description' => esc_html__( 'The area before the content for all pages.', 'grimlock' ),
			),
			'widget-area-20' => array(
				'id'          => 'sidebar-1',
				'name'        => esc_html__( 'Sidebar 1', 'grimlock' ),
				'description' => esc_html__( 'The left hand area for all pages.', 'grimlock' ),
			),
			'widget-area-21' => array(
				'id'          => 'sidebar-2',
				'name'        => esc_html__( 'Sidebar 2', 'grimlock' ),
				'description' => esc_html__( 'The right hand area for all pages.', 'grimlock' ),
			),
			'widget-area-30' => array(
				'id'          => 'after-content-1',
				'name'        => esc_html__( 'After Content', 'grimlock' ),
				'description' => esc_html__( 'The area after the content for all pages.', 'grimlock' ),
			),
			'widget-area-40' => array(
				'id'          => 'prefooter-1',
				'name'        => esc_html__( 'Pre Footer 1', 'grimlock' ),
				'description' => esc_html__( 'The first area before the footer of all pages.', 'grimlock' ),
			),
			'widget-area-41' => array(
				'id'          => 'prefooter-2',
				'name'        => esc_html__( 'Pre Footer 2', 'grimlock' ),
				'description' => esc_html__( 'The second area before the footer of all pages.', 'grimlock' ),
			),
			'widget-area-42' => array(
				'id'          => 'prefooter-3',
				'name'        => esc_html__( 'Pre Footer 3', 'grimlock' ),
				'description' => esc_html__( 'The third area before the footer of all pages.', 'grimlock' ),
			),
			'widget-area-43' => array(
				'id'          => 'prefooter-4',
				'name'        => esc_html__( 'Pre Footer 4', 'grimlock' ),
				'description' => esc_html__( 'The fourth area before the footer of all pages.', 'grimlock' ),
			),
			'widget-area-50' => array(
				'id'          => 'footer-1',
				'name'        => esc_html__( 'Footer 1', 'grimlock' ),
				'description' => esc_html__( 'The first area for the footer of all pages.', 'grimlock' ),
			),
			'widget-area-51' => array(
				'id'          => 'footer-2',
				'name'        => esc_html__( 'Footer 2', 'grimlock' ),
				'description' => esc_html__( 'The second area for the footer of all pages.', 'grimlock' ),
			),
			'widget-area-52' => array(
				'id'          => 'footer-3',
				'name'        => esc_html__( 'Footer 3', 'grimlock' ),
				'description' => esc_html__( 'The third area for the footer of all pages.', 'grimlock' ),
			),
			'widget-area-53' => array(
				'id'          => 'footer-4',
				'name'        => esc_html__( 'Footer 4', 'grimlock' ),
				'description' => esc_html__( 'The fourth area for the footer of all pages.', 'grimlock' ),
			),
			'widget-area-60' => array(
				'id'          => 'navbar-search-1',
				'name'        => esc_html__( 'Search Modal', 'grimlock' ),
				'description' => esc_html__( 'The widget area in the search modal', 'grimlock' ),
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
	 * Add image sizes for post thumbnails.
	 *
	 * @since 1.0.0
	 */
	public function add_image_sizes() {
		add_image_size( 'custom-header', get_custom_header()->width );
	}

	/**
	 * Define names for the custom image sizes.
	 *
	 * @param array $sizes The array of custom sizes.
	 *
	 * @return array The array of custom sizes.
	 * @since 1.0.0
	 */
	public function add_image_size_names( $sizes ) {
		return array_merge( $sizes, array(
			'custom-header' => esc_html__( 'Header', 'grimlock' ),
		) );
	}


	/**
	 * Add Kirki config for the Theme Customizer.
	 *
	 * @since 1.0.0
	 */
	public function add_customizer_fields() {
		if ( class_exists( 'Kirki' ) ) {
			// Add a new Kirki configuration for the theme.
			Kirki::add_config( 'grimlock', array(
				'option_type' => 'theme_mod',
				'capability'  => 'edit_theme_options',
			) );

			// Add the 'Appearance' panel.
			Kirki::add_panel( 'grimlock_appearance_customizer_panel', array(
				'title'       => esc_html__( 'Appearance', 'grimlock' ),
				'description' => esc_html__( 'Appearance regroups every components you can customize for your site. Basic styles can be set and then overriden for regions and pages when necessary.', 'grimlock' ),
				'priority'    => 20,
			) );
		}
	}

	/**
	 * Binds JS handlers to make Customizer preview reload changes asynchronously and enqueue preview styles.
	 *
	 * @since 1.0.0
	 */
	public function customize_preview_init() {
		wp_enqueue_style( 'grimlock-customizer-preview', GRIMLOCK_PLUGIN_DIR_URL . 'assets/css/customizer-preview.css' );
		wp_enqueue_script( 'grimlock-customizer', GRIMLOCK_PLUGIN_DIR_URL . 'assets/js/customizer.js', array( 'customize-preview' ), GRIMLOCK_VERSION, true );
		wp_localize_script( 'grimlock-customizer', 'grimlock_customizer', apply_filters( 'grimlock_customizer_js_data', array() ) );
	}

	/**
	 * Enqueue script for custom customize control.
	 *
	 * @since 1.0.0
	 */
	public function customize_controls_enqueue_scripts() {
		wp_enqueue_style( 'grimlock-customizer-controls', GRIMLOCK_PLUGIN_DIR_URL . 'assets/css/customizer-controls.css' );
		wp_enqueue_script( 'grimlock-customizer-controls', GRIMLOCK_PLUGIN_DIR_URL . 'assets/js/customizer-controls.js', array( 'customize-controls' ), GRIMLOCK_VERSION, true );
		wp_localize_script( 'grimlock-customizer-controls', 'grimlock_customizer_controls', apply_filters( 'grimlock_customizer_controls_js_data', array(
			'tabs' => array(),
		) ) );
	}

	/**
	 * Disable Kirki inline styles to enqueue a separate dynamic stylesheet instead
	 */
	public function disable_kirki_inline_styles() {
		// Don't do this in the customizer preview, otherwise the preview doesn't work properly when refreshed
		if ( ! is_customize_preview() ) {
			add_filter( 'kirki_output_inline_styles', '__return_false', 10 );
		}
	}

	/**
	 * Define available colors in the palette of the color picker
	 *
	 * @return array
	 */
	public function color_field_palettes() {
		return array(
			GRIMLOCK_BRAND_PRIMARY,
			GRIMLOCK_GRAY_LIGHT,
			GRIMLOCK_GRAY,
			GRIMLOCK_GRAY_DARK,
			'#ffffff',
			'#000000',
		);
	}

	/**
	 * Change default Kirki color swatches
	 *
	 * @param array $swatches Array of color swatches
	 *
	 * @return array Modified array of color swatches
	 */
	public function kirki_default_color_swatches( $swatches ) {
		$grimlock_color_swatches = apply_filters( 'grimlock_color_field_palettes', array() );

		if ( ! empty( $grimlock_color_swatches ) ) {
			return $grimlock_color_swatches;
		}

		return $swatches;
	}

	/**
	 * Fix a bug where Kirki updates the 'variant' value but doesn't update the 'font-weight' and 'font-style' value,
	 * which causes the outputs to always print the default font-weight and font-style instead of the selected ones.
	 *
	 * @param string|array $value Value of the Kirki field
	 * @param string $setting_name Name of the setting
	 * @param string|array $default Default value of the Kirki field
	 * @param string $option_type Whether this is a theme mod or an option
	 *
	 * @return string|array Filtered field value
	 */
	public function kirki_fix_typography_values( $value, $setting_name, $default, $option_type ) {
		if ( is_array( $value ) && ! empty( $default['font-weight'] ) ) {
			$value = wp_parse_args( $value, $default );

			if ( ! empty( $value['variant'] ) ) {
				$font_weight          = str_replace( 'italic', '', $value['variant'] );
				$font_weight          = ( in_array( $font_weight, [ '', 'regular' ], true ) ) ? '400' : $font_weight;
				$value['font-weight'] = $font_weight;

				$is_italic = ( false !== strpos( $value['variant'], 'italic' ) );
				$value['font-style'] = $is_italic ? 'italic' : 'normal';
			}
			else {
				$value['variant'] = $default['font-weight'];
			}
		}

		return $value;
	}

	/**
	 * Change the config of the logo, description and loader for Kirki
	 *
	 * @since 1.0.0
	 *
	 * @param  mixed $config The config for Kirki.
	 *
	 * @return mixed $config The updated config for Kirki.
	 */
	public function change_kirki_config( $config ) {
		$config['disable_loader'] = true;

		// 'style_priority' is incorrect and should be 'styles_priority', so Kirki is currently using a default priority of 999 to enqueue its styles.
		// TODO: decide if we actually need a priority of 10 then fix this if we do, remove it otherwise.
		$config['style_priority'] = 10;

		return $config;
	}

	/**
	 * Registers the section, setting & control for the kirki installer.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Customize_Manager $wp_customize The main customizer object.
	 */
	public function register_kirki_installer( $wp_customize ) {
		// If Kirki exists, don't register the installer section.
		if ( class_exists( 'Kirki' ) ) {
			return;
		}

		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/customizer/section/class-grimlock-installer-customizer-section.php';

		$wp_customize->add_section( new Grimlock_Kirki_Installer_Customizer_Section( $wp_customize, 'kirki_installer', array(
			'title'      => '',
			'capability' => 'install_plugins',
			'priority'   => 0,
		) ) );
		$wp_customize->add_setting( 'kirki_installer_setting', array() );
		$wp_customize->add_control( 'kirki_installer_control', array(
			'section'    => 'kirki_installer',
			'settings'   => 'kirki_installer_setting',
		) );
	}

	/**
	 * Register custom controls to use in the customizer
	 *
	 * @param $wp_customize
	 */
	public function register_controls( $wp_customize ) {
		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/customizer/control/class-grimlock-disabled-customizer-control.php';
		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/customizer/control/class-grimlock-divider-customizer-control.php';
		require_once GRIMLOCK_PLUGIN_DIR_PATH . 'inc/customizer/control/class-grimlock-heading-customizer-control.php';

		if ( class_exists( 'Kirki' ) ) {
			add_filter( 'kirki/control_types', array( $this, 'register_kirki_control_types' ) );
		}
	}

	/**
	 * Register custom controls with Kirki
	 *
	 * @param $controls array The array of Kirki controls for the Customizer
	 *
	 * @return array          The array of Kirki controls
	 * @since 1.0.0
	 */
	public function register_kirki_control_types( $controls ) {
		$controls['text_disabled']     = 'Grimlock_Disabled_Customizer_Control';
		$controls['textarea_disabled'] = 'Grimlock_Disabled_Customizer_Control';
		$controls['divider']           = 'Grimlock_Divider_Customizer_Control';
		$controls['heading']           = 'Grimlock_Heading_Customizer_Control';
		return $controls;
	}
}
