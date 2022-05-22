
<div class="meta-box-sortables ui-sortable">
<?php
$current_user = wp_get_current_user();


ob_start();?>
<div class="premium-feature">

    <div class="sa-addons-container">
        <div class="sa-addon toolbox">

            <div class="sa-addon-button sa-buynow">
                <a target="_blank" href="https://social-articles.com/product/toolbox/?utm_source=social_articles_free&utm_medium=buy_addon_button&utm_campaign=sa_free_<?php echo str_replace('.','_',SA_PLUGIN_VERSION);?>&utm_content=<?php echo $current_user->user_email;?>">
                Buy now - USD 59.-
                </a>
            </div>
            
            <div class="sa-addon-image">
                <img src="<?php echo SA_BASE_URL. '/includes/admin/assets/images/toolbox.jpg'?>">
            </div>
            <div class="sa-addon-name">
                <h4>Toolbox</h4>
            </div>
            <div class="sa-addon-features">
                <div class="sa-curly"></div>
                <ul>
                    <li>Javascript fields validation.</li>
                    <li>You can set wich fields are/aren’t required.</li>
                    <li>Users can create terms in taxonomies.</li>
                    <li>You can let users choose more than one term.</li>
                    <li>You can limit the number of terms that a user can choose.</li>
                    <li>You can blacklist terms in taxonomies.</li>
                    <li>You can show term parents in different level (only for hierarchical taxonomies).</li>
                    <li>If you have more taxonomies created in posts (in addition to tags and categories) they will be available as fields to include in Social Articles form.</li>
                    <li>You can limit the number of characters in title and content.</li>
                    <li>You can active the upload image button in content.</li>
                    <li>You can set fields height.</li>
                    <li>You can choose the WP roles that can use Social Articles.</li>
                </ul>
            </div>
        </div>
        <div class="sa-addon cpt sa-coming-soon">
            <div class="sa-addon-button sa-comingsoon">
                Coming soon
            </div>
            <div class="sa-addon-image">
                <img src="<?php echo SA_BASE_URL. '/includes/admin/assets/images/cpt.jpg'?>">
            </div>
            <div class="sa-addon-name">
                <h4>CPT</h4>
            </div>

            <div class="sa-addon-features">
                <div class="sa-curly"></div>
                <ul>
                    <li>Choose your own custom post type.</li>
                </ul>
            </div>
        </div>
        <div class="sa-addon multiple sa-coming-soon">
            <div class="sa-addon-button sa-comingsoon">
                Coming soon
            </div>
            <div class="sa-addon-image">
                <img src="<?php echo SA_BASE_URL. '/includes/admin/assets/images/multiple.jpg'?>">
            </div>
            <div class="sa-addon-name">
                <h4>Multiple</h4>
            </div>

            <div class="sa-addon-features">
                <div class="sa-curly"></div>
                <ul>
                    <li>Create multiple forms with multiple CPT.</li>
                </ul>
            </div>
        </div>
    </div>



</div>
<?php $features = ob_get_contents();?>
<?php ob_end_clean();?>
<?php $socialArticles->postbox( 'social_articles_premium_options', __('Add-ons', 'social-articles'), $features);?>


    

</div>