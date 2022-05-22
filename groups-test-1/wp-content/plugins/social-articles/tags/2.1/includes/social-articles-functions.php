<?php
if ( !defined( 'ABSPATH' ) ) exit;

function social_articles_load_template_filter( $found_template, $templates ) {
    global $bp;
    if( !bp_sa_is_bp_default() || !bp_is_current_component( $bp->social_articles->slug )){
        return $found_template;
    }
    foreach ( (array) $templates as $template ) {
        if ( file_exists( STYLESHEETPATH . '/' . $template ) )
            $filtered_templates[] = STYLESHEETPATH . '/' . $template;
        else
            $filtered_templates[] = dirname( __FILE__ ) . '/templates/' . $template;
    }
    $found_template = $filtered_templates[0];
    return apply_filters( 'social_articles_load_template_filter', $found_template );
}
add_filter( 'bp_located_template', 'social_articles_load_template_filter', 10, 2 );


function social_articles_load_sub_template( $template ) {
    if( empty( $template ) )
        return false;

    if( bp_sa_is_bp_default() ) {
        if ( $located_template = apply_filters( 'bp_located_template', locate_template( $template , false ), $template ) )
            load_template( apply_filters( 'bp_load_template', $located_template ) );

    } else {
        bp_get_template_part( $template );
    }
}

function get_short_text($text, $limitwrd ) {   
    if (str_word_count($text) > $limitwrd) {
      $words = str_word_count($text, 2);
      if ($words > $limitwrd) {
          $pos = array_keys($words);
          $text = substr($text, 0, $pos[$limitwrd]) . ' [...]';
      }
    }
    return $text;
}

function custom_get_user_posts_count($status, $kind_of_user='logged_user'){
    $args = array();

    if($kind_of_user == 'displayed_user')
        $user_id =  bp_displayed_user_id();
    if($kind_of_user == 'logged_user')
        $user_id =  bp_loggedin_user_id();

    $args['post_status'] = $status;
    $args['author'] = $user_id;
    $args['fields'] = 'ids';
    $args['posts_per_page'] = "-1";
    $args['post_type'] = SA_Helper::get_post_type();
    $ps = get_posts($args);
    return count($ps);
}

add_action('save_post','social_articles_send_notification');
function social_articles_send_notification($id){
    global $bp, $socialArticles;
    $savedPost = get_post($id);
    $notification_already_sent = get_post_meta($id, 'notification_already_sent', true);
    if(empty($notification_already_sent) && function_exists("friends_get_friend_user_ids") && $savedPost->post_status == "publish" && $savedPost->post_type=="post" && !wp_is_post_revision($id) && $socialArticles->options['bp_notifications'] == "true"){
        $friends = friends_get_friend_user_ids($savedPost->post_author);
        foreach($friends as $friend):
            bp_notifications_add_notification(array(
                'user_id'           => $friend,
                'item_id'           => $savedPost->ID,
                'secondary_item_id' => $savedPost->post_author,
                'component_name'    => $bp->social_articles->id,
                'component_action'  => 'new_article'.$savedPost->ID,
                'date_notified'     => false,
                'is_new'            => 1,
                'allow_duplicate'   => false,
            ));
        endforeach;
        bp_notifications_add_notification(array(
            'user_id'           => $savedPost->post_author,
            'item_id'           => $savedPost->ID,
            'secondary_item_id' => -1,
            'component_name'    => $bp->social_articles->id,
            'component_action'  => 'new_article'.$savedPost->ID,
            'date_notified'     => false,
            'is_new'            => 1,
            'allow_duplicate'   => false,
        ));
        update_post_meta($id, 'notification_already_sent', true);
    }
}

function social_articles_format_notifications( $action, $item_id, $secondary_item_id, $total_items, $format = 'string' ) {
    do_action( 'social_articles_format_notifications', $action, $item_id, $secondary_item_id, $total_items, $format );

    $createdPost = get_post($item_id);

    if($secondary_item_id == "-1"){
         $text = '</a> <div id="'.$action.'" class="sa-notification">'.
                    __("One of your articles was approved","social-articles").'<a class="ab-item" title="'.$createdPost->post_title.'"href="'.get_permalink( $item_id ).'">, '.__("check it out!", "social-articles").'
                  </a> 
                  <a href="#" class="social-delete" onclick="deleteArticlesNotification(\''.$action.'\',\''.$item_id.'\', \''.admin_url( 'admin-ajax.php' ).'\'); return false;">x</a><span class="social-loader"></span></div>';
    
    }else{
        $creator = get_userdata($secondary_item_id); 
        $text = '</a> <div id="'.$action.'"class="sa-notification">'.
                    __("There is a new article by ", "social-articles").'<a class="ab-item" href="'.get_bloginfo('blog').'/members/'.$creator->user_login.'">'.$creator->user_nicename.', </a>
                 <a class="ab-item" title="'.$createdPost->post_title.'"href="'.get_permalink( $item_id ).'"> '.__("check it out!", "social-articles").'
                 </a> 
                 <a href="#" class="social-delete" onclick="deleteArticlesNotification(\''.$action.'\',\''.$item_id.'\', \''.admin_url( 'admin-ajax.php' ).'\'); return false;">x</a><span class="social-loader"></span></div>';
    }
    return $text;
}


