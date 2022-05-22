jQuery(document).ready(function() {
    //show and hide attribute mapping instructions
    jQuery("#toggle_am_content").click(function() {
        jQuery("#show_am_content").toggle();
    });
    jQuery("#dont_allow_unlisted_user_role").change(function() {
        if (jQuery(this).is(":checked")) {
            jQuery("#saml_am_default_user_role").attr('disabled', true);
        } else {
            jQuery("#saml_am_default_user_role").attr('disabled', false);
        }
    });
    if (jQuery("#dont_allow_unlisted_user_role").is(":checked")) {
        jQuery("#saml_am_default_user_role").attr('disabled', true);
    } else if (!jQuery("#dont_allow_unlisted_user_role").is(":disabled")) {
        jQuery("#saml_am_default_user_role").attr('disabled', false);
    }
    /*
     * Identity Provider help

    jQuery("#user_selected_idp").change(function() {
        var idp = this.value;
        if(idp == 'adfs') {
            var content = "<a href='http://miniorange.com/adfs_as_idp_wordpress' target='_blank'>Click here to see the guide</a>"
        } else if(idp == 'simplesaml') {
            var content = "<a href='http://miniorange.com/simplesaml_as_idp_wordpress' target='_blank'>Click here to see the guide</a>"
        } else if(idp == 'salesforce') {
            var content = "<a href='http://miniorange.com/salesforce_as_idp_wordpress' target='_blank'>Click here to see the guide</a>"
        } else if(idp == 'okta') {
            var content = "<a href='http://miniorange.com/okta_as_idp_wordpress' target='_blank'>Click here to see the guide</a>"
        }else if(idp == 'shibboleth') {
            var content = "<a href='http://miniorange.com/shibboleth_as_idp_wordpress' target='_blank'>Click here to see the guide</a>"
        } else {
            jQuery("#idp_guide_link").html("");
        }
        jQuery("#idp_guide_link").html(content);
    });*/

    /*
     * Help & Troubleshooting
     */

    //Enable cURL
    jQuery("#help_curl_enable_title").click(function() {
        jQuery("#help_curl_enable_desc").slideToggle(400);
    });

    //enable openssl
    jQuery("#help_openssl_enable_title").click(function() {
        jQuery("#help_openssl_enable_desc").slideToggle(400);
    });

    //attribute mapping
    jQuery("#attribute_mapping").click(function() {
        jQuery("#attribute_mapping_desc").slideToggle(400);
    });

    //role mapping
    jQuery("#role_mapping").click(function(e) {
        e.preventDefault();
        jQuery("#role_mapping_desc").slideToggle(400);
    });

    //idp details
    jQuery("#idp_details_link").click(function(e) {
        e.preventDefault();
        jQuery("#idp_details_desc").slideToggle(400);
    });

    //add widget
    jQuery("#mo_saml_add_widget").change(function() {
        jQuery("#mo_saml_add_widget_steps").slideToggle(400);
    });

    //add shorcut
    jQuery("#mo_saml_add_shortcode").change(function() {
        jQuery("#mo_saml_add_shortcode_steps").slideToggle(400);
    });

    //registration
    jQuery("#help_register_link").click(function(e) {
        e.preventDefault();
        jQuery("#help_register_desc").slideToggle(400);
    });


    //Widget steps
    jQuery("#help_widget_steps_title").click(function() {
        jQuery("#help_widget_steps_desc").slideToggle(400);
    });

    //redirect to idp
    jQuery("#redirect_to_idp").click(function(e) {
        e.preventDefault;
        jQuery("#redirect_to_idp_desc").slideToggle(400);
    });

    //redirect to idp
    jQuery("#registered_only_access").click(function(e) {
        e.preventDefault;
        jQuery("#registered_only_access_desc").slideToggle(400);
    });

    //redirect to idp
    jQuery("#force_authentication_with_idp").click(function(e) {
        e.preventDefault;
        jQuery("#force_authentication_with_idp_desc").slideToggle(400);
    });

    //Instructions
    jQuery("#help_steps_title").click(function() {
        jQuery("#help_steps_desc").slideToggle(400);
    });

    //Working of plugin
    jQuery("#help_working_title1").click(function() {
        jQuery("#help_working_desc2").hide();
        jQuery("#help_working_desc3").hide();
        jQuery("#help_working_desc1").slideToggle(400);
    });

    jQuery("#help_working_title2").click(function() {
        jQuery("#help_working_desc1").hide();
        jQuery("#help_working_desc3").hide();
        jQuery("#help_working_desc2").slideToggle(400);
    });

    jQuery("#help_working_title3").click(function() {
        jQuery("#help_working_desc1").hide();
        jQuery("#help_working_desc2").hide();
        jQuery("#help_working_desc3").slideToggle(400);
    });

    //What is SAML
    jQuery("#help_saml_title").click(function() {
        jQuery("#help_saml_desc").slideToggle(400);
    });

    //SAML flows
    jQuery("#help_saml_flow_title").click(function() {
        jQuery("#help_saml_flow_desc").slideToggle(400);
    });

    //FAQ - certificate
    jQuery("#help_faq_cert_title").click(function() {
        jQuery("#help_faq_cert_desc").slideToggle(400);
    });

    //FAQ - 404 error
    jQuery("#help_faq_404_title").click(function() {
        jQuery("#help_faq_404_desc").slideToggle(400);
    });

    //FAQ - idp not configured properly issue
    jQuery("#help_faq_idp_config_title").click(function() {
        jQuery("#help_faq_idp_config_desc").slideToggle(400);
    });

    //FAQ - redirect to idp issue
    jQuery("#help_faq_idp_redirect_title").click(function() {
        jQuery("#help_faq_idp_redirect_desc").slideToggle(400);
    });

    //Licensing Plans
    jQuery('.goto-opt a').click(function() {
        jQuery('.goto-active').removeClass('goto-active');
        jQuery(this).addClass('goto-active');
    });
    jQuery('.tab').click(function() {
        jQuery('.handler').hide();
        jQuery('.' + jQuery(this).attr('id')).show();
        jQuery('.active').removeClass('active');
        jQuery(this).addClass('active');
        jQuery('.' + jQuery(this).attr('id') + '-rot').css('transform', 'rotateY(0deg)');
        jQuery('.common-rot').not('.' + jQuery(this).attr('id') + '-rot').css({
            'transform': 'rotateY(180deg)',
            'transition': '0.3s'
        });
        jQuery('.cp-single-site, .cp-multi-site').removeClass('show');
        jQuery('.cp-' + jQuery(this).attr('id')).addClass('show');
        jQuery('.' + jQuery(this).attr('id') + ' .clk-icn i').removeClass('fa-expand-alt').addClass('fa-times');
    });
    jQuery('.clk-icn').click(function() {
        jQuery(this).find('i').toggleClass('fa-times fa-expand-alt')
    });
    jQuery('.goto-opt a').click(function(e) {
        var href = jQuery(this).attr("href"),
            offsetTop = href === "#" ? 0 : jQuery(href).offset().top - 180;
        jQuery('html, body').stop().animate({
            scrollTop: offsetTop
        }, 300);
    });
    const toggles = document.querySelectorAll(".faq-toggle");
    toggles.forEach((toggle) => {
        toggle.addEventListener("click", () => {
            toggle.parentNode.classList.toggle("active");
        });
    });
    jQuery(".tab-us").css('border-bottom', '1px solid #2f4f4f');
    jQuery(".instances").css('border-bottom', '4px solid #2f4f4f');
    jQuery(".integration-section").css('display', 'none');
    jQuery("#instances").css('display', 'block');
    jQuery(".multi-network").click(function() {
        jQuery(".integration-section").css('display', 'none');
        jQuery("#multi-network").css('display', 'block');
        jQuery(".multi-network").css('border-bottom', '4px solid #2f4f4f');
    });
    jQuery(".instances").click(function() {
        jQuery(".integration-section").css('display', 'none');
        jQuery("#instances").css('display', 'block');
        jQuery(".instances").css('border-bottom', '4px solid #2f4f4f');
    });
    jQuery(".multi-idp").click(function() {
        jQuery(".integration-section").css('display', 'none');
        jQuery("#multi-idp").css('display', 'block');
        jQuery(".multi-idp").css('border-bottom', '4px solid #2f4f4f');
    });
    jQuery(".multi-network,.instances,.multi-idp").hover(function() {
        jQuery(".tabs11,.tab-us").css('border-bottom', '1px solid #2f4f4f');
    });
    jQuery(".intg-tab").click(function() {
        jQuery(".intg-tab").removeClass('active-tab');
        jQuery(this).addClass('active-tab');
    });
    jQuery(window).scroll(function() {
        var scrollDistance = jQuery(window).scrollTop();
        var num = -1;

        jQuery('.saml-scroll').each(function(i) {
            if (jQuery(this).offset().top - 450 <= scrollDistance) {
                num = i;
            }
        });
        if (num != -1) {
            jQuery('.goto-opt a.goto-active').removeClass('goto-active');
            jQuery('.goto-opt a').eq(num).addClass('goto-active');
        } else {
            jQuery('.goto-opt a.goto-active').removeClass('goto-active');
        }
    }).scroll();

    // sp-tab-switch
    jQuery('.mo-saml-sp-tab-container a').click(function(event) {
        event.preventDefault();

        jQuery('.mo-saml-sp-tab-container .switch-tab-sp a').closest('li').removeClass("mo-saml-current");
        jQuery(this).closest('li').addClass("mo-saml-current");

        // display only active tab content
        var activeTab = jQuery(this).attr("href");
        jQuery('.mo-saml-tab-content').not(activeTab).css("display", "none");
        jQuery(activeTab).fadeIn();

    });
    jQuery('.contact-us-cstm').click(function() {
        jQuery('.contact-form-cstm').addClass('contact-form-cstm-slide');
        jQuery('.contact-form-cstm').removeClass('contact-form-cstm-slide1');
    });
    jQuery('.cls-cstm').click(function() {
        jQuery('.contact-form-cstm').addClass('contact-form-cstm-slide1');
        jQuery('.contact-form-cstm').removeClass('contact-form-cstm-slide');
    });

    jQuery('#mo_saml_goto_login').click(function() {
        jQuery('.mo-saml-reg-text-field').prop('disabled', true);
        jQuery('.mo-saml-login-text-field').prop('disabled', false);
        jQuery('.mo-saml-reg-field , #mo_saml_reg_btn, #mo_saml_goto_login').hide();
        jQuery('.mo-saml-already-reg-field ').show().css('display', 'flex');
        jQuery('#mo_saml_reg_login_btn , #mo_saml_reg_back_btn').show().css('display', 'inline');
        jQuery('.mo-saml-why-reg-txt').hide();
        jQuery('.mo-saml-why-login-txt').show();

    });
    jQuery('#mo_saml_reg_back_btn').click(function() {
        jQuery('.mo-saml-reg-text-field').prop('disabled', false);
        jQuery('.mo-saml-login-text-field').prop('disabled', true);
        jQuery('.mo-saml-reg-field').show().css('display', 'flex');
        jQuery('#mo_saml_reg_btn, #mo_saml_goto_login').show();
        jQuery('.mo-saml-already-reg-field ,  #mo_saml_reg_login_btn , #mo_saml_reg_back_btn').hide();
        jQuery('.mo-saml-why-reg-txt').show();
        jQuery('.mo-saml-why-login-txt').hide();
    });



    jQuery("#contact_us_phone").intlTelInput();

    jQuery("#mo_saml_mo_idp").click(function() {
        jQuery("#mo_saml_mo_idp_form").submit();
    });
    var mo_saved_idp = jQuery('#mo_saml_identity_provider_identifier_name').val();
    var mo_saved_idp_details = jQuery('#mo_saml_identity_provider_identifier_details').val();
    if ((mo_saved_idp != undefined && mo_saved_idp != null && mo_saved_idp != '') && (mo_saved_idp_details != undefined && mo_saved_idp_details != null && mo_saved_idp_details != '')) {
        var details = JSON.parse(jQuery('#mo_saml_identity_provider_identifier_details').val());
        var a_href = details['idp_guide_link'];
        var video_link = details['idp_video_link'];
        var idp_name = jQuery('#mo_saml_identity_provider_identifier_name').val();
        var image_src = details['image_src'];
        mo_hsso_get_idp_data(idp_name, image_src, video_link, a_href);
    }

    // Click to select IDP JS
    jQuery('.logo-saml-cstm').click(function() {
        var a_href = jQuery(this).find('a').data('href');
        var video_link = jQuery(this).find('a').data('video');
        var idp_name = jQuery(this).children().find('h6').text();
        var image_src = jQuery(this).find('img').attr('src');

        mo_hsso_get_idp_data(idp_name, image_src, video_link, a_href);
        document.querySelector('#idp_scroll_saml').scrollIntoView();
    });

    function mo_hsso_get_idp_data(idp_name, image_src, video_link, a_href) {
        var idp_specific_ads_text = JSON.parse(jQuery("#idp_specific_ads").val());
        jQuery('#mo_saml_identity_provider_identifier_name').val(idp_name);
        if (typeof idp_specific_ads_text[idp_name] != "undefined") {
            setTimeout(function() {
                jQuery('#mo_saml_identity_provider_identifier_name').val(idp_name);
                jQuery('#mo-saml-ads-text').show();
                jQuery('#mo-saml-ads-cards-text').text(idp_specific_ads_text[idp_name]["Text"]);
                jQuery('#mo-saml-ads-head').text(idp_specific_ads_text[idp_name]["Heading"]);
                jQuery('#ads-text-link').text(idp_specific_ads_text[idp_name]["Link_Title"]);
                jQuery('#ads-text-link').attr("href", idp_specific_ads_text[idp_name]["Link"]);
                if (idp_specific_ads_text[idp_name]["Know_Title"] && idp_specific_ads_text[idp_name]["Know_Link"]) {
                    jQuery('#ads-knw-more-link').css('display', 'block');
                    jQuery('#ads-knw-more-link').text(idp_specific_ads_text[idp_name]["Know_Title"]);
                    jQuery('#ads-knw-more-link').attr("href", idp_specific_ads_text[idp_name]["Know_Link"]);
                } else {
                    jQuery('#ads-knw-more-link').css('display', 'none');
                }
            }, 0);
        } else {
            jQuery('#mo-saml-ads-text').hide();
        }

        var video_link_id = video_link.split("?v=")[1];
        if (video_link_id == "" || video_link_id == null || video_link_id.length == 0) {
            jQuery('#saml_idp_video_link').hide();
        } else {
            jQuery('#saml_idp_video_link').show();
            jQuery('#saml_idp_video_link').attr('href', video_link);
        }
        jQuery('#mo_saml_selected_idp_div').show();
        jQuery('.hide-hr').show();

        jQuery('#mo_saml_selected_idp_icon_div img').attr('src', image_src);
        jQuery('#saml_idp_guide_link').attr('href', a_href);
    }

    jQuery('#mo-saml-ads-text').hide();
    jQuery("#mo_saml_search_idp_list").on("keyup", function() {
        var value = jQuery(this).val().toLowerCase();
        var active = 0;
        jQuery(".logo-saml-cstm").filter(function() {
            if (jQuery(this).text().toLowerCase().indexOf(value) > -1)
                active = 1;
            jQuery(this).toggle(jQuery(this).text().toLowerCase().indexOf(value) > -1);
            jQuery('.show-msg').css('display', 'none');
        });
        if (active == 0) {
            jQuery('.logo-saml-cstm[data-idp="gilfhNFYsgc"]').show();
            jQuery('.show-msg').css('display', 'block');
        }
    });
    jQuery('#saml_setup_call').change(function() {
        if (jQuery(this).is(":checked")) {
            jQuery('#call_setup_dets').show();
        } else {
            jQuery('#call_setup_dets').hide();
        }
    });
    displayWelcomePage();
    checkUploadMetadataFields();
});
jQuery(function() {
    jQuery("#call_setup_dets").hide();
    jQuery("#js-timezone").select2();

    jQuery("#js-timezone").click(function() {
        var name = $('#name').val();
        var email = $('#email').val();
        var message = $('#message').val();
        jQuery.ajax({
            type: "POST",
            url: "form_submit.php",
            data: { "name": name, "email": email, "message": message },
            success: function(data) {
                jQuery('.result').html(data);
                jQuery('#contactform')[0].reset();
            }
        });
    });

    jQuery("#saml_setup_call").click(function() {
        if (jQuery(this).is(":checked")) {
            jQuery("#call_setup_dets").show();
            document.getElementById("js-timezone").required = true;
            document.getElementById("datepicker").required = true;
            document.getElementById("timepicker").required = true;
            document.getElementById("mo_saml_query").required = false;

            jQuery("#datepicker").datepicker("setDate", +1);
            jQuery('#timepicker').timepicker('option', 'minTime', '00:00');

        } else {
            jQuery("#call_setup_dets").hide();
            document.getElementById("timepicker").required = false;
            document.getElementById("datepicker").required = false;
            document.getElementById("js-timezone").required = false;
            document.getElementById("mo_saml_query").required = true;
        }
    });
    jQuery("#datepicker").datepicker({
        minDate: +1,
        dateFormat: 'M dd, yy'
    });
    jQuery('#timepicker').timepicker({
        timeFormat: 'HH:mm',
        interval: 30,
        minTime: new Date(),
        disableTextInput: true,
        dynamic: false,
        dropdown: true,
        scrollbar: true,
        forceRoundTime: true
    });

    function mo_hsso_valid_query(f) {
        !(/^[a-zA-Z?,.\(\)\/@ 0-9]*$/).test(f.value) ? f.value = f.value.replace(
            /[^a-zA-Z?,.\(\)\/@ 0-9]/, '') : null;
    }
});

