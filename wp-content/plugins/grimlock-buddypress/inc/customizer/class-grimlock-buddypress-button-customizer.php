<?php
/**
 * Grimlock_BuddyPress_Button_Customizer Class
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
class Grimlock_BuddyPress_Button_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'grimlock_button_customizer_elements',                          array( $this, 'add_elements'                          ), 10, 1 );
		add_filter( 'grimlock_button_customizer_border_radius_elements',            array( $this, 'add_border_radius_elements'            ), 10, 1 );
		add_filter( 'grimlock_button_customizer_sm_elements',                       array( $this, 'add_sm_elements'                       ), 10, 1 );
		add_filter( 'grimlock_button_customizer_xs_elements',                       array( $this, 'add_xs_elements'                       ), 10, 1 );
		add_filter( 'grimlock_button_customizer_primary_elements',                  array( $this, 'add_primary_elements'                  ), 10, 1 );
		add_filter( 'grimlock_button_customizer_primary_background_color_elements', array( $this, 'add_primary_background_color_elements' ), 10, 1 );
		add_filter( 'grimlock_button_customizer_primary_background_color_outputs',  array( $this, 'add_primary_background_color_outputs'  ), 10, 1 );
		add_filter( 'grimlock_button_customizer_primary_color_elements',            array( $this, 'add_primary_color_elements'            ), 10, 1 );
		add_filter( 'grimlock_button_customizer_primary_color_outputs',             array( $this, 'add_primary_color_outputs'             ), 10, 1 );
		add_filter( 'grimlock_button_customizer_secondary_elements',                array( $this, 'add_secondary_elements'                ), 10, 1 );
		add_filter( 'grimlock_button_customizer_font_outputs',                      array( $this, 'add_font_outputs'                      ), 10, 1 );
	}

	/**
	 * @param $elements
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function add_elements( $elements ) {
		return array_merge( $elements, array(
			'#buddypress a.button',
			'#buddypress div.generic-button a',
			'#buddypress .standard-form input[type="button"]',
			'#buddypress .standard-form input[type="submit"]',
			'#buddypress .standard-form button[type="submit"]',
			'#buddypress input[type="button"]',
			'#buddypress input[type="submit"]',
			'#buddypress button[type="submit"]',
			'#buddypress form input[type="button"]',
			'#buddypress form input[type="submit"]',
			'#buddypress form button[type="submit"]',
			'.widget div.bp-notification-widget-notifications-list ul.bp-notification-list li > div',
			'.widget div.bp-notification-widget-notifications-list ul.bp-notification-list li > a',
			'#buddypress #insert-media-button',
			'.wp-core-ui .button',
			'.wp-core-ui .button-primary',
			'#buddypress #bp-browse-button',
		) );
	}

	/**
	 * @param $elements
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function add_border_radius_elements( $elements ) {
		return array_merge( $elements, array(
			'#buddypress div.generic-button a',
			'#buddypress ul.item-list > li:not(.load-newest) div.action a',
			'#groups-list-options a',
			'#friends-list-options a',
			'#members-list-options a',
			'#bp-group-rating-list-options a',
			'#bp-member-rating-list-options a',
			'#infinite-handle span',

			'.tribe-events-day-time-slot',
			'.tribe-events-list-separator-month',
			'.tribe-grid-allday .tribe-event-featured.tribe-events-week-allday-single',
			'.tribe-grid-allday .tribe-event-featured.tribe-events-week-hourly-single',
			'.tribe-grid-body .tribe-event-featured.tribe-events-week-allday-single',
			'.tribe-grid-body .tribe-event-featured.tribe-events-week-hourly-single',

			'#buddypress:not(.youzer) .profile-content__body#item-body .screen-profile .nav.nav-pills .nav-link',
			'#whats-new-submit #aw-whats-new-submit',
		) );
	}

	/**
	 * Add CSS selectors to the array of CSS selectors for the small button.
	 *
	 * @param  array $elements The array of CSS selectors for the small button.
	 *
	 * @return array           The updated array of CSS selectors for the small button.
	 */
	public function add_sm_elements( $elements ) {
		return array_merge( $elements, array(
			'#buddypress:not(.youzer) div#item-header .item-admins div.generic-button .group-button',
			'#buddypress:not(.youzer) #activity-stream.grimlock-buddypress-activity-list .activity-item div.activity-comments form.ac-form input',
			'#buddypress:not(.youzer) #activity-stream.grimlock-buddypress-activity-list .activity-item div.activity-comments form.ac-form .ac-reply-cancel',
		) );
	}

	/**
	 * @param $elements
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function add_xs_elements( $elements ) {
		return array_merge( $elements, array(
			'#buddypress div.generic-button a',
			'#buddypress ul.item-list > li:not(.load-newest) div.action a',
		) );
	}

	/**
	 * @param $elements
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function add_primary_elements( $elements ) {
		return array_merge( $elements, array(
			'#buddypress a.button.bp-primary-action',
			'#buddypress .standard-form input[type="button"]',
			'#buddypress .standard-form input[type="submit"]',
			'#buddypress .standard-form button[type="submit"]',
			'#buddypress input[type="button"]',
			'#buddypress input[type="submit"]',
			'#buddypress button[type="submit"]',
			'#buddypress form input[type="button"]',
			'#buddypress form input[type="submit"]',
			'#buddypress form button[type="submit"]',
			"#buddypress:not(.youzer) .generic-button a",
			"#buddypress:not(.youzer) a.button:not(.spam-activity):not(#bp-create-doc-button)",
			'#rtmedia_create_new_album',
			'#buddypress:not(.youzer) div#item-header .item-admins div.generic-button .group-button.join-group',
			'#buddypress:not(.youzer) #avatar-crop-actions .avatar-crop-submit',
			'#groups-list-options a:focus',
			'#groups-list-options a:active',
			'#groups-list-options a.selected',
			'#groups-list-options a.loading',
			'#members-list-options a:focus',
			'#members-list-options a:active',
			'#members-list-options a.selected',
			'#members-list-options a.loading',
			'#friends-list-options a:focus',
			'#friends-list-options a:active',
			'#friends-list-options a.selected',
			'#friends-list-options a.loading',
			'#bp-group-rating-list-options a:focus',
			'#bp-group-rating-list-options a:active',
			'#bp-group-rating-list-options a.selected',
			'#bp-group-rating-list-options a.loading',
			'#bp-member-rating-list-options a:focus',
			'#bp-member-rating-list-options a:active',
			'#bp-member-rating-list-options a.selected',
			'#bp-member-rating-list-options a.loading',
			'.bp-messages-wrap .reply .send button[type="submit"]',
			'#buddypress:not(.youzer) div.item-list-tabs.primary-list-tabs > ul > li.selected > a',
			'#buddypress:not(.youzer) div.item-list-tabs.primary-list-tabs > ul > li.current > a',
			'#buddypress:not(.youzer) div.item-list-tabs.primary-list-tabs > ul > li.current > a:hover',
			'#buddypress:not(.youzer) div.item-list-tabs.primary-list-tabs > ul > li.current > a:active',
			'#buddypress:not(.youzer) div.item-list-tabs.primary-list-tabs > ul > li.current > a:focus',
			'#buddypress:not(.youzer) div.item-list-tabs.primary-list-tabs > ul > li.selected > a:hover',
			'#buddypress:not(.youzer) div.item-list-tabs.primary-list-tabs > ul > li.selected > a:active',
			'#buddypress:not(.youzer) div.item-list-tabs.primary-list-tabs > ul > li.selected > a:focus',
			'#buddypress:not(.youzer) div.item-list-tabs.primary-list-tabs > ul > li.selected > a span',
			'#buddypress:not(.youzer) div.item-list-tabs.primary-list-tabs > ul > li.current > a span',

			'#buddypress:not(.youzer) div.item-list-tabs.bp-navs > ul > li.selected > a',
			'#buddypress:not(.youzer) div.item-list-tabs.bp-navs > ul > li.current > a',
			'#buddypress:not(.youzer) div.item-list-tabs.bp-navs > ul > li.current > a:hover',
			'#buddypress:not(.youzer) div.item-list-tabs.bp-navs > ul > li.current > a:active',
			'#buddypress:not(.youzer) div.item-list-tabs.bp-navs > ul > li.current > a:focus',
			'#buddypress:not(.youzer) div.item-list-tabs.bp-navs > ul > li.selected > a:hover',
			'#buddypress:not(.youzer) div.item-list-tabs.bp-navs > ul > li.selected > a:active',
			'#buddypress:not(.youzer) div.item-list-tabs.bp-navs > ul > li.selected > a:focus',
			'#buddypress:not(.youzer) div.item-list-tabs.bp-navs > ul > li.selected > a span',
			'#buddypress:not(.youzer) div.item-list-tabs.bp-navs > ul > li.current > a span',
			'.webui-popover-bp-extended-friendship-popup .request-friend-ext-button-wrap .request-friend-ext-button',
			'.buddypress .padder > #buddypress.buddypress-wrap .button',
			'.wp-core-ui .button-primary',
			'.wp-core-ui .button',
			'#buddypress #insert-media-button',
			'.widget div.bp-notification-widget-notifications-list ul.bp-notification-list li > div',
			'.widget div.bp-notification-widget-notifications-list ul.bp-notification-list li > a',
			'.widget.widget_bp_birthday_widget .send-private-message a',
		) );
	}

	/**
	 * @param $elements
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function add_primary_background_color_elements( $elements ) {
		return array_merge( $elements, array(
			'#buddypress div.item-list-tabs.primary-list-tabs > ul > li.selected > a',
			'#buddypress div.item-list-tabs.bp-navs > ul > li.selected > a',
			'#buddypress div.item-list-tabs.primary-list-tabs > ul > li.current > a',
			'#buddypress div.item-list-tabs.bp-navs > ul > li.current > a',
			'#buddypress:not(.youzer) #profile-content__nav ul li > a span',
			'#buddypress:not(.youzer) #profile-content__nav ul li > a:after',
			'#buddypress:not(.youzer) #profile-content__nav .priority-nav__dropdown-toggle:after',
			'#groups-list-options a.loading:before',
			'#members-list-options a.loading:before',
			'#friends-list-options a.loading:before',
			'#bp-group-rating-list-options a.loading:before',
			'#bp-member-rating-list-options a.loading:before',
			'#buddypress:not(.youzer) .mejs-controls .mejs-time-rail .mejs-time-current',
			'#buddypress:not(.youzer) .rtm-lightbox-container .rtmedia-single-meta .rtmedia-like:hover',
			'#buddypress:not(.youzer) .rtm-lightbox-container .rtmedia-single-meta .rtmedia-like:focus',
			'#buddypress:not(.youzer) .rtm-tabs > li.active a',
			'#buddypress:not(.youzer) #activity-stream.grimlock-buddypress-activity-list .activity-item:hover:before',
			'#buddypress:not(.youzer) table#message-threads tr.unread td',
			'#secondary-right .widget.buddypress.widget_bp_featured_members_list_widget .lSSlideOuter .lSPager > li.active > a',
			'#secondary-right .widget.buddypress.widget_bp_featured_members_list_widget .lSSlideOuter .lSPager > li:hover > a',
			'#secondary-left .widget.buddypress.widget_bp_featured_members_list_widget .lSSlideOuter .lSPager > li.active > a',
			'#secondary-left .widget.buddypress.widget_bp_featured_members_list_widget .lSSlideOuter .lSPager > li:hover > a',
			'.buddypress.register .bp_register #register-page #signup_form .register-section:before',
			'.main-navigation .navbar-nav.navbar-nav--buddypress .has-notification .bp-notifications-nav:after',
			'.grimlock-buddypress-groups-section .section__content ul#groups-list > li div.item-avatar:before',
			'.grimlock-buddypress-groups-section .section__content ul#groups-list > li div.item-avatar:after',
			'.uppy-StatusBar-progress',
			'#buddypress .bppp-bar',
			'.bppp-congrats .dashicons',
			'#buddypress .card-body .item-meta .activity .dashicons',
			'.bp-card-list .hmk-match-value',
		) );
	}

	/**
	 * @param $outputs
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function add_primary_background_color_outputs( $outputs ) {
		return array_merge( $outputs, array(
			array(
				'element'  => '#buddypress div.item-list-tabs.primary-list-tabs > ul > li> a span.count',
				'property' => 'color'
			),
			array(
				'element'  => implode( ',', array(
					'.activity-item.joined_group .card',
					'.activity-item.created_group .card',
					'body #buddypress:not(.youzer) div.message-search > form input[type="text"]:focus',
					'body #buddypress:not(.youzer) div.dir-search > form input[type="text"]:focus',
				) ),
				'property' => 'border-bottom-color',
			),
			array(
				'element'  => implode( ',', array(
					'#buddypress:not(.youzer) .drag-drop #drag-drop-area.rtm-drag-drop-active',
					'#buddypress:not(.youzer) .rtm-drag-drop-active',
					'.main-navigation .navbar-nav.navbar-nav--buddypress.logged-out .menu-item--profile:hover:after',
					'#friends-list-options a.selected',
					'#members-list-options a.selected',
					'#groups-list-options a.selected',
					'#bp-group-rating-list-options a.selected',
					'#bp-member-rating-list-options a.selected',
				) ),
				'property' => 'border-color',
				'suffix'   => '!important',
			),
			array(
				'element'  => implode( ',', array(
					'.c100.hmk-percentage .slice .bar',
					'.c100.hmk-percentage .slice .fill',
					'#buddypress:not(.youzer) #media_search_form #media_search_input:focus',
					'#buddypress:not(.youzer) div#item-header .profile-header__avatar img.avatar:hover',
					'#groups-list-options a:hover',
					'#members-list-options a:hover',
					'#friends-list-options a:hover',
					'#bp-group-rating-list-options a:hover',
					'#bp-member-rating-list-options a:hover',
					'#buddypress:not(.youzer) #item-body form#whats-new-form #whats-new-options select:focus',
					'#buddypress:not(.youzer) form#whats-new-form #whats-new-options select:focus',
					'#buddypress:not(.youzer) #activity-stream.grimlock-buddypress-activity-list .activity-item div.activity-comments form.ac-form .ac-textarea textarea:focus',
				) ),
				'property' => 'border-color',
			),
			array(
				'element'  => implode( ',', array(
					'#members-list.bp-card-list .hmk-trigger-match:hover',
					'#members-list.bp-card-list .member-list__item .hmk-trigger-match:active',
					'#members-list.bp-card-list .member-list__item .hmk-trigger-match:focus',
					'#members-list.bp-card-list .member-list__item .hmk-trigger-match span',
					'#buddypress:not(.youzer) div#item-header .hmk-percentage > span:last-of-type',
					'#buddypress:not(.youzer) div.item-list-tabs.primary-list-tabs > ul > li > a:hover',
					'#buddypress:not(.youzer) div.item-list-tabs.bp-navs > ul > li > a:hover',
					'#buddypress:not(.youzer) #profile-content__nav ul li > a:hover',
					'#buddypress:not(.youzer) #profile-content__nav ul li > a:active',
					'#buddypress:not(.youzer) #profile-content__nav ul li > a:focus',
					'#buddypress:not(.youzer) #profile-content__nav .priority-nav__dropdown-toggle:hover',
					'#buddypress:not(.youzer) #profile-content__nav .priority-nav__dropdown-toggle:active',
					'#buddypress:not(.youzer) #profile-content__nav .priority-nav__dropdown-toggle:focus',
					'#buddypress:not(.youzer) #activity-stream.grimlock-buddypress-activity-list .activity-item div.activity-comments .acomment-meta a:hover',
					'#buddypress:not(.youzer) #profile-content__nav ul li.current > a',
					'#buddypress:not(.youzer) #profile-content__nav ul li.selected > a',
					'#buddypress:not(.youzer) #profile-content__nav ul li:hover > a',
					'div.item-options a:hover',
					'#buddypress:not(.youzer) #activity-stream.grimlock-buddypress-activity-list .activity-item .activity-content .activity-read-more > a.loading:after',
					'#buddypress:not(.youzer) .rtm-tabs > li.active a',
					'#buddypress:not(.youzer) #activity-stream.grimlock-buddypress-activity-list .activity-item:hover:after',
					'#groups-list-options a:hover',
					'#members-list-options a:hover',
					'#friends-list-options a:hover',
					'#bp-group-rating-list-options a:hover',
					'#bp-member-rating-list-options a:hover',
					'.buddypress.groups.single-item div#item-header .item-admins > .btn:hover',
					'.buddypress.groups.single-item div#item-header .item-admins > .btn:active',
					'.buddypress.groups.single-item div#item-header .item-admins > .btn:focus',
					'.bp-card-list .hmk-match-text',
					'#buddypress div.item-list-tabs.bp-navs > ul > li> a span.count',
				) ),
				'property' => 'color',
			),
			array(
				'element'       => implode( ',', array(
					'.ai_reaction_loader_inner:after',
				) ),
				'property'      => 'border-color',
				'value_pattern' => '$ transparent transparent',
			),
			array(
				'element'  => implode( ',', array(
					'.bp-messages-wrap .reply .send button[type="submit"]',
					'.bp-messages-wrap div.bulk-message .progress-value',
				) ),
				'property' => 'background-color',
				'suffix'   => '!important',
			),
			array(
				'element'  => implode( ',', array(
					'.item-notification-friend-request',
				) ),
				'property' => 'color',
				'suffix'   => '!important',
			),
			array(
				'element'  => implode( ',', array(
					'a.bps-toggle-modal:hover span',
				) ),
				'property' => 'background-color',
				'suffix'   => '1c',
			),
		) );
	}

	/**
	 * @param $elements
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function add_primary_color_elements( $elements ) {
		return array_merge( $elements, array(
			'#buddypress div.item-list-tabs.primary-list-tabs > ul > li.selected > a',
			'#buddypress div.item-list-tabs.bp-navs > ul > li.selected > a',
			'#buddypress div.item-list-tabs.primary-list-tabs > ul > li.current > a',
			'#buddypress div.item-list-tabs.bp-navs > ul > li.current > a',
			'#buddypress:not(.youzer) #profile-content__nav ul li > a span',
			'#buddypress:not(.youzer) .div.item-list-tabs.primary-list-tabs > ul > li.selected > a',
			'#buddypress:not(.youzer) .div.item-list-tabs.bp-navs > ul > li.selected > a',
			'#buddypress:not(.youzer) table#message-threads tr.unread td',
			'.buddypress.register .bp_register #register-page #signup_form .register-section:before',
			'.uppy-StatusBar-progress',
			'#buddypress .bppp-congrats .dashicons',
			'#buddypress .card-body .item-meta .activity .dashicons',
			'.bp-card-list .hmk-match-value',
		) );
	}

	/**
	 * @param $outputs
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function add_primary_color_outputs( $outputs ) {
		return array_merge( $outputs, array(
			array(
				'element'  => '#buddypress div.item-list-tabs.primary-list-tabs > ul > li> a span.count',
				'property' => 'background-color',
			),
			array(
				'element'  => implode( ',', array(
					'.bp-card-list .hmk-match-text',
					'#buddypress div.item-list-tabs.bp-navs > ul > li> a span.count',
				) ),
				'property' => 'background-color',
			),
		) );
	}

	/**
	 * @param $elements
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function add_secondary_elements( $elements ) {
		return array_merge( $elements, array(
			'#buddypress a.button',
			'#buddypress div.generic-button a',
			'#buddypress #bp-browse-button',
			'#buddypress:not(.youzer) button#rtmedia-add-media-button-post-update',
			'#buddypress:not(.youzer) button.rtmedia-comment-media-upload',
			'#buddypress:not(.youzer) .standard-form div.submit input[type="button"]',
			'#create-controls input[type="submit"].cancel',
		) );
	}

	/**
	 * Add selectors and properties to the CSS rule-set for the button font.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $outputs The array of CSS selectors and properties for the button font.
	 *
	 * @return array          The updated array of CSS selectors for the button font.
	 */
	public function add_font_outputs( $outputs ) {
		return array_merge( $outputs, array(
			array(
				'element'  => implode( ',', array(
					'body:not([class*="yz-"]) .widget div.item-options a',
					'#groups-list-options a',
					'#members-list-options a',
					'#friends-list-options a',
					'#bp-group-rating-list-options a',
					'#bp-member-rating-list-options a',
					'.card .hmk-trigger-match',
					'.card .mutual-friends',
					'body.grimlock-buddypress--members-actions-text-displayed #site #members-list div.action a',
					'body.grimlock-buddypress--members-actions-text-displayed #site #members-swipe-list div.action a',
					'body.grimlock-buddypress--members-actions-text-displayed #site #friend-list div.action a',
					'body.grimlock-buddypress--members-actions-text-displayed #site #admins-list div.action a',
					'body.grimlock-buddypress--members-actions-text-displayed #site #mods-list div.action a',
					'body.grimlock-buddypress--members-actions-text-displayed #site div#item-header #profile-header.profile-header--member #item-buttons.action a',
					'body.grimlock-buddypress--groups-actions-text-displayed #site #groups-list div.action a',
					'body.grimlock-buddypress--groups-actions-text-displayed #site div#item-header #profile-header.profile-header--group #item-buttons.action a',
				) ),
				'property' => 'font-weight',
				'choice'   => 'font-weight',
			),
			array(
				'element'  => implode( ',', array(
					'#groups-list-options a',
					'#members-list-options a',
					'#friends-list-options a',
					'#bp-group-rating-list-options a',
					'#bp-member-rating-list-options a',
				) ),
				'property' => 'text-transform',
				'choice'   => 'text-transform',
			),
		) );
	}
}

return new Grimlock_BuddyPress_Button_Customizer();
