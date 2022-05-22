<?php
function mo_hsso_setting()
{
    ?>
<div class="bg-main-cstm pb-4 mo-saml-margin-left">
    <div class="row container-fluid" id="headless-setting-tab-form">
        <div class="col-md-8 mt-4 ml-5">
            <form method="post" id="mo_hsso_enable_headless_form" action="">
                <?php wp_nonce_field("mo_hsso_enable_headless_option");?>
                <input type="hidden" name="option" value="mo_hsso_enable_headless_option" />
                <div class="p-4 shadow-cstm bg-white rounded">
                    <div class="row align-items-baseline">
                        <div class="col-md-7">
                            <h4> Use WordPress as a headless CMS</h4>
                        </div>
                        <div class="col-md-5">
                            <h6> <a href="<?php echo admin_url('admin.php?page=mo_hsso_settings&tab=hsso'); ?>"
                                    class="btn btn-cstm ml-3"><svg xmlns="http://www.w3.org/2000/svg" width="16"
                                        height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd"
                                            d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z" />
                                    </svg>&nbsp; Back to SSO Configuration
                                </a>
                            </h6>
                        </div>
                    </div>
                    <div class="form-head"></div>
                    <div class="row align-items-baseline mt-2">
                    </div>
                    <div class="bg-cstm p-3 rounded mt-4">
                        This option disables access to the front-end of your site and let's you integrate with any
                        front-end environment using REST API.
                        Headless mode sets up a redirect for the users trying to access the front-end of the site. The
                        only requests that are granted admission are ones that are either trying to access the REST API,
                        the WP GraphQL API, or any logged-in user looking to access the headless install for editing or
                        creating posts.
                    </div>
                    <h6 class="bg-cstm p-3 rounded mt-4"><b>Note</b>: This option accepts requests to REST API or
                        WP_GRAPHQL endpoints. </h6>
                    <div class="row align-items-top mt-4">
                        <div class="col-md-1">
                            <input type="checkbox" id="switch" name="mo_hsso_enable_headless" class="mo-saml-switch"
                                onchange="document.getElementById('mo_hsso_enable_headless_form').submit();"
                                <?php echo checked(get_option('mo_hsso_enable_headless')=='true')?> />
                            <label class="mo-saml-switch-label" for="switch">Toggle</label>
                        </div>
                        <div class="col-md-11">
                            <span class="bg-cstm p-1 rounded mt-4 font-size: 0.9rem;">Enable Headless mode by disabling
                                WP
                                Front-end</span>
                        </div>
                    </div>
            </form>
            <form method="post" action="">
                <input type="hidden" name="option" value="mo_hsso_setting_option" />
                <?php wp_nonce_field('mo_hsso_setting_option');
                    $to_check=((get_option('mo_hsso_wp_setting')=='mo_headless_unauthorized' && get_option('mo_hsso_enable_headless')=='true'));?>
                <div class="mt-2 row align-items-baseline">
                    <div class="col-md-6 inline-block pr-0 pl-0">
                        <input type="radio" name="mo_hsso_wp_setting" value="mo_headless_unauthorized" <?php if ( get_option('mo_hsso_enable_headless') == false ) {
                                echo 'disabled';
                            }
							else if ( get_option('mo_hsso_wp_setting') == 'mo_headless_unauthorized' ) {
                                echo 'checked="checked"';
                           }?>>
                        <span class="" style="font-size: 0.9rem;">Display 403 Unauthorized error when accessing front-end of the WordPress site</span>
                    </div>
                    <div class="col-md-6 pr-0">
                        <input type="radio" name="mo_hsso_wp_setting" class="d-inline-block"
                            value="mo_headless_redirect" <?php if ( get_option('mo_hsso_enable_headless') == false ) {
                                    echo 'disabled';
                                }
                                else if ( get_option('mo_hsso_wp_setting') == 'mo_headless_redirect' ) {
                                    echo 'checked="checked"';
                            }?>>
                        <span class="font-weight-normal" style="font-size: 0.9rem;">Redirect non-logged users trying to access the site to WordPress login page</span>
                    </div>
                </div>


                <div class="text-center">
                    <input type="submit" class="btn-cstm bg-info rounded mt-4" name="btnsubmit" value="Save">
                </div>
        </div>
        </form>
    </div>
    <?php mo_hsso_display_support_form(false,true); ?>
</div>
</div>
<?php
}
?>