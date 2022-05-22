<?php

/**
 * Grimlock_Author_Avatars_Section_Widget_Fields Class
 *
 * @author  Themosaurus
 * @since   1.0.0
 * @package  grimlock
 */
class Grimlock_Author_Avatars_Section_Widget_Fields extends Grimlock_Section_Widget_Fields {

	/**
	 * Setup class
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct( $id_base = 'grimlock_author_avatars_section_widget' ) {
		parent::__construct( $id_base );

		add_filter( "{$this->id_base}_tabs",           array( $this, 'change_tabs'              ), 10, 1 );

		// General tab
		remove_action( "{$this->id_base}_general_tab", array( $this, 'add_thumbnail_field'      ), 100 );
		remove_action( "{$this->id_base}_general_tab", array( $this, 'add_thumbnail_size_field' ), 100 );
		remove_action( "{$this->id_base}_general_tab", array( $this, 'add_text_field'           ), 130 );
		remove_action( "{$this->id_base}_general_tab", array( $this, 'add_text_wpautoped_field' ), 140 );

		// Query tab
		add_action( "{$this->id_base}_query_tab",      array( $this, 'add_roles_field'          ), 100, 2 );
		add_action( "{$this->id_base}_query_tab",      array( $this, 'add_show_name_field'      ), 110, 2 );
		add_action( "{$this->id_base}_query_tab",      array( $this, 'add_limit_field'          ), 120, 2 );
		add_action( "{$this->id_base}_query_tab",      array( $this, 'add_user_link_field'      ), 130, 2 );
		add_action( "{$this->id_base}_query_tab",      array( $this, 'add_orderby_field'        ), 140, 2 );
		add_action( "{$this->id_base}_query_tab",      array( $this, 'add_sort_direction_field' ), 150, 2 );
		add_action( "{$this->id_base}_query_tab",      array( $this, 'add_hiddenusers_field'    ), 160, 2 );

		// Layout tab
		add_action( "{$this->id_base}_layout_tab",     array( $this, 'add_avatars_layout_field' ), 90, 2 );

		// Style tab fields
		remove_action( "{$this->id_base}_style_tab",   array( $this, 'add_color_field'          ), 210 );
	}

	/**
	 * Change the list of tabs in the widget
	 *
	 * @param array $tabs The array containing the current tabs
	 *
	 * @return array The new array of tabs
	 */
	public function change_tabs( $tabs ) {
		return array_merge( $tabs, array(
			'general' => esc_html__( 'General', 'grimlock' ),
			'query'   => esc_html__( 'Query',   'grimlock' ),
			'layout'  => esc_html__( 'Layout',  'grimlock' ),
			'style'   => esc_html__( 'Style',   'grimlock' ),
		) );
	}

	/**
	 * Add a radio image field to set the layout of the section
	 *
	 * @param array $instance
	 * @param WP_Widget $widget
	 * @since 1.0.0
	 */
	public function add_avatars_layout_field( $instance, $widget ) {
		$args = array(
			'id'      => $widget->get_field_id( 'avatars_layout' ),
			'name'    => $widget->get_field_name( 'avatars_layout' ),
			'label'   => esc_html__( 'Layout:', 'grimlock-author-avatars' ),
			'value'   => $instance['avatars_layout'],
			'choices' => array(
				'avatars-12-by-5-cols-classic'    => GRIMLOCK_AUTHOR_AVATARS_PLUGIN_DIR_URL . 'assets/images/template-avatars-12-by-5-cols-classic.png',
				'avatars-3-3-3-3-cols-classic'    => GRIMLOCK_AUTHOR_AVATARS_PLUGIN_DIR_URL . 'assets/images/template-avatars-3-3-3-3-cols-classic.png',
				'avatars-4-4-4-cols-classic'      => GRIMLOCK_AUTHOR_AVATARS_PLUGIN_DIR_URL . 'assets/images/template-avatars-4-4-4-cols-classic.png',
				'avatars-2-2-2-2-2-2-cols-grid'   => GRIMLOCK_AUTHOR_AVATARS_PLUGIN_DIR_URL . 'assets/images/template-avatars-2-2-2-2-2-2-cols-grid.png',
				'avatars-2-2-2-2-2-2-cols-line'    => GRIMLOCK_AUTHOR_AVATARS_PLUGIN_DIR_URL . 'assets/images/template-avatars-12-by-5-cols-classic.png',
			),
		);

		$this->radio_image( apply_filters( "{$this->id_base}_avatars_layout_field_args", $args, $instance ) );
	}

