<?php
/*
	Plugin Name: Maintenance
	Plugin URI: https://wpmaintenancemode.com/
	Description: Put your site in maintenance mode, away from the public view. Use maintenance plugin if your website is in development or you need to change a few things, run an upgrade. Make it only accessible to logged in users.
	Version: 4.06
	Author: WebFactory Ltd
	Author URI: https://www.webfactoryltd.com/
	License: GPL2

  Copyright 2013-2022  WebFactory Ltd  (email : support@webfactoryltd.com)

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
*/

class MTNC
{
  public function __construct()
  {
    global $mtnc_variable;
    $mtnc_variable = new stdClass();

    add_action('plugins_loaded', array(&$this, 'mtnc_constants'), 1);
    add_action('plugins_loaded', array(&$this, 'mtnc_lang'), 2);
    add_action('plugins_loaded', array(&$this, 'mtnc_includes'), 3);
    add_action('plugins_loaded', array(&$this, 'mtnc_admin'), 4);

    register_activation_hook(__FILE__, array(&$this, 'mtnc_activation'));
    register_deactivation_hook(__FILE__, array(&$this, 'mtnc_deactivation'));

    add_action('template_include', array(&$this, 'mtnc_template_include'), 999999);
    add_action('do_feed_rdf', array(&$this, 'disable_feed'), 0, 1);
    add_action('do_feed_rss', array(&$this, 'disable_feed'), 0, 1);
    add_action('do_feed_rss2', array(&$this, 'disable_feed'), 0, 1);
    add_action('do_feed_atom', array(&$this, 'disable_feed'), 0, 1);
    add_action('wp_logout', array(&$this, 'mtnc_user_logout'));
    add_action('init', array(&$this, 'mtnc_admin_bar'));
    add_action('init', array(&$this, 'mtnc_set_global_options'), 1);
    add_filter('admin_footer_text', array(&$this, 'admin_footer_text'), 10, 1);

    add_action('admin_action_mtnc_install_wpfssl', array(&$this, 'install_wpfssl'));

    add_filter(
      'plugin_action_links_' . plugin_basename(__FILE__),
      array(&$this, 'plugin_action_links')
    );
  }

  function admin_footer_text($text_org)
  {
    if (false === mtnc_is_plugin_page()) {
      return $text_org;
    }

    $text = '<i><a target="_blank" href="https://wpmaintenancemode.com/?ref=mtnc-free">WP Maintenance</a> v' . MTNC_VERSION . ' by <a href="https://www.webfactoryltd.com/" title="' . __('Visit our site to get more great plugins', 'maintenance') . '" target="_blank">WebFactory Ltd</a>.';
    $text .= ' Please <a target="_blank" href="https://wordpress.org/support/plugin/maintenance/reviews/#new-post" title="' . __('Rate the plugin', 'maintenance') . '">' . __('Rate the plugin ★★★★★', 'maintenance') . '</a>.</i> ';
    return $text;
  } // admin_footer_text

  // add settings link to plugins page
  function plugin_action_links($links)
  {
    $settings_link = '<a href="' . admin_url('admin.php?page=maintenance') . '" title="' . __('Maintenance Settings', 'maintenance') . '">' . __('Settings', 'maintenance') . '</a>';
    $pro_link = '<a href="' . admin_url('admin.php?page=maintenance#open-pro-dialog') . '" title="' . __('Get PRO', 'maintenance') . '"><b>' . __('Get PRO', 'maintenance') . '</b></a>';

    array_unshift($links, $pro_link);
    array_unshift($links, $settings_link);

    return $links;
  } // plugin_action_links

