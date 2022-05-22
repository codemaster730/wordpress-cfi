<?php

function mo_hsso_wordpress_authentication(){
    ?>
<div class="row container-fluid" id="headless-tab-form">
    <div class="col-md-8 mt-4 ml-5">
        <form method="post" action="" id="mo_hsso_wordpress_authentication">
            <?php wp_nonce_field("mo_hsso_wordpress_authentication");?>
            <input type="hidden" name="option" value="mo_hsso_wordpress_authentication" />
            <div class="p-4 shadow-cstm bg-white rounded">
                <div class="row align-items-baseline">
                    <div class="col-md-12">
                        <h4> Configure Headless for WordPress Authentication</h4>
                    </div>
                </div>
                <div class="form-head"></div>

                <div class="bg-cstm p-3 rounded mt-4">
                    <strong>Note:</strong>
                    This option allows WordPress Authentication for any of the integrated frontend frameworks like Gatsby, Vue, Angular,React, NextJS, etc via JWT token
                </div>
                <div class="row align-items-top mt-4">
                        <div class="col-md-1">
                            <input type="checkbox" id="switch" name="mo_hsso_wordpress_authentication" class="mo-saml-switch"
                                onchange="document.getElementById('mo_hsso_wordpress_authentication').submit();"
                                <?php echo checked(get_option('mo_hsso_wordpress_authentication')=='true')?> />
                            <label class="mo-saml-switch-label" for="switch">Toggle</label>
                        </div>
                        <div class="col-md-11">
                            <span class="bg-cstm p-1 rounded mt-4 font-size: 0.9rem;">Enable Wordpress Authentication for headless CMS
                            </span>
                        </div>
                </div>
            </div>
        </form>
    </div>
    <?php mo_hsso_display_support_form(false,true); ?>
</div>
<?php
}