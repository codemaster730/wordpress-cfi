<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Grimlock_Author_Avatars_Section_Block
 *
 * @author  themosaurus
 * @since   1.0.6
 * @package grimlock-author-avatars/inc
 */
class Grimlock_Author_Avatars_Section_Block extends Grimlock_Section_Block {

	/**
	 * Setup class.
	 *
	 * @param string $type Block type
	 * @param string $domain Block domain
	 *
	 * @since 1.1.5
	 */
	public function __construct( $type = 'author-avatars-section', $domain = 'grimlock-author-avatars' ) {
		parent::__construct( $type, $domain );

		// General Panel
		remove_filter( "{$this->id_base}_general_panel_fields",  array( $this, 'add_thumbnail_field'          ), 100 );
		remove_filter( "{$this->id_base}_general_panel_fields",  array( $this, 'add_thumbnail_size_field'     ), 100 );
		remove_filter( "{$this->id_base}_general_panel_fields",  array( $this, 'add_text_field'               ), 140 );
		remove_filter( "{$this->id_base}_general_panel_fields",  array( $this, 'add_text_wpautoped_field'     ), 150 );

		// Query Panel
		add_action( "{$this->id_base}_query_panel_fields",      array( $this, 'add_roles_field'           ), 100, 2 );
		add_action( "{$this->id_base}_query_panel_fields",      array( $this, 'add_show_name_field'       ), 110, 2 );
		add_action( "{$this->id_base}_query_panel_fields",      array( $this, 'add_limit_field'           ), 120, 2 );
		add_action( "{$this->id_base}_query_panel_fields",      array( $this, 'add_user_link_field'       ), 130, 2 );
		add_action( "{$this->id_base}_query_panel_fields",      array( $this, 'add_orderby_field'         ), 140, 2 );
		add_action( "{$this->id_base}_query_panel_fields",      array( $this, 'add_sort_direction_field'  ), 150, 2 );
		add_action( "{$this->id_base}_query_panel_fields",      array( $this, 'add_hiddenusers_field'     ), 160, 2 );

		// Layout Panel
		add_action( "{$this->id_base}_layout_panel_fields",     array( $this, 'add_avatars_layout_field'  ), 90,  2 );

		// Style tab fields
		remove_action( "{$this->id_base}_style_tab",            array( $this, 'add_color_field' ), 210 );
	}

	/**
	 * Get block args used for JS registering of the block
	 *
	 * @return array Array of block args
	 */
	public function get_block_js_args() {
		return array(
			'title'    => __( 'Grimlock Author Avatars Section', 'grimlock-author-avatars' ),
			'icon' => array(
				'foreground'=> '#000000',
				'src' => 'admin-users',
			),
			'category' => 'widgets',
			'keywords' => array( __( 'query', 'grimlock-author-avatars' ), __( 'section', 'grimlock-author-avatars' ), __( 'author', 'grimlock-author-avatars' ), __( 'avatar', 'grimlock-author-avatars' ) ),
			'supports' => array(
				'html'   => false,
				'align'  => array( 'wide', 'full' ),
				'anchor' => true,
			),
		);
	}

	/**
	 * Get block panels
	 *
	 * @return array Array of panels
	 */
	public function get_panels() {
		return array(
			'general' => esc_html__( 'General', 'grimlock-author-avatars' ),
			'query'   => esc_html__( 'Query', 'grimlock-author-avatars' ),
			'layout'  => esc_html__( 'Layout', 'grimlock-author-avatars' ),
			'style'   => esc_html__( 'Style', 'grimlock-author-avatars' ),
		);
	}

	/**
	 * Add a select to set the roles.
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_roles_field( $fields ) {
		$all_roles = wp_roles()->roles;

		/**
		 * (Core filter) Filters the list of editable roles.
		 *
		 * @param array[] $all_roles Array of arrays containing role information.
		 */
		$editable_roles = apply_filters( 'editable_roles', $all_roles );

		$choices = array();

		foreach( $editable_roles as $role ) {
			$choices[ $role['name'] ] = $role['name'];
		}

		$fields[] = $this->select_field( apply_filters( "{$this->id_base}_roles_field_args", array(
			'name'     => 'roles',
			'label'    => esc_html__( 'Only show users of roles', 'grimlock-author-avatars' ),
			'choices'  => $choices,
			'multiple' => true,
		) ) );

