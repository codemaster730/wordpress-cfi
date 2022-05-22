<?php
/**
 * Grimlock_BuddyPress_Youzify_Table_Customizer Class
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
 * The table class for the Customizer.
 */
class Grimlock_BuddyPress_Youzify_Table_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'grimlock_table_customizer_striped_background_color_elements', array( $this, 'add_striped_background_color_elements' ), 10, 1 );
		add_filter( 'grimlock_table_customizer_striped_background_color_outputs',  array( $this, 'add_striped_background_color_outputs'  ), 10, 1 );
	}

	/**
	 * Add CSS selectors to the array of CSS selectors for the striped table row background color.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $elements The array of CSS selectors for the striped table row background color.
	 *
	 * @return array           The updated array of CSS selectors for the striped table row background color.
	 */
	public function add_striped_background_color_elements( $elements ) {
		return array_merge( $elements, array(
			'.youzify-user-actions a',
			'.youzify-directory-filter .item-list-tabs li a span',
			'#youzify-wall-nav li a span',
			'.youzify-my-account-widget .youzify-menu-links .youzify-link-logout',
			'.youzify-wg-title-icon-bg .youzify-widget-title i',
			'.youzify-profile-navmenu .youzify-nav-view-more-menu li a span.count',
			'.youzify-group-navmenu #membership-requests-groups-li a span',
			'.youzify-group-navmenu a#members span',
			'.youzify-group-navmenu a#media span',
			'.youzify-profile-navmenu .youzify-navbar-item a span',
			'.youzify-profile-navmenu .youzify-navbar-item a span.count',
			'.youzify-tab-comment .view-comment-button',
			'.youzify-sidebar .widget-content .widget-title:before',
			'.youzify-tab-title-box .youzify-tab-title-icon i',
			'.settings-inner-content .options-section-title h2 i',
			'div.youzify .editfield .field-visibility-settings-notoggle .current-visibility-level',
			'div.youzify .editfield .field-visibility-settings-toggle .current-visibility-level',
			'.mycred-history #buddypress.youzify .mycred-table tfoot',
			'.mycred-history #buddypress.youzify .mycred-table thead',
			'.youzify-item-tools',
			'#youzify-wall-form .youzify-wall-options .youzify-wall-opts-item label',
			'.youzify .activity-inner span.activity-read-more a',
			'.youzify-link-title .youzify-link-count',
			'#bbpress-forums .bbp-forums-list li',
			'#bbpress-forums .youzify-bbp-box .youzify-bbp-box-title i',
			'#youzify-wall-form .youzify-file-preview',
			'.youzify .activity-meta .youzify-post-liked-by .youzify-view-all',
			'.youzify div.item-list-tabs .youzify-bar-select',
			'.youzify-my-account-widget .youzify-menu-links .youzify-link-item .youzify-link-title .youzify-link-count',
			'.youzify #friend-list .action a',
			'.youzify #youzify-members-list .youzify-user-actions a',
			'#group-settings-form .youzify-group-submit-form #group-creation-previous',
			'.youzify-group-settings-tab .youzify-group-submit-form #group-creation-previous',
			'.youzify-group-infos-widget .youzify-group-widget-title i',
			'#invite-list .list-title i',
			'.messages-notices .thread-options .unread span',
			'.notifications .notification-actions .mark-unread span',
			'.sitewide-notices .thread-options .deactivate-notice',
			'.messages-notices .thread-options .delete span',
			'.notifications .notification-actions .delete span',
			'.youzify-icons-silver li i',
			'.youzify #youzify-members-list .youzify-user-actions .follow-button a',
			'.youzify .group-members-list .action a i',
			'.youzify-author .youzify-statistics-bg',
			'.youzify-widget .youzify-widget-head .youzify-edit-widget:hover',
			'#bbpress-forums div.bbp-forum-header',
			'.youzify-media-widget .youzify-media-no-items',
			'#bbpress-forums div.bbp-topic-header',
			'.youzify-follow-message-button',
			'.youzify-bbp-topic-head',
			'.youzify.youzify-account-page .nice-select',
			'.youzify-skillbar',
			'.youzify .youzify-media-filter .youzify-filter-item .youzify-filter-content:not(.youzify-current-filter)',
			'#buddypress.youzify div.generic-button a:not(.friendship-button)',
			'.buddypress .youzify div.generic-button a:not(.friendship-button)',
			'.youzify-user-actions a:not(.friendship-button)',
			'.youzify-form-tool i:hover',
			'#youzify-wall-form .youzify-wall-textarea',
			'.youzify-service-icon i',
		) );
	}

	/**
	 * Add selectors and properties to the CSS rule-set for the striped table row background color.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $outputs The array of CSS selectors and properties for the striped table row background color.
	 *
	 * @return array          The updated array of CSS selectors for the striped table row background color.
	 */
	public function add_striped_background_color_outputs( $outputs ) {
		return array_merge( $outputs, array(
			array(
				'element'  => implode( ',', array(
					'.youzify-user-actions .youzify-send-message',
					'.youzify-user-actions .youzify-second-btn',
					'.youzify-profile-navmenu .youzify-nav-view-more-menu li a',
					'div.youzify .editfield .field-visibility-settings-notoggle .current-visibility-level',
					'div.youzify .editfield .field-visibility-settings-toggle .current-visibility-level',
					'.mycred-history #buddypress.youzify .mycred-table tfoot',
					'.mycred-history #buddypress.youzify .mycred-table thead',
					'#buddypress.youzify .mycred-table tbody tr',
					'#whats-new-post-in-box .nice-select',
					'.youzify-wall-actions',
					'#bbpress-forums .bbp-forums-list li',
					'.follows .youzify #youzify-members-list .youzify-user-actions a.youzify-send-message',
					'.my-friends .youzify #youzify-members-list .youzify-user-actions a.youzify-send-message',
					'.group_members #youzify-members-list li .youzify-user-actions a.youzify-send-message',
					'.youzify .group-members-list li .youzify-user-actions a.youzify-send-message',
					'.youzify-social-buttons .message-button .youzify-send-message',
					'.youzify-sidebar .widget-content .widget-title',
					'.youzify .youzify-wall-embed',
					'.youzify-user-actions',
					'.youzify-widget .youzify-widget-head',
					'.youzify-recent-posts .youzify-post-item',
					'.youzify-tab-comment .youzify-comment-head',
					'.youzify-profile-list-widget .youzify-more-items a',
					'.youzify-my-account-widget .youzify-menu-links .youzify-links-section:first-of-type',
					'.youzify-my-account-widget .youzify-menu-links .youzify-link-logout',
					'.settings-inner-content .options-section-title',
					'.settings-sidebar .account-menus ul',
					'#youzify-export-data .youzify-section-content',
					'.youzify-section-content>.uk-option-item',
					'.youzify .youzify-settings-actions',
					'.youzify .editfield',
					'.youzify-review-item .youzify-item-content',
					'.youzify #activity-stream .ac-form',
					'#bbpress-forums .youzify-bbp-box .youzify-bbp-box-title',
					'.youzify-wall-attchments .youzify-form-attachments',
					'.youzify .activity-comments li',
					'.youzify .activity-comments li li',
					'.activity-item .youzify-share-buttons',
					'#youzify-groups-list .action',
					'#group-settings-form fieldset ul',
					'.youzify-group-settings-tab fieldset ul',
					'#group-settings-form fieldset',
					'.youzify-group-settings-tab fieldset',
					'.youzify-group-infos-widget .youzify-group-widget-title',
					'#invite-list .list-title',
					'.messages-notices thead',
					'.notifications thead',
					'#buddypress table.messages-notices tr',
					'#buddypress table.notifications tr',
					'.item-list-tabs #search-message-form',
					'div.bbp-forum-header',
					'div.bbp-topic-header',
					'.youzify-wall-options',
					'.youzify-account-settings-menu .youzify-account-menu',
				) ),
				'property' => 'border-color',
			),
			array(
				'element'  => implode( ',', array(
					'#buddypress.youzify div.bp-avatar-status p.warning',
					'#buddypress.youzify div.bp-cover-image-status p.warning',
					'.youzify div.bp-avatar-status p.warning',
					'.youzify div.bp-cover-image-status p.warning',
					'body.youzify div.bp-avatar-status p.warning',
					'.youzify .youzify-wg-skills-options .youzify-wg-item',
					'.youzify-cphoto-options .youzify-wg-container',
					'.youzify-wg-services-options .youzify-wg-item',
					'.youzify-no-content',
				) ),
				'property' => 'background-color',
				'suffix'   => '!important',
			),
			array(
				'element'  => implode( ',', array(
					'.youzify #drag-drop-area',
					'#youzify .youzify-user-actions .youzify-send-message',
					'#youzify .youzify-user-actions .yzmd-second-btn',
				) ),
				'property' => 'border-color',
				'suffix'   => '!important',
			),
			array(
				'element'  => implode( ',', array(
					'#youzify-groups-list .action a.youzify-manage-group',
				) ),
				'property' => 'background',
				'suffix'   => '!important',
			),
		) );
	}
}

return new Grimlock_BuddyPress_Youzify_Table_Customizer();
