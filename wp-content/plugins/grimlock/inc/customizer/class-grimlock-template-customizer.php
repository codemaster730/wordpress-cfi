<?php
/**
 * Grimlock_Template_Customizer Class
 *
 * @author  Themosaurus
 * @since   1.0.0
 * @package grimlock
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The Grimlock Customizer class for the templates.
 */
abstract class Grimlock_Template_Customizer extends Grimlock_Base_Customizer {
	/**
	 * @var string $id The ID for the group of features in the Customizer.
	 * @since 1.0.0
	 */
	protected $id;

	/**
	 * @var array The array of elements to target the posts in theme.
	 * @since 1.0.0
	 */
	protected $elements;

	/**
	 * Add custom classes to body to modify layout.
	 *
	 * @param $classes
	 * @since 1.0.0
	 *
	 * @return string[]
	 */
	public function add_body_classes( $classes ) {
		if ( $this->is_template() ) {
			$classes[] = "grimlock--{$this->id}";
		}
		return $classes;
	}

	/**
	 * Add custom classes to content to modify layout.
	 *
	 * @param $classes
	 * @since 1.0.0
	 *
	 * @return string[]
	 */
	public function add_content_classes( $classes ) {
		if ( $this->is_template() ) {
			$classes[] = "region--{$this->get_theme_mod( "{$this->id}_layout")}";
			$classes[] = "region--container-{$this->get_theme_mod( "{$this->id}_container_layout")}";
		}
		return $classes;
	}

	/**
	 * Add arguments using theme mods to customize the Custom Header.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args The default arguments to render the Custom Header.
	 *
	 * @return array      The arguments to render the Custom Header.
	 */
	public function add_custom_header_args( $args ) {
		if ( $this->is_template() ) {
			$padding                  = $this->get_theme_mod( "{$this->id}_custom_header_padding_y" );
			$args['padding_top']      = $padding;
			$args['padding_bottom']   = $padding;

			$args['layout']           = $this->get_theme_mod( "{$this->id}_custom_header_layout" );
			$args['container_layout'] = $this->get_theme_mod( "{$this->id}_custom_header_container_layout" );

		}
		return $args;
	}

	/**
	 * Check if the right sidebar has to be displayed.
	 *
	 * @param $default
	 *
	 * @return bool True when sidebar right must be displayed, false otherwise.
	 * @since 1.0.0
	 */
	public function has_sidebar_right_displayed( $default ) {
		if ( $this->is_template() ) {
			return '3-6-3-cols-left' == $this->get_theme_mod( "{$this->id}_layout" ) ||
			       '9-3-cols-left' == $this->get_theme_mod( "{$this->id}_layout" );
		}
		return $default;
	}

	/**
	 * Check if the left sidebar has to be displayed.
	 *
	 * @param $default
	 *
	 * @return bool True when sidebar left must be displayed, false otherwise.
	 * @since 1.0.0
	 */
	public function has_sidebar_left_displayed( $default ) {
		if ( $this->is_template() ) {
			return '3-6-3-cols-left' == $this->get_theme_mod( "{$this->id}_layout" ) ||
			       '3-9-cols-left' == $this->get_theme_mod( "{$this->id}_layout" );
		}
		return $default;
	}

	/**
	 * Check if the custom header is displayed or not.
	 *
	 * @since 1.0.7
	 *
	 * @return bool True if the custom header is displayed, false otherwise.
	 */
	public function has_custom_header_displayed( $default ) {
		if ( $this->is_template() ) {
			return true == $this->get_theme_mod( "{$this->id}_custom_header_displayed" );
		}
		return $default;
	}

	/**
	 * Check if the current template is the expected template.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True when the template is the expected template, false otherwise.
	 */
	protected abstract function is_template();

