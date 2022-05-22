<?php
    global $bp, $post, $wpdb, $socialArticles;

    $directWorkflow = isDirectWorkflow();
    $initialCount = $socialArticles->options['post_per_page'];
    $publishCount = custom_get_user_posts_count('publish', 'displayed_user');
?>

<section id="articles-container">     
    <?php if($publishCount > 0 || bp_displayed_user_id()==bp_loggedin_user_id()):?>    

    <?php do_action('sa_prev_articles_list');?>

    <div class="publish-container">    
        <?php get_articles(0, 'publish');?>
        <div id="more-container-publish">
        </div>    
        <?php
        if($publishCount > $initialCount){ ?>
        <div class="more-articles-button-container">       
            <input type="submit" id="more-articles-button" class="button" onclick ="getMoreArticles('publish'); return false;" value="<?php _e("Load more articles", "social-articles");?>"/>       
            <img width="60px" id="more-articles-loader" src="<?php echo SA_BASE_URL . '/assets/images/loading.svg' ; ?>"/>
        </div>       
        <?php
        }
        ?>
    </div>
    <?php else: ?>
    <div id="message" class="messageBox note icon">
        <span><?php _e("This user doesn't have any article.", "social-articles");?></span>
    </div>
    <?php endif;?>
</section>     

<input type="hidden" value="<?php echo $initialCount;?>" id="inicialcount"/>
<input type="hidden" value="<?php echo $publishCount;?>" id="postcount"/>
<input type="hidden" value="<?php echo $initialCount;?>" id="offset"/>
<input type="hidden" value="<?php echo "publish";?>" id="current-state"/>

