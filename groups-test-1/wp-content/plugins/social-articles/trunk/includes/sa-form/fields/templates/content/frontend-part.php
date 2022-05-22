<?php
$sa_content_config = $this->get_config();
?>
<div class="sa-field-front sa-field-content" style="width: calc(<?php echo $this->get_width();?> - 30px)">

    <div class="editor-container">
        <span class="titlelabel"><?php echo $sa_content_config['field_config']['label']; ?></span>
        <?php
        $editor_id = $this->slug;

        $settings = array(
            'quicktags'     => false,
            'textarea_rows' => 15,
            'media_buttons' => false,
            'teeny' => true,
            'theme' => "simple"

        );

        if($sa_content_config['field_config']['advance'] == 'true'){
            $tinymceButtons = array('formatselect','|','bold','italic','underline','strikethrough','blockquote','|', 'link','|', 'alignleft','aligncenter','alignright','alignjustify','|','numlist','bullist','outdent', 'indent', '|', 'undo', 'redo', 'removeformat', '|', 'fullscreen');
            $settings['tinymce'] = array(
                'toolbar1' => implode(' ',$tinymceButtons),
                'toolbar2'=>false
            );
        }

        wp_editor( $this->get_data($article_id, $_POST), $editor_id, $settings);
        ?>
    </div>
</div>