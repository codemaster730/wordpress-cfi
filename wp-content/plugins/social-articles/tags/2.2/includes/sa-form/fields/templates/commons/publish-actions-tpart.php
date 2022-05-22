
<?php if(($this->article_status=="draft" || $this->article_status == "new-post") && !isDirectWorkflow()):?>
    <input type="checkbox" name="publish-status" id="publish-status" value="pending"/>
    <label for="publish-status"><span></span><?php _e("Save and move it under review", "social-articles"); ?></label>
<?php endif?>
<?php if(($this->article_status=="draft" || $this->article_status == "new-post") && isDirectWorkflow()):?>
    <input type="checkbox" name="publish-status" id="publish-status" value="publish" />
    <label for="publish-status"><span></span><?php _e("Save and publish", "social-articles"); ?></label>
<?php endif?>