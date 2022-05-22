<?php
/**
 * BP Profile Search - form template 'bps-form-home'
 *
 * See http://dontdream.it/bp-profile-search/form-templates/ if you wish to modify this template or develop a new one.
 *
 * @package BuddyPress
 */

// @codingStandardsIgnoreFile
// Allow plugin text domain in theme.

// 1st section: set the default value of the template options
if ( !isset ( $options['theme'] ) ) $options['theme'] = 'none';
if ( !isset ( $options['collapsible'] ) ) $options['collapsible'] = 'No';

// 2nd section: display the form to select the template options
if ( is_admin () ) { return 'end_of_options 4.9'; }

// 3rd section: display the search form

// This $filters_escaped_escaped object and all his values are escaped by the
// bps_escaped_form_data function from the BP Profile Search plugin
$filters_escaped = bps_escaped_form_data($version = '4.9');
wp_register_script ('bps-template', plugins_url ('bp-profile-search/bps-template.js'), array (), BPS_VERSION);

$form_id    = 'bps_' . $filters_escaped->location . '_' . $filters_escaped->unique_id;
$form_class = 'bps_' . $filters_escaped->location . ' bps_' . $filters_escaped->unique_id;

$requested_url = bp_get_requested_url();
$form_action_suffix = '';
if( ( strpos( $requested_url, BP_MEMBERS_SLUG ) !== false ) && ( strpos( $requested_url, 'membersmap' ) !== false ) ) {
	$form_action_suffix = 'membersmap';
}
?>

