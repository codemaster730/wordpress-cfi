<?php
$sa_title_config = $this->get_config();
?>
<div class="sa-field-content" style="width: calc(<?php echo $this->get_width();?> - 30px)">
    <span class="titlelabel"><?php echo $sa_title_config['field_config']['label']; ?></span>
    <input type="text" id="<?php echo $this->slug ;?>" name="<?php echo $this->slug ;?>" class="sa-title-input" autofocus placeholder="<?php echo $sa_title_config['field_config']['placeholder']; ?>" value="<?php echo $this->get_data($article_id, $_POST); ?>"/>
</div>

