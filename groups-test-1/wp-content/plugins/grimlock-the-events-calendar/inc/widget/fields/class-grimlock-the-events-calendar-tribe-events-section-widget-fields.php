<?php

/**
 * Grimlock_The_Events_Calendar_Tribe_Events_Section_Widget_Fields Class
 *
 * @author  Themosaurus
 * @since   1.0.0
 * @package  grimlock
 */
class Grimlock_The_Events_Calendar_Tribe_Events_Section_Widget_Fields extends Grimlock_Query_Section_Widget_Fields {

	/**
	 * Setup class
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct( $id_base = 'grimlock_the_events_calendar_tribe_events_section_widget' ) {
		parent::__construct( $id_base );

		// General tab
		add_action( "{$this->id_base}_general_tab", array( $this, 'add_event_date_displayed_field'     ), 200, 2 );
		add_action( "{$this->id_base}_general_tab", array( $this, 'add_event_venue_displayed_field'    ), 210, 2 );
		add_action( "{$this->id_base}_general_tab", array( $this, 'add_event_category_displayed_field' ), 220, 2 );
		add_action( "{$this->id_base}_general_tab", array( $this, 'add_event_cost_displayed_field'     ), 230, 2 );

		// Query tab
		remove_action( "{$this->id_base}_query_tab", array( $this, 'add_post_type_field'          ), 100 );
		remove_action( "{$this->id_base}_query_tab", array( $this, 'add_handpick_posts_field'     ), 100 );
		remove_action( "{$this->id_base}_query_tab", array( $this, 'add_posts_field'              ), 100 );
		add_action( "{$this->id_base}_query_tab",    array( $this, 'add_event_display_field'      ), 112, 2 );
		add_action( "{$this->id_base}_query_tab",    array( $this, 'add_show_only_featured_field' ), 113, 2 );
		add_action( "{$this->id_base}_query_tab",    array( $this, 'add_start_date_field'         ), 115, 2 );
		add_action( "{$this->id_base}_query_tab",    array( $this, 'add_end_date_field'           ), 117, 2 );

		if ( class_exists( 'Tribe__Events__Pro__Main' ) ) {
			add_action( "{$this->id_base}_query_tab", array( $this, 'add_hide_recurrences_field' ), 119, 2 );
		}

		add_filter( "{$this->id_base}_orderby_field_args", array( $this, 'change_orderby_field_args' ), 10, 2 );
	}

	/**
	 * Display a date picker field for the widget form.
	 *
	 * @param $args
	 */
	protected function date_picker( $args ) {
		if ( empty( $args ) ) {
			return;
		}
		$args = wp_parse_args( $args, array(
			'id'          => '',
			'name'        => '',
			'value'       => '',
			'label'       => '',
			'description' => '',
		) ); ?>
		<p>
			<label for="<?php echo $args['id']; ?>"><?php echo $args['label']; ?></label>
			<input class="widefat grimlock_the_events_calendar_tribe_events_section_widget-date-picker" id="<?php echo $args['id']; ?>" name="<?php echo $args['name']; ?>" type="text" value="<?php echo esc_attr( $args['value'] ); ?>" />
			<?php if ( ! empty( $args['description'] ) ) : ?>
				<small style="display: inline-block;"><?php echo $args['description'] ?></small>
			<?php endif; ?>
		</p>
		<?php
	}

	/**
	 * Add a checkbox to set whether the event date is displayed in the widget
	 *
	 * @param array $instance
	 * @param WP_Widget $widget
	 * @since 1.0.0
	 */
	public function add_event_date_displayed_field( $instance, $widget ) {
		$args = array(
			'id'    => $widget->get_field_id( 'event_date_displayed' ),
			'name'  => $widget->get_field_name( 'event_date_displayed' ),
			'label' => esc_html__( 'Display date', 'grimlock-the-events-calendar' ),
			'value' => $instance['event_date_displayed'],
		);

		$this->checkbox( apply_filters( "{$this->id_base}_event_date_displayed_field_args", $args, $instance ) );
	}