  function pro_dialog()
  {
    $out = '';

    $out .= '<div id="mtnc-pro-dialog" style="display: none;" title="WP Maintenance PRO is here!"><span class="ui-helper-hidden-accessible"><input type="text"/></span>';

    $out .= '<div class="center logo"><a href="https://wpmaintenancemode.com/?ref=mtnc-free-pricing-table" target="_blank"><img src="' . MTNC_URI . 'images/wp-maintenance-logo.png' . '" alt="WP Maintenance PRO" title="WP Maintenance PRO"></a><br>';

    $out .= '<span>Limited PRO Launch Discount - <b>all prices are LIFETIME</b>! Pay once &amp; use forever!</span>';
    $out .= '</div>';

    $out .= '<table id="mtnc-pro-table">';
    $out .= '<tr>';
    $out .= '<td class="center">Lifetime Personal License</td>';
    $out .= '<td class="center">Lifetime Team License</td>';
    $out .= '<td class="center">Lifetime Agency License</td>';
    $out .= '</tr>';

    $out .= '<tr class="prices">';
    $out .= '<td class="center"><del>$49 /year</del><br><span>$59</span> <b>/lifetime</b></td>';
    $out .= '<td class="center"><del>$89 /year</del><br><span>$69</span> <b>/lifetime</b></td>';
    $out .= '<td class="center"><del>$199 /year</del><br><span>$119</span> <b>/lifetime</b></td>';
    $out .= '</tr>';

    $out .= '<tr>';
    $out .= '<td><span class="dashicons dashicons-yes"></span><b>1 Site License</b></td>';
    $out .= '<td><span class="dashicons dashicons-yes"></span><b>5 Sites License</b></td>';
    $out .= '<td><span class="dashicons dashicons-yes"></span><b>100 Sites License</b></td>';
    $out .= '</tr>';

    $out .= '<tr>';
    $out .= '<td><span class="dashicons dashicons-yes"></span>All Plugin Features</td>';
    $out .= '<td><span class="dashicons dashicons-yes"></span>All Plugin Features</td>';
    $out .= '<td><span class="dashicons dashicons-yes"></span>All Plugin Features</td>';
    $out .= '</tr>';

    $out .= '<tr>';
    $out .= '<td><span class="dashicons dashicons-yes"></span>Lifetime Updates &amp; Support</td>';
    $out .= '<td><span class="dashicons dashicons-yes"></span>Lifetime Updates &amp; Support</td>';
    $out .= '<td><span class="dashicons dashicons-yes"></span>Lifetime Updates &amp; Support</td>';
    $out .= '</tr>';

    $out .= '<tr>';
    $out .= '<td><span class="dashicons dashicons-yes"></span>+20 Themes</td>';
    $out .= '<td><span class="dashicons dashicons-yes"></span>+20 Themes</td>';
    $out .= '<td><span class="dashicons dashicons-yes"></span>+20 Themes</td>';
    $out .= '</tr>';

    $out .= '<tr>';
    $out .= '<td><span class="dashicons dashicons-yes"></span>+3.7 Million HD Images</td>';
    $out .= '<td><span class="dashicons dashicons-yes"></span>+3.7 Million HD Images</td>';
    $out .= '<td><span class="dashicons dashicons-yes"></span>+3.7 Million HD Images</td>';
    $out .= '</tr>';

    $out .= '<tr>';
    $out .= '<td><span class="dashicons dashicons-yes"></span>5 new themes each month guaranteed</td>';
    $out .= '<td><span class="dashicons dashicons-yes"></span>5 new themes each month guaranteed</td>';
    $out .= '<td><span class="dashicons dashicons-yes"></span>5 new themes each month guaranteed</td>';
    $out .= '</tr>';

    $out .= '<tr>';
    $out .= '<td><span class="dashicons dashicons-no"></span>Licenses &amp; Sites Manager</td>';
    $out .= '<td><span class="dashicons dashicons-yes"></span>Licenses &amp; Sites Manager</td>';
    $out .= '<td><span class="dashicons dashicons-yes"></span>Licenses &amp; Sites Manager</td>';
    $out .= '</tr>';

    $out .= '<tr>';
    $out .= '<td><span class="dashicons dashicons-no"></span>White-label Mode</td>';
    $out .= '<td><span class="dashicons dashicons-yes"></span>White-label Mode</td>';
    $out .= '<td><span class="dashicons dashicons-yes"></span>White-label Mode</td>';
    $out .= '</tr>';

    $out .= '<tr>';
    $out .= '<td><span class="dashicons dashicons-no"></span>Full Plugin Rebranding</td>';
    $out .= '<td><span class="dashicons dashicons-no"></span>Full Plugin Rebranding</td>';
    $out .= '<td><span class="dashicons dashicons-yes"></span>Full Plugin Rebranding</td>';
    $out .= '</tr>';

    $out .= '<tr>';
    $out .= '<td><a class="button button-buy" data-href-org="https://wpmaintenancemode.com/buy/?product=personal-launch&ref=pricing-table" href="https://wpmaintenancemode.com/buy/?product=personal-launch&ref=pricing-table" target="_blank">Lifetime License<br>$59 -&gt; BUY NOW</a>
    <br>or <a class="button-buy" data-href-org="https://wpmaintenancemode.com/buy/?product=personal-monthly&ref=pricing-table" href="https://wpmaintenancemode.com/buy/?product=personal-monthly&ref=pricing-table" target="_blank">only $7.99 <small>/month</small></a></td>';
    $out .= '<td><a class="button button-buy" data-href-org="https://wpmaintenancemode.com/buy/?product=team-launch&ref=pricing-table" href="https://wpmaintenancemode.com/buy/?product=team-launch&ref=pricing-table" target="_blank">Lifetime License<br>$69 -&gt; BUY NOW</a></td>';
    $out .= '<td><a class="button button-buy" data-href-org="https://wpmaintenancemode.com/buy/?product=agency-launch&ref=pricing-table" href="https://wpmaintenancemode.com/buy/?product=agency-launch&ref=pricing-table" target="_blank">Lifetime License<br>$119 -&gt; BUY NOW</a></td>';
    $out .= '</tr>';

    $out .= '</table>';

    $out .= '<div class="center footer"><b>100% No-Risk Money Back Guarantee!</b> If you don\'t like the plugin over the next 7 days, we will happily refund 100% of your money. No questions asked! Payments are processed by our merchant of records - <a href="https://paddle.com/" target="_blank">Paddle</a>.</div></div>';

    return $out;
  } // pro_dialog

