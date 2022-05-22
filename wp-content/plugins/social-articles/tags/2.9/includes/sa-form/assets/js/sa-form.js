
/*Fields backend functions*/

function save_sa_taxonomy_regular_config(slug){
    var sa_config_input = jQuery('#'+slug+'_config');
    var sa_config = JSON.parse(sa_config_input.val());
    sa_config.placeholder=jQuery('#sa-form-'+slug+'-placeholder').val();
    sa_config.label=jQuery('#sa-form-'+slug+'-label').val();
    sa_config.multiple= jQuery('#sa-form-'+slug+'-type').val() === 'true';
    update_field_config_data(sa_config_input, sa_config);
}

function save_sa_taxonomy_hierarchical_config(slug){
    var sa_config_input = jQuery('#'+slug+'_config');
    var sa_config = JSON.parse(sa_config_input.val());
    sa_config.placeholder=jQuery('#sa-form-'+slug+'-placeholder').val();
    sa_config.label=jQuery('#sa-form-'+slug+'-label').val();
    sa_config.multiple= jQuery('#sa-form-'+slug+'-type').val() === 'true';
    update_field_config_data(sa_config_input, sa_config);
}

function save_sa_title_config(){
    var sa_config_input = jQuery('#sa_title_config');
    var sa_config = JSON.parse(sa_config_input.val());
    sa_config.empty_message=jQuery('#sa-form-title-empty-message').val();
    sa_config.placeholder=jQuery('#sa-form-title-placeholder').val();
    sa_config.label=jQuery('#sa-form-title-label').val();
    update_field_config_data(sa_config_input, sa_config);
}

function save_sa_content_config(){
    var sa_config_input = jQuery('#sa_content_config');
    var sa_config = JSON.parse(sa_config_input.val());
    sa_config.label=jQuery('#sa-form-content-label').val();
    sa_config.advance = jQuery('#sa-form-content-advance').prop('checked');
    update_field_config_data(sa_config_input, sa_config);
}

function save_sa_featured_image_config(){
    var sa_config_input = jQuery('#sa_featured_image_config');
    var sa_config = JSON.parse(sa_config_input.val());
    sa_config.label=jQuery('#sa-form-featured_image-label').val();
    update_field_config_data(sa_config_input, sa_config);
}


/*Form Builder functions*/

function restore_config_form(){
    switch_field_to_original(jQuery('.sortable-item'));
    var displayed_form = jQuery('#config-form-container .field-config-form').detach();
    if(jQuery.trim(displayed_form)){
        displayed_form = jQuery(displayed_form);
        jQuery('#' + displayed_form.attr('field')).append(displayed_form.hide());
    }

    jQuery('.edit-field-message').show();
}

function restore_original_config_form(){
    switch_field_to_original(jQuery('.sortable-item'));
    var displayed_form = jQuery('#config-form-container').html();
    jQuery('#config-form-container').html('');
    if(jQuery.trim(displayed_form)){
        displayed_form = jQuery(displayed_form);
        jQuery('#' + displayed_form.attr('field')).append(displayed_form.hide());
    }

    jQuery('.edit-field-message').show();
}

function update_selected_fields(){
    var items = [];
    items_objects = jQuery('.sorted-fields .sortable-item');
    jQuery.each(items_objects, function(index, value){
        items.push(value.id);
    });
    jQuery('#selected_fields').val(items);
}

function update_config_size(button, size){
    var config_field = jQuery('#'+jQuery(button).closest('li').attr('id')+'_config');
    config = config_field.val();
    config = JSON.parse(config);
    config.size = size;
    config_field.val(JSON.stringify(config));
}

function update_field_config_data(sa_config_input, sa_config){
    var sa_config_old = sa_config_input.val(); //to detect changes
    restore_config_form();
    sa_config = JSON.stringify(sa_config);
    if(sa_config != sa_config_old){
        switch_field_to_modified(sa_config_input.closest('li'));
        sa_config_input.val(sa_config);
    }
}

function switch_field_to_original(item) {
    item.removeClass('editable-field').addClass('original-field');
}

function switch_field_to_editable(item) {
    item.removeClass('original-field').addClass('editable-field');
}

function switch_field_to_modified(item) {
    item.removeClass('original-field').removeClass('editable-field').addClass('modified-field');
    jQuery('#changes-detected-message').show();
}


function init_form_builder(){
    jQuery('#sa-form-builder .sortable-list').sortable({
        connectWith: '#sa-form-builder .sortable-list',
        placeholder: 'placeholder',
        start:function( event, ui){
            if ( jQuery(ui.item).hasClass('resizable') ) {
                jQuery('.placeholder').css('width', '47.5%')
            }else{
                jQuery('.placeholder').css('width', '97%')
            }
        },
        stop:function( event, ui){
            update_selected_fields();
            jQuery('#changes-detected-message').show();
        }
    });

    controls  = jQuery('#resizable-controls').clone();
    jQuery(controls).attr('id', '');

    fieldControls  = jQuery('#field-controls').clone();
    jQuery(fieldControls).attr('id', '');

    jQuery('.resizable').prepend(controls);
    jQuery('.sortable-item').append(fieldControls);

    jQuery('.sa-fullwidth .sa-field-size-indicator').html('1/1');
    jQuery('.sa-halfwidth .sa-field-size-indicator').html('1/2');

    jQuery('.resizable-controls .full').on('click', function(){
        var li = jQuery(this).closest('li');
        li.removeClass('sa-halfwidth').addClass('sa-fullwidth');
        jQuery(this).siblings('.sa-field-size-indicator').html('1/1');
        update_config_size(this, 'sa-fullwidth');
        switch_field_to_modified(li);
    });

    jQuery('.resizable-controls .half').on('click', function(){
        var li = jQuery(this).closest('li');
        li.removeClass('sa-fullwidth').addClass('sa-halfwidth');
        jQuery(this).siblings('.sa-field-size-indicator').html('1/2');
        update_config_size(this, 'sa-halfwidth');
        switch_field_to_modified(li);
    });

    jQuery('.field-controls .sa-delete-field').on('click', function(){
        jQuery('.sa-field-factory .sortable-list').append(jQuery(this).closest('li').detach());
        update_selected_fields();
    });

    jQuery('.field-controls .sa-edit-field').on('click', function(){
        restore_config_form();
        var li = jQuery(this).closest('li');
        switch_field_to_editable(li);
        jQuery('#config-form-container').html(li.find('.field-config-form').detach().show());
        jQuery('.edit-field-message').hide();
    })
}


function sa_advance(slug) {
    if (jQuery('#sa-form-' + slug + '-advance').prop('checked')) {
        jQuery('#sa-form-' + slug + '-advance').val('true');
    } else {
        jQuery('#sa-form-' + slug + '-advance').val('false');
    }
}