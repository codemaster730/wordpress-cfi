<?php
/**
 * Grimlock The Events Calendar template functions.
 *
 * @package grimlock-the-events-calendar
 */

function grimlock_the_events_calendar_tribe_events_template( $args ) {
	?>
	<div class="card tribe-event__card">
		<?php
		/**
		 * Hook: grimlock_the_events_calendar_tribe_events_before_card_body
		 *
		 * @hooked grimlock_post_thumbnail - 10
		 */
		do_action( 'grimlock_the_events_calendar_tribe_events_before_card_body', $args ); ?>

		<div class="card-body">
			<?php
			/**
			 * Hook: grimlock_the_events_calendar_tribe_events_card_body
			 *
			 * @hooked grimlock_the_events_calendar_tribe_events_header  - 10
			 * @hooked grimlock_the_events_calendar_tribe_events_content - 20
			 * @hooked grimlock_the_events_calendar_tribe_events_footer  - 30
			 */
			do_action( 'grimlock_the_events_calendar_tribe_events_card_body', $args ); ?>
		</div><!-- .card-body -->

		<?php
		/**
		 * Hook: grimlock_the_events_calendar_tribe_events_card_body
		 */
		do_action( 'grimlock_the_events_calendar_tribe_events_after_card_body', $args ); ?>
	</div><!-- .card -->
	<?php
}

function grimlock_the_events_calendar_tribe_events_header( $args ) {
	?>
	<header class="entry-header clearfix">
		<?php
		/**
		 * Hook: grimlock_the_events_calendar_tribe_events_header
		 *
		 * @hooked grimlock_post_title                                  - 10
		 * @hooked grimlock_the_events_calendar_tribe_events_start_date - 20
		 */
		do_action( 'grimlock_the_events_calendar_tribe_events_header', $args ); ?>
	</header>
	<?php
}

/**
 * Prints HTML for the event date
 *
 * @param array $args The array of arguments from the component
 */
