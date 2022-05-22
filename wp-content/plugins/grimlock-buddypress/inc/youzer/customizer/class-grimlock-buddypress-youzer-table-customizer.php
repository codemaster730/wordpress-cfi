<?php
/**
 * Grimlock_BuddyPress_Youzer_Table_Customizer Class
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
class Grimlock_BuddyPress_Youzer_Table_Customizer {
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
			'.yzm-user-actions a',
			'.yz-directory-filter .item-list-tabs li a span',
			'#yz-wall-nav li a span',
			'.yz-my-account-widget .yz-menu-links .yz-link-logout',
			'.yz-wg-title-icon-bg .yz-widget-title i',
			'.yz-profile-navmenu .yz-nav-view-more-menu li a span.count',
			'.yz-group-navmenu #membership-requests-groups-li a span',
			'.yz-group-navmenu a#members span',
			'.yz-group-navmenu a#media span',
			'.yz-profile-navmenu .yz-navbar-item a span',
			'.yz-profile-navmenu .yz-navbar-item a span.count',
			'.yz-tab-comment .view-comment-button',
			'.youzer-sidebar .widget-content .widget-title:before',
			'.yz-tab-title-box .yz-tab-title-icon i',
			'.settings-inner-content .options-section-title h2 i',
			'.youzer .editfield .field-visibility-settings-notoggle',
			'.youzer .editfield .field-visibility-settings-toggle',
			'.mycred-history #buddypress.youzer .mycred-table tfoot',
			'.mycred-history #buddypress.youzer .mycred-table thead',
			'.yz-item-tools',
			'.yz-wall-options .yz-wall-opts-item label',
			'.youzer .activity-inner span.activity-read-more a',
			'.yz-link-title .yz-link-count',
			'#bbpress-forums .bbp-forums-list li',
			'#bbpress-forums .yz-bbp-box .yz-bbp-box-title i',
			'#yz-wall-form .yz-file-preview',
			'.youzer .activity-meta .yz-post-liked-by .yz-view-all',
			'.youzer div.item-list-tabs .yz-bar-select',
			'.yz-my-account-widget .yz-menu-links .yz-link-item .yz-link-title .yz-link-count',
			'.youzer #friend-list .action a',
			'.youzer #yz-members-list .yzm-user-actions a',
			'#group-settings-form .yz-group-submit-form #group-creation-previous',
			'.yz-group-settings-tab .yz-group-submit-form #group-creation-previous',
			'.yz-group-infos-widget .yz-group-widget-title i',
			'#invite-list .list-title i',
			'.messages-notices .thread-options .unread span',
			'.notifications .notification-actions .mark-unread span',
			'.sitewide-notices .thread-options .deactivate-notice',
			'.messages-notices .thread-options .delete span',
			'.notifications .notification-actions .delete span',
			'.yz-icons-silver li i',
			'.youzer #yz-members-list .yzm-user-actions .follow-button a',
			'.youzer .group-members-list .action a i',
			'.yzb-author .yz-statistics-bg',
			'.yz-widget .yz-widget-head .yz-edit-widget:hover',
			'#bbpress-forums div.bbp-forum-header',
			'#bbpress-forums div.bbp-topic-header',
			'.yz-follow-message-button',
			'.yz-bbp-topic-head',
			'.youzer.yz-account-page .nice-select',
			'.yz-skillbar',
			'.youzer .yz-media-filter .yz-filter-item .yz-filter-content:not(.yz-current-filter)',
			'#buddypress.youzer div.generic-button a:not(.friendship-button)',
			'.buddypress .youzer div.generic-button a:not(.friendship-button)',
			'.yzm-user-actions a:not(.friendship-button)',
			'.yz-form-tool i:hover',
			'#yz-wall-form .yz-wall-textarea',
			'.yz-service-icon i',
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
					'.yzm-user-actions .yz-send-message',
					'.yzm-user-actions .yzmd-second-btn',
					'.yz-profile-navmenu .yz-nav-view-more-menu li a',
					'.youzer .editfield .field-visibility-settings-notoggle .current-visibility-level',
					'.youzer .editfield .field-visibility-settings-toggle .current-visibility-level',
					'.mycred-history #buddypress.youzer .mycred-table tfoot',
					'.mycred-history #buddypress.youzer .mycred-table thead',
					'#buddypress.youzer .mycred-table tbody tr',
					'#whats-new-post-in-box .nice-select',
					'.yz-wall-actions',
					'#bbpress-forums .bbp-forums-list li',
					'.follows .youzer #yz-members-list .yzm-user-actions a.yz-send-message',
					'.my-friends .youzer #yz-members-list .yzm-user-actions a.yz-send-message',
					'.group_members #yz-members-list li .yzm-user-actions a.yz-send-message',
					'.youzer .group-members-list li .yzm-user-actions a.yz-send-message',
					'.yz-social-buttons .message-button .yz-send-message',
					'.youzer-sidebar .widget-content .widget-title',
					'.youzer .yz-wall-embed',
					'.yzm-user-actions',
					'.yz-widget .yz-widget-head',
					'.yz-recent-posts .yz-post-item',
					'.yz-tab-comment .yz-comment-head',
					'.yz-profile-list-widget .yz-more-items a',
					'.yz-my-account-widget .yz-menu-links .yz-links-section:first-of-type',
					'.yz-my-account-widget .yz-menu-links .yz-link-logout',
					'.settings-inner-content .options-section-title',
					'.settings-sidebar .account-menus ul',
					'#yz-export-data .youzer-section-content',
					'.youzer-section-content>.uk-option-item',
					'.youzer .youzer-settings-actions',
					'.youzer .editfield',
					'.yz-review-item .yz-item-content',
					'.youzer #activity-stream .ac-form',
					'#bbpress-forums .yz-bbp-box .yz-bbp-box-title',
					'.yz-wall-attchments .yz-form-attachments',
					'.youzer .activity-comments li',
					'.youzer .activity-comments li li',
					'.activity-item .yz-share-buttons',
					'#yz-groups-list .action',
					'#group-settings-form fieldset ul',
					'.yz-group-settings-tab fieldset ul',
					'#group-settings-form fieldset',
					'.yz-group-settings-tab fieldset',
					'.yz-group-infos-widget .yz-group-widget-title',
					'#invite-list .list-title',
					'.messages-notices thead',
					'.notifications thead',
					'#buddypress table.messages-notices tr',
					'#buddypress table.notifications tr',
					'.item-list-tabs #search-message-form',
					'div.bbp-forum-header',
					'div.bbp-topic-header',
					'.yz-wall-options',
					'.yz-account-settings-menu .yz-account-menu',
				) ),
				'property' => 'border-color',
			),
			array(
				'element'  => implode( ',', array(
					'#buddypress.youzer div.bp-avatar-status p.warning',
					'#buddypress.youzer div.bp-cover-image-status p.warning',
					'.youzer div.bp-avatar-status p.warning',
					'.youzer div.bp-cover-image-status p.warning',
					'body.youzer div.bp-avatar-status p.warning',
					'.youzer .yz-wg-skills-options .yz-wg-item',
					'.yz-cphoto-options .yz-wg-container',
					'.yz-wg-services-options .yz-wg-item',
					'.yz-no-content',
				) ),
				'property' => 'background-color',
				'suffix'   => '!important',
			),
			array(
				'element'  => implode( ',', array(
					'.youzer #drag-drop-area',
				) ),
				'property' => 'border-color',
				'suffix'   => '!important',
			),
			array(
				'element'  => implode( ',', array(
					'#yz-groups-list .action a.yz-manage-group',
				) ),
				'property' => 'background',
				'suffix'   => '!important',
			),
		) );
	}
}

return new Grimlock_BuddyPress_Youzer_Table_Customizer();
