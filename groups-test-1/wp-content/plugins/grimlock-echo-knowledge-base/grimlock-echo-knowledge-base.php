<?php
/*
 * Plugin name: Grimlock for Knowledge Base for Documents and FAQs
 * Plugin URI:  http://www.themosaurus.com
 * Description: Adds integration features for Grimlock and Knowledge Base for Documents and FAQs.
 * Author:      Themosaurus
 * Author URI:  http://www.themosaurus.com
 * Version:     1.0.6
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: grimlock-echo-knowledge-base
 * Domain Path: /languages
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'GRIMLOCK_ECHO_KNOWLEDGE_BASE_VERSION',         '1.0.6' );
define( 'GRIMLOCK_ECHO_KNOWLEDGE_BASE_PLUGIN_FILE',     __FILE__ );
define( 'GRIMLOCK_ECHO_KNOWLEDGE_BASE_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'GRIMLOCK_ECHO_KNOWLEDGE_BASE_PLUGIN_DIR_URL',  plugin_dir_url( __FILE__ ) );

// Initialize update checker
require 'libs/plugin-update-checker/plugin-update-checker.php';
Puc_v4_Factory::buildUpdateChecker(
	'http://files.themosaurus.com/grimlock-echo-knowledge-base/version.json',
	__FILE__,
	'grimlock-echo-knowledge-base'
);

/**
 * Load plugin.
 */
function grimlock_echo_knowledge_base_loaded() {
	require_once 'inc/class-grimlock-echo-knowledge-base.php';

	global $grimlock_echo_knowledge_base;
	$grimlock_echo_knowledge_base = new Grimlock_Echo_Knowledge_Base();

	do_action( 'grimlock_echo_knowledge_base_loaded' );
}
add_action( 'grimlock_loaded', 'grimlock_echo_knowledge_base_loaded' );
