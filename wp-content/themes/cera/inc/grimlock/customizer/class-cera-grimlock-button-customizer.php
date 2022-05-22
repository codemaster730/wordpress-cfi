<?php
/**
 * Cera_Grimlock_Button_Customizer Class
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
 * The button class for the Customizer.
 */
class Cera_Grimlock_Button_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'grimlock_button_customizer_defaults',                            array( $this, 'change_defaults'                         ), 10, 1 );
		add_filter( 'grimlock_button_customizer_elements',                            array( $this, 'add_elements'                            ), 10, 1 );
		add_filter( 'grimlock_button_customizer_primary_elements',                    array( $this, 'add_primary_elements'                    ), 10, 1 );
		add_filter( 'grimlock_button_customizer_secondary_elements',                  array( $this, 'add_secondary_elements'                  ), 10, 1 );
		add_filter( 'grimlock_button_customizer_primary_background_color_elements',   array( $this, 'add_primary_background_color_elements'   ), 10, 1 );
		add_filter( 'grimlock_button_customizer_primary_background_color_outputs',    array( $this, 'add_primary_background_color_outputs'    ), 10, 1 );
		add_filter( 'grimlock_button_customizer_secondary_background_color_elements', array( $this, 'add_secondary_background_color_elements' ), 10, 1 );
		add_filter( 'grimlock_button_customizer_secondary_background_color_outputs',  array( $this, 'add_secondary_background_color_outputs'  ), 10, 1 );
		add_filter( 'grimlock_button_customizer_primary_color_elements',              array( $this, 'add_primary_color_elements'              ), 10, 1 );
		add_filter( 'grimlock_button_customizer_primary_color_outputs',               array( $this, 'add_primary_color_outputs'               ), 10, 1 );
		add_filter( 'grimlock_button_customizer_border_radius_elements',              array( $this, 'add_border_radius_elements'              ), 10, 1 );
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
		$defaults['button_font']          = array(
			'font-family'    => CERA_FONT_FAMILY_BASE,
			'font-weight'    => CERA_BUTTON_FONT_VARIANT,
			'font-size'      => CERA_BUTTON_FONT_SIZE,
			'line-height'    => CERA_BUTTON_LINE_HEIGHT,
			'letter-spacing' => CERA_BUTTON_FONT_LETTER_SPACING,
			'subsets'        => array( 'latin-ext' ),
			'text-transform' => CERA_BUTTON_FONT_TEXT_TRANSFORM,
		);
		$defaults['button_border_radius'] = CERA_BUTTON_BORDER_RADIUS;
		$defaults['button_border_width']  = CERA_BUTTON_BORDER_WIDTH;
		$defaults['button_padding_y']     = CERA_BUTTON_PADDING_Y;
		$defaults['button_padding_x']     = CERA_BUTTON_PADDING_X;

		$defaults['button_primary_background_color']       = CERA_BUTTON_PRIMARY_BACKGROUND_COLOR;
		$defaults['button_primary_color']                  = CERA_BUTTON_PRIMARY_COLOR;
		$defaults['button_primary_border_color']           = CERA_BUTTON_PRIMARY_BORDER_COLOR;
		$defaults['button_primary_hover_background_color'] = CERA_BUTTON_PRIMARY_HOVER_BACKGROUND_COLOR;
		$defaults['button_primary_hover_color']            = CERA_BUTTON_PRIMARY_HOVER_COLOR;
		$defaults['button_primary_hover_border_color']     = CERA_BUTTON_PRIMARY_HOVER_BORDER_COLOR;

		$defaults['button_secondary_background_color']       = CERA_BUTTON_SECONDARY_BACKGROUND_COLOR;
		$defaults['button_secondary_color']                  = CERA_BUTTON_SECONDARY_COLOR;
		$defaults['button_secondary_border_color']           = CERA_BUTTON_SECONDARY_BORDER_COLOR;
		$defaults['button_secondary_hover_background_color'] = CERA_BUTTON_SECONDARY_HOVER_BACKGROUND_COLOR;
		$defaults['button_secondary_hover_color']            = CERA_BUTTON_SECONDARY_HOVER_COLOR;
		$defaults['button_secondary_hover_border_color']     = CERA_BUTTON_SECONDARY_HOVER_BORDER_COLOR;
		return $defaults;
	}

	/**
	 * Add CSS selectors to the array of CSS selectors for the button.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $elements The array of CSS selectors for the button.
	 *
	 * @return array           The updated array of CSS selectors for the button.
	 */
	public function add_elements( $elements ) {
		return array_merge( $elements, array(
			'.btn-selector',
			'.vertical-navbar .navbar-nav.navbar-nav--login .menu-item > a.btn',
			'.vertical-navbar .navbar-nav--login .menu-item .btn',
			'.posts-filters .posts-filter .nav-link',
			'.posts-filters .posts-filters__child .priority-nav__dropdown a',
		) );
	}

	/**
	 * Add CSS selectors to the array of CSS selectors for the secondary button.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $elements The array of CSS selectors for the secondary button.
	 *
	 * @return array           The updated array of CSS selectors for the secondary button.
	 */
	public function add_secondary_elements( $elements ) {
		return array_merge( $elements, array(
			'.grimlock-section .section__title ins[class*="decoration--block"].decoration--inverse',
			'.grimlock-section .section__subtitle ins[class*="decoration--block"].decoration--inverse',
			'.posts-filters .posts-filter .nav-link',
			'.posts-filters .posts-filters__child .priority-nav__dropdown a',
		) );
	}

	/**
	 * Add CSS selectors to the array of CSS selectors for the secondary button background-color.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $elements The array of CSS selectors for the secondary button background-color.
	 *
	 * @return array           The updated array of CSS selectors for the secondary button background-color.
	 */
	public function add_secondary_background_color_elements( $elements ) {
		return array_merge( $elements, array(
			'.grimlock-section .section__title ins.decoration--secondary:after',
			'.grimlock-section .section__subtitle ins.decoration--secondary:after',
		) );
	}

	/**
	 * Add CSS selectors to the array of CSS selectors for the primary button.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $elements The array of CSS selectors for the primary button.
	 *
	 * @return array           The updated array of CSS selectors for the primary button.
	 */
	public function add_primary_elements( $elements ) {
		return array_merge( $elements, array(
			'.btn-primary-selector',
			'.login-submit > #wp-submit',
			'.grimlock-section .section__title ins[class*="decoration--block"]',
			'.grimlock-section .section__subtitle ins[class*="decoration--block"]',
			'.vertical-navbar .navbar-nav.navbar-nav--login .menu-item > a.btn.btn-primary',
			'.grimlock-login-navbar_nav_menu .menu-item.menu-item--register a.btn-primary',
			'.vertical-navbar .navbar-nav .menu-item > a ins',
		) );
	}

	/**
	 * Add CSS selectors to the array of CSS selectors for the primary button color.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $elements The array of CSS selectors for the primary button color.
	 *
	 * @return array           The updated array of CSS selectors for the primary button color.
	 */
	public function add_primary_color_elements( $elements ) {
		return array_merge( $elements, array(
			'.datepicker table tr td.active.active',
			'.datepicker table tr td.active.disabled',
			'.datepicker table tr td.active.disabled.active',
			'.datepicker table tr td.active.disabled.disabled',
			'.datepicker table tr td.active.disabled:active',
			'.datepicker table tr td.active.disabled:hover',
			'.datepicker table tr td.active:active',
			'.datepicker table tr td.active:hover',
		) );
	}

	/**
	 * Add CSS selectors to the array of CSS selectors for the button border radius.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $elements The array of CSS selectors for the button border radius.
	 *
	 * @return array           The updated array of CSS selectors for the button border radius.
	 */
	public function add_border_radius_elements( $elements ) {
		return array_merge( $elements, array(
			'.posts-filters .posts-filter .nav-link',
			'.modal .login-footer a',
			'.yza-form-actions > a, .yza-form-actions > button',
			'.posts-filters .priority-nav__dropdown-toggle',
			'.vertical-navbar .vertical-navbar-search .search-field',
			'.hamburger-navbar .hamburger-navbar-nav > .menu-item > a',
			'.vertical-navbar .navbar-nav .menu-item > a ins',
		) );
	}

	/**
	 * Add selectors and properties to the CSS rule-set for the primary button color.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $outputs The array of CSS selectors and properties for the primary button color.
	 *
	 * @return array          The updated array of CSS selectors for the primary button color.
	 */
	public function add_primary_color_outputs( $outputs ) {
		return array_merge( $outputs, array(
			array(
				'element'  => implode( ',', array(
					'.posts [id^="post-"].format-link .card-body a',
				) ),
				'property' => 'color',
				'suffix'   => '!important',
			),
		) );
	}

	/**
	 * Add selectors and properties to the CSS rule-set for the secondary button background color.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $outputs The array of CSS selectors and properties for the secondary button background color.
	 *
	 * @return array          The updated array of CSS selectors for the secondary button background color.
	 */
	public function add_secondary_background_color_outputs( $outputs ) {
		return array_merge( $outputs, array(
			array(
				'element'  => implode( ',', array(
					'.posts-filters .priority-nav__dropdown-toggle:hover',
					'.posts-filters .priority-nav__dropdown-toggle.is-open',
				) ),
				'property' => 'background-color',
			),
			array(
				'element'  => implode( ',', array(
					'.posts-filters .priority-nav__dropdown-toggle:hover',
					'.posts-filters .priority-nav__dropdown-toggle.is-open',
				) ),
				'property' => 'border-color',
			),
		) );
	}

	/**
	 * Add CSS selectors to the array of CSS selectors for the primary button background color.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $elements The array of CSS selectors for the primary button background color.
	 *
	 * @return array           The updated array of CSS selectors for the primary button background color.
	 */
	public function add_primary_background_color_elements( $elements ) {
		return array_merge( $elements, array(
			'.main-navigation .navbar-nav.navbar-nav--buddypress.logged-out .menu-item--profile:hover:after',
			'.grimlock-hero.region--12-cols-center-boxed .section__content:before',
			'.mejs-controls .mejs-time-rail .mejs-time-current',
			'.grimlock-section:not(.grimlock-hero):not(.grimlock-custom_header).section--full-viewport',
			'#custom_header',
			'.custom-checkbox .custom-control-input:checked ~ .custom-control-label::before',
			'.posts [id^="post-"].format-link .card-body',
			'.grimlock .parallax-mirror',
		) );
	}

	/**
	 * Add selectors and properties to the CSS rule-set for the primary button background color.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $outputs The array of CSS selectors and properties for the primary button background color.
	 *
	 * @return array          The updated array of CSS selectors for the primary button background color.
	 */
	public function add_primary_background_color_outputs( $outputs ) {
		return array_merge( $outputs, array(
			array(
				'element'  => implode( ',', array(
					'.wp-block-pullquote',
				) ),
				'property' => 'border-color',
				'suffix'   => '!important',
			),
			array(
				'element'  => implode( ',', array(
					'.card .author img:hover',
					'.card .author .avatar-round-ratio:hover',
					'.posts--3-3-3-3-cols-classic .card .author .avatar-round-ratio:hover',
					'.posts--4-4-4-cols-classic .card .author .avatar-round-ratio:hover',
					'.posts--6-6-cols-classic .card .author .avatar-round-ratio:hover',
					'.custom-control.custom-checkbox:hover .custom-control-label:before',
				) ),
				'property' => 'border-color',
			),
			array(
				'element'  => implode( ',', array(
					'.grimlock-section .section__title ins.decoration--brush:before',
					'.grimlock-section .section__subtitle ins.decoration--brush:before',
					'.posts-filters .priority-nav__dropdown-toggle:hover',
					'.posts-filters .priority-nav__dropdown-toggle.is-open',
				) ),
				'property' => 'color',
			),
		) );
	}




}

return new Cera_Grimlock_Button_Customizer();
