<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class Grimlock_The_Events_Calendar_Tribe_Events_Component
 *
 * @author  themosaurus
 * @since   1.0.0
 * @package grimlock/inc/components
 */
class Grimlock_The_Events_Calendar_Tribe_Events_Component extends Grimlock_Component {
	/**
	 * Create a new Grimlock_Component instance.
	 *
	 * @param array $props Array of variables to be used within template.
	 */
	public function __construct( $props = array() ) {
		parent::__construct( wp_parse_args( $props, array(
			'post_thumbnail_displayed'  => true,
			'post_thumbnail_size'       => 'medium',
			'post_thumbnail_attr'       => array( 'class' => 'card-img' ),
			'post_content_displayed'    => false,
			'post_excerpt_displayed'    => true,
			'post_more_link_displayed'  => true,
			'event_date_displayed'      => true,
			'event_venue_displayed'     => false,
			'event_category_displayed'  => false,
			'event_cost_displayed'      => false,
		) ) );
	}

	/**
	 * Render the current component with props data on page.
	 *
	 * @since 1.0.0
	 */
	public function render() {
		/**
		 * Hook: grimlock_the_events_calendar_tribe_events_before_card_body
		 *
		 * @hooked grimlock_the_events_calendar_tribe_events_template - 10
		 */
		do_action( 'grimlock_the_events_calendar_tribe_events_template', $this->props );
	}
}
