<?php
/**
 * Grimlock_BuddyPress_Youzify_Archive_Customizer Class
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
class Grimlock_BuddyPress_Youzify_Archive_Customizer {
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
			'.youzify-white-bg',
			'#youzify-profile-navmenu',
			'.youzify-hdr-v7',
			'.youzify-hdr-v2',
			'.youzify-directory-filter',
			'#youzify-members-list li .youzify-user-data',
			'.youzify-tab-post',
			'#buddypress .youzify-main-column table.profile-fields',
			'.youzify #youzify-wall-form',
			'.youzify #youzify-wall-nav',
			'#buddypress.youzify .activity-list li.load-more',
			'#buddypress.youzify .activity-list>li',
			'.youzify .activity-list>li',
			'.youzify-sidebar .widget-content',
			'.youzify-sidebar .youzify-wp-author-widget',
			'.youzify-my-account-widget',
			'.youzify-wall-content',
			'#youzify-groups-list li .youzify-group-data',
			'.youzify-ad .youzify-widget-head',
			'.youzify-pagination .youzify-nav-links .page-numbers',
			'.youzify-pagination .youzify-pagination-pages',
			'.youzify-directory .pagination .page-numbers',
			'.youzify-tab-comment',
			'.settings-main-content .settings-inner-content',
			'.youzify-account-header',
			'.settings-sidebar .account-infos',
			'.settings-sidebar .account-menus',
			'.mycred-history #buddypress.youzify .mycred-table',
			'.youzify-review-item',
			'.youzify-wall-new-post',
			'#bbpress-forums .youzify-bbp-box',
			'#bbpress-forums div.bbp-search-form',
			'.youzify-wall-link-content',
			'.youzify #subnav',
			'#buddypress div.item-list-tabs#subnav',
			'#buddypress.youzify div.item-list-tabs#subnav',
			'#group-create-body',
			'#group-settings-form',
			'#group-create-tabs li',
			'.youzify-group-infos-widget',
			'.youzify .group-request-list li',
			'#invite-list',
			'.youzify-group-manage-members-search',
			'.youzify .group-members-list li',
			'#buddypress table.messages-notices',
			'#buddypress table.notifications',
			'.youzify-author',
			'#send-reply',
			'#buddypress div#message-thread .message-box',
			'#buddypress div#message-thread .message-box:hover',
			'#buddypress div#message-thread .message-box.alt',
			'#message-recipients',
			'#send_message_form',
			'.logy-form',
			'#youzify-not-friend-message',
			'.my-groups .youzify #youzify-groups-list li .youzify-group-data',
			'.youzify .youzify-wg-skills-options .youzify-wg-item',
			'.youzify-cphoto-options .youzify-wg-container',
			'.youzify-wg-services-options .youzify-wg-item',
			'.youzify-no-content',
			'#youzify #send_message_form',
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
			'.youzify-card-show-avatar-border .item-avatar',
			'.youzify-card-show-avatar-border .youzify-item-avatar',
			'.youzify-photo-border',
			'#youzify-profile-navmenu .youzify-settings-menu',
			'.youzify-profile-navmenu .youzify-nav-view-more-menu',
			'.follows .youzify #youzify-members-list li .youzify-user-data',
			'.my-friends .youzify #youzify-members-list li .youzify-user-data',
			'.group_members #youzify-members-list li .youzify-user-data',
			'.youzify .group-members-list li .youzify-user-data',
			'#bbpress-forums div.even',
			'#bbpress-forums ul.even',
			'#bbpress-forums li',
			'.youzify .youzify-wg-opts',
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
			'#youzify-profile-navmenu .youzify-settings-menu',
			'#youzify-profile-navmenu .youzify-settings-menu a',
			'.youzify-profile-navmenu .youzify-nav-view-more-menu',
			'.youzify-profile-navmenu .youzify-nav-view-more-menu a',
			'.youzify-star-rating i.star-empty',
			'.settings-sidebar .account-menus ul li a',
			'.youzify #friend-list .action a',
			'.youzify #youzify-members-list .youzify-user-actions a',
			'.follows .youzify #youzify-members-list .youzify-user-actions a.youzify-send-message',
			'.my-friends .youzify #youzify-members-list .youzify-user-actions a.youzify-send-message',
			'.group_members #youzify-members-list li .youzify-user-actions a.youzify-send-message',
			'.youzify .group-members-list li .youzify-user-actions a.youzify-send-message',
			'.message-action-star span.icon:before',
			'#group-create-tabs li, #group-create-tabs li a',
			'#group-settings-form label',
			'.youzify-group-settings-tab label',
			'.youzify div.item-list-tabs li a',
			'.youzify-group-infos-widget .youzify-group-widget-title',
			'.youzify .group-request-list .item .item-title a',
			'.group-members-list .section-header',
			'.main-column .section-header',
			'#invite-list .list-title',
			'.youzify .group-members-list .item .item-title a',
			'.youzify .thread-sender .thread-from .from a',
			'.youzify #youzify-members-list .youzify-user-actions .follow-button a',
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
					'.youzify .youzify-wall-embed .youzify-embed-avatar',
					'.youzify .activity-meta .youzify-post-liked-by a',
				) ),
				'property' => 'border-color',
			),
			array(
				'element'  => implode( ',', array(
					'.youzify .youzify-wall-embed .youzify-embed-avatar',
					'.youzify .activity-meta .youzify-post-liked-by a',
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
					'.youzify_cs_checkbox_field .youzify_field_indication',
				) ),
				'property' => 'border-color',
			),
			array(
				'element'  => implode( ',', array(
					'#youzify-directory-search-box',
					'.youzify-author .youzify-use-borders li',
				) ),
				'property' => 'border-color',
				'suffix'   => '30',
			),
			array(
				'element'  => implode( ',', array(
					'.youzify-responsive-menu span',
					'.youzify-responsive-menu span::after',
					'.youzify-responsive-menu span::before',
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
			'.youzify-widget .youzify-widget-title',
			'#youzify-profile-navmenu',
			'#youzify-profile-navmenu a',
			'.youzify-tab-comment .youzify-comment-fullname',
			'.youzify-aboutme-name',
			'.youzify-recent-posts .youzify-post-head .youzify-post-title a',
			'.youzify-sidebar .widget-content .widget-title',
			'.youzify-forums-forum-item .youzify-forums-forum-title',
			'.youzify-forums-topic-item .youzify-forums-topic-title',
			'.settings-inner-content .options-section-title h2',
			'.settings-sidebar .account-menus h2',
			'.youzify-account-head h2',
			'.youzify .editfield fieldset legend',
			'.youzify-uploader-change-item h2',
			'.uk-option-item .option-infos label',
			'.youzify-review-item .youzify-head-meta .youzify-item-name a',
			'.youzify .activity-header .activity-head p a:first-of-type',
			'.youzify-wall-new-post .youzify-post-title a',
			'.youzify-my-account-widget .youzify-widget-header .youzify-widget-head .youzify-user-name',
			'.youzify-items-list-widget .youzify-list-item a.youzify-item-name',
			'.youzify-notifications-widget .youzify-notif-item .youzify-notif-content .youzify-notif-desc',
			'.youzify .youzify-wall-embed .youzify-embed-name',
			'.follows .youzify #youzify-members-list .item .item-title a',
			'.my-friends .youzify #youzify-members-list .item .item-title a',
			'.group_members #youzify-members-list li .item .item-title a',
			'.youzify .group-members-list li .item .item-title a',
			'.youzify-tab-post .youzify-post-title a',
			'.bbp-forum-info .youzify-forums-forum-head .youzify-forums-forum-title',
			'#bbpress-forums .youzify-bbp-box .youzify-bbp-box-title',
			'.youzify .activity-comments .acomment-meta a',
			'.youzify-directory-filter #directory-show-filter a',
			'.youzify-directory-filter #directory-show-search a',
			'.youzify-directory-filter .item-list-tabs li a',
			'#youzify-wall-nav li a',
			'.youzify-wall-link-data .youzify-wall-link-title',
			'#youzify-groups-list .item .item-title a',
			'.youzify-hdr-v2 .youzify-snumber',
			'#message-recipients .highlight h2',
			'.notifications tbody td.notification-description a',
			'#message-thread .message-metadata a',
			'.sitewide-notices .youzify-notice-msg-title',
			'.my-groups .youzify #youzify-groups-list .item .item-title a',
			'#youzify-profile-navmenu',
			'.youzify-account-settings-menu .youzify-menu-head .youzify-menu-title',
			'#youzify-export-data .youzify-section-content h2',
			'.uk-option-item .option-content label',
			'.youzify-widget .youzify-widget-title',
			'#youzify-members-list .youzify-fullname',
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
			'.youzify-widget .youzify-widget-main-content .youzify-more-items a',
			'.settings-sidebar .account-menus .youzify-active-menu',
			'.settings-sidebar .account-menus ul li a:hover',
			'.youzify-my-account-widget .youzify-menu-links .youzify-link-item',
			'.youzify .activity-inner span.activity-read-more a',
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
			'.youzify-widget .youzify-widget-main-content .youzify-more-items a:hover',
			'.youzify-widget .youzify-widget-main-content .youzify-more-items a:active',
			'.youzify-widget ..youzify .activity-inneryouzify-widget-main-content .youzify-more-items a:focus',
			'.youzify-widget .youzify-widget-main-content a:hover',
			'.youzify-my-account-widget .youzify-menu-links .youzify-link-item:hover',
			'.youzify .activity-inner span.activity-read-more a:hover',
			'.youzify #youzify-members-list .youzify-user-actions .follow-button a:hover',
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
			'.youzify-media .youzify-media-group-head',
			'.youzify-wg-border-radius .youzify-slideshow-img',
			'.youzify-wg-border-radius .youzify-tab-comment', '.youzify-wg-border-radius .youzify-tab-post',
			'.youzify-wg-border-radius .youzify-widget',
			'.youzify-wg-border-radius .youzify-widget .youzify-widget-main-content',
			'.youzify-wg-border-radius .youzify-widget.without-title .youzify-link-cover',
			'.youzify-wg-border-radius .youzify-widget.without-title .youzify-quote-content:before',
			'.youzify-wg-border-radius .youzify-widget.without-title .youzify-quote-cover',
			'#message-recipients',
			'#message-recipients .highlight-icon i',
			'#message-thread .message-box',
			'#send-reply',
			'#send-reply #send_reply_button',
			'#send_message_form',
			'#send_message_form .submit #send',
			'.follows .youzify #youzify-members-list li .youzify-user-data',
			'.follows .youzify-page-btns-border-radius #youzify-members-list .youzify-user-actions a',
			'.item-list-tabs #search-message-form #messages_search',
			'.item-list-tabs #search-message-form #messages_search_submit',
			'.messages-notices .thread-options a span',
			'.messages-options-nav #messages-bulk-manage',
			'.messages-options-nav select',
			'.my-friends .youzify #friend-list li',
			'.my-friends .youzify #youzify-members-list li .youzify-user-data',
			'.my-friends .youzify-page-btns-border-radius #friend-list .action a',
			'.my-friends .youzify-page-btns-border-radius #youzify-members-list .youzify-user-actions a',
			'.my-groups .youzify #youzify-groups-list li .youzify-group-data',
			'.my-groups .youzify-page-btns-border-radius #youzify-groups-list .action a',
			'.notifications .notification-actions a span',
			'.notifications-options-nav #notification-bulk-manage',
			'.notifications-options-nav select',
			'.youzify .pagination .page-numbers',
			'.youzify-form-item input[type=text]',
			'.youzify-form-item textarea',
			'.group_members #youzify-members-list li .youzify-user-data',
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
					'#youzify-members-list > li .youzify-user-cover',
					'#youzify-groups-list > li .youzify-user-cover',
					'#youzify-groups-list li .youzify-group-cover',
				) ),
				'property'      => 'border-radius',
				'value_pattern' => '$rem $rem 0 0',
			),
		) );
	}
}

return new Grimlock_BuddyPress_Youzify_Archive_Customizer();
