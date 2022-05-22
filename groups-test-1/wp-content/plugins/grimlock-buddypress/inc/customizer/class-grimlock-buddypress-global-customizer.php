<?php
/**
 * Grimlock_BuddyPress_Global_Customizer Class
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
 * The background image class for the Customizer.
 */
class Grimlock_BuddyPress_Global_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'grimlock_global_customizer_content_background_color_elements', array( $this, 'add_content_background_color_elements' ), 10, 1 );
		add_filter( 'grimlock_global_customizer_content_background_color_outputs',  array( $this, 'add_content_background_color_outputs'  ), 10, 1 );
	}

	/**
	 * Add CSS selectors to the array of CSS selectors for the content background color.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $elements The array of CSS selectors for the content background color.
	 *
	 * @return array           The updated array of CSS selectors for the content background color.
	 */
	public function add_content_background_color_elements( $elements ) {
		return array_merge( $elements, array(
			'#buddypress:not(.youzer) .profile-content',
			'#buddypress:not(.youzer) .rtm-lightbox-container',
			'.mfp-content .rtm-single-meta',
			'#buddypress:not(.youzer).bmf-white-popup',
			'.vex.vex-theme-flat-attack .vex-content',
			'.more-articles-button-container img',
			'#buddypress:not(.youzer) #activity-stream.grimlock-buddypress-activity-list .activity-item:after',
			'.buddypress .padder > #buddypress.buddypress-wrap',
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
	public function add_content_background_color_outputs( $outputs ) {
		return array_merge( $outputs, array(
			array(
				'element'  => implode( ',', array(
					'#buddypress:not(.youzer) #profile-content__nav ul li > a span.count',
					'#buddypress:not(.youzer) #activity-stream.grimlock-buddypress-activity-list .activity-item:before',
				) ),
				'property' => 'border-color',
			),
			array(
				'element'     => implode( ',', array(
					'#buddypress:not(.youzer) #profile-content__nav ul li > a span',
				) ),
				'property'    => 'border-color',
				'media_query' => '@media (min-width: 768px)',
			),
			array(
				'element'  => implode( ',', array(
					'.buddypress.directory.members #buddypress .bps_filters .bps-filters-item',
					'#yz-members-directory .bps_filters .bps-filters-item',
				) ),
				'property' => 'color',
			),
		) );
	}
}

return new Grimlock_BuddyPress_Global_Customizer();
