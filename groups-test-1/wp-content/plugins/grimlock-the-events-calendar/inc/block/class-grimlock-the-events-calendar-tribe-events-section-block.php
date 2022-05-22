<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Grimlock_Tribe_Events_Section_Block
 *
 * @author  themosaurus
 * @since   1.1.5
 * @package grimlock-the-events-calendar/inc
 */
class Grimlock_Tribe_Events_Section_Block extends Grimlock_Query_Section_Block {

	/**
	 * Setup class.
	 *
	 * @param string $type Block type
	 * @param string $domain Block domain
	 *
	 * @since 1.1.5
	 */
	public function __construct( $type = 'tribe-events-section', $domain = 'grimlock-the-events-calendar' ) {
		parent::__construct( $type, $domain );

		// General Panel
		add_filter( "{$this->id_base}_general_panel_fields",   array( $this, 'add_separator'                      ), 240 );
		add_filter( "{$this->id_base}_general_panel_fields",   array( $this, 'add_event_date_displayed_field'     ), 240 );
		add_filter( "{$this->id_base}_general_panel_fields",   array( $this, 'add_event_venue_displayed_field'    ), 250 );
		add_filter( "{$this->id_base}_general_panel_fields",   array( $this, 'add_event_category_displayed_field' ), 260 );
		add_filter( "{$this->id_base}_general_panel_fields",   array( $this, 'add_event_cost_displayed_field'     ), 270 );

		// Query Panel
		remove_filter( "{$this->id_base}_query_panel_fields",  array( $this, 'add_post_type_field'                ), 100 );
		remove_filter( "{$this->id_base}_query_panel_fields",  array( $this, 'add_handpick_posts_field'           ), 110 );
		remove_filter( "{$this->id_base}_query_panel_fields",  array( $this, 'add_posts_field'                    ), 120 );
		add_filter( "{$this->id_base}_query_panel_fields",     array( $this, 'add_event_display_field'            ), 112 );
		add_filter( "{$this->id_base}_query_panel_fields",     array( $this, 'add_show_only_featured_field'       ), 114 );
		add_filter( "{$this->id_base}_query_panel_fields",     array( $this, 'add_start_date_field'               ), 116 );
		add_filter( "{$this->id_base}_query_panel_fields",     array( $this, 'add_end_date_field'                 ), 118 );

		if ( class_exists( 'Tribe__Events__Pro__Main' ) ) {
			add_action( "{$this->id_base}_query_panel_fields", array( $this, 'add_hide_recurrences_field'         ), 119 );
		}

		add_filter( "{$this->id_base}_orderby_field_args",     array( $this, 'change_orderby_field_args'          ), 10  );
	}

	/**
	 * Get block args used for JS registering of the block
	 *
	 * @return array Array of block args
	 */
	public function get_block_js_args() {
		return array(
			'title'    => __( 'Grimlock Events Section', 'grimlock-the-events-calendar' ),
			'icon' => array(
				'foreground'=> '#000000',
				'src' => 'calendar-alt',
			),
			'category' => 'widgets',
			'keywords' => array( __( 'query', 'grimlock-the-events-calendar' ), __( 'section', 'grimlock-the-events-calendar' ), __( 'events', 'grimlock-the-events-calendar' ) ),
			'supports' => array(
				'html'   => false,
				'align'  => array( 'wide', 'full' ),
				'anchor' => true,
			),
		);
	}

