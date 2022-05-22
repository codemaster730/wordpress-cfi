<?php
/**
 * Grimlock_Single_Customizer Class
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
 * The Grimlock Customizer class for the single posts.
 */
class Grimlock_Single_Customizer extends Grimlock_Singular_Template_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->id      = 'single';
		$this->section = 'grimlock_single_customizer_section';
		$this->title   = esc_html__( 'Single Post', 'grimlock' );

		add_action( 'after_setup_theme',                         array( $this, 'add_customizer_fields'           ), 20    );

		add_filter( 'body_class',                                array( $this, 'add_body_classes'                ), 10, 1 );

		add_filter( 'grimlock_customizer_controls_js_data',      array( $this, 'add_customizer_controls_js_data' ), 10, 1 );
		add_filter( 'grimlock_content_class',                    array( $this, 'add_content_classes'             ), 10, 1 );
		add_filter( 'grimlock_custom_header_args',               array( $this, 'add_custom_header_args'          ), 20, 1 );
		add_filter( 'grimlock_custom_header_displayed',          array( $this, 'has_custom_header_displayed'     ), 10, 1 );
		add_filter( 'grimlock_template_sidebar_right_displayed', array( $this, 'has_sidebar_right_displayed'     ), 10, 1 );
		add_filter( 'grimlock_template_sidebar_left_displayed',  array( $this, 'has_sidebar_left_displayed'      ), 10, 1 );
		add_filter( 'grimlock_single_args',                      array( $this, 'add_single_args'                 ), 10, 1 );
		add_filter( 'grimlock_single_post_navigation_displayed', array( $this, 'has_post_navigation_displayed'   ), 10, 1 );
		add_filter( 'grimlock_the_post_navigation_args',         array( $this, 'add_post_navigation_args'        ), 10, 1 );

		add_filter( 'kirki_grimlock_dynamic_css',                array( $this, 'add_dynamic_css'                 ), 10, 1 );
	}

	/**
	 * Add custom classes to body to modify layout.
	 *
	 * @param $classes
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function add_body_classes( $classes ) {
		$classes = parent::add_body_classes( $classes );

		if ( $this->is_template() ) {
			$classes[] = "grimlock--single-post-navigation-layout-{$this->get_theme_mod( 'single_post_navigation_layout' )}";
		}

		return $classes;
	}

	/**
	 * Add arguments using theme mods to customize the Custom Header.
	 *
	 * @param array $args The default arguments to render the Custom Header.
	 *
	 * @return array      The arguments to render the Custom Header.
	 */
	public function add_custom_header_args( $args ) {
		$args = parent::add_custom_header_args( $args );

		if ( $this->is_template() ) {
			$args['post']                         = get_queried_object();
			$args['single_tag_displayed']         = $this->get_theme_mod( 'single_tag_custom_header_displayed' );
			$args['single_category_displayed']    = $this->get_theme_mod( 'single_category_custom_header_displayed' );
			$args['single_post_format_displayed'] = $this->get_theme_mod( 'single_post_format_custom_header_displayed' );
			$args['single_post_date_displayed']   = $this->get_theme_mod( 'single_post_date_custom_header_displayed' );
			$args['single_post_author_displayed'] = $this->get_theme_mod( 'single_post_author_custom_header_displayed' );
		}
		return $args;
	}

	/**
	 * Add arguments using theme mods to customize the post.
	 *
	 * @param array $args The default arguments to render the post.
	 *
	 * @return array      The arguments to render the post.
	 */
	public function add_single_args( $args ) {
		$args['post_thumbnail_displayed']        = $this->get_theme_mod( 'single_post_thumbnail_displayed' );
		$args['post_date_displayed']             = $this->get_theme_mod( 'single_post_date_displayed' );
		$args['post_author_displayed']           = $this->get_theme_mod( 'single_post_author_displayed' );
		$args['post_author_biography_displayed'] = $this->get_theme_mod( 'single_post_author_biography_displayed' );
		$args['category_displayed']              = $this->get_theme_mod( 'single_category_displayed' );
		$args['post_tag_displayed']              = $this->get_theme_mod( 'single_post_tag_displayed' );
		$args['post_format_displayed']           = $this->get_theme_mod( 'single_post_format_displayed' );
		return $args;
	}

	/**
	 * @param $default
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function has_post_navigation_displayed( $default ) {
		if ( $this->is_template() ) {
			return true == $this->get_theme_mod( 'single_post_navigation_displayed' );
		}
		return $default;
	}

	/**
	 * Change post navigation args
	 *
	 * @param array $args Post navigation args
	 *
	 * @return array Modified post navigation args
	 */
	public function add_post_navigation_args( $args ) {
		if ( ! $this->is_template() ) {
			return $args;
		}

		switch ( $this->get_theme_mod( 'single_post_navigation_layout' ) ) {
			case 'modern':
			case 'modern-floating':
				$next_text = '%title';
				$next_post = get_next_post();
				if ( ! empty( $next_post ) ):
					$next_text =  get_the_post_thumbnail( $next_post->ID, 'thumbnail' ) . '<span class="post-title">%title</span>';
				endif;
				$previous_text = '%title';
				$previous_post = get_previous_post();
				if ( ! empty( $previous_post ) ):
					$previous_text = '<span class="post-title">%title</span>' . get_the_post_thumbnail( $previous_post->ID, 'thumbnail' );
				endif;
				$args = array(
					'next_text' => $next_text,
					'prev_text' => $previous_text,
				);
				break;
		}

		return $args;
	}

	/**
	 * Check if the current template is the expected template.
	 *
	 * @return bool True when the template is the expected template, false otherwise.
	 */
	protected function is_template() {
		return apply_filters( 'grimlock_single_customizer_is_template', is_single() );
	}

	/**
	 * Register default values, settings and custom controls for the Theme Customizer.
	 *
	 * @since 1.0.0
	 */
	public function add_customizer_fields() {
		$this->defaults = apply_filters( 'grimlock_single_customizer_defaults', array(
			'single_custom_header_displayed'             => has_header_image(),
			'single_post_date_custom_header_displayed'   => false,
			'single_post_author_custom_header_displayed' => false,
			'single_tag_custom_header_displayed'         => false,
			'single_category_custom_header_displayed'    => false,
			'single_post_format_custom_header_displayed' => false,

			'single_post_thumbnail_displayed'            => false,
			'single_post_date_displayed'                 => true,
			'single_post_author_displayed'               => true,
			'single_post_tag_displayed'                  => true,
			'single_category_displayed'                  => true,
			'single_post_format_displayed'               => true,
			'single_post_author_biography_displayed'     => true,
			'single_post_navigation_displayed'           => true,

			'single_custom_header_layout'                => '12-cols-center',
			'single_custom_header_container_layout'      => 'classic',
			'single_layout'                              => '3-6-3-cols-left',
			'single_sidebar_mobile_displayed'            => true,
			'single_container_layout'                    => 'classic',
			'single_post_navigation_layout'              => 'classic',

			'single_custom_header_padding_y'             => GRIMLOCK_SECTION_PADDING_Y,
			'single_content_padding_y'                   => GRIMLOCK_CONTENT_PADDING_Y,
		) );

		$this->add_section(                                      array( 'priority' => 30  ) );

		$this->add_heading_field(                                array( 'priority' => 10, 'label' => esc_html__( 'Header Display', 'grimlock' ) ) );
		$this->add_custom_header_displayed_field(                array( 'priority' => 20  ) );
		$this->add_post_date_custom_header_displayed_field(      array( 'priority' => 30  ) );
		$this->add_post_author_custom_header_displayed_field(    array( 'priority' => 40  ) );
		$this->add_tag_custom_header_displayed_field(            array( 'priority' => 50  ) );
		$this->add_category_custom_header_displayed_field(       array( 'priority' => 60  ) );
		$this->add_post_format_custom_header_displayed_field(    array( 'priority' => 70  ) );

		$this->add_divider_field(                                array( 'priority' => 70 ) );
		$this->add_heading_field(                                array( 'priority' => 70, 'label' => esc_html__( 'Content Display', 'grimlock' ) ) );
		$this->add_post_thumbnail_displayed_field(               array( 'priority' => 70, 'label' => esc_html__( 'Display featured image', 'grimlock' ) ) );
		$this->add_post_date_displayed_field(                    array( 'priority' => 80, 'label' => esc_html__( 'Display date', 'grimlock' ) ) );
		$this->add_post_author_displayed_field(                  array( 'priority' => 90, 'label' => esc_html__( 'Display author', 'grimlock' ) ) );
		$this->add_post_author_biography_displayed_field(        array( 'priority' => 100 ) );
		$this->add_post_tag_displayed_field(                     array( 'priority' => 110 ) );
		$this->add_category_displayed_field(                     array( 'priority' => 120 ) );
		$this->add_post_format_displayed_field(                  array( 'priority' => 130, 'label' => esc_html__( 'Display format', 'grimlock' ) ) );
		$this->add_post_navigation_displayed_field(              array( 'priority' => 140 ) );

		$this->add_custom_header_layout_field(                   array( 'priority' => 200 ) );
		$this->add_divider_field(                                array( 'priority' => 210 ) );
		$this->add_custom_header_container_layout_field(         array( 'priority' => 210 ) );
		$this->add_divider_field(                                array( 'priority' => 220 ) );
		$this->add_layout_field(                                 array( 'priority' => 220 ) );
		$this->add_sidebar_mobile_displayed_field(               array( 'priority' => 230 ) );
		$this->add_divider_field(                                array( 'priority' => 240 ) );
		$this->add_container_layout_field(                       array( 'priority' => 240 ) );
		$this->add_divider_field(                                array( 'priority' => 250 ) );
		$this->add_post_navigation_layout_field(                 array( 'priority' => 250 ) );

		$this->add_custom_header_padding_y_field(                array( 'priority' => 300 ) );
		$this->add_divider_field(                                array( 'priority' => 310 ) );
		$this->add_content_padding_y_field(                      array( 'priority' => 310 ) );

	}

	/**
	 * Add tabs to the Customizer to group controls.
	 *
	 * @param  array $js_data The array of data for the Customizer controls.
	 *
	 * @return array          The filtred array of data for the Customizer controls.
	 */
	public function add_customizer_controls_js_data( $js_data ) {
		$js_data['tabs'][$this->section] = array(
			array(
				'label' => esc_html__( 'General', 'grimlock' ),
				'class' => 'single-general-tab',
				'controls' => array(
					"{$this->section}_heading_10",
					'single_custom_header_displayed',
					'single_post_date_custom_header_displayed',
					'single_post_author_custom_header_displayed',
					'single_tag_custom_header_displayed',
					'single_category_custom_header_displayed',
					'single_post_format_custom_header_displayed',
					"{$this->section}_divider_70",
					"{$this->section}_heading_70",
					'single_post_thumbnail_displayed',
					'single_post_date_displayed',
					'single_post_author_displayed',
					'single_post_tag_displayed',
					'single_category_displayed',
					'single_post_format_displayed',
					'single_post_author_biography_displayed',
					'single_post_navigation_displayed',
				),
			),
			array(
				'label' => esc_html__( 'Layout', 'grimlock' ),
				'class' => 'single-layout-tab',
				'controls' => array(
					'single_custom_header_layout',
					"{$this->section}_divider_210",
					'single_custom_header_container_layout',
					"{$this->section}_divider_220",
					'single_layout',
					'single_sidebar_mobile_displayed',
					"{$this->section}_divider_240",
					'single_container_layout',
					"{$this->section}_divider_250",
					'single_post_navigation_layout',
				),
			),
			array(
				'label' => esc_html__( 'Style', 'grimlock' ),
				'class' => 'single-style-tab',
				'controls' => array(
					'single_custom_header_padding_y',
					"{$this->section}_divider_310",
					'single_content_padding_y',
				),
			),
		);
		return $js_data;
	}

	/**
	 * Add a Kirki section in the Customizer.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args
	 */
	protected function add_section( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			Kirki::add_panel( 'grimlock_posts_customizer_panel', array(
				'priority' => 120,
				'title'    => esc_html__( 'Posts', 'grimlock' ),
			) );

			Kirki::add_section( $this->section, apply_filters( "{$this->section}_args", array(
				'title'    => $this->title,
				'priority' => 30,
				'panel'    => 'grimlock_posts_customizer_panel',
			) ) );
		}
	}

	/**
	 * Add a Kirki checkbox to set the header post date display in the Customizer.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args
	 */
	protected function add_post_date_custom_header_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'checkbox',
				'section'  => $this->section,
				'label'    => esc_html__( 'Display date', 'grimlock' ),
				'settings' => "{$this->id}_post_date_custom_header_displayed",
				'default'  => $this->get_default( "{$this->id}_post_date_custom_header_displayed" ),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_post_date_custom_header_displayed_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox to set the header post author display in the Customizer.
	 *
	 * @since 1.0.6
	 *
	 * @param array $args
	 */
	protected function add_post_author_custom_header_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'checkbox',
				'section'  => $this->section,
				'label'    => esc_html__( 'Display author', 'grimlock' ),
				'settings' => "{$this->id}_post_author_custom_header_displayed",
				'default'  => $this->get_default( "{$this->id}_post_author_custom_header_displayed" ),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_post_author_custom_header_displayed_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox to set the header tags display in the Customizer.
	 *
	 * @since 1.4.1
	 *
	 * @param array $args
	 */
	protected function add_tag_custom_header_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'checkbox',
				'section'  => $this->section,
				'label'    => esc_html__( 'Display tags', 'grimlock' ),
				'settings' => "{$this->id}_tag_custom_header_displayed",
				'default'  => $this->get_default( "{$this->id}_tag_custom_header_displayed" ),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_tag_custom_header_displayed_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox to set the header categories display in the Customizer.
	 *
	 * @since 1.0.6
	 *
	 * @param array $args
	 */
	protected function add_category_custom_header_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'checkbox',
				'section'  => $this->section,
				'label'    => esc_html__( 'Display categories', 'grimlock' ),
				'settings' => "{$this->id}_category_custom_header_displayed",
				'default'  => $this->get_default( "{$this->id}_category_custom_header_displayed" ),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_category_custom_header_displayed_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox to set the header post format display in the Customizer.
	 *
	 * @since 1.0.6
	 *
	 * @param array $args
	 */
	protected function add_post_format_custom_header_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'checkbox',
				'section'  => $this->section,
				'label'    => esc_html__( 'Display format', 'grimlock' ),
				'settings' => "{$this->id}_post_format_custom_header_displayed",
				'default'  => $this->get_default( "{$this->id}_post_format_custom_header_displayed" ),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_post_format_custom_header_displayed_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox to set the post navigation display in the Customizer.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args
	 */
	protected function add_post_navigation_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'checkbox',
				'section'  => $this->section,
				'label'    => esc_html__( 'Display navigation between posts', 'grimlock' ),
				'settings' => 'single_post_navigation_displayed',
				'default'  => $this->get_default( 'single_post_navigation_displayed' ),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_single_customizer_post_navigation_displayed_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox to set the biography display in the Customizer.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args
	 */
	protected function add_post_author_biography_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'checkbox',
				'section'  => $this->section,
				'label'    => esc_html__( 'Display author biography', 'grimlock' ),
				'settings' => 'single_post_author_biography_displayed',
				'default'  => $this->get_default( 'single_post_author_biography_displayed' ),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_single_customizer_post_author_biography_displayed_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki radio-image field to set the layout for the navigation layout in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_post_navigation_layout_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'radio-image',
				'section'  => $this->section,
				'label'    => esc_html__( 'Navigation', 'grimlock' ),
				'settings' => "{$this->id}_post_navigation_layout",
				'default'  => $this->get_default( "{$this->id}_post_navigation_layout" ),
				'priority' => 10,
				'choices'  => array(
					'classic'         => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/navigation-classic.png',
					'modern-floating' => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/navigation-modern-floating.png',
					//'modern'          => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/navigation-modern.png',
				),
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_post_navigation_layout_field_args", $args ) );
		}
	}
}

return new Grimlock_Single_Customizer();
