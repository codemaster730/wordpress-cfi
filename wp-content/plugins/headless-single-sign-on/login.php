<?php
/**
* Plugin Name: Headless Single Sign On
* Description: This plugin allows SSO into Headless Single Sign On Plugin
* Version: 1.4
* Author: miniOrange
* Author URI: http://miniorange.com
* License: GPL2
*/

include_once dirname( __FILE__ ) . '/mo_login_saml_sso_widget.php';
require( 'mo-saml-class-customer.php' );
require( 'mo_saml_settings_page.php' );
require( 'MetadataReader.php' );
include_once 'Utilities.php';
//include_once  'WPConfigEditor.php';

class HssoLogin {

	function __construct() {
		add_action( 'admin_menu', array( $this, 'hsso_sso_menu' ) );
		add_action( 'admin_init', array( $this, 'mo_hsso_login_widget_save_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'plugin_settings_style' ) );
		register_deactivation_hook( __FILE__, array( $this, 'mo_hsso_saml_deactivate') );
		add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array($this,'my_plugin_action_links') );
		add_action( 'admin_enqueue_scripts', array( $this, 'plugin_settings_script' ) );
		$mo_hsso_utls = new HssoUtilities();
		remove_action( 'admin_notices', array( $mo_hsso_utls, 'mo_hsso_success_message' ) );
		remove_action( 'admin_notices', array( $mo_hsso_utls, 'mo_hsso_error_message' ) );
		add_action( 'wp_authenticate', array( $this, 'mo_hsso_authenticate' ) );
		add_action( 'admin_footer', array( $this, 'mo_hsso_feedback_request' ) );
		add_action( 'plugin_action_links_' . plugin_basename( __FILE__ ), array($this,'mo_hsso_plugin_action_links') );
		register_activation_hook(__FILE__,array($this,'plugin_activate'));
        add_action('login_form', array( $this, 'mo_hsso_modify_login_form' ) );
		add_action('plugins_loaded', array($this, 'mo_hsso_load_translations'));
        add_action( 'wp_ajax_skip_entire_plugin_tour', array($this, 'close_welcome_modal'));
		add_action('init',array($this,'mo_hsso_init'));
        add_action('init', array($this, 'mo_hsso_wp_redirect'));
        add_action( 'wp', array($this, 'mo_hsso_disable_front_end') );
		add_action('wp_login', array($this, 'mo_hsso_activate_wordpress_authentication'));
		add_action( 'user_register',array($this, 'mo_hsso_activate_wordpress_authentication'));
	}

    function close_welcome_modal(){
        update_option('mo_is_new_user',1);
    }

	function mo_hsso_activate_wordpress_authentication() {	
		$activate_wordpress_authentication = get_option('mo_hsso_wordpress_authentication');
		if($activate_wordpress_authentication=='true')
		{
			HssoUtilities::mo_hsso_activate_headless();
		}
	}

	function mo_hsso_init(){
        if(array_key_exists('option',$_GET) ){
            $headless_sso =  sanitize_title($_GET['option']);
            if($headless_sso === 'hsso')
            { 
				if(is_user_logged_in())
				{
					HssoUtilities::mo_hsso_activate_headless();
            	} else {
                	auth_redirect();
                	exit;
            	}
			}
        }
    }

	/** Function to check user status and 403 redirect accordingly */
    function mo_hsso_wp_redirect()
    {
        $current_url = $this->get_current_url();
        if( get_option('mo_hsso_wp_setting') === "mo_headless_unauthorized" && get_option('mo_hsso_enable_headless') == true  && strpos($current_url, '/wp-json') === false && strpos($current_url, 'wp-login.php') === false && !is_user_logged_in() ) {
            http_response_code(403); ?>
            <h1 style="padding: 20px">Unauthorized Access</h1>

            <?php exit();
        }
    }

    function get_current_url() {
        return ( isset( $_SERVER['HTTPS'] ) ? 'https' : 'http' ) . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    }

	function mo_hsso_configurations()
	{
		$hsso_configuration = array();
		$hsso_configuration["Headless URL"] = get_option('mo_hsso_url') ?? ' ';
		$hsso_configuration["Enable Redirect Option"] = get_option('mo_hsso_enable_headless') ?? ' ';
		$hsso_configuration["Restrict Option"] = get_option('mo_hsso_wp_setting') ?? ' ';
		$hsso_json_string=json_encode($hsso_configuration,JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
		return $hsso_json_string;
	}

	/** Function to check user status and redirect accordingly **/
    function mo_hsso_disable_front_end()
    {
        if (!is_admin() && get_option('mo_hsso_wp_setting')==="mo_headless_redirect" && get_option('mo_hsso_enable_headless') == true ) {
            $post_ID = get_the_id();
            $front_page_id = get_option('page_on_front');
            $blog_page_id = get_option('page_for_posts');
            if ($front_page_id === $post_ID || $blog_page_id === $post_ID || is_front_page()) {
                wp_safe_redirect(get_admin_url());
                exit;

            } else {
                $post_edit_link = admin_url('post.php?post=' . $post_ID . '&action=edit');
                if (is_user_logged_in()) {
                    wp_safe_redirect($post_edit_link);
                    exit;
                } else {
                    wp_safe_redirect(wp_login_url($post_edit_link));
                    exit;
                }
            }
        }
    }
	function my_plugin_action_links( $links ) {
		$url = esc_url( add_query_arg(
			'page',
			'mo_hsso_settings',
			get_admin_url() . 'admin.php?page=mo_hsso_settings&tab=licensing'
		) );

		$license_link = "<a href='$url'>" . __( 'Premium Plans' ) . '</a>';

		array_push(
			$links,
			$license_link
		);
		return $links;
	}

	function mo_hsso_load_translations(){
		load_plugin_textdomain('Headless-Single-Sign-On', false, dirname(plugin_basename(__FILE__)). '/resources/lang/');
	}


	function mo_hsso_feedback_request() {

		mo_hsso_display_saml_feedback_form();
	}

	function mo_hsso_login_widget_saml_options() {
		global $wpdb;
		mo_hsso_register_saml_sso();
	}
	public function mo_hsso_saml_deactivate(){
        delete_option('mo_is_new_user');

		if(mo_hsso_is_customer_registered_saml(false))
			return;
		if(!mo_hsso_is_curl_installed())
			return;

		delete_option('mo_saml_show_upgrade_notice');
		delete_option('mo_saml_show_addons_notice');
		wp_redirect('plugins.php');

	}

	public function mo_hsso_remove_account() {
		if ( ! is_multisite() ) {
			//delete all customer related key-value pairs
			delete_option( 'mo_saml_host_name' );
			delete_option( 'mo_saml_new_registration' );
			delete_option( 'mo_saml_admin_phone' );
			delete_option( 'mo_saml_admin_password' );
			delete_option( 'mo_saml_verify_customer' );
			delete_option( 'mo_saml_admin_customer_key' );
			delete_option( 'mo_saml_admin_api_key' );
			delete_option( 'mo_saml_customer_token' );
			delete_option('mo_saml_admin_email');
			delete_option( 'mo_hsso_message' );
			delete_option( 'mo_saml_registration_status' );
			delete_option( 'mo_saml_idp_config_complete' );
			delete_option( 'mo_saml_transactionId' );
			delete_option( 'mo_proxy_host' );
			delete_option( 'mo_proxy_username' );
			delete_option( 'mo_proxy_port' );
			delete_option( 'mo_proxy_password' );
			delete_option( 'mo_saml_show_mo_idp_message' );


		} else {
			global $wpdb;
			$blog_ids         = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
			$original_blog_id = get_current_blog_id();

			foreach ( $blog_ids as $blog_id ) {
				switch_to_blog( $blog_id );
				//delete all your options
				//E.g: delete_option( {option name} );
				delete_option( 'mo_saml_host_name' );
				delete_option( 'mo_saml_new_registration' );
				delete_option( 'mo_saml_admin_phone' );
				delete_option( 'mo_saml_admin_password' );
				delete_option( 'mo_saml_verify_customer' );
				delete_option( 'mo_saml_admin_customer_key' );
				delete_option( 'mo_saml_admin_api_key' );
				delete_option( 'mo_saml_customer_token' );
				delete_option( 'mo_hsso_message' );
				delete_option( 'mo_saml_registration_status' );
				delete_option( 'mo_saml_idp_config_complete' );
				delete_option( 'mo_saml_transactionId' );
				delete_option( 'mo_saml_show_mo_idp_message' );
				delete_option('mo_saml_admin_email');
			}
			switch_to_blog( $original_blog_id );
		}
	}

	function plugin_settings_style( $page) {
		 if ( $page != 'toplevel_page_mo_hsso_settings' && !(isset($_REQUEST['page']) && $_REQUEST['page'] == 'mo_saml_licensing') && $page != 'headless-single-sign-on_page_mo_hsso_setting' && !(isset($_REQUEST['page']) && $_REQUEST['page'] == 'mo_hsso_saml_settings')) {
             if($page != 'index.php')
		         return;
		 }
		if((isset($_REQUEST['tab']) && $_REQUEST['tab'] == 'licensing') || (isset($_REQUEST['page']) && $_REQUEST['page'] == 'mo_saml_licensing') || (isset($_REQUEST['tab']) && $_REQUEST['tab'] == 'save') || (isset($_REQUEST['page']) && $_REQUEST['page'] == 'mo_hsso_settings') || (isset($_REQUEST['page']) && $_REQUEST['page'] == 'mo_hsso_setting') || (isset($_REQUEST['page']) && $_REQUEST['page'] == 'mo_hsso_saml_settings')){
			wp_enqueue_style( 'mo_saml_bootstrap_css', plugins_url( 'includes/css/bootstrap/bootstrap.min.css', __FILE__ ), array(), mo_hsso_options_plugin_constants::Version, 'all' );
		}
		wp_enqueue_style('mo_saml_jquery_ui_style',plugins_url('includes/css/jquery-ui.min.css', __FILE__), array(), mo_hsso_options_plugin_constants::Version, 'all');
        wp_enqueue_style( 'mo_saml_admin_gotham_font_style', 'https://fonts.cdnfonts.com/css/gotham', array(), mo_hsso_options_plugin_constants::Version, 'all' );
		wp_enqueue_style( 'mo_saml_admin_settings_style', plugins_url( 'includes/css/style_settings.min.css', __FILE__ ), array(), mo_hsso_options_plugin_constants::Version, 'all' );
		wp_enqueue_style( 'mo_saml_admin_settings_phone_style', plugins_url( 'includes/css/phone.css', __FILE__ ), array(), mo_hsso_options_plugin_constants::Version, 'all' );
		wp_enqueue_style( 'mo_saml_time_settings_style', plugins_url( 'includes/css/datetime-style-settings.min.css', __FILE__ ), array(),mo_hsso_options_plugin_constants::Version, 'all' );
		wp_enqueue_style( 'mo_saml_wpb-fa', plugins_url( 'includes/css/style-icon.css', __FILE__ ), array(), mo_hsso_options_plugin_constants::Version, 'all' );

	}

	function plugin_settings_script( $page ) {
		 if ( $page != 'toplevel_page_mo_hsso_settings' && !(isset($_REQUEST['page']) && $_REQUEST['page'] == 'mo_saml_licensing') && $page != 'headless-single-sign-on_page_mo_hsso_setting' && $page!='headless-single-sign-on_page_mo_hsso_saml_settings') {
		 	return;
		 }
		wp_localize_script( 'rml-script', 'readmelater_ajax', array( 'ajax_url' => admin_url('admin-ajax.php')) );


		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-autocomplete');
		wp_enqueue_script('jquery-ui-datepicker');
		wp_enqueue_script('mo_saml_select3_script', 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js');
		wp_enqueue_script('mo_saml_select2_script', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js');
		wp_enqueue_script('mo_saml_timepicker_script', 'https://cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js');
		wp_enqueue_script( 'mo_saml_admin_settings_script', plugins_url( 'includes/js/settings.min.js', __FILE__ ), array(), mo_hsso_options_plugin_constants::Version, false );
		wp_enqueue_script( 'mo_saml_admin_settings_phone_script', plugins_url( 'includes/js/phone.min.js', __FILE__ ), array(), mo_hsso_options_plugin_constants::Version, false );

		if((isset($_REQUEST['tab']) && $_REQUEST['tab'] == 'licensing') || (isset($_REQUEST['page']) && $_REQUEST['page'] == 'mo_saml_licensing')){
			wp_enqueue_script( 'mo_saml_modernizr_script', plugins_url( 'includes/js/modernizr.js', __FILE__ ), array(), mo_hsso_options_plugin_constants::Version, false );
			wp_enqueue_script( 'mo_saml_popover_script', plugins_url( 'includes/js/bootstrap/popper.min.js', __FILE__ ), array(), mo_hsso_options_plugin_constants::Version, false );
			wp_enqueue_script( 'mo_saml_bootstrap_script', plugins_url( 'includes/js/bootstrap/bootstrap.min.js', __FILE__ ), array(), mo_hsso_options_plugin_constants::Version, false );
		}


	}

    function mo_hsso_modify_login_form() {
        if(get_option('mo_saml_add_sso_button_wp') == 'true')
            $this->mo_hsso_add_sso_button();
    }

    function mo_hsso_add_sso_button() {
        if(!is_user_logged_in()){
            $saml_idp_name = get_option('saml_identity_name');
            $customButtonText = $saml_idp_name ? 'Login with '. $saml_idp_name : 'Login with SSO';
			$html = '
                <script>
                    function loginWithSSOButton(id) {
                        if( id === "mo_saml_login_sso_button")
                            document.getElementById("saml_user_login_input").value = "saml_user_login";
                        document.getElementById("loginform").submit(); 
                    }
				</script>
		        <input id="saml_user_login_input" type="hidden" name="option" value="" />
                <div id="mo_saml_button" style="height:55px;display:flex;justify-content:center;align-items:center;">
                    <div id="mo_saml_login_sso_button" onclick="loginWithSSOButton(this.id)" style="width:100%;display:flex;justify-content:center;align-items:center;font-size:13px" class="button button-primary">
					<img style="width:18px;height:13px;padding-right:1px"src="'. plugin_dir_url(__FILE__) . 'images/lock-icon.png">'.$customButtonText.'</div>
					</div><div style="padding:5px;font-size:14px;height:20px;text-align:center"><b>OR</b></div>';
			echo $html;
        }
    }
	public function plugin_activate(){
		if(is_multisite()){
			global $wpdb;
			$blog_ids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
			$original_blog_id = get_current_blog_id();

			foreach($blog_ids as $blog_id){
				switch_to_blog($blog_id);
				update_option('mo_saml_guest_log',true);
				update_option('mo_saml_guest_enabled',true);
				update_option( 'mo_saml_free_version', 1 );

			}
			switch_to_blog($original_blog_id);
		} else {
			update_option('mo_saml_guest_log',true);
			update_option('mo_saml_guest_enabled',true);
			update_option( 'mo_saml_free_version', 1 );
		}
		update_option('mo_hsso_plugin_do_activation_redirect', true);
	}

	static function mo_hsso_check_option_admin_referer($option_name){
		return (isset($_POST['option']) and $_POST['option']==$option_name and check_admin_referer($option_name));
	}

	function mo_hsso_login_widget_save_settings() {

		if (get_option('mo_hsso_plugin_do_activation_redirect')) {
			delete_option('mo_hsso_plugin_do_activation_redirect');
			if(!isset($_GET['activate-multi']) )
			{
				wp_redirect(admin_url() . 'admin.php?page=mo_hsso_settings');
				exit;
			}
		}
		if ( current_user_can( 'manage_options' ) ) {

			$mo_hsso_utils = new HssoUtilities();

			if(self::mo_hsso_check_option_admin_referer("clear_attrs_list")){
				delete_option("mo_saml_test_config_attrs");
				update_option('mo_hsso_message',__('List of attributes cleared','Headless-Single-Sign-On'));
				$mo_hsso_utils->mo_hsso_show_success_message();
			}

			if ( isset( $_POST['option'] ) and $_POST['option'] == "mo_saml_mo_idp_message" ) {
				update_option( 'mo_saml_show_mo_idp_message', 1 );

				return;
			}
			if( self::mo_hsso_check_option_admin_referer("change_miniorange")){
				self::mo_hsso_remove_account();
				update_option('mo_saml_guest_enabled',true);
				//update_option( 'mo_hsso_message', 'Logged out of miniOrange account' );
				//$this->mo_hsso_show_success_message();
				return;
			}

			if ( self::mo_hsso_check_option_admin_referer("login_widget_saml_save_settings")) {
				if ( ! mo_hsso_is_curl_installed() ) {
					update_option( 'mo_hsso_message', 'ERROR: <a href="http://php.net/manual/en/curl.installation.php" target="_blank">PHP cURL extension</a> is not installed or disabled. Save Identity Provider Configuration failed.' );
					$mo_hsso_utils->mo_hsso_show_error_message();

					return;
				}


				if (( $mo_hsso_utils->mo_hsso_check_empty_or_null( $_POST['saml_identity_name'] ) || $mo_hsso_utils->mo_hsso_check_empty_or_null( $_POST['saml_login_url'] ) || $mo_hsso_utils->mo_hsso_check_empty_or_null( $_POST['saml_issuer'] )) && $mo_hsso_utils->mo_hsso_check_empty_or_null($_POST['saml_b2c_tenant_id'])) {
					update_option( 'mo_hsso_message', __('All the fields are required. Please enter valid entries.','Headless-Single-Sign-On' ));
					$mo_hsso_utils->mo_hsso_show_error_message();
					$log_message = ['saml_identity_name' => $_POST['saml_identity_name'], 'same_login_url' => $_POST['saml_login_url'], 'saml_issuer' => $_POST['saml_issuer'], 'saml_b2c_tenant_id' => $_POST['saml_b2c_tenant_id']];

					return;
				} else if ( ! preg_match( "/^\w*$/", $_POST['saml_identity_name'] ) ) {
					update_option( 'mo_hsso_message', __('Please match the requested format for Identity Provider Name. Only alphabets, numbers and underscore is allowed.','Headless-Single-Sign-On') );
					$mo_hsso_utils->mo_hsso_show_error_message();

					$log_message = ['saml_identity_name' => $_POST['saml_identity_name']];

					return;
				} else if(isset($_POST['saml_identity_name']) and !empty($_POST['saml_identity_name'])) {
					$saml_identity_name    = htmlspecialchars(trim( $_POST['saml_identity_name'] ));
					$saml_login_url        = htmlspecialchars(trim( $_POST['saml_login_url'] ));
					$saml_issuer           = htmlspecialchars(trim( $_POST['saml_issuer'] ));
					$saml_x509_certificate =  $_POST['saml_x509_certificate'];

					update_option( 'saml_identity_name', $saml_identity_name );
					update_option( 'saml_login_url', $saml_login_url );
					update_option( 'saml_issuer', $saml_issuer );

					if(array_key_exists('mo_saml_identity_provider_identifier_name',$_POST)){
						$mo_saml_identity_provider_identifier_name = htmlspecialchars($_POST['mo_saml_identity_provider_identifier_name']);
						update_option('mo_saml_identity_provider_identifier_name',$mo_saml_identity_provider_identifier_name);
					}


					foreach ( $saml_x509_certificate as $key => $value ) {
						if ( empty( $value ) ) {
							unset( $saml_x509_certificate[ $key ] );
						} else {
							$saml_x509_certificate[ $key ] = HssoUtilities::sanitize_certificate( $value );

							if ( ! @openssl_x509_read( $saml_x509_certificate[ $key ] ) ) {
								update_option( 'mo_hsso_message', __('Invalid certificate: Please provide a valid X.509 certificate.','Headless-Single-Sign-On') );
								$mo_hsso_utils->mo_hsso_show_error_message();
								delete_option( 'saml_x509_certificate' );
								return;
							}
						}
					}
					if ( empty( $saml_x509_certificate ) ) {
						update_option( "mo_hsso_message", __('Invalid Certificate: Please provide a certificate' ,'Headless-Single-Sign-On'));
						$mo_hsso_utils->mo_hsso_show_error_message();

						return;
					}
					$saml_x509_certificate = maybe_serialize($saml_x509_certificate);
					update_option( 'saml_x509_certificate',  $saml_x509_certificate );

					$iconv_enabled = '';
					if(array_key_exists('enable_iconv',$_POST))
						$iconv_enabled = 'checked';

					update_option('mo_saml_encoding_enabled',$iconv_enabled);

					$log_message =
						array('saml_identity_name' =>$saml_identity_name,
						      'saml_login_url' => $saml_login_url,
						      'saml_issuer' => $saml_issuer ,
						      'saml_identity_provider_name' => $mo_saml_identity_provider_identifier_name,
						      'saml_x509_certificate' => $saml_x509_certificate,
						      'iconv_enabled' => $iconv_enabled);


				}


				if(isset($_POST['saml_b2c_tenant_id']) and !empty($_POST['saml_b2c_tenant_id'])){
					$b2c_tenant_id = htmlspecialchars($_POST['saml_b2c_tenant_id']);
					$b2c_tenant_id_postfix = strpos($b2c_tenant_id, ".onmicrosoft.com");
					if($b2c_tenant_id_postfix !== false)
						$b2c_tenant_id = substr($b2c_tenant_id, 0, $b2c_tenant_id_postfix);
					update_option('saml_b2c_tenant_id', $b2c_tenant_id);
					$log_message = array(
						'b2c_tenant_id'=> $b2c_tenant_id
					);

				}
				if(isset($_POST['saml_IdentityExperienceFramework_id']) and !empty($_POST['saml_IdentityExperienceFramework_id'])){
					$saml_IdentityExperienceFramework_id = htmlspecialchars($_POST['saml_IdentityExperienceFramework_id']);
					update_option('saml_IdentityExperienceFramework_id', $saml_IdentityExperienceFramework_id);
					$log_message = array(
						'saml_IdentityExperienceFramework_id' =>  $saml_IdentityExperienceFramework_id
					);
				}
				if(isset($_POST['saml_ProxyIdentityExperienceFramework_id']) and !empty($_POST['saml_ProxyIdentityExperienceFramework_id'])){
					$saml_ProxyIdentityExperienceFramework_id = htmlspecialchars($_POST['saml_ProxyIdentityExperienceFramework_id']);
					update_option('saml_ProxyIdentityExperienceFramework_id', $saml_ProxyIdentityExperienceFramework_id);
					$log_message = array(
						'Azure B2C saml_ProxyIdentityExperienceFramework_id' => $saml_ProxyIdentityExperienceFramework_id
					);
				}


				update_option( 'mo_hsso_message', __('Identity Provider details saved successfully.','Headless-Single-Sign-On' ));
				$mo_hsso_utils->mo_hsso_show_success_message();

			}

			if(self::mo_hsso_check_option_admin_referer('update_sso_config')){
				$metadata_url = 'https://tenant-name.b2clogin.com/tenant-name.onmicrosoft.com/B2C_1A_signup_signin_saml/Samlp/metadata';
				$b2c_tenant_id = get_option('saml_b2c_tenant_id');
				$metadata_url = str_replace('tenant-name', $b2c_tenant_id, $metadata_url);
				$this->_handle_upload_metadata($metadata_url);
			}

			//Update SP Entity ID
			if(self::mo_hsso_check_option_admin_referer('mo_saml_update_idp_settings_option')){
				if(isset($_POST['mo_saml_sp_entity_id'])) {
					$sp_entity_id = htmlspecialchars($_POST['mo_saml_sp_entity_id']);
					update_option('mo_saml_sp_entity_id', $sp_entity_id);
				}

				update_option('mo_hsso_message', __('Settings updated successfully.','Headless-Single-Sign-On'));
				$mo_hsso_utils->mo_hsso_show_success_message();
				$log_message = [ 'sp_entity_id' =>  $sp_entity_id ];

			}
			//Save Attribute Mapping
			if (self::mo_hsso_check_option_admin_referer("login_widget_saml_attribute_mapping") ) {

				if ( ! mo_hsso_is_curl_installed() ) {
					update_option( 'mo_hsso_message', __('ERROR: <a href="http://php.net/manual/en/curl.installation.php" target="_blank">PHP cURL extension</a> is not installed or disabled. Save Attribute Mapping failed.','Headless-Single-Sign-On') );
					$mo_hsso_utils->mo_hsso_show_error_message();

					return;
				}


				update_option( 'mo_hsso_message', __('Attribute Mapping details saved successfully','Headless-Single-Sign-On' ));
				$mo_hsso_utils->mo_hsso_show_success_message();

			}
			//Save Role Mapping
			if (self::mo_hsso_check_option_admin_referer("login_widget_saml_role_mapping")) {

				if ( ! mo_hsso_is_curl_installed() ) {
					update_option( 'mo_hsso_message', __('ERROR: <a href="http://php.net/manual/en/curl.installation.php" target="_blank">PHP cURL extension</a> is not installed or disabled. Save Role Mapping failed.','Headless-Single-Sign-On') );
					$mo_hsso_utils->mo_hsso_show_error_message();

					return;
				}


				update_option( 'saml_am_default_user_role', htmlspecialchars($_POST['saml_am_default_user_role']) );

				update_option( 'mo_hsso_message', __('Role Mapping details saved successfully.','Headless-Single-Sign-On' ));
				$mo_hsso_utils->mo_hsso_show_success_message();

				$log_message = [ 'default_user_role' =>$_POST['saml_am_default_user_role']];

			}
			//Headless SSO 
			if(self::mo_hsso_check_option_admin_referer("mo_hsso_option")) {
				if(array_key_exists("mo_hsso_url",$_POST)) {
					$endpoint = esc_url_raw($_POST['mo_hsso_url']);
					update_option('mo_hsso_url',$endpoint);
				}
				update_option( 'mo_hsso_message', __('Settings updated successfully.' ,'Headless Single Sign On'));
				$mo_hsso_utils->mo_hsso_show_success_message();
				return;

			}
			if(self::mo_hsso_check_option_admin_referer("mo_hsso_enable_headless_option")) {
				if(array_key_exists('mo_hsso_enable_headless', $_POST)) {
					$enable_redirect = htmlspecialchars($_POST['mo_hsso_enable_headless']);
				} else {
					$enable_redirect = 'false';
				}
				if($enable_redirect == 'on') {
					update_option('mo_hsso_enable_headless', 'true');
				} else {
					update_option('mo_hsso_enable_headless', '');
				}
				return;
			}
			if(self::mo_hsso_check_option_admin_referer("mo_hsso_wordpress_authentication")) {
				if(array_key_exists('mo_hsso_wordpress_authentication', $_POST)) {
					$enable_wordpress_authentication = htmlspecialchars($_POST['mo_hsso_wordpress_authentication']);
				} else {
					$enable_wordpress_authentication = 'false';
				}
				if($enable_wordpress_authentication == 'on') {
					update_option('mo_hsso_wordpress_authentication', 'true');
				} else {
					update_option('mo_hsso_wordpress_authentication', $enable_wordpress_authentication);
				}
				return;
			}
			if(self::mo_hsso_check_option_admin_referer("mo_hsso_setting_option")) {
				if(array_key_exists("mo_hsso_wp_setting",$_POST))
				{
					$mo_headless_option = htmlspecialchars($_POST['mo_hsso_wp_setting']);
					update_option('mo_hsso_wp_setting',$mo_headless_option);
				}
				update_option( 'mo_hsso_message', __('Settings updated successfully.' ,'Headless Single Sign On'));
				$mo_hsso_utils->mo_hsso_show_success_message();
				return;
			}
			if(self::mo_hsso_check_option_admin_referer("mo_hsso_demo_request_option")){

				if(isset($_POST['mo_saml_demo_email']))
					$demo_email = htmlspecialchars($_POST['mo_saml_demo_email']);

				if(isset($_POST['mo_saml_demo_plan']))
					$demo_plan_selected ="wp_headless_sso_premium_plan";

				if(isset($_POST['mo_saml_demo_description']))
					$demo_description = htmlspecialchars($_POST['mo_saml_demo_description']);

				// $license_plans = mo_hsso_license_plans::$license_plans;
				// if(isset($license_plans[$demo_plan_selected]))
				// 	$demo_plan = $license_plans[$demo_plan_selected];

				$addons = mo_hsso_options_addons::$ADDON_TITLE;

				$addons_selected = array();
				foreach($addons as $key => $value){
					if(isset($_POST[$key]) && $_POST[$key] == "true")
						$addons_selected[$key] = $value;
				}
				$status = "";
				if(empty($demo_email)){
					$demo_email = get_option('mo_saml_admin_email');
					$status = "Error :" ."Email address for Demo is Empty.";
				} else if (!filter_var($demo_email, FILTER_VALIDATE_EMAIL)) {
                    update_option( 'mo_hsso_message', __('Please enter a valid email address.' ,'Headless-Single-Sign-On'));
                    $mo_hsso_utils->mo_hsso_show_error_message();
                    return;
                }
				// else{
				// 	$license_plans_slugs = mo_hsso_license_plans::$license_plans_slug;
				// 	if(array_key_exists($demo_plan_selected,$license_plans_slugs)){
				// 		$url = 'https://demo.miniorange.com/wordpress-saml-demo/';
				// 		$headers = array( 'Content-Type' => 'application/x-www-form-urlencoded', 'charset' => 'UTF - 8');
				// 		$args = array(
				// 			'method' =>'POST',
				// 			'body' => array(
				// 				'option' => 'mo_auto_create_demosite',
				// 				'mo_auto_create_demosite_email' => $demo_email,
				// 				'mo_auto_create_demosite_usecase' => $demo_description,
				// 				'mo_auto_create_demosite_demo_plan' => $license_plans_slugs[$demo_plan_selected],
				// 			),
				// 			'timeout' => '20',
				// 			'redirection' => '5',
				// 			'httpversion' => '1.0',
				// 			'blocking' => true,
				// 			'headers' => $headers,
				// 		);

				// 		$response = wp_remote_post( $url, $args );
				// 		if ( is_wp_error( $response ) ) {
				// 			$error_message = $response->get_error_message();
				// 			echo "Something went wrong: $error_message";
				// 			exit();
				// 		}
				// 		$output = wp_remote_retrieve_body($response);
				// 		$output = json_decode($output);
				// 		if(is_null($output)){
				// 			update_option('mo_hsso_message', __('Something went wrong. Please reach out to us using the Support/Contact Us form to get help with the demo.','Headless-Single-Sign-On'));
				// 			$status = __('Error :','Headless-Single-Sign-On') . __('Something went wrong while setting up demo.','Headless-Single-Sign-On');
				// 		}

				// 		if($output->status == 'SUCCESS'){
				// 			update_option('mo_hsso_message', $output->message);
				// 			$status = __('Success :','Headless-Single-Sign-On').$output->message;
				// 		}else{
				// 			update_option('mo_hsso_message', $output->message);
				// 			$status = __('Error :','Headless-Single-Sign-On') .$output->message;
				// 		}
				// 	}else{
				// 		$status = __('Please setup manual demo.','Headless-Single-Sign-On');
				// 	}
				// }

				$message = "[Demo For Customer] : " . $demo_email;
				if(!empty($demo_plan))
					$message .= " <br>[Selected Plan] : " . $demo_plan;
				if(!empty($demo_description))
					$message .= " <br>[Requirements] : " . $demo_description;

				$message .= " <br>[Status] : " .$status;
				if(!empty($addons_selected)){
					$message .= " <br>[Addons] : ";
					foreach($addons_selected as $key => $value){
						$message .= $value;
						if(next($addons_selected))
							$message .= ", ";
					}
				}

				$user = wp_get_current_user();
				$customer = new HssoCustomer();
				$email = get_option( "mo_saml_admin_email" );
				if ( $email == '' ) {
					$email = $user->user_email;
				}
				$phone = get_option( 'mo_saml_admin_phone' );
				$submited = json_decode( $customer->send_email_alert( $email, $phone, $message, true ), true );
				if ( json_last_error() == JSON_ERROR_NONE ) {
					if ( is_array( $submited ) && array_key_exists( 'status', $submited ) && $submited['status'] == 'ERROR' ) {
						update_option( 'mo_hsso_message', $submited['message'] );
						$mo_hsso_utils->mo_hsso_show_error_message();

					}
					else {
						$demo_status = strpos($status,"Error");
						if ( $submited == false || $demo_status !== false ) {

							update_option( 'mo_hsso_message', $status );
							$mo_hsso_utils->mo_hsso_show_error_message();
						} else {
							update_option( 'mo_hsso_message', __('Thanks! We have received your request and will shortly get in touch with you.','Headless-Single-Sign-On'));
							$mo_hsso_utils->mo_hsso_show_success_message();
						}
					}
				}

			}

			if (self::mo_hsso_check_option_admin_referer("saml_upload_metadata")) {
				if ( ! function_exists( 'wp_handle_upload' ) ) {
					require_once( ABSPATH . 'wp-admin/includes/file.php' );
				}
				$this->_handle_upload_metadata();
				return;
			}
			if ( self::mo_hsso_check_option_admin_referer("mo_saml_register_customer")) {

				//register the admin to miniOrange
				$user = wp_get_current_user();
				if ( ! mo_hsso_is_curl_installed() ) {
					update_option( 'mo_hsso_message', __('ERROR: <a href="http://php.net/manual/en/curl.installation.php" target="_blank">PHP cURL extension</a> is not installed or disabled. Registration failed.' ,'Headless-Single-Sign-On'));
					$mo_hsso_utils->mo_hsso_show_error_message();

					return;
				}

				//validation and sanitization
                $email = '';
                $password = '';
                $confirmPassword = '';

                if(isset($_POST['registerEmail']) and !empty($_POST['registerEmail'])) {

                    if ($mo_hsso_utils->mo_hsso_check_empty_or_null($_POST['password']) || $mo_hsso_utils->mo_hsso_check_empty_or_null($_POST['confirmPassword'])) {

                        update_option('mo_hsso_message', __('Please enter the required fields.', 'Headless-Single-Sign-On'));
                        $mo_hsso_utils->mo_hsso_show_error_message();

                        return;
                    } else if (!filter_var($_POST['registerEmail'], FILTER_VALIDATE_EMAIL)) {
                        update_option('mo_hsso_message', __('Please enter a valid email address.', 'Headless-Single-Sign-On'));
                        $mo_hsso_utils->mo_hsso_show_error_message();
                        return;
                    } else if ($this->checkPasswordpattern(htmlspecialchars($_POST['password']))) {
                        update_option('mo_hsso_message', __('Minimum 6 characters should be present. Maximum 15 characters should be present. Only following symbols (!@#.$%^&*-_) should be present.', 'Headless-Single-Sign-On'));
                        $mo_hsso_utils->mo_hsso_show_error_message();
                        return;
                    } else {

                        $email = sanitize_email($_POST['registerEmail']);
                        $password = stripslashes(htmlspecialchars($_POST['password']));
                        $confirmPassword = stripslashes(htmlspecialchars($_POST['confirmPassword']));
                    }
                    update_option('mo_saml_admin_email', $email);

                    if (strcmp($password, $confirmPassword) == 0) {
                        update_option('mo_saml_admin_password', $password);
                        $email = get_option('mo_saml_admin_email');
                        $customer = new HssoCustomer();
                        $content = json_decode($customer->check_customer(), true);
                        if (!is_null($content)) {
                            if (strcasecmp($content['status'], 'CUSTOMER_NOT_FOUND') == 0) {

                                $response = $this->create_customer();
                                if (is_array($response) && array_key_exists('status', $response) && $response['status'] == 'success') {
                                    wp_redirect(admin_url('/admin.php?page=mo_hsso_settings&tab=licensing'), 301);
                                    exit;
                                }
                            } else {
                                $response = $this->get_current_customer();
                                if (is_array($response) && array_key_exists('status', $response) && $response['status'] == 'success') {
                                    wp_redirect(admin_url('/admin.php?page=mo_hsso_settings&tab=licensing'), 301);
                                    exit;
                                }
                                //$this->mo_hsso_show_error_message();
                            }
                        }

                    } else {
                        update_option('mo_hsso_message', __('Passwords do not match.', 'Headless-Single-Sign-On'));
                        delete_option('mo_saml_verify_customer');
                        $mo_hsso_utils->mo_hsso_show_error_message();
                    }
                    return;
                }
                else if ( isset($_POST['loginEmail']) and !empty($_POST['loginEmail'])) {
                    if ($mo_hsso_utils->mo_hsso_check_empty_or_null( $_POST['password'] ) ) {
                        update_option( 'mo_hsso_message', __('All the fields are required. Please enter valid entries.','Headless-Single-Sign-On' ));
                        $mo_hsso_utils->mo_hsso_show_error_message();

                        return;
                    } else if($this->checkPasswordpattern(htmlspecialchars($_POST['password']))){
                        update_option( 'mo_hsso_message', __('Minimum 6 characters should be present. Maximum 15 characters should be present. Only following symbols (!@#.$%^&*-_) should be present.' ,'Headless-Single-Sign-On'));
                        $mo_hsso_utils->mo_hsso_show_error_message();
                        return;
                    }else {
                        $email    = sanitize_email( $_POST['loginEmail'] );
                        $password = stripslashes( htmlspecialchars($_POST['password'] ));
                    }

                    update_option( 'mo_saml_admin_email', $email );
                    update_option( 'mo_saml_admin_password', $password );
                    $customer    = new HssoCustomer();
                    $content     = $customer->get_customer_key();
                    if(!is_null($content)){
                        $customerKey = json_decode( $content, true );
                        if ( json_last_error() == JSON_ERROR_NONE ) {
                            update_option( 'mo_saml_admin_customer_key', $customerKey['id'] );
                            update_option( 'mo_saml_admin_api_key', $customerKey['apiKey'] );
                            update_option( 'mo_saml_customer_token', $customerKey['token'] );
                            $certificate = get_option( 'saml_x509_certificate' );
                            if ( empty( $certificate ) ) {
                                update_option( 'mo_saml_free_version', 1 );
                            }
                            update_option( 'mo_saml_admin_password', '' );
                            update_option( 'mo_hsso_message', __('Customer retrieved successfully','Headless-Single-Sign-On' ));
                            update_option( 'mo_saml_registration_status', 'Existing User' );
                            delete_option( 'mo_saml_verify_customer' );
                            $mo_hsso_utils->mo_hsso_show_success_message();
                            //if(is_array($response) && array_key_exists('status', $response) && $response['status'] == 'success'){
                            wp_redirect( admin_url( '/admin.php?page=mo_hsso_settings&tab=licensing' ), 301 );
                            exit;
                            //}
                        } else {
                            update_option( 'mo_hsso_message', __('Invalid username or password. Please try again.','Headless-Single-Sign-On' ));
                            $mo_hsso_utils->mo_hsso_show_error_message();
                        }
                        update_option( 'mo_saml_admin_password', '' );
                    }
                }
			}
			else if( self::mo_hsso_check_option_admin_referer("mosaml_metadata_download")){
				mo_hsso_miniorange_generate_metadata(true);
			}
			if ( self::mo_hsso_check_option_admin_referer("mo_saml_verify_customer") ) {    //register the admin to miniOrange

				if ( ! mo_hsso_is_curl_installed() ) {
					update_option( 'mo_hsso_message', __('ERROR: <a href="http://php.net/manual/en/curl.installation.php" target="_blank">PHP cURL extension</a> is not installed or disabled. Login failed.','Headless-Single-Sign-On' ));
					$mo_hsso_utils->mo_hsso_show_error_message();

					return;
				}

				//validation and sanitization
				$email    = '';
				$password = '';
				if ( $mo_hsso_utils->mo_hsso_check_empty_or_null( $_POST['email'] ) || $mo_hsso_utils->mo_hsso_check_empty_or_null( $_POST['password'] ) ) {
					update_option( 'mo_hsso_message', __('All the fields are required. Please enter valid entries.','Headless-Single-Sign-On' ));
					$mo_hsso_utils->mo_hsso_show_error_message();

					return;
				} else if($this->checkPasswordpattern(htmlspecialchars($_POST['password']))){
					update_option( 'mo_hsso_message', __('Minimum 6 characters should be present. Maximum 15 characters should be present. Only following symbols (!@#.$%^&*-_) should be present.' ,'Headless-Single-Sign-On'));
					$mo_hsso_utils->mo_hsso_show_error_message();
					return;
				}else {
					$email    = sanitize_email( $_POST['email'] );
					$password = stripslashes( htmlspecialchars($_POST['password'] ));
				}

				update_option( 'mo_saml_admin_email', $email );
				update_option( 'mo_saml_admin_password', $password );
				$customer    = new HssoCustomer();
				$content     = $customer->get_customer_key();
				if(!is_null($content)){
					$customerKey = json_decode( $content, true );
					if ( json_last_error() == JSON_ERROR_NONE ) {
						update_option( 'mo_saml_admin_customer_key', $customerKey['id'] );
						update_option( 'mo_saml_admin_api_key', $customerKey['apiKey'] );
						update_option( 'mo_saml_customer_token', $customerKey['token'] );
						$certificate = get_option( 'saml_x509_certificate' );
						if ( empty( $certificate ) ) {
							update_option( 'mo_saml_free_version', 1 );
						}
						update_option( 'mo_saml_admin_password', '' );
						update_option( 'mo_hsso_message', __('Customer retrieved successfully','Headless-Single-Sign-On' ));
						update_option( 'mo_saml_registration_status', 'Existing User' );
						delete_option( 'mo_saml_verify_customer' );
						$mo_hsso_utils->mo_hsso_show_success_message();
						//if(is_array($response) && array_key_exists('status', $response) && $response['status'] == 'success'){
						wp_redirect( admin_url( '/admin.php?page=mo_hsso_settings&tab=licensing' ), 301 );
						exit;
						//}
					} else {
						update_option( 'mo_hsso_message', __('Invalid username or password. Please try again.','Headless-Single-Sign-On' ));
						$mo_hsso_utils->mo_hsso_show_error_message();
					}
					update_option( 'mo_saml_admin_password', '' );
				}
			}
			else if ( self::mo_hsso_check_option_admin_referer("mo_hsso_contact_us_query_option") ) {
				if ( ! mo_hsso_is_curl_installed() ) {
					update_option( 'mo_hsso_message', __('ERROR: <a href="http://php.net/manual/en/curl.installation.php" target="_blank">PHP cURL extension</a> is not installed or disabled. Query submit failed.' ,'Headless-Single-Sign-On'));
					$mo_hsso_utils->mo_hsso_show_error_message();
					return;
				}

				// Contact Us query
				$email    = sanitize_email($_POST['mo_saml_contact_us_email']);
				$query    = htmlspecialchars($_POST['mo_saml_contact_us_query']);
				$phone    = htmlspecialchars($_POST['mo_saml_contact_us_phone']);


				$call_setup = false;

				if(array_key_exists('saml_setup_call',$_POST)===true){
					$time_zone = $_POST['mo_saml_setup_call_timezone'];
					$call_date = $_POST['mo_saml_setup_call_date'];
					$call_time = $_POST['mo_saml_setup_call_time'];
					$call_setup = true;
				}

				$plugin_config_json = mo_hsso_miniorange_import_export(true, true);
				$hsso_configuration = $this->mo_hsso_configurations();
				$customer = new HssoCustomer();

				if($call_setup == false) {
					$query = $query.'<br><br>'.'Plugin Configuration: '.$plugin_config_json.'<br><br>'.'Headless SSO Configuration : '.$hsso_configuration;
					if ( $mo_hsso_utils->mo_hsso_check_empty_or_null( $email ) || $mo_hsso_utils->mo_hsso_check_empty_or_null( $query ) ) {
						update_option( 'mo_hsso_message', __('Please fill up Email and Query fields to submit your query.','Headless-Single-Sign-On' ));
						$mo_hsso_utils->mo_hsso_show_error_message();
					} else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
						update_option( 'mo_hsso_message', __('Please enter a valid email address.' ,'Headless-Single-Sign-On'));
						$mo_hsso_utils->mo_hsso_show_error_message();
					} else {
						$submited = $customer->submit_contact_us( $email, $phone, $query, false);
						if(!is_null($submited)){
							if ( $submited == false ) {
								update_option( 'mo_hsso_message', __('Your query could not be submitted. Please try again.','Headless-Single-Sign-On' ));
								$mo_hsso_utils->mo_hsso_show_error_message();
							} else {
								update_option( 'mo_hsso_message', __('Thanks for getting in touch! We shall get back to you shortly.' ,'Headless-Single-Sign-On'));
								$mo_hsso_utils->mo_hsso_show_success_message();
							}
						}
					}
				} else {
					if ( $mo_hsso_utils->mo_hsso_check_empty_or_null( $email )) {
						update_option('mo_hsso_message', __('Please fill up Email fields to submit your query.','Headless-Single-Sign-On'));
						$mo_hsso_utils->mo_hsso_show_error_message();
					} else if ($mo_hsso_utils->mo_hsso_check_empty_or_null($call_date)  || $mo_hsso_utils->mo_hsso_check_empty_or_null($call_time) || $mo_hsso_utils->mo_hsso_check_empty_or_null($time_zone) ) {
						update_option('mo_hsso_message', __('Please fill up Schedule Call Details to submit your query.','Headless-Single-Sign-On'));
						$mo_hsso_utils->mo_hsso_show_error_message();
					}
					else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
						update_option( 'mo_hsso_message', __('Please enter a valid email address.','Headless-Single-Sign-On' ));
						$mo_hsso_utils->mo_hsso_show_error_message();
					} else {
						$local_timezone='Asia/Kolkata';
						$call_datetime=$call_date.$call_time;
						$convert_datetime = strtotime ( $call_datetime );
						$ist_date = new DateTime(date ( 'Y-m-d H:i:s' , $convert_datetime ), new DateTimeZone($time_zone));
						$ist_date->setTimezone(new DateTimeZone($local_timezone));
						$query = $query .'<br><br>' .'Meeting Details: '.'('.$time_zone.') '. date('d M, Y  H:i',$convert_datetime). ' [IST Time -> '. $ist_date->format('d M, Y  H:i').']'.'<br><br>'.'Plugin Config: '.$plugin_config_json.'<br><br>'.'Headless SSO Configuration : '.$hsso_configuration;
						$response = $customer->submit_contact_us( $email, $phone, $query, true);
						if(!is_null($response)){
							if ( $response == false ) {
								update_option( 'mo_hsso_message', __('Your query could not be submitted. Please try again.','Headless-Single-Sign-On' ));
								$mo_hsso_utils->mo_hsso_show_error_message();
							} else {
								update_option('mo_hsso_message', __('Thanks for getting in touch! You will receive the call details on your email shortly.','Headless-Single-Sign-On'));
								$mo_hsso_utils->mo_hsso_show_success_message();
							}
						}
					}
				}
			}
			else if ( self::mo_hsso_check_option_admin_referer("mo_saml_go_back") ) {
				update_option( 'mo_saml_registration_status', '' );
				update_option( 'mo_saml_verify_customer', '' );
				delete_option( 'mo_saml_new_registration' );
				delete_option( 'mo_saml_admin_email' );
				delete_option( 'mo_saml_admin_phone' );
			}
            else if(self::mo_hsso_check_option_admin_referer('mo_saml_add_sso_button_wp_option')){
                if(mo_hsso_is_sp_configured()) {
                    if(array_key_exists("mo_saml_add_sso_button_wp", $_POST)) {
                        $add_button = htmlspecialchars($_POST['mo_saml_add_sso_button_wp']);
                    } else {
                        $add_button = 'false';
                    }
                    update_option('mo_saml_add_sso_button_wp', $add_button);
                    update_option('mo_hsso_message', 'Sign in option updated.');
                    $mo_hsso_utils->mo_hsso_show_success_message();
                } else {
                    update_option( 'mo_hsso_message', 'Please complete '.mo_hsso_add_link('Service Provider' , add_query_arg( array('tab' => 'save'), $_SERVER['REQUEST_URI'] )) . ' configuration first.');
                    $mo_hsso_utils->mo_hsso_show_error_message();
                }
            }
			else if ( self::mo_hsso_check_option_admin_referer("mo_saml_goto_login") ) {
				delete_option( 'mo_saml_new_registration' );
				update_option( 'mo_saml_verify_customer', 'true' );
			}
			else if ( self::mo_hsso_check_option_admin_referer("mo_saml_forgot_password_form_option") ) {
				if ( ! mo_hsso_is_curl_installed() ) {
					update_option( 'mo_hsso_message', __('ERROR: <a href="http://php.net/manual/en/curl.installation.php" target="_blank">PHP cURL extension</a> is not installed or disabled. Resend OTP failed.','Headless-Single-Sign-On' ));
					$mo_hsso_utils->mo_hsso_show_error_message();

					return;
				}

				$email = get_option( 'mo_saml_admin_email' );

				$customer = new HssoCustomer();
				$content  = json_decode( $customer->mo_hsso_forgot_password( $email ), true );
				if(!is_null($content)){
					if ( strcasecmp( $content['status'], 'SUCCESS' ) == 0 ) {
						update_option( 'mo_hsso_message', sprintf(__('Your password has been reset successfully. Please enter the new password sent to %s','Headless-Single-Sign-On') , $email) . '.' );
						$mo_hsso_utils->mo_hsso_show_success_message();
					} else {
						update_option( 'mo_hsso_message', __('An error occurred while processing your request. Please Try again.','Headless-Single-Sign-On') );
						$mo_hsso_utils->mo_hsso_show_error_message();
					}
				}
			}
			if ( self::mo_hsso_check_option_admin_referer("mo_hsso_skip_feedback") ) {
				update_option( 'mo_hsso_message', __('Plugin deactivated successfully','Headless-Single-Sign-On') );
				$mo_hsso_utils->mo_hsso_show_success_message();
				deactivate_plugins( __FILE__ );


			}
			if ( self::mo_hsso_check_option_admin_referer("mo_hsso_feedback") ) {
				$user = wp_get_current_user();

				$message = 'Plugin Deactivated';

				$deactivate_reason_message = array_key_exists( 'query_feedback', $_POST ) ? htmlspecialchars($_POST['query_feedback']) : false;


				$reply_required = '';
				if(isset($_POST['get_reply']))
					$reply_required = htmlspecialchars($_POST['get_reply']);
				if(empty($reply_required)){
					$reply_required = "don't reply";
					$message.='<b style="color:red";> &nbsp; [Reply :'.$reply_required.']</b>';
				}else{
					$reply_required = "yes";
					$message.='[Reply :'.$reply_required.']';
				}

				if(is_multisite())
					$multisite_enabled = 'True';
				else
					$multisite_enabled = 'False';

				$message.= ', [Multisite enabled: ' . $multisite_enabled .']';

				$message.= ', Feedback : '.$deactivate_reason_message.'';

				if (isset($_POST['rate']))
					$rate_value = htmlspecialchars($_POST['rate']);

				$message.= ', [Rating :'.$rate_value.']';

				$email = $_POST['query_mail'];
				if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
					$email = get_option('mo_saml_admin_email');
					if(empty($email))
						$email = $user->user_email;
				}
				$phone = get_option( 'mo_saml_admin_phone' );
				$feedback_reasons = new HssoCustomer();
				if(!is_null($feedback_reasons)){
					if(!mo_hsso_is_curl_installed()){
						deactivate_plugins( __FILE__ );
						wp_redirect('plugins.php');
					} else {
						$submited = json_decode( $feedback_reasons->send_email_alert( $email, $phone, $message ), true );
						if ( json_last_error() == JSON_ERROR_NONE ) {
							if ( is_array( $submited ) && array_key_exists( 'status', $submited ) && $submited['status'] == 'ERROR' ) {
								update_option( 'mo_hsso_message', $submited['message'] );
								$mo_hsso_utils->mo_hsso_show_error_message();

							}
							else {
								if ( $submited == false ) {

									update_option( 'mo_hsso_message', __('Error while submitting the query.','Headless-Single-Sign-On') );
									$mo_hsso_utils->mo_hsso_show_error_message();
								}
							}
						}

						deactivate_plugins( __FILE__ );
						update_option( 'mo_hsso_message', __('Thank you for the feedback.','Headless-Single-Sign-On' ));
						$mo_hsso_utils->mo_hsso_show_success_message();
					}
				}
			}
		}
	}

	function _handle_upload_metadata($metadata_url = '') {
		$mo_hsso_utils = new HssoUtilities();
		if ( isset( $_FILES['metadata_file'] ) || isset( $_POST['metadata_url'] ) || !empty($metadata_url)) {
			if ( ! empty( $_FILES['metadata_file']['tmp_name'] ) ) {
				$file = @file_get_contents( $_FILES['metadata_file']['tmp_name'] );
			} else {
				if(!mo_hsso_is_curl_installed()){
					update_option( 'mo_hsso_message', __('PHP cURL extension is not installed or disabled. Cannot fetch metadata from URL.','Headless-Single-Sign-On' ));
					$mo_hsso_utils->mo_hsso_show_error_message();
					return;
				}
				if(isset( $_POST['metadata_url'] ))
					$url = filter_var( $_POST['metadata_url'], FILTER_SANITIZE_URL );
				else
					$url = $metadata_url;


				$response = HssoUtilities::mo_hsso_wp_remote_get($url, array('sslverify'=>false));
				if(!is_null($response)){
					$file = $response['body'];

				}
				else{
					$file = null;
				}

			}
			if(!is_null($file))
				$this->upload_metadata( $file, $metadata_url );
		}
	}

	function upload_metadata( $file,$metadata_url='' ) {
		$mo_hsso_utils = new HssoUtilities();
		$old_error_handler = set_error_handler( array( $this, 'handleXmlError' ) );
		$document          = new DOMDocument();
		$document->loadXML( $file );
		restore_error_handler();
		$first_child = $document->firstChild;
		if ( ! empty( $first_child ) ) {
			$metadata           = new HssoIDPMetadataReader( $document );
			$identity_providers = $metadata->getIdentityProviders();
			if ( ! preg_match( "/^\w*$/", $_POST['saml_identity_metadata_provider'] ) ) {
				update_option( 'mo_hsso_message', __('Please match the requested format for Identity Provider Name. Only alphabets, numbers and underscore is allowed.','Headless-Single-Sign-On') );
				$mo_hsso_utils->mo_hsso_show_error_message();

				return;
			}
			if ( empty( $identity_providers ) && !empty( $_FILES['metadata_file']['tmp_name']) ) {
				update_option( 'mo_hsso_message', __('Please provide a valid metadata file.' ,'Headless-Single-Sign-On'));
				$mo_hsso_utils->mo_hsso_show_error_message();

				return;
			}
			if ( empty( $identity_providers ) && !empty($_POST['metadata_url']) ) {
				update_option( 'mo_hsso_message', __('Please provide a valid metadata URL.','Headless-Single-Sign-On') );
				$mo_hsso_utils->mo_hsso_show_error_message();


				return;
			}
			if(empty($identity_providers) && !empty($metadata_url)){
				update_option( 'mo_hsso_message', __('Unable to fetch Metadata. Please check your IDP configuration again.','Headless-Single-Sign-On' ));
				$mo_hsso_utils->mo_hsso_show_error_message();


				return;
			}
			foreach ( $identity_providers as $key => $idp ) {
				$saml_identity_name = htmlspecialchars($_POST['saml_identity_metadata_provider']);

				$saml_login_url = $idp->getLoginURL( 'HTTP-Redirect' );

				$saml_issuer           = $idp->getEntityID();
				$saml_x509_certificate = $idp->getSigningCertificate();

				update_option( 'saml_identity_name', $saml_identity_name );

				update_option( 'saml_login_url', $saml_login_url );


				update_option( 'saml_issuer', $saml_issuer );
				//certs already sanitized in Metadata Reader
				$saml_x509_certificate = maybe_serialize($saml_x509_certificate);
				update_option( 'saml_x509_certificate',  $saml_x509_certificate  );

				$log_message = [ 'saml_identity_name' =>$saml_identity_name,
				                 'saml_login_url' => $saml_login_url,
				                 'saml_issuer' => $saml_issuer ,
				                 'saml_x509_certificate' =>  $saml_x509_certificate];

				break;
			}
			update_option( 'mo_hsso_message', __('Identity Provider details saved successfully.','Headless-Single-Sign-On' ));
			$mo_hsso_utils->mo_hsso_show_success_message();
		} else {
			if(!empty( $_FILES['metadata_file']['tmp_name']))
			{
				update_option( 'mo_hsso_message', __('Please provide a valid metadata file.','Headless-Single-Sign-On' ));
				$mo_hsso_utils->mo_hsso_show_error_message();
			}
			if(!empty($_POST['metadata_url']))
			{
				update_option( 'mo_hsso_message', __('Please provide a valid metadata URL.','Headless-Single-Sign-On' ));
				$mo_hsso_utils->mo_hsso_show_error_message();
			}
		}
	}

	function get_current_customer() {
		$customer    = new HssoCustomer();
		$content     = $customer->get_customer_key();
		$mo_hsso_utils = new HssoUtilities();
		if(!is_null($content)){
			$customerKey = json_decode( $content, true );

			$response = array();
			if ( json_last_error() == JSON_ERROR_NONE ) {
				update_option( 'mo_saml_admin_customer_key', $customerKey['id'] );
				update_option( 'mo_saml_admin_api_key', $customerKey['apiKey'] );
				update_option( 'mo_saml_customer_token', $customerKey['token'] );
				update_option( 'mo_saml_admin_password', '' );
				$certificate = get_option( 'saml_x509_certificate' );
				if ( empty( $certificate ) ) {
					update_option( 'mo_saml_free_version', 1 );
				}

				delete_option( 'mo_saml_verify_customer' );
				delete_option( 'mo_saml_new_registration' );
				$response['status'] = "success";
				return $response;
			} else {

				update_option( 'mo_hsso_message', __('You already have an account with miniOrange. Please enter a valid password.','Headless-Single-Sign-On' ));
				$mo_hsso_utils->mo_hsso_show_error_message();
				//update_option( 'mo_saml_verify_customer', 'true' );
				//delete_option( 'mo_saml_new_registration' );
				$response['status'] = "error";
				return $response;
			}
		}
	}

	function create_customer() {
		$customer    = new HssoCustomer();
		$customerKey = json_decode( $customer->create_customer(), true );
		if(!is_null($customerKey)){
			$response = array();
			//print_r($customerKey);
			if ( strcasecmp( $customerKey['status'], 'CUSTOMER_USERNAME_ALREADY_EXISTS' ) == 0 ) {
				$api_response = $this->get_current_customer();
				//print_r($api_response);exit;
				if($api_response){
					$response['status'] = "success";
				}
				else
					$response['status'] = "error";

			} else if ( strcasecmp( $customerKey['status'], 'SUCCESS' ) == 0 ) {
				update_option( 'mo_saml_admin_customer_key', $customerKey['id'] );
				update_option( 'mo_saml_admin_api_key', $customerKey['apiKey'] );
				update_option( 'mo_saml_customer_token', $customerKey['token'] );
				update_option( 'mo_saml_free_version', 1 );
				update_option( 'mo_saml_admin_password', '' );
				update_option( 'mo_hsso_message', __('Thank you for registering with miniOrange.','Headless-Single-Sign-On') );
				update_option( 'mo_saml_registration_status', '' );
				delete_option( 'mo_saml_verify_customer' );
				delete_option( 'mo_saml_new_registration' );
				$response['status']="success";
				return $response;
			}

			update_option( 'mo_saml_admin_password', '' );
			return $response;
		}
	}

	function hsso_sso_menu() {
		//Add miniOrange SAML SSO
		$slug = 'mo_hsso_settings';
		add_menu_page( 'MO HSSO Settings ' . __( 'Configure Headless Single Sign On','Headless-Single-Sign-On'), 'Headless Single Sign On', 'administrator', $slug, array(
			$this,
			'mo_hsso_login_widget_saml_options'
		), plugin_dir_url( __FILE__ ) . 'images/miniorange.png' );
		add_submenu_page( $slug	,'Headless-Single-Sign-On'	,__('Headless SSO Configuration','Headless-Single-Sign-On'),'manage_options','mo_hsso_settings'
		, array( $this, 'mo_hsso_login_widget_saml_options'));
		add_submenu_page( $slug	,'Headless Single Sign On'	,__('SAML SSO Configuration','Headless-Single-Sign-On'),'manage_options','mo_hsso_saml_settings'
		, array( $this, 'mo_hsso_login_widget_saml_options'));
		add_submenu_page( $slug	,'Headless Single Sign On'	,__('<div id="mo_saml_headless_settings">Convert to Headless CMS</div>','Headless-Single-Sign-On'),'manage_options','mo_hsso_setting'
			, array( $this, 'mo_hsso_enable_redirect'));
		add_submenu_page($slug	,'Headless Single Sign On'	,__('<div style="color:orange"><img src="'. plugin_dir_url(__FILE__) . 'images/premium_plans_icon.png" style="height:10px;width:12px">  Premium Plans</div>','Headless-Single-Sign-On'),'manage_options','mo_saml_licensing'
			, array( $this, 'mo_hsso_login_widget_saml_options'));
	}

	function mo_hsso_authenticate() {
		$redirect_to = '';
		if ( isset( $_REQUEST['redirect_to'] ) ) {
			$redirect_to = htmlentities( $_REQUEST['redirect_to'] );
		}
		if ( is_user_logged_in() ) {
			$this->mo_hsso_login_redirect($redirect_to);
		}
	}
	function mo_hsso_enable_redirect()
	{
		mo_hsso_setting();
	}

	function mo_hsso_login_redirect($redirect_to){
		$is_admin_url = false;
		if(strcmp(admin_url(),$redirect_to) == 0 || strcmp(wp_login_url(),$redirect_to) == 0 ){
			$is_admin_url = true;
		}
		if ( ! empty( $redirect_to ) && !$is_admin_url ) {
			header( 'Location: ' . $redirect_to );
		} else {
			header( 'Location: ' . site_url() );
		}
		exit();
	}


	function handleXmlError( $errno, $errstr, $errfile, $errline ) {
		if ( $errno == E_WARNING && ( substr_count( $errstr, "DOMDocument::loadXML()" ) > 0 ) ) {
			return;
		} else {
			return false;
		}
	}

	function mo_hsso_plugin_action_links( $links ) {
		$links = array_merge( array(
			'<a href="' . esc_url( admin_url( 'admin.php?page=mo_hsso_settings' ) ) . '">' . __( 'Settings','Headless-Single-Sign-On' ) . '</a>'
		), $links );
		return $links;
	}

	function checkPasswordpattern($password){
		$pattern = '/^[(\w)*(\!\@\#\$\%\^\&\*\.\-\_)*]+$/';

		return !preg_match($pattern,$password);
	}
}
new HssoLogin;