
<div class="meta-box-sortables ui-sortable">
                        <?php ob_start();?>
<div class="premium-feature">
    <div class="premium-title">
        <img src="<?php echo SA_BASE_URL;?>/assets/images/bp-logo.png"/>
        <h1><?php _e("The first add-on is ready!", 'social-articles')?></h1>
    </div>
    <ul>
         <li><span><?php _e("Javascript fields validation", 'social-articles')?></span></li>
         <li><span><?php _e("You can set wich fields are/aren't required", 'social-articles')?></span></li>
         <li><span><?php _e("Users can create terms in taxonomies", 'social-articles')?></span></li>
         <li><span><?php _e("You can let users choose more than one term", 'social-articles')?></span></li>
         <li><span><?php _e("You can limit the number of terms that a user can choose", 'social-articles')?></span></li>
         <li><span><?php _e("You can blacklist terms in taxonomies", 'social-articles')?></span></li>
         <li><span><?php _e("You can show term parents in different level (only for hierarchical taxonomies)", 'social-articles')?></span></li>
         <li><span><?php _e("If you have more taxonomies created in posts (in addition to tags and categories) they will be available as fields to include in Social Articles form.", 'social-articles')?></span></li>
         <li><span><?php _e("You can limit the number of characters in title and content", 'social-articles')?></span></li>
         <li><span><?php _e("You can active the upload image button in content", 'social-articles')?></span></li>
         <li><span><?php _e("You can set  fields height", 'social-articles')?></span></li>
         <li><span><?php _e("You can choose the WP roles that can use Social Articles", 'social-articles')?></span></li>

    </ul>
    <a class="button-primary" target="_blank" href="https://www.broobe.com/contact/">Price USD59.- Contact us</a>


</div>
<?php $features = ob_get_contents();?>
<?php ob_end_clean();?>
<?php $socialArticles->postbox( 'social_articles_premium_options', __('Premium Features', 'social-articles'), $features);?>


    <div class="postbox-container" id="postbox-container-1">
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