	/**
	 * Add a radio image field to set the layout of the section
	 *
	 * @param array $instance
	 * @param WP_Widget $widget
	 * @since 1.0.0
	 */
	public function add_layout_field( $instance, $widget ) {
		$args = array(
			'id'      => $widget->get_field_id( 'layout' ),
			'name'    => $widget->get_field_name( 'layout' ),
			'label'   => esc_html__( 'Alignment:', 'grimlock-author-avatars' ),
			'value'   => $instance['layout'],
			'choices' => array(
				'12-cols-center-left' => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/section-alignment-12-cols-center-left.png',
				'12-cols-center'      => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/section-alignment-12-cols-center.png',
				'12-cols-left'        => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/section-alignment-12-cols-left.png',
			),
		);

		$this->radio_image( apply_filters( "{$this->id_base}_layout_field_args", $args, $instance ) );
	}

	/**
	 * Add a number field to set the limit for the shortcode.
	 *
	 * @param array $instance
	 * @param WP_Widget $widget
	 * @since 1.0.0
	 */
	public function add_limit_field( $instance, $widget ) {
		$args = array(
			'id'    => $widget->get_field_id( 'limit' ),
			'name'    => $widget->get_field_name( 'limit' ),
			'label' => esc_html__( 'Number of users shown:', 'grimlock-author-avatars' ),
			'value' => $instance['limit'],
		);

		$this->numberfield( apply_filters( "{$this->id_base}_limit_field_args", $args, $instance ) );
	}

	/**
	 * Add a select to set the roles.
	 *
	 * @param array $instance
	 * @param WP_Widget $widget
	 * @since 1.0.0
	 */
	public function add_roles_field( $instance, $widget ) {
		if ( ! function_exists( 'get_editable_roles' ) ) {
			require_once ABSPATH . 'wp-admin/includes/user.php';
		}

		$roles   = get_editable_roles();
		$choices = array();

		foreach( $roles as $role ) {
			$choices[$role['name']] = $role['name'];
		}

		$args = array(
			'id'       => $widget->get_field_id( 'roles' ),
			'name'    => $widget->get_field_name( 'roles' ),
			'label'    => esc_html__( 'Only show users of roles:', 'grimlock-author-avatars' ),
			'value'    => $instance['roles'],
			'choices'  => $choices,
			'multiple' => true,
		);

		$this->select( apply_filters( "{$this->id_base}_roles_field_args", $args, $instance ) );
	}

	/**
	 * Add a checkbox to set the display for the user name
	 *
	 * @param array $instance
	 * @param WP_Widget $widget
	 * @since 1.0.0
	 */
	public function add_show_name_field( $instance, $widget ) {
		$args = array(
			'id'    => $widget->get_field_id( 'show_name' ),
			'name'    => $widget->get_field_name( 'show_name' ),
			'label' => esc_html__( 'Show user names next to avatars', 'grimlock-author-avatars' ),
			'value' => $instance['show_name'],
		);

		$this->checkbox( apply_filters( "{$this->id_base}_show_name_field_args", $args, $instance ) );
	}

	/**
	 * Add a select to set the user link.
	 *
	 * @param array $instance
	 * @param WP_Widget $widget
	 * @since 1.0.0
	 */
	public function add_user_link_field( $instance, $widget ) {
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

		$args = array(
			'id'      => $widget->get_field_id( 'user_link' ),
			'name'    => $widget->get_field_name( 'user_link' ),
			'label'   => esc_html__( 'Link user avatars to:', 'grimlock-author-avatars' ),
			'value'   => $instance['user_link'],
			'choices' => $choices,
		);

		$this->select( apply_filters( "{$this->id_base}_user_link_field_args", $args, $instance ) );
	}

	/**
	 * Add a select to set the order.
	 *
	 * @param array $instance
	 * @param WP_Widget $widget
	 * @since 1.0.0
	 */
	public function add_orderby_field( $instance, $widget ) {
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

		$args = array(
			'id'      => $widget->get_field_id( 'orderby' ),
			'name'    => $widget->get_field_name( 'orderby' ),
			'label'   => esc_html__( 'Order by:', 'grimlock-author-avatars' ),
			'value'   => $instance['orderby'],
			'choices' => $choices,
		);

		$this->select( apply_filters( "{$this->id_base}_orderby_field_args", $args, $instance ) );
	}

