<?php
global $standart_fonts;
$standart_fonts = array(
  'Arial, Helvetica, sans-serif'                     => 'Arial, Helvetica, sans-serif',
  'Arial Black, Gadget, sans-serif'                  => 'Arial Black, Gadget, sans-serif',
  'Bookman Old Style, serif'                         => 'Bookman Old Style, serif',
  'Comic Sans MS, cursive'                           => 'Comic Sans MS, cursive',
  'Courier, monospace'                               => 'Courier, monospace',
  'Garamond, serif'                                  => 'Garamond, serif',
  'Georgia, serif'                                   => 'Georgia, serif',
  'Impact, Charcoal, sans-serif'                     => 'Impact, Charcoal, sans-serif',
  'Lucida Console, Monaco, monospace'                => 'Lucida Console, Monaco, monospace',
  'Lucida Sans Unicode, Lucida Grande, sans-serif'   => 'Lucida Sans Unicode, Lucida Grande, sans-serif',
  'MS Sans Serif, Geneva, sans-serif'                => 'MS Sans Serif, Geneva, sans-serif',
  'MS Serif, New York, sans-serif'                   => 'MS Serif, New York, sans-serif',
  'Palatino Linotype, Book Antiqua, Palatino, serif' => 'Palatino Linotype, Book Antiqua, Palatino, serif',
  'Tahoma,Geneva, sans-serif'                        => 'Tahoma, Geneva, sans-serif',
  'Times New Roman, Times,serif'                     => 'Times New Roman, Times, serif',
  'Trebuchet MS, Helvetica, sans-serif'              => 'Trebuchet MS, Helvetica, sans-serif',
  'Verdana, Geneva, sans-serif'                      => 'Verdana, Geneva, sans-serif',
);

function mtnc_get_plugin_options($is_current = false)
{
  $saved = (array) get_option('maintenance_options', array());

  if (!$saved) {
    $saved = mtnc_get_default_array();
  }

  if (!$is_current) {
    $options = wp_parse_args(get_option('maintenance_options', array()), mtnc_get_default_array());
  } else {
    $options = $saved;
  }
  return $options;
}

function mtnc_generate_input_filed($title, $id, $name, $value, $placeholder = '', $label = '', $pro = false)
{
  $out_filed  = '';
  $out_filed .= '<tr valign="top">';
  if ($pro) {
    $out_filed .= '<th scope="row"><label for="' . esc_attr($id) . '">' . esc_attr($title) . ' <sup>PRO</sup></label></th>';
  } else {
    $out_filed .= '<th scope="row"><label for="' . esc_attr($id) . '">' . esc_attr($title) . '</label></th>';
  }
  $out_filed .= '<td>';
  $out_filed .= '<fieldset>';
  if ($pro) {
    $out_filed .= '<input class="open-pro-dialog" data-pro-feature="' . $id . '" type="text" id="' . esc_attr($id) . '" name="" value="" placeholder="' . esc_attr($placeholder) . '"/>';
  } else {
    $out_filed .= '<input type="text" id="' . esc_attr($id) . '" name="lib_options[' . $name . ']" value="' . esc_attr(stripslashes($value)) . '" placeholder="' . esc_attr($placeholder) . '"/>';
  }
  $out_filed .= ' &nbsp; ' . $label;
  if ($pro) {
    $out_filed .= ' This is a <a class="open-pro-dialog" data-pro-feature="' . $id . '" href="#">PRO feature</a>.';
  }
  $out_filed .= '</fieldset>';
  $out_filed .= '</td>';
  $out_filed .= '</tr>';
  mtnc_wp_kses($out_filed);
}

function mtnc_generate_number_field($title, $id, $name, $value, $placeholder = '')
{
  $out_filed  = '';
  $out_filed .= '<tr valign="top">';
  $out_filed .= '<th scope="row"><label for="' . esc_attr($id) . '">' . esc_attr($title) . '</label></th>';
  $out_filed .= '<td>';
  $out_filed .= '<fieldset>';
  $out_filed .= '<input type="number" class="small-text" min="0" step="1" pattern="[0-9]{10}" id="' . esc_attr($id) . '" name="lib_options[' . $name . ']" value="' . esc_attr(stripslashes($value)) . '" placeholder="' . esc_attr($placeholder) . '"/>';
  $out_filed .= '</fieldset>';
  $out_filed .= '</td>';
  $out_filed .= '</tr>';
  mtnc_wp_kses($out_filed);
}

function mtnc_generate_textarea_filed($title, $id, $name, $value)
{
  $out_filed  = '';
  $out_filed .= '<tr valign="top">';
  $out_filed .= '<th scope="row"><label for="' . esc_attr($id) . '">' . esc_attr($title) . '</label></th>';
  $out_filed .= '<td>';
  $out_filed .= '<fieldset>';
  $out_filed .= '<textarea name="lib_options[' . $name . ']" id="' . esc_attr($id) . '" cols="30" rows="10">' . esc_textarea($value) . '</textarea>';
  $out_filed .= '</fieldset>';
  $out_filed .= '</td>';
  $out_filed .= '</tr>';
  mtnc_wp_kses($out_filed);
}


function mtnc_generate_tinymce_filed($title, $id, $name, $value)
{
  $out_filed  = '';
  $out_filed .= '<tr valign="top">';
  $out_filed .= '<th scope="row">' . esc_attr($title) . '</th>';
  $out_filed .= '<td>';
  $out_filed .= '<fieldset>';
  ob_start();
  wp_editor(
    $value,
    $id,
    array(
      'textarea_name' => 'lib_options[' . $name . ']',
      'teeny'         => 1,
      'textarea_rows' => 5,
      'media_buttons' => 0,
    )
  );
  $out_filed .= ob_get_contents();
  ob_clean();
  $out_filed .= '</fieldset>';
  $out_filed .= '</td>';
  $out_filed .= '</tr>';
  mtnc_wp_kses($out_filed);
}


function mtnc_generate_check_field($title, $label, $id, $name, $value, $pro = false)
{
  $out_filed  = '';
  $out_filed .= '<tr valign="top">';
  if ($pro) {
    $out_filed .= '<th scope="row"><label for="' . esc_attr($id) . '">' . esc_attr($title) . ' <sup>PRO</sup></label></th>';
  } else {
    $out_filed .= '<th scope="row"><label for="' . esc_attr($id) . '">' . esc_attr($title) . '</label></th>';
  }
  $out_filed .= '<td>';
  $out_filed .= '<fieldset>';
  $out_filed .= '<label for=' . esc_attr($id) . '>';
  if ($pro) {
    $out_filed .= '<input class="open-pro-dialog" data-pro-feature="' . $id . '" type="checkbox" id="' . esc_attr($id) . '" name="" value="1" />';
  } else {
    $out_filed .= '<input type="checkbox"  id="' . esc_attr($id) . '" name="lib_options[' . $name . ']" value="1" ' . checked(true, $value, false) . '/>';
  }
  $out_filed .= $label;
  if ($pro) {
    $out_filed .= ' This is a <a class="open-pro-dialog" data-pro-feature="' . $id . '" href="#">PRO feature</a>.';
  }
  $out_filed .= '</label>';
  $out_filed .= '</fieldset>';
  $out_filed .= '</td>';
  $out_filed .= '</tr>';
  mtnc_wp_kses($out_filed);
}

