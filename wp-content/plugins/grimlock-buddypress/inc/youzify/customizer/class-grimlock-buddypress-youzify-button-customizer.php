<?php
/**
 * Grimlock_BuddyPress_Youzify_Button_Customizer Class
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
class Grimlock_BuddyPress_Youzify_Button_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'grimlock_button_customizer_primary_elements',                  array( $this, 'add_primary_elements'                  ), 10, 1 );
		add_filter( 'grimlock_button_customizer_primary_color_elements',            array( $this, 'add_primary_color_elements'            ), 10, 1 );
		add_filter( 'grimlock_button_customizer_primary_background_color_elements', array( $this, 'add_primary_background_color_elements' ), 10, 1 );
		add_filter( 'grimlock_button_customizer_primary_background_color_outputs',  array( $this, 'add_primary_background_color_outputs'  ), 10, 1 );
		add_filter( 'grimlock_button_customizer_primary_color_outputs',             array( $this, 'add_primary_color_outputs'             ), 10, 1 );
		add_filter( 'grimlock_button_customizer_secondary_elements',                array( $this, 'add_secondary_elements'                ), 10, 1 );
		add_filter( 'grimlock_button_customizer_elements',                          array( $this, 'remove_elements'                       ), 10, 1 );
		add_filter( 'grimlock_button_customizer_border_radius_elements',            array( $this, 'remove_border_radius_elements'         ), 10, 1 );
		add_filter( 'grimlock_button_customizer_xs_elements',                       array( $this, 'remove_xs_elements'                    ), 10, 1 );
		add_filter( 'grimlock_button_customizer_secondary_elements',                array( $this, 'remove_secondary_elements'             ), 10, 1 );
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
			'#youzify-members-list > li .hmk-trigger-match .generic-button',
			'.youzify-pagination .youzify-nav-links .page-numbers:hover:not(.current)',
			'.pagination .current',
			'.youzify .pagination .page-numbers:not(.current):hover',
			'.youzify-settings-area .youzify-quick-buttons .youzify-button-count',
			'.youzify .drag-drop-buttons #bp-browse-button',
			'.youzify #activity-stream .ac-reply-content input[type=submit]',
			'.youzify #bp-data-export button',
			'#youzify-data-export a',
			'.youzify-account-page .youzify-export-item .ukai-button-item a',
			'.youzify-wg-portfolio-options .youzify-upload-photo',
			'.youzify-link-icon i',
			'.logy-form .logy-form-actions button',
			'div.youzify .bp-avatar-nav ul.avatar-nav-items li.current',
			'body[class*="youzify-"][class*="-scheme"] input[type=submit]',
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
			'#buddypress .comment-reply-link.bp-primary-action > span',
			'#buddypress .comment-reply-link#group-creation-next > span',
			'#buddypress .comment-edit-link.bp-primary-action > span',
			'#buddypress .comment-edit-link#group-creation-next > span',
			'#buddypress .generic-button a.bp-primary-action > span',
			'#buddypress .generic-button a#group-creation-next > span',
			'#buddypress a.button.bp-primary-action > span',
			'#buddypress a.button#group-creation-next > span',
			'#buddypress a.bp-primary-action > span',
			'#buddypress a#group-creation-next > span',
			'#buddypress div.item-list-tabs#subnav > ul > li.current',
			'#buddypress div.item-list-tabs#subnav > ul > li.selected',
			'body .quote-with-img blockquote',
			'div.youzify .bp-avatar-nav li.current a',
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
					'.nice-select .option.focus',
					'.nice-select .option.selected',
					'.nice-select .option.selected.focus',
					'.nice-select .option:hover',

					'[class*="youzify-"][class*="-scheme"] .youzify-pagination .page-numbers.current',
					'.pagination .current',
					'.youzify-file-post',
				) ),
				'property' => 'color',
				'suffix'   => '!important',
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
			'.youzify-aboutme-head:after',
			'input.cmn-toggle-round-flat:checked+label:after',
			'input.cmn-toggle-round-flat:checked+label',
			'#buddypress .comment-reply-link.bp-primary-action > span',
			'#buddypress .comment-reply-link#group-creation-next > span',
			'#buddypress .comment-edit-link.bp-primary-action > span',
			'#buddypress .comment-edit-link#group-creation-next > span',
			'#buddypress .generic-button a.bp-primary-action > span',
			'#buddypress .generic-button a#group-creation-next > span',
			'#buddypress a.button.bp-primary-action > span',
			'#buddypress a.button#group-creation-next > span',
			'#buddypress a.bp-primary-action > span',
			'#buddypress a#group-creation-next > span',
			'#buddypress div.item-list-tabs#subnav > ul > li.current',
			'#buddypress div.item-list-tabs#subnav > ul > li.selected',
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
					'#buddypress.youzify div.bp-avatar-status p.warning',
					'#buddypress.youzify div.bp-cover-image-status p.warning',
					'.youzify div.bp-avatar-status p.warning',
					'.youzify div.bp-cover-image-status p.warning',
					'body.youzify div.bp-avatar-status p.warning',
					'.drag-drop.drag-over #drag-drop-area',
				) ),
				'property' => 'border-color',
				'suffix'   => '!important',
			),
			array(
				'element'  => implode( ',', array(
					'.youzify .editfield .field-visibility-settings-notoggle',
					'.youzify .editfield .field-visibility-settings-toggle',
					'#buddypress.youzify div.bp-avatar-status p.warning',
					'#buddypress.youzify div.bp-cover-image-status p.warning',
					'.youzify div.bp-avatar-status p.warning',
					'.youzify div.bp-cover-image-status p.warning',
					'body.youzify div.bp-avatar-status p.warning',
					'.drag-drop.drag-over #drag-drop-area',
					'.youzify_cs_checkbox_field .youzify_field_indication:after',
					'.follows .youzify #youzify-members-list .youzify-user-actions a.youzify-send-message:hover',
					'.my-friends .youzify #youzify-members-list .youzify-user-actions a.youzify-send-message:hover',
					'.group_members #youzify-members-list li .youzify-user-actions a.youzify-send-message:hover',
					'.youzify .group-members-list li .youzify-user-actions a.youzify-send-message:hover',
				) ),
				'property' => 'border-color',
			),
			array(
				'element'  => implode( ',', array(
					'.youzify .thread-sender .thread-from .from',
					'.youzify-settings-sidebar .youzify-account-menus ul li a.youzify-active-menu',
					'.youzify-settings-sidebar .youzify-account-menus ul li a:hover',
					'.youzify-settings-sidebar .youzify-account-menus ul li a:active',
					'.youzify-settings-sidebar .youzify-account-menus ul li a:focus',
				) ),
				'property' => 'color',
			),
			array(
				'element'  => implode( ',', array(
					'.nice-select .option.focus',
					'.nice-select .option.selected',
					'.nice-select .option.selected.focus',
					'.nice-select .option:hover',
					'.nice-select .list:hover .option.selected:not(:hover)',
					'[class*="youzify-"][class*="-scheme"] .youzify-pagination .page-numbers.current',
					'.pagination .current',
				) ),
				'property' => 'background-color',
				'suffix'   => '!important',
			),
			array(
				'element'  => implode( ',', array(
					'#youzify-wall-nav li a span',
				) ),
				'property' => 'color',
			),
			array(
				'element'  => implode( ',', array(
					'#youzify-members-list > li .youzify-user-actions a:not(.friendship-button):hover',
					'#youzify-groups-list > li .youzify-user-actions a:not(.friendship-button):hover',
				) ),
				'property' => 'color',
				'suffix'   => '!important',
			),
			array(
				'element'  => implode( ',', array(
					'.nice-select .option.focus',
					'.nice-select .option.selected',
					'.nice-select .option.selected.focus',
					'.nice-select .option:hover',
					'.nice-select .list:hover .option.selected:not(:hover)',
					'[class*="youzify-"][class*="-scheme"] .youzify-pagination .page-numbers.current',
					'.pagination .current',

					'.youzify-file-post',
				) ),
				'property' => 'background-color',
				'suffix'   => '!important',
			),
			array(
				'element'  => implode( ',', array(
					'body .quote-with-img:before',
					'body .youzify-link-content',
					'body .youzify-no-thumbnail',
					'body a.youzify-settings-widget',
					'.youzify-quote-content',
				) ),
				'property' => 'background',
				'suffix'   => '!important',
			),
			array(
				'element'  => implode( ',', array(
					'.youzify-cphoto-content',
				) ),
				'property' => 'border-top-color',
			),
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
			'.youzify .option-content .youzify-upload-photo',
			'.youzify #avatar-crop-actions .button',
			'#youzify-skill-button',
			'#youzify-portfolio-button',
			'#youzify-service-button',
			'#youzify-slideshow-button',
			'body .logy-form a.logy-link-button',
		) );
	}

	/**
	 * Remove CSS selectors from the array of CSS selectors for the button.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $elements The array of CSS selectors for the table striped background color.
	 *
	 * @return array           The updated array of CSS selectors for the table striped background color.
	 */
	public function remove_elements( $elements ) {
		$keys = array(
			array_search( '#buddypress div.generic-button a', $elements, true ),
		);

		foreach ( $keys as $key ) {
			if ( false !== $key ) {
				unset( $elements[ $key ] );
			}
		}
		return $elements;
	}

	/**
	 * Remove CSS selectors from the array of CSS selectors for the button border radius.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $elements The array of CSS selectors for the table striped background color.
	 *
	 * @return array           The updated array of CSS selectors for the table striped background color.
	 */
	public function remove_border_radius_elements( $elements ) {
		$keys = array(
			array_search( '#buddypress div.generic-button a',                             $elements, true ),
			array_search( '#buddypress ul.item-list > li:not(.load-newest) div.action a', $elements, true ),
		);

		foreach ( $keys as $key ) {
			if ( false !== $key ) {
				unset( $elements[ $key ] );
			}
		}
		return $elements;
	}

	/**
	 * Remove CSS selectors from the array of CSS selectors for the xs button.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $elements The array of CSS selectors for the table striped background color.
	 *
	 * @return array           The updated array of CSS selectors for the table striped background color.
	 */
	public function remove_xs_elements( $elements ) {
		$keys = array(
			array_search( '#buddypress div.generic-button a',                             $elements, true ),
			array_search( '#buddypress ul.item-list > li:not(.load-newest) div.action a', $elements, true ),
		);

		foreach ( $keys as $key ) {
			if ( false !== $key ) {
				unset( $elements[ $key ] );
			}
		}
		return $elements;
	}

	/**
	 * Remove CSS selectors from the array of CSS selectors for the secondary button.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $elements The array of CSS selectors for the table striped background color.
	 *
	 * @return array           The updated array of CSS selectors for the table striped background color.
	 */
	public function remove_secondary_elements( $elements ) {
		$keys = array(
			array_search( '#buddypress div.generic-button a', $elements, true ),
		);

		foreach ( $keys as $key ) {
			if ( false !== $key ) {
				unset( $elements[ $key ] );
			}
		}
		return $elements;
	}
}

return new Grimlock_BuddyPress_Youzify_Button_Customizer();
