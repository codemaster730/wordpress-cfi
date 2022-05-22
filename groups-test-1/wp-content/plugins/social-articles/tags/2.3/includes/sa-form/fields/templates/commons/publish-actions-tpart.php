
<?php if(($this->article_status=="draft" || $this->article_status == "new-post") && !isDirectWorkflow()):?>
    <input type="submit" name="pending" value="<?php _e("Save and move it under review", "social-articles"); ?>" onclick="jQuery('#post-maker-container').hide(); jQuery('.saving-message').show();" />
<?php endif?>
<?php if(($this->article_status=="draft" || $this->article_status == "new-post") && isDirectWorkflow()):?>
    <input type="submit" name="publish" value="<?php _e("Save and publish", "social-articles"); ?>" onclick="jQuery('#post-maker-container').hide(); jQuery('.saving-message').show();" />
<?php endif?>