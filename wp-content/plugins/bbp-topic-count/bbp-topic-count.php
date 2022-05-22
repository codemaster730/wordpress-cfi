<?php

/*
Plugin Name: bbP topic count
Plugin URI: http://www.rewweb.co.uk/bbp-topic-count-plugin/
Description: This plugin for bbPress shows any combination of the total topics, replies and post count under the avatar on each topic/reply
view in bbpress, and allows you to label these as you wish eg “Topics Created : 253″ or “Topics – 253″
Version: 2.8
Text Domain: bbp-topic-count
Domain Path: /languages
Author: Robin Wilson
Author URI: http://www.rewweb.co.uk
License: GPL2
*/
/*  Copyright 2013-2018  PLUGIN_AUTHOR_NAME  (email : wilsonrobine@btinternet.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

	

/*******************************************
* global variables
*******************************************/

// load the plugin options
$tc_options = get_option( 'tc_settings' );

if(!defined('TC_PLUGIN_DIR'))
	define('TC_PLUGIN_DIR', dirname(__FILE__));

function bbp_topic_count_init() {
  load_plugin_textdomain('bbp-topic-count', false, basename( dirname( __FILE__ ) ) . '/languages' );
}
add_action('plugins_loaded', 'bbp_topic_count_init');


/*******************************************
* file includes
*******************************************/
include(TC_PLUGIN_DIR . '/includes/settings.php');
include(TC_PLUGIN_DIR . '/includes/display.php');
include(TC_PLUGIN_DIR . '/includes/shortcodes.php');
include(TC_PLUGIN_DIR . '/includes/settings_shortcodes.php');
include(TC_PLUGIN_DIR . '/includes/settings_settings.php');

