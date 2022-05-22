<?php

function mo_hsso_configuration_steps()
{
    $sp_base_url = site_url();
    $acs_url = $sp_base_url . '/';
    $sp_entity_id = get_option('mo_saml_sp_entity_id') ?: $sp_base_url . '/wp-content/plugins/miniorange-saml-20-single-sign-on/';
    $sp_metadata_url = $sp_base_url . '/?option=mosaml_metadata';
    ?>
    <!-- <form  name="saml_form_am" method="post" action="" id="mo_saml_idp_config">-->
    <div class="row container-fluid" id="sp-meta-tab-form">
        <div class="col-md-8 mt-4 ml-5">
            <?php
            mo_hsso_display_sp_metadata($sp_entity_id, $acs_url, $sp_metadata_url);
            mo_hsso_display_sp_endpoints_config($sp_base_url, $sp_entity_id);
            ?>
        </div>
        <?php mo_hsso_display_support_form(); ?>
    </div>
    <?php
}

function mo_hsso_display_sp_endpoints_config($sp_base_url, $sp_entity_id)
{
    ?>
    <form width="98%" method="post" id="mo_saml_update_idp_settings_form" action="">
        <?php wp_nonce_field('mo_saml_update_idp_settings_option'); ?>
        <input type="hidden" name="option" value="mo_saml_update_idp_settings_option" />
        <div class="p-4 shadow-cstm bg-white rounded mt-4">
            <div class="row align-items-top">
                <div class="col-md-12 entity-info">
                    <h4 class="form-head">Service Provider Endpoints</h4>
                </div>
            </div>
            <div class="row align-items-top mt-5">
                <div class="col-md-3">
                    <h6 class="text-secondary">SP EntityID / Issuer :</h6>
                </div>
                <div class="col-md-9">
                    <input type="text" name="mo_saml_sp_entity_id" placeholder="Enter Service Provider Entity ID" class="w-100" value="<?php echo $sp_entity_id; ?>" required>
                    <p class="mt-2"><b>Note:</b> If you have already shared the below URLs or Metadata with your IdP, do <b>NOT</b> change SP EntityID. It might break your existing login flow.</p>
                </div>
            </div>
            <div class="row align-items-center mt-5 mt-4 rounded prem-info">
                <div class="prem-icn"><img src="<?php echo plugin_dir_url(mo_hsso_options_plugin_constants::PLUGIN_FILE) . 'images/crown.png' ?>" width="35px">
                    <p class="prem-info-text">Configurable ACS URL / SP Base URL available in the <b>Paid</b> versions of the plugin. <a href="<?php echo admin_url('admin.php?page=mo_hsso_settings&tab=licensing'); ?>" class="text-warning">Click here to upgrade</a></p>
                </div>
                <div class="col-md-3">
                    <h6 class="text-secondary">SP Base URL :</h6>
                </div>
                <div class="col-md-9">
                    <input type="text" placeholder="You site base URL" class="w-50 bg-light cursor-disabled" value="<?php echo $sp_base_url; ?>" disabled="">
                </div>
            </div>
            <div class="row align-items-center justify-content-center mt-5 mt-4">
                <input type="submit" class="btn-cstm bg-info rounded" name="submit" value="Update">
            </div>
        </div>
    </form>
    <?php
}

