<?php
include_once 'Import-export.php';
foreach (glob(plugin_dir_path(__FILE__).'views'.DIRECTORY_SEPARATOR.'*.php') as $filename)
{
    include_once $filename;
}
function mo_hsso_register_saml_sso() {
    if ( isset( $_GET['tab'] ) ) {
        $active_tab = $_GET['tab'];
        if($active_tab== 'addons')
        {
            echo "<script type='text/javascript'>
            highlightAddonSubmenu();
            </script>";
        }
        else if($active_tab=='hsso')
        {
            echo "<script type='text/javascript'>
            highlightHsso();
            </script>";
        }
    } else if ( mo_hsso_is_customer_registered_saml() ) {
        $active_tab = 'save';
    } else {
        $active_tab = 'login';
    }
    ?>
    <?php

    mo_hsso_display_plugin_dependency_warning();

    ?>
    <div id="mo_hsso_settings" >
        <?php
        if($active_tab!=='licensing' && (isset($_REQUEST['page']) && $_REQUEST['page'] !== 'mo_saml_licensing'))
        {
            mo_hsso_display_plugin_header($active_tab);
        }
        ?>
    </div>
    <?php mo_hsso_display_plugin_tabs($active_tab);

}

function mo_hsso_is_curl_installed() {
    if ( in_array( 'curl', get_loaded_extensions() ) ) {
        return 1;
    } else {
        return 0;
    }
}

function mo_hsso_is_openssl_installed() {

    if ( in_array( 'openssl', get_loaded_extensions() ) ) {
        return 1;
    } else {
        return 0;
    }
}

function mo_hsso_is_dom_installed(){

    if ( in_array( 'dom', get_loaded_extensions() ) ) {
        return 1;
    } else {
        return 0;
    }
}

function mo_hsso_is_iconv_installed(){

    if ( in_array( 'iconv', get_loaded_extensions() ) ) {
        return 1;
    } else {
        return 0;
    }
}

function mo_hsso_get_attribute_mapping_url(){

    return add_query_arg( array('tab' => 'opt'), $_SERVER['REQUEST_URI'] );
}

function mo_hsso_get_service_provider_url(){

        return add_query_arg( array('tab' => 'save'), $_SERVER['REQUEST_URI'] );

}
function mo_hsso_get_redirection_sso_url(){
    return add_query_arg( array('tab' => 'general'), $_SERVER['REQUEST_URI'] );
}


function mo_hsso_get_test_url() {

        $url = site_url() . '/?option=testConfig';


    return $url;
}

function mo_hsso_is_customer_registered_saml($check_guest=true) {

    $email       = get_option( 'mo_saml_admin_email' );
    $customerKey = get_option( 'mo_saml_admin_customer_key' );

    if(mo_hsso_is_guest_enabled() && $check_guest)
        return 1;
    if ( ! $email || ! $customerKey || ! is_numeric( trim( $customerKey ) ) ) {
        return 0;
    } else {
        return 1;
    }
}

function mo_hsso_is_guest_enabled(){
    $guest_enabled = get_option('mo_saml_guest_enabled');

    return $guest_enabled;
}

function mo_hsso_is_sp_configured() {
    $saml_login_url = get_option( 'saml_login_url' );


    if ( empty( $saml_login_url ) ) {
        return 0;
    } else {
        return 1;
    }
}