function showTestWindow() {
    var url = jQuery('#mo-saml-test-window-url').val();
    var myWindow = window.open(url, "TEST SAML IDP", "scrollbars=1 width=800, height=600");
}

function redirect_to_attribute_mapping() {
    var url = jQuery('#mo-saml-attribute-mapping-url').val();
    window.location.href = url;
}

function redirect_to_service_provider() {
    var url = jQuery('#mo-saml-service-provider-url').val();
    window.location.href = url;
}

function redirect_to_redi_sso_link() {
    var url = jQuery('#mo-saml-redirect-sso-url').val();
    window.location.href = url;
}

function copyToClipboard(copyButton, element, copyelement) {
    var temp = jQuery("<input>");
    jQuery("body").append(temp);
    temp.val(jQuery(element).text()).select();
    document.execCommand("copy");
    temp.remove();
    jQuery(copyelement).text("Copied");

    jQuery(copyButton).mouseout(function() {
        jQuery(copyelement).text("Copy to Clipboard");
    });
}

function displayWelcomePage() {
    let getting_started_modal = document.getElementById("getting-started");
    let modal_value = document.getElementById("mo_modal_value");
    let saml_issuer = jQuery("#sp_configured_welcome_check").val();
    console.log(modal_value);
    if (modal_value.value != 1 && saml_issuer == '') {
        getting_started_modal.style.display = "block";
    }
}

