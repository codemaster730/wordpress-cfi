<?php
/**
 * Grimlock_BuddyPress_BuddyPress_Docs_Single_BP_Docs_Customizer Class
 *
 * @author  Themosaurus
 * @since   1.0.0
 * @package grimlock-buddypress
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The Grimlock Customizer class for the docs single pages.
 */
class Grimlock_BuddyPress_BuddyPress_Docs_Single_BP_Docs_Customizer extends Grimlock_Singular_Template_Customizer {

	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->id      = 'single_bp_doc';
		$this->section = 'grimlock_buddypress_buddypress_docs_single_bp_doc_customizer_section';
		$this->title   = esc_html__( 'Single Doc', 'grimlock-buddypress' );

		add_action( 'after_setup_theme',                         array( $this, 'add_customizer_fields'           ), 20    );

		add_filter( 'body_class',                                array( $this, 'add_body_classes'                ), 10, 1 );

		add_filter( 'grimlock_customizer_controls_js_data',      array( $this, 'add_customizer_controls_js_data' ), 10, 1 );
		add_filter( 'grimlock_content_class',                    array( $this, 'add_content_classes'             ), 10, 1 );
		add_filter( 'grimlock_custom_header_args',               array( $this, 'add_custom_header_args'          ), 20, 1 );
		add_filter( 'grimlock_custom_header_displayed',          array( $this, 'has_custom_header_displayed'     ), 10, 1 );
		add_filter( 'grimlock_template_sidebar_right_displayed', array( $this, 'has_sidebar_right_displayed'     ), 10, 1 );
		add_filter( 'grimlock_template_sidebar_left_displayed',  array( $this, 'has_sidebar_left_displayed'      ), 10, 1 );
		add_filter( 'grimlock_single_customizer_is_template',    array( $this, 'single_customizer_is_template'   ), 10, 1 );
		add_filter( 'bp_buddypress_template',                    array( $this, 'change_single_doc_template'      ), 10, 1 );
	}

	/**
	 * Add tabs to the Customizer to group controls.
	 *
	 * @param  array $js_data The array of data for the Customizer controls.
	 *
	 * @return array          The filtered array of data for the Customizer controls.
	 */
	public function add_customizer_controls_js_data( $js_data ) {
		$js_data['tabs'][$this->section] = array(
			array(
				'label' => esc_html__( 'General', 'grimlock-buddypress' ),
				'class' => 'single_bp_doc-general-tab',
				'controls' => array(
					"{$this->section}_heading_10",
					'single_bp_doc_custom_header_displayed',
				),
			),
			array(
				'label' => esc_html__( 'Layout', 'grimlock-buddypress' ),
				'class' => 'single_bp_doc-layout-tab',
				'controls' => array(
					'single_bp_doc_custom_header_layout',
					"{$this->section}_divider_110",
					'single_bp_doc_custom_header_container_layout',
					"{$this->section}_divider_120",
					'single_bp_doc_layout',
					'single_bp_doc_sidebar_mobile_displayed',
					"{$this->section}_divider_140",
					'single_bp_doc_container_layout',
				),
			),
			array(
				'label' => esc_html__( 'Style', 'grimlock-buddypress' ),
				'class' => 'single_bp_doc-style-tab',
				'controls' => array(
					'single_bp_doc_custom_header_padding_y',
					"{$this->section}_divider_210",
					'single_bp_doc_content_padding_y',
				),
			),
		);
		return $js_data;
	}

	/**
	 * Register default values, settings and custom controls for the Theme Customizer.
	 *
	 * @since 1.0.0
	 */
	public function add_customizer_fields() {
		$this->defaults = apply_filters( 'grimlock_buddypress_buddypress_docs_single_bp_doc_customizer_defaults', array(
			'single_bp_doc_custom_header_displayed'            => false,

			'single_bp_doc_custom_header_layout'               => '12-cols-center',
			'single_bp_doc_custom_header_container_layout'     => 'classic',
			'single_bp_doc_layout'                             => '12-cols-left',
			'single_bp_doc_sidebar_mobile_displayed'           => true,
			'single_bp_doc_container_layout'                   => 'classic',

			'single_bp_doc_custom_header_padding_y'            => GRIMLOCK_SECTION_PADDING_Y,
			'single_bp_doc_content_padding_y'                  => GRIMLOCK_CONTENT_PADDING_Y,
		) );

		$this->add_section();

		$this->add_heading_field(                            array( 'priority' => 10, 'label' => esc_html__( 'Header Display', 'grimlock' ) ) );
		$this->add_custom_header_displayed_field(            array( 'priority' => 20 ) );

		$this->add_custom_header_layout_field(               array( 'priority' => 100 ) );
		$this->add_divider_field(                            array( 'priority' => 110 ) );
		$this->add_custom_header_container_layout_field(     array( 'priority' => 110 ) );
		$this->add_divider_field(                            array( 'priority' => 120 ) );
		$this->add_layout_field(                             array( 'priority' => 120 ) );
		$this->add_sidebar_mobile_displayed_field(           array( 'priority' => 130 ) );
		$this->add_divider_field(                            array( 'priority' => 140 ) );
		$this->add_container_layout_field(                   array( 'priority' => 140 ) );

		$this->add_custom_header_padding_y_field(            array( 'priority' => 200 ) );
		$this->add_divider_field(                            array( 'priority' => 210 ) );
		$this->add_content_padding_y_field(                  array( 'priority' => 210 ) );
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
			Kirki::add_section( $this->section, apply_filters( "{$this->section}_args", array(
				'title'    => $this->title,
				'priority' => 30,
				'panel'    => 'grimlock_buddypress_customizer_panel',
			) ) );
		}
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
			$args['background_image'] = apply_filters( 'grimlock_buddypress_buddypress_docs_custom_header_background_image', '' );
		}

		return $args;
	}

	/**
	 * Check if the current template is the expected template.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True when the template is the expected template, false otherwise.
	 */
	protected function is_template() {
		$is_template = is_singular( 'bp_doc' );
		return apply_filters( 'grimlock_buddypress_buddypress_docs_single_bp_doc_customizer_is_template', $is_template );
	}

	/**
	 * Disinherit single customizer settings
	 *
	 * @param bool $default True if we are on a default single page
	 *
	 * @return bool
	 */
	public function single_customizer_is_template( $default ) {
		return $default && ! $this->is_template();
	}

	/**
	 * Change the single doc template
	 *
	 * @param string $template The single template
	 *
	 * @return string The single template
	 */
	public function change_single_doc_template( $template ) {
		if ( $this->is_template() ) {
			return get_page_template();
		}
		return $template;
	}
}

return new Grimlock_BuddyPress_BuddyPress_Docs_Single_BP_Docs_Customizer();
