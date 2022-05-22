<?php


function mo_hsso_save_optional_config()
{


    $default_role = get_option('saml_am_default_user_role');
    if (empty($default_role)) {
        $default_role = get_option('default_role');
    }
    $wp_roles         = new WP_Roles();
    $roles            = $wp_roles->get_names();
?>
    <div class="row container-fluid" action="" id="attr-role-tab-form">
        <div class="col-md-8 mt-4 ml-5">
            <?php
            mo_hsso_display_attribute_mapping();
            mo_hsso_display_role_mapping($default_role, $roles); ?>
        </div>
        <?php mo_hsso_display_support_form(true,false); ?>
    </div>
<?php
}

function mo_hsso_display_attribute_mapping()
{
?>
    <div class="p-4 shadow-cstm bg-white rounded">
        <div class="row align-items-top">
            <div class="col-md-12">
                <h4 class="form-head">
                    <span class="entity-info">Attribute Mapping <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"></path>
                            <path d="M5.255 5.786a.237.237 0 0 0 .241.247h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286zm1.557 5.763c0 .533.425.927 1.01.927.609 0 1.028-.394 1.028-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94z"></path>
                        </svg>
                        <span class="entity-info-text attr-info-text p-2">Configure Attribute Mapping. <a href="https://developers.miniorange.com/docs/saml/wordpress/Attribute-Rolemapping" class="text-warning" target="_blank">Click here to know how this is useful</a></span>
                    </span>
                </h4>
            </div>
        </div>

        <div class="row prem-info mt-5 d-block">
            <div class="prem-icn nameid-prem-img"><img src="<?php echo plugin_dir_url(mo_hsso_options_plugin_constants::PLUGIN_FILE) . 'images/crown.png' ?>" width="35px">
                <p class="nameid-prem-text">These attributes are configurable in Standard, Premium, Enterprise and All-Inclusive versions of the plugin. <a href="<?php echo admin_url('admin.php?page=mo_hsso_settings&tab=licensing'); ?>" class="text-warning">Click here to upgrade</a></p>
            </div>
            <div class="row align-items-top">
                <div class="col-md-3">
                    <h6 class="text-secondary">Username (required) </span>:</h6>
                </div>
                <div class="col-md-6">
                    <p>NameID</p>
                </div>
            </div>
            <div class="row align-items-top mt-4">
                <div class="col-md-3">
                    <h6 class="text-secondary">Email (required) </span>:</h6>
                </div>
                <div class="col-md-6">
                    <p>NameID</p>
                </div>
            </div>
            <div class="row align-items-top mt-4">
                <div class="col-md-3">
                    <h6 class="text-secondary">First Name </span>:</h6>
                </div>
                <div class="col-md-6">
                    <input type="text" name="saml_am_first_name" placeholder="Enter attribute name for First Name" class="w-100 bg-light cursor-disabled" value="" disabled>
                </div>
            </div>
            <div class="row align-items-top mt-4">
                <div class="col-md-3">
                    <h6 class="text-secondary">Last Name </span>:</h6>
                </div>
                <div class="col-md-6">
                    <input type="text" name="saml_am_last_name" placeholder="Enter attribute name for Last Name" class="w-100 bg-light cursor-disabled" value="" disabled>
                </div>
            </div>
            <div class="row align-items-top mt-4">
                <div class="col-md-3">
                    <h6 class="text-secondary">Group/Role </span>:</h6>
                </div>
                <div class="col-md-6">
                    <input type="text" name="" placeholder="Enter attribute name for Group/Role" class="w-100 bg-light cursor-disabled" value="" disabled>
                </div>
            </div>
            <div class="row align-items-top mt-4">
                <div class="col-md-3">
                    <h6 class="text-secondary">Map Custom Attributes</h6>
                </div>
                <div class="col-md-6">
                    <p>Customized Attribute Mapping means you can map any attribute of the IDP to the usermeta table of your database.</p>
                </div>
            </div>

        </div>
        <div class="row align-items-top mt-5 prem-info">
            <div class="prem-icn anonymous-prem-img"><img src="<?php echo plugin_dir_url(mo_hsso_options_plugin_constants::PLUGIN_FILE) . 'images/crown.png' ?>" width="35px">
                <p class="anonymous-text">Enable this option if you want to allow users to login to the WordPress site without creating a WordPress user account for them. <a href="<?php echo admin_url('admin.php?page=mo_hsso_settings&tab=licensing'); ?>" class="text-warning">Available in Paid Plugin</a></p>
            </div>
            <div class="col-md-3">
                <h6 class="text-secondary">Anonymous Login :</h6>
            </div>
            <div class="col-md-8">
                <section>
                    <input type="checkbox" id="switch" class="mo-saml-switch cursor-disabled" disabled /><label class="mo-saml-switch-label" for="switch">Toggle</label>

                </section>
            </div>
        </div>

    </div>
<?php

}

