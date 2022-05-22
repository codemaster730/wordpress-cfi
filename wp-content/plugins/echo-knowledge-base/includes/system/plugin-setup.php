<?php

/**
 * Activate the plugin
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
*/

/**
 * Activate this plugin i.e. setup tables, data etc.
 * NOT invoked on plugin updates
 *
 * @param bool $network_wide - If the plugin is being network-activated
 */
function epkb_activate_plugin( $network_wide=false ) {
	global $wpdb;

	if ( is_multisite() && $network_wide ) {
		foreach ( $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs LIMIT 100" ) as $blog_id ) {
			switch_to_blog( $blog_id );
			epkb_get_instance()->kb_config_obj->reset_cache();
			epkb_activate_plugin_do();
			restore_current_blog();
		}
	} else {
		epkb_activate_plugin_do();
	}
}

function epkb_activate_plugin_do() {

	// true if the plugin was activated for the first time since installation
	$plugin_version = get_option( 'epkb_version' );
	if ( empty( $plugin_version ) ) {

		set_transient( '_epkb_plugin_installed', true, HOUR_IN_SECONDS );
		set_transient( '_epkb_plugin_installed_today', true, DAY_IN_SECONDS );

		EPKB_Utilities::save_wp_option( 'epkb_run_setup', true, true );

		// prepare KB configuration
		$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( EPKB_KB_Config_DB::DEFAULT_KB_ID );
		epkb_get_instance()->kb_config_obj->update_kb_configuration( EPKB_KB_Config_DB::DEFAULT_KB_ID, $kb_config );

		// update KB versions
		EPKB_Utilities::save_wp_option( 'epkb_version', Echo_Knowledge_Base::$version, true );
		EPKB_Utilities::save_wp_option( 'epkb_version_first', Echo_Knowledge_Base::$version, true );
	}

	set_transient( '_epkb_plugin_activated', true, HOUR_IN_SECONDS );

	// Clear permalinks
	update_option( 'epkb_flush_rewrite_rules', true );
	flush_rewrite_rules( false );
}
register_activation_hook( Echo_Knowledge_Base::$plugin_file, 'epkb_activate_plugin' );

/**
 * User deactivates this plugin so refresh the permalinks
 */
function epkb_deactivation() {

	// Clear the permalinks to remove our post type's rules
	flush_rewrite_rules( false );

}
register_deactivation_hook( Echo_Knowledge_Base::$plugin_file, 'epkb_deactivation' );
