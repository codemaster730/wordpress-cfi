<?php
/**
 * Single Event Meta (Organizer) Template
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe-events/modules/meta/organizer.php
 *
 * @package TribeEventsCalendar
 * @version 4.4
 */

// @codingStandardsIgnoreFile
// Allow plugin text domain in theme.
$organizer_ids = tribe_get_organizer_ids();
$multiple      = count( $organizer_ids ) > 1;
$phone         = tribe_get_organizer_phone();
$email         = tribe_get_organizer_email();
$website       = tribe_get_organizer_website_link();
$label         = tribe_get_organizer_label(); ?>

<h3 class="widget-title"><?php echo esc_html( tribe_get_organizer_label( ! $multiple ) ); ?></h3>

<div class="tribe-events-meta-group tribe-events-meta-group-organizer p-0">

	<?php
	do_action( 'tribe_events_single_meta_organizer_section_start' ); ?>

	<div class="tribe-organizers">

		<?php foreach ( $organizer_ids as $organizer ) :
			if ( ! $organizer ) :
				continue;
			endif;

			$organizer_post    = get_post( $organizer );
			$organizer_content = apply_filters( 'the_content', $organizer_post->post_content ); ?>

			<div class="tribe-organizer <?php if ( ! $multiple ) : ?>tribe-organizer-one<?php endif; ?>">

				<div class="tribe-organizer-person">

					<div class="tribe-organizer-img mb-2">
						<?php if ( class_exists( 'Tribe__Events__Pro__Main' ) ) : ?>
							<a href="<?php echo esc_url( get_the_permalink( $organizer ) ); ?>">
						<?php endif; ?>

							<?php if ( has_post_thumbnail( $organizer ) ) : ?>
								<?php
								$post_thumbnail_attrs = array(
									'class' => 'img-fluid rounded-circle d-inline-block',
								);
								echo get_the_post_thumbnail( $organizer, array( 50, 50 ), $post_thumbnail_attrs ); ?>
							<?php else : ?>
								<img class="img-fluid rounded-circle d-inline-block" width="50" height=50" src="<?php echo esc_url( apply_filters( 'grimlock_the_events_calendar_organizer_default_avatar', GRIMLOCK_THE_EVENTS_CALENDAR_PLUGIN_DIR_URL . 'assets/images/default-avatar.png' ) ); ?>" alt="<?php esc_html_e( 'avatar', 'the-events-calendar' ); ?>" />
							<?php endif; ?>

						<?php if ( class_exists( 'Tribe__Events__Pro__Main' ) ) : ?>
							</a>
						<?php endif; ?>
					</div> <!-- .tribe-organizer-img -->

					<div class="tribe-organizer-body">

						<h6 class="mb-0 tribe-organizer-title">
							<?php if ( class_exists( 'Tribe__Events__Pro__Main' ) ) : ?>
								<a href="<?php echo esc_url( get_the_permalink( $organizer ) ); ?>">
							<?php endif; ?>

								<?php echo get_the_title( $organizer ); ?>

							<?php if ( class_exists( 'Tribe__Events__Pro__Main' ) ) : ?>
								</a>
							<?php endif; ?>
						</h6>

						<?php if ( ! $multiple ) : ?>
							<?php if ( ! empty( $organizer_content ) ) : ?>
								<div class="mt-2 tribe-organizer-content">
									<?php echo wp_kses_post( $organizer_content ); ?>
								</div>
							<?php endif; ?>
						<?php endif; ?>

					</div> <!-- .tribe-organizer-body -->

				</div>

				<?php
				if ( ! $multiple ) : ?>
					<div class="bg-black-faded rounded-card p-2 tribe-organizer-meta mt-3 text-left">
						<?php
						if ( ! empty( $phone ) ) : ?>
							<div class="tribe-organizer-tel"><?php echo esc_html( $phone ); ?></div>
							<?php
						endif;

						if ( ! empty( $email ) ) : ?>
							<div class="tribe-organizer-email"><?php echo esc_html( $email ); ?></div>
							<?php
						endif;

						if ( ! empty( $website ) ) : ?>
							<div class="tribe-organizer-url">
								<?php
								echo wp_kses( $website, array(
									'a' => array(
										'href'   => true,
										'class'  => true,
										'target' => true,
									),
								) ); ?>
							</div>
							<?php
						endif; ?>
					</div> <!-- .card-footer -->
				<?php
				endif; ?>

			</div> <!-- .tribe-organizer -->
			<?php
		endforeach; ?>

	</div>

	<?php do_action( 'tribe_events_single_meta_organizer_section_end' ); ?>

</div>
