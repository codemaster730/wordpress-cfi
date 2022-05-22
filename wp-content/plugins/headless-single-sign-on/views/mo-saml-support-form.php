<?php

function mo_hsso_display_support_form($display_attrs = false,$mo_hsso_setting=false)
{
    ?>
    <div class="col-md-3 mt-4 pl-0">
        <?php

        if($display_attrs and !empty(get_option('mo_saml_test_config_attrs'))) {
            mo_hsso_display_attrs_list();
        } else {

            ?>
            <div class="bg-white text-center shadow-cstm rounded contact-form-cstm">
                <form method="post" action="" id="mo_hsso_contact_us_query_option" name="mo_hsso_contact_us_query_option">
                    <?php wp_nonce_field("mo_hsso_contact_us_query_option"); ?>
                    <input type="hidden" name="option" value="mo_hsso_contact_us_query_option" />

                    <div class="contact-form-head">
                        <p class="h5">Feature Request/Contact Us <br> (24*7 Support)</p>
                        <p class="h6 mt-3"> Call us at +1 978 658 9387 in case of any help</p>
                    </div>
                    <div class="contact-form-body p-3">
                        <input type="email" id="mo_saml_support_email" placeholder="<?php _e('Enter your email', 'Headless-Single-Sign-On'); ?>" class="mo_saml_table_textbox mt-4" name="mo_saml_contact_us_email" value="<?php echo (get_option('mo_saml_admin_email') == '') ? get_option('admin_email') : get_option('mo_saml_admin_email'); ?>" required>
                        <input type="tel" id="contact_us_phone" pattern="[\+]?[0-9]{1,4}[\s]?([0-9]{4,12})*" class="mo_saml_table_textbox mt-4" name="mo_saml_contact_us_phone" value="<?php echo get_option('mo_saml_admin_phone'); ?>" placeholder="<?php _e('Enter your phone', 'Headless-Single-Sign-On'); ?>">
                        <textarea class="mo_saml_table_textbox mt-4" onkeypress="mo_hsso_valid_query(this)" onkeyup="mo_hsso_valid_query(this)" onblur="mo_hsso_valid_query(this)" name="mo_saml_contact_us_query" rows="4" style="resize: vertical;" required placeholder="<?php _e('Write your query here', 'Headless-Single-Sign-On'); ?>" id="mo_saml_query"></textarea>
                        <div class="mo-saml-call-setup mt-4 p-3">
                            <h6>Setup a Call / Screen-share session with miniOrange Technical Team</h6>
                            <hr />
                            <div class="row align-items-center mt-3">
                                <div class="col-md-9">
                                    <h6 class="text-secondary">Enable this option to setup a call</h6>
                                </div>
                                <div class="col-md-3 pl-0">
                                    <input type="checkbox" id="saml_setup_call" name="saml_setup_call" class="mo-saml-switch" /><label class="mo-saml-switch-label" for="saml_setup_call"></label>
                                </div>
                            </div>
                            <div id="call_setup_dets" class="call-setup-details">
                                <div class="row">
                                    <div class="col-md-3" ><strong><?php _e('TimeZone','Headless-Single-Sign-On');?><font color="#FF0000">*</font>:</strong></div>
                                    <div class="col-md-9">
                                        <select id="js-timezone" class="mo-saml-select-timezone" name="mo_saml_setup_call_timezone">
                                            <?php $zones = mo_hsso_time_zones::$time_zones; ?>
                                            <option value="" selected disabled>---------<?php _e('Select your timezone','Headless-Single-Sign-On');?>--------</option> <?php
                                            foreach($zones as $zone=>$value) {
                                                if($value == 'Etc/GMT'){ ?>
                                                    <option value="<?php echo $value; ?>" selected><?php echo $zone; ?></option>
                                                    <?php
                                                }
                                                else { ?>
                                                    <option value="<?php echo $value; ?>"><?php echo $zone; ?></option>
                                                    <?php
                                                }
                                            } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="row align-items-center text-left mt-4">
                                    <div class="col-md-6 call-setup-datetime">
                                        <strong> <?php _e('Date','Headless-Single-Sign-On');?><font color="#FF0000">*</font>:</strong><br>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" id="datepicker" class="call-setup-textbox pl-2 pt-1 pb-0" placeholder="<?php _e('Select Date','Headless-Single-Sign-On');?>" autocomplete="off" name="mo_saml_setup_call_date">
                                    </div>
                                    <div class="col-md-6 call-setup-datetime mt-3">
                                        <strong> <?php _e('Time (24-hour)','Headless-Single-Sign-On');?><font color="#FF0000">*</font>:</strong><br>
                                    </div>
                                    <div class="col-md-6 mt-3">
                                        <input type="text" id="timepicker" placeholder="<?php _e('Select Time','Headless-Single-Sign-On');?>" class="call-setup-textbox pl-2 pt-1 pb-0" autocomplete="off" name="mo_saml_setup_call_time">
                                    </div>
                                </div>
                                <div>
                                    <p class="mt-4 text-danger call-setup-notice">
                                       <?php _e('Call and Meeting details will be sent to your email. Please verify the email before submitting your query.','Headless-Single-Sign-On');?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <input type="submit" value="Submit" class="btn btn-cstm text-white mt-4 w-50">
                    </div>
                </form>
            </div>


            <?php
        }
        if(!$mo_hsso_setting)
        {
            mo_hsso_display_keep_settings_intact_section();
            mo_hsso_display_suggested_idp_integration();
            mo_hsso_display_suggested_add_ons();
        }
        ?>
    </div>

    <?php
}
