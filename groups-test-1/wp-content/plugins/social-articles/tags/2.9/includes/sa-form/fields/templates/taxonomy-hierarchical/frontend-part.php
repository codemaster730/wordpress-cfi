<?php

$terms = get_terms( $this->slug, array(
    'hide_empty' => false,
) );
$sa_taxo_h_config = $this->get_config();
$selected_terms = $this->get_data($article_id, $_POST);
$multiple = $sa_taxo_h_config['field_config']['multiple'] ? 'multiple="multiple"':'';

?>

<div class="sa-field-front sa-field-content" style="width: calc(<?php echo $this->get_width();?> - 30px)">
    <span class="titlelabel"><?php echo $sa_taxo_h_config['field_config']['label']; ?></span>

    <script type="text/javascript">
        jQuery(function(){
            jQuery("#<?php echo $this->slug;?>").select2(
                {
                    placeholder:"<?php echo $sa_taxo_h_config['field_config']['placeholder']; ?>",
                    allowClear: <?php echo $multiple ?  'false' :  'true' ?>
                    
                }
            );
        })
    </script>
    <div style="float: left; width: 100%">
        <select name="<?php echo $this->slug;?>[]" id="<?php echo $this->slug;?>" <?php echo $multiple;?> style="width: 100%;" >
            <option value=""></option>
            <?php foreach ($terms as $term):?>
                <option <?php echo in_array($term->slug, $selected_terms) ? 'selected' : '';?> value="<?php echo $term->slug;?>"><?php echo $term->name;?></option>
            <?php endforeach;?>
        </select>
    </div>
</div>