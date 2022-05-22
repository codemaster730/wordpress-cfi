<?php

/**
 * Manage FAQ configuration FOR CORE in the database.
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_FAQ_Config_DB {

	// Prefix for WP option name that stores FAQ configuration
	const FAQ_CONFIG_PREFIX =  'epkb_faq_config_';
	const DEFAULT_FAQ_SHORTCODE_ID = 1;
	const DEFAULT_FAQ_SHORTCODE = 'epkb-faq-1';

	private $cached_settings = array();
	private $is_cached_all_faqs = false;

	/**
	 * Retrieve CONFIGURATION for all FAQ SHORTCODES
	 * If none found then return default FAQ configuration.
	 *
	 * @param bool $skip_check - true if caller checks that values are valid and needs quick invocation
	 *
	 * @return array settings for all registered FAQ shortcodes OR default config if none found
	 */
	function get_faq_shortcode_configs( $skip_check=false ) {
		/** @var $wpdb Wpdb */
		global $wpdb;

		// retrieve settings if already cached
		if ( ! empty($this->cached_settings) && $this->is_cached_all_faqs ) {
			if ( $skip_check ) {
				return $this->cached_settings;
			}
			$faq_options_checked = array();
			$data_valid = true;
			foreach( $this->cached_settings as $config ) {
				if ( empty($config['id']) ) {
					$data_valid = false;
					break;
				}
				// use defaults for missing or empty fields
				$faq_shortcode_id = $config['id'];
				$faq_options_checked[$faq_shortcode_id] = wp_parse_args( $config, EPKB_FAQ_Config_Specs::get_default_faq_config( $faq_shortcode_id ) );
			}
			if ( $data_valid && ! empty($faq_options_checked) && ! empty($faq_options_checked[self::DEFAULT_FAQ_SHORTCODE_ID]) ) {
				return $faq_options_checked;
			}
		}

		// retrieve all FAQ options for existing FAQ shortcodes from WP Options table
		$faq_options = $wpdb->get_results("SELECT option_value FROM $wpdb->options WHERE option_name LIKE '" . self::FAQ_CONFIG_PREFIX . "%'", ARRAY_A );
		if ( empty($faq_options) || ! is_array($faq_options) ) {
			EPKB_Logging::add_log("Did not retrieve any faq config. Using defaults. Last error: " . $wpdb->last_error, $faq_options);
			$faq_options = array();
		}

		// unserialize options and use defaults if necessary
		$faq_options_checked = array();
		foreach ( $faq_options as $ix => $row ) {

			if ( ! isset($ix) || empty($row) || empty($row['option_value']) ) {
				continue;
			}

			$config = maybe_unserialize( $row['option_value'] );
			if ( $config === false ) {
				EPKB_Logging::add_log("Could not unserialize configuration: ", EPKB_Utilities::get_variable_string($row['option_value']));
				continue;
			}

			if ( empty($config) || ! is_array($config) ) {
				EPKB_Logging::add_log("Did not find configuration");
				continue;
			}

			if ( count($config) < 100 ) {
				EPKB_Logging::add_log("Found FAQ configuration is incomplete", count($config));
			}

			if ( empty($config['id']) ) {
				EPKB_Logging::add_log("Found invalid configuration", $config);
				continue;
			}

			$faq_shortcode_id = ( $config['id'] === self::DEFAULT_FAQ_SHORTCODE_ID ) ? $config['id'] : EPKB_Utilities::sanitize_get_id( $config['id'] );
			if ( is_wp_error($faq_shortcode_id) ) {
				continue;
			}

			// with WPML we need to trigger hook to have configuration names translated
			if ( EPKB_Utilities::is_wpml_enabled( $config ) ) {
				$config = get_option( self::FAQ_CONFIG_PREFIX . $faq_shortcode_id );
			}

			// use defaults for missing or empty fields
			$faq_options_checked[$faq_shortcode_id] = wp_parse_args( $config, EPKB_FAQ_Config_Specs::get_default_faq_config( $faq_shortcode_id ) );
			$faq_options_checked[$faq_shortcode_id]['id'] = $faq_shortcode_id;

			// filter faq config for Editor
			// TODO $faq_options_checked[$faq_shortcode_id] = EPKB_Editor_Controller::filter_faq_config( $faq_options_checked[$faq_shortcode_id] );

			// cached the settings for future use
			$this->cached_settings[$faq_shortcode_id] = $faq_options_checked[$faq_shortcode_id];
		}

		$this->is_cached_all_faqs = ! empty($faq_options_checked);

		// if no valid FAQ configuration found use default
		if ( empty($faq_options_checked) || ! isset($faq_options_checked[self::DEFAULT_FAQ_SHORTCODE_ID]) ) {
			EPKB_Logging::add_log("Need at least default configuration.");
			$faq_options_checked[self::DEFAULT_FAQ_SHORTCODE_ID] = EPKB_FAQ_Config_Specs::get_default_faq_config( self::DEFAULT_FAQ_SHORTCODE_ID );
		}

		return $faq_options_checked;
	}

	/**
	 * Get IDs for all existing FAQ shortcodes. If missing, return default FAQ ID
	 *
	 * @param bool $ignore_error
	 *
	 * @return array containing all existing FAQ IDs
	 */
	public function get_faq_shortcodes_ids( $ignore_error=false ) {
		/** @var $wpdb Wpdb */
		global $wpdb;

		// retrieve all FAQ option names for existing FAQ shortcodes from WP Options table
		$faq_option_names = $wpdb->get_results("SELECT option_name FROM $wpdb->options WHERE option_name LIKE '" . self::FAQ_CONFIG_PREFIX . "%'", ARRAY_A );
		if ( empty($faq_option_names) || ! is_array($faq_option_names) ) {
			if ( ! $ignore_error ) {
				EPKB_Logging::add_log("Did not retrieve any faq config. Try to deactivate and active FAQ plugin to see if the issue will be fixed (11). Last error: " . $wpdb->last_error, $faq_option_names);
			}
			$faq_option_names = array();
		}

		$faq_shortcode_ids = array();
		foreach ( $faq_option_names as $faq_option_name ) {

			if ( empty($faq_option_name) ) {
				continue;
			}

			$faq_shortcode_id = str_replace( self::FAQ_CONFIG_PREFIX, '', $faq_option_name['option_name'] );
			$faq_shortcode_id = EPKB_Utilities::sanitize_int( $faq_shortcode_id, self::DEFAULT_FAQ_SHORTCODE_ID );
			$faq_shortcode_ids[$faq_shortcode_id] = $faq_shortcode_id;
		}

		// at least include default FAQ ID
		if ( empty($faq_shortcode_ids) || ! isset($faq_shortcode_ids[self::DEFAULT_FAQ_SHORTCODE_ID]) ) {
			$faq_shortcode_ids[self::DEFAULT_FAQ_SHORTCODE_ID] = self::DEFAULT_FAQ_SHORTCODE_ID;
		}

		return $faq_shortcode_ids;
	}

	/**
	 * GET FAQ configuration from the WP Options table. If not found then return ERROR.
	 * Logs all errors so the caller does not need to.
	 *
	 * @param String $faq_shortcode_id to get configuration for
	 * @return array|WP_Error return current FAQ configuration
	 */
	public function get_faq_shortcode_config( $faq_shortcode_id ) {
		/** @var $wpdb Wpdb */
		global $wpdb;

		// always return error if faq_shortcode_id invalid. we don't want to override stored FAQ config if there is
		// internal error that causes this
		$faq_shortcode_id = ( $faq_shortcode_id === self::DEFAULT_FAQ_SHORTCODE_ID ) ? $faq_shortcode_id : EPKB_Utilities::sanitize_get_id( $faq_shortcode_id );
		if ( is_wp_error($faq_shortcode_id) ) {
			return $faq_shortcode_id;
		}
		/** @var int $faq_shortcode_id */

		// retrieve settings if already cached
		if ( ! empty($this->cached_settings[$faq_shortcode_id]) ) {
			$config = wp_parse_args( $this->cached_settings[$faq_shortcode_id], EPKB_FAQ_Config_Specs::get_default_faq_config( $faq_shortcode_id ) );
			$config['id'] = $faq_shortcode_id;
			// filter faq config for Editor
			// TODO $config = EPKB_Editor_Controller::filter_faq_config( $config );
			return $config;
		}

		// retrieve specific FAQ configuration
		$config = $wpdb->get_var("SELECT option_value FROM $wpdb->options WHERE option_name = '" . self::FAQ_CONFIG_PREFIX . $faq_shortcode_id . "'" );
		if ( ! empty($config) ) {
			$config = maybe_unserialize( $config );
		}

		// with WPML we need to trigger hook to have configuration names translated
		if ( EPKB_Utilities::is_wpml_enabled( $config ) ) {
			$config = get_option( self::FAQ_CONFIG_PREFIX . $faq_shortcode_id );
		}

		// if FAQ configuration is missing then return error
		if ( empty($config) || ! is_array($config) ) {
			EPKB_Logging::add_log("Did not find FAQ configuration (DB231).", $faq_shortcode_id);
			return new WP_Error('DB231', "Did not find FAQ configuration. Try to deactivate and reactivate FAQ plugin to see if this fixes the issue. " . EPKB_Utilities::contact_us_for_support() );
		}

		if ( count($config) < 100 ) {
			EPKB_Logging::add_log("Found FAQ configuration is incomplete", count($config));
		}

		// use defaults for missing or empty fields
		$config = wp_parse_args( $config, EPKB_FAQ_Config_Specs::get_default_faq_config( $faq_shortcode_id ) );
		$config['id'] = $faq_shortcode_id;

		// filter faq config for Editor
		// TODO $config = EPKB_Editor_Controller::filter_faq_config( $config );

		// cached the settings for future use
		$this->cached_settings[$faq_shortcode_id] = $config;

		return $config;
	}

	/**
	 * GET FAQ configuration from the WP Options table. If not found then return default.
	 *
	 * @param String $faq_shortcode_id to get configuration for
	 * @return array return current FAQ configuration
	 */
	public function get_faq_shortcode_config_or_default( $faq_shortcode_id ) {

		$faq_config = $this->get_faq_shortcode_config( $faq_shortcode_id );
		if ( is_wp_error( $faq_config ) ) {
			return EPKB_FAQ_Config_Specs::get_default_faq_config( $faq_shortcode_id );
		}

		return $faq_config;
	}

	/**
	 * Return specific value from the FAQ configuration. Values are automatically trimmed.
	 *
	 * @param $setting_name
	 * @param string $faq_shortcode_id
	 * @param string $default
	 * @return string|array with value or $default value if this settings not found
	 */
	public function get_value( $setting_name, $faq_shortcode_id = '', $default = '' ) {

		if ( empty($setting_name) ) {
			return $default;
		}

		$faq_config = $this->get_faq_shortcode_config_or_default( $faq_shortcode_id );
		if ( is_wp_error( $faq_config ) ) {
			EPKB_Logging::add_log( "Could not retrieve FAQ configuration (15a). Settings name: ", $setting_name, $faq_config );
			return $default;
		}

		if ( isset($faq_config[$setting_name]) ) {
			return $faq_config[$setting_name];
		}

		$default_settings = EPKB_FAQ_Config_Specs::get_default_faq_config( self::DEFAULT_FAQ_SHORTCODE_ID );

		return  isset($default_settings[$setting_name]) ? $default_settings[$setting_name] : $default;
	}

	/**
	 * Set specific value in FAQ Configuration
	 *
	 * @param $faq_shortcode_id
	 * @param $key
	 * @param $value
	 * @return array|WP_Error
	 */
	public function set_value( $faq_shortcode_id, $key, $value ) {

		$faq_config = $this->get_faq_shortcode_config( $faq_shortcode_id );
		if ( is_wp_error($faq_config) ) {
			return $faq_config;
		}

		$faq_config[$key] = $value;

		return $this->update_faq_shortcode_configuration( $faq_shortcode_id, $faq_config );
    }

	/**
	 * Update FAQ Configuration. Use default if config missing.
	 *
	 * @param int $faq_shortcode_id is identification of the FAQ to update
	 * @param array $config contains FAQ configuration or empty if adding default configuration
	 *
	 * @return array|WP_Error configuration that was updated
	 */
	public function update_faq_shortcode_configuration( $faq_shortcode_id, array $config ) {

		$faq_shortcode_id = ( $faq_shortcode_id === self::DEFAULT_FAQ_SHORTCODE_ID ) ? $faq_shortcode_id : EPKB_Utilities::sanitize_get_id( $faq_shortcode_id );
		if ( is_wp_error($faq_shortcode_id) ) {
			return $faq_shortcode_id;
		}
		/** @var int $faq_shortcode_id */

		$fields_specification = EPKB_FAQ_Config_Specs::get_fields_specification( $faq_shortcode_id );
		$input_filter = new EPKB_Input_Filter();
		$sanitized_config = $input_filter->validate_and_sanitize_specs( $config, $fields_specification );
		if ( is_wp_error($sanitized_config) ) {
			EPKB_Logging::add_log( 'Could not update FAQ configuration', $faq_shortcode_id, $sanitized_config );
			return $sanitized_config;
		}

		$sanitized_config = wp_parse_args( $sanitized_config, EPKB_FAQ_Config_Specs::get_default_faq_config( $faq_shortcode_id ) );

		return $this->save_faq_shortcode_config( $sanitized_config, $faq_shortcode_id );
	}

	/**
	 * Insert or update FAQ configuration
	 *
	 * @param array $config
	 * @param $faq_shortcode_id - assuming it is a valid ID (sanitized)
	 *
	 * @return array|WP_Error if configuration is missing or cannot be serialized
	 */
	private function save_faq_shortcode_config( array $config, $faq_shortcode_id ) {
		/** @var $wpdb Wpdb */
		global $wpdb;

		if ( empty($config) || ! is_array($config) ) {
			return new WP_Error( 'save_faq_config', 'Configuration is empty' );
		}
		$config['id'] = $faq_shortcode_id;  // ensure it is the same id

		// FAQ configuration always starts with epfaq_config_[ID]
		$option_name = self::FAQ_CONFIG_PREFIX . $faq_shortcode_id;

		// add or update the option
		$serialized_config = maybe_serialize($config);
		if ( empty($serialized_config) ) {
			return new WP_Error( 'save_faq_config', 'Failed to serialize faq config for faq_shortcode_id ' . $faq_shortcode_id );
		}

		$result = $wpdb->query( $wpdb->prepare( "INSERT INTO $wpdb->options (`option_name`, `option_value`, `autoload`) VALUES (%s, %s, %s)
 												 ON DUPLICATE KEY UPDATE `option_name` = VALUES(`option_name`), `option_value` = VALUES(`option_value`), `autoload` = VALUES(`autoload`)",
												$option_name, $serialized_config, 'no' ) );
		if ( $result === false ) {
			EPKB_Logging::add_log( 'Failed to update faq config for faq_shortcode_id', $faq_shortcode_id, 'Last DB ERROR: (' . $wpdb->last_error . ')' );
			return new WP_Error( 'save_faq_config', 'Failed to update faq config for faq_shortcode_id ' . $faq_shortcode_id . ' Last DB ERROR: (' . $wpdb->last_error . ')');
		}

		// cached the settings for future use
		$this->cached_settings[$faq_shortcode_id] = $config;

		return $config;
	}

	/**
	 * Multisite installation has to reset caching between installs.
	 */
	public function reset_cache() {
		$this->cached_settings = array();
		$this->is_cached_all_faqs = false;
	}
}