<?php
/**
 * Grimlock_BuddyPress_Youzer_Archive_Customizer Class
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
 * The post archive page class for the Customizer.
 */
class Grimlock_BuddyPress_Youzer_Archive_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'grimlock_archive_customizer_elements',                       array( $this, 'add_elements'                       ), 10, 1 );
		add_filter( 'grimlock_archive_customizer_post_background_color_elements', array( $this, 'add_post_background_color_elements' ), 10, 1 );
		add_filter( 'grimlock_archive_customizer_post_color_elements',            array( $this, 'add_post_color_elements'            ), 10, 1 );
		add_filter( 'grimlock_archive_customizer_post_color_outputs',             array( $this, 'add_post_color_outputs'             ), 10, 1 );
		add_filter( 'grimlock_archive_customizer_post_background_color_outputs',  array( $this, 'add_post_background_color_outputs'  ), 10, 1 );
		add_filter( 'grimlock_archive_customizer_post_title_color_elements',      array( $this, 'add_post_title_color_elements'      ), 10, 1 );
		add_filter( 'grimlock_archive_customizer_post_link_color_elements',       array( $this, 'add_post_link_color_elements'       ), 10, 1 );
		add_filter( 'grimlock_archive_customizer_post_link_hover_color_elements', array( $this, 'add_post_link_hover_color_elements' ), 10, 1 );
		add_filter( 'grimlock_archive_customizer_post_border_radius_elements',    array( $this, 'add_post_border_radius_elements'    ), 10, 1 );
		add_filter( 'grimlock_archive_customizer_post_border_radius_outputs',     array( $this, 'add_post_border_radius_outputs'     ), 10, 1 );
	}

	/**
	 * Add CSS selectors from the array of CSS selectors for the archive post.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $elements The array of CSS selectors for the archive post.
	 *
	 * @return array           The updated array of CSS selectors for the archive post.
	 */
	public function add_elements( $elements ) {
		return array_merge( $elements, array(
			'.yz-white-bg',
			'#yz-profile-navmenu',
			'.yz-hdr-v7',
			'.yz-hdr-v2',
			'.yz-directory-filter',
			'#yz-members-list li .yzm-user-data',
			'.yz-tab-post',
			'#buddypress .yz-main-column table.profile-fields',
			'#yz-wall-form',
			'#yz-wall-nav',
			'#buddypress.youzer .activity-list li.load-more',
			'#buddypress.youzer .activity-list>li',
			'.youzer .activity-list>li',
			'.youzer-sidebar .widget-content',
			'.youzer-sidebar .yz-wp-author-widget',
			'.yz-my-account-widget',
			'.yz-wall-content',
			'#yz-groups-list li .yz-group-data',
			'.yz-ad .yz-widget-head',
			'.yz-pagination .yz-nav-links .page-numbers',
			'.yz-pagination .yz-pagination-pages',
			'.yz-directory .pagination .page-numbers',
			'.yz-tab-comment',
			'.settings-main-content .settings-inner-content',
			'.yz-account-header',
			'.settings-sidebar .account-infos',
			'.settings-sidebar .account-menus',
			'.mycred-history #buddypress.youzer .mycred-table',
			'.yz-review-item',
			'.yz-wall-new-post',
			'#bbpress-forums .yz-bbp-box',
			'#bbpress-forums div.bbp-search-form',
			'.yz-wall-link-content',
			'.youzer #subnav',
			'#buddypress div.item-list-tabs#subnav',
			'#buddypress.youzer div.item-list-tabs#subnav',
			'#group-create-body',
			'#group-settings-form',
			'#group-create-tabs li',
			'.yz-group-infos-widget',
			'.youzer .group-request-list li',
			'#invite-list',
			'.yz-group-manage-members-search',
			'.youzer .group-members-list li',
			'#buddypress table.messages-notices',
			'#buddypress table.notifications',
			'.yzb-author',
			'#send-reply',
			'#buddypress div#message-thread .message-box',
			'#buddypress div#message-thread .message-box:hover',
			'#buddypress div#message-thread .message-box.alt',
			'#message-recipients',
			'#send_message_form',
			'.logy-form',
			'#yz-not-friend-message',
			'.my-groups .youzer #yz-groups-list li .yz-group-data',
			'.youzer .yz-wg-skills-options .yz-wg-item',
			'.yz-cphoto-options .yz-wg-container',
			'.yz-wg-services-options .yz-wg-item',
			'.yz-no-content',
		) );
	}

	/**
	 * Add CSS selectors from the array of CSS selectors for the archive post background color.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $elements The array of CSS selectors for the archive post background color.
	 *
	 * @return array           The updated array of CSS selectors for the archive post background color.
	 */
	public function add_post_background_color_elements( $elements ) {
		return array_merge( $elements, array(
			'.yz-card-show-avatar-border .item-avatar',
			'.yz-card-show-avatar-border .yz-item-avatar',
			'.yz-photo-border',
			'#yz-profile-navmenu .yz-settings-menu',
			'.yz-profile-navmenu .yz-nav-view-more-menu',
			'.follows .youzer #yz-members-list li .yzm-user-data',
			'.my-friends .youzer #yz-members-list li .yzm-user-data',
			'.group_members #yz-members-list li .yzm-user-data',
			'.youzer .group-members-list li .yzm-user-data',
			'#bbpress-forums div.even',
			'#bbpress-forums ul.even',
			'#bbpress-forums li',
			'.youzer .yz-wg-opts',
		) );
	}

	/**
	 * Add CSS selectors from the array of CSS selectors for the archive post color.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $elements The array of CSS selectors for the archive post color.
	 *
	 * @return array           The updated array of CSS selectors for the archive post color.
	 */
	public function add_post_color_elements( $elements ) {
		return array_merge( $elements, array(
			'#yz-profile-navmenu .yz-settings-menu',
			'#yz-profile-navmenu .yz-settings-menu a',
			'.yz-profile-navmenu .yz-nav-view-more-menu',
			'.yz-profile-navmenu .yz-nav-view-more-menu a',
			'.yz-star-rating i.star-empty',
			'.settings-sidebar .account-menus ul li a',
			'.youzer #friend-list .action a',
			'.youzer #yz-members-list .yzm-user-actions a',
			'.follows .youzer #yz-members-list .yzm-user-actions a.yz-send-message',
			'.my-friends .youzer #yz-members-list .yzm-user-actions a.yz-send-message',
			'.group_members #yz-members-list li .yzm-user-actions a.yz-send-message',
			'.youzer .group-members-list li .yzm-user-actions a.yz-send-message',
			'.message-action-star span.icon:before',
			'#group-create-tabs li, #group-create-tabs li a',
			'#group-settings-form label',
			'.yz-group-settings-tab label',
			'.youzer div.item-list-tabs li a',
			'.yz-group-infos-widget .yz-group-widget-title',
			'.youzer .group-request-list .item .item-title a',
			'.group-members-list .section-header',
			'.main-column .section-header',
			'#invite-list .list-title',
			'.youzer .group-members-list .item .item-title a',
			'.youzer .thread-sender .thread-from .from a',
			'.youzer #yz-members-list .yzm-user-actions .follow-button a',
			'#bbpress-forums div.even',
			'#bbpress-forums ul.even',
			'#bbpress-forums li',
		) );
	}

	/**
	 * Add selectors and properties to the CSS rule-set for the archive post background color.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $outputs The array of CSS selectors and properties for the archive post background color.
	 *
	 * @return array          The updated array of CSS selectors for the archive post background color.
	 */
	public function add_post_background_color_outputs( $outputs ) {
		return array_merge( $outputs, array(
			array(
				'element'  => implode( ',', array(
					'.youzer .yz-wall-embed .yz-embed-avatar',
					'.youzer .activity-meta .yz-post-liked-by a',
				) ),
				'property' => 'border-color',
			),
			array(
				'element'  => implode( ',', array(
					'.youzer .yz-wall-embed .yz-embed-avatar',
					'.youzer .activity-meta .yz-post-liked-by a',
				) ),
				'property' => 'border-color',
			),
		) );
	}

	/**
	 * Add selectors and properties to the CSS rule-set for the archive post color.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $outputs The array of CSS selectors and properties for the archive post color.
	 *
	 * @return array          The updated array of CSS selectors for the archive post color.
	 */
	public function add_post_color_outputs( $outputs ) {
		return array_merge( $outputs, array(
			array(
				'element'  => implode( ',', array(
					'input.cmn-toggle-round-flat+label:after',
					'input.cmn-toggle-round-flat+label',
				) ),
				'property' => 'background-color',
			),
			array(
				'element'  => implode( ',', array(
					'.yz_cs_checkbox_field .yz_field_indication',
				) ),
				'property' => 'border-color',
			),
			array(
				'element'  => implode( ',', array(
					'#yz-directory-search-box',
					'.yzb-author .yz-use-borders li',
				) ),
				'property' => 'border-color',
				'suffix'   => '30',
			),
			array(
				'element'  => implode( ',', array(
					'.yz-responsive-menu span',
					'.yz-responsive-menu span::after',
					'.yz-responsive-menu span::before',
				) ),
				'property' => 'background',
			),
		) );
	}

	/**
	 * Add CSS selectors from the array of CSS selectors for the archive post title color.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $elements The array of CSS selectors for the archive post title color.
	 *
	 * @return array           The updated array of CSS selectors for the archive post title color.
	 */
	public function add_post_title_color_elements( $elements ) {
		return array_merge( $elements, array(
			'.yz-widget .yz-widget-title',
			'#yz-profile-navmenu',
			'#yz-profile-navmenu a',
			'.yz-tab-comment .yz-comment-fullname',
			'.yz-aboutme-name',
			'.yz-recent-posts .yz-post-head .yz-post-title a',
			'.youzer-sidebar .widget-content .widget-title',
			'.yz-forums-forum-item .yz-forums-forum-title',
			'.yz-forums-topic-item .yz-forums-topic-title',
			'.settings-inner-content .options-section-title h2',
			'.settings-sidebar .account-menus h2',
			'.yz-account-head h2',
			'.youzer .editfield fieldset legend',
			'.yz-uploader-change-item h2',
			'.uk-option-item .option-infos label',
			'.yz-review-item .yz-head-meta .yz-item-name a',
			'.youzer .activity-header .activity-head p a:first-of-type',
			'.yz-wall-new-post .yz-post-title a',
			'.yz-my-account-widget .yz-widget-header .yz-widget-head .yz-user-name',
			'.yz-items-list-widget .yz-list-item a.yz-item-name',
			'.yz-notifications-widget .yz-notif-item .yz-notif-content .yz-notif-desc',
			'.youzer .yz-wall-embed .yz-embed-name',
			'.follows .youzer #yz-members-list .item .item-title a',
			'.my-friends .youzer #yz-members-list .item .item-title a',
			'.group_members #yz-members-list li .item .item-title a',
			'.youzer .group-members-list li .item .item-title a',
			'.yz-tab-post .yz-post-title a',
			'.bbp-forum-info .yz-forums-forum-head .yz-forums-forum-title',
			'#bbpress-forums .yz-bbp-box .yz-bbp-box-title',
			'.youzer .activity-comments .acomment-meta a',
			'.yz-directory-filter #directory-show-filter a',
			'.yz-directory-filter #directory-show-search a',
			'.yz-directory-filter .item-list-tabs li a',
			'#yz-wall-nav li a',
			'.yz-wall-link-data .yz-wall-link-title',
			'#yz-groups-list .item .item-title a',
			'.yz-hdr-v2 .yz-snumber',
			'#message-recipients .highlight h2',
			'.notifications tbody td.notification-description a',
			'#message-thread .message-metadata a',
			'.sitewide-notices .yz-notice-msg-title',
			'.my-groups .youzer #yz-groups-list .item .item-title a',
			'#yz-profile-navmenu',
			'.yz-account-settings-menu .yz-menu-head .yz-menu-title',
			'#yz-export-data .youzer-section-content h2',
			'.uk-option-item .option-content label',
			'.yz-widget .yz-widget-title',
			'#yz-members-list .yz-fullname',
		) );
	}

	/**
	 * Add CSS selectors from the array of CSS selectors for the archive post link color.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $elements The array of CSS selectors for the archive post link color.
	 *
	 * @return array           The updated array of CSS selectors for the archive post link color.
	 */
	public function add_post_link_color_elements( $elements ) {
		return array_merge( $elements, array(
			'.yz-widget .yz-widget-main-content .yz-more-items a',
			'.settings-sidebar .account-menus .yz-active-menu',
			'.settings-sidebar .account-menus ul li a:hover',
			'.yz-my-account-widget .yz-menu-links .yz-link-item',
			'.youzer .activity-inner span.activity-read-more a',
		) );
	}

	/**
	 * Add CSS selectors from the array of CSS selectors for the archive post link color on hover.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $elements The array of CSS selectors for the archive post link color on hover.
	 *
	 * @return array           The updated array of CSS selectors for the archive post link color on hover.
	 */
	public function add_post_link_hover_color_elements( $elements ) {
		return array_merge( $elements, array(
			'.yz-widget .yz-widget-main-content .yz-more-items a:hover',
			'.yz-widget .yz-widget-main-content .yz-more-items a:active',
			'.yz-widget ..youzer .activity-inneryz-widget-main-content .yz-more-items a:focus',
			'.yz-widget .yz-widget-main-content a:hover',
			'.yz-my-account-widget .yz-menu-links .yz-link-item:hover',
			'.youzer .activity-inner span.activity-read-more a:hover',
			'.youzer #yz-members-list .yzm-user-actions .follow-button a:hover',
		) );
	}

	/**
	 * Add CSS selectors from the array of CSS selectors for the archive post border radius.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $elements The array of CSS selectors for the archive post border radius.
	 *
	 * @return array           The updated array of CSS selectors for the archive post border radius.
	 */
	public function add_post_border_radius_elements( $elements ) {
		return array_merge( $elements, array(
			'.yz-media .yz-media-group-head',
			'.yz-wg-border-radius .yz-slideshow-img',
			'.yz-wg-border-radius .yz-tab-comment', '.yz-wg-border-radius .yz-tab-post',
			'.yz-wg-border-radius .yz-widget',
			'.yz-wg-border-radius .yz-widget .yz-widget-main-content',
			'.yz-wg-border-radius .yz-widget.without-title .yz-link-cover',
			'.yz-wg-border-radius .yz-widget.without-title .yz-quote-content:before',
			'.yz-wg-border-radius .yz-widget.without-title .yz-quote-cover',
			'#message-recipients',
			'#message-recipients .highlight-icon i',
			'#message-thread .message-box',
			'#send-reply',
			'#send-reply #send_reply_button',
			'#send_message_form',
			'#send_message_form .submit #send',
			'.follows .youzer #yz-members-list li .yzm-user-data',
			'.follows .yz-page-btns-border-radius #yz-members-list .yzm-user-actions a',
			'.item-list-tabs #search-message-form #messages_search',
			'.item-list-tabs #search-message-form #messages_search_submit',
			'.messages-notices .thread-options a span',
			'.messages-options-nav #messages-bulk-manage',
			'.messages-options-nav select',
			'.my-friends .youzer #friend-list li',
			'.my-friends .youzer #yz-members-list li .yzm-user-data',
			'.my-friends .yz-page-btns-border-radius #friend-list .action a',
			'.my-friends .yz-page-btns-border-radius #yz-members-list .yzm-user-actions a',
			'.my-groups .youzer #yz-groups-list li .yz-group-data',
			'.my-groups .yz-page-btns-border-radius #yz-groups-list .action a',
			'.notifications .notification-actions a span',
			'.notifications-options-nav #notification-bulk-manage',
			'.notifications-options-nav select',
			'.youzer .pagination .page-numbers',
			'.yzmsg-form-item input[type=text]',
			'.yzmsg-form-item textarea',
			'.group_members #yz-members-list li .yzm-user-data',
		) );
	}

	/**
	 * Add selectors and properties to the CSS rule-set for the button border radius.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $outputs The array of CSS selectors and properties for the button border radius.
	 *
	 * @return array          The updated array of CSS selectors for the button border radius.
	 */
	public function add_post_border_radius_outputs( $outputs ) {
		return array_merge( $outputs, array(
			array(
				'element'       => implode( ',', array(
					'#yz-members-list > li .yzm-user-cover',
					'#yz-groups-list > li .yzm-user-cover',
					'#yz-groups-list li .yz-group-cover',
				) ),
				'property'      => 'border-radius',
				'value_pattern' => '$rem $rem 0 0',
			),
		) );
	}
}

return new Grimlock_BuddyPress_Youzer_Archive_Customizer();
