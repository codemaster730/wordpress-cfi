<?php
/**
 * Statistics Content Part
 *
 * @package bbPress
 * @subpackage Theme
 */

// @codingStandardsIgnoreFile
// Allow plugin text domain in theme.

// Get the statistics.
$stats = bbp_get_statistics(); ?>

<?php do_action( 'bbp_before_statistics' ); ?>

	<div class="widget_display_stats">

		<div class="row stats_list">

			<?php if ( ! empty( $stats['user_count'] ) ) : ?>
				<div class="col-12 stats_list_item mb-2">
					<div class="bg-black-faded rounded-card p-2 w-100 h-100">
						<div class="row align-items-center">
							<div class="col-auto text-center d-flex pr-0 align-items-center justify-content-center">
								<i class="stats_list-icon stats_list-icon--users text-primary card"></i>
							</div>
							<div class="col">
								<h3 class="mb-0"><?php echo esc_html( $stats['user_count'] ); ?></h3>
								<h5 class="text-muted text-uppercase small font-weight-bold mb-0"><?php esc_html_e( 'Registered Users', 'bbpress' ); ?></h5>
							</div>
						</div>
					</div>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $stats['forum_count'] ) ) : ?>
				<div class="col-12 stats_list_item mb-2">
					<div class="bg-black-faded rounded-card p-2 w-100 h-100">
						<div class="row align-items-center">
							<div class="col-auto text-center d-flex pr-0 align-items-center justify-content-center">
								<i class="stats_list-icon stats_list-icon--forums text-primary card"></i>
							</div>
							<div class="col">
								<h3 class="mb-0"><?php echo esc_html( $stats['forum_count'] ); ?></h3>
								<h5 class="text-muted text-uppercase small font-weight-bold mb-0"><?php esc_html_e( 'Forums', 'bbpress' ); ?></h5>
							</div>
						</div>
					</div>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $stats['topic_count'] ) ) : ?>
				<div class="col-12 stats_list_item mb-2">
					<div class="bg-black-faded rounded-card p-2 w-100 h-100">
						<div class="row align-items-center">
							<div class="col-auto text-center d-flex pr-0 align-items-center justify-content-center">
								<i class="stats_list-icon stats_list-icon--topics text-primary card"></i>
							</div>
							<div class="col">
								<h3 class="mb-0"><?php echo esc_html( $stats['topic_count'] ); ?></h3>
								<h5 class="text-muted text-uppercase small font-weight-bold mb-0"><?php esc_html_e( 'Topics', 'bbpress' ); ?></h5>
							</div>
						</div>
					</div>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $stats['reply_count'] ) ) : ?>
				<div class="col-12 stats_list_item mb-2">
					<div class="bg-black-faded rounded-card p-2 w-100 h-100">
						<div class="row align-items-center">
							<div class="col-auto text-center d-flex pr-0 align-items-center justify-content-center">
								<i class="stats_list-icon stats_list-icon--replies text-primary card"></i>
							</div>
							<div class="col">
								<h3 class="mb-0"><?php echo esc_html( $stats['reply_count'] ); ?></h3>
								<h5 class="text-muted text-uppercase small font-weight-bold mb-0"><?php esc_html_e( 'Replies', 'bbpress' ); ?></h5>
							</div>
						</div>
					</div>
				</div>
			<?php endif; ?>

		</div><!-- .row -->

	</div><!-- .widget_display_stats -->

<?php do_action( 'bbp_after_statistics' ); ?>

<?php unset( $stats );
