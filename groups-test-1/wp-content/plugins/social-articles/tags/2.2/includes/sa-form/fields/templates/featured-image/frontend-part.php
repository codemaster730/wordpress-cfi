<?php

$wp_slug = $this->slug;
$sa_content_config = $this->get_config();
?>
<div class="sa-field-front sa-field-content" style="width: calc(<?php echo $this->get_width();?> - 30px)">
    <span class="titlelabel"><?php echo $sa_content_config['field_config']['label']; ?></span>

    <div id="<?php echo $wp_slug.'-container';?>" class="sa-upload-image-container">

        <input type="hidden" class="text" name="<?php echo $wp_slug;?>" id="<?php echo $wp_slug;?>" value="<?php echo $this->get_data($article_id, $_POST); ?>" />
        <div id="<?php echo $wp_slug.'-image';?>" class="form-image-container">
            <div id="<?php echo $wp_slug;?>-image-button" style="text-align: center; background:#FFF; margin-top: 100px;"></div>
            <a href="#" class="delete-logo" style="display: none" id="delete-logo-<?php echo $wp_slug;?>"><?php _e("Delete", "social-articles"); ?></a>
            <div id="<?php echo $wp_slug;?>-image-container" class="sa-image-container">
                <?php
                $fi = $this->get_data($article_id, $_POST);
                if(!empty($fi)){
                    $image_attributes = wp_get_attachment_image_src( $this->get_data($article_id, $_POST), 'large');?>
                    <img style="max-width: 100%"  src="<?php echo $image_attributes[0]; ?>" />
                    <script>
                        jQuery(function(){
                            jQuery("#delete-logo-<?php echo $wp_slug;?>").show();
                            jQuery("#<?php echo $wp_slug;?>-image-button").hide();
                            jQuery("#<?php echo $wp_slug.'-image';?>").addClass('already-uploaded');
                        })
                    </script>
                    <?php
                }
                ?>
            </div>

        </div>
        <div class="logo-image-error-container" style="display: none"></div>
        <div class="logo-image-attaching" style="display: none">
            <p><?php _e('Generating preview','social-articles');?></p>
            <img width="60px"  src="<?php echo SA_BASE_URL . '/assets/images/loading.svg' ; ?>"/>
        </div>

    </div>

</div>

<script>
    jQuery(function(){

        jQuery("#delete-logo-<?php echo $wp_slug;?>").on('click', function(event){
            event.preventDefault();
            jQuery("#<?php echo $wp_slug;?>-image-button").show();
            jQuery("#<?php echo $wp_slug;?>-image-container").html('');
            jQuery('#<?php echo $wp_slug;?>').val("");
            jQuery("#<?php echo $wp_slug.'-image';?>").removeClass('already-uploaded');
            jQuery(this).hide();
        });

        var uploader = new qq.FileUploader({
            multiple: false,
            element: jQuery('#<?php echo $wp_slug;?>-image-button')[0],
            action:  global_data.ajax_url+"?action=image_uploader",
            uploadButtonText: '<?php _e("Choose image", "social-articles"); ?>',
            orText: '<?php _e("or", "social-articles"); ?>',
            dragText: '<?php _e("Drag your file here to upload", "social-articles"); ?>',
            dropText: '<?php _e("Drop your file", "social-articles"); ?>',
            maxSizeText: '<?php printf( __( 'Maximum upload file size: %s.', 'social-articles' ), ini_get('upload_max_filesize') ); ?>',

            onComplete: function(id, fileName, response){
                jQuery("#"+id).remove();
                if(response.success){

                    jQuery('#<?php echo $wp_slug.'-container';?> .logo-image-attaching').show();
                    jQuery.ajax({
                        type: 'post',
                        url: global_data.ajax_url,
                        data: { action: "attach_image", fileName:fileName },
                        success:
                            function(data) {
                                image = JSON.parse(data);
                                jQuery("#<?php echo $wp_slug;?>-image-button").hide();
                                jQuery('#<?php echo $wp_slug;?>').val(image.attachment_id);
                                jQuery("#<?php echo $wp_slug.'-image';?>").addClass('already-uploaded');
                                jQuery('#<?php echo $wp_slug;?>-image-container').html('<img style="max-width: 100%" src="'+image.preview_image+'" />');
                                jQuery("#delete-logo-<?php echo $wp_slug;?>").show();

                                jQuery('#<?php echo $wp_slug.'-container';?> .logo-image-attaching').hide();

                            }
                    });
                }
            },
            onSubmit: function(id, fileName){
                jQuery('#<?php echo $wp_slug.'-container';?> .logo-image-error-container').html('').hide();
            },
            showMessage: function(message){
                jQuery('#<?php echo $wp_slug.'-container';?> .logo-image-error-container').html(message).show();
            }
            
        });


    })
</script>