function mtnc_generate_button_field($title, $label, $button_label, $id, $name, $value, $pro = false)
{
  $out_filed  = '';
  $out_filed .= '<tr valign="top">';
  if ($pro) {
    $out_filed .= '<th scope="row"><label for="' . esc_attr($id) . '">' . esc_attr($title) . ' <sup>PRO</sup></label></th>';
  } else {
    $out_filed .= '<th scope="row"><label for="' . esc_attr($id) . '">' . esc_attr($title) . '</label></th>';
  }
  $out_filed .= '<td>';
  $out_filed .= '<fieldset>';
  if ($pro) {
    $out_filed .= '<button class="open-pro-dialog" data-pro-feature="' . $id . '" id="' . esc_attr($id) . '" name="">' . $button_label . '</button> &nbsp; ';
  } else {
    $out_filed .= '<input type="checkbox"  id="' . esc_attr($id) . '" name="lib_options[' . $name . ']" value="1" ' . checked(true, $value, false) . '/>';
  }
  $out_filed .= $label;
  if ($pro) {
    $out_filed .= ' This is a <a class="open-pro-dialog" data-pro-feature="' . $id . '" href="#">PRO feature</a>.';
  }
  $out_filed .= '</fieldset>';
  $out_filed .= '</td>';
  $out_filed .= '</tr>';
  mtnc_wp_kses($out_filed);
}

function mtnc_generate_image_filed($title, $id, $name, $value, $class, $name_btn, $class_btn)
{
  $out_filed = '';

  $out_filed .= '<tr valign="top">';
  $out_filed .= '<th scope="row">' . esc_attr($title) . '</th>';
  $out_filed .= '<td>';
  $out_filed .= '<fieldset>';
  $out_filed .= '<input type="hidden" id="' . esc_attr($id) . '" name="lib_options[' . $name . ']" value="' . esc_attr($value) . '" />';
  $out_filed .= '<div class="img-container">';
  $url        = '';
  if ($value !== '') {
    $image = wp_get_attachment_image_src($value, 'full');
    $url   = @esc_url($image[0]);
  }

  $out_filed .= '<div class="' . esc_attr($class) . '" style="background-image:url(' . $url . ')">';
  if ($value) {
    $out_filed .= '<input class="button button-primary delete-img remove" type="button" value="x" />';
  }
  $out_filed .= '</div>';
  $out_filed .= '<input type="button" class="' . esc_attr($class_btn) . '" value="' . esc_attr($name_btn) . '"/>';

  $out_filed .= '</div>';
  $out_filed .= '</fieldset>';
  $out_filed .= '</td>';
  $out_filed .= '</tr>';
  mtnc_wp_kses($out_filed);
}

function mtnc_get_color_field($title, $id, $name, $value, $default_color)
{
  $out_filed  = '';
  $out_filed .= '<tr valign="top">';
  $out_filed .= '<th scope="row"><label for="' . esc_attr($id) . '">' . esc_attr($title) . '</label></th>';
  $out_filed .= '<td>';
  $out_filed .= '<fieldset>';
  $out_filed .= '<input type="text" id="' . esc_attr($id) . '" name="lib_options[' . $name . ']" data-default-color="' . esc_attr($default_color) . '" value="' . wp_kses_post(stripslashes($value)) . '" />';
  $out_filed .= '<fieldset>';
  $out_filed .= '</td>';
  $out_filed .= '</tr>';
  mtnc_wp_kses($out_filed);
}

function mtnc_get_google_font($font = null)
{
  $font_params = $full_link = $gg_fonts = '';

  $gg_fonts = json_decode(mtnc_get_google_fonts());

  if (property_exists($gg_fonts, $font)) {
    $curr_font = $gg_fonts->{$font};
    if (!empty($curr_font)) {
      foreach ($curr_font->variants as $values) {
        if (!empty($values->id)) {
          $font_params .= $values->id . ',';
        } elseif (!empty($values)) {
          $font_params .= $values . ',';
        }
      }

      $font_params = trim($font_params, ',');
      $full_link   = $font . ':' . $font_params;
    }
  }

  return $full_link;
}

/*
 * Function get_fonts_field is backward compatibility with Maintenance PRO Version 3.6.2 and below */
function get_fonts_field($title, $id, $name, $value)
{
  return mtnc_get_fonts_field($title, $id, $name, $value);
}

function mtnc_get_fonts_field($title, $id, $name, $value)
{
  global $standart_fonts;
  $out_items = $gg_fonts = '';

  $gg_fonts = json_decode(mtnc_get_google_fonts());

  $out_filed  = '';
  $out_filed .= '<tr valign="top">';
  $out_filed .= '<th scope="row">' . esc_attr($title) . '</th>';
  $out_filed .= '<td>';
  $out_filed .= '<fieldset>';
  if (!empty($standart_fonts)) {
    $out_items .= '<optgroup label="' . __('Standard Fonts', 'maintenance') . '">';
    foreach ($standart_fonts as $key => $options) {
      $out_items .= '<option value="' . $key . '" ' . selected($value, $key, false) . '>' . $options . '</option>';
    }
  }

  if (!empty($gg_fonts)) {
    $out_items .= '<optgroup label="' . __('Google Web Fonts', 'maintenance') . '">';
    foreach ($gg_fonts as $key => $options) {
      $out_items .= '<option value="' . $key . '" ' . selected($value, $key, false) . '>' . $key . '</option>';
    }
  }

  if (!empty($out_items)) {
    $out_filed .= '<select class="select2_customize" name="lib_options[' . $name . ']" id="' . esc_attr($id) . '">';
    $out_filed .= $out_items;
    $out_filed .= '</select>';
  }
  $out_filed .= '<fieldset>';
  $out_filed .= '</td>';
  $out_filed .= '</tr>';
  return $out_filed; // phpcs:ignore WordPress.Security.EscapeOutput
}