	/**
	 * Add a checkbox to set whether the event venue is displayed in the widget
	 *
	 * @param array $instance
	 * @param WP_Widget $widget
	 * @since 1.0.0
	 */
	public function add_event_venue_displayed_field( $instance, $widget ) {
		$args = array(
			'id'    => $widget->get_field_id( 'event_venue_displayed' ),
			'name'  => $widget->get_field_name( 'event_venue_displayed' ),
			'label' => esc_html__( 'Display venue', 'grimlock-the-events-calendar' ),
			'value' => $instance['event_venue_displayed'],
		);

		$this->checkbox( apply_filters( "{$this->id_base}_event_venue_displayed_field_args", $args, $instance ) );
	}

	/**
	 * Add a checkbox to set whether the event category is displayed in the widget
	 *
	 * @param array $instance
	 * @param WP_Widget $widget
	 * @since 1.0.0
	 */
	public function add_event_category_displayed_field( $instance, $widget ) {
		$args = array(
			'id'    => $widget->get_field_id( 'event_category_displayed' ),
			'name'  => $widget->get_field_name( 'event_category_displayed' ),
			'label' => esc_html__( 'Display categories', 'grimlock-the-events-calendar' ),
			'value' => $instance['event_category_displayed'],
		);

		$this->checkbox( apply_filters( "{$this->id_base}_event_category_displayed_field_args", $args, $instance ) );
	}

	/**
	 * Add a checkbox to set whether the event cost is displayed in the widget
	 *
	 * @param array $instance
	 * @param WP_Widget $widget
	 * @since 1.0.0
	 */
	public function add_event_cost_displayed_field( $instance, $widget ) {
		$args = array(
			'id'    => $widget->get_field_id( 'event_cost_displayed' ),
			'name'  => $widget->get_field_name( 'event_cost_displayed' ),
			'label' => esc_html__( 'Display cost', 'grimlock-the-events-calendar' ),
			'value' => $instance['event_cost_displayed'],
		);

		$this->checkbox( apply_filters( "{$this->id_base}_event_cost_displayed_field_args", $args, $instance ) );
	}

	/**
	 * Add a select to set the title format for the Section Component.
	 *
	 * @param array $instance
	 * @param WP_Widget $widget
	 * @since 1.0.0
	 */
	public function add_event_display_field( $instance, $widget ) {
		$args = array(
			'id'      => $widget->get_field_id( 'event_display' ),
			'name'    => $widget->get_field_name( 'event_display' ),
			'label'   => esc_html__( 'Events display:', 'grimlock-the-events-calendar' ),
			'value'   => $instance['event_display'],
			'choices' => array(
				'custom'   => esc_html__( 'All events', 'grimlock-the-events-calendar' ),
				'list'     => esc_html__( 'Only upcoming events', 'grimlock-the-events-calendar' ),
				'past'     => esc_html__( 'Only past events', 'grimlock-the-events-calendar' ),
			),
		);

		$this->select( apply_filters( "{$this->id_base}_event_display_field_args", $args, $instance ) );
	}

	/**
	 * Add a checkbox field to set whether the query should only return featured events
	 *
	 * @param array $instance
	 * @param WP_Widget $widget
	 * @since 1.1.3
	 */
	public function add_show_only_featured_field( $instance, $widget ) {
		$args = array(
			'id'    => $widget->get_field_id( 'show_only_featured' ),
			'name'  => $widget->get_field_name( 'show_only_featured' ),
			'label' => esc_html__( 'Show only featured events', 'grimlock-the-events-calendar' ),
			'value' => $instance['show_only_featured'],
		);

		$this->checkbox( apply_filters( "{$this->id_base}_show_only_featured_field_args", $args, $instance ) );
	}