		return $fields;
	}

	/**
	 * Add a checkbox to set the display for the user name
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_show_name_field( $fields ) {
		$fields[] = $this->toggle_field( apply_filters( "{$this->id_base}_show_name_field_args", array(
			'name'  => 'show_name',
			'label' => esc_html__( 'Show user names next to avatars', 'grimlock-author-avatars' ),
		) ) );

		return $fields;
	}

	/**
	 * Add a number field to set the limit of users shown.
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_limit_field( $fields ) {
		$fields[] = $this->number_field( apply_filters( "{$this->id_base}_limit_field_args", array(
			'name'  => 'limit',
			'label' => esc_html__( 'Number of users shown', 'grimlock-author-avatars' ),
		) ) );

		return $fields;
	}

	/**
	 * Add a select to set the user link.
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_user_link_field( $fields ) {
		$choices = array(
			'authorpage' => esc_html__( 'Author page', 'grimlock-author-avatars' ),
			'website'    => esc_html__( 'Website', 'grimlock-author-avatars' ),
		);

		if ( is_multisite() ) {
			$choices['last_post_all'] = esc_html__( 'Lastest post', 'grimlock-author-avatars' );
			$choices['blog']          = esc_html__( 'Blog', 'grimlock-author-avatars' );
		} else {
			$choices['last_post'] = esc_html__( 'Lastest post', 'grimlock-author-avatars' );
		}

		if ( function_exists( 'buddypress' ) ) {
			$choices['bp_memberpage'] = esc_html__( 'Member page', 'grimlock-author-avatars' );
		} elseif ( class_exists( 'UM' ) ) {
			$choices['um_profile'] = esc_html__( 'Member page', 'grimlock-author-avatars' );
		} elseif ( function_exists( 'bbpress' ) ) {
			$choices['bppress_memberpage'] = esc_html__( 'Member page', 'grimlock-author-avatars' );
		}

		$choices['false'] = esc_html__( 'None', 'grimlock-author-avatars' );

		$fields[] = $this->select_field( apply_filters( "{$this->id_base}_user_link_field_args", array(
			'name'     => 'user_link',
			'label'    => esc_html__( 'Link user avatars to', 'grimlock-author-avatars' ),
			'choices'  => $choices,
		) ) );

		return $fields;
	}

	/**
	 * Add a select to set the order.
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_orderby_field( $fields ) {
		$choices = array(
			'random'               => esc_html__( 'Random', 'grimlock-author-avatars' ),
			'user_id'              => esc_html__( 'ID', 'grimlock-author-avatars' ),
			'user_login'           => esc_html__( 'Login', 'grimlock-author-avatars' ),
			'display_name'         => esc_html__( 'Display name', 'grimlock-author-avatars' ),
			'last_name'            => esc_html__( 'Last name', 'grimlock-author-avatars' ),
			'first_name'           => esc_html__( 'First name', 'grimlock-author-avatars' ),
			'date_registered'      => esc_html__( 'Registration date', 'grimlock-author-avatars' ),
			'recent_site_activity' => esc_html__( 'Recent site activity', 'grimlock-author-avatars' ),
			'recent_post_activity' => esc_html__( 'Recent post activity', 'grimlock-author-avatars' ),
		);

		if ( function_exists( 'bbpress' ) ) {
			$choices['bbpress_post_count'] = esc_html__( 'bbPress post count', 'grimlock-author-avatars' );
		}

		if ( function_exists( 'buddypress' ) ) {
			$choices['budy_press_recent_activity'] = esc_html__( 'BuddyPress Recent activity', 'grimlock-author-avatars' );
		}

		$fields[] = $this->select_field( apply_filters( "{$this->id_base}_orderby_field_args", array(
			'name'     => 'orderby',
			'label'    => esc_html__( 'Order by', 'grimlock-author-avatars' ),
			'choices'  => $choices,
		) ) );

		return $fields;
	}

	/**
	 * Add a select to set the sorting  direction.
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_sort_direction_field( $fields ) {
		$fields[] = $this->select_field( apply_filters( "{$this->id_base}_sort_direction_field_args", array(
			'name'     => 'sort_direction',
			'label'    => esc_html__( 'Order', 'grimlock-author-avatars' ),
			'choices'  => array(
				'ascending'  => esc_html__( 'Ascending', 'grimlock-author-avatars' ),
				'descending' => esc_html__( 'Descending', 'grimlock-author-avatars' ),
			),
		) ) );

		return $fields;
	}

	/**
	 * Add a text field to set a list of hidden users
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_hiddenusers_field( $fields ) {
		$fields[] = $this->text_field( apply_filters( "{$this->id_base}_hiddenusers_field_args", array(
			'name'     => 'hiddenusers',
			'label'    => esc_html__( 'Excluded users (list of comma-separated user ids to exclude)', 'grimlock-author-avatars' ),
		) ) );

		return $fields;
	}

	/**
	 * Add a radio image field to set the layout of groups for the section
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_avatars_layout_field( $fields ) {
		$fields[] = $this->radio_image_field( apply_filters( "{$this->id_base}_avatars_layout_field_args", array(
			'name'    => 'avatars_layout',
			'label'   => esc_html__( 'Layout', 'grimlock-author-avatars' ),
			'choices' => array(
				'avatars-12-by-5-cols-classic'        => GRIMLOCK_AUTHOR_AVATARS_PLUGIN_DIR_URL . 'assets/images/template-avatars-12-by-5-cols-classic.png',
				'avatars-3-3-3-3-cols-classic'        => GRIMLOCK_AUTHOR_AVATARS_PLUGIN_DIR_URL . 'assets/images/template-avatars-3-3-3-3-cols-classic.png',
				'avatars-4-4-4-cols-classic'          => GRIMLOCK_AUTHOR_AVATARS_PLUGIN_DIR_URL . 'assets/images/template-avatars-4-4-4-cols-classic.png',
				'avatars-2-2-2-2-2-2-cols-grid'       => GRIMLOCK_AUTHOR_AVATARS_PLUGIN_DIR_URL . 'assets/images/template-avatars-2-2-2-2-2-2-cols-grid.png',
				'avatars-2-2-2-2-2-2-cols-line'       => GRIMLOCK_AUTHOR_AVATARS_PLUGIN_DIR_URL . 'assets/images/template-avatars-12-by-5-cols-classic.png',
				'avatars-12-by-5-cols-overlay-slider' => GRIMLOCK_AUTHOR_AVATARS_PLUGIN_DIR_URL . 'assets/images/template-avatars-12-by-5-cols-overlay-slider.png',
				'avatars-3-3-3-3-cols-overlay-slider' => GRIMLOCK_AUTHOR_AVATARS_PLUGIN_DIR_URL . 'assets/images/template-avatars-3-3-3-3-cols-overlay-slider.png',
				'avatars-4-4-4-cols-overlay-slider'   => GRIMLOCK_AUTHOR_AVATARS_PLUGIN_DIR_URL . 'assets/images/template-avatars-4-4-4-cols-overlay-slider.png',
			),
		) ) );

		return $fields;
	}

	/**
	 * Add a radio image field to set the layout of the section
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_layout_field( $fields ) {
		$fields[] = $this->radio_image_field( apply_filters( "{$this->id_base}_layout_field_args", array(
			'name'    => 'layout',
			'label'   => esc_html__( 'Alignment', 'grimlock-author-avatars' ),
			'choices' => array(
				'12-cols-center-left' => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/section-alignment-12-cols-center-left.png',
				'12-cols-center'      => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/section-alignment-12-cols-center.png',
				'12-cols-left'        => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/section-alignment-12-cols-left.png',
			),
		) ) );

		return $fields;
	}

	/**
	 * Get default field values for the block
	 *
	 * @return array Array of default field values
	 */
	public function get_defaults() {
		$defaults = parent::get_defaults();

		return array_merge( $defaults, array(
			'title'               => esc_html__( 'Author Avatars', 'grimlock-author-avatars' ),

			'avatars_layout'      => 'avatars-12-by-5-cols-classic',
			'layout'              => '12-cols-center-left',

			'limit'               => 5,
			'roles'               => array( 'Subscriber' ),
			'show_name'           => true,
			'user_link'           => 'authorpage',
			'orderby'             => 'display_name',
			'sort_direction'      => 'ascending',
			'hiddenusers'         => '',
		) );
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
		$attributes = $this->sanitize_attributes( $attributes );
		ob_start();
		do_action( 'grimlock_author_avatars_section', apply_filters( "{$this->id_base}_component_args", $this->get_component_args( $attributes ), $attributes ) );
		return ob_get_clean();
	}

	/**
	 * Get the component args
	 *
	 * @param array $attributes Block attributes
	 *
	 * @return array Component args
	 */
	protected function get_component_args( $attributes ) {
		$args = parent::get_component_args( $attributes );

		return array_merge( $args, array(
			'avatars_layout' => $attributes['avatars_layout'],

			'limit'          => $attributes['limit'],
			'roles'          => $attributes['roles'],
			'show_name'      => $attributes['show_name'],
			'user_link'      => $attributes['user_link'],
			'orderby'        => $attributes['orderby'],
			'sort_direction' => $attributes['sort_direction'],
			'hiddenusers'    => $attributes['hiddenusers'],
		) );
	}

	/**
	 * Handles sanitizing attributes for the current block instance.
	 *
	 * @param array $new_attributes New attributes for the current block instance
	 *
	 * @return array Attributes to save
	 */
	public function sanitize_attributes( $new_attributes ) {
		$attributes = parent::sanitize_attributes( $new_attributes );

		$attributes['avatars_layout'] = isset( $new_attributes['avatars_layout'] ) ? sanitize_text_field( $new_attributes['avatars_layout'] ) : '';

		$attributes['limit']          = isset( $new_attributes['limit'] ) ? intval( $new_attributes['limit'] ) : 0;
		$attributes['roles']          = isset( $new_attributes['roles'] ) ? $new_attributes['roles'] : array();
		$attributes['show_name']      = filter_var( $new_attributes['show_name'], FILTER_VALIDATE_BOOLEAN );
		$attributes['user_link']      = isset( $new_attributes['user_link'] ) ? sanitize_text_field( $new_attributes['user_link'] ) : '';
		$attributes['orderby']        = isset( $new_attributes['orderby'] ) ? sanitize_text_field( $new_attributes['orderby'] ) : '';
		$attributes['sort_direction'] = isset( $new_attributes['sort_direction'] ) ? sanitize_text_field( $new_attributes['sort_direction'] ) : '';
		$attributes['hiddenusers']    = isset( $new_attributes['hiddenusers'] ) ? sanitize_text_field( $new_attributes['hiddenusers'] ) : '';

		return $attributes;
	}
}

return new Grimlock_Author_Avatars_Section_Block();
