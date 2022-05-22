<?php
/**
 * View: Venue meta
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/venue/meta.php
 *
 * See more documentation about our views templating system.
 *
 * @link http://m.tri.be/1aiy
 *
 * @version 5.0.1
 *
 * @var WP_Post $venue The venue post object.
 * @var bool $enable_maps Boolean on whether or not maps are enabled.
 * @var bool $show_map Boolean on whether or not to show map for this venue.
 *
 */

$classes = [ 'tribe-events-pro-venue__meta' ];

if ( $enable_maps && $show_map ) {
	$classes[] = 'tribe-events-pro-venue__meta--has-map';
}
?>

<div <?php tribe_classes( $classes ); ?>>

	<div class="tribe-events-pro-venue__meta-row tribe-common-g-row <?php if ( has_post_thumbnail() ) : ?>tribe-events-pro-venue__meta--has-image<?php endif; ?>">

		<div class="tribe-events-pro-venue__meta-data tribe-common-g-col">

			<?php if ( $enable_maps && $show_map ) : ?>

				<?php if ( has_post_thumbnail( $venue ) ) : ?>

					<div class="tribe-events-pro-venue__meta-image-inside">

						<?php echo get_the_post_thumbnail($venue, 'large'); ?>

					</div>

				<?php endif; ?>

			<?php endif; ?>

			<?php $this->template( 'venue/meta/title', [ 'venue' => $venue ] ); ?>

			<?php $this->template( 'venue/meta/details', [ 'venue' => $venue ] ); ?>

			<?php $this->template( 'venue/meta/content', [ 'venue' => $venue ] ); ?>

		</div>

		<?php if ( $enable_maps && $show_map ) : ?>

			<div class="tribe-events-pro-venue__meta-map tribe-common-g-col">
				<?php $this->template( 'venue/meta/map', [ 'venue' => $venue ] ); ?>
			</div>

		<?php else: ?>

			<?php if ( has_post_thumbnail( $venue ) ) : ?>

				<div class="tribe-events-pro-venue__meta-image tribe-common-g-col">
					<?php echo get_the_post_thumbnail($venue, 'large'); ?>
				</div>

			<?php endif; ?>

		<?php endif; ?>

	</div>

</div>
