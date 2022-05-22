<?php

/**
 * Enable select2 for the specified field if the select2 option from BP Xprofile Custom Field Types is enabled for the field
 *
 * @param int $field_id The xprofile field id
 * @param string $field_name The [name] attribute of the select tag
 */
function grimlock_bp_profile_search_enable_bpxcftr_select2( $field_id, $field_name ) {
	if ( function_exists( 'bpxcftr_is_selectable_field' ) ) {
		// Check if select2 is enabled for the field
		$use_select2 = bp_xprofile_get_meta( $field_id, 'field', 'do_select2' ) === 'on';

		if ( $use_select2 ) {
			// Enqueue select2
			add_filter( 'bpxcftr_load_front_assets', '__return_true' );
			$bpxcftr_assets_loader = new BPXProfileCFTR\Bootstrap\Assets_Loader();
			$bpxcftr_assets_loader->register_front_assets();
			wp_enqueue_style( 'select2' );
			wp_enqueue_script( 'select2' );

			// Enable select 2 for field ?>
			<script>
				jQuery( function ( $ ) {
					$( 'select[name="<?php echo $field_name; ?>"]' ).select2();
				} );
			</script>
			<?php
		}
	}
}
