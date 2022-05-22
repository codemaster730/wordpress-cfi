<?php
/*
 * Plugin name: Grimlock
 * Plugin URI:  http://www.themosaurus.com
 * Description: Provides components for the theme. Extends Customizer using Kirki Toolkit to modify components.
 * Author:      Themosaurus
 * Author URI:  http://www.themosaurus.com/grimlock
 * Version:     1.5.2
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: grimlock
 * Domain Path: /languages
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'GRIMLOCK_VERSION',                                '1.5.2'                             );
define( 'GRIMLOCK_PLUGIN_FILE',                            __FILE__                            );
define( 'GRIMLOCK_PLUGIN_DIR_PATH',                        plugin_dir_path( __FILE__ )         );
define( 'GRIMLOCK_PLUGIN_DIR_URL',                         plugin_dir_url( __FILE__ )          );

// Colors
define( 'GRIMLOCK_GRAY_DARK',                             '#373a3c'                            );
define( 'GRIMLOCK_GRAY',                                  '#55595c'                            );
define( 'GRIMLOCK_GRAY_LIGHT',                            '#818a91'                            );
define( 'GRIMLOCK_GRAY_LIGHTER',                          '#eceeef'                            );
define( 'GRIMLOCK_GRAY_LIGHTEST',                         '#f7f7f7'                            );

define( 'GRIMLOCK_BRAND_PRIMARY',                         '#0275d8'                            );

define( 'GRIMLOCK_BORDER_COLOR',                          GRIMLOCK_GRAY_LIGHTER                );

define( 'GRIMLOCK_BODY_COLOR',                            GRIMLOCK_GRAY_DARK                   );

define( 'GRIMLOCK_LINK_COLOR',                            GRIMLOCK_BRAND_PRIMARY               );
define( 'GRIMLOCK_LINK_HOVER_COLOR',                      '#014c8c'                            );

define( 'GRIMLOCK_NAVIGATION_ITEM_COLOR',                 'rgba(0,0,0,0.5)'                    );

define( 'GRIMLOCK_BUTTON_PRIMARY_COLOR',                  '#ffffff'                            );
define( 'GRIMLOCK_BUTTON_PRIMARY_BACKGROUND_COLOR',       GRIMLOCK_BRAND_PRIMARY               );
define( 'GRIMLOCK_BUTTON_PRIMARY_BORDER_COLOR',           GRIMLOCK_BRAND_PRIMARY               );
define( 'GRIMLOCK_BUTTON_PRIMARY_HOVER_COLOR',            GRIMLOCK_BUTTON_PRIMARY_COLOR        );
define( 'GRIMLOCK_BUTTON_PRIMARY_HOVER_BACKGROUND_COLOR', '#014682'                            );
define( 'GRIMLOCK_BUTTON_PRIMARY_HOVER_BORDER_COLOR',     '#01315a'                            );

define( 'GRIMLOCK_SECTION_BACKGROUND_COLOR',              'rgba(236,238,239,0.8)'              );

// Typography
define( 'GRIMLOCK_FONT_FAMILY_SANS_SERIF',                'sans-serif'                         );
define( 'GRIMLOCK_FONT_FAMILY_SERIF',                     'serif'                              );
define( 'GRIMLOCK_FONT_SIZE',                             '1rem'                               );
define( 'GRIMLOCK_LETTER_SPACING',                        '0px'                                );
define( 'GRIMLOCK_LINE_HEIGHT',                           '1.5'                                );

define( 'GRIMLOCK_HEADINGS_LINE_HEIGHT',                  '1.1'                                );

define( 'GRIMLOCK_HEADING1_FONT_SIZE',                    '2.5rem'                             );
define( 'GRIMLOCK_HEADING2_FONT_SIZE',                    '2rem'                               );
define( 'GRIMLOCK_HEADING3_FONT_SIZE',                    '1.75rem'                            );
define( 'GRIMLOCK_HEADING4_FONT_SIZE',                    '1.5rem'                             );
define( 'GRIMLOCK_HEADING5_FONT_SIZE',                    '1.25rem'                            );
define( 'GRIMLOCK_HEADING6_FONT_SIZE',                    '1rem'                               );

define( 'GRIMLOCK_DISPLAY_HEADING1_FONT_SIZE',            '6rem'                               );
define( 'GRIMLOCK_DISPLAY_HEADING2_FONT_SIZE',            '5.5rem'                             );
define( 'GRIMLOCK_DISPLAY_HEADING3_FONT_SIZE',            '4.5rem'                             );
define( 'GRIMLOCK_DISPLAY_HEADING4_FONT_SIZE',            '3.5rem'                             );

define( 'GRIMLOCK_LEAD_FONT_SIZE',                        '1.25rem'                            );

// Components
define( 'GRIMLOCK_BORDER_WIDTH',                          1                                    ); // px
define( 'GRIMLOCK_BORDER_RADIUS',                         .25                                  ); // rem
define( 'GRIMLOCK_BOX_SHADOW_X_OFFSET',                   0                                    ); // px
define( 'GRIMLOCK_BOX_SHADOW_Y_OFFSET',                   0                                    ); // px
define( 'GRIMLOCK_BOX_SHADOW_BLUR_RADIUS',                10                                   ); // px
define( 'GRIMLOCK_BOX_SHADOW_SPREAD_RADIUS',              0                                    ); // px
define( 'GRIMLOCK_BOX_SHADOW_COLOR',                      'rgba(0,0,0,0)'                      );

// Paddings
define( 'GRIMLOCK_CONTENT_PADDING_Y',                     4                                    ); // %
define( 'GRIMLOCK_SECTION_PADDING_Y',                     GRIMLOCK_CONTENT_PADDING_Y           ); // %

// Initialize update checker
require 'libs/plugin-update-checker/plugin-update-checker.php';
Puc_v4_Factory::buildUpdateChecker(
	'http://files.themosaurus.com/grimlock/version.json',
	__FILE__,
	'grimlock'
);

/**
 * Require and recommend plugins with Grimlock
 */
function grimlock_load_tgm_plugin_activation() {
	require 'libs/tgm-plugin-activation/class-tgm-plugin-activation.php';
	global $grimlock_tgm_plugin_activation;
	$grimlock_tgm_plugin_activation = require 'inc/class-grimlock-tgm-plugin-activation.php';
}
add_action( 'after_setup_theme', 'grimlock_load_tgm_plugin_activation' );

/**
 * Load plugin.
 */
function grimlock_loaded() {
	require_once 'inc/class-grimlock.php';

	global $grimlock;
	$grimlock = new Grimlock();

	do_action( 'grimlock_loaded' );
}
add_action( 'plugins_loaded', 'grimlock_loaded' );
