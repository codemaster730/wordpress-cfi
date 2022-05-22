<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Grimlock_bbPress_Topic_Component
 *
 * @author  themosaurus
 * @since   1.0.0
 * @package grimlock-bbpress
 */
class Grimlock_bbPress_Topic_Component extends Grimlock_Component {
	/**
	 * Create a new Grimlock_Component instance.
	 *
	 * @param array $props Array of variables to be used within template.
	 */
	public function __construct( $props = array() ) {
		parent::__construct( wp_parse_args( $props, array(
			'topic_started_by_displayed'  => true,
			'topic_voice_count_displayed' => true,
			'topic_reply_count_displayed' => true,
			'topic_freshness_displayed'   => true,
			'topic_more_link_displayed'   => false,
		) ) );
	}

	/**
	 * Render the current component with props data on page.
	 *
	 * @since 1.0.0
	 */
	public function render() {
		?>
		<div class="card topic__card">
			<div class="card-body">
				<header class="entry-header clearfix">
					<?php
					do_action( 'grimlock_bbpress_topic_title' ); ?>
					<div class="entry-meta">
						<?php
						if ( true == $this->props['topic_started_by_displayed'] ) :
							do_action( 'grimlock_bbpress_topic_started_by' );
						endif;

						if ( true == $this->props['topic_voice_count_displayed'] ) :
							do_action( 'grimlock_bbpress_topic_voice_count' );
						endif;

						if ( true == $this->props['topic_reply_count_displayed'] ) :
							do_action( 'grimlock_bbpress_topic_reply_count' );
						endif;

						if ( true == $this->props['topic_freshness_displayed'] ) :
							do_action( 'grimlock_bbpress_topic_freshness' );
						endif; ?>
					</div><!-- .entry-meta -->
				</header><!-- .entry-header -->

				<?php
				$more_link_text = '';
				if ( true == $this->props['topic_more_link_displayed'] ) :
					$more_link_text = apply_filters( 'grimlock_bbpress_topic_more_link_text', $more_link_text );
					do_action( 'grimlock_bbpress_topic_more_link', $more_link_text );
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