function mo_hsso_display_role_mapping($default_role, $roles)
{
?>
    <form name="saml_form_am_role_mapping" method="post" action="">
        <?php
        wp_nonce_field('login_widget_saml_role_mapping'); ?>
        <input type="hidden" name="option" value="login_widget_saml_role_mapping" />

        <div class="p-4 shadow-cstm bg-white rounded mt-4">
            <div class="row align-items-top">
                <div class="col-md-12">
                    <h4 class="form-head">
                    <span class="entity-info">Role Mapping <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"></path>
                            <path d="M5.255 5.786a.237.237 0 0 0 .241.247h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286zm1.557 5.763c0 .533.425.927 1.01.927.609 0 1.028-.394 1.028-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94z"></path>
                        </svg>
                        <span class="entity-info-text role-map-info-text p-2">Configure Role Mapping. <a href="https://developers.miniorange.com/docs/saml/wordpress/Attribute-Rolemapping#Role-Mapping" class="text-warning" target="_blank">Click here to know how this is useful</a></span>
                    </span>
                    </h4>
                </div>
            </div>
            <div class="row align-items-center mt-5">
                <div class="col-md-3">
                    <h5>Default Role : </h5>
                </div>
                <div class="col-md-3">
                    <select id="saml_am_default_user_role" name="saml_am_default_user_role">
                        <?php
                        echo wp_dropdown_roles($default_role);
                        ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="submit" class="btn-cstm bg-info rounded" name="submit" value="Update">
                </div>
            </div>
            <div class="row prem-info mt-5">
                <div class="prem-icn role-prem-img"><img src="<?php echo plugin_dir_url(mo_hsso_options_plugin_constants::PLUGIN_FILE) . 'images/crown.png' ?>" width="35px">
                    <p class="role-prem-text">Customized Role Mapping options are configurable in the Premium, Enterprise and All-Inclusive versions of the plugin. <a href="<?php echo admin_url('admin.php?page=mo_hsso_settings&tab=licensing'); ?>" class="text-warning">Click here to upgrade</a></p>
                </div>
                <div class="row align-items-top mt-4 col-md-12">
                    <div class="col-md-7">
                        <h6 class="text-secondary">Do not auto create users if roles are not mapped here </span>:</h6>
                    </div>
                    <div class="col-md-5">
                        <input type="checkbox" id="switch" class="mo-saml-switch cursor-disabled" disabled /><label class="mo-saml-switch-label" for="switch">Toggle</label>

                        <p class="mt-2">Enable this option if you do not want the unmapped users to register into your site via SSO.</p>
                    </div>
                </div>
                <div class="row align-items-top mt-4 col-md-12">
                    <div class="col-md-7">
                        <h6 class="text-secondary">Do not assign role to unlisted users </span>:</h6>
                    </div>
                    <div class="col-md-5">
                        <input type="checkbox" id="switch" class="mo-saml-switch cursor-disabled" disabled /><label class="mo-saml-switch-label" for="switch">Toggle</label>

                        <p class="mt-2">Enable this option if you do not want to assign any roles to unmapped users.</p>
                    </div>
                </div>
            </div>

            <div class="row d-block prem-info mt-5">
                <div class="prem-icn role-admin-prem-img"><img src="<?php echo plugin_dir_url(mo_hsso_options_plugin_constants::PLUGIN_FILE) . 'images/crown.png' ?>" width="35px">
                    <p class="role-admin-prem-text">Customized Role Mapping options are configurable in the Premium, Enterprise and All-Inclusive versions of the plugin. <a href="<?php echo admin_url('admin.php?page=mo_hsso_settings&tab=licensing'); ?>" class="text-warning">Click here to upgrade</a></p>
                </div>
                <?php
                foreach ($roles as $role_value => $role_name) {
                ?>
                    <div class="row align-items-top mt-4">
                        <div class="col-md-3">
                            <h6 class="text-secondary"><?php echo $role_name; ?> </span>:</h6>
                        </div>
                        <div class="col-md-7">
                            <input type="text" name="" placeholder="Semi-colon(;) separated Group/Role value for <?php echo $role_name; ?>" class="w-100 bg-light cursor-disabled" value="" disabled>
                        </div>
                    </div>
                <?php
                }
                ?>
            </div>
        </div>
    </form>

    <?php

}

