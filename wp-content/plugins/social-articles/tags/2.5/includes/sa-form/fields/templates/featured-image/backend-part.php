<?php

?>

<div class="field-config-form-container">
    <h4><?php printf(__('Field: %s ', 'social-articles'),$this->name)?></h4>
    <div class="fields-wrap">
        <p>
            <label><?php _e('Label', 'social-articles'); ?></label>
            <input type="text" class="sa-form-featured_image-label" id="sa-form-featured_image-label" value="<?php echo $this->config['field_config']['label']; ?>">
        </p>
        <p style="border:none">
            <?php  printf( __( 'Maximum upload file size: %s (PHP limit)', 'social-articles' ), ini_get('upload_max_filesize') ); ?>
        </p>

        <p class="field-config-actions">
            <a href="javascript:save_sa_featured_image_config();" class="ok-button"><span class="dashicons dashicons-yes"></span></a>
            <a href="javascript:restore_original_config_form();" class="cancel-button"><span class="dashicons dashicons-no-alt"></span></a>
        </p>

    </div>
</div>
