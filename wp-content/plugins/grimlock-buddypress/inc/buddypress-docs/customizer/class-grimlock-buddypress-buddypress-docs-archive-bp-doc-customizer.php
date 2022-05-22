<?php
/**
 * Grimlock_BuddyPress_BuddyPress_Docs_Archive_BP_Docs_Customizer Class
 *
 * @author  Themosaurus
 * @since   1.4.0
 * @package grimlock-buddypress
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The Grimlock Customizer class for the docs archive pages.
 */
class Grimlock_BuddyPress_BuddyPress_Docs_Archive_BP_Docs_Customizer extends Grimlock_Template_Customizer {

	/**
	 * Setup class.
	 *
	 * @since 1.4.0
	 */
	public function __construct() {
		$this->id         = 'archive_bp_doc';
		$this->section    = 'grimlock_buddypress_buddypress_docs_archive_bp_doc_customizer_section';
		$this->title      = esc_html__( 'Docs Directory', 'grimlock-buddypress' );

		add_action( 'after_setup_theme',                            array( $this, 'add_customizer_fields'           ), 20    );

		add_filter( 'body_class',                                   array( $this, 'add_body_classes'                ), 10, 1 );

		add_filter( 'grimlock_customizer_controls_js_data',         array( $this, 'add_customizer_controls_js_data' ), 10, 1 );
		add_filter( 'grimlock_content_class',                       array( $this, 'add_content_classes'             ), 10, 1 );
		add_filter( 'grimlock_custom_header_args',                  array( $this, 'add_custom_header_args'          ), 20, 1 );
		add_filter( 'grimlock_custom_header_displayed',             array( $this, 'has_custom_header_displayed'     ), 10, 1 );
		add_filter( 'grimlock_template_sidebar_right_displayed',    array( $this, 'has_sidebar_right_displayed'     ), 20, 1 );
		add_filter( 'grimlock_template_sidebar_left_displayed',     array( $this, 'has_sidebar_left_displayed'      ), 20, 1 );
		add_filter( 'bp_buddypress_template',                       array( $this, 'change_docs_directory_template'  ), 10, 1 );

		add_action( 'customize_controls_print_scripts',             array( $this, 'add_scripts'                     ), 30, 1 );

		add_filter( 'grimlock_archive_customizer_is_template',      array( $this, 'archive_customizer_is_template'  ), 10,  1 );

		add_filter( 'grimlock_custom_header_customizer_padding_y_field_args',        array( $this, 'add_custom_header_customizer_padding_y_field_description'        ), 10,  1 );
		add_filter( 'grimlock_custom_header_customizer_layout_field_args',           array( $this, 'add_custom_header_customizer_layout_field_description'           ), 10,  1 );
		add_filter( 'grimlock_custom_header_customizer_container_layout_field_args', array( $this, 'add_custom_header_customizer_container_layout_field_description' ), 10,  1 );

		add_filter( 'grimlock_buddypress_buddypress_docs_custom_header_background_image', array( $this, 'custom_header_background_image' ) );
	}

	/**
	 * Add tabs to the Customizer to group controls.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $js_data The array of data for the Customizer controls.
	 *
	 * @return array          The filtred array of data for the Customizer controls.
	 */
	public function add_customizer_controls_js_data( $js_data ) {
		$js_data['tabs'][$this->section] = array(
			array(
				'label' => esc_html__( 'General', 'grimlock-buddypress' ),
				'class' => 'archive_bp_doc-general-tab',
				'controls' => array(
					'archive_bp_doc_title',
					"{$this->section}_heading_20",
					"{$this->section}_divider_20",
					'archive_bp_doc_custom_header_displayed',
				),
			),
			array(
				'label' => esc_html__( 'Layout', 'grimlock-buddypress' ),
				'class' => 'archive_bp_doc-layout-tab',
				'controls' => array(
					'archive_bp_doc_custom_header_layout',
					"{$this->section}_divider_110",
					'archive_bp_doc_custom_header_container_layout',
					"{$this->section}_divider_120",
					'archive_bp_doc_layout',
					"{$this->section}_divider_140",
					'archive_bp_doc_sidebar_mobile_displayed',
					'archive_bp_doc_container_layout',
				),
			),
			array(
				'label' => esc_html__( 'Style', 'grimlock-buddypress' ),
				'class' => 'archive_bp_doc-style-tab',
				'controls' => array(
					'archive_bp_doc_custom_header_background_image',
					"{$this->section}_divider_210",
					'archive_bp_doc_custom_header_padding_y',
					"{$this->section}_divider_220",
					'archive_bp_doc_content_padding_y',
				),
			),
		);
		return $js_data;
	}