function mo_hsso_display_sp_metadata($sp_entity_id, $acs_url, $sp_metadata_url)
{
    ?>
    <div class="p-4 shadow-cstm bg-white rounded">
        <div class="row align-items-top">
            <div class="col-md-12">
                <h4 class="form-head">
                    <span class="entity-info">Provide Metadata <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                        <path d="M5.255 5.786a.237.237 0 0 0 .241.247h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286zm1.557 5.763c0 .533.425.927 1.01.927.609 0 1.028-.394 1.028-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94z" />
                    </svg>
                <span class="entity-info-text mo-saml-provide-metadata-txt">Configure Service Provider Metadata. <a href="https://developers.miniorange.com/docs/saml/wordpress/Service-Provider-Metadata" class="text-warning" target="_blank">Click here to know how this is useful</a></span>
                </span>
                </h4>
            </div>
        </div>
        <h5 class="form-head form-head-bar mt-5">Provide Metadata URL</h5>
        <div class="row align-items-center mt-5 mt-4">
            <div class="col-md-3">
                <h6 class="mt-2">Metadata URL :</h6>
            </div>
            <div class="col-md-9 d-inline-flex align-items-center">
                <code class="mr-2 rounded p-2 bg-cstm"><b><a id="sp_metadata_url" target="_blank" href="<?php echo $sp_metadata_url; ?>" class="text-dark"><?php echo $sp_metadata_url; ?></a></b></code>
                <i class="icon-copy mo_copy copytooltip rounded-circle" onclick="copyToClipboard(this, '#sp_metadata_url', '#metadata_url_copy');"><span id="metadata_url_copy" class="copytooltiptext">Copy to Clipboard</span></i>
            </div>
        </div>
        <div class="row align-items-top mt-5 mt-5">
            <div class="col-md-3">
                <h6>Metadata XML File :</h6>
            </div>
            <div class="col-md-7">
                <a class="btn-cstm bg-info rounded" onclick="document.forms['mo_saml_download_metadata'].submit();">Download</a>
            </div>
        </div>
        <div class="text-center">
            <div class="mt-5 form-head form-head-bar form-sep"><span class="bg-secondary rounded-circle p-2 text-white">OR</span></div>
        </div>
        <div class="row align-items-baseline">
            <div class="col-md-6">
                <h5 class="form-head form-head-bar mt-5">Note the following to configure the IDP</h5>
            </div>
            <div class="col-md-6 text-right">
                <a href="https://plugins.miniorange.com/wordpress-saml-guides" class="btn btn-cstm ml-3" target="_blank"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-book" viewBox="0 0 16 16">
                        <path d="M1 2.828c.885-.37 2.154-.769 3.388-.893 1.33-.134 2.458.063 3.112.752v9.746c-.935-.53-2.12-.603-3.213-.493-1.18.12-2.37.461-3.287.811V2.828zm7.5-.141c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492V2.687zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783z"></path>
                    </svg>&nbsp; All IDP Setup Guides</a>
            </div>
        </div>
        <table class="meta-data-table rounded mt-5">
            <tbody>
            <tr>
                <td><b>SP-EntityID / Issuer</b></td>
                <td>
                    <table class="w-100">
                        <tbody>
                        <tr>
                            <td><span id="entity_id"><?php echo $sp_entity_id; ?></span></td>
                            <td><i class="icon-copy mo_copy copytooltip rounded-circle float-right" onclick="copyToClipboard(this, '#entity_id', '#entity_id_copy');"><span id="entity_id_copy" class="copytooltiptext">Copy to Clipboard</span></i></td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td><b>ACS (AssertionConsumerService) URL</b></td>
                <td>
                    <table class="w-100">
                        <tbody>
                        <tr>
                            <td><span id="base_url"><?php echo $acs_url; ?></span></td>
                            <td><i class="icon-copy mo_copy copytooltip rounded-circle float-right" onclick="copyToClipboard(this, '#base_url', '#base_url_copy');"><span id="base_url_copy" class="copytooltiptext">Copy to Clipboard</span></i></td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td><b>Audience URI</b></td>
                <td>
                    <table class="w-100">
                        <tbody>
                        <tr>
                            <td><span id="audience"><?php echo $sp_entity_id; ?></span></td>
                            <td><i class="icon-copy mo_copy copytooltip rounded-circle float-right" onclick="copyToClipboard(this, '#audience','#audience_copy');"><span id="audience_copy" class="copytooltiptext">Copy to Clipboard</span></i></td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td><b>NameID format</b></td>
                <td>
                    <table class="w-100">
                        <tbody>
                        <tr>
                            <td><span id="nameid">
                                            urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified
                                        </span></td>
                            <td><i class="icon-copy mo_copy copytooltip rounded-circle float-right" onclick="copyToClipboard(this, '#nameid', '#nameid_copy');"><span id="nameid_copy" class="copytooltiptext">Copy to Clipboard</span></i></td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td><b>Recipient URL</b></td>
                <td>
                    <table class="w-100">
                        <tbody>
                        <tr>
                            <td><span id="recipient"><?php echo $acs_url; ?></span></td>
                            <td><i class="icon-copy mo_copy copytooltip rounded-circle float-right" onclick="copyToClipboard(this, '#recipient','#recipient_copy');"><span id="recipient_copy" class="copytooltiptext">Copy to Clipboard</span></i></td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td style="width:40%; padding: 15px;font-weight: 400"><b>Destination URL</b></td>
                <td>
                    <table class="w-100">
                        <tbody>
                        <tr>
                            <td><span id="destination"><?php echo $acs_url; ?></span></td>
                            <td><i class="icon-copy mo_copy copytooltip rounded-circle float-right" onclick="copyToClipboard(this, '#destination','#destination_copy');"><span id="destination_copy" class="copytooltiptext">Copy to Clipboard</span></i></td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td class="p-3"><b>Default Relay State (Optional)</b></td>
                <td>
                    <table class="w-100">
                        <tbody>
                        <tr>
                            <td><a href="<?php echo admin_url('admin.php?page=mo_hsso_settings&tab=licensing'); ?>" class="btn btn-cstm ml-3">Premium</a></td>
                            <td class="text-right"><img src="<?php echo plugin_dir_url(mo_hsso_options_plugin_constants::PLUGIN_FILE) . 'images/crown.png' ?>" width="20px">&nbsp;</td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td class="p-3"><b>Certificate (Optional)</b></td>
                <td>
                    <table class="w-100">
                        <tbody>
                        <tr>
                            <td><a href="<?php echo admin_url('admin.php?page=mo_hsso_settings&tab=licensing'); ?>" class="btn btn-cstm ml-3">Premium</a></td>
                            <td class="text-right"><img src="<?php echo plugin_dir_url(mo_hsso_options_plugin_constants::PLUGIN_FILE) . 'images/crown.png' ?>" width="20px">&nbsp;</td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
        <!-- <h6>Provide this metadata URL to your Identity Provider or download the .xml file to upload it in your idp:</h6> -->

    </div>
    <form name="mo_saml_download_metadata" method="post" action="">
        <?php wp_nonce_field("mosaml_metadata_download"); ?>
        <input type="hidden" name="option" value="mosaml_metadata_download" />

    </form>
    <?php
}
