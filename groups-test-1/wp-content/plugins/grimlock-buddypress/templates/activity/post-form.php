<?php
/**
 * BuddyPress - Activity Post Form
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 * @version 3.0.0
 */

// @codingStandardsIgnoreFile
// Allow plugin text domain in theme.
?>
<form action="<?php bp_activity_post_form_action(); ?>" method="post" id="whats-new-form" name="whats-new-form" class="card card-static">

	<?php do_action( 'bp_before_activity_post_form' ); ?>

	<div class="whats-new-form-content d-flex">

		<div id="whats-new-avatar" class="d-none d-md-flex align-self-start">

			<a href="<?php echo esc_url( bp_loggedin_user_domain() ); ?>">
				<?php bp_loggedin_user_avatar(); ?>
			</a>

		</div> <!-- #whats-new-avatar -->

		<div class="media-body">

			<?php $active_class = empty( $_GET['r'] ) ? '' : 'active'; ?>

			<div id="whats-new-content" class="<?php echo esc_attr( $active_class ); ?>">

				<div id="whats-new-textarea" class="<?php echo esc_attr( $active_class ); ?>">
					<?php
					/* translators: %s: User name */
					$placeholder = sprintf( esc_html__( "What's new, %s?", 'buddypress' ), bp_get_user_firstname( bp_get_loggedin_user_fullname() ) );

					if ( bp_is_group() ) :
						/* translators: 1: The group name, 2: The user name */
						$placeholder = sprintf( esc_html__( "What's new in %s, %s?", 'buddypress' ), bp_get_group_name(), bp_get_user_firstname( bp_get_loggedin_user_fullname() ) );
					endif; ?>
                    <textarea class="bp-suggestions" name="whats-new" id="whats-new" cols="50" rows="10" placeholder="<?php echo esc_attr( $placeholder ); ?>" <?php echo bp_is_group() ? 'data-suggestions-group-id="' . esc_attr( (int) bp_get_current_group_id() ) . '"' : ''; ?>><?php if ( isset( $_GET['r'] ) ) : ?>@<?php echo esc_textarea( wp_unslash( $_GET['r'] ) ); ?> <?php endif; ?></textarea>
				</div>

				<div id="whats-new-options" class="element-animated fade-in short">

					<?php if ( bp_is_active( 'groups' ) && ! bp_is_my_profile() && ! bp_is_group() ) : ?>

						<div id="whats-new-post-in-box">
							<select id="whats-new-post-in" name="whats-new-post-in">
								<option selected="selected" value="0"><?php esc_html_e( 'My Profile', 'buddypress' ); ?></option>

								<?php if ( bp_has_groups( 'user_id=' . bp_loggedin_user_id() . '&type=alphabetical&max=100&per_page=100&populate_extras=0&update_meta_cache=0' ) ) :
									while ( bp_groups() ) : bp_the_group(); ?>

										<option value="<?php bp_group_id(); ?>"><?php bp_group_name(); ?></option>

									<?php endwhile;
								endif; ?>

							</select>
						</div>
						<input type="hidden" id="whats-new-post-object" name="whats-new-post-object" value="groups" />

					<?php elseif ( bp_is_group_activity() ) : ?>

						<input type="hidden" id="whats-new-post-object" name="whats-new-post-object" value="groups" />
						<input type="hidden" id="whats-new-post-in" name="whats-new-post-in" value="<?php bp_group_id(); ?>" />

					<?php endif; ?>

					<div id="whats-new-submit">
						<input type="submit" name="aw-whats-new-submit" id="aw-whats-new-submit" class="mr-0" value="<?php esc_attr_e( 'Post Update', 'buddypress' ); ?>" />
					</div>

					<div class="clear"></div>

					<?php do_action( 'bp_activity_post_form_options' ); ?>

				</div><!-- #whats-new-options -->

			</div><!-- #whats-new-content -->

		</div> <!-- .media-body -->

	</div> <!-- .media -->

	<?php wp_nonce_field( 'post_update', '_wpnonce_post_update' ); ?>
	<?php do_action( 'bp_after_activity_post_form' ); ?>

</form> <!-- #whats-new-form -->
