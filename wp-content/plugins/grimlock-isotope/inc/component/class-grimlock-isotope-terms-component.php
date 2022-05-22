<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Grimlock_Isotope_Terms_Component
 *
 * @author  themosaurus
 * @since   1.0.0
 * @package grimlock-isotope
 */
class Grimlock_Isotope_Terms_Component extends Grimlock_Terms_Component {
	/**
	 * Render the current component with data.
	 *
	 * @since 1.0.0
	 */
	public function render() {
		// TODO: Consider removing extra filter to force use of displayed prop.
		$is_displayed = apply_filters( 'grimlock_isotope_terms_displayed', is_home() );
		if ( $is_displayed ) :
			$terms = get_terms( $this->props );

			// Change `post_tag` value to match `post_class()` output.
			$tax = 'post_tag' === $this->props['taxonomy'] ? 'tag' : $this->props['taxonomy'];

			if ( ! is_wp_error( $terms ) && is_array( $terms ) ) : ?>
				<ul <?php $this->render_id(); ?> <?php $this->render_class( array(
					'posts-filter',
					'nav',
					'nav-pills',
					'controls'
				) ); ?>>

					<?php
					if ( ! empty( $this->props['archive_link_label'] ) ): ?>
						<li class="nav-item">
							<a class="nav-link control <?php echo $is_displayed ? 'active' : ''; ?>" href="#" data-filter="*">
								<?php echo $this->props['archive_link_label']; ?>
							</a>
						</li>
						<?php
					endif;

					foreach ( $terms as $term ): ?>
						<li class="nav-item">
							<a class="nav-link control" href="#" data-filter="<?php echo ".{$tax}-{$term->slug}"; ?>"><?php echo $term->name ?></a>
						</li>
						<?php
					endforeach; ?>
				</ul>
				<?php
			endif;
		endif;
	}
}