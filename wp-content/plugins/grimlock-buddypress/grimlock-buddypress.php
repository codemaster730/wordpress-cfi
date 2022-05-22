<?php
/*
 * Plugin name: Grimlock for BuddyPress
 * Plugin URI:  http://www.themosaurus.com
 * Description: Adds integration features for Grimlock and BuddyPress.
 * Author:      Themosaurus
 * Author URI:  http://www.themosaurus.com
 * Version:     1.4.9
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: grimlock-buddypress
 * Domain Path: /languages
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'GRIMLOCK_BUDDYPRESS_VERSION',              '1.4.9'  );
define( 'GRIMLOCK_BUDDYPRESS_MIN_GRIMLOCK_VERSION', '1.3.8'   );
define( 'GRIMLOCK_BUDDYPRESS_PLUGIN_FILE',           __FILE__ );
define( 'GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_PATH',      plugin_dir_path( __FILE__ ) );
define( 'GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_URL',       plugin_dir_url( __FILE__ ) );

// Initialize update checker
require 'libs/plugin-update-checker/plugin-update-checker.php';
Puc_v4_Factory::buildUpdateChecker(
	'http://files.themosaurus.com/grimlock-buddypress/version.json',
	__FILE__,
	'grimlock-buddypress'
);

/**
 * Display notice if Grimlock version doesn't match minimum requirement
 */
function grimlock_buddypress_dependency_notice() {
	$url = self_admin_url( 'update-core.php?action=do-plugin-upgrade&plugins=' ) . urlencode( plugin_basename( GRIMLOCK_PLUGIN_FILE ) );
	$url = wp_nonce_url( $url, 'upgrade-core' ); ?>
	<div class="notice notice-error">
		<p><?php printf( esc_html__( '%1$sGrimlock for BuddyPress%2$s requires %1$sGrimlock %3$s%2$s to continue running properly. Please %4$supdate Grimlock.%5$s', 'grimlock-buddypress' ), '<strong>', '</strong>', GRIMLOCK_BUDDYPRESS_MIN_GRIMLOCK_VERSION, '<a href="' . esc_url( $url ) . '">', "</a>" ); ?></p>
	</div>
	<?php
}

/**
 * Load plugin.
 */
function grimlock_buddypress_loaded() {
	if ( version_compare( GRIMLOCK_VERSION, GRIMLOCK_BUDDYPRESS_MIN_GRIMLOCK_VERSION, '<' ) ) {
		add_action( 'admin_notices', 'grimlock_buddypress_dependency_notice' );
		return;
	}

	if ( function_exists( 'buddypress' ) ) {
		require_once 'inc/class-grimlock-buddypress.php';

		global $grimlock_buddypress;
		$grimlock_buddypress = new Grimlock_BuddyPress();

		require_once 'inc/grimlock-buddypress-template-functions.php';
		require_once 'inc/grimlock-buddypress-template-hooks.php';

		do_action( 'grimlock_buddypress_loaded' );
	}
}
add_action( 'grimlock_loaded', 'grimlock_buddypress_loaded' );

/**
 * Load Youzer add on
 */
function grimlock_buddypress_youzer_loaded() {
	if ( class_exists( 'Youzer' ) ) {
		require_once 'inc/youzer/class-grimlock-buddypress-youzer.php';
		global $grimlock_buddypress_youzer;
		$grimlock_buddypress_youzer = new Grimlock_BuddyPress_Youzer();

		require_once 'inc/youzer/grimlock-buddypress-youzer-template-hooks.php';
		require_once 'inc/youzer/grimlock-buddypress-youzer-template-functions.php';
	}
}
add_action( 'grimlock_buddypress_loaded', 'grimlock_buddypress_youzer_loaded' );

/**
 * Load Youzify add on
 */
function grimlock_buddypress_youzify_loaded() {
	if ( class_exists( 'Youzify' ) ) {
		require_once 'inc/youzify/class-grimlock-buddypress-youzify.php';
		global $grimlock_buddypress_youzify;
		$grimlock_buddypress_youzify = new Grimlock_BuddyPress_Youzify();

		require_once 'inc/youzify/grimlock-buddypress-youzify-template-hooks.php';
		require_once 'inc/youzify/grimlock-buddypress-youzify-template-functions.php';
	}
}
add_action( 'grimlock_buddypress_loaded', 'grimlock_buddypress_youzify_loaded' );

/**
 * Load BP Profile Search add on
 */
