<?php
/**
 * Grimlock_BuddyPress_Youzify_Control_Customizer Class
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
class Grimlock_BuddyPress_Youzify_Control_Customizer {
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
			'.youzify .option-content input:not([type=radio]):not(.uk-upload-button):not(.wp-color-picker):not(.wp-picker-clear)',
			'#youzify-directory-search-box form input[type=text]',
			'.youzify #activity-stream .ac-form .bp-emojionearea .bp-emojionearea-editor',
			'.youzify #activity-stream .ac-form textarea',
			'.youzify-wall-custom-form .youzify-wall-cf-item input',
			'.youzify-wall-custom-form .youzify-wall-cf-item textarea',
			'.youzify .editfield input:not([type=radio]):not([type=checkbox])',
			'#group-settings-form .youzify-group-field-item input[type=text]',
			'.youzify-group-settings-tab .youzify-group-field-item input[type=text]',
			'.youzify-group-manage-members-search #search-members-form #members_search',
			'.youzify-form-item .bp-emojionearea .bp-emojionearea-editor',
			'.youzify-form-item input:not(.search)',
			'.youzify-form-item textarea',
			'.youzify .option-content textarea',
			'.logy-form-item input:not([type=checkbox])',
			'#youzify .youzify .yzwc-main-content form .form-row input.input-text',
			'#youzify .youzify .yzwc-main-content form .form-row textarea',
			'#youzify #group-settings-form textarea',
			'#youzify .youzify-group-settings-tab textarea',
			'.youzify #activity-stream .ac-form .youzify-emojionearea .youzify-emojionearea-editor',
			'.youzify #activity-stream .ac-form textarea',
			'#youzify .editfield input:not([type=radio]):not([type=checkbox]):not(.ed_button)',
			'#youzify .option-content input:not([type=radio]):not(.uk-upload-button):not(.wp-color-picker):not(.wp-picker-clear):not(.ed_button)',


			'.youzify-msg-form-item .emojionearea .emojionearea-editor',
			'.youzify-msg-form-item input:not(.search)',
			'.youzify-msg-form-item textarea',
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
			'.youzify #bbpress-forums select',
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
			'.youzify #bbpress-forums select',
		) );
	}
}

return new Grimlock_BuddyPress_Youzify_Control_Customizer();
