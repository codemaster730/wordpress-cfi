<?php
global $socialArticles;
?>

<div id="sa-form-builder">

    <div class="column left first sa-field-factory">
        <h4><?php _e('Available Fields', 'social-articles')?></h4>
        <div class="drag-field-message">
            <p><?php _e('Drag and drop the fields you need from this column to the Form Layout column', 'social-articles');?></p>
        </div>
        <ul class="sortable-list ">
            <?php foreach ($socialArticles->form_settings->available_fields as $key => $field): ?>
                <li id="<?php echo $key; ?>" class="sortable-item <?php if($field->config['field_config']['resizable']) echo 'resizable '?> <?php echo $field->config['field_config']['size'];?>">
                    <div class="sa-field-content"><?php echo $field->name?></div>
                    <input type="hidden" name="<?php echo $key; ?>_config" id="<?php echo $key; ?>_config" value="<?php echo htmlspecialchars(json_encode($field->config['field_config']), ENT_QUOTES)?>">

                    <div class="field-config-form" style="display: none" config-field="<?php echo $key; ?>_config" field="<?php echo $key; ?>">
                        <?php $field->show_backend_field();?>
                    </div>
                </li>
            <?php endforeach;?>
        </ul>
    </div>

    <div class="column left sa-form">
        <h4><?php _e('Form Layout', 'social-articles')?></h4>
        <ul class="sortable-list sorted-fields">
            <?php
                $selected_items = array();
                foreach ($socialArticles->form_settings->selected_fields as $key => $field):
                    $selected_items[] = $key;
                ?>
                <li id="<?php echo $key; ?>" class="sortable-item <?php if($field->config['field_config']['resizable']) echo 'resizable '?> <?php echo $field->config['field_config']['size'];?>">
                    <div class="sa-field-content"><?php echo $field->name?></div>
                    <input type="hidden" name="<?php echo $key; ?>_config" id="<?php echo $key; ?>_config" value="<?php echo htmlspecialchars(json_encode($field->config['field_config']), ENT_QUOTES)?>">

                    <div class="field-config-form" style="display: none" config-field="<?php echo $key; ?>_config" field="<?php echo $key; ?>">
                        <?php $field->show_backend_field();?>
                    </div>
                </li>
            <?php endforeach;?>
        </ul>
    </div>

    <div class="column left sa-field-config">
        <h4><?php _e('Field Configuration', 'social-articles')?></h4>
        <div class="edit-field-message">
            <p><?php printf(__('Click on %s to edit the field configuration', 'social-articles'), '<span class="dashicons dashicons-edit"></span>');?></p>
        </div>
        <div id="config-form-container">
        </div>
    </div>

    <div class="clearer">&nbsp;</div>
    <input type="hidden" name="selected_fields" id="selected_fields" value="<?php echo implode(',',$selected_items);?>">
    <?php
    $save_button = '<div class="submitbutton"><input type="submit" class="button-primary" name="submit" value="'.__('Update Social Articles Settings','social-articles'). '" /></div><br class="clear"/>';
    $socialArticles->postbox( 'social_articles_view_options', '',$save_button);
    ?>
</div>

<div id="resizable-controls" class="resizable-controls" style="display:none"><span class="sa-field-size-indicator">1/2</span><span class="full">+</span><span class="half">-</span></div>
<div id="field-controls" class="field-controls" style="display:none"><span class="sa-edit-field sa-icon-container"><span class="dashicons dashicons-edit"></span></span><span class="sa-delete-field sa-icon-container"><span class="dashicons dashicons-trash"></span></span></div>

<script type="text/javascript">

    jQuery(document).ready(function(){
      init_form_builder();
    });



</script>