<?php
/**
 * Grimlock_BuddyPress_Archive_Customizer Class
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
class Grimlock_BuddyPress_Archive_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'grimlock_archive_customizer_elements',                       array( $this, 'add_elements'                       ), 10, 1 );
		add_filter( 'grimlock_archive_customizer_post_background_color_elements', array( $this, 'add_post_background_color_elements' ), 10, 1 );
		add_filter( 'grimlock_archive_customizer_post_background_color_outputs',  array( $this, 'add_post_background_color_outputs'  ), 10, 1 );
		add_filter( 'grimlock_archive_customizer_post_color_elements',            array( $this, 'add_post_color_elements'            ), 10, 1 );
		add_filter( 'grimlock_archive_customizer_post_color_outputs',             array( $this, 'add_post_color_outputs'             ), 10, 1 );
		add_filter( 'grimlock_archive_customizer_post_border_color_outputs',      array( $this, 'add_post_border_color_outputs'      ), 10, 1 );
		add_filter( 'grimlock_archive_customizer_post_border_width_ouputs',       array( $this, 'add_post_border_width_outputs'      ), 10, 1 );
		add_filter( 'grimlock_archive_customizer_post_title_color_elements',      array( $this, 'add_post_title_color_elements'      ), 10, 1 );
		add_filter( 'grimlock_archive_customizer_post_link_color_elements',       array( $this, 'add_post_link_color_elements'       ), 10, 1 );
		add_filter( 'grimlock_archive_customizer_post_link_hover_color_elements', array( $this, 'add_post_link_hover_color_elements' ), 10, 1 );
		add_filter( 'grimlock_archive_customizer_post_border_radius_elements',    array( $this, 'add_post_border_radius_elements'    ), 10, 1 );
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
			'.bp_register #register-page .standard-form .register-section',
			'#buddypress:not(.youzer) #activity-stream.grimlock-buddypress-activity-list .activity-item',
			'.lwa.lwa-divs-only',
			'.buddypress.directory.members .region--9-3-cols-left .widget',
			'.buddypress.directory.members .region--3-9-cols-left .widget',
			'.buddypress.directory.activity .region--9-3-cols-left .widget',
			'.buddypress.directory.activity .region--3-9-cols-left .widget',
			'.buddypress.directory.groups .region--9-3-cols-left .widget',
			'.buddypress.directory.groups .region--3-9-cols-left .widget',
			'.post-save-options.messages-container',
			'.bp-messages-wrap',
			'html body .bp-messages-wrap.bp-messages-mobile',
			'.users-blocked',
			'#buddypress:not(.youzer) #activity-stream.grimlock-buddypress-activity-list .load-more > a',
			'#buddypress:not(.youzer) #activity-stream.grimlock-buddypress-activity-list .load-newest > a',
			'.buddypress.directory.members #buddypress:not(.youzer) .bps_filters',
			'#articles-dir-list #articles-container > #message',
			'#bbp-search-form',
			'.profile-content__body #subnav ~ form.standard-form',
			'.buddypress.settings.bp-user.privacy .profile-content__body #subnav ~ form',
			'#buddypress > .pos-r > .bps_form',
			// TODO : Migrate to BP Docs class
			'#docs-filter-sections .docs-filter-section',
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
			'#register-page .lwa.lwa-divs-only .lwa-remember',
			'#buddypress:not(.youzer) div.item-list-tabs.primary-list-tabs > ul',
			'#buddypress:not(.youzer) div.item-list-tabs.primary-list-tabs > ul > li > a span',
			'#buddypress:not(.youzer) div.item-list-tabs.bp-navs > ul',
			'#buddypress:not(.youzer) div.item-list-tabs.bp-navs > ul > li > a span',
			'.priority-nav__dropdown',
			'#buddypress:not(.youzer) #profile-content__nav',
			'#buddypress:not(.youzer) #profile-content__nav .priority-nav__dropdown',
			'.group-invites #buddypress:not(.youzer) #send-invite-form > .invite .left-menu div#invite-list > ul > li',
			'.group-invites #buddypress:not(.youzer) #group-create-body .left-menu div#invite-list > ul > li',
			'.webui-popover-bp-extended-friendship-popup',
			'.item-notification-friend-request',
			'.c100:after',
			'.widget_bp_birthday_widget ul.birthday-members-list li .emoji',
			'.members-map .gm-style .gm-style-iw-c',
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
			'.group-invites #buddypress:not(.youzer) #send-invite-form > .invite .left-menu div#invite-list > ul > li',
			'.group-invites #buddypress:not(.youzer) #group-create-body .left-menu div#invite-list > ul > li',
			'.bp-card-list--members__item .card > a',
			'.bp_register #register-page .standard-form .register-section',
			'#buddypress:not(.youzer) div.item-list-tabs.primary-list-tabs > ul > li + li',
			'#buddypress:not(.youzer) div.item-list-tabs.primary-list-tabs > ul > li > a',
			'#buddypress:not(.youzer) div.item-list-tabs.primary-list-tabs > ul > li > span',
			'#buddypress:not(.youzer) div.item-list-tabs.bp-navs > ul > li + li',
			'#buddypress:not(.youzer) div.item-list-tabs.bp-navs > ul > li > a',
			'#buddypress:not(.youzer) div.item-list-tabs.bp-navs > ul > li > span',
			'.webui-popover-bp-extended-friendship-popup',
			'#buddypress:not(.youzer) #activity-stream.grimlock-buddypress-activity-list .load-more.loading > a:after',
			'#buddypress:not(.youzer) #activity-stream.grimlock-buddypress-activity-list .load-newest.loading > a:after',
			'#buddypress:not(.youzer) div#item-header .hmk-percentage',
			'.members-map .gm-style .gm-style-iw-c',
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
				'element'       => implode( ',', array(
					'#buddypress:not(.youzer) #profile-content__nav:after',
				) ),
				'property'      => 'background-image',
				'value_pattern' => 'linear-gradient(90deg, rgba(255,255,255,0) 0%, $ 100%)',
				'media_query'   => '@media (max-width: 768px)',
			),
			array(
				'element'  => implode( ',', array(
					'.webui-popover.bottom > .webui-arrow:after',
					'.webui-popover.bottom-right > .webui-arrow:after',
					'.webui-popover.bottom-left > .webui-arrow:after',
				) ),
				'property' => 'border-bottom-color',
			),
			array(
				'element'       => implode( ',', array(
					'[class*="bp_core_whos_online_widget"]:not([class*="yz-"]) .item-avatar > a:before',
				) ),
				'property'      => 'box-shadow',
				'value_pattern' => '0 0 0 5px $',
			),
			array(
				'element'       => implode( ',', array(
					'.members-map .gm-style .gm-style-iw-t::after',
				) ),
				'property' => 'background',
				'suffix'   => '!important',
			),
			array(
				'element'       => implode( ',', array(
					'#buddypress:not(.youzer) div.item-list-tabs.primary-list-tabs > ul > li > a:hover span',
					'#buddypress:not(.youzer) div.item-list-tabs.primary-list-tabs > ul > li > a:focus span',
					'#buddypress:not(.youzer) div.item-list-tabs.primary-list-tabs > ul > li > a:active span',
					'#buddypress:not(.youzer) div.item-list-tabs.primary-list-tabs > ul > li.selected > a span',
					'#buddypress:not(.youzer) div.item-list-tabs.primary-list-tabs > ul > li.current > a span',

					'#buddypress:not(.youzer) div.item-list-tabs.bp-navs > ul > li > a:hover span',
					'#buddypress:not(.youzer) div.item-list-tabs.bp-navs > ul > li > a:focus span',
					'#buddypress:not(.youzer) div.item-list-tabs.bp-navs > ul > li > a:active span',
					'#buddypress:not(.youzer) div.item-list-tabs.bp-navs > ul > li.selected > a span',
					'#buddypress:not(.youzer) div.item-list-tabs.bp-navs > ul > li.current > a span',
				) ),
				'property'      => 'box-shadow',
				'value_pattern' => '0 0 0 3px $',
				'media_query'   => '@media (min-width: 768px)',
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
					'.bp-card-list .card-img svg',
					'.bp-card-list .card-img svg rect',
				) ),
				'property' => 'fill',
				'suffix'   => '!important',
			),
		) );
	}

	/**
	 * Add selectors and properties to the CSS rule-set for the archive post border color.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $outputs The array of CSS selectors and properties for the archive post border color.
	 *
	 * @return array          The updated array of CSS selectors for the archive post border color.
	 */
	public function add_post_border_color_outputs( $outputs ) {
		return array_merge( $outputs, array(
			array(
				'element'  => implode( ',', array(
					'#buddypress:not(.youzer) div.item-list-tabs.primary-list-tabs > ul',
					'#buddypress:not(.youzer) div.item-list-tabs.bp-navs > ul',
					'#buddypress:not(.youzer) #profile-content__nav .priority-nav__dropdown',
					'.webui-popover-bp-extended-friendship-popup',
					'#buddypress:not(.youzer) div.item-list-tabs.primary-list-tabs > ul > li > a span',
					'#buddypress:not(.youzer) div.item-list-tabs.bp-navs > ul > li > a span',
				) ),
				'property' => 'border-color',
			),
			array(
				'element'  => implode( ',', array(
					'#articles-dir-list .article-container .article-footer',
					'.bp-messages-wrap .reply',
					'.login-footer',
				) ),
				'property' => 'border-top-color',
			),
			array(
				'element'  => implode( ',', array(
					'#buddypress:not(.youzer) #profile-content__nav',
				) ),
				'property' => 'border-bottom-color',
			),
		) );
	}

	/**
	 * Add selectors and properties to the CSS rule-set for the archive post border color.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $outputs The array of CSS selectors and properties for the archive post border color.
	 *
	 * @return array          The updated array of CSS selectors for the archive post border color.
	 */
	public function add_post_border_width_outputs( $outputs ) {
		return array_merge( $outputs, array(
			array(
				'element'  => implode( ',', array(
					'#buddypress:not(.youzer) div.item-list-tabs.primary-list-tabs > ul > li > a span',
					'#buddypress:not(.youzer) div.item-list-tabs.bp-navs > ul > li > a span',
				) ),
				'property' => 'border-width',
				'units'    => 'px',
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
			'#buddypress:not(.youzer) #activity-stream.grimlock-buddypress-activity-list .activity-item div.activity-comments .acomment-meta a',
			'#buddypress:not(.youzer) #profile-content__nav ul li > a',
			'#buddypress:not(.youzer) #profile-content__nav .priority-nav__dropdown-toggle',
			'#articles-dir-list .article-container .article-data .title',
			'.webui-popover-bp-extended-friendship-popup .webui-popover-title',
			// TODO : Migrate to BP Docs class
			'[class*="widget_recent_bp_docs"] ul li a:not(:hover)',
			'.members-dir-map-sidebar .bps-label',
			'.members-dir-map-sidebar .widget-title',
			'.members-map .gm-style .gm-style-iw-c .members-map-pin-popup__name',
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
			'.card .mutual-friends',
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
			'.card .mutual-friends:hover',
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
			'.priority-nav__dropdown',
			'#buddypress:not(.youzer) div#item-header .profile-header__body .item-activity',
			'.buddypress.groups.single-item #buddypress:not(.youzer) .cover-btn-edit',
			'.buddypress.bp-user:not(.activity-permalink) #buddypress:not(.youzer) .cover-btn-edit',
			'.yith-ajaxsearchform-container .autocomplete-suggestions',
			'.vex.vex-theme-flat-attack .vex-content',
			'#buddypress:not(.youzer).bmf-white-popup',
			'#buddypress:not(.youzer) #activity-stream.grimlock-buddypress-activity-list .activity-item .activity-content .activity-inner img',
			'#articles-dir-list .article-container .article-image',
			'.bp-messages-wrap .threads-list .thread .pic.group > *',
			'.buddypress.groups.single-item .entry-content > #buddypress:not(.youzer) .avatar-overlay-edit:after',
			'.buddypress.bp-user:not(.activity-permalink) .entry-content > #buddypress:not(.youzer) .avatar-overlay-edit:after',
			'.webui-popover-bp-extended-friendship-popup',
			'#buddypress .profile-header__featured-media .rtmedia-list-item img',
			'#articles-dir-list .article-container .article-content',
			'#bbpress-forums ul.bbp-forums li.bbp-header',
			'#bbpress-forums ul.bbp-topics li.bbp-header',
			'#bbpress-forums ul.bbp-search-results li.bbp-header',
			'#buddypress:not(.youzer) div.section .rtm-privacy-levels',
			'#buddypress:not(.youzer) .standard-form .checkbox',
			'#buddypress:not(.youzer) .standard-form .radio',
			'.widget.buddypress #message',
			'.widget_bp_birthday_widget ul.birthday-members-list li',
			'ul.item-list:not(.activity-list):not([class*="yz-"]) > li',
			// TODO : Migrate to BP DOCS class
			'.standard-form label[for="bp-docs-group-enable"]',
			'#buddypress .standard-form #group-doc-options',
			'[class*="widget_recent_bp_docs"] ul li',
			'#doc-permissions-summary',
			'#doc-permissions-details',
			'.folder-toggle-link',
			'.members-map .gm-style .gm-style-iw-c',
			'#bpchk-place-map',
			'.checkin-by-autocomplete',
			'#checkin-by-autocomplete-map',
		) );
	}
}

return new Grimlock_BuddyPress_Archive_Customizer();