	/**
	 * Register default values, settings and custom controls for the Theme Customizer.
	 *
	 * @since 1.4.0
	 */
	public function add_customizer_fields() {
		$this->defaults = apply_filters( 'grimlock_buddypress_buddypress_docs_archive_bp_doc_customizer_defaults', array(
			'archive_bp_doc_title'                             => function_exists( 'bp_docs_get_docs_directory_title' ) ? bp_docs_get_docs_directory_title() : '',
			'archive_bp_doc_description'                       => '',
			'archive_bp_doc_custom_header_displayed'           => has_header_image(),

			'archive_bp_doc_custom_header_layout'              => '6-6-cols-left-reverse',
			'archive_bp_doc_custom_header_container_layout'    => 'classic',
			'archive_bp_doc_layout'                            => '12-cols-left',
			'archive_bp_doc_sidebar_mobile_displayed'          => true,
			'archive_bp_doc_container_layout'                  => 'classic',

			'archive_bp_doc_custom_header_background_image'    => get_header_image(),
			'archive_bp_doc_custom_header_padding_y'           => GRIMLOCK_SECTION_PADDING_Y,
			'archive_bp_doc_content_padding_y'                 => GRIMLOCK_CONTENT_PADDING_Y,
		) );

		$this->add_section();

		$this->add_title_field(                          array( 'priority' => 10  ) );
		$this->add_divider_field(                        array( 'priority' => 20  ) );
		$this->add_heading_field(                        array( 'priority' => 20, 'label' => esc_html__( 'Display', 'grimlock-buddypress' ) ) );
		$this->add_custom_header_displayed_field(        array( 'priority' => 20  ) );

		$this->add_custom_header_layout_field(           array( 'priority' => 100 ) );
		$this->add_divider_field(                        array( 'priority' => 110 ) );
		$this->add_custom_header_container_layout_field( array( 'priority' => 110 ) );
		$this->add_divider_field(                        array( 'priority' => 120 ) );
		$this->add_layout_field(                         array( 'priority' => 120 ) );
		$this->add_sidebar_mobile_displayed_field(       array( 'priority' => 130 ) );
		$this->add_divider_field(                        array( 'priority' => 140 ) );
		$this->add_container_layout_field(               array( 'priority' => 140 ) );

		$this->add_custom_header_background_image_field( array( 'priority' => 200 ) );
		$this->add_divider_field(                        array( 'priority' => 210 ) );
		$this->add_custom_header_padding_y_field(        array( 'priority' => 210 ) );
		$this->add_divider_field(                        array( 'priority' => 220 ) );
		$this->add_content_padding_y_field(              array( 'priority' => 220 ) );
	}