function grimlock_buddypress_bp_profile_search_loaded() {
	if ( function_exists( 'bps_templates' ) ) {
		require_once 'inc/bp-profile-search/class-grimlock-buddypress-bp-profile-search.php';
		global $grimlock_buddypress_bp_profile_search;
		$grimlock_buddypress_bp_profile_search = new Grimlock_BuddyPress_BP_Profile_Search();

		require_once 'inc/bp-profile-search/grimlock-buddypress-bp-profile-search-template-functions.php';
	}
}
add_action( 'grimlock_buddypress_loaded', 'grimlock_buddypress_bp_profile_search_loaded' );

/**
 * Load BP Member Swipe
 */
function grimlock_buddypress_bp_member_swipe_loaded() {
	if ( class_exists( 'BP_Member_Swipe' ) ) {
		require_once 'inc/bp-member-swipe/grimlock-buddypress-bp-member-swipe-template-hooks.php';
		require_once 'inc/bp-member-swipe/grimlock-buddypress-bp-member-swipe-template-functions.php';
	}
}
add_action( 'grimlock_buddypress_loaded', 'grimlock_buddypress_bp_member_swipe_loaded' );

/**
 * Load RTMedia add on
 */
function grimlock_buddypress_buddypress_media_loaded() {
	if ( class_exists( 'RTMedia' ) ) {
		require_once 'inc/buddypress-media/class-grimlock-buddypress-buddypress-media.php';
		global $grimlock_buddypress_buddypress_media;
		$grimlock_buddypress_buddypress_media = new Grimlock_BuddyPress_BuddyPress_Media();

		require_once 'inc/buddypress-media/grimlock-buddypress-buddypress-media-template-hooks.php';
		require_once 'inc/buddypress-media/grimlock-buddypress-buddypress-media-template-functions.php';
	}
}
add_action( 'grimlock_buddypress_loaded', 'grimlock_buddypress_buddypress_media_loaded' );

/**
 * Load BP Better Messages add on
 */
function grimlock_buddypress_bp_better_messages_loaded() {
	if ( class_exists( 'BP_Better_Messages' ) ) {
		require_once 'inc/bp-better-messages/class-grimlock-buddypress-bp-better-messages.php';
		global $grimlock_buddypress_bp_better_messages;
		$grimlock_buddypress_bp_better_messages = new Grimlock_BuddyPress_BP_Better_Messages();

		require_once 'inc/bp-better-messages/grimlock-buddypress-bp-better-messages-template-hooks.php';
	}
}
add_action( 'grimlock_buddypress_loaded', 'grimlock_buddypress_bp_better_messages_loaded' );

/**
 * Load BP Better Messages add on
 */
function grimlock_buddypress_buddypress_docs_loaded() {
	if ( class_exists( 'BP_Docs' ) ) {
		require_once 'inc/buddypress-docs/class-grimlock-buddypress-buddypress-docs.php';
		global $grimlock_buddypress_buddypress_docs;
		$grimlock_buddypress_buddypress_docs = new Grimlock_BuddyPress_BuddyPress_Docs();
	}
}
add_action( 'grimlock_buddypress_loaded', 'grimlock_buddypress_buddypress_docs_loaded' );

/**
 * Load BP Maps for members add on
 */
function grimlock_buddypress_bp_maps_for_members_loaded() {
	if ( function_exists( 'pp_mm_init' ) ) {
		require_once 'inc/bp-maps-for-members/class-grimlock-buddypress-bp-maps-for-members.php';
		global $grimlock_buddypress_bp_maps_for_members;
		$grimlock_buddypress_bp_maps_for_members = new Grimlock_BuddyPress_BP_Maps_For_Members();

		require_once 'inc/bp-maps-for-members/grimlock-buddypress-bp-maps-for-members-template-functions.php';
	}
}
add_action( 'grimlock_buddypress_loaded', 'grimlock_buddypress_bp_maps_for_members_loaded' );

/**
 * Load Social Articles add on
 */
function grimlock_buddypress_social_articles_loaded() {
	if ( class_exists( 'SocialArticles' ) ) {
		require_once 'inc/social-articles/class-grimlock-buddypress-social-articles.php';
		global $grimlock_buddypress_social_articles;
		$grimlock_buddypress_social_articles = new Grimlock_BuddyPress_Social_Articles();
	}
}
add_action( 'grimlock_buddypress_loaded', 'grimlock_buddypress_social_articles_loaded' );

/**
 * Load BP Featured Members add on
 */
