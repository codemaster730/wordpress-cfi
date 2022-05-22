
<?php if(($this->article_status=="draft" || $this->article_status == "new-post") && !isDirectWorkflow()):?>
    <input type="submit" name="pending" value="<?php _e("Save and move it under review", "social-articles"); ?>" onclick="submitForm()" />
<?php endif?>
<?php if(($this->article_status=="draft" || $this->article_status == "new-post") && isDirectWorkflow()):?>
    <input type="submit" name="publish" value="<?php _e("Save and publish", "social-articles"); ?>" onclick="submitForm()" />
<?php endif?>