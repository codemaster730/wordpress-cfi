<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class BP_Member_Swipe_Directory_Block
 *
 * @author  themosaurus
 * @since   1.0.0
 * @package bp-member-swipe/inc
 */
class BP_Member_Swipe_Directory_Block {

	private $attributes = array();

	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'init',                                  array( $this, 'register_block'                           ), 10 );
		add_filter( 'bp_rest_xprofile_fields_prepare_value', array( $this, 'bp_rest_xprofile_fields_add_options'      ), 10, 3 );
	}

	/**
	 * Register the swipe directory block for Gutenberg
	 */
	public function register_block() {
		// Automatically load script dependencies and version from webpack compiled file
		$asset_file = include( BP_MEMBER_SWIPE_PLUGIN_DIR_PATH . 'assets/js/build/directory-block.asset.php');

		wp_register_script(
			'bp-member-swipe-directory-block',
			BP_MEMBER_SWIPE_PLUGIN_DIR_URL . 'assets/js/build/directory-block.js',
			$asset_file['dependencies'],
			$asset_file['version']
		);

		wp_register_style(
			'bp-member-swipe-directory-block',
			BP_MEMBER_SWIPE_PLUGIN_DIR_URL . 'assets/css/directory-block.css',
			array( 'wp-edit-blocks', 'forms', 'bp-member-swipe-directory-swiper' ),
			BP_MEMBER_SWIPE_VERSION
		);

		/*
		 * Load directory-block-rtl.css instead of directory-block.css for RTL compatibility
		 */
		wp_style_add_data( 'bp-member-swipe-directory-block', 'rtl', 'replace' );

		register_block_type( 'bp-member-swipe/directory', array(
			'editor_script'   => 'bp-member-swipe-directory-block',
			'editor_style'    => 'bp-member-swipe-directory-block',
			'render_callback' => array( $this, 'render_block' ),
			'attributes'      => array(
				'xProfileFilters' => array(
					'type' => 'object',
					'default' => array(),
				),
				'membersLayout' => array(
					'type'    => 'object',
					'default' => array(
						'columns' => 2,
					),
				),
				'membersQuery' => array(
					'type'    => 'object',
					'default' => array(
						'order' => 'random',
					),
				),
			),
		) );

		wp_set_script_translations( 'bp-member-swipe-directory-block', 'bp-member-swipe', BP_MEMBER_SWIPE_PLUGIN_DIR_PATH . 'languages' );
	}

	/**
	 * Render the Gutenberg block
	 *
	 * @param $attributes
	 * @param $content
	 *
	 * @return string
	 */
	public function render_block( $attributes, $content ) {
		$this->attributes = $attributes;

		add_filter( 'bp_member_swipe_loop_query_args', array( $this, 'add_loop_query_args' ), 10, 1 );

		$content = do_shortcode( '[bms_directory]' );

		remove_filter( 'bp_member_swipe_loop_query_args', array( $this, 'add_loop_query_args' ), 10 );

		return $content;
	}

	/**
	 * Add args to the members query to apply the member filters selected in the block
	 *
	 * @param $query_args
	 *
	 * @return mixed
	 */
	public function add_loop_query_args( $query_args ) {
		if ( ! empty( $this->attributes['membersQuery'] ) ) {
			$query_args['type'] = $this->attributes['membersQuery']['order'];
		}

		if ( empty( $this->attributes['xProfileFilters'] ) ) {
			return $query_args;
		}

		global $bp, $wpdb;

		foreach ( $this->attributes['xProfileFilters'] as $field_id => $field_value ) {
			if ( empty( $field_value ) ) {
				continue;
			}

			$sql = $wpdb->prepare( "SELECT user_id FROM {$bp->profile->table_name_data} WHERE field_id = %d AND ", $field_id );

			if ( is_array( $field_value ) ) {
				$sql_value_condition = array();
				foreach ( $field_value as $option ) {
					$serialized = '%' . $wpdb->esc_like( serialize( $option ) ) . '%';
					$sql_value_condition[] = $wpdb->prepare( "value LIKE %s", $serialized );
				}
				$sql_value_condition = '(' . implode( ' AND ' , $sql_value_condition ) . ')';
			}
			else {
				$field       = new BP_XProfile_Field( $field_id );
				$comparator  = ! empty( $field->get_children() ) ? '=' : 'LIKE';
				$field_value = $wpdb->esc_like( $field_value );
				if ( 'LIKE' === $comparator ) {
					$field_value = '%' . $field_value . '%';
				}
				$sql_value_condition = $wpdb->prepare( "value {$comparator} %s", $field_value );
			}

			$sql .= $sql_value_condition;

			$results = $wpdb->get_col( $sql );

			$users = isset( $users ) ? array_intersect( $users, $results ) : $results;

			if ( empty( $users ) ) {
				break;
			}
		}

		if ( isset( $users ) ) {
			$query_args['include'] = ! empty( $users ) ? implode( ',', $users ) : '0';
		}

		return $query_args;
	}

	/**
	 * Add XProfile fields options to the endpoint response (for select, checkbox, etc... field types)
	 *
	 * @param $response WP_REST_Response
	 * @param $request WP_REST_Request
	 * @param $field BP_XProfile_Field
	 *
	 * @return WP_REST_Response
	 */
	public function bp_rest_xprofile_fields_add_options( $response, $request, $field ) {
		$new_data = $response->data;

		if ( $field->type_obj->supports_options ) {
			$options = $field->get_children();

			if ( empty( $options ) ) {
				$options = array();
			}

			$options = array_map( function( $option ) {
				return array(
					'label' => $option->name,
					'value' => esc_attr( stripslashes( $option->name ) ),
				);
			}, $options );

			$new_data = array_merge( $new_data, array(
				'options' => $options,
			) );
		}

		$response->set_data( $new_data );

		return $response;
	}
}

return new BP_Member_Swipe_Directory_Block();