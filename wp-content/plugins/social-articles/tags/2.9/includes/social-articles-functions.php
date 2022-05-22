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
      if (!empty($limitwrd) && $words > $limitwrd) {
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
    $ps = get_posts(apply_filters('sa_user_posts_count',$args));
    return count($ps);
}

add_action('save_post','social_articles_send_notification');
function social_articles_send_notification($id){
    global $bp, $socialArticles;

    if(function_exists('bp_notifications_add_notification')) {
        $savedPost = get_post($id);
        $notification_already_sent = get_post_meta($id, 'notification_already_sent', true);
        if (empty($notification_already_sent) &&
            $savedPost->post_status == "publish" &&
            $savedPost->post_type == "post" &&
            !wp_is_post_revision($id)
        ):

            $friends = array();
            if (function_exists("friends_get_friend_user_ids") &&
                $socialArticles->options['bp_notifications'] == "true"
            ) {
                $friends = friends_get_friend_user_ids($savedPost->post_author);
            }

            $friends = apply_filters('saf_add_extra_friends', $friends, $savedPost->post_author, $socialArticles);

            foreach ($friends as $friend):
                bp_notifications_add_notification(array(
                    'user_id' => $friend,
                    'item_id' => $savedPost->ID,
                    'secondary_item_id' => $savedPost->post_author,
                    'component_name' => $bp->social_articles->id,
                    'component_action' => 'new_article' . $savedPost->ID,
                    'date_notified' => bp_core_current_time(),
                    'is_new' => 1,
                    'allow_duplicate' => false,
                ));
            endforeach;

            if (!isDirectWorkflow()) {
                bp_notifications_add_notification(array(
                    'user_id' => $savedPost->post_author,
                    'item_id' => $savedPost->ID,
                    'secondary_item_id' => -1,
                    'component_name' => $bp->social_articles->id,
                    'component_action' => 'new_article' . $savedPost->ID,
                    'date_notified' => bp_core_current_time(),
                    'is_new' => 1,
                    'allow_duplicate' => false,
                ));
            }
            update_post_meta($id, 'notification_already_sent', true);

        endif;
    }
}

function social_articles_format_notifications( $action, $item_id, $secondary_item_id, $total_items, $format) {

    do_action( 'social_articles_format_notifications', $action, $item_id, $secondary_item_id, $total_items, $format );

    $createdPost = get_post($item_id);
    $security = wp_create_nonce( "sa_security_ajax" );

    if($secondary_item_id == -1){
        if("string" === $format){
            $return  = '<div id="'.$action.'" class="sa-notification">'.
                            __("One of your articles was approved","social-articles").
                            '<a class="ab-item" title="'.$createdPost->post_title.'" href="'.get_permalink( $item_id ).'">, '.__("check it out!", "social-articles").'</a>
                        </div>';
        }else{
            $return =  array(
                'text' => '<span class="sa-text-notification" data-action="'.$action.'" data-item="'.$item_id.'" data-admin-url="'.admin_url( 'admin-ajax.php' ).'" data-security="'.$security.'" ></span>'.__("One of your articles was approved","social-articles").' - '.$createdPost->post_title,
                'link' => get_permalink( $item_id )
            );
        }
    }else{
        $creator = get_userdata($secondary_item_id); 

        if("string" === $format){
            $return = '<div id="'.$action.'"class="sa-notification"><span>'.
                __("There is a new article by ", "social-articles").'</span><a class="ab-item" href="'.bp_core_get_user_domain($secondary_item_id).'">'.$creator->user_nicename.', </a>
                 <a class="ab-item" title="'.$createdPost->post_title.'" href="'.get_permalink( $item_id ).'"> '.__("check it out!", "social-articles").'
                 </a> 
                 </div>';

        }else{
            $return =  array(
                'text' => '<span class="sa-text-notification" data-action="'.$action.'" data-item="'.$item_id.'" data-admin-url="'.admin_url( 'admin-ajax.php' ).'" data-security="'.$security.'" ></span>'.__("There is a new article by ", "social-articles").$creator->user_nicename.' - '.$createdPost->post_title,
                'link' => get_permalink( $item_id )
            );
        }

    }
    return $return;
}