	/**
	 * Add a Kirki section in the Customizer.
	 *
	 * @since 1.4.0
	 *
	 * @param array $args
	 */
	protected function add_section( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			Kirki::add_section( $this->section, apply_filters( "{$this->section}_args", array(
				'title'    => $this->title,
				'priority' => isset( $args['priority'] ) ? $args['priority'] :  10,
				'panel'    => 'grimlock_buddypress_customizer_panel',
			) ) );
		}
	}

	/**
	 * Add a Kirki text field to set the title in the Customizer.
	 *
	 * @since 1.4.0
	 *
	 * @param array $args
	 */
	protected function add_title_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'              => 'text_disabled',
				'label'             => esc_html__( 'Title', 'grimlock-buddypress' ),
				'description'       => esc_html__( 'You can change the header title of the docs directory by editing it in BuddyPress Docs settings in the admin.', 'grimlock-buddypress' ),
				'section'           => $this->section,
				'settings'          => 'archive_bp_doc_title',
				'default'           => $this->get_default( 'archive_bp_doc_title' ),
				'priority'          => 10,
				'sanitize_callback' => 'wp_kses_post',
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_buddypress_buddypress_docs_archive_bp_doc_customizer_title_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki textarea field to set the description in the Customizer.
	 *
	 * @since 1.4.0
	 *
	 * @param array $args
	 */
	protected function add_description_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'              => 'textarea',
				'label'             => esc_html__( 'Description', 'grimlock-buddypress' ),
				'section'           => $this->section,
				'settings'          => 'archive_bp_doc_description',
				'default'           => $this->get_default( 'archive_bp_doc_description' ),
				'priority'          => 10,
				'sanitize_callback' => 'wp_kses_post',
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_buddypress_buddypress_docs_archive_bp_doc_customizer_description_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki image field to set the background image for the Custom Header in the Customizer.
	 *
	 * @since 1.4.0
	 *
	 * @param array $args The array of arguments for the Kirki field.
	 */
	protected function add_custom_header_background_image_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'image',
				'section'  => $this->section,
				'label'    => esc_html__( 'Header image', 'grimlock-buddypress' ),
				'settings' => 'archive_bp_doc_custom_header_background_image',
				'default'  => $this->get_default( 'archive_bp_doc_custom_header_background_image' ),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_buddypress_buddypress_docs_archive_bp_doc_customizer_background_image_field_args', $args ) );
		}
	}

	/**
	 * Add arguments using theme mods to customize the Custom Header.
	 *
	 * @since 1.4.0
	 *
	 * @param array $args The default arguments to render the Custom Header.
	 *
	 * @return array      The arguments to render the Custom Header.
	 */
	public function add_custom_header_args( $args ) {
		$args = parent::add_custom_header_args( $args );

		if ( $this->is_template() ) {
			$args['title']            = bp_docs_get_docs_directory_title();
			$args['subtitle']         = $this->get_theme_mod( 'archive_bp_doc_description' );
			$args['background_image'] = $this->get_theme_mod( 'archive_bp_doc_custom_header_background_image' );
		}

		return $args;
	}

	/**
	 * Check whether Custom Header has to be displayed.
	 *
	 * @since 1.4.0
	 *
	 * @return bool True when Custom Header has to be displayed, false otherwise.
	 */
	public function has_custom_header_displayed( $default ) {
		if ( $this->is_template() ) {
			return true == $this->get_theme_mod( 'archive_bp_doc_custom_header_displayed' );
		}
		return $default;
	}

	/**
	 * Check if the current template is the expected template, the docs page or a similar template.
	 *
	 * @since 1.4.0
	 *
	 * @return bool True when the template is the expected template, false otherwise.
	 */
	protected function is_template() {
		$is_template = is_post_type_archive( 'bp_doc' ) || is_tax( 'bp_docs_tag' );
		return apply_filters( 'grimlock_buddypress_buddypress_docs_archive_bp_doc_customizer_is_template', $is_template );
	}

	/**
	 * Disinherit archive customizer settings
	 *
	 * @param bool $default True if we are on a default archive page
	 *
	 * @return bool
	 */
	public function archive_customizer_is_template( $default ) {
		return $default && ! $this->is_template();
	}

	/**
	 * Return the custom header background image to use on docs templates
	 *
	 * @param string $background_image Background image url
	 *
	 * @return string Modified background image url
	 */
	public function custom_header_background_image( $background_image ) {
		return $this->get_theme_mod( 'archive_bp_doc_custom_header_background_image' );
	}

	/**
	 * Add a link in the description pointing to the job listing archive vertical padding field.
	 *
	 * @since 1.4.0
	 *
	 * @param  array $args The array of arguments for the Kirki field.
	 *
	 * @return array       The updated array of arguments for the Kirki field.
	 */
	public function add_custom_header_customizer_padding_y_field_description( $args ) {
		$args['description'] .= wp_kses( __( ', <a href="#archive_bp_doc_custom_header_padding_y" rel="tc-control">Docs Page</a>', 'grimlock-buddypress' ), array(
			'a' => array(
				'href' => array(),
				'rel'  => array(),
			),
		) );
		return $args;
	}

	/**
	 * Add a link in the description pointing to the job listing archive layout field.
	 *
	 * @since 1.4.0
	 *
	 * @param  array $args The array of arguments for the Kirki field.
	 *
	 * @return array       The updated array of arguments for the Kirki field.
	 */
	public function add_custom_header_customizer_layout_field_description( $args ) {
		$args['description'] .= wp_kses( __( ', <a href="#archive_bp_doc_custom_header_layout" rel="tc-control">Docs Page</a>', 'grimlock-buddypress' ), array(
			'a' => array(
				'href' => array(),
				'rel'  => array(),
			),
		) );
		return $args;
	}

	/**
	 * Add a link in the description pointing to the job listing archive container layout field.
	 *
	 * @since 1.4.0
	 *
	 * @param  array $args The array of arguments for the Kirki field.
	 *
	 * @return array       The updated array of arguments for the Kirki field.
	 */
	public function add_custom_header_customizer_container_layout_field_description( $args ) {
		$args['description'] .= wp_kses( __( ', <a href="#archive_bp_doc_custom_header_container_layout" rel="tc-control">Docs Page</a>', 'grimlock-buddypress' ), array(
			'a' => array(
				'href' => array(),
				'rel'  => array(),
			),
		) );
		return $args;
	}

	/**
	 * Change the docs archive template
	 *
	 * @param string $template The archive template
	 *
	 * @return string The archive template
	 */
	public function change_docs_directory_template( $template ) {
		if ( $this->is_template() ) {
			return get_page_template();
		}
		return $template;
	}

	/**
	 * Add scripts to improve user experience in the customizer
	 */
	public function add_scripts() {
		?>
		<script type="text/javascript">
            jQuery( document ).ready( function( $ ) {
                wp.customize.section( '<?php echo esc_js( $this->section ); ?>', function( section ) {
                    section.expanded.bind( function( isExpanded ) {
                        var previewUrl = '<?php echo esc_js( trailingslashit( get_post_type_archive_link( 'bp_doc' ) ) ); ?>';
                        if ( isExpanded && wp.customize.previewer.previewUrl.get() !== previewUrl ) {
                            wp.customize.previewer.previewUrl.set( previewUrl );
                        }
                    } );
                } );
            } );
		</script>
		<?php
	}
}

return new Grimlock_BuddyPress_BuddyPress_Docs_Archive_BP_Docs_Customizer();
