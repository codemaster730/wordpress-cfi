<?php
function mo_hsso_show_addons_page()
{
    require_once(ABSPATH . '/wp-admin/includes/plugin.php');
    $addons_displayed = array();
    $addon_desc = array(
        'scim'                          =>  __('Allows real-time user sync (automatic user create, delete, and update) from your Identity Provider such as Azure, Okta, Onelogin into your WordPress site.', 'Headless-Single-Sign-On'),
        'page_restriction'              =>  __('Restrict access to WordPress pages/posts based on user roles and their login status, thereby protecting these pages/posts from unauthorized access.', 'Headless-Single-Sign-On'),
        'file_prevention'               =>  __('Restrict any kind of media files such as images, audio, videos, documents, etc, and any extension (configurable) such as png, pdf, jpeg, jpg, bmp, gif, etc.', 'Headless-Single-Sign-On'),
        'ssologin'                      =>  __('SSO Login Audit tracks all the SSO users and generates detailed reports. The advanced search filters in audit reports makes it easy to find and keep track of your users.', 'Headless-Single-Sign-On'),
        'buddypress'                    =>  __('Integrate user information sent by the SAML Identity Provider in SAML Assertion with the BuddyPress profile fields.', 'Headless-Single-Sign-On'),
        'learndash'                     =>  __('Allows mapping your users to different LearnDash LMS plugin groups as per their group information sent by configured  SAML Identity Provider.', 'Headless-Single-Sign-On'),
        'attribute_based_redirection'   =>  __('Enables you to redirect your users to different pages after they log into your site, based on the attributes sent by your Identity Provider.', 'Headless-Single-Sign-On'),
        'ssosession'                    =>  __('Helps you in managing the login session time of your users based on their WordPress roles. Session time for roles can be specified.', 'Headless-Single-Sign-On'),
        'fsso'                          =>  __('Allows secure access to the site using various federations such as InCommon, HAKA, HKAF, etc. Users can log into the WordPress site using their university credentials.', 'Headless-Single-Sign-On'),
        'memberpress'                   =>  __('Map users to different membership levels created by the MemberPress plugin using the group information sent by your Identity Provider.', 'Headless-Single-Sign-On'),
        'wp_members'                    =>  __('Integrate WP-members fields using the attributes sent by your SAML Identity Provider in the SAML Assertion.', 'Headless-Single-Sign-On'),
        'woocommerce'                   =>  __('Map WooCommerce checkout page fields using the attributes sent by your IDP. This also allows you to map the users in different WooCommerce roles based on their IDP groups.', 'Headless-Single-Sign-On'),
        'guest_login'                   =>  __('Allows users to SSO into your site without creating a user account for them. This is useful when you dont want to manage the user accounts at the WordPress site.', 'Headless-Single-Sign-On'),
        'paid_mem_pro'                  =>  __('Map your users to different Paid MembershipPro membership levels as per the group information sent by your Identity Provider.', 'Headless-Single-Sign-On'),
        'profile_picture_add_on'        =>  __('Maps raw image data or URL received from your Identity Provider into Gravatar for the user.', 'Headless-Single-Sign-On')
    );
?>
    <div id="miniorange-addons" style="position:relative;z-index: 1">

        <div class="row container-fluid" id="addon-tab-form">
            <div class="col-md-8 mt-4 ml-5">

                <!-- <p id="recommended_section" style="font-size:20px;padding-left:10px;margin-top:5px;display:none"><b><?php _e('Recommended Add-ons for you', 'Headless-Single-Sign-On'); ?>:</b></p> -->
                <?php

                
                ?>
                <?php
                    $heading_is_displayed = false;
                foreach (mo_hsso_options_addons::$RECOMMENDED_ADDONS_PATH as $key => $value) {
                    if (is_plugin_active($value)) {
                        $addon = $key;
                        $addons_displayed[$addon] = $addon; if(!$heading_is_displayed){?>
                        <h4 class="form-head" id="recommended_section"><?php _e('Recommended Add-ons for you', 'Headless-Single-Sign-On'); ?></h4>
                        <?php
                        $heading_is_displayed = true;
                        }
                        echo mo_hsso_get_addon_tile($addon, mo_hsso_options_addons::$ADDON_TITLE[$addon], $addon_desc[$addon], mo_hsso_options_addons::$ADDON_URL[$addon], true);
                        ?>
                       
                    <?php
                    }
                }
                
                if ($heading_is_displayed) {
                echo '
                <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
                ';
                }
                ?>               
                        <h4 class="form-head"><?php _e('Check out all our add-ons', 'Headless-Single-Sign-On'); ?></h4>
                
                <?php
                
                foreach ($addon_desc as $key => $value) {
                    if (!in_array($key, $addons_displayed)) 
                    echo mo_hsso_get_addon_tile($key, mo_hsso_options_addons::$ADDON_TITLE[$key], $value, mo_hsso_options_addons::$ADDON_URL[$key], false);
                }
                ?>
            </div>
            <?php mo_hsso_display_support_form(); ?>

        </div>
    </div> <?php
        }

        function mo_hsso_get_addon_tile($addon_name, $addon_title, $addon_desc, $addon_url, $active)
        {

            $icon_url = plugins_url("images/addons_logos/" . $addon_name . ".png", mo_hsso_options_plugin_constants::PLUGIN_FILE);

            ?>
    <div class="mo-saml-add-ons-cards mt-3">
        <h4 class="mo-saml-addons-head"><?php echo $addon_title ?></h4>
        <p class="pt-4 pr-2 pb-4 pl-4"><?php echo $addon_desc ?></p>
        <img src="<?php echo $icon_url ?>" class="mo-saml-addons-logo" alt=" Image">
        <span class="mo-saml-add-ons-rect"></span>
        <span class="mo-saml-add-ons-tri"></span>
        <a class="mo-saml-addons-readmore" href="<?php echo $addon_url ?>" target="_blank">Learn More</a>
    </div>
<?php   }
?>