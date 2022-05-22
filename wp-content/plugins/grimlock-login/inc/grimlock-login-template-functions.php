<?php
/**
 * Grimlock Login template functions.
 *
 * @package grimlock-login
 */

/**
 * Display register and login links for the navbar
 *
 * @param array $args The array of arguments from the component
 */
function grimlock_login_navbar_nav_menu_login_register_buttons( $args ) {
	if ( ! is_user_logged_in() ) : ?>

		<ul class="<?php echo esc_attr( join( ' ', $args['class'] ) ); ?> <?php echo 'modal' === $args['login_button_action'] ? 'grimlock-login--button-action-modal' : esc_attr( $args['login_button_action'] ); ?>">
			<li class="menu-item menu-item--login <?php echo $args['login_button_action'] === 'dropdown' ? 'menu-item-has-children' : ''; ?>">
				<?php switch ( $args['login_button_action'] ) {
					case 'modal':
						?>
						<button type="button" class="btn btn-outline-primary" data-target="#grimlock-login-form-modal" data-toggle="modal"><?php esc_html_e( 'Login', 'grimlock-login' ); ?></button>
						<?php
						break;
					case 'dropdown':
						?>
						<a href="#" class="btn btn-outline-primary"><?php esc_html_e( 'Login', 'grimlock-login' ); ?></a>
						<ul class="sub-menu grimlock-login-dropdown">
							<li class="menu-item">
								<?php wp_login_form( array( 'hide_register' => true ) ); ?>
							</li>
						</ul>
						<?php
						break;
					case 'inline':
						?>
						<div id="grimlock-login-form-inline">
							<?php wp_login_form( array( 'hide_register'  => true ) ); ?>
						</div>
						<?php
						break;
					case 'link':
					default:
						?>
						<a href="<?php echo esc_url( wp_login_url() ); ?>" class="btn btn-outline-primary"><?php esc_html_e( 'Login', 'grimlock-login' ); ?></a>
						<?php
						break;
				} ?>
			</li>
			<?php if ( ! empty( get_option( 'users_can_register', false ) ) ) : ?>
				<li class="menu-item menu-item--register">
					<a href="<?php echo esc_url( wp_registration_url() ); ?>" class="btn btn-primary"><?php esc_html_e( 'Register', 'grimlock-login' ); ?></a>
				</li>
			<?php endif; ?>
		</ul>

	<?php endif;
}

/**
 * Display the login form in a modal
 *
 * @param array $args The array of arguments from the component
 */
function grimlock_login_form_modal( $args ) {
	if ( ! is_user_logged_in() ) :
		$custom_logo_url = $args['custom_logo'];

		// If there's no login custom logo, try to fallback on the site logo
		if ( empty( $custom_logo_url ) ) {
			$custom_logo = wp_get_attachment_image_src( get_theme_mod( 'custom_logo' ), 'full' );

			if ( is_array( $custom_logo ) ) {
				$custom_logo_url = $custom_logo[0];
			}
		} ?>

		<div class="modal fade" id="grimlock-login-form-modal" tabindex="-1" role="dialog" aria-labelledby="grimlock-login-form-modal-title" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
					<div class="modal-header">

						<h5 class="modal-title" id="grimlock-login-form-modal-title">
							<?php if ( ! empty( $args['custom_logo_displayed'] ) && ! empty( $custom_logo_url ) ) : ?>
								<img class="grimlock-login-form-modal-logo" src="<?php echo esc_url( $custom_logo_url ); ?>" alt="Login Form Logo" />
							<?php endif; ?>
						</h5>

						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>

					</div>
					<div class="modal-body">
						<?php wp_login_form(); ?>
					</div>
				</div>
			</div>
		</div>

	<?php endif;
}