function highlightAddonSubmenu() {
    jQuery(document).ready(function() {
        jQuery('#mo_saml_addons_submenu').parent().parent().parent().find('li').removeClass('current');
        jQuery('#mo_saml_addons_submenu').parent().parent().addClass('current');
    });
}
function highlightHsso() {
    jQuery(document).ready(function() {
        jQuery('#mo_saml_headless_sso_submenu').parent().parent().parent().find('li').removeClass('current');
        jQuery('#mo_saml_headless_sso_submenu').parent().parent().addClass('current');
    });
}

function skip_plugin_tour() {
    let getting_started_modal = document.getElementById("getting-started");
    let data = {
        action: 'skip_entire_plugin_tour',
    };

    jQuery.post(ajaxurl, data, function(response) {
        getting_started_modal.style.display = "none";
    });

}
function checkUploadMetadataFields() {
    var fileField = jQuery("#metadata_file");
    var urlField = jQuery("#metadata_url");

    if (fileField.val() == "" && urlField.val() == "")
    {
        fileField.prop("required", true);
        urlField.prop("required", true);
    }
    else
    {
        fileField.prop("required", false);
        urlField.prop("required", false);
    }
}

function checkMetadataFile() {
    jQuery("#metadata_file").prop("required",true);
    jQuery("#metadata_url").prop("required",false);
    jQuery("#metadata-submit-button").click();
}
function checkMetadataUrl() {
    jQuery("#metadata_file").prop("required",false);
    jQuery("#metadata_url").prop("required",true);
    jQuery("#metadata-submit-button").click();
}

function addCertificateErrorClass() {
    var error = jQuery(".error").text();
    if (error.includes("X.509")) {
        jQuery("#saml_x509_certificate").addClass("mo-saml-error-box");
        jQuery(".mo-saml-error-tip").show();
        jQuery('html, body').animate({
            scrollTop: jQuery('#saml_issuer').offset().top
        }, 'slow');
        jQuery(function() {
            setTimeout(function() {
                jQuery(".mo-saml-error-tip").hide(100);
            }, 5000);
        });
    }
}

function removeCertificateErrorClass() {
    if(jQuery("#saml_x509_certificate").val() != "") {
        jQuery("#saml_x509_certificate").removeClass("mo-saml-error-box");
    }
}