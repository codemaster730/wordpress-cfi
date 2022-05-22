<?php

/**
 * Handles settings specifications.
 */
class EPKB_Settings_Specs {

	/**
	 * Defines data needed for display, initialization and validation/sanitation of settings
	 *
	 * ALL FIELDS ARE MANDATORY by default ( otherwise use 'mandatory' => false )
	 *
	 * @return array with settings specification
	 */
	public static function get_fields_specification() {

		// all default settings are listed here
		$plugin_settings = array(
			'debug' => array(
				'label'       => 'not used',
				'name'        => 'debug',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'off'
			)
		);

		return apply_filters( 'epkb_settings_specs', $plugin_settings );
	}

	/**
	 * Get Plugin default configuration
	 *
	 * @return array contains default setting values
	 */
	public static function get_default_settings() {
		$setting_specs = self::get_fields_specification();

		$default_configuration = array();
		foreach( $setting_specs as $key => $spec ) {
			$default = isset($spec['default']) ? $spec['default'] : '';
			$default_configuration += array( $key => $default );
		}

		return $default_configuration;
	}

	/**
	 * Get names of all configuration items for Plugin settings
	 * @return array
	 */
	public static function get_specs_item_names() {
		return array_keys( self::get_fields_specification() );
	}

	/**
	 * Get names of all configuration items for Plugin settings
	 * @return array
	 */
	public static function get_specs_item_name_keys() {
		$keys = array();
		foreach ( self::get_default_settings() as $key => $spec ) {
			$keys[$key] = '';
		}
		return $keys;
	}
}