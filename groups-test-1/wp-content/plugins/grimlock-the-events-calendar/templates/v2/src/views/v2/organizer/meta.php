<?php
/**
 * View: Organizer meta
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/organizer/meta.php
 *
 * See more documentation about our views templating system.
 *
 * @link http://m.tri.be/1aiy
 *
 * @version 5.0.0
 *
 * @var WP_Post $organizer The organizer post object.
 *
 */

$classes = [ 'tribe-events-pro-organizer__meta' ];

$content = tribe_get_the_content( null, false, $organizer->ID );
$url     = tribe_get_organizer_website_url( $organizer->ID );
$email   = tribe_get_organizer_email( $organizer->ID );
$phone   = tribe_get_organizer_phone( $organizer->ID );

$has_content = ! empty( $content );
$has_details = ! empty( $url ) || ! empty( $email ) || ! empty( $phone );
?>
<div <?php tribe_classes( $classes ); ?>>

	<div class="row align-items-md-center">

		<div class="tribe-events-pro-organizer__meta-image-column col-12 col-sm-auto">

			<div class="tribe-events-pro-organizer__meta-image">

				<?php if ( has_post_thumbnail( $organizer ) ) : ?>

					<?php
					$post_thumbnail_attrs = array( 'class' => 'img-fluid rounded-circle d-inline-block' );
					echo get_the_post_thumbnail( $organizer, array( 80, 80 ), $post_thumbnail_attrs );
					?>

				<?php else : ?>

					<img class="img-fluid rounded-circle d-inline-block" width="80" height="80" src="<?php echo esc_url( apply_filters( 'grimlock_the_events_calendar_organizer_default_avatar', GRIMLOCK_THE_EVENTS_CALENDAR_PLUGIN_DIR_URL . 'assets/images/default-avatar.png' ) ); ?>" alt="<?php esc_html_e( 'avatar', 'the-events-calendar' ); ?>" />

				<?php endif; ?>

			</div>

		</div>

		<div class="col-12 col-sm pl-0 pl-sm-4 pt-3 pt-sm-0">

			<?php $this->template( 'organizer/meta/title', [ 'organizer' => $organizer ] ); ?>

			<?php if ( $has_details ) : ?>

				<?php $this->template( 'organizer/meta/details', [ 'organizer' => $organizer, 'has_details' => $has_details ] ); ?>

			<?php endif; ?>

		</div>

	</div>

	<?php if ( $has_content ) : ?>

		<?php $this->template( 'organizer/meta/content', [ 'organizer' => $organizer ] ); ?>

	<?php endif; ?>

</div>
