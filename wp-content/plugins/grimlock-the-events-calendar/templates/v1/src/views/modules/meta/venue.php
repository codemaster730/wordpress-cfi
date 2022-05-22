<?php
/**
 * Single Event Meta (Venue) Template
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe-events/modules/meta/venue.php
 *
 * @package TribeEventsCalendar
 */

// @codingStandardsIgnoreFile
// Allow plugin text domain in theme.
if ( ! tribe_get_venue_id() ) {
	return;
}

$phone = tribe_get_phone();

// @codingStandardsIgnoreStart
$website = tribe_get_venue_website_link();
// @codingStandardsIgnoreEnd ?>

<h3 class="widget-title"> <?php esc_html_e( 'Venue', 'the-events-calendar' ); ?> </h3>

<div class="tribe-events-meta-group tribe-events-meta-group-venue">

	<div class="bg-black-faded mb-2 p-3 rounded-card">
		<?php do_action( 'tribe_events_single_meta_venue_section_start' ); ?>
		<h5><a href="<?php echo esc_url( get_the_permalink( tribe_get_venue_id() ) ); ?>"><span class="tribe-venue-name"><?php echo get_the_title( tribe_get_venue_id() ); ?></span></a></h5>
		<?php if ( tribe_address_exists() ) : ?>
			<div class="tribe-venue-location small m-0">
				<address class="tribe-events-address m-0">
					<?php
					// @codingStandardsIgnoreStart
					echo tribe_get_full_address(); ?>
					<?php if ( ! empty( $phone ) ) : ?>
						<div class="tribe-venue-tel"><?php echo esc_html( $phone ); ?></div>
					<?php endif; ?>
					<?php if ( ! empty( $website ) ) : ?>
						<div class="url">
							<?php
							echo wp_kses( $website, array(
								'a' => array(
									'href'   => true,
									'class'  => true,
									'target' => true,
								),
							) ); ?>
						</div>
					<?php endif; ?>
					<?php if ( tribe_show_google_map_link() ) : ?>
						<hr />
						<div><?php echo tribe_get_map_link_html(); ?></div>
					<?php endif;
					// @codingStandardsIgnoreEnd ?>
				</address>
			</div>
		<?php endif; ?>

		<?php do_action( 'tribe_events_single_meta_venue_section_end' ); ?>
	</div>
</div>
