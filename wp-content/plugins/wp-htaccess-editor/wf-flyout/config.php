<?php
$config = array();

$config['plugin_screen'] = 'settings_page_wp-htaccess-editor';
$config['icon_border'] = '1px solid #00000077';
$config['icon_right'] = '35px';
$config['icon_bottom'] = '35px';
$config['icon_image'] = 'htaccess-editor.png';
$config['icon_padding'] = '9px';
$config['icon_size'] = '55px';
$config['menu_accent_color'] = '#dd3036';
$config['custom_css'] = '#wf-flyout .wff-menu-item .dashicons.dashicons-universal-access { font-size: 30px; padding: 0px 10px 0px 0; } #wf-flyout .ucp-icon .wff-icon img { max-width: 70%; } #wf-flyout .ucp-icon .wff-icon { line-height: 57px; } #wf-flyout .wp301-icon .wff-icon img { max-width: 66%; } #wf-flyout .wp301-icon .wff-icon { line-height: 57px; }';

$config['menu_items'] = array(
  array('href' => 'https://wp301redirects.com/?ref=wff-htaccess&coupon=50off', 'label' => 'Fix most common SEO issues on WordPress that everbody ignores', 'icon' => '301-logo.png', 'class' => 'wp301-icon'),
  array('href' => 'https://wpreset.com/?ref=wff-wp-htaccess', 'target' => '_blank', 'label' => 'Get WP Reset PRO with 50% off', 'icon' => 'wp-reset.png'),
  array('href' => 'https://underconstructionpage.com/?ref=wff-wp-htaccess&coupon=welcome', 'target' => '_blank', 'label' => 'Create the perfect Under Construction Page', 'icon' => 'ucp.png', 'class' => 'ucp-icon'),
  array('href' => 'https://wpsticky.com/?ref=wff-wp-htaccess', 'target' => '_blank', 'label' => 'Make a menu sticky with WP Sticky', 'icon' => 'dashicons-admin-post'),
  array('href' => 'https://wordpress.org/support/plugin/wp-htaccess-editor/reviews/?filter=5#new-post', 'target' => '_blank', 'label' => 'Rate the Plugin', 'icon' => 'dashicons-thumbs-up'),
  array('href' => 'https://wordpress.org/support/plugin/wp-htaccess-editor/#new-post', 'target' => '_blank', 'label' => 'Get Support', 'icon' => 'dashicons-sos'),
);
