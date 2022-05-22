<?php
?>
<div class="field-config-form-container">
    <h4><?php printf(__('Field: %s ', 'social-articles'),$this->name)?></h4>
    <div class="fields-wrap">
        <p>
            <label for="sa-form-<?php echo $this->slug;?>-label"><?php _e('Label', 'social-articles'); ?></label>
            <input type="text" class="sa-form-<?php echo $this->slug;?>-label" id="sa-form-<?php echo $this->slug;?>-label" value="<?php echo $this->config['field_config']['label']; ?>">
        </p>
        <p>
            <label for="sa-form-<?php echo $this->slug;?>-placeholder"><?php _e('Placeholder', 'social-articles'); ?></label>
            <input type="text" class="sa-form-<?php echo $this->slug;?>-placeholder"  id="sa-form-<?php echo $this->slug;?>-placeholder"
                   value="<?php echo $this->config['field_config']['placeholder']; ?>">
        </p>
        <p>
            <label for="sa-form-<?php echo $this->slug;?>-type"><?php _e('Type', 'social-articles'); ?></label>
            <select class="sa-form-<?php echo $this->slug;?>-type" id="sa-form-<?php echo $this->slug;?>-type">
                <option value="true" <?php if($this->config['field_config']['multiple']) echo 'selected';?>><?php _e('Multiple','social-article');?></option>
                <option value="false" <?php if(!$this->config['field_config']['multiple']) echo 'selected';?>><?php _e('Single','social-article');?></option>
            </select>
        </p>

        <p class="field-config-actions">
            <a href="javascript:save_sa_taxonomy_hierarchical_config('<?php echo $this->slug;?>');" class="ok-button"><span class="dashicons dashicons-yes"></span></a>
            <a href="javascript:restore_original_config_form();" class="cancel-button"><span class="dashicons dashicons-no-alt"></span></a>
        </p>

    </div>
</div>