function mtnc_get_fonts_subsets($title, $id, $name, $value)
{
  global $standart_fonts;
  $out_items = $gg_fonts = $curr_font = $mt_option = '';
  $mt_option = mtnc_get_plugin_options(true);
  $curr_font = esc_attr($mt_option['body_font_family']);
  $vars      = 'subsets';

  $gg_fonts = json_decode(mtnc_get_google_fonts(), true);

  if (!empty($gg_fonts)) {

    $out_filed  = '';
    $out_filed .= '<tr valign="top">';
    $out_filed .= '<th scope="row">' . esc_attr($title) . '</th>';
    $out_filed .= '<td>';
    $out_filed .= '<fieldset>';
    $out_filed .= '<select class="select2_customize" name="lib_options[' . $name . ']" id="' . esc_attr($id) . '">';
    if (!empty($gg_fonts[$curr_font])) {
      foreach ($gg_fonts[$curr_font]['variants'] as $key => $v) {
        $out_filed .= '<option value="' . $v . '" ' . selected($value, $v, false) . '>' . $v . '</option>';
      }
    }
    $out_filed .= '</select>';

    $out_filed .= '<fieldset>';
    $out_filed .= '</td>';
    $out_filed .= '</tr>';
  }
  return $out_filed; // phpcs:ignore WordPress.Security.EscapeOutput
}

function mtnc_page_create_meta_boxes()
{
  global $mtnc_variable;
  $mt_option = mtnc_get_plugin_options(true);

  if (!$mt_option['default_settings'] || $mt_option['gg_analytics_id']) {
    //add_meta_box('review-top', __('Please help us keep the plugin free &amp; maintained', 'maintenance'), 'mtnc_add_review_top', $mtnc_variable->options_page, 'normal', 'high');
  }
  add_meta_box('mtnc-toc', __('Jump to Settings Sections', 'maintenance'), 'mtnc_add_toc', $mtnc_variable->options_page, 'normal', 'high');
  add_meta_box('mtnc-general', __('Content', 'maintenance'), 'mtnc_add_data_fields', $mtnc_variable->options_page, 'normal', 'high');
  add_meta_box('mtnc-themes', __('Themes', 'maintenance'), 'mtnc_add_themes_fields', $mtnc_variable->options_page, 'normal', 'high');
  add_meta_box('mtnc-design', __('Design', 'maintenance'), 'mtnc_add_design_fields', $mtnc_variable->options_page, 'normal', 'high');
  add_meta_box('mtnc-access', __('Access Settings', 'maintenance'), 'mtnc_add_access_fields', $mtnc_variable->options_page, 'normal', 'high');
  add_meta_box('mtnc-css', __('Advanced Settings', 'maintenance'), 'mtnc_add_css_fields', $mtnc_variable->options_page, 'normal', 'default');
  add_meta_box('mtnc-excludepages', __('Exclude pages from maintenance mode', 'maintenance'), 'mtnc_add_exclude_pages_fields', $mtnc_variable->options_page, 'normal', 'default');
}
add_action('add_mt_meta_boxes', 'mtnc_page_create_meta_boxes', 10);

function mtnc_page_create_meta_boxes_widget_pro()
{
  global $mtnc_variable;

}
add_action('add_mt_meta_boxes', 'mtnc_page_create_meta_boxes_widget_pro', 15);

function mtnc_add_review_top() {
  $promo_text  = '';
  $promo_text .= '<p><b>Your review means a lot!</b> Please help us spread the word so that others know the Maintenance plugin is free and well maintained!<br>
  Thank you very much for using our plugin and helping us out!</p>';
  $promo_text .= '<p><br><a href="https://wordpress.org/support/plugin/maintenance/reviews/#new-post" target="_blank" class="button button-primary">Leave a Review</a> &nbsp;&nbsp; <a href="#" class="hide-review-box">I already left a review ;)</a></p>';
  mtnc_wp_kses($promo_text);
}

function mtnc_page_create_meta_boxes_widget_support()
{
  global $mtnc_variable;

  add_meta_box('promo-mtnc', __('Work faster - get the PRO version', 'maintenance'), 'mtnc_promo_mtnc', $mtnc_variable->options_page, 'side', 'high');

  if (!defined('WPFSSL_OPTIONS_KEY')) {
    add_meta_box('promo-wpfssl', __('Solve all SSL problems with the free WP Force SSL plugin', 'maintenance'), 'mtnc_promo_wpfssl', $mtnc_variable->options_page, 'side', 'high');
  }

  add_meta_box('promo-review2', __('Help us keep the plugin free &amp; maintained', 'maintenance'), 'mtnc_review_box', $mtnc_variable->options_page, 'side', 'high');

  add_meta_box('promo-content2', __('Something is not working? Do you need our help?', 'maintenance'), 'mtnc_contact_support', $mtnc_variable->options_page, 'side', 'default');
}
add_action('add_mt_meta_boxes', 'mtnc_page_create_meta_boxes_widget_support', 13);

function mtnc_add_toc($object, $box) {
  $out = '';

  $out .= '<ul>';
  $out .= '<li><a href="#mtnc-general">Content</a></li>';
  $out .= '<li><a href="#mtnc-themes">Themes</a></li>';
  $out .= '<li><a href="#mtnc-design">Design</a></li>';
  $out .= '<li><a href="#mtnc-access">Access Settings</a></li>';
  $out .= '<li><a href="#mtnc-css">Advanced Settings</a></li>';
  $out .= '<li><a href="#mtnc-excludepages">Exlcuded Pages</a></li>';
  $out .= '</ul>';

  mtnc_wp_kses($out);
}

function mtnc_add_data_fields($object, $box)
{
  $mt_option = mtnc_get_plugin_options(true);
  $is_blur   = false;

  $page_title = $heading = $description = $logo_width = $logo_height = '';

  $allowed_tags = wp_kses_allowed_html('post');
  if (isset($mt_option['page_title'])) {
    $page_title = wp_kses(stripslashes($mt_option['page_title']), $allowed_tags);
  }
  if (isset($mt_option['heading'])) {
    $heading = wp_kses_post($mt_option['heading']);
  }
  if (isset($mt_option['description'])) {
    $description = wp_kses(stripslashes($mt_option['description']), $allowed_tags);
  }
  if (isset($mt_option['footer_text'])) {
    $footer_text = wp_kses_post($mt_option['footer_text']);
  }
  if (isset($mt_option['logo_width'])) {
    $logo_width = wp_kses_post($mt_option['logo_width']);
  }
  if (isset($mt_option['logo_height'])) {
    $logo_height = wp_kses_post($mt_option['logo_height']);
  }
  ?>
  <table class="form-table">
    <tbody>
      <?php
        mtnc_generate_input_filed(__('Page Title', 'maintenance'), 'page_title', 'page_title', $page_title);
        mtnc_generate_button_field(__('SEO Options', 'maintenance'), 'Make sure your page can be indexed and found from day one!', 'Configure SEO Options', 'content_seo', '', false, true);
        mtnc_generate_input_filed(__('Headline', 'maintenance'), 'heading', 'heading', $heading);
        mtnc_generate_tinymce_filed(__('Description', 'maintenance'), 'description', 'description', $description);
        mtnc_generate_input_filed(__('Footer Text', 'maintenance'), 'footer_text', 'footer_text', $footer_text);
        mtnc_generate_check_field(__('Show Some Love', 'maintenance'), __('Show a small link in the footer to let others know you\'re using this awesome &amp; free plugin', 'maintenance'), 'show_some_love', 'show_some_love', !empty($mt_option['show_some_love']));
        mtnc_generate_check_field(__('Show Contact Form', 'maintenance'), 'Enable &amp; customize a contact form on the page so that visitors can easily get in touch with you.', 'content_contact_form', '', false, true);
        mtnc_generate_check_field(__('Show Map', 'maintenance'), 'Make it super-easy for visitors to find your business by displaying a map with your location.', 'content_map', '', false, true);
        mtnc_generate_check_field(__('Show Progress Bar', 'maintenance'), 'Let visitors know how your new site is progressing and when is it going to be complete.', 'content_progress_bar', '', false, true);
        mtnc_generate_check_field(__('Enable Frontend Login', 'maintenance'), '', 'is_login', 'is_login', isset($mt_option['is_login']));

        mtnc_wp_kses('<tr><td colspan="2"><p><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"></p></td></tr>');
        ?>
    </tbody>
  </table>
<?php
}

