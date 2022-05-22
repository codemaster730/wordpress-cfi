<?php

function mo_hsso(){
    $site_url = get_site_url().'?option=hsso';
    $endpoint = get_option('mo_hsso_url');
    ?>
<div class="row container-fluid" id="headless-tab-form">
    <div class="col-md-8 mt-4 ml-5">
        <form method="post" action="">
            <?php wp_nonce_field("mo_hsso_option");?>
            <input type="hidden" name="option" value="mo_hsso_option" />
            <div class="p-4 shadow-cstm bg-white rounded">
                <div class="row align-items-baseline">
                    <div class="col-md-6">
                        <h4> Configure Headless SSO</h4>
                    </div>
                    <div class="col-md-5">
                        <h6> <a href="<?php echo admin_url('admin.php?page=mo_hsso_setting'); ?>"
                                class="btn btn-cstm ml-3">Convert WordPress to Headless CMS</a></h6>
                    </div>
                </div>
                <div class="form-head"></div>
                <div class="row align-items-center mt-4">
                    <div class="col-md-6">
                        <h6 class="text-secondary"> Plugin endpoint to fetch the JWT token :</h6>
                    </div>
                    <div class="col-md-6">
                        <h6 class="p-1 rounded">
                            <table class="w-100">
                                <tbody>
                                    <tr>
                                        <td>
                                            <span id="siteurl"
                                                class="mr-2 rounded p-2 bg-cstm"><?php echo $site_url; ?></span>
                                            <i class="icon-copy mo_copy copytooltip rounded-circle"
                                                onclick="copyToClipboard(this, '#siteurl','#siteurl_copy');"><span
                                                    id="siteurl_copy" class="copytooltiptext">Copy to
                                                    Clipboard</span></i>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </h6>
                    </div>
                </div>

                <div class="bg-cstm p-3 rounded mt-4">
                    <strong>Note:</strong>
                    Copy the above endpoint and configure it in your Frontend application, on SSO redirect to the above
                    endpoint and the plugin will redirect the user to Wordpress Login Page.
                    The user can then enter the credentials and the JWT response will be posted to the configured
                    endpoint. </small>
                </div>

                <div class="row align-items-center mt-4">
                    <div class="col-md-4">
                        <h6 class="text-secondary"> JWT Signing Certificate </h6>
                    </div>
                    <div class="col-md-7">
                        <h6 class="pl-3 rounded">
                            <table class="w-100">
                                <tbody>
                                     <tr>
                                     <td><a href="<?php echo admin_url('admin.php?page=mo_hsso_settings&tab=licensing'); ?>" class="btn btn-cstm">Available in Premium</a>
                                    <img src="<?php echo plugin_dir_url(mo_hsso_options_plugin_constants::PLUGIN_FILE) . 'images/crown.png' ?>" width="30px">&nbsp;</td>
                                    </tr>
                                </tbody>
                            </table>
                        </h6>
                    </div>
                </div>

            </div>
            <div class="p-4 shadow-cstm bg-white rounded mt-4">

                <div class="mb-3">
                    <h4>
                        Configure the Front End Endpoint
                    </h4>
                </div>
                <div class="form-head"></div>
                <div class="row align-items-center mt-5">
                    <div class="col-md-6">
                        <h6 class="text-secondary"> Enter the endpoint where JWT token is sent: </h6>
                    </div>
                    <div class="col-md-5">
                        <input type="url" name="mo_hsso_url" placeholder="Enter the Endpoint to post the JWT on"
                            required value="<?php echo $endpoint; ?>" class="w-100">
                    </div>
                </div>
                <h6 class="bg-cstm p-3 rounded mt-4"><b>Note</b>: JWT response will include Expiration time, JWT token,
                    Token type, and Time generated on.</h6>
                <div class="text-center">
                    <input type="submit" class="btn-cstm bg-info rounded mt-4" name="submit" value="Save Configuration">
                </div>
            </div>
        </form>
    </div>
    <?php mo_hsso_display_support_form(false,true); ?>
</div>
<?php
}