	/**
	 * Add a text field to set the start date for the query
	 *
	 * @param array $instance
	 * @param WP_Widget $widget
	 * @since 1.0.0
	 */
	public function add_start_date_field( $instance, $widget ) {
		$args = array(
			'id'    => $widget->get_field_id( 'start_date' ),
			'name'  => $widget->get_field_name( 'start_date' ),
			'label' => esc_html__( 'Start date:', 'grimlock-the-events-calendar' ),
			'value' => $instance['start_date'],
		);

		$this->date_picker( apply_filters( "{$this->id_base}_start_date_field_args", $args, $instance ) );
	}

	/**
	 * Add a text field to set the start date for the query
	 *
	 * @param array $instance
	 * @param WP_Widget $widget
	 * @since 1.0.0
	 */
	public function add_end_date_field( $instance, $widget ) {
		$args = array(
			'id'    => $widget->get_field_id( 'end_date' ),
			'name'  => $widget->get_field_name( 'end_date' ),
			'label' => esc_html__( 'End date:', 'grimlock-the-events-calendar' ),
			'value' => $instance['end_date'],
		);

		$this->date_picker( apply_filters( "{$this->id_base}_end_date_field_args", $args, $instance ) );
	}

	/**
	 * Add a checkbox field to set whether the event recurrences should be hidden
	 *
	 * @param array $instance
	 * @param WP_Widget $widget
	 * @since 1.0.0
	 */
	public function add_hide_recurrences_field( $instance, $widget ) {
		$args = array(
			'id'    => $widget->get_field_id( 'hide_recurrences' ),
			'name'  => $widget->get_field_name( 'hide_recurrences' ),
			'label' => esc_html__( 'Hide event recurrences', 'grimlock-the-events-calendar' ),
			'value' => $instance['hide_recurrences'],
		);

		$this->checkbox( apply_filters( "{$this->id_base}_hide_recurrences_field_args", $args, $instance ) );
	}

	/**
	 * Add a select to set the taxonomies for the query
	 *
	 * @param array $instance
	 * @param WP_Widget $widget
	 * @since 1.0.0
	 */
	public function add_taxonomies_field( $instance, $widget ) {
		$taxonomies_choices = array();
		$terms              = get_terms( array( 'taxonomy' => 'tribe_events_cat' ) );
		$terms_choices      = array();

		if ( ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				$terms_choices['tribe_events_cat|' . $term->slug] = $term->name;
			}
		}

		$taxonomies_choices['tribe_events_cat'] = array(
			'label'      => esc_html__( 'Categories', 'grimlock-the-events-calendar' ),
			'subchoices' => $terms_choices,
		);

		$args = array(
			'id'       => $widget->get_field_id( 'taxonomies' ),
			'name'     => $widget->get_field_name( 'taxonomies' ),
			'label'    => esc_html__( 'Taxonomies:', 'grimlock-the-events-calendar' ),
			'value'    => $instance['taxonomies'],
			'choices'  => $taxonomies_choices,
			'multiple' => true,
		);