function mo_hsso_display_attrs_list()
{
    $idp_attrs = get_option('mo_saml_test_config_attrs');
    $idp_attrs = maybe_unserialize($idp_attrs);
    if (!empty($idp_attrs)) { ?>
        <div class="bg-white text-center shadow-cstm rounded contact-form-cstm p-4">
            <h4><?php _e('Attributes sent by the Identity Provider', 'Headless-Single-Sign-On'); ?>:</h4>
            <div>
                <table style="table-layout: fixed;border: 1px solid #fff;width: 100%;background-color: #e9f0ff;">
                    <tr style="text-align:center;background:#d3e1ff;">
                        <td style="font-weight:bold;padding: 2%;word-break: break-word;border:2.5px solid #fff;"><?php _e('ATTRIBUTE NAME', 'Headless-Single-Sign-On'); ?></td>
                        <td style="font-weight:bold;padding: 2%;word-break: break-word;border:2.5px solid #fff;"><?php _e('ATTRIBUTE VALUE', 'Headless-Single-Sign-On'); ?></td>
                    </tr>
                    <?php foreach ($idp_attrs as $attr_name => $values) { ?>
                        <tr style="text-align:center;">
                            <td style="font-weight:bold;border:2.5px solid #fff;padding:2%; word-wrap:break-word;"> <?php echo $attr_name; ?></td>
                            <td style="padding:2%;border:2.5px solid #fff; word-wrap:break-word;"> <?php echo implode("<hr/>", $values); ?> </td>
                        </tr>
                    <?php } ?>

                </table>
                <br />
                <p style="text-align:center;"><input type="button" class="btn-cstm rounded mt-3" value="<?php _e('Clear Attributes List', 'Headless-Single-Sign-On'); ?>" onclick="document.forms['attrs_list_form'].submit();"></p>
                <div style="padding-right:8px;">
                    <p><b><?php _e('NOTE', 'Headless-Single-Sign-On'); ?> :</b> <?php _e('Please clear this list after configuring the plugin to hide your confidential attributes.', 'Headless-Single-Sign-On'); ?><br />
                        <?php _e('Click on <b>Test configuration</b> in <b>Service Provider Setup</b> tab to populate the list again.', 'Headless-Single-Sign-On'); ?></p>
                </div>
                <form method="post" action="" id="attrs_list_form">
                    <?php wp_nonce_field('clear_attrs_list'); ?>
                    <input type="hidden" name="option" value="clear_attrs_list">
                </form>
            </div>
        </div>
<?php
    }
}