function mtnc_add_access_fields($object, $box)
{
  ?>
  <table class="form-table">
    <tbody>
      <?php
        mtnc_generate_check_field(__('Enable Secret Access Link', 'maintenance'), 'Give clients and friends a secret access link so they can see the full site.', 'access_secret_link', '', false, true);
        mtnc_generate_check_field(__('Password Protect the Page', 'maintenance'), 'Protect the maintenanace page with a password so that only selected people can open it. Perfect for launches.', 'access_password', '', false, true);
        mtnc_generate_check_field(__('Enable URL Based Rules', 'maintenance'), 'Individually pick pages, posts and URLs that will be or not be hidden behind the maintenance page.', 'access_url_rules', '', false, true);

        mtnc_wp_kses('<tr><td colspan="2"><p><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"></p></td></tr>');
        ?>
    </tbody>
  </table>
<?php
}

function mtnc_add_design_fields($object, $box)
{
  $mt_option = mtnc_get_plugin_options(true);
  $is_blur   = false;

  $page_title = $heading = $description = $logo_width = $logo_height = '';

  $allowed_tags = wp_kses_allowed_html('post');
  if (isset($mt_option['page_title'])) {
    $page_title = wp_kses(stripslashes($mt_option['page_title']), $allowed_tags);
  }
  if (isset($mt_option['heading'])) {
    $heading = wp_kses_post($mt_option['heading']);
  }
  if (isset($mt_option['description'])) {
    $description = wp_kses(stripslashes($mt_option['description']), $allowed_tags);
  }
  if (isset($mt_option['footer_text'])) {
    $footer_text = wp_kses_post($mt_option['footer_text']);
  }
  if (isset($mt_option['logo_width'])) {
    $logo_width = wp_kses_post($mt_option['logo_width']);
  }
  if (isset($mt_option['logo_height'])) {
    $logo_height = wp_kses_post($mt_option['logo_height']);
  }
  ?>
  <table class="form-table">
    <tbody>
      <?php
        mtnc_smush_option();
        mtnc_generate_number_field(__('Set Logo Width', 'maintenance'), 'logo_width', 'logo_width', $logo_width);
        mtnc_generate_number_field(__('Set Logo Height', 'maintenance'), 'logo_height', 'logo_height', $logo_height);
        mtnc_generate_image_filed(__('Logo', 'maintenance'), 'logo', 'logo', (int) $mt_option['logo'], 'boxes box-logo', __('Upload Logo', 'maintenance'), 'upload_logo upload_btn button');
        mtnc_generate_image_filed(__('Retina Logo (optional)', 'maintenance'), 'retina_logo', 'retina_logo', (int) $mt_option['retina_logo'], 'boxes box-logo', __('Upload Retina Logo', 'maintenance'), 'upload_logo upload_btn button');
        do_action('mtnc_background_field');
        mtnc_generate_input_filed(__('Background Video', 'maintenance'), 'design_bg_video', 'design_bg_video', '', '', 'Use a YouTube video for the bage background. It\' be muted and looped.', true);
        mtnc_generate_image_filed(__('Background Image (portrait mode)', 'maintenance'), 'bg_image_portrait', 'bg_image_portrait', isset($mt_option['bg_image_portrait']) ? (int) $mt_option['bg_image_portrait'] : '', 'boxes box-logo', __('Upload image for portrait device orientation', 'maintenance'), 'upload_logo upload_btn button');
        mtnc_generate_image_filed(__('Page Preloader Image', 'maintenance'), 'preloader_img', 'preloader_img', isset($mt_option['preloader_img']) ? (int) $mt_option['preloader_img'] : '', 'boxes box-logo', __('Upload preloader', 'maintenance'), 'upload_logo upload_btn button');

        do_action('mtnc_color_fields');
        do_action('mtnc_font_fields');

        if (isset($mt_option['is_blur'])) {
          if ($mt_option['is_blur']) {
            $is_blur = true;
          }
        }

        mtnc_generate_check_field(__('Apply Background Blur', 'maintenance'), 'Add blur effect to the background image', 'is_blur', 'is_blur', $is_blur);
        mtnc_generate_number_field(__('Set Blur Intensity', 'maintenance'), 'blur_intensity', 'blur_intensity', (int) $mt_option['blur_intensity']);

        mtnc_wp_kses('<tr><td colspan="2"><p><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"></p></td></tr>');
        ?>
    </tbody>
  </table>
<?php
}

// helper function for creating dropdowns
function mtnc_create_select_options($options, $selected = null, $output = true) {
  $out = "\n";

  if(!is_array($selected)) {
    $selected = array($selected);
  }

  foreach ($options as $tmp) {
    $data = '';
    if (isset($tmp['disabled'])) {
      $data .= ' disabled="disabled" ';
    }
    if (in_array($tmp['val'], $selected)) {
      $out .= "<option selected=\"selected\" value=\"{$tmp['val']}\"{$data}>{$tmp['label']}&nbsp;</option>\n";
    } else {
      $out .= "<option value=\"{$tmp['val']}\"{$data}>{$tmp['label']}&nbsp;</option>\n";
    }
  } // foreach

  if ($output) {
    mtnc_wp_kses($out);
  } else {
    return $out;
  }
} // create_select_options