	/**
	 * Add a select to set the sorting  direction.
	 *
	 * @param array $instance
	 * @param WP_Widget $widget
	 * @since 1.0.0
	 */
	public function add_sort_direction_field( $instance, $widget ) {
		$args = array(
			'id'      => $widget->get_field_id( 'sort_direction' ),
			'name'    => $widget->get_field_name( 'sort_direction' ),
			'label'   => esc_html__( 'Order:', 'grimlock-author-avatars' ),
			'value'   => $instance['sort_direction'],
			'choices' => array(
				'ascending'  => esc_html__( 'Ascending', 'grimlock-author-avatars' ),
				'descending' => esc_html__( 'Descending', 'grimlock-author-avatars' ),
			),
		);

		$this->select( apply_filters( "{$this->id_base}_sort_direction_field_args", $args, $instance ) );
	}

	/**
	 * Add a text field to set a list of hidden users
	 *
	 * @param array $instance
	 * @param WP_Widget $widget
	 * @since 1.0.0
	 */
	public function add_hiddenusers_field( $instance, $widget ) {
		$args = array(
			'id'    => $widget->get_field_id( 'hiddenusers' ),
			'name'  => $widget->get_field_name( 'hiddenusers' ),
			'label' => esc_html__( 'Excluded users (list of comma-separated user ids to exclude):', 'grimlock' ),
			'value' => html_entity_decode( $instance['hiddenusers'] ),
		);

		$this->textfield( apply_filters( "{$this->id_base}_hiddenusers_field_args", $args, $instance ) );
	}

	/**
	 * Change the default settings for the widget
	 *
	 * @param array $defaults The default settings for the widget.
	 *
	 * @return array The updated default settings for the widget.
	 */
	public function change_defaults( $defaults ) {
		$defaults = parent::change_defaults( $defaults );

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
	 * Change the arguments sent to the component in charge of rendering the widget
	 *
	 * @param array $component_args The arguments for the component in charge of rendering the widget
	 * @param array $instance Settings for the current widget instance.
	 * @param array $widget_args Display arguments including 'before_title', 'after_title',
	 *                           'before_widget', and 'after_widget'.
	 *
	 * @return array The updated arguments for the component in charge of rendering the widget
	 */
	public function change_component_args( $component_args, $instance, $widget_args, $widget_id ) {
		$component_args = parent::change_component_args( $component_args, $instance, $widget_args, $widget_id );

		return array_merge( $component_args, array(
			'avatars_layout' => $instance['avatars_layout'],

			'limit'          => $instance['limit'],
			'roles'          => $instance['roles'],
			'show_name'      => $instance['show_name'],
			'user_link'      => $instance['user_link'],
			'orderby'        => $instance['orderby'],
			'sort_direction' => $instance['sort_direction'],
			'hiddenusers'    => $instance['hiddenusers'],
		) );
	}

	/**
	 * Handles sanitizing settings for the current widget instance.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $new_instance New settings for this instance as input by the user via
	 *                            WP_Widget::form().
	 * @param array $old_instance Old settings for this instance.
	 *
	 * @return array Settings to save or bool false to cancel saving.
	 */
	public function sanitize_instance( $new_instance, $old_instance ) {
		$instance = parent::sanitize_instance( $new_instance, $old_instance );

		$instance['avatars_layout']      = isset( $new_instance['avatars_layout'] ) ? sanitize_text_field( $new_instance['avatars_layout'] ) : '';

		$instance['limit']               = isset( $new_instance['limit'] ) ? intval( $new_instance['limit'] ) : 0;
		$instance['roles']               = isset( $new_instance['roles'] ) ? $new_instance['roles'] : array();
		$instance['show_name']           = ! empty( $new_instance['show_name'] );
		$instance['user_link']           = isset( $new_instance['user_link'] ) ? sanitize_text_field( $new_instance['user_link'] ) : '';
		$instance['orderby']             = isset( $new_instance['orderby'] ) ? sanitize_text_field( $new_instance['orderby'] ) : '';
		$instance['sort_direction']      = isset( $new_instance['sort_direction'] ) ? sanitize_text_field( $new_instance['sort_direction'] ) : '';
		$instance['hiddenusers']         = isset( $new_instance['hiddenusers'] ) ? sanitize_text_field( $new_instance['hiddenusers'] ) : '';

		return $instance;
	}
}

return new Grimlock_Author_Avatars_Section_Widget_Fields();
