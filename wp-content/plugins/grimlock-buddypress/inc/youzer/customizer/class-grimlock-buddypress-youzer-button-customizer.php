<?php
/**
 * Grimlock_BuddyPress_Youzer_Button_Customizer Class
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
class Grimlock_BuddyPress_Youzer_Button_Customizer {
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
			'#yz-members-list > li .hmk-trigger-match .generic-button',
			'.yz-pagination .yz-nav-links .page-numbers:hover:not(.current)',
			'.pagination .current',
			'.youzer .pagination .page-numbers:not(.current):hover',
			'.yz-settings-area .yz-quick-buttons .yz-button-count',
			'.youzer .drag-drop-buttons #bp-browse-button',
			'.youzer #activity-stream .ac-reply-content input[type=submit]',
			'.youzer #bp-data-export button',
			'#yz-data-export a',
			'.yz-account-page .yz-export-item .ukai-button-item a',
			'.yz-wg-portfolio-options .yz-upload-photo',
			'.yz-link-icon i',
			'.logy-form .logy-form-actions button',
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

					'[class*="yz-"][class*="-scheme"] .yz-pagination .page-numbers.current',
					'.pagination .current',
					'.yzw-file-post',
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
			'.yz-aboutme-head:after',
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
					'#buddypress.youzer div.bp-avatar-status p.warning',
					'#buddypress.youzer div.bp-cover-image-status p.warning',
					'.youzer div.bp-avatar-status p.warning',
					'.youzer div.bp-cover-image-status p.warning',
					'body.youzer div.bp-avatar-status p.warning',
					'.drag-drop.drag-over #drag-drop-area',
				) ),
				'property' => 'border-color',
				'suffix'   => '!important',
			),
			array(
				'element'  => implode( ',', array(
					'.youzer .editfield .field-visibility-settings-notoggle',
					'.youzer .editfield .field-visibility-settings-toggle',
					'#buddypress.youzer div.bp-avatar-status p.warning',
					'#buddypress.youzer div.bp-cover-image-status p.warning',
					'.youzer div.bp-avatar-status p.warning',
					'.youzer div.bp-cover-image-status p.warning',
					'body.youzer div.bp-avatar-status p.warning',
					'.drag-drop.drag-over #drag-drop-area',
					'.yz_cs_checkbox_field .yz_field_indication:after',
					'.follows .youzer #yz-members-list .yzm-user-actions a.yz-send-message:hover',
					'.my-friends .youzer #yz-members-list .yzm-user-actions a.yz-send-message:hover',
					'.group_members #yz-members-list li .yzm-user-actions a.yz-send-message:hover',
					'.youzer .group-members-list li .yzm-user-actions a.yz-send-message:hover',
				) ),
				'property' => 'border-color',
			),
			array(
				'element'  => implode( ',', array(
					'.yz-directory-filter .item-list-tabs li a span',
					'.youzer .thread-sender .thread-from .from',
					'.yz-settings-sidebar .yz-account-menus ul li a.yz-active-menu',
					'.yz-settings-sidebar .yz-account-menus ul li a:hover',
					'.yz-settings-sidebar .yz-account-menus ul li a:active',
					'.yz-settings-sidebar .yz-account-menus ul li a:focus',
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
					'[class*="yz-"][class*="-scheme"] .yz-pagination .page-numbers.current',
					'.pagination .current',
				) ),
				'property' => 'background-color',
				'suffix'   => '!important',
			),
			array(
				'element'  => implode( ',', array(
					'.yz-directory-filter .item-list-tabs li a span',
					'#yz-wall-nav li a span',
				) ),
				'property' => 'color',
			),
			array(
				'element'  => implode( ',', array(
					'#yz-members-list > li .yzm-user-actions a:not(.friendship-button):hover',
					'#yz-groups-list > li .yzm-user-actions a:not(.friendship-button):hover',
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
					'[class*="yz-"][class*="-scheme"] .yz-pagination .page-numbers.current',
					'.pagination .current',

					'.yzw-file-post',
				) ),
				'property' => 'background-color',
				'suffix'   => '!important',
			),
			array(
				'element'  => implode( ',', array(
					'body .quote-with-img:before',
					'body .yz-link-content',
					'body .yz-no-thumbnail',
					'body a.yz-settings-widget',
					'.yzw-quote-content',
				) ),
				'property' => 'background',
				'suffix'   => '!important',
			),
			array(
				'element'  => implode( ',', array(
					'.yz-cphoto-content',
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
			'.youzer .option-content .yz-upload-photo',
			'.youzer #avatar-crop-actions .button',
			'#yz-skill-button',
			'#yz-portfolio-button',
			'#yz-service-button',
			'#yz-slideshow-button',
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

return new Grimlock_BuddyPress_Youzer_Button_Customizer();