function grimlock_buddypress_bp_featured_members_loaded() {
	if ( class_exists( 'BP_Featured_Members' ) ) {
		require_once 'inc/bp-featured-members/class-grimlock-buddypress-bp-featured-members.php';
		global $grimlock_buddypress_bp_featured_members;
		$grimlock_buddypress_bp_featured_members = new Grimlock_BuddyPress_BP_Featured_Members();
	}
}
add_action( 'grimlock_buddypress_loaded', 'grimlock_buddypress_bp_featured_members_loaded' );

/**
 * Load BuddyPress Global Search add on
 */
function grimlock_buddypress_buddypress_global_search_loaded() {
	if ( function_exists( 'buddyboss_global_search_init' ) ) {
		require_once 'inc/buddypress-global-search/class-grimlock-buddypress-buddypress-global-search.php';
		global $grimlock_buddypress_buddypress_global_search;
		$grimlock_buddypress_buddypress_global_search = new Grimlock_BuddyPress_BuddyPress_Global_Search();
	}
}
add_action( 'grimlock_buddypress_loaded', 'grimlock_buddypress_buddypress_global_search_loaded' );

/**
 * Load Favorites add on
 */
function grimlock_buddypress_favorites_loaded() {
	if ( class_exists( 'Favorites' ) ) {
		require_once 'inc/favorites/class-grimlock-buddypress-favorites.php';
		global $grimlock_buddypress_favorites;
		$grimlock_buddypress_favorites = new Grimlock_BuddyPress_Favorites();
	}
}
add_action( 'grimlock_buddypress_loaded', 'grimlock_buddypress_favorites_loaded' );

/**
 * Load Charitable add on
 */
function grimlock_buddypress_charitable_loaded() {
	if ( class_exists( 'Charitable' ) ) {
		require_once 'inc/charitable/class-grimlock-buddypress-charitable.php';
		global $grimlock_buddypress_charitable;
		$grimlock_buddypress_charitable = new Grimlock_BuddyPress_Charitable();
	}
}
add_action( 'grimlock_buddypress_loaded', 'grimlock_buddypress_charitable_loaded' );

/**
 * Load BuddyPress Activity Shortcode add on
 */
function grimlock_buddypress_bp_activity_shortcode_loaded() {
	if ( class_exists( 'BD_Activity_Stream_Shortcodes_Helper' ) ) {
		require_once 'inc/bp-activity-shortcode/class-grimlock-buddypress-bp-activity-shortcode.php';
		global $grimlock_buddypress_bp_activity_shortcode;
		$grimlock_buddypress_bp_activity_shortcode = new Grimlock_BuddyPress_BP_Activity_Shortcode();
	}
}
add_action( 'grimlock_buddypress_loaded', 'grimlock_buddypress_bp_activity_shortcode_loaded' );

/**
 * Load BuddyPress Create Group Type add on
 */
function grimlock_buddypress_bp_create_group_type_loaded() {
	if ( class_exists( 'Bp_Add_Group_Types' ) ) {
		require_once 'inc/bp-create-group-type/class-grimlock-buddypress-bp-create-group-type.php';
		global $grimlock_buddypress_bp_create_group_type;
		$grimlock_buddypress_bp_create_group_type = new Grimlock_BuddyPress_BP_Create_Group_Type();
	}
}
add_action( 'grimlock_buddypress_loaded', 'grimlock_buddypress_bp_create_group_type_loaded' );

/**
 * Load BP Verified Member add on
 */
function grimlock_buddypress_bp_verified_member_loaded() {
	if ( class_exists( 'BP_Verified_Member' ) ) {
		require_once 'inc/bp-verified-member/class-grimlock-buddypress-bp-verified-member.php';
		global $grimlock_buddypress_bp_verified_member;
		$grimlock_buddypress_bp_verified_member = new Grimlock_BuddyPress_BP_Verified_Member();
	}
}
add_action( 'grimlock_buddypress_loaded', 'grimlock_buddypress_bp_verified_member_loaded' );

/**
 * Force page template selection for BP Pages.
 *
 * @since 1.0.0
 */
function grimlock_buddypress_force_page_template() {
	$page_template = 'template-classic-12-cols-left.php';
	if ( function_exists( 'bp_core_get_directory_page_ids' ) && '' !== locate_template( $page_template ) ) {
		$page_ids = bp_core_get_directory_page_ids();
		foreach( $page_ids as $page_id ) {
			update_post_meta( $page_id, '_wp_page_template', $page_template );
		}
	}
}

register_activation_hook( __FILE__, 'grimlock_buddypress_force_page_template' );
add_action( 'update_option_bp-pages', 'grimlock_buddypress_force_page_template', 10 );
