<?php
?>
<div class="field-config-form-container">
    <h4><?php printf(__('Field: %s ', 'social-articles'),$this->name)?></h4>
    <div class="fields-wrap">
        <p>
            <label><?php _e('Label', 'social-articles'); ?></label>
            <input type="text" class="sa-form-content-label" id="sa-form-content-label" value="<?php echo $this->config['field_config']['label']; ?>">
        </p>

        <div class="sa-bk-field-container">
            <label><?php _e('Advance options', 'social-articles'); ?></label>
            <div class="onoffswitch">
                <input type="checkbox" class="onoffswitch-checkbox sa-form-content-advance" name="sa-form-content-advance" id="sa-form-content-advance" <?php if($this->config['field_config']['advance']){echo 'checked';}; ?> onchange="sa_advance('content');">
                <label class="onoffswitch-label" for="sa-form-content-advance" >
                    <span class="onoffswitch-inner" data-no="<?php _e('No','social-article');?>" data-yes="<?php _e('Yes','social-article');?>"></span>
                    <span class="onoffswitch-switch"></span>
                </label>
            </div>
        </div>

        <p class="field-config-actions">
            <a href="javascript:save_sa_content_config();" class="ok-button"><span class="dashicons dashicons-yes"></span></a>
            <a href="javascript:restore_original_config_form();" class="cancel-button"><span class="dashicons dashicons-no-alt"></span></a>
        </p>

    </div>
</div>
