<?php

/**
 * Elements of form UI and others
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_HTML_Elements {

	// Form Elements------------------------------------------------------------------------------------------/

	/**
	 * Add Default Fields
	 *
	 * @param array $input_array
	 * @param array $custom_defaults
	 *
	 * @return array
	 */
	public static function add_defaults( array $input_array, array $custom_defaults=array() ) {

		$defaults = array(
			'id'                => '',
			'name'              => 'text',
			'value'             => '',
			'label'             => '',
			'title'             => '',
			'class'             => '',
			'main_label_class'  => '',
			'label_class'       => '',
			'input_class'       => '',
			'input_group_class' => '',
			'radio_class'       => '',
			'action_class'      => '',
			'container_class'   => '',
			'desc'              => '',
			'info'              => '',
			'placeholder'       => '',
			'readonly'          => false,  // will not be submitted
			'required'          => '',
			'autocomplete'      => false,
			'data'              => false,
			'disabled'          => false,
			'size'              => 3,
			'max'               => 50,
			'options'           => array(),
			'label_wrapper'     => '',
			'input_wrapper'     => '',
			'icon_color'        => '',
			'return_html'       => false,
			'unique'            => true,
			'text_class'        => '',
			'icon'              => '',
			'list'              => array(),
			'btn_text'          => '',
			'btn_url'           => '',
			'more_info_text'    => '',
			'more_info_url'     => '',
			'tooltip_title'     => '',
			'tooltip_body'      => '',
			'is_pro'            => ''
		);
		$defaults = array_merge( $defaults, $custom_defaults );
		return array_merge( $defaults, $input_array );
	}

	/**
	 * Renders an HTML Text field
	 *
	 * @param array $args Arguments for the text field
	 * @param bool $return_html
	 * @return false|string
	 */
	public static function text( $args = array(), $return_html=false ) {

		if ( $return_html ) {
			ob_start();
		}

		$args = self::add_defaults( $args );
		$args = self::get_specs_info( $args );

		$readonly = $args['readonly'] ? ' readonly' : '';

		$data = '';
		if ( ! empty( $args['data'] ) ) {
			foreach ( $args['data'] as $key => $value ) {
				$data .= 'data-' . $key . '="' . $value . '" ';
			}
		}

		if ( empty( $args['main_tag'] ) ) {
			$main_tag = 'li';
		} else {
			$main_tag = $args['main_tag'];
		}		?>

		<<?php echo ( $main_tag == 'li' ? $main_tag : 'div' ); ?> class="input_group <?php echo esc_html( $args['input_group_class'] ); ?>" id="<?php echo esc_attr( $args['name'] ); ?>_group" >

			<label class="<?php echo esc_attr( $args['label_class'] ); ?>" for="<?php echo esc_attr( $args['name'] ); ?>">  <?php
			    echo esc_html( $args['label'] );
			    if ( ! empty( $args['tooltip_title'] ) ) {
			        EPKB_HTML_Admin::display_tooltip( $args['tooltip_title'], $args['tooltip_body'] );
			    }
				if ( $args['is_pro'] ) {
					EPKB_HTML_Admin::display_pro_setting_tag( $args['label'] );
				}        ?>
			</label>

			<div class="input_container <?php echo esc_attr( $args['input_class'] ); ?>">
			    <input type="text"
			           name="<?php echo  esc_attr( $args['name'] ); ?>"
			           id="<?php echo  esc_attr( $args['name'] ); ?>"
			           autocomplete="<?php echo ( $args[ 'autocomplete' ] ? 'on' : 'off' ); ?>"
			           value="<?php echo esc_attr( $args['value'] ); ?>"
			           placeholder="<?php echo esc_attr( $args['placeholder'] ); ?>"						<?php
			    echo $data . $readonly;						?>
			           maxlength="<?php echo esc_attr( $args['max'] ); ?>"
			    />
			</div>

		</<?php echo ( $main_tag == 'li' ? $main_tag : 'div' ); ?>>		<?php

		if ( $return_html ) {
			return ob_get_clean();
		}

		return '';
	}

	/**
	 * Renders an HTML textarea
	 *
	 * @param array $args Arguments for the textarea
	 */
	public static function textarea( $args = array() ) {

		$defaults = array(
			'name'        => 'textarea',
			'class'       => 'large-text',
			'rows'        => 4,
		);
		$args = self::add_defaults( $args, $defaults );

		if ( empty( $args['main_tag'] ) ) {
			$main_tag = 'li';
		} else {
			$main_tag = $args['main_tag'];
		}		?>

		<<?php echo ( $main_tag == 'li' ? $main_tag : 'div' ); ?> class="epkb-input-group <?php echo esc_attr( $args['input_group_class'] ); ?>" id="<?php echo esc_attr( $args['name'] ); ?>_group" >

		<label class="<?php echo esc_attr( $args['label_class'] ); ?>" for="<?php echo esc_attr( $args['name'] ); ?>">
			<?php echo esc_html( $args['label'] ); ?>
		</label>
		<div class="input_container <?php echo esc_attr( $args['input_class'] ); ?>">
				<textarea
						rows="<?php echo esc_attr( $args['rows'] ); ?>"
						name="<?php echo esc_attr( $args['name'] ); ?>"
						id="<?php echo esc_attr( $args['name'] ); ?>"
						value="<?php echo esc_attr( $args['value'] ); ?>"
						placeholder="<?php echo esc_attr( $args['placeholder'] ); ?>"
					<?php echo ( $args['disabled'] ? ' disabled="disabled"' : '' ); ?> ><?php echo esc_html( $args['value'] ); ?>
				</textarea>
		</div>

		</<?php echo ( $main_tag == 'li' ? $main_tag : 'div' ); ?>>		<?php

		if ( ! empty( $args['info'] ) ) { ?>
			<span class="info-icon"><p class="hidden"><?php echo esc_html( $args['info'] ); ?></p></span>		<?php
		}
	}

	/**
	 * Renders an HTML Checkbox
	 *
	 * @param array $args
	 * @param bool $return_html
	 *
	 * @return string
	 */
	public static function checkbox( $args = array(), $return_html=false ) {

		if ( $return_html ) {
			ob_start();
		}

		$defaults = array(
			'name'         => 'checkbox',
		);
		$args = self::add_defaults( $args, $defaults );	?>

		<div class="config-input-group <?php echo esc_attr( $args['input_group_class'] ); ?>" id="<?php echo esc_attr( $args['name'] ); ?>_group">

			<label class="<?php echo esc_attr( $args['label_class'] ); ?>" for="<?php echo esc_attr( $args['name'] ); ?>">
				<?php echo esc_html( $args['label'] ); ?>
			</label>

			<div class="input_container <?php echo esc_attr( $args['container_class'] ); ?>">
				<input type="checkbox"
				       name="<?php echo esc_attr( $args['name'] ); ?>"
				       id="<?php echo esc_attr( $args['name'] ); ?>"
				       value="on"
				       class="<?php echo esc_attr( $args['input_class'] ); ?>"
					<?php echo checked( "on", $args['value'], false ); ?> />
			</div>
		</div>			<?php

		if ( $return_html ) {
			return ob_get_clean();
		}

		return '';
	}

	/**
	 * Renders an HTML Toggle ( checkbox )
	 *
	 * @param array $args
	 * textLoc - left, right
	 * @return false|string
	 */
	public static function checkbox_toggle( $args = array() ) {
		$defaults = array(
			'name'          => '',
			'text'          => '',
			'data'          => '',
			'topDesc'       => '',
			'bottomDesc'    => '',
			'textLoc'       => 'left',
			'checked'       => false,
			'toggleOnText'  => __( 'yes', 'echo-knowledge-base' ),
			'toggleOffext'  => __( 'no', 'echo-knowledge-base' ),
			'return_html'   => false,
		);
		$args       = self::add_defaults( $args, $defaults );
		$text       = $args['text'];
		$topDesc    = $args['topDesc'];
		$bottomDesc = $args['bottomDesc'];

		if ( $args['return_html'] ) {
			ob_start();
		}   ?>

		<div id="<?php echo esc_attr( $args['id'] ); ?>" class="epkb-settings-control-container epkb-settings-control-type-toggle <?php echo 'epkb-settings-control-type-toggle--' . esc_attr( $args['textLoc'] ); ?>" data-field="<?php echo esc_attr( $args['data'] ); ?>">     <?php

			if ( ! empty( $topDesc ) ) {    ?>
				<div class="epkb-settings-control__description"><?php echo wp_kses_post( $topDesc ); ?></div>  <?php
			}   ?>

			<div class="epkb-settings-control__field">
				<label class="epkb-settings-control__title"><?php echo esc_html( $text ); ?></label>
				<div class="epkb-settings-control__input">
					<label class="epkb-settings-control-toggle">
						<input type="checkbox" class="epkb-settings-control__input__toggle" value="on" name="<?php echo esc_attr( $args['name'] ); ?>" <?php checked( true, $args['checked'] ); ?>/>
						<span class="epkb-settings-control__input__label" data-on="<?php echo esc_attr( $args['toggleOnText'] ); ?>" data-off="<?php echo esc_attr( $args['toggleOffext'] ); ?>"></span>
						<span class="epkb-settings-control__input__handle"></span>
					</label>
				</div>
			</div>			<?php

			if ( ! empty( $bottomDesc ) ) {     ?>
				<div class="epkb-settings-control__description"><?php echo wp_kses_post( $bottomDesc ); ?></div>  <?php
			}   ?>

		</div>		<?php

		if ( $args['return_html'] ) {
			return ob_get_clean();
		}
	}

	/**
	 * Renders an HTML drop-down box
	 *
	 * @param array $args
	 */
	public static function dropdown( $args = array() ) {

		$args = self::add_defaults( $args );
		$args = self::get_specs_info( $args );  ?>

		<div class="epkb-input-group <?php echo esc_attr( $args['input_group_class'] ); ?>" id="<?php echo esc_attr( $args['name'] ); ?>_group">
			<label class="<?php echo esc_attr( $args['label_class'] ); ?>" for="<?php echo esc_attr( $args['name'] ); ?>">  <?php
				echo esc_html( $args['label'] );
				if ( ! empty( $args['tooltip_title'] ) ) {
					EPKB_HTML_Admin::display_tooltip( $args['tooltip_title'], $args['tooltip_body'] );
				}
				if ( $args['is_pro'] ) {
					EPKB_HTML_Admin::display_pro_setting_tag( $args['label'] );
				}                ?>
			</label>

			<div class="input_container <?php echo esc_attr( $args['input_class'] ); ?>" id="">

				<select name="<?php echo esc_attr( $args['name'] ); ?>" id="<?php echo esc_attr( $args['name'] ); ?>">     <?php
					foreach( $args['options'] as $key => $label ) {
						$selected = selected( $key, $args['value'], false );
						echo '<option value="' . esc_attr( $key ) . '"' . $selected . '>' . esc_html( $label ) . '</option>';
					}  ?>
				</select>
			</div>		<?php

			if ( ! empty( $args['info'] ) ) { ?>
				<span class='info-icon'><p class='hidden'><?php echo esc_html( $args['info'] ); ?></p></span>			<?php
			}	?>

		</div>		<?php
	}

	/**
	 * Renders several HTML radio buttons in a row
     *      desc_condition  if checked (value) matches it will show the description text. If the value is not set it will always show the description.
	 *
	 * @param array $args
	 */
	public static function radio_buttons_horizontal( $args = array() ) {

		$defaults = array(
			'id'                => 'radio',
			'name'              => 'radio-buttons',
            'desc_condition'    => '',
		);
		$args = self::add_defaults( $args, $defaults );
		$ix = 0; ?>

		<div class="epkb-input-group <?php echo esc_attr( $args['input_group_class'] ); ?>" id="<?php echo esc_attr( $args['name'] ); ?>_group" >

			<span class="main_label <?php echo esc_attr( $args['main_label_class'] ); ?>"><?php echo esc_html( $args['label'] ); ?></span>

			<div class="radio-buttons-horizontal <?php echo esc_attr( $args['input_class'] ); ?>" id="<?php echo esc_attr( $args['name'] ); ?>">
				<ul>					<?php

					foreach( $args['options'] as $key => $label ) {

						if ( (string) $key === (string) $args['value'] ) {
							$activeClass = "epkb-radio--active ";
						} else {
							$activeClass = '';
						} ?>

						<li class="<?php echo esc_attr( $activeClass ).esc_attr( $args['radio_class'] ); ?>">
							<div class="input_container">
								<input type="radio"
								       name="<?php echo esc_attr( $args['name'] ); ?>"
								       id="<?php echo esc_attr( $args['name'].$ix ); ?>"
								       value="<?php echo esc_attr( $key ); ?>"
								       autocomplete="<?php echo ( $args['autocomplete'] ? 'on' : 'off' ); ?>"									<?php
										checked( $key, $args['value'] )			?>
								/>
							</div>

							<label class="<?php echo esc_attr( $args['label_class'] ); ?>" for="<?php echo esc_attr( $args['name'] ).$ix ?>">
								<?php echo esc_html( $label ); ?>
							</label>
						</li>						<?php

						$ix++;
					} //foreach    	?>

				</ul>  <?php

				if ( ! empty( $args['info'] ) ) { ?>
					<span class="info-icon"><p class="hidden"><?php echo esc_html( $args['info'] ); ?></p></span>				<?php
				} ?>
			</div><?php

			if ( $args['desc'] ) {

                $showDesc = '';

                // If there is a condition check for which option is checked.
				if ( isset( $args['desc_condition'] ) ) {
					if ( (string) esc_attr( $args['desc_condition'] ) === (string) esc_attr( $args['value'] ) ) {
						$showDesc = 'radio-buttons-horizontal-desc--show';
					}
				} else {  // If no Condition show desc all the time.
					$showDesc = 'radio-buttons-horizontal-desc--show';
				}
				echo '<span class="radio-buttons-horizontal-desc '.$showDesc.'">'.$args['desc'].'</span>';

			} ?>

		</div>		<?php
	}

	/**
	 * Renders several HTML radio buttons in a row but as Icons.
	 *
	 * @param array $args
	 *  options key     = icon CSS name
	 *  option value    = text ( Hidden )*
	 */
	public static function radio_buttons_icon_selection( $args = array() ) {

		$defaults = array(
			'id'                => 'radio',
			'name'              => 'radio-buttons',
		);
		$args = self::add_defaults( $args, $defaults );
		$args = self::get_specs_info( $args );

		$ix = 0;   		?>

		<div class="epkb-input-group epkb-admin__radio-icons <?php echo esc_attr( $args['input_group_class'] ); ?>" id="<?php echo esc_attr( $args['name'] ); ?>_group" >

			<span class="epkb-main_label <?php echo esc_attr( $args['main_label_class'] ); ?>"><?php echo esc_html( $args['label'] );
            if ( $args['is_pro'] ) {
					EPKB_HTML_Admin::display_pro_setting_tag( $args['label'] );
				} ?>
            </span>

			<div class="epkb-radio-buttons-container <?php echo esc_attr( $args['input_class'] ); ?>" id="<?php echo esc_attr( $args['name'] ); ?>">              <?php 
			
				foreach( $args['options'] as $key => $label ) {	?>

					<div class="epkb-input-container">
						<label class="epkb-label" for="<?php echo esc_attr( $args['name'] ).$ix ?>">
							<span class="epkb-label__text"><?php echo esc_html( $label ); ?></span>
							<input class="epkb-input" type="radio"
								name="<?php echo esc_attr( $args['name'] ); ?>"
								id="<?php echo esc_attr( $args['name'] . $ix ); ?>"
								value="<?php echo esc_attr( $key ); ?>"
								autocomplete="<?php echo ( $args['autocomplete'] ? 'on' : 'off' ); ?>"<?php
								checked( $key, $args['value'] )	?>
							/>

                            <?php
                            switch ($key) {
	                            case 'ep_font_icon_help_dialog':
		                            echo '<span class="'.esc_attr( $key ).' epkbfa-input-icon"></span>';
		                            break;
	                            default:
		                            echo '<span class="epkbfa epkbfa-font epkbfa-'.esc_attr( $key ).' epkbfa-input-icon"></span>';

                            } ?>

						</label>
					</div> <?php

					$ix++;
				} //foreach

				if ( ! empty( $args['tooltip_title'] ) ) {
					EPKB_HTML_Admin::display_tooltip( $args['tooltip_title'], $args['tooltip_body'] );
				}


				if ( ! empty( $args['info'] ) ) { ?>
					<span class="info-icon"><p class="hidden"><?php echo esc_html( $args['info'] ); ?></p></span> <?php
				} ?>
			</div> <?php

			if ( $args['desc'] ) {
				echo $args['desc'];
			} ?>

		</div>	<?php
	}

	/**
	 * Renders several HTML radio buttons in a column
	 *
	 * @param array $args
	 *
	 * @return false|string
	 */
	public static function radio_buttons_vertical( $args = array() ) {

		$defaults = array(
			'id'                => 'radio',
			'name'              => 'radio-buttons',
			'data'              => array()
		);
		$args = self::add_defaults( $args, $defaults );
		$id =  esc_attr( $args['name'] );
		$ix = 0;

		$data_escaped = '';
		foreach ( $args['data'] as $key => $value ) {
			$data_escaped .= 'data-' . esc_attr( $key ) . '="' . esc_attr( $value ) . '" ';
		}

		if ( ! empty($args['data']['example_image']) ) {
			$args['input_group_class'] =  $args['input_group_class'] . ' eckb-wizard-radio-btn-vertical-example ';
		}

		ob_start();		?>

		<div class="config-input-group <?php echo esc_attr( $args['input_group_class'] ); ?>" id="<?php echo $id; ?>_group">		<?php

			if ( ! empty($args['data']['example_image']) ) {
				echo '<div class="eckb-wizard-radio-btn-vertical-example__icon epkbfa epkbfa-eye"></div>';
			}

			if ( ! empty($args['label']) ) {     ?>
				<span class="main_label <?php echo esc_attr( $args['main_label_class'] ); ?>">
					<?php echo esc_html( $args['label'] ); ?>
				</span>            <?php
			}                       ?>

			<div class="radio-buttons-vertical <?php echo esc_attr( $args['input_class'] ); ?>" id="<?php echo $id; ?>">
				<ul>	                <?php

					foreach( $args['options'] as $key => $label ) {
						$checked = checked( $key, $args['value'], false );		                ?>

						<li class="<?php echo esc_attr( $args['radio_class'] ); ?>">			                <?php

							$checked_class ='';
							if ( $args['value'] == $key ) {
								$checked_class = 'checked-radio';
							} ?>

							<div class="input_container config-col-1 <?php echo $checked_class; ?>">
								<input type="radio"
								       name="<?php echo esc_attr( $args['name'] ); ?>"
								       id="<?php echo $id . $ix; ?>"
								       value="<?php echo esc_attr( $key ); ?>"					                <?php
								echo $data_escaped . ' ' . $checked; ?> />
							</div>
							<label class="<?php echo esc_attr( $args['label_class'] ); ?> config-col-10" for="<?php echo $id . $ix; ?>">
								<?php echo wp_kses_post( $label ); ?>
							</label>
						</li>		                <?php

						$ix++;
					} //foreach	                ?>

				</ul>

			</div>

		</div>        <?php

		return ob_get_clean();
	}

	/**
	 * Single Inputs for text_fields_horizontal function
	 * @param array $args
	 */
	public static function horizontal_text_input( $args = array() ) {

		$args = self::add_defaults( $args );

		$data_escaped = '';
		if ( ! empty( $args['data'] ) ) {
			foreach ( $args['data'] as $key => $value ) {
				$data_escaped .= 'data-' . esc_attr( $key ) . '="' . esc_attr( $value ) . '" ';
			}
		}		?>

		<div class="<?php echo esc_attr( $args['text_class'] ); ?>">     <?php

			if ( ! empty( $args['label'] ) ) {    ?>
				<label class="<?php echo esc_attr( $args['label_class'] ); ?>" for="<?php echo esc_attr( $args['name'] ); ?>">
					<?php echo esc_html( $args['label'] ); ?>
				</label>    <?php
			}   ?>

			<div class="input_container">
				<input type="text"
				       name="<?php echo esc_attr( $args['name'] ); ?>"
				       <?php echo empty( $args['id'] ) ? '' : ' id="' . esc_attr( $args['id'] ) . '"'; ?>
				       autocomplete="<?php echo ( $args['autocomplete'] ? 'on' : 'off' ); ?>"
				       value="<?php echo esc_attr( $args['value'] ); ?>"
				       placeholder="<?php echo esc_attr( $args['placeholder'] ); ?>"
				       maxlength="<?php echo esc_attr( $args['max'] ); ?>"					<?php
				echo $data_escaped . ( $args[ 'disabled' ] ? ' disabled="disabled"' : '' );	?> />
			</div>

		</div>	<?php
	}

	/**
	 * Renders several HTML checkboxes in several columns
	 *
	 * @param array $args
	 */
	public static function checkboxes_multi_select( $args = array() ) {

		$defaults = array(
			'id'           => 'checkbox',
			'name'         => 'checkbox',
			'value'        => array(),
			'main_class'   => '',
			'main_tag'     => 'li'
		);
		$args = self::add_defaults( $args, $defaults );
		$ix = 0;    	?>

		<<?php echo esc_html( $args['main_tag'] ); ?> class=" <?php echo esc_attr( $args['input_group_class'] ); ?>" id="<?php echo esc_attr( $args['name'] ); ?>_group" >   <?php

		if ( $args['label'] != '' ) {   ?>
			<div class="main_label <?php echo esc_attr( $args['main_label_class'] ); ?>"><?php echo esc_html( $args['label'] ); ?></div>  <?php
		}   ?>

		<div class="epkb-checkboxes-horizontal <?php echo esc_attr( $args['input_class'] ); ?>" id="<?php echo esc_attr( $args['name'] ); ?>">
			 		<?php

				foreach( $args['options'] as $key => $label ) {

					$tmp_value = is_array( $args['value'] ) ? $args['value'] : array();
					$checked = in_array( $key, $tmp_value );
					$label = str_replace( ',', '', $label );
					$input_id = $args['name'] . '-' . $ix;  ?>

					<div class="epkb-input-group">
						<label class="<?php echo esc_attr( $args['label_class'] ); ?>" for="<?php echo esc_attr( $input_id ); ?>">
							<?php echo esc_html( $label ); ?>
						</label>
						<div class="input_container <?php echo esc_html( $args['input_class'] ); ?>">
							<input type="checkbox"
							       name="<?php echo esc_attr( $args['name'] ); ?>"
							       id="<?php echo esc_attr( $input_id ); ?>"
							       autocomplete="<?php echo ( $args['autocomplete'] ? 'on' : 'off' ); ?>"
							       value="<?php echo esc_attr( $key ); ?>"
								<?php checked( true, $checked ); ?>
							/>
						</div>
					</div>   	<?php

					$ix++;
				} //foreach   	?>

		</div>
		</<?php echo esc_html( $args['main_tag'] ); ?>>   <?php
	}

	/**
	 * Output submit button
	 *
	 * @param string $button_label
	 * @param string $action
	 * @param string $main_class
	 * @param string $html - any additional hidden fields
	 * @param bool $unique_button - is this unique button or a group of buttons - use 'ID' for the first and 'class' for the other
	 * @param bool $return_html
	 * @param string $inputClass
	 * @return string
	 */
	public static function submit_button_v2( $button_label, $action, $main_class='', $html='', $unique_button=true, $return_html=false, $inputClass='' ) {

		if ( $return_html ) {
			ob_start();
		}		?>

		<div class="submit <?php echo esc_attr( $main_class ); ?>">
			<input type="hidden" name="action" value="<?php echo esc_attr( $action ); ?>"/>     <?php

			if ( $unique_button ) {  ?>
				<input type="hidden" name="_wpnonce_epkb_ajax_action" value="<?php echo wp_create_nonce( "_wpnonce_epkb_ajax_action" ); ?>"/>
				<input type="submit" id="<?php echo esc_attr( $action ); ?>" class="<?php echo esc_attr( $inputClass ); ?>" value="<?php echo esc_attr( $button_label ); ?>" />  <?php
			} else {    ?>
				<input type="submit" class="<?php echo esc_attr( $action ) . ' ' . esc_attr( $inputClass ); ?>" value="<?php echo esc_attr( $button_label ); ?>" />  <?php
			}

			echo wp_kses_post( $html );  ?>
		</div>  <?php

		if ( $return_html ) {
			return ob_get_clean();
		}

		return '';
	}

	/**
	 * Renders an HTML Text field
	 * This has Wrappers because you need to be able to wrap both elements ( Label , Input )
	 *
	 * @param array $args Arguments for the text field
	 * @return string Text field
	 */
	public static function text_basic( $args = array() ) {

		$args = self::add_defaults( $args );
		$id             = $args['name'];
		$autocomplete   = $args['autocomplete'] ? 'on' : 'off';
		$readonly       = $args['readonly'] ? ' readonly' : '';
		$data = '';
		$label_wrap_open  = '';
		$label_wrap_close = '';
		$input_wrap_open  = '';
		$input_wrap_close = '';

		if ( ! empty( $args['data'] ) ) {
			foreach ( $args['data'] as $key => $value ) {
				$data .= 'data-' . $key . '="' . $value . '" ';
			}
		}
		if ( ! empty( $args['label_wrapper']) ) {
			$label_wrap_open   = '<' . esc_html( $args['label_wrapper'] ) . ' class="' . esc_attr( $args['main_label_class'] ) . '" >';
			$label_wrap_close  = '</' . esc_html( $args['label_wrapper'] ) . '>';
		}
		if ( ! empty( $args['input_wrapper']) ) {
			$label_wrap_open   = '<' . esc_html( $args['input_wrapper'] ) . ' class="' . esc_attr( $args['input_group_class'] ) . '" >';
			$label_wrap_close  = '<' . esc_html( $args['input_wrapper'] ) . '>';
		}

		if ( ! empty( $args['return_html'] ) ) {
			ob_start();
		}

		echo $label_wrap_open;  ?>
		<label class="<?php echo esc_attr( $args['label_class'] ); ?>" for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $args['label'] ); ?></label>		<?php
		echo $label_wrap_close;

		echo  $input_wrap_open; ?>
		<input type="text" name="<?php echo esc_attr( $id ); ?>" id="<?php echo esc_attr( $id ); ?>" class="<?php echo esc_attr( $args['input_class'] ); ?>"
		       autocomplete="<?php echo $autocomplete; ?>" value="<?php echo esc_attr( $args['value'] ); ?>"
		       placeholder="<?php echo esc_attr( $args['placeholder'] ); ?>" maxlength="<?php echo esc_attr( $data ); ?>" <?php echo $readonly; ?> />		<?php
		echo  $input_wrap_close;

		if ( ! empty( $args['return_html'] ) ) {
			return ob_get_clean();
		}
		return '';
	}

	private static function get_specs_info( $args ) {

		if ( empty( $args['specs'] ) ) {
			return $args;
		}

		$specs_name = $args['specs'];
		$field_specs = EPKB_KB_Config_Specs::get_fields_specification( EPKB_KB_Config_DB::DEFAULT_KB_ID );

		if ( empty( $field_specs[$specs_name] ) ) {
			return $args;
		}

		$field_spec = $field_specs[$specs_name];
		$field_spec = wp_parse_args( $field_spec, EPKB_KB_Config_Specs::get_defaults() );

		// FUTURE TO DO fix input_class
		$args_specs = array(
			'name'              => $field_spec['name'],
			'label'             => $field_spec['label'],
			'type'              => $field_spec['type'],
			'input_group_class' => 'epkb-admin__input-field epkb-admin__' . $field_spec['type'] . '-field ',
			'tooltip_title'     => $field_spec['label'],
			'tooltip_body'      => $field_spec['tooltip_body'],
			'input_class'       => $field_spec['is_pro'] && ! EPKB_Utilities::is_elegant_layouts_enabled() ? 'epkb-admin__input-disabled' : '',
			'is_pro'            => $field_spec['is_pro']
		);

		if ( $args_specs['type'] == 'select' ) {
			$args['options'] = $field_spec['options'];
		}

		return array_merge( $args, $args_specs );
	}

	/**
	 * Display settings as an admin form field
	 *
	 * @param $field_value
	 * @param $field_specs
	 * @param $tooltip
	 * @param bool $pro_disabled
	 */
	public static function display_admin_settings_field( $field_value, $field_specs, $tooltip='', $pro_disabled=false ) {

		// shared args
		$args = array(
			'name'              => $field_specs['name'],
			'label'             => $field_specs['label'],
			'input_group_class' => 'epkb-admin__input-field epkb-admin__' . $field_specs['type'] . '-field'
		);

		// tooltip args
		if ( $tooltip != '' ) {
			$args['tooltip_title'] = $field_specs['label'];
			$args['tooltip_body'] = $tooltip;
		}

		// add input disabled class
		if ( ! empty( $pro_disabled ) ) {
			$args['input_class'] = 'epkb-admin__input-disabled';
		}

		// custom args
		switch ( $field_specs['type'] ) {

			case 'select':
				$args['options'] = $field_specs['options'];
				$args['value'] = $field_value;
				self::dropdown( $args );
				break;

			case 'number':
			case 'text':
			default:
				$args['value'] = $field_value;
				$args['type'] = $field_specs['type'];
				self::text( $args );
				break;
		}
	}
}