<?php
/**
 * Grimlock_BuddyPress_Control_Customizer Class
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
 * The Grimlock BuddyPress Customizer style class.
 */
class Grimlock_BuddyPress_Control_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'grimlock_control_customizer_elements',                    array( $this, 'add_elements'                    ), 10, 1 );
		add_filter( 'grimlock_control_customizer_color_elements',              array( $this, 'add_color_elements'              ), 10, 1 );
		add_filter( 'grimlock_control_customizer_background_color_elements',   array( $this, 'add_background_color_elements'   ), 10, 1 );
		add_filter( 'grimlock_control_customizer_border_width_elements',       array( $this, 'add_border_width_elements'       ), 10, 1 );
		add_filter( 'grimlock_control_customizer_border_width_outputs',        array( $this, 'add_border_width_outputs'        ), 10, 1 );
		add_filter( 'grimlock_control_customizer_border_color_elements',       array( $this, 'add_border_color_elements'       ), 10, 1 );
		add_filter( 'grimlock_control_customizer_border_color_outputs',        array( $this, 'add_border_color_outputs'        ), 10, 1 );
		add_filter( 'grimlock_control_customizer_border_radius_elements',      array( $this, 'add_border_radius_elements'      ), 10, 1 );
		add_filter( 'grimlock_control_customizer_focus_border_color_elements', array( $this, 'add_focus_border_color_elements' ), 10, 1 );
	}
	/**
	 * @param $elements
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function add_elements( $elements ) {
		foreach ( $elements as $element ) {
			$elements[] = "#buddypress .standard-form {$element}";
			$elements[] = "#buddypress div.dir-search {$element}";
		}
		$elements[] = "#buddypress li.groups-members-search input[type=text]";

		$elements = array_merge( $elements, array(
			'.bp-messages-wrap .taggle_list',
			'.bp-messages-wrap .new-message form > div input',
			'.bp-messages-wrap .new-message form > div textarea',
			'.bp-emojionearea',
			'.youzer .yz-wall-textarea',
			'bp-.emojionearea.form-control',
			'#buddypress:not(.youzer) .standard-form input[type="search"]',
			'#buddypress:not(.youzer) .standard-form input[type="text"]',
			'#buddypress:not(.youzer) .standard-form input[type="date"]',
			'#buddypress:not(.youzer) .standard-form input[type="datetime-local"]',
			'#buddypress:not(.youzer) .standard-form input[type="datetime"]',
			'#buddypress:not(.youzer) .standard-form input[type="email"]',
			'#buddypress:not(.youzer) .standard-form input[type="number"]',
			'#buddypress:not(.youzer) .standard-form input[type="password"]',
			'#buddypress:not(.youzer) .standard-form input[type="search"]',
			'#buddypress:not(.youzer) .standard-form input[type="tel"]',
			'#buddypress:not(.youzer) .standard-form input[type="url"]',
			'#buddypress:not(.youzer) .standard-form select',
			'#buddypress:not(.youzer) .standard-form textarea',
			'.webui-popover-bp-extended-friendship-popup .request_friend_message',
			'.directory.members .region--9-3-cols-left #secondary-left .widget_bps_widget .bps-custom-select',
			'.directory.members .region--9-3-cols-left #secondary-right .widget_bps_widget .bps-custom-select',
			'.directory.members .region--3-9-cols-left #secondary-left .widget_bps_widget .bps-custom-select',
			'.directory.members .region--3-9-cols-left #secondary-right .widget_bps_widget .bps-custom-select',
			'.bps-custom-select',
			'#buddypress:not(.youzer) #item-body form#whats-new-form #whats-new-options select',
			'#buddypress:not(.youzer) form#whats-new-form #whats-new-options select',
			'#buddypress:not(.youzer) #activity-stream.grimlock-buddypress-activity-list .activity-item div.activity-comments form.ac-form .ac-textarea textarea',

			// TODO: Migrate to MediaPress class
			'form#mpp-whats-new-form textarea',
			'div.mpp-activity-comments form textarea',

			// TODO: Migrate to BP Docs class
			'.docs form input:not([type])',
			'.docs-filters form input:not([type])',
			'input[id="new-folder"]',


			'#bpchk-autocomplete-place',
		) );

		return $elements;
	}

	/**
	 * Add CSS selectors to the array of CSS selectors for the control border width.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $elements The array of CSS selectors for the control border width.
	 *
	 * @return array           The updated array of CSS selectors for the control border width.
	 */
	public function add_border_width_elements( $elements ) {
		return array_merge( $elements, array(
			'#buddypress:not(.youzer) form#whats-new-form #whats-new-options select',
			'.sa-field-front .select2-container--default .select2-selection--single',
			'.sa-field-front .select2-container--default .select2-selection--multiple',
			'.sa-field-front .select2-container--default.select2-container--focus .select2-selection--multiple',
			'.wp-core-ui.wp-editor-wrap.html-active textarea',
			'#yz-bp .bp-messages-wrap .bp-emojionearea .bp-emojionearea-editor',
			'#buddypress .bp-messages-wrap .bp-emojionearea .bp-emojionearea-editor'
		) );
	}

	/**
	 * Add selectors and properties to the CSS rule-set for the content background color.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $outputs The array of CSS selectors and properties for the content background color.
	 *
	 * @return array          The updated array of CSS selectors for the content background color.
	 */
	public function add_border_width_outputs( $outputs ) {
		return array_merge( $outputs, array(
			array(
				'element'  => implode( ',', array(
					'#wp-sa_content-editor-container',
					'#buddypress:not(.youzer) .wp-editor-container',
					'.wp-core-ui.wp-editor-wrap.html-active textarea',
				) ),
				'property' => 'border-width',
				'units'    => 'px',
				'suffix'   => '!important',
			),
		) );
	}

	/**
	 * Add CSS selectors to the array of CSS selectors for the control border color.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $elements The array of CSS selectors for the control border color.
	 *
	 * @return array           The updated array of CSS selectors for the control border color.
	 */
	public function add_border_color_elements( $elements ) {
		return array_merge( $elements, array(
			'.sa-field-front .select2-container--default .select2-selection--single',
			'.sa-field-front .select2-container--default .select2-selection--multiple',
			'.sa-field-front .select2-container--default.select2-container--focus .select2-selection--multiple',
			'#wp-sa_content-editor-container',
			'.sa-upload-image-container',
			'#buddypress:not(.youzer) .wp-editor-container',
			'.wp-core-ui.wp-editor-wrap.html-active textarea',
			'#yz-bp .bp-messages-wrap .bp-emojionearea .bp-emojionearea-editor',
			'#buddypress .bp-messages-wrap .bp-emojionearea .bp-emojionearea-editor'
		) );
	}

	/**
	 * Add selectors and properties to the CSS rule-set for the content background color.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $outputs The array of CSS selectors and properties for the content background color.
	 *
	 * @return array          The updated array of CSS selectors for the content background color.
	 */
	public function add_border_color_outputs( $outputs ) {
		return array_merge( $outputs, array(
			array(
				'element'  => implode( ',', array(
					'#wp-sa_content-editor-container',
					'#buddypress:not(.youzer) .wp-editor-container',
				) ),
				'property' => 'border-color',
				'suffix'   => '!important',
			),
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
			'.sa-field-front .select2-container--default .select2-selection--single',
			'.sa-field-front .select2-container--default .select2-selection--multiple',
			'.sa-field-front .select2-container--default.select2-container--focus .select2-selection--multiple',
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
			'.sa-field-front .select2-container--default .select2-selection--single .select2-selection__rendered',
			'.bps_form .editfield .btn-location',
		) );
	}

	/**
	 * Add CSS selectors to the array of CSS selectors for the control border radius.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $elements The array of CSS selectors for the control border radius.
	 *
	 * @return array           The updated array of CSS selectors for the control border radius.
	 */
	public function add_border_radius_elements( $elements ) {
		return array_merge( $elements, array(
			'.bps-form-home .editfield .bps-custom-select',
			'.sa-field-front .select2-container--default .select2-selection--single',
			'.sa-field-front .select2-container--default .select2-selection--multiple',
			'.sa-field-front .select2-container--default.select2-container--focus .select2-selection--multiple',
			'#wp-sa_content-editor-container',
			'.sa-upload-image-container',
			'.bp-messages-wrap .bp-emojionearea .bp-emojionearea-editor'
		) );
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
	public function add_focus_border_color_elements( $elements ) {
		return array_merge( $elements, array(
			'.bp-messages-wrap .chat-header .bpbm-search form input:focus',
			'.bp-messages-wrap .active .taggle_list',
			'.bp-messages-wrap .new-message form > div input:focus',
			'.bp-messages-wrap #send-to .ui-autocomplete',
			'.bp-messages-wrap .new-message form > div input:focus',
			'.bp-messages-wrap .new-message form > div textarea:focus',
			'#yz-bp .bp-messages-wrap .bp-emojionearea.focused .bp-emojionearea-editor',
			'#buddypress .bp-messages-wrap .bp-emojionearea.focused .bp-emojionearea-editor'
		) );
	}
}

return new Grimlock_BuddyPress_Control_Customizer();
