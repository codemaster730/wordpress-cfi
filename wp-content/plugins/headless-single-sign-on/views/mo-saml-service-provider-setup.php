<?php

function mo_hsso_apps_config_saml()
{

    //Broker Service
    $saml_identity_name    = get_option('saml_identity_name');
    $saml_login_url        = get_option('saml_login_url');
    $saml_issuer           = get_option('saml_issuer');
    $saml_x509_certificate = maybe_unserialize(get_option('saml_x509_certificate'));
    $saml_x509_certificate = !is_array($saml_x509_certificate) ? array(0 => $saml_x509_certificate) : $saml_x509_certificate;
    $mo_saml_identity_provider_identifier_name = get_option('mo_saml_identity_provider_identifier_name') ? get_option('mo_saml_identity_provider_identifier_name') : "";
    $idp_data = new stdClass();

    if (!empty($mo_saml_identity_provider_identifier_name)) {
        if (array_key_exists($mo_saml_identity_provider_identifier_name, mo_hsso_options_plugin_idp_specific_ads::$idp_specific_ads)) {
            $idp_array = mo_hsso_options_plugin_idp_specific_ads::$idp_specific_ads[$mo_saml_identity_provider_identifier_name];
            $idp_data->ads_text = $idp_array['Text'];
            $idp_data->ads_heading = $idp_array['Heading'];
            $idp_data->ads_link = $idp_array["Link"];
        }
        if (array_key_exists($mo_saml_identity_provider_identifier_name, mo_hsso_options_plugin_idp::$IDP_GUIDES)) {
            $idp_guides_array = mo_hsso_options_plugin_idp::$IDP_GUIDES[$mo_saml_identity_provider_identifier_name];
            $idp_key = $idp_guides_array[0];
            $idp_data->idp_guide_link = 'https://plugins.miniorange.com/' . $idp_guides_array[1];
            $idp_data->image_src = plugin_dir_url(mo_hsso_options_plugin_constants::PLUGIN_FILE) . 'images/idp-guides-logos/' . $idp_key . '.png';
            if ($idp_key == array_key_exists($idp_key, mo_hsso_options_plugin_idp_videos::$IDP_VIDEOS)) {
                $idp_data->idp_video_link = 'https://www.youtube.com/watch?v=' . mo_hsso_options_plugin_idp_videos::$IDP_VIDEOS[$idp_key];
            }
        }
    }

    $saml_is_encoding_enabled = get_option('mo_saml_encoding_enabled') !== false ? get_option('mo_saml_encoding_enabled') : 'checked';

?>
    <div class="row container-fluid" id="cstm-idp-section">
        <div class="col-md-8 mt-4 ml-5">
            <?php mo_hsso_display_idp_selector();
            mo_hsso_display_sp_configuration($saml_identity_name, $saml_login_url, $saml_issuer, $saml_x509_certificate, $mo_saml_identity_provider_identifier_name, $saml_is_encoding_enabled, $idp_data);
            ?>

        </div>
        <script>
            addCertificateErrorClass();
        </script>
        <?php mo_hsso_display_support_form(); ?>
    </div>
<?php
}

