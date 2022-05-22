<?php
/**
 * Grimlock Jetpack template functions.
 *
 * @package grimlock-jetpack
 */

function grimlock_jetpack_jetpack_testimonial_template( $args ) {
	?>
	<div class="card jetpack-testimonial__card">
		<?php
		/**
		 * Hook: grimlock_jetpack_jetpack_testimonial_before_card_body
		 *
		 * @hooked grimlock_post_thumbnail - 10
		 */
		do_action( 'grimlock_jetpack_jetpack_testimonial_before_card_body', $args ); ?>

		<div class="card-body">
			<?php
			/**
			 * Hook: grimlock_jetpack_jetpack_testimonial_card_body
			 *
			 * @hooked grimlock_jetpack_jetpack_testimonial_header  - 10
			 * @hooked grimlock_jetpack_jetpack_testimonial_content - 20
			 * @hooked grimlock_jetpack_jetpack_testimonial_footer  - 30
			 */
			do_action( 'grimlock_jetpack_jetpack_testimonial_card_body', $args ); ?>
		</div><!-- .card-body -->

		<?php
		/**
		 * Hook: grimlock_jetpack_jetpack_testimonial_card_body
		 */
		do_action( 'grimlock_jetpack_jetpack_testimonial_after_card_body', $args ); ?>
	</div><!-- .card -->
	<?php
}

function grimlock_jetpack_jetpack_testimonial_header( $args ) {
	?>
	<header class="entry-header clearfix">
		<?php
		/**
		 * Hook: grimlock_jetpack_jetpack_testimonial_header
		 */
		do_action( 'grimlock_jetpack_jetpack_testimonial_header', $args ); ?>
	</header>
	<?php
}

function grimlock_jetpack_jetpack_testimonial_excerpt( $args ) { ?>
	<div class="entry-summary clearfix">
		<?php the_excerpt(); ?>
	</div>

<?php }

/**
 * Prints HTML for the testimonial footer
 *
 * @param array $args The array of arguments from the component
 */
function grimlock_jetpack_jetpack_testimonial_footer( $args ) {
	ob_start();

	/**
	 * Hook: grimlock_jetpack_jetpack_testimonial_footer
	 *
	 */
	do_action( 'grimlock_jetpack_jetpack_testimonial_footer', $args );

	$footer_content = ob_get_clean();

	if ( ! empty( trim( $footer_content ) ) ) : ?>
		<footer class="entry-testimonial-author clearfix">
			<?php echo $footer_content; ?>
		</footer><!-- .entry-footer -->
	<?php endif;
}
