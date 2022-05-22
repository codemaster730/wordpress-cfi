<?php

/**
 * Class Grimlock_Term_Query_Section_Component
 *
 * @author  themosaurus
 * @since   1.0.0
 * @package grimlock-query/inc/components
 */
class Grimlock_Term_Query_Section_Component extends Grimlock_Section_Component {
	/**
	 * Setup class.
	 *
	 * @param array $props
	 * @since 1.0.0
	 */
	public function __construct( $props = array() ) {
		parent::__construct( wp_parse_args( $props, array(
			'term_thumbnail_size' => 'large',
			'terms_layout'        => '12-cols-classic',
		) ) );
	}

	/**
	 * Retrieve the classes for the component as an array.
	 *
	 * @since 1.0.0
	 *
	 * @param string|array $class One or more classes to add to the class list.
	 * @return array Array of classes.
	 */
	public function get_class( $class = '' ) {
		$classes   = parent::get_class( $class );
		$classes[] = 'grimlock-term-query-section';
		return array_unique( $classes );
	}

	/**
	 * Retrieve the classes for the term query as an array.
	 *
	 * @since 1.0.0
	 *
	 * @param  string|array $class One or more classes to add to the class list.
	 * @return array Array of classes.
	 */
	public function get_terms_class( $class = '' ) {
		$classes   = $this->parse_array( $class );
		$classes[] = 'grimlock-term-query-section__terms';
		$classes[] = 'grimlock-terms';
		$classes[] = 'terms';
		$classes[] = 'grimlock-terms--height-equalized';
		$classes[] = 'terms--height-equalized';
		$classes[] = "grimlock-terms--{$this->props['terms_layout']}";
		$classes[] = "terms--{$this->props['terms_layout']}";
		return array_unique( $classes );
	}

	/**
	 * Display the classes for the query posts.
	 *
	 * @since 1.0.0
	 *
	 * @param string|array $class One or more classes to add to the class list.
	 */
	public function render_terms_class( $class = '' ) {
		$classes = $this->get_terms_class( $class );
		$this->output_class( $classes );
	}

	/**
	 * Display the current component content.
	 *
	 * @since 1.0.0
	 */
	protected function render_content() {
		if ( $this->is_displayed() ) : ?>
			<div class="grimlock-section__content section__content">
				<?php
				$has_query = isset( $this->props['query'] ) && $this->props['query'] instanceof WP_Term_Query;
				if ( $has_query ) : ?>
					<div <?php $this->render_terms_class(); ?>>
						<?php
						foreach ( $this->props['query']->get_terms() as $term ) : ?>
							<article id="term-<?php echo esc_attr( uniqid() ); ?>" class="term term-<?php echo esc_attr( $term->term_id ); ?> term--<?php echo esc_attr( $term->taxonomy ); ?>">
								<?php
								$props = array_merge ( (array) $term, array(
									'term_thumbnail_size' => $this->props['term_thumbnail_size'],
								) );

								if ( has_action( "grimlock_term_query_{$term->taxonomy}" ) ) :
									do_action( "grimlock_term_query_{$term->taxonomy}", $props );
								else :
									do_action( 'grimlock_term_query_category', $props );
								endif; ?>
							</article><!-- #term-## -->
						<?php
						endforeach; ?>
					</div><!-- .grimlock-term-query-section__posts.posts -->
				<?php
				endif; ?>
			</div><!-- .section__content -->
		<?php endif;
	}
}