function mo_hsso_display_idp_selector()
{
?>
    <div class="pt-3 pr-5 pb-5 pl-5 shadow-cstm bg-cstm rounded">
        <div class="row align-items-center pb-3">
            <div class="col-md-12">
                <input class="idp-search-box rounded-0" id="mo_saml_search_idp_list" type="text" placeholder="Search and select your IDP" value="">
                <span class="idp-search-glass"><span role="img" aria-label="Search" class="css-wl6vgu" style="--icon-primary-color:currentColor; --icon-secondary-color:var(--background-default, #FFFFFF);"><svg width="24" height="24" viewBox="0 0 24 24" role="presentation">
                            <path d="M16.436 15.085l3.94 4.01a1 1 0 01-1.425 1.402l-3.938-4.006a7.5 7.5 0 111.423-1.406zM10.5 16a5.5 5.5 0 100-11 5.5 5.5 0 000 11z" fill="currentColor" fill-rule="evenodd"></path>
                        </svg></span></span>
            </div>
        </div>
        <div class="text-center show-msg" style="display: none;">
            <h6>Choose Custom IDP if you don't find your IDP</h6>
        </div>
        <div class="row">
            <div class="col-md-12 text-center rounded mo-saml-scroll-cstm">
                <div class="row justify-content-center pb-2" id="mo_saml_idps_grid_div">
                    <?php
                    $image_path = ".." . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "idp-guides-logos" . DIRECTORY_SEPARATOR;
                    foreach (mo_hsso_options_plugin_idp::$IDP_GUIDES as $key => $value) {
                        $idp_videos = mo_hsso_options_plugin_idp_videos::$IDP_VIDEOS;
                        $idp_video_index = $idp_videos[$value[0]];
                    ?>
                        <div class="col-md-2 logo-saml-cstm" data-idp="<?php echo $idp_video_index ?>">
                            <a target="_blank" data-idp-value="<?php echo $idp_video_index ?>" data-href="https://plugins.miniorange.com/<?php echo $value[1]; ?>" data-video="https://www.youtube.com/watch?v=<?php echo $idp_video_index ?>">
                                <img loading="lazy" width="30px" src="<?php echo plugins_url($image_path . $value[0] . '.png', __FILE__); ?>">
                                <br>
                                <h6 class="mt-2"><?php echo $key ?></h6>
                            </a>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" name="idp_specific_ads" id="idp_specific_ads" value='<?php echo json_encode(mo_hsso_options_plugin_idp_specific_ads::$idp_specific_ads) ?>' />
<?php
}

function mo_hsso_display_sp_configuration($saml_identity_name, $saml_login_url, $saml_issuer, $saml_x509_certificate, $mo_saml_identity_provider_identifier_name, $saml_is_encoding_enabled, $idp_data)
{
?>
    <div class="p-4 shadow-cstm bg-white rounded mt-4" id="idp_scroll_saml">
        <div class="row align-items-top">
            <div class="col-md-12">
                <h4 class="form-head">
                    <span class="entity-info">Configure Service Provider <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                            <path d="M5.255 5.786a.237.237 0 0 0 .241.247h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286zm1.557 5.763c0 .533.425.927 1.01.927.609 0 1.028-.394 1.028-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94z" />
                        </svg>
                        <span class="entity-info-text">Configure Service Provider using either of the following options. <a href="https://developers.miniorange.com/docs/saml/wordpress/Service-Provider-Setup" class="text-warning" target="_blank">Click here to know how this is useful</a></span>
                    </span>
                </h4>
            </div>
        </div>
        <div class="row align-items-center mt-5 mb-5" id="mo_saml_selected_idp_div" style="display: none;">
            <div class="col-md-4">
                <div class="text-center rounded w-50 shadow-cstm p-1" id="mo_saml_selected_idp_icon_div">
                    <img width="55" src="" alt="" class="p-1">
                </div>
            </div>
            <div class="col-md-4">
                <a target="_blank" href="" id="saml_idp_guide_link" class="text-white pl-4 pr-4 pt-2 pb-2 rounded bg-info"><svg width="16" height="16" fill="currentColor" class="bi bi-wrench" viewBox="0 0 16 16">
                        <path d="M.102 2.223A3.004 3.004 0 0 0 3.78 5.897l6.341 6.252A3.003 3.003 0 0 0 13 16a3 3 0 1 0-.851-5.878L5.897 3.781A3.004 3.004 0 0 0 2.223.1l2.141 2.142L4 4l-1.757.364L.102 2.223zm13.37 9.019.528.026.287.445.445.287.026.529L15 13l-.242.471-.026.529-.445.287-.287.445-.529.026L13 15l-.471-.242-.529-.026-.287-.445-.445-.287-.026-.529L11 13l.242-.471.026-.529.445-.287.287-.445.529-.026L13 11l.471.242z" />
                    </svg> &nbsp;Setup Guide</a>
            </div>
            <div class="col-md-4">
                <a target="_blank" href="" id="saml_idp_video_link" class="text-white pl-4 pr-4 pt-2 pb-2 rounded bg-danger"><svg width="16" height="16" fill="currentColor" class="bi bi-youtube" viewBox="0 0 16 16">
                        <path d="M8.051 1.999h.089c.822.003 4.987.033 6.11.335a2.01 2.01 0 0 1 1.415 1.42c.101.38.172.883.22 1.402l.01.104.022.26.008.104c.065.914.073 1.77.074 1.957v.075c-.001.194-.01 1.108-.082 2.06l-.008.105-.009.104c-.05.572-.124 1.14-.235 1.558a2.007 2.007 0 0 1-1.415 1.42c-1.16.312-5.569.334-6.18.335h-.142c-.309 0-1.587-.006-2.927-.052l-.17-.006-.087-.004-.171-.007-.171-.007c-1.11-.049-2.167-.128-2.654-.26a2.007 2.007 0 0 1-1.415-1.419c-.111-.417-.185-.986-.235-1.558L.09 9.82l-.008-.104A31.4 31.4 0 0 1 0 7.68v-.123c.002-.215.01-.958.064-1.778l.007-.103.003-.052.008-.104.022-.26.01-.104c.048-.519.119-1.023.22-1.402a2.007 2.007 0 0 1 1.415-1.42c.487-.13 1.544-.21 2.654-.26l.17-.007.172-.006.086-.003.171-.007A99.788 99.788 0 0 1 7.858 2h.193zM6.4 5.209v4.818l4.157-2.408L6.4 5.209z" />
                    </svg> &nbsp;Video Guide</a>
            </div>
        </div>
        <div class="mo-saml-sp-tab-container mt-4">
            <ul class="row switch-tab-sp text-center">
                <li class="mo-saml-current"><a href="#mo-saml-idp-manual-tab" class="btn">Enter IDP Metadata Manually</a></li>

                <li class="col-md-2 or">OR</li>
                <li><a href="#mo-saml-upload-idp-tab" class="btn">Upload IDP Metadata</a></li>
            </ul>

            <div class="mo-saml-sp-tab">
                <input type="hidden" id="mo-saml-test-window-url" value="<?php echo mo_hsso_get_test_url(); ?>">
                <input type="hidden" id="mo-saml-attribute-mapping-url" value="<?php echo mo_hsso_get_attribute_mapping_url(); ?>">
                <input type="hidden" id="mo-saml-service-provider-url" value="<?php echo mo_hsso_get_service_provider_url(); ?>">
                <input type="hidden" id="mo-saml-redirect-sso-url" value="<?php echo mo_hsso_get_redirection_sso_url(); ?>">
                <form method="post" action="">
                    <?php
                    if (function_exists('wp_nonce_field'))
                        wp_nonce_field('login_widget_saml_save_settings'); ?>
                    <input type="hidden" name="option" value="login_widget_saml_save_settings" />
                    <div id="mo-saml-idp-manual-tab" class="mo-saml-tab-content">
                        <input type="hidden" name="mo_saml_identity_provider_identifier_name" id="mo_saml_identity_provider_identifier_name" value="<?php echo $mo_saml_identity_provider_identifier_name; ?>" />
                        <input type="hidden" name="mo_saml_identity_provider_identifier_details" id="mo_saml_identity_provider_identifier_details" value='<?php echo (isset($idp_data)) ? json_encode($idp_data) : ""; ?>' />
                        <div class="row align-items-top mt-5">
                            <div class="col-md-3 pr-0">
                                <h6 class="text-secondary">Identity Provider Name </span>:</h6>
                            </div>
                            <div class="col-md-7">
                                <input type="text" name="saml_identity_name" placeholder="Identity Provider name like ADFS, SimpleSAML, Salesforce" class="w-100" value="<?php echo $saml_identity_name; ?>" required title="Only alphabets, numbers and underscore is allowed" pattern="\w+">
                            </div>
                        </div>
                        <div class="row align-items-top mt-5">
                            <div class="col-md-3">
                                <h6 class="text-secondary">IdP Entity ID or Issuer </span>:</h6>
                            </div>
                            <div class="col-md-7">
                                <input type="text" name="saml_issuer" id="saml_issuer" placeholder="Identity Provider Entity ID or Issuer" class="w-100" value="<?php echo $saml_issuer; ?>" required="">
                                <p class="mt-2"><b>Note</b> : You can find the <b>EntityID</b> in Your IdP-Metadata XML file enclosed in <code>EntityDescriptor</code> tag having attribute as <code>entityID</code></p>
                            </div>
                        </div>
                        <div class="row align-items-top mt-5">
                            <div class="col-md-3">
                                <h6 class="text-secondary">SAML Login URL </span>:</h6>
                            </div>
                            <div class="col-md-7">
                                <input type="url" name="saml_login_url" placeholder="Single Sign On Service URL (HTTP-Redirect binding) of your IdP" class="w-100" value="<?php echo $saml_login_url; ?>" required="">
                                <p class="mt-2"><b>Note</b> : You can find the <b>SAML Login URL</b> in Your IdP-Metadata XML file enclosed in <code>SingleSignOnService</code> tag (Binding type: HTTP-Redirect)</p>
                            </div>
                        </div>
                        <?php
                        foreach ($saml_x509_certificate as $key => $value) {
                        ?>
                            <div class="row align-items-top mt-5">
                                <div class="col-md-3">
                                    <h6 class="text-secondary">X.509 Certificate </span>:</h6>
                                </div>
                                <div class="col-md-7">
                                    <textarea rows="4" cols="5" name="saml_x509_certificate[<?php echo $key; ?>]" id="saml_x509_certificate" onkeyup="removeCertificateErrorClass();" placeholder="Copy and Paste the content from the downloaded certificate or copy the content enclosed in X509Certificate tag (has parent tag KeyDescriptor use=signing) in IdP-Metadata XML file" class="w-100" required=""><?php echo $value; ?></textarea>


                                    <span class="mo-saml-error-tip">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#ffa300" class="bi bi-exclamation-square-fill" viewBox="0 0 16 16">
                                            <path d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2zm6 4c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995A.905.905 0 0 1 8 4zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"></path>
                                        </svg>&nbsp; Invalid Certificate
                                    </span>

                                    <p class="mt-2"><b>Note</b> : Format of the certificate - <br><b class="text-secondary">-----BEGIN CERTIFICATE-----<br>XXXXXXXXXXXXXXXXXXXXXXXXXXX<br>-----END
                                            CERTIFICATE-----</b></p>
                                </div>
                            </div>
                        <?php
                        }
                        ?>
                        <div class="row align-items-top mt-5">
                            <div class="col-md-3">
                                <h6 class="text-secondary">Character encoding :</h6>
                            </div>
                            <div class="col-md-8">

                                <input type="checkbox" id="switch" name="enable_iconv" class="mo-saml-switch" <?php echo $saml_is_encoding_enabled; ?> /><label class="mo-saml-switch-label" for="switch">Toggle</label>

                                <p class="mt-2"><b>Note</b> : Uses iconv encoding to convert X509 certificate into correct encoding.</p>
                            </div>
                        </div>
                        <div class="row align-items-top mt-2">
                            <div class="col-md-3"></div>
                            <div class="col-md-9">
                                <input type="submit" class="btn btn-cstm rounded mt-3 mr-3 w-176" name="submit" value="Save">
                                <input type="button" class="btn btn-cstm rounded mt-3 w-176" id="test_config" <?php if (!mo_hsso_is_sp_configured() || !get_option('saml_x509_certificate') || !mo_hsso_is_openssl_installed())
                                                                                                                    echo 'disabled'; ?> title="You can only test your Configuration after saving your Service Provider Settings." onclick="showTestWindow();" value="Test Configuration">
                            </div>
                            <div class="col-md-3">

                            </div>
                        </div>
                        <div class="row align-items-top mt-2">
                            <div class="col-md-3"></div>
                            <div class="col-md-9">
                                <input type="button" class="btn btn-cstm rounded mt-3 w-372" name="saml_request" id="export-import-config" <?php if (!mo_hsso_is_sp_configured() || !get_option('saml_x509_certificate')) {
                                                                                                                                                echo 'disabled';
                                                                                                                                            } ?> title="Export Plugin Configuration" value="Export Plugin Configuration" onclick="jQuery('#mo_export').submit();">
                            </div>
                        </div>
                    </div>
                </form>
                <form method="post" action="" name="mo_export" id="mo_export">
                    <?php
                    wp_nonce_field('mo_hsso_export'); ?>
                    <input type="hidden" name="option" value="mo_hsso_export" />
                </form>

                <div id="mo-saml-upload-idp-tab" class="mo-saml-tab-content">
                    <form name="saml_upload_metadata_form" method="post" id="saml_upload_metadata_form" action="<?php echo admin_url('admin.php?page=mo_hsso_saml_settings&tab=save'); ?>" enctype="multipart/form-data">
                        <input type="hidden" name="option" value="saml_upload_metadata" />
                        <?php wp_nonce_field("saml_upload_metadata"); ?>
                        <div class="row align-items-center mt-5">
                            <div class="col-md-3 pr-0">
                                <h6 class="text-secondary">Identity Provider Name </span>:</h6>
                            </div>
                            <div class="col-md-7">
                                <input type="text" name="saml_identity_metadata_provider" placeholder="Identity Provider name like ADFS, SimpleSAML, Salesforce" class="w-100" value="" required="" title="Only alphabets, numbers and underscore is allowed" pattern="\w+">
                            </div>
                        </div>
                        <div class="row align-items-center mt-5">
                            <div class="col-md-3">
                                <h6 class="text-secondary">Upload Metadata :</h6>
                            </div>
                            <div class="col-md-4">
                                <input type="file" id="metadata_file" name="metadata_file" required>
                            </div>
                            <div class="col-md-4">
                                <button type="button" value="Upload" onclick="checkMetadataFile();" class="btn btn-cstm rounded d-flex align-items-center"><svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z" />
                                        <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708l3-3z" />
                                    </svg>&nbsp;&nbsp;Upload</button>
                            </div>
                        </div>
                        <div class="text-center">
                            <div class="mt-5 form-head form-head-bar form-sep"><span class="bg-secondary rounded-circle p-2 text-white">OR</span></div>
                        </div>
                        <div class="row align-items-center mt-5">
                            <div class="col-md-3">
                                <h6 class="text-secondary">Enter metadata URL :</h6>
                            </div>
                            <div class="col-md-4">
                                <input type="url" name="metadata_url" onkeypress="checkUploadMetadataFields();" id="metadata_url" placeholder="Enter metadata URL of your IdP" class="w-100" value="" required>
                            </div>
                            <div class="col-md-4">
                                <button type="button" value="Fetch" onclick="checkMetadataUrl();" class="btn btn-cstm rounded d-flex align-items-center"><svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd" d="M3.5 6a.5.5 0 0 0-.5.5v8a.5.5 0 0 0 .5.5h9a.5.5 0 0 0 .5-.5v-8a.5.5 0 0 0-.5-.5h-2a.5.5 0 0 1 0-1h2A1.5 1.5 0 0 1 14 6.5v8a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 14.5v-8A1.5 1.5 0 0 1 3.5 5h2a.5.5 0 0 1 0 1h-2z"></path>
                                        <path fill-rule="evenodd" d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"></path>
                                    </svg>&nbsp;&nbsp;Fetch Metadata</button>
                            </div>
                        </div>
                        <input type="submit" id="metadata-submit-button" style="display:none" />
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php
}