  public function mtnc_constants()
  {
    define('MTNC_VERSION', '4.06');
    define('MTNC_DB_VERSION', 2);
    define('MTNC_WP_VERSION', get_bloginfo('version'));
    define('MTNC_DIR', trailingslashit(plugin_dir_path(__FILE__)));
    define('MTNC_URI', trailingslashit(plugin_dir_url(__FILE__)));
    define('MTNC_INCLUDES', MTNC_DIR . trailingslashit('includes'));
    define('MTNC_LOAD', MTNC_DIR . trailingslashit('load'));
  }

  public function mtnc_set_global_options()
  {
    global $mt_options;
    $mt_options = mtnc_get_plugin_options(true);
  }

  public function mtnc_lang()
  {
    load_plugin_textdomain('maintenance');
  }

  public function mtnc_includes()
  {
    require_once MTNC_INCLUDES . 'functions.php';
    require_once MTNC_INCLUDES . 'update.php';
    require_once MTNC_DIR . 'load/functions.php';

    require_once dirname(__FILE__) . '/wf-flyout/wf-flyout.php';
    new wf_flyout(__FILE__);
  }

  public function mtnc_admin()
  {
    if (is_admin()) {
      require_once MTNC_INCLUDES . 'admin.php';
    }
  }

  public function mtnc_activation()
  {
    self::mtnc_clear_cache();
  }

  public function mtnc_deactivation()
  {
    self::mtnc_clear_cache();
  }

  public static function mtnc_clear_cache()
  {
    wp_cache_flush();
    if (function_exists('w3tc_pgcache_flush')) {
      w3tc_pgcache_flush();
    }
    if (function_exists('wp_cache_clear_cache')) {
      wp_cache_clear_cache();
    }
    if (method_exists('LiteSpeed_Cache_API', 'purge_all')) {
      LiteSpeed_Cache_API::purge_all();
    }
    do_action('litespeed_purge_all');
    if (class_exists('Endurance_Page_Cache')) {
      $epc = new Endurance_Page_Cache;
      $epc->purge_all();
    }
    if (class_exists('SG_CachePress_Supercacher') && method_exists('SG_CachePress_Supercacher', 'purge_cache')) {
      SG_CachePress_Supercacher::purge_cache(true);
    }
    if (class_exists('SiteGround_Optimizer\Supercacher\Supercacher')) {
      SiteGround_Optimizer\Supercacher\Supercacher::purge_cache();
    }
    if (isset($GLOBALS['wp_fastest_cache']) && method_exists($GLOBALS['wp_fastest_cache'], 'deleteCache')) {
      $GLOBALS['wp_fastest_cache']->deleteCache(true);
    }
    if (is_callable('wpfc_clear_all_cache')) {
      wpfc_clear_all_cache(true);
    }
    if (is_callable(array('Swift_Performance_Cache', 'clear_all_cache'))) {
      Swift_Performance_Cache::clear_all_cache();
    }
    if (is_callable(array('Hummingbird\WP_Hummingbird', 'flush_cache'))) {
      Hummingbird\WP_Hummingbird::flush_cache(true, false);
    }
    if (function_exists('rocket_clean_domain')) {
      rocket_clean_domain();
    }
    do_action('cache_enabler_clear_complete_cache');
  }

