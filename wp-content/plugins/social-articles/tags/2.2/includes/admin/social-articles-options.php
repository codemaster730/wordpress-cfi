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
                        <a class="nav-tab" id="get-premium-tab" href="#top#get-premium"><?php _e( 'Get Premium', 'social-articles' );?></a>
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
                        <div id="get-premium" class="sa-tab">
                            <?php include(SA_BASE_PATH.'/includes/admin/tabs/social-articles-get-premium-settings.php'); ?>
                        </div>
                    </div>


                </form>
            </div>
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