function bp_sa_is_bp_default() {
    if(current_theme_supports('buddypress') || in_array( 'bp-default', array( get_stylesheet(), get_template() ) )  || ( defined( 'BP_VERSION' ) && version_compare( BP_VERSION, '1.7', '<' ) ))
        return true;
    else {
        return false; //wordpress theme
    }
}

function isDirectWorkflow(){
    global $socialArticles;
    return $socialArticles->options['workflow'] == 'direct' ;
}

add_action( 'wp_head', 'sa_notifications_stuff' );
function sa_notifications_stuff(){
    echo "
    <style>
        .sa-notification {
            min-height: 30px !important;
            width: calc(100% - 10px)!important;
            padding-left: 10px !important;
            text-shadow: none !important;
            min-width: 320px !important;
            padding-right: 21px !important;
            line-height: 12px !important;
            margin-bottom: 8px !important;
        }
        .sa-notification a {
            display: inline !important;            
            min-width: 0 !important;
            padding: 0 !important;
        }
        
        .sa-notification .social-delete{
            position: absolute !important;
            right: 5px;
            border: 1px solid !important;
            line-height: 10px !important;
            height: auto !important;
            padding: 3px !important;
            top: 9px;
            padding-bottom: 5px !important;
        }        
       
       
        table.notifications .sa-notification{
            margin-bottom: 0 !important;
            min-height: 0 !important;
        }
       
        table.notifications .sa-notification .social-delete{
            display: none !important;
           
        }
       
        .social-loader {
            background: url('".SA_BASE_URL."/assets/images/loading.svg') no-repeat;            
            position: absolute !important;
            right: 4px;
            top: 9px;
            z-index: 10;
            display: none;
            width: 16px !important;
            height: 20px !important;
            background-size: contain!important;
        }
        
        #wpadminbar .menupop .ab-sub-wrapper{
          /*  display:block*/
        }
       
    </style>";

    echo '<script>
        jQuery(function(){
                jQuery(".sa-notification").prev().hide();        
        });

        function deleteArticlesNotification(action_id, item_id, adminUrl){
            //jQuery("#"+action_id).children(".social-delete").html("");
            jQuery("#wp-admin-bar-bp-notifications #"+action_id ).children(".social-loader").show();
            jQuery("#wp-admin-bar-bp-notifications #"+action_id + " .social-delete").css("visibility","hidden");

            jQuery.ajax({
                type: "post",
                url: adminUrl,
                data: { action: "deleteArticlesNotification", action_id:action_id, item_id:item_id },
                success:
                function(data) {
                    jQuery("#wp-admin-bar-bp-notifications #"+action_id).parent().remove();
                    jQuery("#ab-pending-notifications").html(jQuery("#ab-pending-notifications").html() - 1);
                }
             });             
        }
    </script>';
}

add_action( 'sa_counters', 'add_counters_counters');
function add_counters_counters(){
    $publishCount_displayed = custom_get_user_posts_count('publish', 'displayed_user');
    $publishCount = custom_get_user_posts_count('publish', 'logged_user');
    $pendingCount = custom_get_user_posts_count('pending', 'logged_user');
    $draftCount = custom_get_user_posts_count('draft', 'logged_user');

    if(isDirectWorkflow()){
        $postCount = $draftCount + $publishCount;
    }else{
        $postCount = $draftCount + $publishCount + $pendingCount;
    }

    echo "<script>
                jQuery(function () {
                    jQuery('#articles span').html('".$publishCount_displayed."');
                    jQuery('#draft span').html('".$draftCount."');
                    jQuery('#under-review span').html('".$pendingCount."');
                    jQuery('#wp-admin-bar-article-list-item a span.count').html('".$postCount."');
                });
          </script>";
}


?>