function grimlock_the_events_calendar_tribe_events_start_date( $args ) {
	if ( empty( $args['event_date_displayed'] ) ) {
		return;
	}

	?>
	<div class="event-date">
		<?php if ( function_exists( 'tribe_is_recurring_event' ) && tribe_is_recurring_event() ) :
			$event                    = get_post( get_the_ID() );
			$inner                    = '<span class="tribe-event-date-start">';
			$date_without_year_format = tribe_get_date_format();
			$date_with_year_format    = tribe_get_date_format( true );

			/**
			 * If a yearless date format should be preferred.
			 *
			 * By default, this will be true if the event starts and ends in the current year.
			 *
			 * @param bool    $use_yearless_format
			 * @param WP_Post $event
			 */
			$use_yearless_format = apply_filters( 'tribe_events_event_schedule_details_use_yearless_format',
				(
					tribe_get_start_date( $event, false, 'Y' ) === date_i18n( 'Y' )
					&& tribe_get_end_date( $event, false, 'Y' ) === date_i18n( 'Y' )
				),
				$event
			);

			$format = $use_yearless_format ? $date_without_year_format : $date_with_year_format;

			$inner      .= tribe_get_start_date( $event, false, $format );
			$recurrences = tribe_get_recurrence_start_dates();
			$last_date   = strtotime( end( $recurrences ) );
			$inner      .= ' <span class="event-date__separator">-</span> ';
			$inner      .= tribe_format_date( $last_date, false, $format );
			$inner      .= '</span>';

			/**
			 * Provides an opportunity to modify the *inner* schedule details HTML (ie before it is
			 * wrapped).
			 *
			 * @param string $inner_html  the output HTML
			 * @param int    $event_id    post ID of the event we are interested in
			 */
			echo apply_filters( 'tribe_events_event_schedule_details_inner', $inner, $event->ID );
		elseif ( function_exists( 'tribe_events_event_schedule_details' ) ) : ?>
			<?php echo tribe_events_event_schedule_details(); ?>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Prints HTML for the event venue
 *
 * @param array $args The array of arguments from the component
 */
function grimlock_the_events_calendar_tribe_events_venue( $args ) {
	if ( ! empty( $args['event_venue_displayed'] ) && tribe_has_venue( get_the_ID() ) ) : ?>
		<div class="event-venue">
			<span><?php echo tribe_get_venue_link( get_the_ID() ); ?></span>
		</div>
	<?php endif;
}

/**
 * Prints HTML for the event footer
 *
 * @param array $args The array of arguments from the component
 */
function grimlock_the_events_calendar_tribe_events_footer( $args ) {
	ob_start();

	/**
	 * Hook: grimlock_the_events_calendar_tribe_events_footer
	 *
	 */
	do_action( 'grimlock_the_events_calendar_tribe_events_footer', $args );

	$footer_content = ob_get_clean();

	if ( ! empty( trim( $footer_content ) ) ) : ?>
		<footer class="entry-footer clearfix">
			<?php echo $footer_content; ?>
		</footer><!-- .entry-footer -->
	<?php endif;
}

/**
 * Prints HTML for the event category
 *
 * @param array $args The array of arguments from the component
 */
function grimlock_the_events_calendar_tribe_events_category_list( $args ) {
	if ( ! empty( $args['event_category_displayed'] ) ) :
		printf(
			'<div class="event-cat-links cat-links">%1$s</div>',
			get_the_term_list( get_the_ID(), 'tribe_events_cat', '', ' ' )
		); // WPCS: XSS OK.
	endif;
}

/**
 * Prints HTML for the event cost
 *
 * @param array $args The array of arguments from the component
 */
function grimlock_the_events_calendar_tribe_events_cost( $args ) {
	if ( ! empty( $args['event_cost_displayed'] ) && ! empty( tribe_get_cost( get_the_ID() ) ) ) :?>
		<div class="event-cost">
			<span><?php echo tribe_get_formatted_cost( get_the_ID() ); ?></span>
		</div>
	<?php endif;
}

/**
 * Prints HTML for the category list in the custom header
 *
 * @param array $args The array of arguments from the component
 */
function grimlock_the_events_calendar_single_tribe_events_custom_header_category_list( $args ) {
	if ( ! empty( $args['single_tribe_events_category_displayed'] ) ) {
		printf(
			'<div class="event-cat-links cat-links">%1$s</div>',
			get_the_term_list( get_queried_object_id(), 'tribe_events_cat', '', ' ' )
		); // WPCS: XSS OK.
	}
}

/**
 * Prints HTML for the event date in the custom header
 *
 * @param array $args The array of arguments from the component
 */
function grimlock_the_events_calendar_single_tribe_events_custom_header_date( $args ) {
	if ( ! empty( $args['single_tribe_events_date_displayed'] ) ) { ?>
		<div class="event-date">
			<span><?php echo tribe_events_event_schedule_details( get_queried_object_id() ); ?></span>
		</div>
	<?php }
}

/**
 * Prints HTML for the event venue in the custom header
 *
 * @param array $args The array of arguments from the component
 */
function grimlock_the_events_calendar_single_tribe_events_custom_header_venue( $args ) {
	if ( ! empty( $args['single_tribe_events_venue_displayed'] ) && tribe_has_venue( get_queried_object_id() ) ) {
		?>
		<div class="event-venue">
			<span><?php echo tribe_get_venue_link( get_queried_object_id() ); ?></span>
		</div>
		<?php
	}
}

/**
 * Prints HTML for the event organizer in the custom header
 *
 * @param array $args The array of arguments from the component
 */
function grimlock_the_events_calendar_single_tribe_events_custom_header_organizer( $args ) {
	if ( ! empty( $args['single_tribe_events_organizer_displayed'] ) && tribe_has_organizer( get_queried_object_id() ) ) {
		?>
		<div class="event-organizer">
			<span><?php echo tribe_get_organizer_link( get_queried_object_id() ); ?></span>
		</div>
		<?php
	}
}

/**
 * Prints HTML for the event cost in the custom header
 *
 * @param array $args The array of arguments from the component
 */
function grimlock_the_events_calendar_single_tribe_events_custom_header_cost( $args ) {
	if ( ! empty( $args['single_tribe_events_cost_displayed'] ) && ! empty( tribe_get_cost( get_queried_object_id() ) ) ) {
		?>
		<div class="event-cost">
			<span><?php echo tribe_get_formatted_cost( get_queried_object_id() ); ?></span>
		</div>
		<?php
	}
}

/**
 * Prints HTML for the venue address in the custom header
 *
 * @param array $args The array of arguments from the component
 */
function grimlock_the_events_calendar_single_tribe_venue_custom_header_address( $args ) {
	if ( ! empty( $args['single_tribe_venue_address_displayed'] ) && ! empty( tribe_get_venue_single_line_address( get_queried_object_id() ) ) ) {
		?>
		<div class="venue-address">
			<span><?php echo tribe_get_venue_single_line_address( get_queried_object_id() ); ?></span>
		</div>
		<?php
	}
}

/**
 * Prints HTML for the venue phone in the custom header
 *
 * @param array $args The array of arguments from the component
 */
function grimlock_the_events_calendar_single_tribe_venue_custom_header_phone( $args ) {
	if ( ! empty( $args['single_tribe_venue_phone_displayed'] ) && ! empty( tribe_get_phone( get_queried_object_id() ) ) ) {
		?>
		<div class="venue-phone">
			<span><?php echo tribe_get_phone( get_queried_object_id() ); ?></span>
		</div>
		<?php
	}
}

/**
 * Prints HTML for the venue website in the custom header
 *
 * @param array $args The array of arguments from the component
 */
function grimlock_the_events_calendar_single_tribe_venue_custom_header_website( $args ) {
	if ( ! empty( $args['single_tribe_venue_website_displayed'] ) && ! empty( tribe_get_venue_website_link( get_queried_object_id() ) ) ) {
		?>
		<div class="venue-website">
			<span><?php echo tribe_get_venue_website_link( get_queried_object_id() ); ?></span>
		</div>
		<?php
	}
}

/**
 * Prints HTML for the organizer phone in the custom header
 *
 * @param array $args The array of arguments from the component
 */
function grimlock_the_events_calendar_single_tribe_organizer_custom_header_phone( $args ) {
	if ( ! empty( $args['single_tribe_organizer_phone_displayed'] ) && ! empty( tribe_get_organizer_phone( get_queried_object_id() ) ) ) {
		?>
		<div class="organizer-phone">
			<span><?php echo tribe_get_organizer_phone( get_queried_object_id() ); ?></span>
		</div>
		<?php
	}
}

/**
 * Prints HTML for the organizer website in the custom header
 *
 * @param array $args The array of arguments from the component
 */
function grimlock_the_events_calendar_single_tribe_organizer_custom_header_website( $args ) {
	if ( ! empty( $args['single_tribe_organizer_website_displayed'] ) && ! empty( tribe_get_organizer_website_link( get_queried_object_id() ) ) ) {
		?>
		<div class="organizer-website">
			<span><?php echo tribe_get_organizer_website_link( get_queried_object_id() ); ?></span>
		</div>
		<?php
	}
}

/**
 * Prints HTML for the organizer email in the custom header
 *
 * @param array $args The array of arguments from the component
 */
function grimlock_the_events_calendar_single_tribe_organizer_custom_header_email( $args ) {
	if ( ! empty( $args['single_tribe_organizer_email_displayed'] ) && ! empty( tribe_get_organizer_email( get_queried_object_id() ) ) ) {
		?>
		<div class="organizer-email">
			<span>
				<a href="<?php echo esc_url( 'mailto:' . tribe_get_organizer_email( get_queried_object_id() ) ); ?>">
					<?php echo tribe_get_organizer_email( get_queried_object_id() ); ?>
				</a>
			</span>
		</div>
		<?php
	}
}

/**
 * Prints HTML for the "back" button on single events
 *
 * @param array $args The array of arguments from the component
 */
function grimlock_the_events_calendar_custom_header_single_post_back( $args ) {
	if ( ! empty( $args['single_post_back_displayed'] ) ) : ?>
		<div class="single-post-back tribe-events-back">
			<a href="<?php echo esc_url( tribe_get_events_link() ); ?>">
				<?php
				/* translators: %s: Events plural label */
				printf( esc_html_x( 'All %s', '%s Events plural label', 'the-events-calendar' ), tribe_get_event_label_plural() ); ?>
			</a>
			<span class="single-post-back__active"><?php the_title(); ?></span>
		</div>
	<?php endif;
}
