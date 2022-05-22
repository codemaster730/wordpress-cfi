
<div class="meta-box-sortables ui-sortable">
<?php
$current_user = wp_get_current_user();


ob_start();?>
<div class="troubleshooting">

    <h4>Reset to default</h4>
    <p>This action will restore All social articles settings to default. Try this if you are having configuration problems.</p>
    <a type="button" id="sa-reset-options" class="sa-troubleshooting-button button button-primary">Reset Options</a>
    <h4>It still doesn't work?</h4>
    <p>Get in touch with us: <a href="mailto:dev@broobe.com">dev@broobe.com</a></p>
    <script>
        jQuery(function(){
            jQuery("#sa-reset-options").on("click", function(){
                jQuery.ajax({
                    type: "get",
                    url: "<?php echo admin_url( 'admin-ajax.php' );?>",
                    data: {action: "reset_sa_options"},
                    success:function (data) {
                        window.location.replace("/wp-admin/options-general.php?page=social-articles");
                    }
                })

            })
        })
    </script>
</div>
<?php $troubleshooting = ob_get_contents();?>
<?php ob_end_clean();?>
<?php $socialArticles->postbox( 'social_articles_troubleshooting', __('Troubleshooting', 'social-articles'), $troubleshooting);?>


    

</div>