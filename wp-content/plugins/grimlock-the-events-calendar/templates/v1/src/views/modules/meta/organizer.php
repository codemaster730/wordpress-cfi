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

				<div class="m-0 p-3 text-center">

					<div class="tribe-organizer-img mb-2">
						<a href="<?php echo esc_url( get_the_permalink( $organizer ) ); ?>">
							<?php if ( has_post_thumbnail( $organizer ) ) : ?>
								<?php
								$post_thumbnail_attrs = array(
									'class' => 'img-fluid rounded-circle d-inline-block',
								);
								echo get_the_post_thumbnail( $organizer, array( 60, 60 ), $post_thumbnail_attrs ); ?>
							<?php else : ?>
								<img class="img-fluid rounded-circle d-inline-block" src="<?php echo esc_url( apply_filters( 'grimlock_the_events_calendar_organizer_default_avatar', GRIMLOCK_THE_EVENTS_CALENDAR_PLUGIN_DIR_URL . 'assets/images/default-avatar.png' ) ); ?>" alt="<?php esc_html_e( 'avatar', 'the-events-calendar' ); ?>" />
							<?php endif; ?>
						</a>
					</div> <!-- .tribe-organizer-img -->

					<div class="tribe-organizer-body">

						<h6 class="mb-0 tribe-organizer-title">
							<a href="<?php echo esc_url( get_the_permalink( $organizer ) ); ?>">
								<?php echo get_the_title( $organizer ); ?>
							</a>
						</h6>

						<?php if ( ! empty( $label ) ) : ?>
							<span class="badge badge-primary tribe-organizer-badge mt-2 rounded-button"><?php
								// @codingStandardsIgnoreStart
								echo tribe_get_organizer_label( $organizer );
								// @codingStandardsIgnoreEnd ?></span>
						<?php endif; ?>

						<?php if ( ! $multiple ) : ?>
							<?php if ( ! empty( $organizer_content ) ) : ?>
								<div class="mt-2 tribe-organizer-content">
									<?php echo wp_kses_post( $organizer_content ); ?>
								</div>
							<?php endif; ?>
						<?php endif; ?>

					</div> <!-- .tribe-organizer-body -->

				</div> <!-- .media -->

				<?php
				if ( ! $multiple ) : ?>
					<div class="bg-black-faded rounded-card p-3 text-center">
						<?php
						if ( ! empty( $phone ) ) : ?>
							<span class="tribe-organizer-tel"><?php echo esc_html( $phone ); ?></span>
							<?php
						endif;

						if ( ! empty( $email ) ) : ?>
							<span class="tribe-organizer-email"><?php echo esc_html( $email ); ?></span>
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