function mtnc_smush_option() {
  if (defined('WP_SMUSH_VERSION')) {
    echo '<tr>';
    echo '<th><label for="smush_support">Enable Image Compression</label></th>';
    echo '<td style="line-height: 1.5;">';
    echo 'Configure <a href="' . esc_url(admin_url('admin.php?page=smush')) . '">image compression options</a>.';
    echo '</td>';
    echo '</tr>';
  } else {
    echo '<tr>';
    echo '<th><label for="smush_support">Enable Image Compression</label></th>';
    echo '<td style="line-height: 1.5;">';
    echo '<input type="checkbox" id="smush_support" type="checkbox" value="1" class="skip-save">The easiest way to speed up any site is to <b>compress images</b>. On an average page you can easily save a few megabytes. Doing it manually in Photoshop is a pain! That\'s why there are plugins like <a href="' . admin_url('plugin-install.php?fix-install-button=1&tab=plugin-information&plugin=wp-smushit&TB_iframe=true&width=600&height=550') . '" class="thickbox open-plugin-details-modal smush-thickbox">Smush</a> that specialize in compressing images. <a href="' . admin_url('plugin-install.php?fix-install-button=1&tab=plugin-information&plugin=wp-smushit&TB_iframe=true&width=600&height=550') . '" class="thickbox open-plugin-details-modal smush-thickbox">Install the free Smush plugin</a>. It has no limit on the amount of images you can compress, seamlessly integrates with WordPress, and is compatible with all plugins &amp; themes. And best of all - <b>it\'s used by over a million users just like you</b>.';
    echo '</td>';
    echo '</tr>';
  }
} // mtnc_smush_option

function mtnc_add_css_fields()
{
  $mt_option = mtnc_get_plugin_options(true);
  $gg_analytics_id = '';
  if (!empty($mt_option['gg_analytics_id'])) {
    $gg_analytics_id = esc_js($mt_option['gg_analytics_id']);
  }

  echo '<table class="form-table">';
  echo '<tbody>';
  mtnc_generate_input_filed(__('Google Analytics ID', 'maintenance'), 'gg_analytics_id', 'gg_analytics_id', $gg_analytics_id, __('UA-XXXXX-X', 'maintenance'));
  mtnc_generate_input_filed(__('Custom Tracking Pixel/Code', 'maintenance'), 'advanced_pixel', 'advanced_pixel', '', '', 'Place 3rd party tracking pixels and other tracking code here.', true);
  mtnc_generate_check_field(__('503 Response Code', 'maintenance'), __('Service temporarily unavailable, Google analytics will be disabled.', 'maintenance'), '503_enabled', '503_enabled', !empty($mt_option['503_enabled']));
  mtnc_generate_check_field(__('Send no-cache Headers', 'maintenance'), __('If you don\'t want Google, Facebook, Twitter and similar services to cache the preview of your site under maintenance use this option.', 'maintenance'), 'advanced_nocache', 'advanced_nocache', false, true);
  mtnc_generate_check_field(__('Enable WP REST API', 'maintenance'), __('By default WP REST API is blocked along with all other pages/URLs. If you need it while the site is under maintenance use this option.', 'maintenance'), 'advanced_restapi', 'advanced_restapi', false, true);

  mtnc_generate_textarea_filed(__('CSS Code', 'maintenance'), 'custom_css', 'custom_css', wp_kses_stripslashes($mt_option['custom_css']));
  echo '<tr><td>&nbsp;</td><td>Enter only the CSS code, without the <i>&lt;style&gt; tags.</i></td></tr>';
  echo '<tr><td colspan="2"><p><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"></p></td></tr>';
  echo '</tbody>';
  echo '</table>';
}

function mtnc_add_themes_fields()
{

$themes = array (
  0 =>
  array (
    'id' => '5f2f8c65307b6f3097f2ca4d25d5cb26',
    'name' => 'Adventure Blog',
    'name_clean' => 'adventure-blog',
    'status' => 'pro',
  ),
  1 =>
  array (
    'id' => '0a0c5efe1e95f91a42bc9e6e6ca884dd',
    'name' => 'Business',
    'name_clean' => 'business',
    'status' => 'pro',
  ),
  2 =>
  array (
    'id' => '06142f926b2da71d8dddfba3254a78cb',
    'name' => 'Digital Marketing Agency',
    'name_clean' => 'digital-marketing-agency',
    'status' => 'pro',
  ),
  3 =>
  array (
    'id' => '1f62286e16a799a6cf57a5013518a915',
    'name' => 'E-Shop',
    'name_clean' => 'e-shop',
    'status' => 'pro',
  ),
  4 =>
  array (
    'id' => 'd41b1b0a6d4cb304e886121b3118cfa0',
    'name' => 'Fashion',
    'name_clean' => 'fashion',
    'status' => 'pro',
  ),
  5 =>
  array (
    'id' => '7f96d3918bd5840258a6dce654f4b0dc',
    'name' => 'Flower Shop',
    'name_clean' => 'flower-shop',
    'status' => 'pro',
  ),
  6 =>
  array (
    'id' => '1c498ed60de01a93c2a4cac0ab50ddc2',
    'name' => 'Gaming',
    'name_clean' => 'gaming',
    'status' => 'pro',
  ),
  7 =>
  array (
    'id' => 'bb9f78a54648fe776fe7cdce018d4649',
    'name' => 'Interior Design',
    'name_clean' => 'interior-design',
    'status' => 'pro',
  ),
  8 =>
  array (
    'id' => 'bce5308440264fa4a8ce9cf1b38f3242',
    'name' => 'Mobile App',
    'name_clean' => 'mobile-app',
    'status' => 'pro',
  ),
  9 =>
  array (
    'id' => 'b20f2da4e5cd0753638723ff12383378',
    'name' => 'Non-Profit Organization',
    'name_clean' => 'non-profit-organization',
    'status' => 'pro',
  ),
  10 =>
  array (
    'id' => '2c6c47a437172cf970e9027ab7c4f680',
    'name' => 'Photography',
    'name_clean' => 'photography',
    'status' => 'pro',
  ),
  11 =>
  array (
    'id' => 'ea2584e286d8e0304994f4d9d9e4d335',
    'name' => 'Podcast',
    'name_clean' => 'podcast',
    'status' => 'pro',
  ),
  12 =>
  array (
    'id' => 'f7432f296c75f398c018ebbd0118cf1f',
    'name' => 'Product Marketing',
    'name_clean' => 'product-marketing',
    'status' => 'pro',
  ),
  13 =>
  array (
    'id' => '274bd92fd91aadc05fe0637f614633d8',
    'name' => 'Restaurant',
    'name_clean' => 'restaurant',
    'status' => 'pro',
  ),
  14 =>
  array (
    'id' => '1ff8ca16c5010eec8797eb5416373c6d',
    'name' => 'Skincare',
    'name_clean' => 'skincare',
    'status' => 'pro',
  ),
  15 =>
  array (
    'id' => 'a2df8994e86f844e9fe7516fb272b6f3',
    'name' => 'Social Media',
    'name_clean' => 'social-media',
    'status' => 'pro',
  ),
  16 =>
  array (
    'id' => 'eb668b7221bb4ed50c8edc8aebb68ba4',
    'name' => 'Sport',
    'name_clean' => 'sport',
    'status' => 'pro',
  ),
  17 =>
  array (
    'id' => '906d50132e2caf64ad57d9c76b07f78c',
    'name' => 'Travel Vlog',
    'name_clean' => 'travel-vlog',
    'status' => 'pro',
  ),
  18 =>
  array (
    'id' => 'd1dd1f82d0d557460f22ac7058c291e0',
    'name' => 'Wedding',
    'name_clean' => 'wedding',
    'status' => 'pro',
  ),
  19 =>
  array (
    'id' => '35b404155b3be97d198dadf05ddfc960',
    'name' => 'Wellness',
    'name_clean' => 'wellness',
    'status' => 'pro',
  ),
);

  function mntc_themes_sort($item1, $item2) {
    if (strtotime($item1['last_edit']) == strtotime($item2['last_edit'])) {
      return 0;
    }
    return strtotime($item1['last_edit']) < strtotime($item2['last_edit']) ? 1 : -1;
  }
  //usort($themes,'mntc_themes_sort');

  echo '<p>Are you in a hurry? Looking for something that looks great for your site? Pick one of <b>+20 premium pre-built themes</b> and be done in 5 minutes! Our PRO plugin comes with built-in SEO analyzer, a collection of over 3.7 million images and it can connect to any mailing system like Mailchimp so you can start collecting emails from day one! Did we mention you can <b>rebrand the plugin</b> and control all client sites from the plugin\'s centralized Dashboard?</p>';

  $i = 1;
  foreach ($themes as $theme) {
    echo '<div class="theme-thumb" data-theme="' . esc_html($theme['name_clean']) . '">';
    $i++;
    if ($theme['status'] != 'free') {
      echo '<a href="' . esc_url('https://themes.wpmaintenancemode.com/?maintenance-preview=' . $theme['id']) . '" target="_blank"><img src="' . esc_url(MTNC_URI) . 'images/pro-templates/' . esc_html($theme['name_clean']) . '.jpg" alt="Preview ' . esc_html($theme['name']) . '" title="Preview ' . esc_html($theme['name']) . '"></a>';
    }
    echo '<span class="name">' . esc_html($theme['name']) . '</span>';
    echo '<span name="actions">';
    if ($theme['status'] != 'free') {
      echo '<a href="#" data-pro-feature="theme-' . esc_attr($theme['name_clean']) . '" class="open-pro-dialog button button-primary">BUY lifetime license</a>&nbsp; &nbsp;';
      echo '<a target="_blank" class="button button-secondary" href="' . esc_url('https://themes.wpmaintenancemode.com/?maintenance-preview=' . $theme['id']) . '">Preview</a>';
    }
    echo '</span>';
    if ($theme['status'] != 'free') {
      echo '<div class="ribbon" title="PRO theme. Click \'Get this theme\' for more info."><i><span class="dashicons dashicons-star-filled"></span></i></div>';
    }
    echo '</div>';
  } // foreach theme
}

