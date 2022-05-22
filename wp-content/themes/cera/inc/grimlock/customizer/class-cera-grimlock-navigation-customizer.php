<?php
/**
 * Cera_Grimlock_Navigation_Customizer Class
 *
 * @author   Themosaurus
 * @since    1.0.0
 * @package grimlock
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The navigation class for the Customizer.
 */
class Cera_Grimlock_Navigation_Customizer extends Grimlock_Navigation_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::__construct();

		add_filter( 'grimlock_vertical_navigation_args',                                         array( $this, 'change_navigation_args'                         ), 10, 1 );
		add_filter( 'body_class',                                                                array( $this, 'change_body_classes'                            ), 20, 1 );

		add_filter( 'grimlock_navigation_customizer_defaults',                                   array( $this, 'change_defaults'                                ), 10, 1 );
		add_filter( 'grimlock_navigation_customizer_menu_item_color_outputs',                    array( $this, 'add_menu_item_color_outputs'                    ), 10, 1 );
		add_filter( 'grimlock_navigation_customizer_background_color_outputs',                   array( $this, 'add_background_color_outputs'                   ), 10, 1 );
		add_filter( 'grimlock_navigation_customizer_border_bottom_width_outputs',                array( $this, 'add_border_bottom_width_outputs'                ), 10, 1 );
		add_filter( 'grimlock_navigation_customizer_menu_item_active_color_elements',            array( $this, 'add_menu_item_active_color_elements'            ), 10, 1 );
		add_filter( 'grimlock_navigation_customizer_menu_item_active_color_outputs',             array( $this, 'add_menu_item_active_color_outputs'             ), 10, 1 );
		add_filter( 'grimlock_navigation_customizer_menu_item_active_background_color_elements', array( $this, 'add_menu_item_active_background_color_elements' ), 10, 1 );
		add_filter( 'grimlock_navigation_customizer_sub_menu_item_background_color_outputs',     array( $this, 'add_sub_menu_item_background_color_outputs'     ), 10, 1 );
		add_filter( 'grimlock_navigation_customizer_sub_menu_item_color_outputs',                array( $this, 'add_sub_menu_item_color_outputs'                ), 10, 1 );
		add_filter( 'grimlock_navigation_customizer_font_elements',                              array( $this, 'add_font_elements'                              ), 10, 1 );
		add_filter( 'grimlock_navigation_customizer_layout_field_args',                          array( $this, 'change_layout_field_args'                       ), 10, 1 );
		add_filter( 'grimlock_navigation_customizer_position_field_args',                        array( $this, 'change_position_field_args'                     ), 10, 1 );
		add_filter( 'grimlock_navigation_customizer_search_form_modal_displayed_field_args',     array( $this, 'change_search_form_modal_displayed_field_args'  ), 10, 1 );
		add_filter( 'grimlock_navigation_customizer_secondary_logo_field_args',                  array( $this, 'change_secondary_logo_field_args'               ), 10, 1 );
		add_filter( 'grimlock_navigation_customizer_secondary_logo_size_field_args',             array( $this, 'change_secondary_logo_field_args'               ), 10, 1 );
	}

	/**
	 * Register default values, settings and custom controls for the Theme Customizer.
	 */
	public function add_customizer_fields() {
		parent::add_customizer_fields();

		$this->defaults = apply_filters( 'cera_grimlock_navigation_customizer_defaults', array_merge( $this->defaults, array(
			'navigation_fixed_logged_out_displayed' => true,
			'navigation_fixed_default_state'        => 'open',
		) ) );

		$this->add_navigation_fixed_default_state_field(          array( 'priority' => 315 ) );
		$this->add_navigation_fixed_logged_out_displayed_field(   array( 'priority' => 316 ) );
	}

	/**
	 * Add tabs to the Customizer to group controls.
	 *
	 * @param  array $js_data The array of data for the Customizer controls.
	 *
	 * @return array          The filtered array of data for the Customizer controls.
	 */
	public function add_customizer_controls_js_data( $js_data ) {
		$js_data['tabs'][ $this->section ][2]['controls'][] = 'navigation_fixed_default_state';
		$js_data['tabs'][ $this->section ][2]['controls'][] = 'navigation_fixed_logged_out_displayed';
		return $js_data;
	}

	/**
	 * Add a Kirki checkbox field in the customizer to set whether the navigation should be displayed when logged out
	 *
	 * @param array $args The array of arguments for the Kirki field.
	 */
	protected function add_navigation_fixed_logged_out_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'checkbox',
				'section'  => $this->section,
				'label'    => esc_html__( 'Display Vertical Navigation for Logged Out Users', 'cera' ),
				'settings' => 'navigation_fixed_logged_out_displayed',
				'default'  => $this->get_default( 'navigation_fixed_logged_out_displayed' ),
				'priority' => 10,
				'active_callback' => array(
					array(
						array(
							'setting'  => 'navigation_layout',
							'operator' => '===',
							'value'    => 'fixed-left',
						),
						array(
							'setting'  => 'navigation_layout',
							'operator' => '===',
							'value'    => 'fixed-right',
						),
					)
				),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'cera_grimlock_navigation_customizer_vertical_logged_out_displayed_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki radio field set the default state of the vertical navigation
	 *
	 * @param array $args
	 */
	public function add_navigation_fixed_default_state_field( $args = array() ) {
		if ( class_exists( 'Kirki') ) {
			$args = wp_parse_args( $args, array(
				'type'      => 'radio',
				'label'     => esc_html__( 'Vertical Navigation Default State', 'cera' ),
				'section'   => $this->section,
				'settings'  => 'navigation_fixed_default_state',
				'default'   => $this->get_default( 'navigation_fixed_default_state' ),
				'priority'  => 10,
				'active_callback' => array(
					array(
						array(
							'setting'  => 'navigation_layout',
							'operator' => '===',
							'value'    => 'fixed-left',
						),
						array(
							'setting'  => 'navigation_layout',
							'operator' => '===',
							'value'    => 'fixed-right',
						),
					)
				),
				'choices'   => array(
					'open'   => esc_attr__( 'Open', 'cera' ),
					'closed' => esc_attr__( 'Closed', 'cera' ),
				),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'cera_grimlock_navigation_customizer_vertical_default_state_field_args', $args ) );
		}
	}

	/**
	 * Allows to check if the navigation should be displayed when user is logged out
	 *
	 * @return bool True if it should be displayed, false if not
	 */
	protected function get_navigation_logged_out_displayed() {
		return ! $this->get_theme_mod( 'navigation_fixed_logged_out_displayed' ) &&
		       in_array( $this->get_theme_mod( 'navigation_layout' ), array( 'fixed-left', 'fixed-right' ) );
	}

	/**
	 * Change the navigation component args
	 *
	 * @param array $args The navigation component args
	 *
	 * @return array The modified args
	 */
	public function change_navigation_args( $args ) {
		if ( $this->get_navigation_logged_out_displayed() ) {
			$args['displayed'] = is_user_logged_in();
		}

		return $args;
	}

	/**
	 * Add/remove custom classes on body
	 *
	 * @param array $classes
	 *
	 * @return array
	 */
	public function change_body_classes( $classes ) {
		if ( $this->get_navigation_logged_out_displayed() && ! is_user_logged_in() ) {
			$classes = array_diff( $classes, array(
				"grimlock--navigation-{$this->get_theme_mod( 'navigation_layout' )}",
				"grimlock--navigation-{$this->get_theme_mod( 'navigation_position' )}",
				'grimlock--navigation-fixed',
			) );

			$classes[] = 'grimlock--navigation-hidden';
		}

		$vertical_nav_default_state = isset( $_COOKIE['cera_vertical_nav_state'] ) ? sanitize_text_field( $_COOKIE['cera_vertical_nav_state'] ) : '';
		if ( ! in_array( $vertical_nav_default_state, array( 'open', 'closed' ) ) ) {
			$vertical_nav_default_state = $this->get_theme_mod( 'navigation_fixed_default_state' );
		}

		if ( ( $vertical_nav_default_state === 'closed' ) ) {
			$classes[] = 'slideout-mini';
		}

		return $classes;
	}

	/**
	 * Change default values and control settings for the Customizer.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $defaults The array of default values for the Customizer controls.
	 *
	 * @return array           The updated array of default values for the Customizer controls.
	 */
	public function change_defaults( $defaults ) {
		$defaults['navigation_font'] = array(
			'font-family'    => CERA_NAVIGATION_FONT_FAMILY,
			'font-weight'    => CERA_NAVIGATION_FONT_WEIGHT,
			'font-size'      => CERA_NAVIGATION_FONT_SIZE,
			'line-height'    => CERA_NAVIGATION_LINE_HEIGHT,
			'letter-spacing' => CERA_NAVIGATION_LETTER_SPACING,
			'subsets'        => CERA_NAVIGATION_SUBSETS,
			'text-transform' => CERA_NAVIGATION_TEXT_TRANSFORM,
		);

		$defaults['navigation_background_color']                  = CERA_NAVIGATION_BACKGROUND;
		$defaults['navigation_menu_item_color']                   = CERA_NAVIGATION_ITEM_COLOR;
		$defaults['navigation_menu_item_active_background_color'] = CERA_NAVIGATION_ITEM_ACTIVE_BACKGROUND_COLOR;
		$defaults['navigation_menu_item_active_color']            = CERA_NAVIGATION_ITEM_COLOR_ACTIVE;
		$defaults['navigation_sub_menu_item_color']               = CERA_NAVIGATION_SUB_MENU_ITEM_COLOR;
		$defaults['navigation_sub_menu_item_background_color']    = CERA_NAVIGATION_SUB_MENU_ITEM_BACKGROUND_COLOR;

		$defaults['navigation_border_bottom_width'] = CERA_NAVIGATION_BORDER_BOTTOM_WIDTH;
		$defaults['navigation_border_bottom_top']   = CERA_NAVIGATION_BORDER_TOP_WIDTH;
		$defaults['navigation_border_bottom_color'] = CERA_NAVIGATION_BORDER_COLOR;

		$defaults['navigation_layout']                        = CERA_NAVIGATION_LAYOUT;
		$defaults['navigation_secondary_logo']                = '';
		$defaults['navigation_position']                      = CERA_NAVIGATION_POSITION;
		$defaults['navigation_container_layout']              = CERA_NAVIGATION_CONTAINER_LAYOUT;
		$defaults['navigation_padding_y']                     = CERA_NAVIGATION_PADDING_Y;
		$defaults['navigation_stick_to_top']                  = CERA_NAVIGATION_STICK_TO_TOP;
		$defaults['navigation_stick_to_top_background_color'] = CERA_NAVIGATION_STICK_TO_TOP_BACKGROUND;
		$defaults['navigation_mobile_background_color']       = CERA_NAVIGATION_MOBILE_BACKGROUND;

		$defaults['navigation_search_form_displayed']               = CERA_NAVIGATION_SEARCH_FORM_DISPLAYED;
		$defaults['navigation_search_form_color']                   = CERA_NAVIGATION_SEARCH_FORM_COLOR;
		$defaults['navigation_search_form_placeholder_color']       = CERA_NAVIGATION_SEARCH_FORM_PLACEHOLDER_COLOR;
		$defaults['navigation_search_form_background_color']        = CERA_NAVIGATION_SEARCH_FORM_BACKGROUND_COLOR;
		$defaults['navigation_search_form_active_background_color'] = CERA_NAVIGATION_SEARCH_FORM_ACTIVE_BACKGROUND_COLOR;
		$defaults['navigation_search_form_active_color']            = CERA_NAVIGATION_SEARCH_FORM_ACTIVE_COLOR;

		return $defaults;
	}

	/**
	 * Add selectors and properties to the CSS rule-set for the navigation bottom border.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $outputs The array of CSS selectors and properties for the navigation bottom border.
	 *
	 * @return array          The updated array of CSS selectors for the navigation bottom border.
	 */
	public function add_border_bottom_width_outputs( $outputs ) {
		return array_merge( $outputs, array(
			array(
				'element'  => implode( ',', array(
					'.main-navigation .navbar-nav.navbar-nav--main-menu > .menu-item > a:before',
				) ),
				'property' => 'bottom',
				'prefix'   => '-',
				'units'    => 'px',
			),
		) );
	}

	/**
	 * Add CSS selectors to the array of CSS selectors for the menu item active color.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $elements The array of CSS selectors for the menu item active color.
	 *
	 * @return array           The updated array of CSS selectors for the menu item active color.
	 */
	public function add_menu_item_active_color_elements( $elements ) {
		return array_merge( $elements, array(
			'.navbar-search.navbar-search--animate:not(.navbar-search--open) .search-icon:hover',
			'.navbar-search.navbar-search--animate:not(.navbar-search--open) .search-icon:active',
			'.vertical-navbar .vertical-navbar__widgets a:not(.btn):hover',
			'.vertical-navbar .vertical-navbar__widgets a:not(.btn):active',
			'.vertical-navbar .vertical-navbar__widgets a:not(.btn):focus',
			'.vertical-navbar .site_identity',
		) );
	}

	/**
	 * Add CSS selectors to the array of CSS selectors for the menu item active color.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $elements The array of CSS selectors for the menu item active color.
	 *
	 * @return array           The updated array of CSS selectors for the menu item active color.
	 */
	public function add_menu_item_active_background_color_elements( $elements ) {
		return array_merge( $elements, array(
			'.navbar-search.navbar-search--animate:not(.navbar-search--open) .search-icon:hover',
		) );
	}


	/**
	 * Add selectors and properties to the CSS rule-set for the sub-menu item background color.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $outputs The array of CSS selectors and properties for the sub-menu item background color.
	 *
	 * @return array          The updated array of CSS selectors for the sub-menu item background color.
	 */
	public function add_background_color_outputs( $outputs ) {
		return array_merge( $outputs, array(
			array(
				'element'  => implode( ',', array(
					'.vertical-navbar .vertical-navbar-brand',
					// Grimlock Login
					'#navigation .nav.navbar-nav.navbar-nav--login li.menu-item.menu-item--login .btn',
					'#navigation .nav.navbar-nav.navbar-nav--login li.menu-item.menu-item--register .btn',
				) ),
				'property' => 'background-color',
			),
			array(
				'element'  => implode( ',', array(
					'.vertical-navbar.main-navigation',
				) ),
				'property' => 'background-color',
				'media_query' => '@media (max-width: 992px)',
			),
			array(
				'element'       => implode( ',', array(
					'.main-navigation .widget:not(.widget-content).widget_bp_core_whos_online_widget .item-avatar > a:before',
				) ),
				'property'      => 'box-shadow',
				'value_pattern' => '0 0 0 5px $',
			),
			array(
				'element'       => implode( ',', array(
					'.slideout-mini.grimlock--navigation-fixed-left .slideout-wrapper .navbar-nav .menu-item a ins',
					'.slideout-mini.grimlock--navigation-fixed-right .slideout-wrapper .navbar-nav .menu-item a ins',
				) ),
				'property'      => 'box-shadow',
				'value_pattern' => '0 0 0 4px $',
				'media_query'   => '@media (min-width: 992px)',
			),
			array(
				'element'  => implode( ',', array(
					'[class*="grimlock--navigation-hamburger-"] .hamburger-navbar',
					'.grimlock--navigation-fixed-left .hamburger-navbar',
					'.grimlock--navigation-fixed-right .hamburger-navbar',
				) ),
				'property'    => 'background-color',
				'media_query' => '@media (max-width: 992px)',
				'suffix'      => '!important',
			),
		) );
	}

	/**
	 * Add selectors and properties to the CSS rule-set for the sub-menu item background color.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $outputs The array of CSS selectors and properties for the sub-menu item background color.
	 *
	 * @return array          The updated array of CSS selectors for the sub-menu item background color.
	 */
	public function add_menu_item_color_outputs( $outputs ) {
		return array_merge( $outputs, array(
			array(
				'element'  => implode( ',', array(
					// Grimlock Login
					'#navigation .nav.navbar-nav.navbar-nav--login li.menu-item.menu-item--login .btn',
				) ),
				'property' => 'color',
			),
			array(
				'element'  => implode( ',', array(
					// Grimlock Login
					'#navigation .nav.navbar-nav.navbar-nav--login li.menu-item.menu-item--login .btn:hover',
				) ),
				'property' => 'border-color',
			),
			array(
				'element'  => implode( ',', array(
					// Grimlock Login
					'#navigation .nav.navbar-nav.navbar-nav--login li.menu-item.menu-item--login .btn',
				) ),
				'property' => 'border-color',
				'suffix'   => '33',
			),
			array(
				'element'  => implode( ',', array(
					// Grimlock Login
					'#navigation .nav.navbar-nav.navbar-nav--login li.menu-item.menu-item--login .btn:hover',
				) ),
				'property' => 'background-color',
				'suffix'   => '0d',
			),
		) );
	}

	/**
	 * Add selectors and properties to the CSS rule-set for the sub-menu item background color.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $outputs The array of CSS selectors and properties for the sub-menu item background color.
	 *
	 * @return array          The updated array of CSS selectors for the sub-menu item background color.
	 */
	public function add_menu_item_active_color_outputs( $outputs ) {
		return array_merge( $outputs, array(
			array(
				'element'  => implode( ',', array(
					'.main-navigation .navbar-nav.navbar-nav--main-menu > .menu-item > a:before',
				) ),
				'property' => 'background-color',
			),
			array(
				'element'  => implode( ',', array(
					// Grimlock Login
					'#navigation .nav.navbar-nav.navbar-nav--login li.menu-item.menu-item--register .btn',
				) ),
				'property' => 'color',
			),
			array(
				'element'  => implode( ',', array(
					// Grimlock Login
					'#navigation .nav.navbar-nav.navbar-nav--login li.menu-item.menu-item--register .btn:hover',
				) ),
				'property' => 'border-color',
			),
			array(
				'element'  => implode( ',', array(
					// Grimlock Login
					'#navigation .nav.navbar-nav.navbar-nav--login li.menu-item.menu-item--register .btn',
				) ),
				'property' => 'border-color',
				'suffix'   => '59',
			),
			array(
				'element'  => implode( ',', array(
					// Grimlock Login
					'#navigation .nav.navbar-nav.navbar-nav--login li.menu-item.menu-item--register .btn',
				) ),
				'property' => 'background-color',
				'suffix'   => '1c',
			),
		) );
	}

	/**
	 * Add selectors and properties to the CSS rule-set for the sub-menu item background color.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $outputs The array of CSS selectors and properties for the sub-menu item background color.
	 *
	 * @return array          The updated array of CSS selectors for the sub-menu item background color.
	 */
	public function add_sub_menu_item_background_color_outputs( $outputs ) {
		return array_merge( $outputs, array(
			array(
				'element'  => implode( ',', array(
					'.dropdown-classic .dropdown-menu',
					'.dropdown-classic .dropdown-menu a',
				) ),
				'property' => 'background-color',
				'suffix'   => '!important',
			),
			array(
				'element'  => implode( ',', array(
					'.dropdown-classic .dropdown-menu a:hover',
				) ),
				'property' => 'color',
				'suffix'   => '!important',
			),
		) );
	}

	/**
	 * Add selectors and properties to the CSS rule-set for the sub-menu item color.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $outputs The array of CSS selectors and properties for the sub-menu item color.
	 *
	 * @return array          The updated array of CSS selectors for the sub-menu item color.
	 */
	public function add_sub_menu_item_color_outputs( $outputs ) {
		return array_merge( $outputs, array(
			array(
				'element'  => implode( ',', array(
					'.dropdown-classic .dropdown-menu',
					'.dropdown-classic .dropdown-menu a',
				) ),
				'property' => 'color',
				'suffix'   => '!important',
			),
			array(
				'element'  => implode( ',', array(
					'.dropdown-classic .dropdown-menu a:hover',
				) ),
				'property' => 'background-color',
				'suffix'   => '!important',
			),
		) );
	}


	/**
	 * Add CSS selectors to the array of CSS selectors for the navigation font.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $elements The array of CSS selectors for the navigation font.
	 *
	 * @return array           The updated array of CSS selectors for the navigation font.
	 */
	public function add_font_elements( $elements ) {
		return array_merge( $elements, array(
			'.navbar-nav--hamburger-menu .menu-item .btn',
			'.navbar-nav--login .menu-item .btn',
		) );
	}

	/**
	 * Change layout field args
	 *
	 * @since 1.0.0
	 *
	 * @param  array $args The array of field args
	 *
	 * @return array       The updated array of field args
	 */
	public function change_layout_field_args( $args ) {
		unset( $args['choices']['fat-left'] );
		unset( $args['choices']['fat-center'] );

		return $args;
	}

	/**
	 * Change position field args
	 *
	 * @since 1.0.0
	 *
	 * @param  array $args The array of field args
	 *
	 * @return array       The updated array of field args
	 */
	public function change_position_field_args( $args ) {
		unset( $args['choices']['classic-bottom'] );
		unset( $args['choices']['inside-bottom'] );

		return $args;
	}

	/**
	 * Change search form modal displayed field args
	 *
	 * @param  array $args The array of field args
	 *
	 * @return array       The updated array of field args
	 */
	public function change_search_form_modal_displayed_field_args( $args ) {
		if ( empty( $args['active_callback'] ) ) {
			$args['active_callback'] = array();
		}

		$args['active_callback'][] = array(
			'setting'  => 'navigation_layout',
			'operator' => 'in',
			'value'    => array( 'classic-left', 'classic-center', 'classic-right' ),
		);

		return $args;
	}

	/**
	 * Change secondary logo field args
	 *
	 * @param  array $args The array of field args
	 *
	 * @return array       The updated array of field args
	 */
	public function change_secondary_logo_field_args( $args ) {
		if ( ! empty( $args['active_callback'][0] ) && is_array( $args['active_callback'][0]['value'] ) ) {
			$args['active_callback'][0]['value'][] = 'fixed-left';
			$args['active_callback'][0]['value'][] = 'fixed-right';
		}

		return $args;
	}
}

return new Cera_Grimlock_Navigation_Customizer();