<form action="<?php echo trailingslashit( $filters_escaped->action ) . $form_action_suffix; ?>" method="<?php echo $filters_escaped->method; ?>" id="<?php echo $form_id; ?>" class="<?php echo $form_class; ?> bps_form bps-form-home">

	<?php
	$fields_names = array_map(function( $field ) {
		return $field->html_name;
	}, $filters_escaped->fields);

	$fields_names = array_unique($fields_names);
	$fields = array_values( array_intersect_key( $filters_escaped->fields, $fields_names ) );
	?>

	<?php foreach ( $filters_escaped->fields as $filter_escaped_item ):

		$id = $filter_escaped_item->unique_id;
		$name  = sanitize_title( $filter_escaped_item->html_name );
		$value = $filter_escaped_item->value;
		$display = $filter_escaped_item->display;

		if ( $display == 'none' ) continue;

		if ( $display == 'hidden' ) { ?>
			<input type='hidden' name="<?php echo $name; ?>" value="<?php echo $value; ?>">
			<?php continue;
		} ?>

		<div id="<?php echo $id; ?>_wrap" class="editfield bps-editfield bps-<?php echo $display; ?>">

			<?php
			if ( !empty ( $filter_escaped_item->error_message ) ) { ?>
				<div class="bps-error"><?php echo $filter_escaped_item->error_message; ?></div>
				<?php
			} ?>

			<?php
			switch ( $display ) {
				case 'date-range':
					?>
					<div class="bps-label"><?php echo $filter_escaped_item->label; ?></div>
					<div class="row">
						<div class="col-5 pr-0">
							<input type="date" id="<?php echo $id; ?>" name="<?php echo $name.'[min]'; ?>" value="<?php echo $value['min']; ?>" />
						</div> <!-- .col -->
						<div class="col-2 d-flex align-items-center justify-content-center">
							<span class='bps-range-separator'><i class='fa fa-angle-double-right fa-lg'></i></span>
						</div> <!-- .col -->
						<div class="col-5 pl-0">
							<input type="date" name="<?php echo $name.'[max]'; ?>" value="<?php echo $value['max']; ?>" />
						</div> <!-- .col -->
					</div> <!-- .row -->
				<?php
				break;
				case 'range':
				case 'integer-range':
				case 'range-select':

					if ( 'datebox' == $filter_escaped_item->type ) {

						wp_enqueue_script( 'jquery-ui-slider' );
						$field_settings = BP_XProfile_Field_Type_Datebox::get_field_settings( $filter_escaped_item->id );

						if ( 'absolute' === $field_settings['range_type'] ) {
							$current_year = intval( date( 'Y' ) );
							$age_range_end = $field_settings['range_absolute_start'] < $current_year ? $current_year - $field_settings['range_absolute_start'] : 0;
							$age_range_start = $field_settings['range_absolute_end'] < $current_year ? $current_year - $field_settings['range_absolute_end'] : 0;
						}
						else {
							$age_range_end = $field_settings['range_relative_start'] < 0 ? abs( $field_settings['range_relative_start'] ) : 0;
							$age_range_start = $field_settings['range_relative_end'] < 0 ? abs( $field_settings['range_relative_end'] ) : 0;
						}

						$val_min = ! empty( $value['min'] ) ? $value['min'] : $age_range_start;
						$val_max = ! empty( $value['max'] ) ? $value['max'] : $age_range_end;

						if ( apply_filters( 'grimlock_buddypress_bp_profile_search_age_range_slider_enabled', false ) ) : ?>

							<div class="bps-label"><?php echo $filter_escaped_item->label; ?></div>
							<div id="<?php echo esc_attr( $id ); ?>">
								<div class="bps-range-slider"></div>
								<div class="bps-range-value">
									<span class="bps-range-value-min"><?php echo esc_html( $val_min ); ?></span>
									<span class='bps-range-separator'>&dash;</span>
									<span class="bps-range-value-max"><?php echo esc_html( $val_max ); ?></span>
								</div>
								<input type="hidden" name="<?php echo $name.'[min]'; ?>" value="<?php echo $val_min; ?>" />
								<input type="hidden" name="<?php echo $name.'[max]'; ?>" value="<?php echo $val_max; ?>" />
							</div> <!-- .row -->

							<script>
			                    jQuery( function ( $ ) {
			                        $( '#<?php echo esc_js( $id ); ?> .bps-range-slider' ).slider( {
			                            range: true,
			                            min: <?php echo esc_js( $age_range_start ); ?>,
			                            max: <?php echo esc_js( $age_range_end ); ?>,
			                            values: [ <?php echo esc_js( $val_min ) ?>, <?php echo esc_js( $val_max ) ?> ],
			                            slide: function( event, ui ) {
			                                $( '#<?php echo esc_js( $id ); ?> input[name="<?php echo $name.'[min]'; ?>"]' ).val( ui.values[0] );
			                                $( '#<?php echo esc_js( $id ); ?> input[name="<?php echo $name.'[max]'; ?>"]' ).val( ui.values[1] );
			                                $( '#<?php echo esc_js( $id ); ?> .bps-range-value-min' ).text( ui.values[0] );
			                                $( '#<?php echo esc_js( $id ); ?> .bps-range-value-max' ).text( ui.values[1] );
			                            }
			                        } );
			                    } );
							</script>

						<?php else :

							echo "<div class='bps-label'>";
							echo $filter_escaped_item->label;
							echo "</div>";
							echo "<div class='row'>";
							echo "<div class='col pr-0'><div class='bps-custom-select'><select name='" . $name . "[min]' id='$filter_escaped_item->code'>";
							echo "<option  value=''>" . esc_html__( 'from', 'grimlock-buddypress' ) . '</option><option disabled>──────────</option>';
							for ( $k = $age_range_start; $age_range_end > $k; $k++ ) {
								echo "<option " . selected( $value['min'], $k, false ) . " value='$k'>$k</option>";
							}
							echo '</select></div></div>';

							echo "<div class='col-auto d-flex align-items-center justify-content-center'><span class='bps-range-separator'><i class='fa fa-angle-double-right fa-lg'></i></span></div>";

							echo "<div class='col pl-0'><div class='bps-custom-select'><select name='" . $name . "[max]'>";
							echo "<option  value=''>" . esc_html__( 'to', 'grimlock-buddypress' ) . '</option><option disabled>──────────</option>';
							for ( $k = $age_range_start; $age_range_end > $k; $k++ ) {
								echo "<option " . selected( $value['max'], $k, false ) . " value='$k'>$k</option>";
							}
							echo '</select></div></div>';
							echo '</div>';

						endif;
					} else { ?>
						<div class="bps-label"><?php echo $filter_escaped_item->label; ?></div>
						<div class="row">
							<div class="col-5 pr-0">
								<input type="number" id="<?php echo $id; ?>" name="<?php echo $name.'[min]'; ?>" value="<?php echo $value['min']; ?>" />
							</div> <!-- .col -->
							<div class="col-2 d-flex align-items-center justify-content-center">
								<span class='bps-range-separator'><i class='fa fa-angle-double-right fa-lg'></i></span>
							</div> <!-- .col -->
							<div class="col-5 pl-0">
								<input type="number" name="<?php echo $name.'[max]'; ?>" value="<?php echo $value['max']; ?>" />
							</div> <!-- .col -->
						</div> <!-- .row -->
					<?php }
					break;

				case 'textbox': ?>
					<div class="bps-label"><?php echo $filter_escaped_item->label; ?></div>
					<input type="search" id="<?php echo $id; ?>" name="<?php echo $name; ?>" value="<?php echo $value; ?>" placeholder="<?php echo $filter_escaped_item->label; ?>">
					<?php
					break;

				case 'integer': ?>
					<div class="bps-label"><?php echo $filter_escaped_item->label; ?></div>
					<input type="number" id="<?php echo $id; ?>" name="<?php echo $name; ?>" value="<?php echo $value; ?>" placeholder="<?php echo $filter_escaped_item->label; ?>">
					<?php
					break;

				case 'date': ?>
					<div class="bps-label"><?php echo $filter_escaped_item->label; ?></div>
					<input type="date" id="<?php echo $id; ?>" name="<?php echo $name; ?>" value="<?php echo $value; ?>">
					<?php
					break;

				case 'distance':

					wp_enqueue_script ($f->script_handle);
					wp_enqueue_script ('bps-template');

					$of = __('of', 'bp-profile-search');
					$km = __('km', 'bp-profile-search');
					$miles = __('miles', 'bp-profile-search');
					$placeholder = __('Select a location', 'grimlock-buddypress');
					$icon_title = __('Get current location', 'grimlock-buddypress');
					?>

					<div class="row">
						<div class="col-12">
							<div class="bps-label"><?php echo $filter_escaped_item->label; ?></div>
						</div>
						<div class="col-location col-12 col-xl mb-2 mb-xl-0">
							<div class="pos-r h-100">
								<input type="search" id="<?php echo $id; ?>" name="<?php echo $name.'[location]'; ?>"  value="<?php echo $value['location']; ?>" placeholder="<?php echo $placeholder; ?>">
								<button type="button" id="<?php echo $id; ?>_icon" title="<?php echo $icon_title; ?>" class="btn-location" data-toggle="tooltip" data-placement="top">
									<span class="dashicons dashicons-location"></span>
								</button>
							</div>
						</div>
						<div class="col-distance col col-lg-auto pl-xl-0 pr-0">
							<input type="number" min="1" name="<?php echo $name.'[distance]'; ?>" placeholder="<?php if (empty ($value['distance'])): echo '1'; endif; ?>" value="<?php echo $value['distance']; ?>">
						</div>
						<div class="col-unit col-auto">
							<div class="bps-custom-select">
								<select name="<?php echo $name.'[units]'; ?>">
									<option value="km" <?php selected ($value['units'], "km"); ?>><?php echo $km; ?></option>
									<option value="miles" <?php selected ($value['units'], "miles"); ?>><?php echo $miles; ?></option>
								</select>
							</div>
						</div>
					</div>

					<input type="hidden" id="<?php echo $id; ?>_lat" name="<?php echo $name.'[lat]'; ?>" value="<?php echo $value['lat']; ?>">
					<input type="hidden" id="<?php echo $id; ?>_lng" name="<?php echo $name.'[lng]'; ?>" value="<?php echo $value['lng']; ?>">

					<script>
	                    jQuery(function ($) {
	                        bps_autocomplete('<?php echo $id; ?>', '<?php echo $id; ?>_lat', '<?php echo $id; ?>_lng');
	                        $('#<?php echo $id; ?>_icon').click(function () {
	                            bps_locate('<?php echo $id; ?>', '<?php echo $id; ?>_lat', '<?php echo $id; ?>_lng')
	                        });
	                    });
					</script>

					<?php
					break;

				case 'selectbox': ?>
					<label class="bps-label" for="<?php echo $id; ?>"><?php echo $filter_escaped_item->label; ?></label>
					<div class="bps-custom-select">
						<select id="<?php echo $id; ?>" name="<?php echo $name ?>">

							<?php $no_selection = apply_filters( 'bps_field_selectbox_no_selection', $filter_escaped_item->label, $filter_escaped_item );
							if ( is_string( $no_selection ) ) {
								echo "<option value=''>$no_selection</option><option disabled>──────────</option>";
							}

							foreach ( $filter_escaped_item->options as $key => $label ) {
								if ( ! empty( $key ) ) {
									echo "<option " . selected( $value, $key, false ) . " value='$key'>$label</option>";
								}
							} ?>
						</select>
					</div>
					<?php
					grimlock_bp_profile_search_enable_bpxcftr_select2( $filter_escaped_item->id, $name );
					break;

				case 'multiselectbox': ?>
					<div class="bps-label"><?php echo $filter_escaped_item->label; ?></div>
					<select id="<?php echo $id; ?>" name="<?php echo $name.'[]'; ?>" multiple="multiple" size="<?php echo $filter_escaped_item->multiselect_size; ?>">
						<?php foreach ($filter_escaped_item->options as $key => $label) { ?>
							<option <?php if (in_array ($key, $value)) echo 'selected="selected"'; ?> value="<?php echo $key; ?>"><?php echo $label; ?></option>
						<?php } ?>
					</select>
					<?php
					grimlock_bp_profile_search_enable_bpxcftr_select2( $filter_escaped_item->id, $name . '[]' );
					break;

				case 'radio':
					wp_enqueue_script ('bps-template'); ?>

					<div class="bps-label"><?php echo $filter_escaped_item->label; ?></div>
					<div class="radio">
						<?php foreach ($filter_escaped_item->options as $key => $label) { ?>
							<label class="custom-control custom-radio">
								<input type="radio" class="custom-control-input" name="<?php echo $name; ?>" value="<?php echo $key; ?>" <?php if ($key == $value) echo 'checked="checked"'; ?> />
								<span class='custom-control-indicator'></span>
								<span class='custom-control-description'><?php echo $label; ?></span>
							</label>
						<?php } ?>

						<a href="javascript:bps_clear_radio('<?php echo $id; ?>_wrap')"><?php echo __('Clear', 'buddypress'); ?></a>
					</div><!-- .radio -->

					<?php
					break;

				case 'checkbox': ?>
					<div class="bps-label"><?php echo $filter_escaped_item->label; ?></div>
					<div class="checkbox">
						<?php foreach ($filter_escaped_item->options as $key => $label) { ?>
							<label class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" name="<?php echo $name.'[]'; ?>" value="<?php echo $key; ?>" <?php if (in_array ($key, $value)) echo 'checked="checked"'; ?> />
								<span class='custom-control-indicator'></span>
								<span class='custom-control-description'><?php echo $label; ?></span>
							</label>
						<?php } ?>
					</div> <!-- .checkbox -->
					<?php
					break;

				default:

					$field_template = 'members/bps-'. $display. '-form-field.php';
					$located = bp_locate_template ($field_template);
					if ($located) {
						include $located;
					}
					else { ?>
						<p class="bps-error"><?php echo "BP Profile Search: unknown display <em>$display</em> for field <em>$filter_escaped_item->name</em>."; ?></p>
						<?php
					}
					break;
			}

			if ( ! empty( $filter_escaped_item->description ) && '-' !== $filter_escaped_item->description ) { ?>
				<div class="description bps-description mt-1"><?php echo $filter_escaped_item->description; ?></div>
			<?php } ?>

		</div> <!-- .editfield -->

	<?php
	endforeach ?>

	<div class="submit bps-submit">
		<input type="submit" value="<?php echo esc_html( apply_filters( 'grimlock_buddypress_bps_form_home_submit_value', __( 'Submit', 'grimlock-buddypress' ) ) ); ?>">
	</div> <!-- .bps-submit -->

</form>

<?php return 'end_of_template 4.9'; ?>