function mtnc_generate_web_link($placement = '', $page = '/', $params = array(), $anchor = '') {
  $base_url = 'https://wpmaintenancemode.com';

  if ('/' != $page) {
    $page = '/' . trim($page, '/') . '/';
  }
  if ($page == '//') {
    $page = '/';
  }

  $parts = array_merge(array('utm_source' => 'maintenance-free', 'utm_medium' => 'plugin', 'utm_content' => $placement, 'utm_campaign' => 'maintenance-free-v' . MTNC_VERSION), $params);

  if (!empty($anchor)) {
    $anchor = '#' . trim($anchor, '#');
  }

  $out = $base_url . $page . '?' . http_build_query($parts, '', '&amp;') . $anchor;

  return $out;
} // csmm_generate_web_link


function mtnc_add_exclude_pages_fields()
{
  $mt_option = mtnc_get_plugin_options(true);
  $out_filed = '';

  $post_types = get_post_types(
    array(
      'show_ui' => true,
      'public'  => true,
    ),
    'objects'
  );

  $out_filed .= '<table class="form-table">';
  $out_filed .= '<tbody>';
  $out_filed .= '<tr valign="top">';
  $out_filed .= '<th colspan="2" scope="row">' . __('Select the page(s) to be displayed normally, excluded by maintenance mode.', 'maintenance') . ' Please note that in order to prevent issues on sites with large number of posts we show only the first 200 entries for each post type (post, page, product,...).</th>';
  $out_filed .= '</tr>';

  foreach ($post_types as $post_slug => $type) {

    if (($post_slug === 'attachment') || ($post_slug === 'revision') || ($post_slug === 'nav_menu_item')
    ) {
      continue;
    }

    $args = array(
      'posts_per_page' => 200,
      'orderby'        => 'NAME',
      'order'          => 'ASC',
      'post_type'      => $post_slug,
      'post_status'    => 'publish',
    );

    $posts_array = get_posts($args);
    $db_pages_ex = array();

    if (!empty($posts_array)) {

      /*Exclude pages from maintenance mode*/
      if (!empty($mt_option['exclude_pages']) && isset($mt_option['exclude_pages'][$post_slug])) {
        $db_pages_ex = $mt_option['exclude_pages'][$post_slug];
      }

      $out_filed .= '<tr valign="top">';
      $out_filed .= '<th scope="row">' . $type->labels->name . '</th>';

      $out_filed .= '<fieldset>';
      $out_filed .= '<td>';

      $out_filed .= '<select id="exclude-pages-' . $post_slug . '" name="lib_options[exclude_pages][' . $post_slug . '][]" style="width:100%;" class="exclude-pages multiple-select-mt" multiple="multiple">';

      foreach ($posts_array as $post_values) {
        $current = null;
        if (!empty($db_pages_ex) && in_array($post_values->ID, $db_pages_ex, false)) {
          $current = $post_values->ID;
        }
        $selected   = selected($current, $post_values->ID, false);
        $out_filed .= '<option value="' . $post_values->ID . '" ' . $selected . '>' . esc_html($post_values->post_title) . '</option>';
      }

      $out_filed .= '</select>';

      $out_filed .= '</fieldset>';
      $out_filed .= '</td>';
      $out_filed .= '</tr>';
    }
  }

  $out_filed .= '<tr><td colspan="2"><p><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"></p></td></tr>';
  $out_filed .= '</tbody>';
  $out_filed .= '</table>';

  mtnc_wp_kses($out_filed);
}

function mtnc_get_background_fileds_action()
{
  $mt_option = mtnc_get_plugin_options(true);
  mtnc_generate_image_filed(__('Background Image', 'maintenance'), 'body_bg', 'body_bg', esc_attr($mt_option['body_bg']), 'boxes box-bg', __('Upload Background', 'maintenance'), 'upload_background upload_btn button');
}
add_action('mtnc_background_field', 'mtnc_get_background_fileds_action', 10);