function mo_hsso_download_logs($error_msg,$cause_msg) {

    echo '<div style="font-family:Calibri;padding:0 3%;">';
    echo '<hr class="header"/>';
    echo '          <p style="font-size: larger       ">' . __('Please try the solution given above.If the problem persists,download the plugin configuration by clicking on Export Plugin Configuration and mail us at <a href="mailto:info@xecurify.com">info@xecurify.com</a>','Headless-Single-Sign-On') . '.</p>
                    <p>' . __('We will get back to you soon!','Headless-Single-Sign-On') . '<p>
                    </div>
                    <div style="margin:3%;display:block;text-align:center;">
                    <div style="margin:3%;display:block;text-align:center;">
                    <form method="get" action="" name="mo_export" id="mo_export">';
                    wp_nonce_field('mo_hsso_export');
				echo '<input type="hidden" name="option" value="export_configuration" />
				<input type="submit" class="miniorange-button" value="' . __('Export Plugin Configuration','Headless-Single-Sign-On') . '">
				<input class="miniorange-button" type="button" value="' . __('Close','Headless-Single-Sign-On') . '" onclick="self.close()"></form>
                
               ';
    echo '&nbsp;&nbsp;';

    $samlResponse = htmlspecialchars($_POST['SAMLResponse']);
    update_option('MO_SAML_RESPONSE',$samlResponse);
    $error_array  = array("Error"=>$error_msg,"Cause"=>$cause_msg);
    update_option('MO_SAML_TEST',$error_array);
    update_option('MO_SAML_TEST_STATUS',0);
    ?>
    <style>
    .miniorange-button {
    padding:1%;
    background: linear-gradient(0deg,rgb(14 42 71) 0,rgb(26 69 138) 100%)!important;
    cursor: pointer;font-size:15px;
    border-width: 1px;border-style: solid;
    border-radius: 3px;white-space: nowrap;
    box-sizing: border-box;
    box-shadow: 0px 1px 0px rgba(120, 200, 230, 0.6) inset;color: #FFF;
    margin: 22px;
    }
</style>
    <?php

    exit();

}

function mo_hsso_add_query_arg($query_arg, $url){
    if(strpos($url, 'mo_saml_licensing') !== false){
        $url = str_replace('mo_saml_licensing', 'mo_hsso_settings', $url);
    }
    else if (strpos($url, 'mo_saml_enable_debug_logs') !== false){
	    $url = str_replace('mo_saml_enable_debug_logs', 'mo_hsso_settings', $url);
    }
    $url = add_query_arg($query_arg, $url);
    return $url;
}

function mo_hsso_miniorange_generate_metadata($download=false) {

    $sp_base_url = get_option( 'mo_saml_sp_base_url' );
    if ( empty( $sp_base_url ) ) {
        $sp_base_url = site_url();
    }
    if ( substr( $sp_base_url, - 1 ) == '/' ) {
        $sp_base_url = substr( $sp_base_url, 0, - 1 );
    }
    $sp_entity_id = get_option( 'mo_saml_sp_entity_id' );
    if ( empty( $sp_entity_id ) ) {
        $sp_entity_id = $sp_base_url . '/wp-content/plugins/miniorange-saml-20-single-sign-on/';
    }

    $entity_id   = $sp_entity_id;
    $acs_url     = $sp_base_url . '/';

    if(ob_get_contents())
        ob_clean();
    header( 'Content-Type: text/xml' );
    if($download)
            header('Content-Disposition: attachment; filename="Metadata.xml"');
    echo '<?xml version="1.0"?>
<md:EntityDescriptor xmlns:md="urn:oasis:names:tc:SAML:2.0:metadata" validUntil="2022-10-28T23:59:59Z" cacheDuration="PT1446808792S" entityID="' . $entity_id . '">
  <md:SPSSODescriptor AuthnRequestsSigned="false" WantAssertionsSigned="true" protocolSupportEnumeration="urn:oasis:names:tc:SAML:2.0:protocol">
    <md:NameIDFormat>urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified</md:NameIDFormat>
    <md:AssertionConsumerService Binding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST" Location="' . $acs_url . '" index="1"/>
  </md:SPSSODescriptor>
  <md:Organization>
    <md:OrganizationName xml:lang="en-US">miniOrange</md:OrganizationName>
    <md:OrganizationDisplayName xml:lang="en-US">miniOrange</md:OrganizationDisplayName>
    <md:OrganizationURL xml:lang="en-US">http://miniorange.com</md:OrganizationURL>
  </md:Organization>
  <md:ContactPerson contactType="technical">
    <md:GivenName>miniOrange</md:GivenName>
    <md:EmailAddress>info@xecurify.com</md:EmailAddress>
  </md:ContactPerson>
  <md:ContactPerson contactType="support">
    <md:GivenName>miniOrange</md:GivenName> 
    <md:EmailAddress>info@xecurify.com</md:EmailAddress>
  </md:ContactPerson>
</md:EntityDescriptor>';
    exit;

}
?>