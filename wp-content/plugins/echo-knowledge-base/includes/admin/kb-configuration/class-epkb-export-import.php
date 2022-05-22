<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Handle import and export of KB configuration
 *
 * @copyright   Copyright (C) 2019, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_Export_Import {
	
	private $message = array(); // error/warning/success messages
	//private $operation_log = array();
	private $add_ons_info = array(
										'Echo_Knowledge_Base' => 'epkb',
										'Echo_Advanced_Search' => 'asea',
										'Echo_Article_Rating_And_Feedback' => 'eprf', 
										'Echo_Elegant_Layouts' => 'elay',
										'Echo_Widgets' => 'widg',
										'Echo_Article_Features' => 'eart',
										// FUTURE DODO Links Editor and MKB
							);

	private $ignored_fields = array('id', 'status', 'kb_main_pages', 'kb_name', 'kb_articles_common_path','categories_in_url_enabled','wpml_is_enabled');

	/**
	 * Run export
	 * @param $kb_id
	 * return text message about error or stop script and show export file
	 * @return String|array
	 */
	public function download_export_file( $kb_id ) {

		if ( ! current_user_can('manage_options') ) {
			$this->message['error'] = __( 'Login or refresh this page to export KB configuration.', 'echo-knowledge-base' );
			return $this->message;
		}

		// export data and report error if an issue found
		$exported_data = $this->export_kb_config( $kb_id );
		if ( empty( $exported_data ) ) {
			return $this->message;
		}

		ignore_user_abort( true );
		
		if ( ! $this->is_function_disabled( 'set_time_limit' ) && ! ini_get( 'safe_mode' ) ) {
			set_time_limit( 0 );
		}

		nocache_headers();
		header( 'Content-Type: application/json; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=kb_' . $kb_id . '_config_export_' . date('Y_m_d_H_i_s') . '.json' );
		header( "Expires: 0" );

		echo json_encode($exported_data);

		return [];
	}
	
	/**
	 * Export KB configuration.
	 *
	 * @param $kb_id
	 * @return null
	 */
	private function export_kb_config( $kb_id ) {
		global $wp_widget_factory;

		$export_data = array();

		// process each plugin (KB core and add-ons)
		foreach ($this->add_ons_info as $add_on_class => $add_on_prefix) {

			if ( ! class_exists($add_on_class) ) {
				continue;
			}

			// retrieve plugin instance
			/** @var $plugin_instance Echo_Knowledge_Base */
			$plugin_instance = $this->get_plugin_instance( $add_on_prefix );
			if ( empty($plugin_instance) ) {
				return null;
			}

			// retrieve plugin configuration
			$add_on_config = $plugin_instance->kb_config_obj->get_kb_config( $kb_id, true );
			if ( is_wp_error( $add_on_config ) ) {
				$this->message['error'] = $add_on_config->get_error_message();
				return null;
			}
			if ( ! is_array($add_on_config) ) {
				$this->message['error'] = __( 'Found invalid data.', 'echo-knowledge-base' ) . ' (' . $add_on_prefix . ')';
				return null;
			}

			// remove protected fields
			foreach( $this->ignored_fields as $ignored_field ) {
				if ( isset($add_on_config[$ignored_field]) )  {
					unset($add_on_config[$ignored_field]);
				}
			}
			
			$export_data[$add_on_prefix] = $add_on_config;
			$export_data[$add_on_prefix]['plugin_version'] = $plugin_instance::$version;
		}

		if ( empty($export_data) ) {
			$this->message['error'] = 'E40'; // do not translate;
			return null;
		}
		
		// export WordPress widgets if it is available
		if ( empty($wp_widget_factory) || empty($wp_widget_factory->widgets ) ) {
			return $export_data;
		}

		// Check our sidebar for the widgets exists
		/** TODO	$sidebars = get_option( 'sidebars_widgets' );
		if ( empty( $sidebars['eckb_articles_sidebar'] ) ) {
			return $export_data;
		}
		
		// get names and indexes for widgets
		$export_data['kb_widgets'] = array();
		foreach ( $sidebars['eckb_articles_sidebar'] as $key => $widget_id ) {
			$sidebar_widget_data = explode( '-', $widget_id );
			$sidebar_widget_index = array_pop( $sidebar_widget_data );
			$sidebar_widget_name = implode($sidebar_widget_data);
			
			if ( ! isset( $export_data['kb_widgets'][$sidebar_widget_name] ) ) {
				$export_data['kb_widgets'][$sidebar_widget_name] = array();
			}
			
			$export_data['kb_widgets'][$sidebar_widget_name][$sidebar_widget_index] =  array();
		}

		// check each widget option to know if it was added to the eckb widget panel
		foreach ( $export_data['kb_widgets'] as $widget_name => $widget_data ) {

			$widget_option = get_option( 'widget_' . $widget_name );
			if ( empty( $widget_option ) ) {
				continue;
			}
			
			foreach ( $export_data['kb_widgets'][$widget_name] as $sidebar_widget_index => $data ) {
				
				if ( empty( $widget_option[$sidebar_widget_index] ) ) {
					continue;
				}
				
				$export_data['kb_widgets'][$widget_name][$sidebar_widget_index] = $widget_option[$sidebar_widget_index];
			}
			
		} */

		return $export_data;
	}

	/**
	 * Import KB configuration from a file.
	 *
	 * @param $kb_id
	 * @return array|null
	 */
	public function import_kb_config( $kb_id ) {

		if ( ! current_user_can('manage_options') ) {
			$this->message['error'] = __( 'You do not have permission.', 'echo-knowledge-base' );
			return $this->message;
		}

		$import_file_name = $_FILES['import_file']['tmp_name'];
		if ( empty($import_file_name) ) {
			$this->message['error'] = __( 'Import file format is not correct.', 'echo-knowledge-base' ) . ' (0)';
			return $this->message;
		}

		// check the file
		if ( empty( is_uploaded_file( $import_file_name ) ) ) {
			$this->message['error'] = __( 'Import file format is not correct.', 'echo-knowledge-base' ) . ' (3)';
			return $this->message;
		}

		// retrieve content of the imported file
		$import_data_file = file_get_contents($import_file_name);
		if ( empty($import_data_file) ) {
			$this->message['error'] = __( 'Import file format is not correct.', 'echo-knowledge-base' ) . ' (1)';
			return $this->message;
		}

		// validate imported data
		$import_data = json_decode($import_data_file, true);
		if ( empty($import_data) || ! is_array($import_data) ) {
			$this->message['error'] = __( 'Import file format is not correct.', 'echo-knowledge-base' ) . ' (2)';
			return $this->message;
		}

		// KB Core needs to be present
		if ( ! isset($import_data['epkb']) ) {
			$this->message['error'] = __( 'Knowledge Base data is missing', 'echo-knowledge-base' );
			return $this->message;
		}

		// process each plugin (KB core and add-ons)
		foreach ($this->add_ons_info as $add_on_class => $add_on_prefix) {

			$plugin_name = $this->get_plugin_name( $add_on_class );
			
			// add-on is installed but not active and no data is present in import for the add-on
			if ( empty($import_data[$add_on_prefix]) && ! class_exists($add_on_class) ) {
				continue;
			}
			
			// import data exists but plugin is not active
			if ( isset($import_data[$add_on_prefix]) && ! class_exists($add_on_class) ) {
				$this->message['error'] = __( 'Import failed because found import data for a plugin that is not active: ', 'echo-knowledge-base' ) . $plugin_name;
				return $this->message;
			}

			// plugin is active but import data does not exist TODO - should be warning to user
			if ( ! isset($import_data[$add_on_prefix]) && class_exists($add_on_class) ) {
				/* OK to import less $this->message['error'] = __( 'Import failed because found a plugin that is active with no corresponding import data: ', 'echo-knowledge-base' ) . $plugin_name;
				return $this->message; */
				continue;
			}

			// ensure imported data have correct format
			if ( ! is_array($import_data[$add_on_prefix]) ) {
				$this->message['error'] = __( 'Import failed because found invalid data.', 'echo-knowledge-base' ) . ' (' . $plugin_name . ')';
				return $this->message;
			}

			// verify most data is preset
			$specs_class_name = strtoupper($add_on_prefix) . '_KB_Config_Specs';
			if ( ! class_exists($specs_class_name) || ! method_exists( $specs_class_name, 'get_specs_item_names') ) {
				$this->message['error'] = 'E34 (' . $plugin_name . ')'; // do not translate
				return $this->message;
			}

			$add_on_config = $import_data[$add_on_prefix];

			// check if we need to upgrade data
			$add_on_config['id'] = $kb_id;
			$this->upgrade_plugin_data( $add_on_prefix, $add_on_config );

			/** @var $specs_class_name EPKB_KB_Config_Specs */
			$specs_found = 0;
			$specs_not_found = 0;
			$fields_specification = $specs_class_name::get_specs_item_names();
			foreach( $fields_specification as $key ) {
				if ( isset($add_on_config[$key]) ) {
					$specs_found++;
				} else {
					$specs_not_found++;
				}
			}

			// validate imported data
			if ( $specs_found == 0 || $specs_not_found > $specs_found ) {
				$this->message['error'] = __( "Found invalid data.", 'echo-knowledge-base' ) . ' (' . $plugin_name . ',' . $specs_found . ',' . $specs_not_found . ')';
				return $this->message;
			}

			// retrieve plugin instance
			/** @var $plugin_instance Echo_Knowledge_Base */
			$plugin_instance = $this->get_plugin_instance( $add_on_prefix );
			if ( empty( $plugin_instance ) ) {
				$this->message['error'] =  __( 'Import failed', 'echo-knowledge-base' );
				return $this->message;
			}

			// for KB Core, Main and Article Page could have Elegant layout so we need it enabled
			if ( $add_on_prefix == 'epkb' ) {

				if ( ! in_array( $add_on_config['kb_main_page_layout'], EPKB_KB_Config_Layouts::get_main_page_layout_names() ) ) {
					$this->message['error'] = __( "Elegant Layouts needs to be active.", 'echo-knowledge-base' ) . ' (' . $add_on_config['kb_main_page_layout'] . ')';

					return $this->message;
				}
			}

			// remove protected fields
			foreach( $this->ignored_fields as $ignored_field ) {
				if ( isset($add_on_config[$ignored_field]) )  {
					unset($add_on_config[$ignored_field]);
				}
			}
			
			$orig_config = $plugin_instance->kb_config_obj->get_kb_config( $kb_id, true );
			if ( is_wp_error( $orig_config ) ) {
				$this->message['error'] =  'E31 (' . $plugin_name . ')' . $orig_config->get_error_message();  // do not translate
				return $this->message;
			}

			$add_on_config = array_merge( $orig_config, $add_on_config);
			
			// update add-on configuration
			$add_on_config = $plugin_instance->kb_config_obj->update_kb_configuration( $kb_id, $add_on_config );
			/** @var $add_on_config WP_Error */
			if ( is_wp_error($add_on_config) ) {
				$this->message['error'] =  'E36 (' . $plugin_name . ')' . $add_on_config->get_error_message();  // do not translate
				return $this->message;
			}

			//$this->operation_log[] = 'Import completed for plugin ' . $plugin_name;
		}
		
		// import KB Widgets
		/** TODO	$old_kb_widgets = get_option('sidebars_widgets');

		// move old widgets to inactive panel to save user's settings
		if ( ! empty($old_kb_widgets['eckb_articles_sidebar']) ) {
			foreach ( $old_kb_widgets['eckb_articles_sidebar'] as $key => $widget_id ) {
				$old_kb_widgets['wp_inactive_widgets'][] = $widget_id;
			}
		}
		
		$old_kb_widgets['eckb_articles_sidebar'] = array();
		
		// Import widgets 
		if ( ! empty( $import_data['kb_widgets'] ) )  {
			foreach ( $import_data['kb_widgets'] as $new_widget_name => $new_widgets ) {
				
				foreach ( $new_widgets as $new_widget ) {

					$widget_option = get_option( 'widget_' . $new_widget_name );
					if ( empty( $widget_option ) ) {
						continue; // This means that widget is not installed on this WP instance
					}
					
					$new_widget_index = 1;
					while ( isset( $widget_option[$new_widget_index] ) and $new_widget_index < 100 ) {
						$new_widget_index++;
					}
				
					$widget_option[$new_widget_index] = $new_widget;
					update_option( 'widget_' . $new_widget_name, $widget_option );
					
					$old_kb_widgets['eckb_articles_sidebar'][] = $new_widget_name . '-' . $new_widget_index;
				}
			}
		}
		
		update_option( 'sidebars_widgets', $old_kb_widgets ); */
		
		//$this->operation_log[] = 'Import finished successfully';
		$this->message['success'] =  __( 'Import finished successfully', 'echo-knowledge-base' );
		
		return $this->message;
	}

	private function upgrade_plugin_data( $add_on_prefix, &$plugin_config ) {

		$import_plugin_version = empty($plugin_config['plugin_version']) ? '' : $plugin_config['plugin_version'];

		switch ( $add_on_prefix ) {

			case 'epkb':
				$last_version = empty($import_plugin_version) ? '6.9.9' : $import_plugin_version;
				if ( $last_version != Echo_Knowledge_Base::$version ) {
					EPKB_Upgrades::run_upgrade( $plugin_config, $last_version );
				}
				break;

			case 'asea':
				$last_version = empty($import_plugin_version) ? '2.13.9' : $import_plugin_version;
				if ( class_exists('Echo_Advanced_Search') && $last_version != Echo_Advanced_Search::$version && class_exists('ASEA_Upgrades') && is_callable(array('ASEA_Upgrades', 'run_upgrade')) ) {
					ASEA_Upgrades::run_upgrade( $plugin_config, $last_version );
				}
				break;

			case 'elay':
				$last_version = empty($import_plugin_version) ? '2.5.4' : $import_plugin_version;
				if ( class_exists('Echo_Elegant_Layouts') && $last_version != Echo_Elegant_Layouts::$version && class_exists('ELAY_Upgrades') && is_callable(array('ELAY_Upgrades', 'run_upgrade')) ) {
					ELAY_Upgrades::run_upgrade( $plugin_config, $last_version );
				}
				break;

			case 'eprf':
				$last_version = empty($import_plugin_version) ? '1.4.0' : $import_plugin_version;
				if ( class_exists('Echo_Article_Rating_And_Feedback') && $last_version != Echo_Article_Rating_And_Feedback::$version && class_exists('EPRF_Upgrades') && is_callable(array('EPRF_Upgrades', 'run_upgrade')) ) {
					EPRF_Upgrades::run_upgrade( $plugin_config, $last_version );
				}
				break;

		}
	}

	/**
	 * Call function to get/save add_on configuration
	 * @param $prefix
	 * @return null on error (and set error message) or valid DB object
	 */
	private function get_plugin_instance( $prefix ) {

		if ( ! in_array( $prefix, $this->add_ons_info ) ) {
			$this->message['error'] = 'E37 (' . $prefix . ')'; // do not translate
			return null;
		}

		// get function
		$add_on_function_name = $prefix . '_get_instance';
		if ( ! function_exists($add_on_function_name) ) {
			$this->message['error'] = 'E38 (' . $add_on_function_name . ')'; // do not translate
			return null;
		}

		// get DB class instance
		$instance = call_user_func($add_on_function_name);
		if ( is_object($instance) ) {
			return $instance;
		}

		$plugin_name = array_flip($this->add_ons_info);
		$plugin_name = isset($plugin_name[$prefix]) ? $this->get_plugin_name($plugin_name[$prefix]) : 'Unknown plugin';

		$this->message['error'] = $plugin_name . ' - ' . __( 'is the plugin active?', 'echo-knowledge-base' );

		return null;
	}

	private function get_plugin_name( $add_on_class_name ) {
		return str_replace('_', ' ', $add_on_class_name);
	}

	/**
	 * Checks whether function is disabled.
	 * @param $function
	 * @return bool
	 */
	private function is_function_disabled( $function ) {
		$disabled = explode( ',',  ini_get( 'disable_functions' ) );
		return in_array( $function, $disabled );
	}
}