
<div class="meta-box-sortables ui-sortable">
                        <?php ob_start();?>
<div class="premium-feature">
    <div class="premium-title">
        <img src="<?php echo SA_BASE_URL;?>/assets/images/bp-logo.png"/>
        <h1><?php _e("Coming Soon!!!", 'social-articles')?></h1>
    </div>
    <ul>
        <!--
        <li><span><?php _e("Mail Notifications", 'social-articles')?></span>
            <small><?php _e("Mail Notification sends mail to your mailboxes. When new notification arrives, Mail Notification sends a new email.", 'social-articles')?></small>
        </li>
        <li><span><?php _e("Html Template Customization", 'social-articles')?></span>
            <small><?php _e("Customize your email with HTML Template Customizations!", 'social-articles');?></small>
        </li>-->
        <li><span><?php _e("Custom Post Type definition", 'social-articles')?></span></li>
        <li><span><?php _e("Custom taxonomies", 'social-articles')?></span></li>
        <li><span><?php _e("Taxonomies Black/White List", 'social-articles')?></span></li>
        <li><span><?php _e("Hierarchical taxonomies", 'social-articles')?></span></li>
        <li><span><?php _e("Ability to add terms from the front end", 'social-articles')?></span></li>
        <li><span><?php _e("Required Fields definition", 'social-articles')?></span></li>
        <li><span><?php _e("Required Fields Messages definition", 'social-articles')?></span></li>
        <li><span><?php _e("More Editor Functions", 'social-articles')?></span></li>
        <li><span><?php _e("Premium features for each field", 'social-articles')?></span></li>
        <li><span><?php _e("Ability to change labels", 'social-articles')?></span></li>
        <li><span><?php _e("More!", 'social-articles')?></span></li>


    </ul>
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