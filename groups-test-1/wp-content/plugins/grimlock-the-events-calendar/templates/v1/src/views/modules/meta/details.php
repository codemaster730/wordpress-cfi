<?php
/**
 * Single Event Meta (Details) Template
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe-events/modules/meta/details.php
 *
 * @package TribeEventsCalendar
 */

// @codingStandardsIgnoreFile
// Allow plugin text domain in theme.
$time_format          = get_option( 'time_format', Tribe__Date_Utils::TIMEFORMAT );
$time_range_separator = tribe_get_option( 'timeRangeSeparator', ' - ' );

$start_datetime       = tribe_get_start_date();
$start_date           = tribe_get_start_date( null, false );
$start_time           = tribe_get_start_date( null, false, $time_format );
$start_ts             = tribe_get_start_date( null, false, Tribe__Date_Utils::DBDATEFORMAT );

$end_datetime         = tribe_get_end_date();
$end_date             = tribe_get_display_end_date( null, false );
$end_time             = tribe_get_end_date( null, false, $time_format );
$end_ts               = tribe_get_end_date( null, false, Tribe__Date_Utils::DBDATEFORMAT );

$time_formatted = null;
if ( $start_time === $end_time ) {
	$time_formatted = $start_time;
} else {
	$time_formatted = $start_time . $time_range_separator . $end_time;
}

$event_id = Tribe__Main::post_id_helper();

/**
 * Returns a formatted time for a single event
 *
 * @var string Formatted time string
 * @var int Event post id
 */
$time_formatted = apply_filters( 'tribe_events_single_event_time_formatted', $time_formatted, $event_id );

/**
 * Returns the title of the "Time" section of event details
 *
 * @var string Time title
 * @var int Event post id
 */
$time_title = apply_filters( 'tribe_events_single_event_time_title', esc_html__( 'Time:', 'the-events-calendar' ), $event_id );

$cost    = tribe_get_formatted_cost();
$website = tribe_get_event_website_link();
?>

<div class="tribe-events-meta-group tribe-events-meta-group-details">

	<div class="row">

		<div class="tribe-events-meta-group-item col-12 col-md-4">

			<dl>

				<?php
				do_action( 'tribe_events_single_meta_details_section_start' );

				// All day (multiday) events.
				if ( tribe_event_is_all_day() && tribe_event_is_multiday() ) : ?>

					<dt> <?php esc_html_e( 'Start:', 'the-events-calendar' ); ?> </dt>
					<dd>
						<span class="tribe-events-span tribe-events-start-datetime published dtstart" title="<?php echo esc_attr( $start_ts ); ?>"> <?php echo esc_html( $start_date ); ?> </span>
					</dd>

					<dt> <?php esc_html_e( 'End:', 'the-events-calendar' ); ?> </dt>
					<dd>
						<span class="tribe-events-span dtend" title="<?php echo esc_attr( $end_ts ); ?>"> <?php echo esc_html( $end_date ); ?> </span>
					</dd>

					<?php // All day (single day) events.
				elseif ( tribe_event_is_all_day() ) : ?>

					<dt> <?php esc_html_e( 'Date:', 'the-events-calendar' ); ?> </dt>
					<dd>
						<span class="tribe-events-span tribe-events-start-datetime published dtstart" title="<?php echo esc_attr( $start_ts ); ?>"> <?php echo esc_html( $start_date ); ?> </span>
					</dd>

					<?php // Multiday events.
				elseif ( tribe_event_is_multiday() ) : ?>

					<dt> <?php esc_html_e( 'Start:', 'the-events-calendar' ); ?> </dt>
					<dd>
						<span class="tribe-events-span updated published dtstart" title="<?php echo esc_attr( $start_ts ); ?>"> <?php echo esc_html( $start_datetime ); ?> </span>
					</dd>

					<dt> <?php esc_html_e( 'End:', 'the-events-calendar' ); ?> </dt>
					<dd>
						<span class="tribe-events-span dtend" title="<?php echo esc_attr( $end_ts ); ?>"> <?php echo esc_html( $end_datetime ); ?> </span>
					</dd>

					<?php // Single day events.
				else : ?>

					<dt> <?php esc_html_e( 'Date:', 'the-events-calendar' ); ?> </dt>
					<dd>
						<span class="tribe-events-span tribe-events-start-date published dtstart" title="<?php echo esc_attr( $start_ts ); ?>"> <?php echo esc_html( $start_date ); ?> </span>
					</dd>

					<dt> <?php echo esc_html( $time_title ); ?> </dt>
					<dd>
						<div class="tribe-events-span tribe-events-start-time published dtstart" title="<?php echo esc_attr( $end_ts ); ?>">
							<?php echo wp_kses_post( $time_formatted ); ?>
						</div>
					</dd>

				<?php endif ?>

			</dl>

		</div> <!-- .tribe-events-meta-group-item -->

		<?php if ( ! empty( $cost ) ) : ?>
			<div class="tribe-events-meta-group-item col-12 col-md-4">
				<dl>
					<dt> <?php esc_html_e( 'Cost:', 'the-events-calendar' ); ?> </dt>
					<dd class="tribe-events-event-cost"> <?php echo esc_html( $cost ); ?> </dd>
				</dl>
			</div> <!-- .tribe-events-meta-group-item -->
		<?php endif ?>

		<?php if ( ! empty( $website ) ) : ?>
			<div class="tribe-events-meta-group-item col-12 col-md-4">
				<dl>
					<dt> <?php esc_html_e( 'Website:', 'the-events-calendar' ); ?> </dt>
					<dd class="tribe-events-event-url"> <?php
						echo wp_kses( $website, array(
							'a' => array(
								'href'   => true,
								'class'  => true,
								'target' => true,
							),
						) ); ?> </dd>
				</dl>
			</div> <!-- .tribe-events-meta-group-item -->
		<?php endif ?>

	</div> <!-- .row -->

	<?php do_action( 'tribe_events_single_meta_details_section_end' ); ?>
</div>

