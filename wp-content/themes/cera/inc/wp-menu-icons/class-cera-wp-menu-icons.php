<?php
/**
 * Cera WP Menu Icons Class
 *
 * @package  cera
 * @author   Themosaurus
 * @since    1.1.11
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Cera_WP_Menu_Icons' ) ) :
	/**
	 * The Cera WP Menu Icons integration class
	 */
	class Cera_WP_Menu_Icons {
		/**
		 * Setup class.
		 */
		public function __construct() {
			add_action( 'admin_enqueue_scripts',        array( $this, 'admin_enqueue_scripts' ), 10    );
			add_filter( 'wp_menu_icons_register_icons', array( $this, 'add_cera_icons'        ), 20, 1 );
		}

		/**
		 * Enqueue admin scripts and styles
		 */
		public function admin_enqueue_scripts() {
			wp_enqueue_style( 'cera-icons', get_template_directory_uri() . '/assets/css/cera-icons.css', array(), CERA_VERSION );
		}

		/**
		 * Add Cera icons to the available WP Menu Icons
		 *
		 * @param array $icons Array of available menu icons sets
		 *
		 * @return array
		 */
		public function add_cera_icons( $icons ) {
			$icons['cera_icons'] = array(
				'name'    => 'Cera Icons',
				'url'     => false,
				'iconmap' => 'cera-icon cera-menu-arrow, cera-icon cera-quote, cera-icon cera-pushpin, cera-icon cera-swap, cera-icon cera-alphabetical, cera-icon cera-talk, cera-icon cera-student, cera-icon cera-reply-all, cera-icon cera-star-on, cera-icon cera-heart-on, cera-icon cera-activity, cera-icon cera-airplay, cera-icon cera-alert-circle, cera-icon cera-alert-octagon, cera-icon cera-alert-triangle, cera-icon cera-align-center, cera-icon cera-align-justify, cera-icon cera-align-left, cera-icon cera-align-right, cera-icon cera-anchor, cera-icon cera-aperture, cera-icon cera-archive, cera-icon cera-arrow-down-circle, cera-icon cera-arrow-down-left, cera-icon cera-arrow-down-right, cera-icon cera-arrow-down, cera-icon cera-arrow-left-circle, cera-icon cera-arrow-left, cera-icon cera-arrow-right-circle, cera-icon cera-arrow-right, cera-icon cera-arrow-up-circle, cera-icon cera-arrow-up-left, cera-icon cera-arrow-up-right, cera-icon cera-arrow-up, cera-icon cera-at-sign, cera-icon cera-award, cera-icon cera-bar-chart-2, cera-icon cera-bar-chart, cera-icon cera-battery-charging, cera-icon cera-battery, cera-icon cera-bell-off, cera-icon cera-bell, cera-icon cera-bluetooth, cera-icon cera-bold, cera-icon cera-book-open, cera-icon cera-book, cera-icon cera-bookmark, cera-icon cera-box, cera-icon cera-briefcase, cera-icon cera-calendar, cera-icon cera-camera-off, cera-icon cera-camera, cera-icon cera-cast, cera-icon cera-check-circle, cera-icon cera-check-square, cera-icon cera-check, cera-icon cera-chevron-down, cera-icon cera-chevron-left, cera-icon cera-chevron-right, cera-icon cera-chevron-up, cera-icon cera-chevrons-down, cera-icon cera-chevrons-left, cera-icon cera-chevrons-right, cera-icon cera-chevrons-up, cera-icon cera-chrome, cera-icon cera-circle, cera-icon cera-clipboard, cera-icon cera-clock, cera-icon cera-cloud-drizzle, cera-icon cera-cloud-lightning, cera-icon cera-cloud-off, cera-icon cera-cloud-rain, cera-icon cera-cloud-snow, cera-icon cera-cloud, cera-icon cera-code, cera-icon cera-codepen, cera-icon cera-codesandbox, cera-icon cera-coffee, cera-icon cera-columns, cera-icon cera-command, cera-icon cera-compass, cera-icon cera-copy, cera-icon cera-corner-down-left, cera-icon cera-corner-down-right, cera-icon cera-corner-left-down, cera-icon cera-corner-left-up, cera-icon cera-corner-right-down, cera-icon cera-corner-right-up, cera-icon cera-corner-up-left, cera-icon cera-corner-up-right, cera-icon cera-cpu, cera-icon cera-credit-card, cera-icon cera-crop, cera-icon cera-crosshair, cera-icon cera-database, cera-icon cera-delete, cera-icon cera-disc, cera-icon cera-dollar-sign, cera-icon cera-download-cloud, cera-icon cera-download, cera-icon cera-droplet, cera-icon cera-edit-2, cera-icon cera-edit-3, cera-icon cera-edit, cera-icon cera-external-link, cera-icon cera-eye-off, cera-icon cera-eye, cera-icon cera-facebook, cera-icon cera-fast-forward, cera-icon cera-feather, cera-icon cera-figma, cera-icon cera-file-minus, cera-icon cera-file-plus, cera-icon cera-file-text, cera-icon cera-file, cera-icon cera-film, cera-icon cera-filter, cera-icon cera-flag, cera-icon cera-folder-minus, cera-icon cera-folder-plus, cera-icon cera-folder, cera-icon cera-framer, cera-icon cera-frown, cera-icon cera-gift, cera-icon cera-git-branch, cera-icon cera-git-commit, cera-icon cera-git-merge, cera-icon cera-git-pull-request, cera-icon cera-github, cera-icon cera-gitlab, cera-icon cera-globe, cera-icon cera-grid, cera-icon cera-hard-drive, cera-icon cera-hash, cera-icon cera-headphones, cera-icon cera-heart, cera-icon cera-help-circle, cera-icon cera-hexagon, cera-icon cera-home, cera-icon cera-image, cera-icon cera-inbox, cera-icon cera-info, cera-icon cera-instagram, cera-icon cera-italic, cera-icon cera-key, cera-icon cera-layers, cera-icon cera-layout, cera-icon cera-life-buoy, cera-icon cera-link-2, cera-icon cera-link, cera-icon cera-linkedin, cera-icon cera-list, cera-icon cera-loader, cera-icon cera-lock, cera-icon cera-log-in, cera-icon cera-log-out, cera-icon cera-mail, cera-icon cera-map-pin, cera-icon cera-map, cera-icon cera-maximize-2, cera-icon cera-maximize, cera-icon cera-meh, cera-icon cera-menu, cera-icon cera-message-circle, cera-icon cera-message-square, cera-icon cera-mic-off, cera-icon cera-mic, cera-icon cera-minimize-2, cera-icon cera-minimize, cera-icon cera-minus-circle, cera-icon cera-minus-square, cera-icon cera-minus, cera-icon cera-monitor, cera-icon cera-moon, cera-icon cera-more-horizontal, cera-icon cera-more-vertical, cera-icon cera-mouse-pointer, cera-icon cera-move, cera-icon cera-music, cera-icon cera-navigation-2, cera-icon cera-navigation, cera-icon cera-octagon, cera-icon cera-package, cera-icon cera-paperclip, cera-icon cera-pause-circle, cera-icon cera-pause, cera-icon cera-pen-tool, cera-icon cera-percent, cera-icon cera-phone-call, cera-icon cera-phone-forwarded, cera-icon cera-phone-incoming, cera-icon cera-phone-missed, cera-icon cera-phone-off, cera-icon cera-phone-outgoing, cera-icon cera-phone, cera-icon cera-pie-chart, cera-icon cera-play-circle, cera-icon cera-play, cera-icon cera-plus-circle, cera-icon cera-plus-square, cera-icon cera-plus, cera-icon cera-pocket, cera-icon cera-power, cera-icon cera-printer, cera-icon cera-radio, cera-icon cera-refresh-ccw, cera-icon cera-refresh-cw, cera-icon cera-repeat, cera-icon cera-rewind, cera-icon cera-rotate-ccw, cera-icon cera-rotate-cw, cera-icon cera-rss, cera-icon cera-save, cera-icon cera-scissors, cera-icon cera-search, cera-icon cera-send, cera-icon cera-server, cera-icon cera-settings, cera-icon cera-share-2, cera-icon cera-share, cera-icon cera-shield-off, cera-icon cera-shield, cera-icon cera-shopping-bag, cera-icon cera-shopping-cart, cera-icon cera-shuffle, cera-icon cera-sidebar, cera-icon cera-skip-back, cera-icon cera-skip-forward, cera-icon cera-slack, cera-icon cera-slash, cera-icon cera-sliders, cera-icon cera-smartphone, cera-icon cera-smile, cera-icon cera-speaker, cera-icon cera-square, cera-icon cera-star, cera-icon cera-stop-circle, cera-icon cera-sun, cera-icon cera-sunrise, cera-icon cera-sunset, cera-icon cera-tablet, cera-icon cera-tag, cera-icon cera-target, cera-icon cera-terminal, cera-icon cera-thermometer, cera-icon cera-thumbs-down, cera-icon cera-thumbs-up, cera-icon cera-toggle-left, cera-icon cera-toggle-right, cera-icon cera-trash-2, cera-icon cera-trash, cera-icon cera-trello, cera-icon cera-trending-down, cera-icon cera-trending-up, cera-icon cera-triangle, cera-icon cera-truck, cera-icon cera-tv, cera-icon cera-twitter, cera-icon cera-type, cera-icon cera-umbrella, cera-icon cera-underline, cera-icon cera-unlock, cera-icon cera-upload-cloud, cera-icon cera-upload, cera-icon cera-user-check, cera-icon cera-user-minus, cera-icon cera-user-plus, cera-icon cera-user-x, cera-icon cera-user, cera-icon cera-users, cera-icon cera-video-off, cera-icon cera-video, cera-icon cera-voicemail, cera-icon cera-volume-1, cera-icon cera-volume-2, cera-icon cera-volume-x, cera-icon cera-volume, cera-icon cera-watch, cera-icon cera-wifi-off, cera-icon cera-wifi, cera-icon cera-wind, cera-icon cera-x-circle, cera-icon cera-x-octagon, cera-icon cera-x-square, cera-icon cera-x, cera-icon cera-youtube, cera-icon cera-zap-off, cera-icon cera-zap, cera-icon cera-zoom-in, cera-icon cera-zoom-out',
			);

			return $icons;
		}
	}
endif;

return new Cera_WP_Menu_Icons();
