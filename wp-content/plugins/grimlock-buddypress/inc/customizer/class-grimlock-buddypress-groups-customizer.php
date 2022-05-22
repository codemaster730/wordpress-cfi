<?php
/**
 * Grimlock_BuddyPress_Groups_Customizer Class
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
class Grimlock_BuddyPress_Groups_Customizer extends Grimlock_Singular_Template_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->id      = 'groups';
		$this->section = 'grimlock_buddypress_groups_section';
		$this->title   = esc_html__( 'Groups Directory', 'grimlock-buddypress' );

		add_filter( 'body_class',                           array( $this, 'add_body_classes'                ), 10, 1 );
		add_action( 'after_setup_theme',                    array( $this, 'add_customizer_fields'           ), 20    );
		add_filter( 'grimlock_customizer_controls_js_data', array( $this, 'add_customizer_controls_js_data' ), 10, 1 );

		add_filter( 'grimlock_custom_header_args',      array( $this, 'add_custom_header_args'      ), 20, 1 );
		add_filter( 'grimlock_custom_header_displayed', array( $this, 'has_custom_header_displayed' ), 10, 1 );

		add_filter( 'bp_ajax_querystring',                               array( $this, 'change_groups_query_args'           ), 100, 2 );
		add_filter( 'grimlock_buddypress_groups_per_page',               array( $this, 'groups_per_page'                    ), 10,  1 );
		add_filter( 'grimlock_buddypress_groups_actions_text_displayed', array( $this, 'is_groups_actions_text_displayed'   ), 10,  1 );
		add_action( 'grimlock_buddypress_group_custom_fields',           array( $this, 'add_group_custom_fields'            ), 10,  1 );

		add_action( 'customize_controls_print_scripts', array( $this, 'add_scripts' ), 30, 1 );
	}

	/**
	 * Register default values, settings and custom controls for the Theme Customizer.
	 *
	 * @since 1.0.0
	 */
	public function add_customizer_fields() {
		$this->defaults = apply_filters( 'grimlock_buddypress_groups_customizer_defaults', array(
			'groups_custom_header_displayed'        => false,
			'groups_per_page'                       => '24',
			'groups_counts_displayed'               => true,
			'groups_actions_text_displayed'         => false,
			'groups_group_types_displayed'          => false,

			'groups_custom_header_layout'           => '12-cols-center',
			'groups_custom_header_container_layout' => 'classic',
			'groups_items_layout'                   => '3-3-3-3-cols-classic',

			'groups_custom_header_padding_y'        => GRIMLOCK_SECTION_PADDING_Y,
		) );

		// TODO: remove deprecated defaults filter
		$this->defaults = apply_filters( 'grimlock_buddypress_customizer_defaults', $this->defaults );

		if ( bp_is_active( 'groups' ) ) {
			$this->add_section();
		}

		// General Tab
		$this->add_heading_field(                                       array( 'priority' => 100, 'label' => esc_html__( 'Header Display', 'grimlock-buddypress' ) ) );
		$this->add_custom_header_displayed_field(                       array( 'priority' => 100 ) );
		$this->add_divider_field(                                       array( 'priority' => 110 ) );
		$this->add_per_page_field(                                      array( 'priority' => 110 ) );
		$this->add_counts_displayed_field(                              array( 'priority' => 120 ) );
		$this->add_actions_text_displayed_field(                        array( 'priority' => 130 ) );
		$this->add_group_types_displayed_field(                         array( 'priority' => 140 ) );

		// Layout Tab
		$this->add_custom_header_layout_field(                          array( 'priority' => 200 ) );
		$this->add_divider_field(                                       array( 'priority' => 210 ) );
		$this->add_custom_header_container_layout_field(                array( 'priority' => 210 ) );
		$this->add_divider_field(                                       array( 'priority' => 220 ) );
		$this->add_items_layout_field(                                  array( 'priority' => 220 ) );

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
				'class'    => 'groups-general-tab',
				'controls' => array(
					"{$this->section}_heading_100",
					'groups_custom_header_displayed',
					"{$this->section}_divider_110",
					'groups_per_page',
					'groups_counts_displayed',
					'groups_actions_text_displayed',
					'groups_group_types_displayed',
				),
			),
			array(
				'label'    => esc_html__( 'Layout', 'grimlock-buddypress' ),
				'class'    => 'groups-layout-tab',
				'controls' => array(
					'groups_custom_header_layout',
					"{$this->section}_divider_210",
					'groups_custom_header_container_layout',
					"{$this->section}_divider_220",
					'groups_items_layout',
				),
			),
			array(
				'label'    => esc_html__( 'Style', 'grimlock-buddypress' ),
				'class'    => 'groups-style-tab',
				'controls' => array(
					'groups_custom_header_padding_y',
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
		return bp_is_groups_directory();
	}

	/**
	 * Add a Kirki checkbox field in the Customizer to set whether the groups action text is displayed.
	 *
	 * @since 1.0.5
	 *
	 * @param array $args The array of arguments for the Kirki field.
	 */
	protected function add_actions_text_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki') ) {
			$args = wp_parse_args( $args, array(
				'type'        => 'checkbox',
				'section'     => $this->section,
				'label'       => esc_html__( 'Display groups actions text', 'grimlock-buddypress' ),
				'description' => esc_html__( 'If this field is checked, BuddyPress action buttons for group lists will have a text in addition to the icon.', 'grimlock-buddypress' ),
				'settings'    => "{$this->id}_actions_text_displayed",
				'default'     => $this->get_default( "{$this->id}_actions_text_displayed" ),
				'priority'    => 20,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_buddypress_groups_customizer_actions_text_displayed_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox field in the Customizer to set whether the group types are displayed.
	 *
	 * @since 1.4.0
	 *
	 * @param array $args The array of arguments for the Kirki field.
	 */
	protected function add_group_types_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki') ) {
			$args = wp_parse_args( $args, array(
				'type'        => 'checkbox',
				'section'     => $this->section,
				'label'       => esc_html__( 'Display group types', 'grimlock-buddypress' ),
				'settings'    => "{$this->id}_group_types_displayed",
				'default'     => $this->get_default( "{$this->id}_group_types_displayed" ),
				'priority'    => 20,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_buddypress_groups_customizer_group_types_displayed_field_args', $args ) );
		}
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
	 * Add a Kirki select control to change the groups per page
	 *
	 * @param array $args
	 * @since 1.0.8
	 */
	protected function add_per_page_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'        => 'number',
				'section'     => $this->section,
				'label'       => esc_html__( 'Groups per Page', 'grimlock-buddypress' ),
				'description' => esc_html__( 'Insert 0 to remove the page limit.', 'grimlock-buddypress' ),
				'settings'    => "{$this->id}_per_page",
				'default'     => $this->get_default( "{$this->id}_per_page" ),
				'priority'    => 10,
				'transport'   => 'refresh',
				'choices'   => array(
					'min'  => 0,
					'step' => 1,
				),
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_buddypress_{$this->id}_customizer_per_page_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox field in the Customizer to set whether the groups counts should be displayed
	 *
	 * @since 1.3.19
	 *
	 * @param array $args The array of arguments for the Kirki field.
	 */
	protected function add_counts_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki') ) {
			$args = wp_parse_args( $args, array(
				'type'        => 'checkbox',
				'section'     => $this->section,
				'label'       => esc_html__( 'Display groups counts', 'grimlock-buddypress' ),
				'settings'    => "{$this->id}_counts_displayed",
				'default'     => $this->get_default( "{$this->id}_counts_displayed" ),
				'priority'    => 20,
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_buddypress_{$this->id}_customizer_counts_displayed_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki radio-image field to set the groups list layout in the Customizer
	 *
	 * @param array $args
	 * @since 1.4.0
	 */
	protected function add_items_layout_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'radio-image',
				'section'  => $this->section,
				'label'    => esc_html__( 'Groups Layout', 'grimlock-buddypress' ),
				'settings' => "{$this->id}_items_layout",
				'default'  => $this->get_default( "{$this->id}_items_layout" ),
				'priority' => 10,
				'choices'  => array(
					'2-2-2-2-2-2-cols-classic'         => GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_URL . 'assets/images/members-items-2-2-2-2-2-2-cols-classic.png',
					'2-2-2-2-2-cols-classic'           => GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_URL . 'assets/images/members-items-2-2-2-2-2-cols-classic.png',
					'3-3-3-3-cols-classic'             => GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_URL . 'assets/images/members-items-3-3-3-3-cols-classic.png',
					'4-4-4-cols-classic'               => GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_URL . 'assets/images/members-items-4-4-4-cols-classic.png',
					'6-6-cols-classic'                 => GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_URL . 'assets/images/members-items-6-6-cols-classic.png',
					'12-cols-inline'                   => GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_URL . 'assets/images/members-items-12-cols-inline.png',
				),
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_buddypress_{$this->id}_customizer_items_layout_field_args", $args ) );
		}
	}

	/**
	 * Change members/groups per page using customizer value
	 *
	 * @param string $query_string The query string used by BuddyPress to build the members/groups query
	 * @param string $object       Whether this query is for members or groups
	 *
	 * @return string The modified query string
	 */
	public function change_groups_query_args( $query_string, $object ) {
		if ( ! is_string( $query_string ) || 'groups' !== $object ) {
			return $query_string;
		}

		$query_args = explode( '&', $query_string );

		// Handle groups per page
		$per_page = $this->get_theme_mod( 'groups_per_page' );

		if ( ! empty( $per_page ) || $per_page == 0 ) {
			foreach ( $query_args as $key => $query_arg ) {
				if ( strpos( $query_arg, 'per_page' ) !== false ) {
					unset( $query_args[ $key ] );
					break;
				}
			}

			$query_args[] = "per_page={$per_page}";
		}

		$query_string = implode( '&', $query_args );

		return $query_string;
	}

	/**
	 * Return groups per page
	 */
	public function groups_per_page() {
		return $this->get_theme_mod( 'groups_per_page' );
	}

	/**
	 * Add custom classes to body to modify friend icons.
	 *
	 * @since 1.0.5
	 * @param array $classes The array of body classes.
	 *
	 * @return array The updated array of body classes.
	 */
	public function add_body_classes( $classes ) {
		$classes = parent::add_body_classes( $classes );

		$groups_counts_displayed       = $this->get_theme_mod( 'groups_counts_displayed' );
		$groups_actions_text_displayed = $this->get_theme_mod( 'groups_actions_text_displayed' );

		if ( ! empty( $groups_counts_displayed ) ) {
			$classes[] = 'grimlock-buddypress--groups-counts-displayed';
		}

		if ( ! empty( $groups_actions_text_displayed ) ) {
			$classes[] = 'grimlock-buddypress--groups-actions-text-displayed';
		}

		$classes[] = "grimlock-buddypress--groups-items-layout-{$this->get_theme_mod( 'groups_items_layout' )}";

		return $classes;
	}

	/**
	 * Check whether BP group action text need to be displayed.
	 *
	 * @since 1.0.5
	 *
	 * @param  bool $default The value for the text display.
	 *
	 * @return bool          True if the text needs to be displayed, false otherwise.
	 */
	public function is_groups_actions_text_displayed( $default ) {
		return (bool) $this->get_theme_mod( 'groups_actions_text_displayed' );
	}

	/**
	 * Display fields in groups using theme mods
	 *
	 * @param int $group_id The id of the group.
	 */
	public function add_group_custom_fields( $group_id ) {
		if ( function_exists( 'buddypress' ) ) {
			if ( empty( $group_id ) ) {
				$group_id = bp_get_group_id();
			}

			if ( empty( $group_id ) ) {
				$group_id = bp_get_current_group_id();
			}

			if ( ! empty( $this->get_theme_mod( 'groups_group_types_displayed' ) ) ) {

				$group_types = bp_groups_get_group_type( $group_id, false );
				if ( ! empty( $group_types ) ) {
					echo '<div class="grimlock-buddypress-group-types mt-1">';

					foreach ( $group_types as $group_type ) {
						$group_type_label = bp_groups_get_group_type_object( $group_type )->labels['singular_name'];

						if ( $group_type_url = bp_get_group_type_directory_permalink( $group_type ) ) {
							echo '<a href="' . esc_url( $group_type_url ) . '" class="badge badge-primary px-2 py-1 mr-1 mt-1">' . $group_type_label . '</a>';
						} else {
							echo '<span class="badge badge-primary px-2 py-1 mx-1 mt-1">' . $group_type_label . '</span>';
						}
					}

					echo '</div>';
				}

			}
		}
	}

	/**
	 * Add scripts to improve user experience in the customizer
	 */
	public function add_scripts() {
		if ( ! bp_is_active( 'groups' ) ) {
			return;
		}
		?>
		<script type="text/javascript">
            jQuery( document ).ready( function( $ ) {
                wp.customize.section( '<?php echo esc_js( $this->section ); ?>', function( section ) {
                    section.expanded.bind( function( isExpanded ) {
                        var previewUrl = '<?php echo esc_js( trailingslashit( bp_get_groups_directory_permalink() ) ); ?>';
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

return new Grimlock_BuddyPress_Groups_Customizer();
