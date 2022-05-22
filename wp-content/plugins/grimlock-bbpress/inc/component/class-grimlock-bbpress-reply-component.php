<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Grimlock_bbPress_Reply_Component
 *
 * @author  themosaurus
 * @since   1.0.0
 * @package grimlock-bbpress
 */
class Grimlock_bbPress_Reply_Component extends Grimlock_Component {
	/**
	 * Create a new Grimlock_Component instance.
	 *
	 * @param array $props Array of variables to be used within template.
	 */
	public function __construct( $props = array() ) {
		parent::__construct( wp_parse_args( $props, array(
			'reply_author_displayed'    => true,
			'reply_date_displayed'      => true,
			'reply_permalink_displayed' => true,
			'reply_content_displayed'   => true,
			'reply_more_link_displayed' => false,
		) ) );
	}

	/**
	 * Render the current component with props data on page.
	 *
	 * @since 1.0.0
	 */
	public function render() {
		?>
		<div class="card reply__card">
			<div class="card-body">
				<header class="entry-header clearfix">
					<?php
					do_action( 'grimlock_bbpress_reply_title' ); ?>
					<div class="entry-meta">
						<?php
						if ( true == $this->props['reply_date_displayed'] ) :
							do_action( 'grimlock_bbpress_reply_date' );
						endif;

						if ( true == $this->props['reply_permalink_displayed'] ) :
							do_action( 'grimlock_bbpress_reply_permalink' );
						endif;

						if ( true == $this->props['reply_author_displayed'] ) :
							do_action( 'grimlock_bbpress_reply_author' );
						endif; ?>
					</div><!-- .entry-meta -->
				</header><!-- .entry-header -->

				<?php
				$more_link_text = '';
				if ( true == $this->props['reply_more_link_displayed'] ) :
					$more_link_text = apply_filters( 'grimlock_bbpress_reply_more_link_text', $more_link_text );
				endif;

				if ( true == $this->props['reply_content_displayed'] ) : ?>
					<div class="entry-content clearfix">
						<?php
						do_action( 'grimlock_bbpress_reply_content', $more_link_text ); ?>
					</div><!-- .entry-content -->
				<?php
				endif; ?>

				<footer class="entry-footer clearfix">
					<?php
					if ( get_edit_post_link() ) :
						edit_post_link(
							sprintf(
							/* translators: %s: Name of current post */
								esc_html__( 'Edit %s', 'grimlock-bbpress' ),
								the_title( '<span class="screen-reader-text sr-only">"', '"</span>', false )
							),
							'<span class="edit-link">',
							'</span>'
						);
					endif; ?>
				</footer><!-- .entry-footer -->
			</div><!-- .card-body -->
		</div><!-- .card -->
		<?php
	}
}