function mtnc_get_color_fileds_action()
{
  $mt_option = mtnc_get_plugin_options(true);
  mtnc_get_color_field(__('Background Color', 'maintenance'), 'body_bg_color', 'body_bg_color', esc_attr(strip_tags($mt_option['body_bg_color'])), '#111111');
  mtnc_get_color_field(__('Font Color', 'maintenance'), 'font_color', 'font_color', esc_attr(strip_tags($mt_option['font_color'])), '#ffffff');
  mtnc_get_color_field(__('Login Block Background Color', 'maintenance'), 'controls_bg_color', 'controls_bg_color', isset($mt_option['controls_bg_color']) ? esc_attr(strip_tags($mt_option['controls_bg_color'])) : '', '#000000');
}
add_action('mtnc_color_fields', 'mtnc_get_color_fileds_action', 10);


function mtnc_get_font_fileds_action()
{
  $mt_option = mtnc_get_plugin_options(true);
  mtnc_wp_kses(mtnc_get_fonts_field(__('Font Family', 'maintenance'), 'body_font_family', 'body_font_family', esc_html($mt_option['body_font_family'])));
  $subset = '';

  if (!empty($mt_option['body_font_subset'])) {
    $subset = $mt_option['body_font_subset'];
  }
  mtnc_wp_kses(mtnc_get_fonts_subsets(__('Subsets', 'maintenance'), 'body_font_subset', 'body_font_subset', esc_html($subset)));
}
add_action('mtnc_font_fields', 'mtnc_get_font_fileds_action', 10);


function mtnc_contact_support()
{
  $promo_text  = '';
  $promo_text .= '<div class="sidebar-promo">';
  $promo_text .= '<p>We\'re here for you! We know how frustrating it is when things don\'t work!<br>Please <a href="https://wordpress.org/support/plugin/maintenance/" target="_blank">open a new topic in our official support forum</a> and we\'ll get back to you ASAP! We answer all questions, and most of them within a few hours.</p>';
  $promo_text .= '<p><a href="https://wordpress.org/support/plugin/maintenance/" target="_blank" class="button button-secondary">Get Help Now</a></p>';
  $promo_text .= '</div>';
  echo $promo_text; // phpcs:ignore WordPress.Security.EscapeOutput
}

function mtnc_review_box()
{
  $promo_text  = '';
  $promo_text .= '<div class="sidebar-promo">';
  $promo_text .= '<p><b>Your review means a lot!</b> Please help us spread the word so that others know this plugin is free and well maintained! Thank you very much for <a href="https://wordpress.org/support/plugin/maintenance/reviews/#new-post" target="_blank">reviewing the Maintanance plugin with ★★★★★ stars</a>!</p>';
  $promo_text .= '<p><a href="https://wordpress.org/support/plugin/maintenance/reviews/#new-post" target="_blank" class="button button-primary">Leave a Review</a> &nbsp;&nbsp; <a href="#" class="hide-review-box2">I already left a review ;)</a></p>';
  $promo_text .= '</div>';
  echo $promo_text; // phpcs:ignore WordPress.Security.EscapeOutput
}

function mtnc_promo_wpfssl()
{
  $promo_text  = '';
  //$promo_text .= '<h3 class="textcenter"><b>Problems with SSL certificate?<br>Moving a site from HTTP to HTTPS?<br>Mixed content giving you troubles?</b></h3>';
  $promo_text .= '<p class="textcenter"><a href="#" class="textcenter install-wpfssl"><img style="max-width: 90%;" src="' . MTNC_URI . 'images/wp-force-ssl-logo.png" alt="WP Force SSL" title="WP Force SSL"></a></p>';

  $promo_text .= '<p class="textcenter"><br><a href="#" class="install-wpfssl button button-primary">Install &amp; activate the free WP Force SSL plugin</a></p>';

  $promo_text .= '<p><a href="https://wordpress.org/plugins/wp-force-ssl/" target="_blank">WP Force SSL</a> is a free WP plugin maintained by the same team as this Maintenance plugin. It has <b>+180,000 users, 5-star rating</b>, and is hosted on the official WP repository.</p>';
  mtnc_wp_kses($promo_text);
} // mtnc_promo_wpfssl

function mtnc_promo_mtnc()
{
  $promo_text  = '';
  //$promo_text  .= '<h3 class="textcenter"><b>Problems with SSL certificate?<br>Moving a site from HTTP to HTTPS?<br>Mixed content giving you troubles?</b></h3>';
  $promo_text .= '<p class="textcenter"><a data-pro-feature="sidebar-mascot" href="#" class="textcenter open-pro-dialog"><img style="max-width: 70%; max-height: 300px;" src="' . MTNC_URI . 'images/maintenance-mascot.png" alt="WP Maintenance PRO" title="WP Maintenance PRO"></a></p>';

  $promo_text .= '<p class="textcenter"><br><a href="#" data-pro-feature="sidebar-button" class="open-pro-dialog button button-primary">Get PRO now</a></p>';

  $promo_text .= '<p class="textcenter">PRO version is here! Grab the launch discount - <b>all prices are LIFETIME!</b></p>';
  mtnc_wp_kses($promo_text);
} // mtnc_promo_mtnc

function mtnc_cur_page_url()
{
  $page_url = 'http';
  if (isset($_SERVER['HTTPS'])) {
    $page_url .= 's';
  }
  $page_url .= '://';
  if (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] !== '80') {
    $page_url .= wp_unslash($_SERVER['SERVER_NAME']) . ':' . wp_unslash($_SERVER['SERVER_PORT']) . wp_unslash($_SERVER['REQUEST_URI']);
  } else {
    $page_url .= wp_unslash($_SERVER['SERVER_NAME']) . wp_unslash($_SERVER['REQUEST_URI']);
  }
  return $page_url;
}

function mtnc_check_exclude()
{
  global $mt_options, $post;
  $mt_options = mtnc_get_plugin_options(true);
  $is_skip    = false;
  $cur_url    = mtnc_cur_page_url();
  if (is_page() || is_single()) {
    $curr_id = $post->ID;
  } else {
    if (is_home()) {
      $blog_id = get_option('page_for_posts');
      if ($blog_id) {
        $curr_id = $blog_id;
      }
    }

    if (is_front_page()) {
      $front_page_id = get_option('show_on_front');
      if ($front_page_id) {
        $curr_id = $front_page_id;
      }
    }
  }

  if (isset($mt_options['exclude_pages']) && !empty($mt_options['exclude_pages'])) {
    $exlude_objs = $mt_options['exclude_pages'];
    foreach ($exlude_objs as $objs_id) {
      foreach ($objs_id as $obj_id) {
        if ($curr_id === (int) $obj_id) {
          $is_skip = true;
          break;
        }
      }
    }
  }

  return $is_skip;
}


