<?php
/**
 * Single Event Meta Template
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe-events/modules/meta.php
 *
 * @package TribeEventsCalendar
 */

// @codingStandardsIgnoreFile
// Allow plugin text domain in theme and unescaped template tags.

do_action( 'tribe_events_single_meta_before' );

// Do we want to group venue meta separately?
$set_venue_apart = apply_filters( 'tribe_events_single_event_the_meta_group_venue', false, get_the_ID() );

$event_tags = tribe_meta_event_tags( '<span></span>', ', ', false ); ?>

<?php if ( tribe_has_organizer() ) : ?>
	<div class="widget widget-organizer widget-organizers">
		<?php tribe_get_template_part( 'modules/meta/organizer' ); ?>
	</div>
<?php endif; ?>

<?php if ( ! empty( $event_tags ) ) : ?>
	<div class="widget widget-tags">
		<h3 class="widget-title"><?php esc_html_e( 'Event tags', 'the-events-calendar' ); ?></h3>
		<dl>
			<?php echo wp_kses_post( $event_tags ); ?>
		</dl>
	</div> <!-- .widget -->
<?php endif; ?>

<?php do_action( 'tribe_events_single_meta_after' );
