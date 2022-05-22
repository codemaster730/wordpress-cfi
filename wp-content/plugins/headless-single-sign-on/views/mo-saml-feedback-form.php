<?php
function mo_hsso_display_saml_feedback_form() {
	if ( 'plugins.php' != basename( $_SERVER['PHP_SELF'] ) ) {
		return;
	}

	wp_enqueue_script( 'utils' );
	wp_enqueue_style( 'mo_saml_admin_plugins_page_style', plugins_url( '/includes/css/style_settings.min.css', mo_hsso_options_plugin_constants::PLUGIN_FILE ), array(), mo_hsso_options_plugin_constants::Version, false );
	?>

    </head>
    <body>


    <div id="feedback_modal" class="mo_modal" style="width:90%; margin-left:12%; margin-top:5%; text-align:center; margin-left">

        <div class="mo_modal-content" style="width:50%;">
            <h3 style="margin: 2%; text-align:center;"><b><?php _e('Your feedback','Headless-Single-Sign-On');?></b><span class="mo_close" style="cursor: pointer">&times;</span>
            </h3>
			<hr style="width:75%;">
			
            <form name="f" method="post" action="" id="mo_hsso_feedback">
                <?php wp_nonce_field("mo_hsso_feedback");?>
                <input type="hidden" name="option" value="mo_hsso_feedback"/>
                <div>
                    <p style="margin:2%">
					<h4 style="margin: 2%; text-align:center;"><?php _e('Please help us to improve our plugin by giving your opinion.','Headless-Single-Sign-On');?><br></h4>
					
					<div id="smi_rate" style="text-align:center">
					<input type="radio" name="rate" id="angry"  value="1"/>
						<label for="angry"><img class="sm" src="<?php echo plugin_dir_url( mo_hsso_options_plugin_constants::PLUGIN_FILE ) . 'images/angry.png'; ?>" />
						</label>
						
					<input type="radio" name="rate" id="sad"  value="2"/>
						<label for="sad"><img class="sm" src="<?php echo plugin_dir_url( mo_hsso_options_plugin_constants::PLUGIN_FILE ) . 'images/sad.png'; ?>" />
						</label>
					
					
					<input type="radio" name="rate" id="neutral"  value="3"/>
						<label for="neutral"><img class="sm" src="<?php echo plugin_dir_url( mo_hsso_options_plugin_constants::PLUGIN_FILE ) . 'images/normal.png'; ?>" />
						</label>
						
					<input type="radio" name="rate" id="smile" value="4"/>
						<label for="smile">
						<img class="sm" src="<?php echo plugin_dir_url( mo_hsso_options_plugin_constants::PLUGIN_FILE ) . 'images/smile.png'; ?>" />
						</label>
						
					<input type="radio" name="rate" id="happy" value="5" checked/>
						<label for="happy"><img class="sm" src="<?php echo plugin_dir_url( mo_hsso_options_plugin_constants::PLUGIN_FILE ) . 'images/happy.png'; ?>" />
						</label>
						
					<div id="outer" style="visibility:visible"><span id="result"><?php _e('Thank you for appreciating our work','Headless-Single-Sign-On');?></span></div>
					</div><br>
					<hr style="width:75%;">
					<?php $email = get_option("mo_saml_admin_email");
						if(empty($email)){
							$user = wp_get_current_user();
							$email = $user->user_email;
						}
						?>
					<div style="text-align:center;">
						
						<div style="display:inline-block; width:60%;">
						<input type="email" id="query_mail" name="query_mail" style="text-align:center; border:0px solid black; border-style:solid; background:#f0f3f7; width:20vw;border-radius: 6px;"
                              placeholder="<?php _e('Please enter your email address','Headless-Single-Sign-On');?>" required value="<?php echo $email; ?>" readonly="readonly"/>
						
						<input type="radio" name="edit" id="edit" onclick="editName()" value=""/>
						<label for="edit"><img class="editable" src="<?php echo plugin_dir_url( mo_hsso_options_plugin_constants::PLUGIN_FILE ) . 'images/61456.png'; ?>" />
						</label>
						
						</div>
						<br><br>
						<textarea id="query_feedback" name="query_feedback" rows="4" style="width: 60%"
                              placeholder="<?php _e('Tell us what happened!','Headless-Single-Sign-On');?>"></textarea>
						<br><br>
						  <input type="checkbox" name="get_reply" value="reply" checked><?php _e('miniOrange representative will reach out to you at the email-address entered above.','Headless-Single-Sign-On');?></input>
					</div>
					<br>
                   
                    <div class="mo-modal-footer" style="text-align: center;margin-bottom: 2%">
                        <input type="submit" name="miniorange_feedback_submit"
                               class="button button-primary button-large" value="<?php _e('Send','Headless-Single-Sign-On');?>"/>
						<span width="30%">&nbsp;&nbsp;</span>
                        <input type="button" name="miniorange_skip_feedback"
                               class="button button-primary button-large" value="<?php _e('Skip','Headless-Single-Sign-On');?>" onclick="document.getElementById('mo_hsso_feedback_form_close').submit();"/>
                    </div>
                </div>
				

            </form>
            <form name="f" method="post" action="" id="mo_hsso_feedback_form_close">
                <?php wp_nonce_field("mo_hsso_skip_feedback");?>
                <input type="hidden" name="option" value="mo_hsso_skip_feedback"/>
            </form>

        </div>

    </div>

    <script>
        jQuery('a[aria-label="Deactivate Headless Single Sign On"]').click(function () {

            var mo_modal = document.getElementById('feedback_modal');

            var span = document.getElementsByClassName("mo_close")[0];

            mo_modal.style.display = "block";
			document.querySelector("#query_feedback").focus();
            span.onclick = function () {
                mo_modal.style.display = "none";
                jQuery('#mo_hsso_feedback_form_close').submit();
            };

            window.onclick = function (event) {
                if (event.target === mo_modal) {
                    mo_modal.style.display = "none";
                }
            };
            return false;

        });

        const INPUTS = document.querySelectorAll('#smi_rate input');
        INPUTS.forEach(el => el.addEventListener('click', (e) => updateValue(e)));


        function editName(){

            document.querySelector('#query_mail').removeAttribute('readonly');
            document.querySelector('#query_mail').focus();
            return false;

        }
        function updateValue(e) {
            document.querySelector('#outer').style.visibility="visible";
            var result = '<?php _e('Thank you for appreciating our work','Headless-Single-Sign-On');?>';
            switch(e.target.value){
                case '1':	result = '<?php _e('Not happy with our plugin? Let us know what went wrong','Headless-Single-Sign-On');?>';
                    break;
                case '2':	result = '<?php esc_html_e('Found any issues? Let us know and we\'ll fix it ASAP','Headless-Single-Sign-On');?>';
                    break;
                case '3':	result = '<?php _e('Let us know if you need any help','Headless-Single-Sign-On');?>';
                    break;
                case '4':	result = '<?php esc_html_e('We\'re glad that you are happy with our plugin','Headless-Single-Sign-On');?>';
                    break;
                case '5':	result = '<?php _e('Thank you for appreciating our work');?>';
                    break;
            }
            document.querySelector('#result').innerHTML = result;

        }
    </script><?php
}

?>