function mtnc_load_maintenance_page($original_template)
{
  global $mt_options;

  $v_curr_date_start = $v_curr_date_end = $v_curr_time = '';
  $vdate_start       = $vdate_end = date_i18n('Y-m-d', strtotime(current_time('mysql', 0)));
  $vtime_start       = date_i18n('h:i:s A', strtotime('01:00:00 am'));
  $vtime_end         = date_i18n('h:i:s A', strtotime('12:59:59 pm'));

  if (file_exists(MTNC_LOAD . 'index.php') && isset($_GET['maintenance-preview'])) {
    add_filter('script_loader_tag', 'mtnc_defer_scripts', 10, 2);
    return MTNC_LOAD . 'index.php';
  }

  $not_logged_in = !is_user_logged_in();
  if (apply_filters('mtnc_load_maintenance_page_for_this_user', $not_logged_in)) {
    if (!empty($mt_options['state'])) {

      if (!empty($mt_options['expiry_date_start'])) {
        $vdate_start = $mt_options['expiry_date_start'];
      }
      if (!empty($mt_options['expiry_date_end'])) {
        $vdate_end = $mt_options['expiry_date_end'];
      }
      if (!empty($mt_options['expiry_time_start'])) {
        $vtime_start = $mt_options['expiry_time_start'];
      }
      if (!empty($mt_options['expiry_time_end'])) {
        $vtime_end = $mt_options['expiry_time_end'];
      }

      $v_curr_time = strtotime(current_time('mysql', 0));

      $v_curr_date_start = strtotime($vdate_start . ' ' . $vtime_start);
      $v_curr_date_end   = strtotime($vdate_end . ' ' . $vtime_end);

      if (mtnc_check_exclude()) {
        return $original_template;
      }

      if (($v_curr_time < $v_curr_date_start) || ($v_curr_time > $v_curr_date_end)) {
        if (!empty($mt_options['is_down'])) { // is down - is flag for "Open website after countdown expired"
          return $original_template;
        }
      }
    } else {
      return $original_template;
    }

    if (file_exists(MTNC_LOAD . 'index.php')) {
      add_filter('script_loader_tag', 'mtnc_defer_scripts', 10, 2);
      return MTNC_LOAD . 'index.php';
    } else {
      return $original_template;
    }
  } else {
    return $original_template;
  }
}

function mtnc_defer_scripts($tag, $handle)
{
  if (strpos($handle, '_ie') !== 0) {
    return $tag;
  }
  return str_replace(' src', ' defer="defer" src', $tag);
}

function mtnc_metaboxes_scripts()
{
  global $mtnc_variable;
  ?>
  <script type="text/javascript">
    //<![CDATA[
    jQuery(document).ready(function() {
      jQuery('.if-js-closed').removeClass('if-js-closed').addClass('closed');
      postboxes.add_postbox_toggles('<?php echo esc_html($mtnc_variable->options_page); ?>');
    });
    //]]>
  </script>
<?php
}

function mtnc_add_toolbar_items()
{
  global $wp_admin_bar, $wpdb;
  $mt_options = mtnc_get_plugin_options(true);
  $check      = '';
  if (!is_super_admin() || !is_admin_bar_showing()) {
    return;
  }
  $url_to = admin_url('admin.php?page=maintenance');

  if ($mt_options['state']) {
    $check = 'On';
  } else {
    $check = 'Off';
  }
  $wp_admin_bar->add_menu(
    array(
      'id'    => 'maintenance_options',
      'title' => __('Maintenance', 'maintenance') . __(' is ', 'maintenance') . $check,
      'href'  => $url_to,
      'meta'  => array(
        'title' => __(
          'Maintenance',
          'maintenance'
        ) . __(
          ' is ',
          'maintenance'
        ) . $check,
      ),
    )
  );
}


function mtnc_hex2rgb($hex)
{
  $hex = str_replace('#', '', $hex);

  if (strlen($hex) === 3) {
    $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
    $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
    $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
  } else {
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
  }
  $rgb = array($r, $g, $b);
  return implode(',', $rgb);
}


function mtnc_insert_attach_sample_files()
{
  global $wpdb;
  $title            = '';
  $attach_id        = 0;
  $is_attach_exists = $wpdb->get_results("SELECT p.ID FROM $wpdb->posts p WHERE  p.post_title LIKE '%mt-sample-background%'", OBJECT);

  if (!empty($is_attach_exists)) {
    $attach_id = current($is_attach_exists)->ID;
  } else {
    require_once ABSPATH . 'wp-admin/includes/image.php';
    $image_url    = MTNC_DIR . 'images/mt-sample-background.jpg';
    $file_name    = basename($image_url);
    $file_content = file_get_contents($image_url);
    $upload       = wp_upload_bits($file_name, null, $file_content, current_time('mysql', 0));

    if (!$upload['error']) {
      $title = preg_replace('/\.[^.]+$/', '', $file_name);

      $wp_filetype = wp_check_filetype(basename($upload['file']), null);
      $attachment  = array(
        'guid'           => $upload['url'],
        'post_mime_type' => $wp_filetype['type'],
        'post_title'     => $title,
        'post_content'   => '',
        'post_status'    => 'inherit',
      );

      $attach_id   = wp_insert_attachment($attachment, $upload['file']);
      $attach_data = wp_generate_attachment_metadata($attach_id, $upload['file']);
      wp_update_attachment_metadata($attach_id, $attach_data);
    }
  }

  if (!empty($attach_id)) {
    return $attach_id;
  } else {
    return '';
  }
}

function mtnc_get_default_array()
{
  $defaults = array(
    'state'             => true,
    'page_title'        => __('Site is undergoing maintenance', 'maintenance'),
    'heading'           => __('Maintenance mode is on', 'maintenance'),
    'description'       => __('Site will be available soon. Thank you for your patience!', 'maintenance'),
    'footer_text'       => '&copy; ' . get_bloginfo('name') . ' ' . date('Y'),
    'show_some_love'    => '',
    'logo_width'        => 220,
    'logo_height'       => '',
    'logo'              => '',
    'retina_logo'       => '',
    'body_bg'           =>  mtnc_insert_attach_sample_files(),
    'bg_image_portrait' => '',
    'preloader_img'     => '',
    'body_bg_color'     => '#111111',
    'controls_bg_color' => '#111111',
    'font_color'        => '#ffffff',
    'body_font_family'  => 'Open Sans',
    'body_font_subset'  => 'Latin',
    'is_blur'           => false,
    'blur_intensity'    => 5,
    '503_enabled'       => false,
    'gg_analytics_id'   => '',
    'is_login'          => true,
    'custom_css'        => '',
    'exclude_pages'     => '',
    'default_settings'  => true,
  );

  return apply_filters('mtnc_get_default_array', $defaults);
}

if (!function_exists('mtnc_get_google_fonts')) {
  function mtnc_get_google_fonts()
  {
    $gg_fonts = file_get_contents(MTNC_DIR . 'includes/fonts/googlefonts.json');
    return $gg_fonts;
  }
}
