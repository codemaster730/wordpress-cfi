<?php
add_action('wp_ajax_nopriv_delete_article', 'delete_article' );
add_action('wp_ajax_delete_article', 'delete_article' );  
function delete_article(){
    global $socialArticles;
    $postToDelete = get_post($_POST['post_id']);

    if($postToDelete->post_status == 'publish'){
        $status_id = 'articles';
    }else{
        if($postToDelete->post_status == 'pending'){
            $status_id = 'under-review';
        }else{
            $status_id = $postToDelete->post_status;
        }
    }

    if(bp_loggedin_user_id()==$postToDelete->post_author && ($socialArticles->options['allow_author_deletion']=="true" || $postToDelete->post_status=="draft") ){
       wp_delete_post($_POST['post_id']);
       echo json_encode(array("status"=>"ok", 'post_status'=>$status_id));
    }else{
       echo json_encode(array("status"=>"error"));
    }
    die();    
}

add_action('wp_ajax_nopriv_get_more_articles', 'get_more_articles' );
add_action('wp_ajax_get_more_articles', 'get_more_articles' );  
function get_more_articles(){    
    $offset = $_POST['offset'];
    $status = $_POST['status'];
    ob_start();            
    get_articles($offset, $status);        
    $out = ob_get_contents();
    ob_end_clean();      
    echo $out;
    die();    
}

add_action('wp_ajax_nopriv_deleteArticlesNotification', 'delete_articles_notification' );
add_action('wp_ajax_deleteArticlesNotification', 'delete_articles_notification' );
function delete_articles_notification(){
    global $bp;
    $user_id=$bp->loggedin_user->id;
    $item_id=$_POST['item_id'];
    $component_name='social_articles';
    $component_action=$_POST['action_id'];
    bp_notifications_delete_notifications_by_item_id($user_id, $item_id, $component_name, $component_action);
    die();        
}

function get_articles($offset, $status, $all = false){
    global $bp, $post, $socialArticles;
    if($all){
       $postPerPage = -1;
    }else{
       $postPerPage = $socialArticles->options['post_per_page']; 
    }        

    $args = array(     'post_status'       => $status,
                       'ignore_sticky_posts'    => 1,                       
                       'posts_per_page'    => $postPerPage,
                       'offset'            => $offset,                      
                       'post_type'         => SA_Helper::get_post_type(),
                       'author'            => bp_displayed_user_id()                                    
                 );                 
    
    $articles_query = new WP_Query( $args );
             
    if ($articles_query->have_posts()):
        while ($articles_query->have_posts()):
            $articles_query-> the_post();
            $allCategories = array();
            $categories = get_the_category();
            for($i=0; $i < count($categories); $i++){                                                                
                $allCategories[]='<a href="'.get_category_link( $categories[$i]->cat_ID ).'" >'.
                                    $categories[$i]->cat_name.
                                 '</a>';                
            }      
                 
            $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), "thumbnail");                      
            if( $image == null){
                $image = "NO-IMAGE";  
            }else{
                $image = $image[0];
            }
            
            ?>            
             <article id="<?php echo $post->ID; ?>" class="article-container">              
                <div class="article-content">
                    <div class="article-metadata">
                        <?php if(bp_displayed_user_id()==bp_loggedin_user_id()):?>
                            <div class="author-options">
                                <?php if($socialArticles->options['allow_author_adition']=="true" || $post->post_status=="draft"):?>
                                    <a class="edit" title="<?php _e("edit article", "social-articles" );?>" href="<?php echo $bp->loggedin_user->domain.'articles/new?article='.$post->ID;?>"></a>
                                <?php endif;?>
                                <?php if($socialArticles->options['allow_author_deletion']=="true" || $post->post_status=="draft"):?>
                                    <a class="delete" title="<?php _e("delete article", "social-articles" );?>" id="delete-<?php echo $post->ID; ?>" href="#" onclick="deleteArticle(<?php echo $post->ID; ?>); return false;"></a>
                                <?php endif;?>
                            </div>
                        <?php endif;?>
                        <div class="clear"></div>
                    </div>
                                        

                    <?php
                    $background_image = "";
                    $empty_class= "";
                    if($image!="NO-IMAGE"):
                        $background_image = "background:url('".$image."') no-repeat;";
                    else:
                        $empty_class = ' sa-no-image';
                    endif;?>

                    <div class="article-image <?php echo $empty_class;?>" style="<?php echo $background_image;?>">
                    </div>                    
                    <div class="article-data">
                        <h3 class="title"><a href="<?php the_permalink();?>"><?php the_title(); ?></a></h3>                        
                        <span class="date"><?php the_time('j');?>&nbsp;<?php the_time('F');?>&nbsp;<?php the_time('Y');?></span>                         
                        <div class="excerpt">                                                           
                            <?php echo get_short_text(get_the_excerpt(),$socialArticles->options['excerpt_length']);?>                        
                        </div>                          
                    </div>    
                    

                    <div class="article-footer">
                        <div class="article-categories">
                            <?php _e("Archived", "social-articles" ); echo ": ".implode(" | ",$allCategories);?>
                        </div>
                        <div class="article-likes">
                            <a href="<?php echo get_comments_link( $post->ID ); ?>">
                                <span class="likes-count">
                                      <?php $comments = wp_count_comments( $post->ID ); echo $comments->approved; ?>
                                </span>
                                <span class="likes-text"><?php _e("comments", "social-articles" )?></span>
                            </a>
                        </div>
                    </div>
                    <div style="clear:both"></div>                          
                </div>          
            </article>                  
            <?php endwhile; ?>        
        <?php endif;          
	wp_reset_query();
}
?>