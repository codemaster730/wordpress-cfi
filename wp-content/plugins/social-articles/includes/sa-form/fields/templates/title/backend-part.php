<?php

?>

<div class="field-config-form-container">
    <h4><?php printf(__('Field: %s ', 'social-articles'),$this->name)?></h4>
    <div class="fields-wrap">
        <p>
            <label><?php _e('Label', 'social-articles'); ?></label>
            <input type="text" class="sa-form-title-label" id="sa-form-title-label" value="<?php echo $this->config['field_config']['label']; ?>">
        </p>
        <p>
            <label><?php _e('Placeholder', 'social-articles'); ?></label>
            <input type="text" class="sa-form-title-placeholder"  id="sa-form-title-placeholder"
                   value="<?php echo $this->config['field_config']['placeholder']; ?>">
        </p>
        <!--
        <p>
            <label><?php _e('Required', 'social-articles'); ?></label>
            <input type="checkbox" checked disabled>
            <i class="fa fa-info-circle" aria-hidden="true" title="<?php _e('Upgrade to premium to change this setting', 'social-articles'); ?>"></i>
        </p>-->

        <h5><?php _e('Messages', 'social-articles'); ?></h5>
        <p>
            <label><?php _e('Empty', 'social-articles'); ?></label>
            <input type="text" class="sa-form-title-empty-message" id="sa-form-title-empty-message" value="<?php echo $this->config['field_config']['empty_message']; ?>">
        </p>

        <p class="field-config-actions">
            <a href="javascript:save_sa_title_config();" class="ok-button"><span class="dashicons dashicons-yes"></span></a>
            <a href="javascript:restore_original_config_form();" class="cancel-button"><span class="dashicons dashicons-no-alt"></span></a>
        </p>

    </div>
</div>