  public function mtnc_user_logout()
  {
    wp_safe_redirect(get_bloginfo('url'));
    exit;
  }

  public function disable_feed()
  {
    global $mt_options;

    if (!is_user_logged_in() && !empty($mt_options['state'])) {
      nocache_headers();
      echo '<?xml version="1.0" encoding="UTF-8" ?><status>Service unavailable.</status>';
      exit;
    }
  }

  public function mtnc_template_include($original_template)
  {
    $original_template = mtnc_load_maintenance_page($original_template);
    return $original_template;
  }

  public function mtnc_admin_bar()
  {
    add_action('admin_bar_menu', 'mtnc_add_toolbar_items', 100);
  }

  function is_plugin_installed($slug)
  {
    if (!function_exists('get_plugins')) {
      require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
    $all_plugins = get_plugins();

    if (!empty($all_plugins[$slug])) {
      return true;
    } else {
      return false;
    }
  } // is_plugin_installed

  // auto download / install / activate WP Force SSL plugin
  function install_wpfssl()
  {
    check_ajax_referer('install_wpfssl');

    if (false === current_user_can('administrator')) {
      wp_die('Sorry, you have to be an admin to run this action.');
    }

    $plugin_slug = 'wp-force-ssl/wp-force-ssl.php';
    $plugin_zip = 'https://downloads.wordpress.org/plugin/wp-force-ssl.latest-stable.zip';

    @include_once ABSPATH . 'wp-admin/includes/plugin.php';
    @include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
    @include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
    @include_once ABSPATH . 'wp-admin/includes/file.php';
    @include_once ABSPATH . 'wp-admin/includes/misc.php';
    echo '<style>
		body{
			font-family: sans-serif;
			font-size: 14px;
			line-height: 1.5;
			color: #444;
		}
		</style>';

    echo '<div style="margin: 20px; color:#444;">';
    echo 'If things are not done in a minute <a target="_parent" href="' . admin_url('plugin-install.php?s=force%20ssl%20webfactory&tab=search&type=term') . '">install the plugin manually via Plugins page</a><br><br>';
    echo 'Starting ...<br><br>';

    wp_cache_flush();
    $upgrader = new Plugin_Upgrader();
    echo 'Check if WP Force SSL is already installed ... <br />';
    if ($this->is_plugin_installed($plugin_slug)) {
      echo 'WP Force SSL is already installed! <br /><br />Making sure it\'s the latest version.<br />';
      $upgrader->upgrade($plugin_slug);
      $installed = true;
    } else {
      echo 'Installing WP Force SSL.<br />';
      $installed = $upgrader->install($plugin_zip);
    }
    wp_cache_flush();

    if (!is_wp_error($installed) && $installed) {
      echo 'Activating WP Force SSL.<br />';
      $activate = activate_plugin($plugin_slug);

      if (is_null($activate)) {
        echo 'WP Force SSL Activated.<br />';

        echo '<script>setTimeout(function() { top.location = "admin.php?page=maintenance"; }, 1000);</script>';
        echo '<br>If you are not redirected in a few seconds - <a href="admin.php?page=maintenance" target="_parent">click here</a>.';
      }
    } else {
      echo 'Could not install WP Force SSL. You\'ll have to <a target="_parent" href="' . admin_url('plugin-install.php?s=force%20ssl%20webfactory&tab=search&type=term') . '">download and install manually</a>.';
    }

    echo '</div>';
  } // install_wpfssl

} // class MTNC

global $mtnc;
$mtnc = new MTNC();
