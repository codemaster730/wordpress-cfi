<?php
require_once dirname(__FILE__) . '/includes/lib/mo-saml-options-enum.php';
add_action( 'admin_init', 'mo_hsso_miniorange_import_export');

$tab_class_names_array =array(
	"SSO_Login"         => 'mo_hsso_options_enum_sso_loginMoSAML',
	"Identity_Provider" => 'mo_hsso_options_enum_identity_providerMoSAML',
	"Service_Provider"  => 'mo_hsso_options_enum_service_providerMoSAML',
	"Attribute_Mapping" => 'mo_hsso_options_enum_attribute_mappingMoSAML',
	"Role_Mapping"      => 'mo_hsso_options_enum_role_mappingMoSAML'
);

if(get_option('MO_SAML_TEST_STATUS')!=1){

	$tab_class_names_array['Test_Configuration'] = 'mo_hsso_options_test_configuration';
}

define("Hsso_Tab_Class_Names",serialize($tab_class_names_array));

/**
 *Function iterates through the enum to create array of values and converts to JSON and lets user download the file
 */
function mo_hsso_miniorange_import_export($test_config_screen=false, $json_in_string=false) {

    if($test_config_screen)
        $_POST['option'] = 'mo_hsso_export';

	if ( array_key_exists("option", $_POST)  ) {

	    if($_POST['option']=='mo_hsso_export'){
	        if($test_config_screen and $json_in_string)
			{
	            $export_referer = check_admin_referer('mo_hsso_contact_us_query_option');
			}
	        else if($_POST['option']=='mo_hsso_export')
			{
	            $export_referer = check_admin_referer('mo_hsso_export');
			}
					if($export_referer) {
					$tab_class_name = maybe_unserialize(Hsso_Tab_Class_Names);
					$configuration_array = array();
					foreach ($tab_class_name as $key => $value) {
						$configuration_array[$key] = mo_hsso_get_configuration_array($value);
					}
					$configuration_array["Version_dependencies"] = mo_hsso_get_version_informations();
					$version = phpversion();
					if(substr($version,0 ,3) === '5.3'){
						$json_string=(json_encode($configuration_array, JSON_PRETTY_PRINT));
					} else {
						$json_string=(json_encode($configuration_array, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
					}

					if($json_in_string)
						return $json_string;
					header("Content-Disposition: attachment; filename=miniorange-saml-config.json");
					echo $json_string;
					exit;
				}
	    }
	    else if($_POST['option']=='mo_hsso_keep_settings_on_deletion' and check_admin_referer('mo_hsso_keep_settings_on_deletion')) {

            if (array_key_exists('mo_hsso_keep_settings_intact', $_POST))
                update_option('mo_hsso_keep_settings_on_deletion', 'true');
            else
                update_option('mo_hsso_keep_settings_on_deletion', '');

        }

        return;


	}

}

function mo_hsso_get_configuration_array($class_name ) {
	$class_object = call_user_func( $class_name . '::getConstants' );
	$mo_array = array();
	foreach ( $class_object as $key => $value ) {
		$mo_option_exists=get_option($value);

		if($mo_option_exists){
            $mo_option_exists = maybe_unserialize($mo_option_exists);
			$mo_array[ $key ] = $mo_option_exists;

		}

	}

	return $mo_array;
}

function mo_hsso_get_version_informations(){
	$array_version = array();
	$array_version["Plugin_version"] = mo_hsso_options_plugin_constants::Version;
	$array_version["PHP_version"] = phpversion();
	$array_version["Wordpress_version"] = get_bloginfo('version');
	$array_version["OPEN_SSL"] = mo_hsso_is_openssl_installed();
	$array_version["CURL"] = mo_hsso_is_curl_installed();
    $array_version["ICONV"] = mo_hsso_is_iconv_installed();
    $array_version["DOM"] = mo_hsso_is_dom_installed();

	return $array_version;
}

