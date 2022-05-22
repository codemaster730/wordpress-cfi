<?php
function social_articles_page() {?>


    <?php
    global $socialArticles;
    $socialArticles->form_settings->init_form_instance();

    $options = get_option('social_articles_options');

    if (isset($_POST['form_submit'])) {

        /*General and View Options*/
        $options['post_per_page'] = isset($_POST['post_per_page']) ? $_POST['post_per_page'] : '';
        $options['excerpt_length'] = isset($_POST['excerpt_length']) ? $_POST['excerpt_length'] : '';
        $options['published_post_counter'] = isset($_POST['published_post_counter']) ? $_POST['published_post_counter'] : '';

        $options['category_type'] = isset($_POST['category_type']) ? $_POST['category_type'] : '';
        $options['workflow'] = isset($_POST['workflow']) ? $_POST['workflow'] : '';
        $options['bp_notifications'] = isset($_POST['bp_notifications']) ? $_POST['bp_notifications'] : '';
        $options['allow_author_adition'] = isset($_POST['allow_author_adition']) ? $_POST['allow_author_adition'] : '';
        $options['allow_author_deletion'] = isset($_POST['allow_author_deletion']) ? $_POST['allow_author_deletion'] : '';
        $options['show_to_logged_out_users'] = isset($_POST['show_to_logged_out_users']) ? $_POST['show_to_logged_out_users'] : '';

        $options = apply_filters('saf_save_extra_settings',$options); //followers
        $options = apply_filters('sa_save_extra_settings',$options); //toolbox

        update_option('social_articles_options', $options);

        /*Form*/
        $fields = array();
        $fields_list = explode(',',$_POST['selected_fields']);
        foreach ($fields_list as $field){
            $fields[$field] = json_decode(stripslashes($_POST[$field.'_config']));
        }
        $socialArticles->form_settings->save_form_instance($fields);

        /*Message*/
        echo '<div class="updated fade"><p>' . __('Settings Saved', 'social-articles') . '</p></div>';
    }

    ?>


    <div class="wrap options-social-articles">

        <div class="wrap">

            <div id="icon-themes" class="icon32"></div>
            <h2><?php _e( "Social Articles Settings", 'social-articles' ) ?></h2>


        <div class="postbox-container" id="postbox-container-2">

            <div class="meta-box-sortables ui-sortable">
                <form id="form_data" name="form" method="post">
                    <input type="hidden" name="form_submit" value="true" />
                    <h2 class="nav-tab-wrapper" id="sa-admin-tabs">
                        <a class="nav-tab" id="general-tab" href="#top#general"><?php _e( 'General', 'social-articles' );?></a>
                        <a class="nav-tab" id="view-tab" href="#top#view"><?php _e( 'View', 'social-articles' );?></a>
                        <a class="nav-tab" id="form-tab" href="#top#form"><?php _e( 'Form', 'social-articles' );?></a>
                        <a class="nav-tab" id="troubleshooting-tab" href="#top#troubleshooting"><?php _e( 'Troubleshooting', 'social-articles' );?></a>
                        <a class="nav-tab" id="addons-tab" href="#top#addons"><?php _e( 'Add-ons', 'social-articles' );?></a>
                        <span id="changes-detected-message"><span class="dashicons dashicons-warning" style="color: #ff4b4b;"></span><?php _e( 'There are unsaved changes', 'social-articles');?>
                    </h2>

                    <div class="tabwrapper">
                        <div id="general" class="sa-tab">
                            <?php include(SA_BASE_PATH.'/includes/admin/tabs/social-articles-general-settings.php'); ?>
                        </div>
                        <div id="view" class="sa-tab">
                            <?php include(SA_BASE_PATH.'/includes/admin/tabs/social-articles-view-settings.php'); ?>
                        </div>
                        <div id="form" class="sa-tab">
                            <?php include(SA_BASE_PATH.'/includes/admin/tabs/social-articles-form-settings.php'); ?>
                        </div>
                        <div id="troubleshooting" class="sa-tab">
                            <?php include(SA_BASE_PATH.'/includes/admin/tabs/social-articles-troubleshooting.php'); ?>
                        </div>
                        <div id="addons" class="sa-tab">
                            <?php include(SA_BASE_PATH.'/includes/admin/tabs/social-articles-get-premium-settings.php'); ?>
                        </div>
                    </div>


                </form>
            </div>
        </div>

            <div class="postbox-container" id="postbox-container-1" style="    text-align: center; background: #FFF; border-top: 3px solid;">
                <div id="side-sortables" class="meta-box-sortables ui-sortable">
                    <?php ob_start();?>
                    <p><strong><?php _e( 'Want to help make this plugin even better? All donations are used to improve this plugin, so donate $20, $50 or $100 now!', 'social-articles' )?></strong></p>

                    <a target="_blank" href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=PXXBLQV92XEXL">
                        <img src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif">
                    </a>
                    <p><?php _e( 'Or you could:', 'social-articles' )?></p>
                    <ul>
                        <li><a target="_blank" href="http://wordpress.org/extend/plugins/social-articles/"><?php _e( 'Rate the plugin 5★ on WordPress.org', 'social-articles' )?></a></li>
                        <li><a target="_blank" href="http://wordpress.org/tags/social-articles"><?php _e( 'Help out other users in the forums', 'social-articles' )?></a></li>
                        <li><?php printf( __( 'Blog about it & link to the %1$splugin page%2$s', 'social-articles' ), '<a target="_blank" href="http://www.broobe.com/plugins/social-articles/#utm_source=wpadmin&utm_medium=sidebanner&utm_term=link&utm_campaign=social-articles-plugin">', '</a>')?></li>
                    </ul>
                    <?php $donate = ob_get_contents();?>
                    <?php ob_end_clean();?>
                    <?php $socialArticles->postbox( 'social-articles-donation', '<strong class="blue">' . __( 'Help Spread the Word!', 'social-articles' ) . '</strong>', $donate);?>
                </div>
                <br/>
            </div>

    </div>
    <script>
        jQuery(function(){
            jQuery('#form_data input, #form_data select').on('change', function(){
                jQuery('#changes-detected-message').show();

            })
        })

    </script>


    <?php
}


?>