<?php
/**
 * Grimlock BuddyPress template functions for BuddyPress media (Rtmedia).
 *
 * @package grimlock-buddypress
 */

if ( ! function_exists( 'grimlock_buddypress_buddypress_media_member_featured_media' ) ) :
	/**
	 * Display user media.
	 *
	 * @since 1.3.5
	 */
	function grimlock_buddypress_buddypress_media_member_featured_media() {
		if ( $user_id = bp_displayed_user_id() ) :
			$model   = new RTMediaModel();
			$results = $model->get( array( 'media_author' => $user_id, 'context' => 'profile', 'media_type' => 'photo' ), 0, 4 );
			if ( $results && !is_rtmedia_gallery() ) : ?>

			<div class="col-12 col-lg align-items-end profile-header__featured-media-col order-10 order-md-1 p-0 m-0 mx-md-2">
				<div class="rtmedia-activity-container profile-header__featured-media">

					<ul class="rtmedia-list row flex-lg-column justify-content-center justify-content-md-start">
						<?php foreach ( $results as $image ): ?>
							<li class="rtmedia-list-item col col-sm-2 col-md-2 col-lg-12" id="<?php echo $image->id; ?>">
								<div class="rtmedia-media p-2 p-lg-0" id="rtmedia-media-<?php echo $image->id; ?>">
									<a href="<?php echo get_rtmedia_permalink( $image->id ); ?>" title="<?php echo $image->media_title; ?>">
										<img src="<?php rtmedia_image( "rt_media_thumbnail", $image->id ); ?>" alt="<?php echo rtmedia_image_alt( $image->id ); ?>" />
									</a>
								</div> <!-- .rtmedia-media -->
							</li> <!-- .rtmedia-list-item-->
						<?php endforeach; ?>
					</ul> <!-- .rtmedia-list-->

					<?php if ( count( $results ) > 3 ) : ?>
						<div class="rtmedia-items-more d-none d-lg-block">
							<?php if ( bp_is_my_profile() ): ?>
								<a class="rtmedia-items-more__add" href="<?php echo esc_url( bp_get_displayed_user_link() . RTMEDIA_MEDIA_SLUG ); ?>" data-toggle="tooltip" data-placement="top" title="<?php esc_html_e( 'Add new media','grimlock-buddypress' ); ?>"></a>
							<?php else: ?>
								<a class="rtmedia-items-more__view" href="<?php echo esc_url( bp_get_displayed_user_link() . RTMEDIA_MEDIA_SLUG ); ?>" data-toggle="tooltip" data-placement="top" title="<?php esc_html_e( 'View all media','grimlock-buddypress' ); ?>" ></a>
							<?php endif; ?>
						</div> <!-- .rtmedia-items-more -->
					<?php endif; ?>

				</div> <!-- .rtmedia-activity-container-->
			</div> <!-- .col -->

			<?php
			endif; ?>
		<?php
		endif;
	}
endif;