	/**
	 * Add a checkbox field to set whether the event date is displayed in the block
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_event_date_displayed_field( $fields ) {
		$fields[] = $this->toggle_field( apply_filters( "{$this->id_base}_event_date_displayed_field_args", array(
			'name'  => 'event_date_displayed',
			'label' => esc_html__( 'Display date', 'grimlock-the-events-calendar' ),
		) ) );

		return $fields;
	}

	/**
	 * Add a checkbox field to set whether the event venue is displayed in the block
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_event_venue_displayed_field( $fields ) {
		$fields[] = $this->toggle_field( apply_filters( "{$this->id_base}_event_venue_displayed_field_args", array(
			'name'  => 'event_venue_displayed',
			'label' => esc_html__( 'Display venue', 'grimlock-the-events-calendar' ),
		) ) );

		return $fields;
	}

	/**
	 * Add a checkbox field to set whether the event category is displayed in the block
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_event_category_displayed_field( $fields ) {
		$fields[] = $this->toggle_field( apply_filters( "{$this->id_base}_event_category_displayed_field_args", array(
			'name'  => 'event_category_displayed',
			'label' => esc_html__( 'Display category', 'grimlock-the-events-calendar' ),
		) ) );

		return $fields;
	}

	/**
	 * Add a checkbox field to set whether the event cost is displayed in the block
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_event_cost_displayed_field( $fields ) {
		$fields[] = $this->toggle_field( apply_filters( "{$this->id_base}_event_cost_displayed_field_args", array(
			'name'  => 'event_cost_displayed',
			'label' => esc_html__( 'Display cost', 'grimlock-the-events-calendar' ),
		) ) );

		return $fields;
	}

	/**
	 * Add a select to set what events need to be displayed in the query
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_taxonomies_field( $fields ) {
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

		$fields[] = $this->select_field( apply_filters( "{$this->id_base}_taxonomies_field_args", array(
			'name'     => 'taxonomies',
			'label'    => esc_html__( 'Categories', 'grimlock-the-events-calendar' ),
			'choices'  => $taxonomies_choices,
			'multiple' => true,
		) ) );

		return $fields;
	}

	/**
	 * Add a select to set what events need to be displayed in the query
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_event_display_field( $fields ) {
		$fields[] = $this->select_field( apply_filters( "{$this->id_base}_event_display_field_args", array(
			'name'  => 'event_display',
			'label' => esc_html__( 'Events display', 'grimlock-the-events-calendar' ),
			'choices' => array(
				'custom'   => esc_html__( 'All events', 'grimlock-the-events-calendar' ),
				'upcoming' => esc_html__( 'Only upcoming events', 'grimlock-the-events-calendar' ),
				'past'     => esc_html__( 'Only past events', 'grimlock-the-events-calendar' ),
			),
		) ) );

		return $fields;
	}

	/**
	 * Add a checkbox field to set whether the query should only return featured events
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_show_only_featured_field( $fields ) {
		$fields[] = $this->toggle_field( apply_filters( "{$this->id_base}_show_only_featured_field_args", array(
			'name'  => 'show_only_featured',
			'label' => esc_html__( 'Show only featured events', 'grimlock-the-events-calendar' ),
		) ) );

		return $fields;
	}

	/**
	 * Add a date field to set the start date for the query
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_start_date_field( $fields ) {
		$fields[] = $this->date_field( apply_filters( "{$this->id_base}_start_date_field_args", array(
			'name'  => 'start_date',
			'label' => esc_html__( 'Start date', 'grimlock-the-events-calendar' ),
		) ) );

		return $fields;
	}

	/**
	 * Add a date field to set the end date for the query
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_end_date_field( $fields ) {
		$fields[] = $this->date_field( apply_filters( "{$this->id_base}_end_date_field_args", array(
			'name'  => 'end_date',
			'label' => esc_html__( 'End date', 'grimlock-the-events-calendar' ),
		) ) );

		return $fields;
	}

	/**
	 * Add a toggle field to set whether the event recurrences should be hidden
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_hide_recurrences_field( $fields ) {
		$fields[] = $this->toggle_field( apply_filters( "{$this->id_base}_hide_recurrences_field_args", array(
			'name'  => 'hide_recurrences',
			'label' => esc_html__( 'Hide event recurrences', 'grimlock-the-events-calendar' ),
		) ) );

		return $fields;
	}

	/**
	 * Change the orderby field arguments
	 *
	 * @param array $args The field arguments
	 *
	 * @return array The modified field arguments
	 */
	public function change_orderby_field_args( $args ) {
		$args['choices']['event_date'] = esc_html__( 'Event date', 'grimlock-the-events-calendar' );

		return $args;
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

		$attributes['event_date_displayed']     = filter_var( $new_attributes['event_date_displayed'], FILTER_VALIDATE_BOOLEAN );
		$attributes['event_venue_displayed']    = filter_var( $new_attributes['event_venue_displayed'], FILTER_VALIDATE_BOOLEAN );
		$attributes['event_category_displayed'] = filter_var( $new_attributes['event_category_displayed'], FILTER_VALIDATE_BOOLEAN );
		$attributes['event_cost_displayed']     = filter_var( $new_attributes['event_cost_displayed'], FILTER_VALIDATE_BOOLEAN );
		$attributes['event_display']            = isset( $new_attributes['event_display'] ) ? sanitize_text_field( $new_attributes['event_display'] ) : '';
		$attributes['show_only_featured']       = filter_var( $new_attributes['show_only_featured'], FILTER_VALIDATE_BOOLEAN );
		$attributes['start_date']               = isset( $new_attributes['start_date'] ) ? sanitize_text_field( $new_attributes['start_date'] ) : '';
		$attributes['end_date']                 = isset( $new_attributes['end_date'] ) ? sanitize_text_field( $new_attributes['end_date'] ) : '';
		$attributes['hide_recurrences']         = isset( $new_attributes['hide_recurrences'] ) ? filter_var( $new_attributes['hide_recurrences'], FILTER_VALIDATE_BOOLEAN ) : false;

		return $attributes;
	}

	/**
	 * @param $attributes
	 *
	 * @return WP_Query
	 */
	protected function make_query( $attributes ) {
		$query_args = array(
			'post_type'           => 'tribe_events',
			'posts_per_page'      => $attributes['posts_per_page'],
			'orderby'             => $attributes['orderby'],
			'order'               => $attributes['order'],
			'tribeHideRecurrence' => ! empty( $attributes['hide_recurrences'] ),
			'eventDisplay'        => $attributes['event_display'],
			'featured'            => ! empty( $attributes['show_only_featured'] ),
			'start_date'          => $attributes['start_date'],
			'end_date'            => $attributes['end_date'],
			'tax_query'           => array(),
		);

		if ( ! empty( $attributes['taxonomies'] ) ) {
			$taxonomies_terms = array();
			foreach ( $attributes['taxonomies'] as $term ) {
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

		if ( ! empty( $attributes['meta_key'] ) ) {
			$query_args['meta_query'] = array(
				array(
					'key'     => $attributes['meta_key'],
					'value'   => $attributes['meta_value'],
					'compare' => $attributes['meta_compare'],
					'type'    => empty( $attributes['meta_value_num'] ) ? 'CHAR' : 'NUMERIC',
				),
			);
		}

		return tribe_get_events( $query_args, true );
	}

	/**
	 * Get default field values for the block
	 *
	 * @return array Array of default field values
	 */
	public function get_defaults() {
		$defaults = parent::get_defaults();

		return array_merge( $defaults, array(
			'title' => esc_html__( 'Events calendar', 'grimlock-the-events-calendar' ),

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
		do_action( 'grimlock_query_section', apply_filters( "{$this->id_base}_component_args", $this->get_component_args( $attributes ), $attributes ) );
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
			'event_date_displayed'     => $attributes['event_date_displayed'],
			'event_venue_displayed'    => $attributes['event_venue_displayed'],
			'event_category_displayed' => $attributes['event_category_displayed'],
			'event_cost_displayed'     => $attributes['event_cost_displayed'],
		) );
	}
}

return new Grimlock_Tribe_Events_Section_Block();
