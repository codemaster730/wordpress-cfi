<?php
/**
 * Grimlock_BuddyPress_Activities_Customizer Class
 *
 * @author   Themosaurus
 * @since    1.3.19
 * @package grimlock
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The Grimlock Customizer class for BuddyPress.
 */
class Grimlock_BuddyPress_Activities_Customizer extends Grimlock_Singular_Template_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->id      = 'activities';
		$this->section = 'grimlock_buddypress_activities_section';
		$this->title   = esc_html__( 'Activity Stream', 'grimlock-buddypress' );

		add_filter( 'body_class',                           array( $this, 'add_body_classes'                ), 10, 1 );
		add_action( 'after_setup_theme',                    array( $this, 'add_customizer_fields'           ), 20    );
		add_filter( 'grimlock_customizer_controls_js_data', array( $this, 'add_customizer_controls_js_data' ), 10, 1 );

		add_filter( 'grimlock_custom_header_args',      array( $this, 'add_custom_header_args'      ), 20, 1 );
		add_filter( 'grimlock_custom_header_displayed', array( $this, 'has_custom_header_displayed' ), 10, 1 );

		add_action( 'customize_controls_print_scripts', array( $this, 'add_scripts' ), 30, 1 );
	}

	/**
	 * Register default values, settings and custom controls for the Theme Customizer.
	 *
	 * @since 1.0.0
	 */
	public function add_customizer_fields() {
		$this->defaults = apply_filters( 'grimlock_buddypress_activities_customizer_defaults', array(
			'activities_custom_header_displayed'        => false,

			'activities_custom_header_layout'           => '12-cols-center',
			'activities_custom_header_container_layout' => 'classic',

			'activities_custom_header_padding_y'        => GRIMLOCK_SECTION_PADDING_Y,
		) );

		// TODO: remove deprecated defaults filter
		$this->defaults = apply_filters( 'grimlock_buddypress_customizer_defaults', $this->defaults );

		if ( bp_is_active( 'activity' ) ) {
			$this->add_section();
		}

		// General Tab
		$this->add_heading_field(                                       array( 'priority' => 100, 'label' => esc_html__( 'Header Display', 'grimlock-buddypress' ) ) );
		$this->add_custom_header_displayed_field(                       array( 'priority' => 100 ) );
		$this->add_divider_field(                                       array( 'priority' => 110 ) );

		// Layout Tab
		$this->add_custom_header_layout_field(                          array( 'priority' => 200 ) );
		$this->add_divider_field(                                       array( 'priority' => 210 ) );
		$this->add_custom_header_container_layout_field(                array( 'priority' => 210 ) );

		// Style Tab
		$this->add_custom_header_padding_y_field(                       array( 'priority' => 300 ) );
	}

	/**
	 * Add tabs to the Customizer to group controls.
	 *
	 * @param  array $js_data The array of data for the Customizer controls.
	 *
	 * @return array          The filtered array of data for the Customizer controls.
	 */
	public function add_customizer_controls_js_data( $js_data ) {
		$js_data['tabs'][ $this->section ] = array(
			array(
				'label'    => esc_html__( 'General', 'grimlock-buddypress' ),
				'class'    => 'activities-general-tab',
				'controls' => array(
					"{$this->section}_heading_100",
					'activities_custom_header_displayed',
					"{$this->section}_divider_110",
					'activities_per_page',
					'activities_actions_text_displayed',
				),
			),
			array(
				'label'    => esc_html__( 'Layout', 'grimlock-buddypress' ),
				'class'    => 'activities-layout-tab',
				'controls' => array(
					'activities_custom_header_layout',
					"{$this->section}_divider_210",
					'activities_custom_header_container_layout',
				),
			),
			array(
				'label'    => esc_html__( 'Style', 'grimlock-buddypress' ),
				'class'    => 'activities-style-tab',
				'controls' => array(
					'activities_custom_header_padding_y',
				),
			),
		);
		return $js_data;
	}

	/**
	 * Check if the current template is the expected template.
	 *
	 * @since 1.3.19
	 *
	 * @return bool True when the template is the expected template, false otherwise.
	 */
	protected function is_template() {
		return bp_is_activity_directory();
	}

	/**
	 * Add a Kirki section in the Customizer.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args The array of arguments for the Kirki section.
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
	 * Add scripts to improve user experience in the customizer
	 */
	public function add_scripts() {
		if ( ! bp_is_active( 'activity' ) ) {
			return;
		}
		?>
		<script type="text/javascript">
            jQuery( document ).ready( function( $ ) {
                wp.customize.section( '<?php echo esc_js( $this->section ); ?>', function( section ) {
                    section.expanded.bind( function( isExpanded ) {
                        var previewUrl = '<?php echo esc_js( trailingslashit( bp_get_activity_directory_permalink() ) ); ?>';
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

return new Grimlock_BuddyPress_Activities_Customizer();