	/**
	 * Add a Kirki section in the Customizer.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args
	 */
	protected function add_section( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'title'    => $this->title,
				'priority' => 120,
			) );

			Kirki::add_section( $this->section, apply_filters( "grimlock_{$this->id}_customizer_section_args", $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox to set the Custom Header display.
	 *
	 * @since 1.0.7
	 *
	 * @param array $args
	 */
	protected function add_custom_header_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'checkbox',
				'section'  => $this->section,
				'label'    => esc_html__( 'Display header image', 'grimlock' ),
				'settings' => "{$this->id}_custom_header_displayed",
				'default'  => $this->get_default( "{$this->id}_custom_header_displayed" ),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_custom_header_displayed_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki slider field to set the the Custom Header padding for the template in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_custom_header_padding_y_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$outputs = apply_filters( "grimlock_{$this->id}_customizer_custom_header_padding_y_outputs", array(
				array(
					'element'  => ".grimlock--{$this->id} .grimlock-custom_header > .region__inner",
					'property' => 'padding-top',
					'units'    => '%',
					'suffix'   => '!important',
				),
				array(
					'element'  => ".grimlock--{$this->id} .grimlock-custom_header > .region__inner",
					'property' => 'padding-bottom',
					'units'    => '%',
					'suffix'   => '!important',
				),
			) );

			$args = wp_parse_args( $args, array(
				'type'        => 'slider',
				'section'     => $this->section,
				'label'       => esc_attr__( 'Header Padding', 'grimlock' ),
				'settings'    => "{$this->id}_custom_header_padding_y",
				'default'     => $this->get_default( "{$this->id}_custom_header_padding_y" ),
				'choices'     => array(
					'min'     => 0,
					'max'     => 25,
					'step'    => .25,
				),
				'priority'    => 10,
				'transport'   => 'postMessage',
				'output'      => array(
					$this->get_css_var_output( "{$this->id}_custom_header_padding_y", '%' ),
				),
				'js_vars'     => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_custom_header_padding_y_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki slider field to set the the content padding for the template in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_content_padding_y_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$outputs = apply_filters( "grimlock_{$this->id}_customizer_content_padding_y_outputs", array(
				$this->get_css_var_output( "{$this->id}_content_padding_y", '%' ),
				array(
					'element'  => ".grimlock--{$this->id} .site-content",
					'property' => 'padding-top',
					'units'    => '%',
				),
				array(
					'element'  => ".grimlock--{$this->id} .site-content",
					'property' => 'padding-bottom',
					'units'    => '%',
				),
			) );

			$args = wp_parse_args( $args, array(
				'type'      => 'slider',
				'section'   => $this->section,
				'label'     => esc_attr__( 'Content Padding', 'grimlock' ),
				'settings'  => "{$this->id}_content_padding_y",
				'default'   => $this->get_default( "{$this->id}_content_padding_y" ),
				'choices'   => array(
					'min'   => 0,
					'max'   => 10,
					'step'  => .25,
				),
				'priority'  => 10,
				'output'    => $outputs,
				'transport' => 'postMessage',
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_content_padding_y_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki radio-image field to set the layout in the Customizer.
	 *
	 * @since 1.0.7
	 *
	 * @param array $args The array of arguments for the Kirki field.
	 */
	protected function add_custom_header_layout_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'      => 'radio-image',
				'section'   => $this->section,
				'label'     => esc_html__( 'Header Layout', 'grimlock' ),
				'settings'  => "{$this->id}_custom_header_layout",
				'default'   => $this->get_default( "{$this->id}_custom_header_layout" ),
				'priority'  => 10,
				'choices'   => array(
					'12-cols-left'                 => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/custom_header-12-cols-left.png',
					'12-cols-center'               => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/custom_header-12-cols-center.png',
					'12-cols-right'                => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/custom_header-12-cols-right.png',
					'6-6-cols-left-reverse'        => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/custom_header-6-6-cols-left-reverse.png',
					'6-6-cols-left'                => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/custom_header-6-6-cols-left.png',
					'6-6-cols-left-reverse-modern' => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/custom_header-6-6-cols-left-reverse-modern.png',
					'6-6-cols-left-modern'         => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/custom_header-6-6-cols-left-modern.png',
				),
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_custom_header_layout_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki radio-image field to set the layout for the region container in the Customizer.
	 *
	 * @since 1.0.7
	 *
	 * @param array $args The array of arguments for the Kirki field.
	 */
	protected function add_custom_header_container_layout_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'radio-image',
				'section'  => $this->section,
				'label'    => esc_html__( 'Header Spread', 'grimlock' ),
				'settings' => "{$this->id}_custom_header_container_layout",
				'default'  => $this->get_default( "{$this->id}_custom_header_container_layout" ),
				'priority' => 10,
				'choices'  => array(
					'fluid'    => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/region-container-fluid.png',
					'classic'  => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/region-container-classic.png',
					'narrow'   => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/region-container-narrow.png',
					'narrower' => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/region-container-narrower.png',
				),
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_custom_header_container_layout_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki radio-image field to set the layout for the template container layout in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_container_layout_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'radio-image',
				'section'  => $this->section,
				'label'    => esc_html__( 'Spread', 'grimlock' ),
				'settings' => "{$this->id}_container_layout",
				'default'  => $this->get_default( "{$this->id}_container_layout" ),
				'priority' => 10,
				'choices'  => array(
					'fluid'    => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/template-container-fluid.png',
					'classic'  => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/template-container-classic.png',
					'narrow'   => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/template-container-narrow.png',
					'narrower' => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/template-container-narrower.png',
				),
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_container_layout_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki radio-image field to set the layout for the template post layout in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_posts_layout_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'radio-image',
				'section'  => $this->section,
				'label'    => esc_html__( 'Posts', 'grimlock' ),
				'settings' => "{$this->id}_posts_layout",
				'default'  => $this->get_default( "{$this->id}_posts_layout" ),
				'priority' => 10,
				'choices'  => array(
					'4-4-4-cols-classic'               => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/template-posts-4-4-4-cols-classic.png',
					'3-3-3-3-cols-classic'             => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/template-posts-3-3-3-3-cols-classic.png',
					'6-6-cols-classic'                 => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/template-posts-6-6-cols-classic.png',
					'12-cols-classic'                  => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/template-posts-12-cols-classic.png',
					'4-4-4-cols-overlay'               => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/posts-4-4-4-cols-overlay.png',
					'3-3-3-3-cols-overlay'             => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/posts-3-3-3-3-cols-overlay.png',
					'6-6-cols-overlay'                 => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/posts-6-6-cols-overlay.png',
					'12-cols-overlay'                  => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/posts-12-cols-overlay.png',
					'12-cols-lateral-modern-alternate' => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/posts-12-cols-lateral-modern-alternate.png',
					'6-6-cols-lateral'                 => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/template-posts-6-6-cols-lateral.png',
					'6-6-cols-lateral-reverse'         => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/template-posts-6-6-cols-lateral-reverse.png',
					'12-cols-lateral'                  => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/template-posts-12-cols-lateral.png',
					'12-cols-lateral-reverse'          => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/template-posts-12-cols-lateral-reverse.png',
				),
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_posts_layout_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki radio-image field to set the template content layout in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_layout_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'radio-image',
				'section'  => $this->section,
				'label'    => esc_html__( 'Sidebars', 'grimlock' ),
				'settings' => "{$this->id}_layout",
				'default'  => $this->get_default( "{$this->id}_layout" ),
				'priority' => 10,
				'choices'  => array(
					'3-6-3-cols-left' => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/template-content-3-6-3-cols-left.png',
					'12-cols-left'    => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/template-content-12-cols-left.png',
					'3-9-cols-left'   => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/template-content-3-9-cols-left.png',
					'9-3-cols-left'   => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/template-content-9-3-cols-left.png',
				),
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_layout_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox to set the sidebar display for the mobile devices in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_sidebar_mobile_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'checkbox',
				'section'  => $this->section,
				'label'    => esc_html__( 'Display sidebar on mobile pages', 'grimlock' ),
				'settings' => "{$this->id}_sidebar_mobile_displayed",
				'default'  => $this->get_default( "{$this->id}_sidebar_mobile_displayed" ),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_sidebar_mobile_displayed_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox to set the post thumbnail display in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_post_thumbnail_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'checkbox',
				'section'  => $this->section,
				'label'    => esc_html__( 'Display featured images', 'grimlock' ),
				'settings' => "{$this->id}_post_thumbnail_displayed",
				'default'  => $this->get_default( "{$this->id}_post_thumbnail_displayed" ),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_post_thumbnail_displayed_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox to set the post date display in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_post_date_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'checkbox',
				'section'  => $this->section,
				'label'    => esc_html__( 'Display dates', 'grimlock' ),
				'settings' => "{$this->id}_post_date_displayed",
				'default'  => $this->get_default( "{$this->id}_post_date_displayed" ),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_post_date_displayed_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox to set the post author display in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_post_author_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'checkbox',
				'section'  => $this->section,
				'label'    => esc_html__( 'Display authors', 'grimlock' ),
				'settings' => "{$this->id}_post_author_displayed",
				'default'  => $this->get_default( "{$this->id}_post_author_displayed" ),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_post_author_displayed_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox to set the post content display in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_post_content_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'checkbox',
				'section'  => $this->section,
				'label'    => esc_html__( 'Display contents', 'grimlock' ),
				'settings' => "{$this->id}_post_content_displayed",
				'default'  => $this->get_default( "{$this->id}_post_content_displayed" ),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_post_content_displayed_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox to set the post excerpt display in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_post_excerpt_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'checkbox',
				'section'  => $this->section,
				'label'    => esc_html__( 'Display excerpts', 'grimlock' ),
				'settings' => "{$this->id}_post_excerpt_displayed",
				'default'  => $this->get_default( "{$this->id}_post_excerpt_displayed" ),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_post_excerpt_displayed_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox field to set the component display of the more link in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_post_more_link_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki') ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'checkbox',
				'section'  => $this->section,
				'label'    => esc_html__( 'Display Continue reading' , 'grimlock' ),
				'settings' => "{$this->id}_post_more_link_displayed",
				'default'  => $this->get_default( "{$this->id}_post_more_link_displayed" ),
				'priority' => 10,
				'active_callback' => array(
					array(
						'setting'  => "{$this->id}_post_excerpt_displayed",
						'operator' => '==',
						'value'    => true,
					),
				),
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_post_more_link_displayed_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox to set the post tag display in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_post_tag_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'checkbox',
				'section'  => $this->section,
				'label'    => esc_html__( 'Display tags', 'grimlock' ),
				'settings' => "{$this->id}_post_tag_displayed",
				'default'  => $this->get_default( "{$this->id}_post_tag_displayed" ),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_post_tag_displayed_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox to set the category display in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_category_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'checkbox',
				'section'  => $this->section,
				'label'    => esc_html__( 'Display categories', 'grimlock' ),
				'settings' => "{$this->id}_category_displayed",
				'default'  => $this->get_default( "{$this->id}_category_displayed" ),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_category_displayed_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox to set the post format display in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_post_format_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'checkbox',
				'section'  => $this->section,
				'label'    => esc_html__( 'Display formats', 'grimlock' ),
				'settings' => "{$this->id}_post_format_displayed",
				'default'  => $this->get_default( "{$this->id}_post_format_displayed" ),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_post_format_displayed_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox field to set the component display of the comments link in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_comments_link_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki') ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'checkbox',
				'section'  => $this->section,
				'label'    => esc_html__( 'Display Leave a comment' , 'grimlock' ),
				'settings' => "{$this->id}_comments_link_displayed",
				'default'  => $this->get_default( "{$this->id}_comments_link_displayed" ),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_comments_link_displayed_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox to equalize the height for posts (or not) in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_posts_height_equalized_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'checkbox',
				'section'  => $this->section,
				'label'    => esc_html__( 'Equalize height for posts', 'grimlock' ),
				'settings' => "{$this->id}_posts_height_equalized",
				'default'  => $this->get_default( "{$this->id}_posts_height_equalized" ),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_posts_height_equalized_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the color in the Customizer.
	 *
	 * @since 1.0.0
	 * @param array $args
	 */
	protected function add_post_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( "grimlock_{$this->id}_customizer_post_color_elements", $this->elements );
			$outputs  = apply_filters( "grimlock_{$this->id}_customizer_post_color_outputs", array(
				$this->get_css_var_output( "{$this->id}_post_color" ),
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'color',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Text Color', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => "{$this->id}_post_color",
				'default'   => $this->get_default( "{$this->id}_post_color" ),
				'choices'   => array(
					'alpha'    => false,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'output'    => $outputs,
				'transport' => 'postMessage',
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_post_color_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the background color in the Customizer.
	 *
	 * @since 1.0.0
	 * @param array $args
	 */
	protected function add_post_background_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( "grimlock_{$this->id}_customizer_post_background_color_elements", $this->elements );
			$outputs  = apply_filters( "grimlock_{$this->id}_customizer_post_background_color_outputs", array(
				$this->get_css_var_output( "{$this->id}_post_background_color" ),
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'background-color',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Background Color', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => "{$this->id}_post_background_color",
				'default'   => $this->get_default( "{$this->id}_post_background_color" ),
				'choices'   => array(
					'alpha'    => true,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'output'    => $outputs,
				'transport' => 'postMessage',
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_post_background_color_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the border color in the Customizer.
	 *
	 * @since 1.0.0
	 * @param array $args
	 */
	protected function add_post_border_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( "grimlock_{$this->id}_customizer_post_border_color_elements", $this->elements );
			$outputs  = apply_filters( "grimlock_{$this->id}_customizer_post_border_color_outputs", array(
				$this->get_css_var_output( "{$this->id}_post_border_color" ),
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'border-color',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Border Color', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => "{$this->id}_post_border_color",
				'default'   => $this->get_default( "{$this->id}_post_border_color" ),
				'choices'   => array(
					'alpha'    => true,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'output'    => $outputs,
				'transport' => 'postMessage',
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_post_border_color_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki slider control to set the border radius for the post thumbnail in the Customizer.
	 *
	 * @since 1.0.0
	 * @param array $args
	 */
	protected function add_post_thumbnail_border_radius_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = array();
			foreach ( $this->elements as $element ) {
				$elements[] = "{$element} .card-img";
			}
			$elements = apply_filters( "grimlock_{$this->id}_customizer_post_thumbnail_border_radius_elements", $elements );

			$outputs = apply_filters( "grimlock_{$this->id}_customizer_post_thumbnail_border_radius_outputs", array(
				$this->get_css_var_output( "{$this->id}_post_thumbnail_border_radius", 'rem' ),
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'border-radius',
					'units'    => 'rem',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'slider',
				'section'   => $this->section,
				'label'     => esc_attr__( 'Thumbnail Border Radius', 'grimlock' ),
				'settings'  => "{$this->id}_post_thumbnail_border_radius",
				'default'   => $this->get_default( "{$this->id}_post_thumbnail_border_radius" ),
				'choices'   => array(
					'min'   => 0,
					'max'   => 10,
					'step'  => .05,
				),
				'priority'  => 10,
				'output'    => $outputs,
				'transport' => 'postMessage',
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_post_thumbnail_border_radius_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the title color in the Customizer.
	 *
	 * @since 1.0.0
	 * @param array $args
	 */
	protected function add_post_title_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = array();
			foreach ( $this->elements as $element ) {
				$elements[] = "{$element} .entry-title";
				$elements[] = "{$element} .entry-title a";
			}
			$elements = apply_filters( "grimlock_{$this->id}_customizer_post_title_color_elements", $elements );

			$outputs = apply_filters( "grimlock_{$this->id}_customizer_post_title_color_outputs", array(
				$this->get_css_var_output( "{$this->id}_post_title_color" ),
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'color',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Title Color', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => "{$this->id}_post_title_color",
				'default'   => $this->get_default( "{$this->id}_post_title_color" ),
				'choices'   => array(
					'alpha'    => false,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'output'    => $outputs,
				'transport' => 'postMessage',
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_post_title_color_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the link color in the Customizer.
	 *
	 * @since 1.0.0
	 * @param array $args
	 */
	protected function add_post_link_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = array();
			foreach ( $this->elements as $element ) {
				$elements[] = "{$element} .entry-meta a:not(.badge):not(.btn):not(.button):not([rel='tag'])";
				$elements[] = "{$element} .entry-content a:not(.badge):not(.btn):not(.button):not([rel='tag'])";
				$elements[] = "{$element} .entry-summary a:not(.badge):not(.btn):not(.button):not([rel='tag'])";
				$elements[] = "{$element} .entry-footer a:not(.badge):not(.btn):not(.button):not([rel='tag'])";
			}
			$elements = apply_filters( "grimlock_{$this->id}_customizer_post_link_color_elements", $elements );

			$outputs = apply_filters( "grimlock_{$this->id}_customizer_post_link_color_outputs", array(
				$this->get_css_var_output( "{$this->id}_post_link_color" ),
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'color',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Link Color', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => "{$this->id}_post_link_color",
				'default'   => $this->get_default( "{$this->id}_post_link_color" ),
				'choices'   => array(
					'alpha'    => false,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'output'    => $outputs,
				'transport' => 'postMessage',
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_post_link_color_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the link hover color in the Customizer.
	 *
	 * @since 1.0.0
	 * @param array $args
	 */
	protected function add_post_link_hover_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = array();
			foreach ( $this->elements as $element ) {
				$elements[] = "{$element} .entry-title a:hover";
				$elements[] = "{$element} .entry-title a:active";
				$elements[] = "{$element} .entry-title a:focus";
				$elements[] = "{$element} .entry-meta a:not(.badge):not(.btn):not(.button):not([rel='tag']):hover";
				$elements[] = "{$element} .entry-meta a:not(.badge):not(.btn):not(.button):not([rel='tag']):active";
				$elements[] = "{$element} .entry-meta a:not(.badge):not(.btn):not(.button):not([rel='tag']):focus";
				$elements[] = "{$element} .entry-content a:not(.badge):not(.btn):not(.button):not([rel='tag']):hover";
				$elements[] = "{$element} .entry-content a:not(.badge):not(.btn):not(.button):not([rel='tag']):active";
				$elements[] = "{$element} .entry-content a:not(.badge):not(.btn):not(.button):not([rel='tag']):focus";
				$elements[] = "{$element} .entry-summary a:not(.badge):not(.btn):not(.button):not([rel='tag']):hover";
				$elements[] = "{$element} .entry-summary a:not(.badge):not(.btn):not(.button):not([rel='tag']):active";
				$elements[] = "{$element} .entry-summary a:not(.badge):not(.btn):not(.button):not([rel='tag']):focus";
				$elements[] = "{$element} .entry-footer a:not(.badge):not(.btn):not(.button):not([rel='tag']):hover";
				$elements[] = "{$element} .entry-footer a:not(.badge):not(.btn):not(.button):not([rel='tag']):active";
				$elements[] = "{$element} .entry-footer a:not(.badge):not(.btn):not(.button):not([rel='tag']):focus";
			}
			$elements = apply_filters( "grimlock_{$this->id}_customizer_post_link_hover_color_elements", $elements );

			$outputs = apply_filters( "grimlock_{$this->id}_customizer_post_link_hover_color_outputs", array(
				$this->get_css_var_output( "{$this->id}_post_link_hover_color" ),
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'color',
					'suffix' => '!important',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Link Color Hover', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => "{$this->id}_post_link_hover_color",
				'default'   => $this->get_default( "{$this->id}_post_link_hover_color" ),
				'choices'   => array(
					'alpha'    => false,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'output'    => $outputs,
				'transport' => 'postMessage',
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_post_link_hover_color_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki slider field to set the padding in the Customizer.
	 *
	 * @since 1.0.0
	 * @param array $args
	 */
	protected function add_post_padding_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = array();
			foreach ( $this->elements as $element ) {
				$elements[] = "{$element} .card-body";
			}
			$elements = apply_filters( "grimlock_{$this->id}_customizer_post_padding_elements", $elements );

			$outputs = apply_filters( "grimlock_{$this->id}_customizer_post_padding_outputs", array(
				$this->get_css_var_output( "{$this->id}_post_padding", 'px' ),
				array(
					'element'     => implode( ',', $elements ),
					'property'    => 'padding',
					'units'       => 'px',
					'media_query' => '@media (min-width: 992px)'
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'slider',
				'section'   => $this->section,
				'label'     => esc_attr__( 'Padding', 'grimlock' ),
				'settings'  => "{$this->id}_post_padding",
				'default'   => $this->get_default( "{$this->id}_post_padding" ),
				'choices'   => array(
					'min'   => 0,
					'max'   => 50,
					'step'  => 1,
				),
				'priority'  => 10,
				'output'    => $outputs,
				'transport' => 'refresh',
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_post_padding_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki slider control to set the border radius in the Customizer.
	 *
	 * @since 1.0.0
	 * @param array $args
	 */
	protected function add_post_border_radius_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( "grimlock_{$this->id}_customizer_post_border_radius_elements", $this->elements );
			$outputs  = apply_filters( "grimlock_{$this->id}_customizer_post_border_radius_outputs", array(
				$this->get_css_var_output( "{$this->id}_post_border_radius", 'rem' ),
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'border-radius',
					'units'    => 'rem',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'slider',
				'section'   => $this->section,
				'label'     => esc_attr__( 'Border Radius', 'grimlock' ),
				'settings'  => "{$this->id}_post_border_radius",
				'default'   => $this->get_default( "{$this->id}_post_border_radius" ),
				'choices'   => array(
					'min'   => 0,
					'max'   => 10,
					'step'  => .05,
				),
				'priority'  => 10,
				'output'    => $outputs,
				'transport' => 'postMessage',
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_post_border_radius_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki slider control to set the border width in the Customizer.
	 *
	 * @since 1.0.0
	 * @param array $args
	 */
	protected function add_post_border_width_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( "grimlock_{$this->id}_customizer_post_border_width_elements", $this->elements );
			$outputs  = apply_filters( "grimlock_{$this->id}_customizer_post_border_width_outputs", array(
				$this->get_css_var_output( "{$this->id}_post_border_width", 'px' ),
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'border-width',
					'units'    => 'px',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'slider',
				'section'   => $this->section,
				'label'     => esc_attr__( 'Border Width', 'grimlock' ),
				'settings'  => "{$this->id}_post_border_width",
				'default'   => $this->get_default( "{$this->id}_post_border_width" ),
				'choices'   => array(
					'min'   => 0,
					'max'   => 10,
					'step'  => 1,
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'js_vars'   => $this->to_js_vars( $outputs ),
				'output'    => $outputs,
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_post_border_width_field_args", $args ) );
		}
	}

	/**
	 * Enqueue custom styles based on theme mods.
	 *
	 * @param string $styles The styles printed by Kirki
	 *
	 * @since 1.0.0
	 *
	 * @return string        The updated styles printed by Kirki.
	 */
	public function add_dynamic_css( $styles ) {
		if ( false == $this->get_theme_mod( "{$this->id}_sidebar_mobile_displayed" ) ) {
			$styles .= "
			@media (max-width: 768px) {
				.grimlock--{$this->id} .sidebar {
					display: none;
				}
			}";
		}
		return $styles;
	}
}