function bp_sa_is_bp_default() {
    if(current_theme_supports('buddypress') || in_array( 'bp-default', array( get_stylesheet(), get_template() ) )  || ( defined( 'BP_VERSION' ) && version_compare( BP_VERSION, '1.7', '<' ) ))
        return true;
    else {
        return false; //wordpress theme
    }
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
        .sa-text-delete{
            margin-left: 10px !important;
            border: 1px solid;
            padding: 0 5px 2px 5px !important;
            line-height: 10px !important;
            font-weight: bold !important;
        }
       
    </style>";

    echo '<script>
        jQuery(function(){
                jQuery(".sa-notification").prev().hide();   
                jQuery.each(jQuery(".sa-text-notification"), function(){               
                     
                    var link =  jQuery(this).parent();                                    
                    link.attr("data-item", jQuery(this).attr("data-item"));
                    link.attr("data-action", jQuery(this).attr("data-action"));
                    link.attr("data-admin-url", jQuery(this).attr("data-admin-url"));
                    link.attr("data-security", jQuery(this).attr("data-security"));
                    link.on("click", function(e){
                        e.preventDefault();
                        markArticlesNotification(jQuery(this).attr("data-action"),jQuery(this).attr("data-item"),jQuery(this).attr("data-admin-url"),jQuery(this).attr("data-security"));                      
                        var href = jQuery(this).attr("href");
                        setTimeout(function() {window.location = href}, 1000);
                    })
                })
        });

        function markArticlesNotification(action_id, item_id, adminUrl, security){
            jQuery.ajax({
                type: "post",
                url: adminUrl,
                async:"false",
                data: { action: "markArticlesNotification",  security:security, action_id:action_id, item_id:item_id },
                success:
                function(data) {
                    //jQuery("#wp-admin-bar-bp-notifications #"+action_id).parent().remove();
                    //jQuery("#ab-pending-notifications").html(jQuery("#ab-pending-notifications").html() - 1);
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
                    jQuery('.sa-global-counter').html('".$publishCount_displayed."');
                });
          </script>";
}

add_action('wp_ajax_dismiss_sa_message', 'dismiss_sa_message' );
function dismiss_sa_message(){
    check_ajax_referer( 'sa_security_ajax', 'security');
    update_option('dismiss_sa_message',SA_PLUGIN_VERSION);
    die();
}

add_action('wp_ajax_reset_sa_options', 'reset_sa_options' );
function reset_sa_options(){
    delete_option('social_articles_options');
    delete_option('sa_registered_fields');
    delete_option('sa_selected_fields');
    $options = array();
    $options = set_default_options($options);
    update_option('social_articles_options',$options);

    echo "ok";
    die();
}

add_action( 'admin_notices', 'bp_social_articles_addons_notice');
function bp_social_articles_addons_notice() {
    $security = wp_create_nonce( "sa_security_ajax" );
    $current_user = wp_get_current_user();

    if(get_option('dismiss_sa_message') != SA_PLUGIN_VERSION) {
        echo '<div class="sa-notice info notice-info notice">';
        echo '<p>' . __('Thanks for using Social Articles! ', 'social-articles');

        echo '<a type="button" class="sa-settings-button button button-primary" href="/wp-admin/options-general.php?page=social-articles">Settings</a> ';
        echo '<a type="button" class="sa-addons-button button button-primary" target="_blank" href="https://social-articles.com/?utm_source=social_articles_free&utm_medium=top_button&utm_campaign=sa_free_'.str_replace('.','_',SA_PLUGIN_VERSION).'&utm_content='.$current_user->user_email.'">Check out our premium add-ons</a>';
        echo '</p>';



        echo '<button type="button" class="sa-dismiss-notice notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>';
        echo '</div>';

        echo '  <script>
                    jQuery(function(){
                        jQuery(".sa-dismiss-notice").on("click", function(){
                            jQuery(".sa-notice").fadeOut();
                            
                            jQuery.ajax({
                                type: "post",
                                url: "'.admin_url( 'admin-ajax.php' ).'",
                                data: {action: "dismiss_sa_message", security: "'.$security.'"}
                            })                        
                                    
                        })                    
                    })
                </script>';
    }
    echo '
    <style>    
            .sa-notice{
                background: #333 url("'.SA_BASE_URL. '/includes/admin/assets/images/pattern.png") no-repeat;
                background-size: cover;
                color: #FFF;
                min-height: 48px;
            }
            
            .sa-notification {
                float: left !important;
                width: 100% !important;
            }
            
            .sa-notification a{
                display: inline !important;
                padding: 0 !important;
                min-width: 0 !important;
            }
            .sa-settings-button:before{
                background: 0 0;
                color: #fff;
                content: "\f111";
                display: block;
                font: 400 16px/20px dashicons;
                speak: none;
                height: 29px;
                text-align: center;
                width: 16px;
                float: left;
                margin-top: 3px;
                margin-right: 4px;
            }
            
            .sa-addons-button:before{
                background: 0 0;
                color: #fff;
                content: "\f106";
                display: block;
                font: 400 16px/20px dashicons;
                speak: none;
                height: 29px;
                text-align: center;
                width: 16px;
                float: left;
                margin-top: 3px;
                margin-right: 4px;
            }
            .sa-addons-button, .sa-addons-button:visited,.sa-addons-button:active{
                background: #42c9ff !important;
                border-color: #42c9ff !important; 
                color: #fff !important;
                text-decoration: none !important;
                text-shadow: none!important;
                box-shadow: none !important;
            }
            
            .sa-addons-button:hover{
                background:#46beff !important;
                border-color: #46beff !important; 
            }
            
            
            .sa-dismiss-notice{
                top:5px        
            }
            .sa-dismiss-notice:hover:before, .sa-dismiss-notice:focus:before, .sa-dismiss-notice:visited:before{
                color:#46beff !important;
            }
                        
            .sa-notice{
                position:relative
            }
    </style>';

}


?>