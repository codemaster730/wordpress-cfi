<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Grimlock_Terms_Component
 *
 * @author  themosaurus
 * @since   1.0.0
 * @package grimlock/inc/components
 */
class Grimlock_Terms_Component extends Grimlock_Component {
	/**
	 * Create a new Grimlock_Component instance.
	 *
	 * @param array $props Array of variables to be used within template.
	 */
	public function __construct( $props = array() ) {
		parent::__construct( wp_parse_args( $props, array(
			'taxonomy'           => 'category',
			'archive_link'       => get_post_type_archive_link( 'post' ),
			'archive_link_label' => esc_html__( 'All categories', 'grimlock' ),
		) ) );
	}

	/**
	 * Get the current query var for the taxonomy term.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function get_query_var() {
		switch ( $this->props['taxonomy'] ) {
			case 'category':
				return get_query_var( 'category_name' );

			case 'post_tag':
				return get_query_var( 'tag' );

			default:
				return get_query_var( 'term' );
		}
	}

	/**
	 * Render the current component with data.
	 *
	 * @since 1.0.0
	 */
	public function render() {
		if ( $this->is_displayed() ) :
			$terms = get_terms( $this->props );

			if ( ! is_wp_error( $terms ) && is_array( $terms ) ) : ?>
				<ul <?php $this->render_id(); ?> <?php $this->render_class( array(
					'grimlock-posts-filter',
					'posts-filter',
					'nav',
					'nav-pills'
				) ); ?>>

					<?php
					if ( ! empty( $this->props['archive_link'] ) && ! empty( $this->props['archive_link_label'] ) ): ?>
						<li class="nav-item">
							<a class="nav-link <?php echo is_home() ? 'active' : ''; ?>" href="<?php echo $this->props['archive_link']; ?>">
								<?php echo $this->props['archive_link_label']; ?>
							</a>
						</li>
					<?php
					endif;

					foreach ( $terms as $term ): ?>
						<li class="nav-item">
							<a class="nav-link <?php echo $this->get_query_var() === $term->slug ? 'active' : '' ?>"
							   href="<?php echo get_term_link( $term ); ?>">
								<?php echo $term->name; ?>
							</a>
						</li>
					<?php
					endforeach; ?>
				</ul>
			<?php endif;
		endif;
	}
}
