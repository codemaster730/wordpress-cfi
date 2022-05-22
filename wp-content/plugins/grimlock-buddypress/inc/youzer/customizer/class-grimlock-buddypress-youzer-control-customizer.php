<?php
/**
 * Grimlock_BuddyPress_Youzer_Control_Customizer Class
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
 * The control class for the Customizer.
 */
class Grimlock_BuddyPress_Youzer_Control_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'grimlock_control_customizer_elements',                  array( $this, 'add_elements'                  ), 10, 1 );
		add_filter( 'grimlock_control_customizer_color_elements',            array( $this, 'add_color_elements'            ), 10, 1 );
		add_filter( 'grimlock_control_customizer_background_color_elements', array( $this, 'add_background_color_elements' ), 10, 1 );
	}

	/**
	 * Add CSS selectors to the array of CSS selectors for the controls.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $elements The array of CSS selectors for the controls.
	 *
	 * @return array           The updated array of CSS selectors for the controls.
	 */
	public function add_elements( $elements ) {
		return array_merge( $elements, array(
			'.nice-select',
			'.youzer .option-content input:not([type=radio]):not(.uk-upload-button):not(.wp-color-picker):not(.wp-picker-clear)',
			'#yz-directory-search-box form input[type=text]',
			'.youzer #activity-stream .ac-form .bp-emojionearea .bp-emojionearea-editor',
			'.youzer #activity-stream .ac-form textarea',
			'.yz-wall-custom-form .yz-wall-cf-item input',
			'.yz-wall-custom-form .yz-wall-cf-item textarea',
			'.youzer .editfield input:not([type=radio]):not([type=checkbox])',
			'#group-settings-form .yz-group-field-item input[type=text]',
			'.yz-group-settings-tab .yz-group-field-item input[type=text]',
			'.yz-group-manage-members-search #search-members-form #members_search',
			'.yzmsg-form-item .bp-emojionearea .bp-emojionearea-editor',
			'.yzmsg-form-item input:not(.search)',
			'.yzmsg-form-item textarea',
			'.youzer .option-content textarea',
			'.logy-form-item input:not([type=checkbox])',
			'#youzer .youzer .yzwc-main-content form .form-row input.input-text',
			'#youzer .youzer .yzwc-main-content form .form-row textarea',
		) );
	}

	/**
	 * Add CSS selectors to the array of CSS selectors for the control background color.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $elements The array of CSS selectors for the control background color.
	 *
	 * @return array           The updated array of CSS selectors for the control background color.
	 */
	public function add_background_color_elements( $elements ) {
		return array_merge( $elements, array(
			'.youzer #bbpress-forums select',
		) );
	}

	/**
	 * Add CSS selectors to the array of CSS selectors for the control color.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $elements The array of CSS selectors for the control color.
	 *
	 * @return array           The updated array of CSS selectors for the control color.
	 */
	public function add_color_elements( $elements ) {
		return array_merge( $elements, array(
			'.youzer #bbpress-forums select',
		) );
	}
}

return new Grimlock_BuddyPress_Youzer_Control_Customizer();