		$this->select( apply_filters( "{$this->id_base}_taxonomies_field_args", $args, $instance ) );
	}

	/**
	 * Change the orderby field arguments
	 *
	 * @param array $args The field arguments
	 * @param array $instance The widget instance
	 *
	 * @return array The modified field arguments
	 */
	public function change_orderby_field_args( $args, $instance ) {
		$args['choices']['event_date'] = esc_html__( 'Event date', 'grimlock-the-events-calendar' );

		return $args;
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
			'title' => esc_html__( 'Events Calendar', 'grimlock-the-events-calendar' ),

			'event_date_displayed'     => true,
			'event_venue_displayed'    => false,
			'event_category_displayed' => false,
			'event_cost_displayed'     => false,

			'event_display'      => 'custom',
			'show_only_featured' => false,
			'start_date'         => '',
			'end_date'           => '',
			'hide_recurrences'   => true,
			'posts_per_page'     => '3',
			'orderby'            => 'menu_order',
			'order'              => 'ASC',
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
			'event_date_displayed'     => $instance['event_date_displayed'],
			'event_venue_displayed'    => $instance['event_venue_displayed'],
			'event_category_displayed' => $instance['event_category_displayed'],
			'event_cost_displayed'     => $instance['event_cost_displayed'],
		) );
	}

	/**
	 * @param $instance
	 *
	 * @return WP_Query
	 */
	public function make_query( $instance ) {
		$query_args = array(
			'post_type'           => 'tribe_events',
			'posts_per_page'      => $instance['posts_per_page'],
			'orderby'             => $instance['orderby'],
			'order'               => $instance['order'],
			'tribeHideRecurrence' => ! empty( $instance['hide_recurrences'] ),
			'eventDisplay'        => in_array( $instance['event_display'], array( 'list', 'upcoming' ) ) ? 'list' : $instance['event_display'],
			'ends_after'          => in_array( $instance['event_display'], array( 'list', 'upcoming' ) ) ? 'now' : null,
			'featured'            => $instance['show_only_featured'],
			'start_date'          => $instance['start_date'],
			'end_date'            => $instance['end_date'],
			'tax_query'           => array(),
		);

		if ( ! empty( $instance['taxonomies'] ) ) {
			$taxonomies_terms = array();
			foreach ( $instance['taxonomies'] as $term ) {
				$taxonomy_term = explode( '|', $term, 2 );
				if ( ! isset( $taxonomies_terms[ $taxonomy_term[0] ] ) ) {
					$taxonomies_terms[ $taxonomy_term[0] ] = array();
				}
				$taxonomies_terms[ $taxonomy_term[0] ][] = $taxonomy_term[1];
			}

			$tax_query = array();
			foreach ( $taxonomies_terms as $taxonomy => $terms ) {
				$tax_query[] = array(
					'taxonomy' => $taxonomy,
					'field'    => 'slug',
					'terms'    => $terms
				);
			}

			$query_args['tax_query'] = $tax_query;
		}

		if ( ! empty( $instance['meta_key'] ) ) {
			$query_args['meta_query'] = array(
					array(
							'key'     => $instance['meta_key'],
							'value'   => $instance['meta_value'],
							'compare' => $instance['meta_compare'],
							'type'    => empty( $instance['meta_value_num'] ) ? 'CHAR' : 'NUMERIC',
					),
			);
		}

		return tribe_get_events( $query_args, true );
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

		$instance['event_date_displayed']     = ! empty( $new_instance['event_date_displayed'] );
		$instance['event_venue_displayed']    = ! empty( $new_instance['event_venue_displayed'] );
		$instance['event_category_displayed'] = ! empty( $new_instance['event_category_displayed'] );
		$instance['event_cost_displayed']     = ! empty( $new_instance['event_cost_displayed'] );
		$instance['event_display']            = isset( $new_instance['event_display'] ) ? sanitize_text_field( $new_instance['event_display'] ) : '';
		$instance['show_only_featured']       = ! empty( $new_instance['show_only_featured'] );
		$instance['start_date']               = isset( $new_instance['start_date'] ) ? sanitize_text_field( $new_instance['start_date'] ) : '';
		$instance['end_date']                 = isset( $new_instance['end_date'] ) ? sanitize_text_field( $new_instance['end_date'] ) : '';
		$instance['hide_recurrences']         = ! empty( $new_instance['hide_recurrences'] );

		return $instance;
	}

	/**
	 * Get the widget classes
	 *
	 * @param array $instance Settings for the current widget instance.
	 *
	 * @return array The widget classes
	 */
	protected function get_classes( $instance ) {
		$classes   = parent::get_classes( $instance );
		$classes[] = "grimlock-query-section--the-events-calendar";
		return $classes;
	}
}

return new Grimlock_The_Events_Calendar_Tribe_Events_Section_Widget_Fields();
