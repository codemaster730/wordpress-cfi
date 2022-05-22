
<?php
    $rows = array();

    $rows[] = array(
        'id'      => 'workflow',
        'label'   => __('Select workflow type','social-articles'),
        'content' => $socialArticles->select( 'workflow', array(
            'direct' => __('Direct Publish', 'social-articles'),
            'approval'  => __('With Approval ', 'social-articles'),
        ), false, "", ""
        ),
    );

    $status = "";
    $msg = "";
    if(!function_exists("friends_get_friend_user_ids")){
        $status="disabled";
        $msg=__("To use this feature, you need to activate bb Friend Connections module.", "social-articles");
    }

    $rows[] = array(
        'id'      => 'bp_notifications',
        'label'   => __('Send buddyPress notifications?','social-articles').' - BP Friends',
        'content' => $socialArticles->select( 'bp_notifications', array(
            'false' => __('False', 'social-articles'),
            'true'  => __('True', 'social-articles'),
        ), false, $status, $msg
        ),
    );


    $rows = apply_filters('sa_addon_followers_settings',$rows,$socialArticles);


    $rows[] = array(
        'id'      => 'allow_author_adition',
        'label'   => __('Can users edit their articles?','social-articles'),
        'content' => $socialArticles->select( 'allow_author_adition', array(
            'false' => __('False', 'social-articles'),
            'true'  => __('True', 'social-articles'),
        ), false
        ),
    );

    $rows[] = array(
        'id'      => 'allow_author_deletion',
        'label'   => __('Can users delete their articles?','social-articles'),
        'content' => $socialArticles->select( 'allow_author_deletion', array(
            'false' => __('False', 'social-articles'),
            'true'  => __('True', 'social-articles'),
        ), false
        ),
    );

    $rows = apply_filters('sa_add_extra_settings',$rows,$socialArticles);
    
    $save_button = '<div class="submitbutton"><input type="submit" class="button-primary" name="submit" value="'.__('Update Social Articles Settings','social-articles'). '" /></div><br class="clear"/>';
    $socialArticles->postbox( 'social_articles_general_options', __( 'General', 'social-articles' ), $socialArticles->form_table( $rows ) . $save_button);
    ?>
