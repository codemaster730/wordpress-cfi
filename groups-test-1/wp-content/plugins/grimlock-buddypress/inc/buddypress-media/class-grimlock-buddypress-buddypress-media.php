<?php
/**
 * Grimlock_BuddyPress_BuddyPress_Media Class
 *
 * @package  grimlock-buddypress
 * @author   Themosaurus
 * @since    1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The RTMedia integration class
 */
class Grimlock_BuddyPress_BuddyPress_Media {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'rtmedia_located_template', array( $this, 'locate_template' ), 100, 4 );

		add_filter( 'bp_get_activity_show_filters_options', array( $this, 'add_media_show_filter' ), 10, 2 );

		add_action( 'bp_init', array( $this, 'edit_media_nav_tab' ), 20, 1 );
	}

	/**
	 * Try to locate RTMedia templates in Grimlock BuddyPress
	 *
	 * @param string $template The template path
	 * @param bool $is_url Whether the path should be absolute path or url
	 * @param string $ogpath The template directory containing the original templates in RTMedia
	 * @param string $template_name The name of the template file that needs to be returned
	 *
	 * @return string
	 */
	public function locate_template( $template, $is_url, $ogpath, $template_name ) {
		if ( file_exists( GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_PATH . 'templates/rtmedia/' . $template_name ) ) {
			if ( $is_url ) {
				$template = GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_URL . 'templates/rtmedia/' . $template_name;
			}
			else {
				$template = GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_PATH . 'templates/rtmedia/' . $template_name;
			}
		}

		return $template;
	}

	/**
	 * Change name of rtMedia updates for the BP activity feed filters.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $filters Array of filter options for the given context, in the following format: $option_value => $option_name.
	 * @param string $context Context for the filter. 'activity', 'member', 'member_groups', 'group'.
	 *
	 * @return array          Updated array of filter options for the given context.
	 */
	public function add_media_show_filter( $filters, $context ) {
		$filters['rtmedia_update'] = 'Media Updates';
		return $filters;
	}

	/**
	 * Edit the "Media" nav tab
	 */
	public function edit_media_nav_tab() {
		$media_tab_slug = apply_filters( 'rtmedia_media_tab_slug', RTMEDIA_MEDIA_SLUG );
		if ( ! empty( buddypress()->members->nav->get( $media_tab_slug ) ) ) {
			$count = get_media_counts();

			// Remove the media count if it is 0
			if ( $count['total']['all'] <= 0 ) {
				buddypress()->members->nav->edit_nav(
					array( 'name' => apply_filters( 'rtmedia_media_tab_name', RTMEDIA_MEDIA_LABEL ) ),
					$media_tab_slug
				);
			}
		}

		if ( bp_is_group() ) {
			$media_tab_slug = apply_filters( 'rtmedia_group_media_tab_slug', RTMEDIA_MEDIA_SLUG );
			if ( ! empty( buddypress()->groups->nav->get_secondary( array( 'parent_slug' => bp_get_current_group_slug(), 'slug' => $media_tab_slug ) ) ) ) {
				$media_nav = new RTMediaNav( false );
				$count     = $media_nav->actual_counts( bp_get_current_group_id(), 'group' );

				// Remove the media count if it is 0
				if ( empty( $count ) || $count['total']['all'] <= 0 ) {
					buddypress()->groups->nav->edit_nav(
						array( 'name' => apply_filters( 'rtmedia_media_tab_name', RTMEDIA_MEDIA_LABEL ) ),
						$media_tab_slug,
						bp_get_current_group_slug()
					);
				}
			}
		}
	}
}

return new Grimlock_BuddyPress_BuddyPress_Media();
