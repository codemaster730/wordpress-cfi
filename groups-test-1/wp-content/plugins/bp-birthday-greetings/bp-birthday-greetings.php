<?php
defined( 'ABSPATH' ) || exit;

function  bp_birthday_greetings_settings() {
    add_settings_section(
        'ps_birthday_section',
 
        __( 'BP Birthday Greetings Settings',  'bp-birthday-greetings' ),
 
        'bp_birthday_greetings_page_callback_section',
 
        'buddypress'
    );
 
    add_settings_field(
        'bp-dob',
 
        __( 'Select DOB Field', 'bp-birthday-greetings' ),
 
        'bp_birthday_greetings_field_callback',
 
        'buddypress',
 
        'ps_birthday_section'
    );
    add_settings_field(
        'bp-dp-width',
 
        __( 'Profile Picture Width', 'bp-birthday-greetings' ),
 
        'bp_birthday_dpw_field_callback',
 
        'buddypress',
 
        'ps_birthday_section'
    );
    add_settings_field(
        'bp-dp-height',
 
        __( 'Profile Picture Height', 'bp-birthday-greetings' ),
 
        'bp_birthday_dph_field_callback',
 
        'buddypress',
 
        'ps_birthday_section'
    );
    add_settings_field(
        'bp-dp-type',
 
        __( 'Profile Picture Type', 'bp-birthday-greetings' ),
 
        'bp_birthday_dpt_field_callback',
 
        'buddypress',
 
        'ps_birthday_section'
    );
    register_setting(
        'buddypress',
        'bp-dob',
        'string'
    );
    register_setting(
        'buddypress',
        'bp-dp-width',
        'string'
    );
    register_setting(
        'buddypress',
        'bp-dp-height',
        'string'
    );
    register_setting(
        'buddypress',
        'bp-dp-type',
        'string'
    );
 
}
 

add_action( 'bp_register_admin_settings', 'bp_birthday_greetings_settings',9999 );
 

function bp_birthday_greetings_page_callback_section() {
    ?>
    <p class="description"><?php _e( 'Select DOB Field for which greetings will be sent.', 'bp-birthday-greetings' );?></p>
    <?php
}
 

function bp_birthday_greetings_field_callback() {
    $bp_birthday_option_value = bp_get_option( 'bp-dob' );
    ?>
    <select name="bp-dob">
    	<option>--SELECT FIELD--</option>
    	<?php 
		if( bp_has_profile() ) : 
			while ( bp_profile_groups() ) : bp_the_profile_group();
					while ( bp_profile_fields() ) : bp_the_profile_field(); 
						?>
						<option value="<?php bp_the_profile_field_id(); ?>" <?php if($bp_birthday_option_value==bp_get_the_profile_field_id()):?> selected <?php endif;?>> <?php bp_the_profile_field_name(); ?>
						</option>
					<?php 
					endwhile;
			endwhile;
		endif;
    	?>
    </select>
    <?php
}

function bp_birthday_dpw_field_callback() {
    $bp_birthday_dpw_value = bp_get_option( 'bp-dp-width' );
    $bp_birthday_dpw_value = (empty($bp_birthday_dpw_value)) ? 32 : $bp_birthday_dpw_value;
    ?>
    <input type="number" name="bp-dp-width" value="<?php echo $bp_birthday_dpw_value;?>" min="0">
    <?php
}

function bp_birthday_dph_field_callback() {
    $bp_birthday_dph_value = bp_get_option( 'bp-dp-height' );
    $bp_birthday_dph_value = (empty($bp_birthday_dph_value)) ? 32 : $bp_birthday_dph_value;
    ?>
    <input type="number" name="bp-dp-height" value="<?php echo $bp_birthday_dph_value;?>" min="0">
    <?php
}

function bp_birthday_dpt_field_callback() {
    $bp_birthday_dpt_value = bp_get_option( 'bp-dp-type' );
    $bp_birthday_dpt_value = (empty($bp_birthday_dpt_value)) ? 'thumb' : $bp_birthday_dpt_value;
    ?>
    <select name="bp-dp-type">
        <option value="thumb" <?php if($bp_birthday_dpt_value == 'thumb'):?> selected <?php endif;?>>Thumbnail</option>
        <option value="full" <?php if($bp_birthday_dpt_value == 'full'):?> selected <?php endif;?>>Full Width</option>
    </select>
    <p class="note"><?php _e('For more information please check our FAQ', 'bp-birthday-greetings');?> <a target="_blank" href="https://wordpress.org/plugins/bp-birthday-greetings/#faq">here</a>.</p>
    <?php
}

