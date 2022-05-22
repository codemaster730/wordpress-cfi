<?php
/**
 * PMPRO - Levels
 *
 * @package Paid Memberships Pro
 */

global $wpdb, $pmpro_msg, $pmpro_msgt, $current_user, $pmpro_level;

$pmpro_levels      = pmpro_getAllLevels( false, true );
$pmpro_level_order = pmpro_getOption( 'level_order' );

if ( ! empty( $pmpro_level_order ) ) {
	$level_order = explode( ',', $pmpro_level_order );

	// reorder array
	$reordered_levels = array();
	foreach ( $level_order as $level_id ) {
		foreach ( $pmpro_levels as $key => $level ) {
			if ( $level_id === $level->id ) {
				$reordered_levels[] = $pmpro_levels[ $key ];
			}
		}
	}

	$pmpro_levels = $reordered_levels;
}

$pmpro_levels = apply_filters( 'pmpro_levels_array', $pmpro_levels );

if ( $pmpro_msg ) {
	?>
<div class="pmpro_message <?php echo esc_attr( $pmpro_msgt ); ?>"><?php echo wp_kses_post( $pmpro_msg ); ?></div>
	<?php
}
?>

<?php if ( empty( $pmpro_levels ) ): ?>
	<div class="alert alert-info text-center">
		<?php esc_html_e( 'You must first create levels trough the Paid Memberships Pro settings', 'grimlock-paid-memberships-pro' ); ?>
	</div>
<?php endif; ?>

<div id="pmpro_levels_table" class="pmpro_checkout bg-transparent">
	<div class="row justify-content-center">
		<?php
		$count = 0;
		foreach ( $pmpro_levels as $level ) {
			if ( isset( $current_user->membership_level->ID ) ) {
				$current_level = ( $current_user->membership_level->ID === $level->id );
			} else {
				$current_level = false;
			}

			$count++;
			?>
		<div class="pmpro-levels-col col-md-6 col-lg <?php if ( 0 === $count % 2 ) { ?>odd<?php } ?><?php if ( $current_level === $level ) { ?> active<?php } ?>">

			<div class="card h-100 <?php if ( $current_level === $level ) { ?>border-primary<?php } ?>">

				<div class="card-body text-center pb-3 d-flex flex-column">

					<h2 class="text-center h3 mb-0 pb-0">
						<?php if ( $current_level ) : ?>
							<strong>
								<?php echo esc_html( $level->name ); ?>
							</strong>
						<?php else : ?>
							<?php echo esc_html( $level->name ); ?>
						<?php endif; ?>
					</h2>

					<?php
					$expiration_text = pmpro_getLevelExpiration( $level );
					if ( pmpro_isLevelFree( $level ) ) :
						$cost_text = '<strong>' . esc_html__( 'Free', 'paid-memberships-pro' ) . '</strong>';
					else :
						$cost_text = pmpro_getLevelCost( $level, true, true );
					endif;

					if ( ! empty( $cost_text ) ) : ?>

						<div class="level-short-price text-center py-3">
							<?php echo wp_kses_post( $cost_text ); ?>
						</div>

						<?php if ( ! empty( $level->description ) ) : ?>
							<div class="level-description mb-auto">
								<?php echo wp_kses_post( $level->description ); ?>
							</div>
						<?php endif; ?>

						<?php if ( ! empty( $expiration_text ) ) : ?>
							<div class="level-cost-expiration text-muted text-center small pt-3">
								<?php echo wp_kses_post( $expiration_text ); ?>
							</div>
						<?php endif; ?>

					<?php endif;?>

				</div>

				<div class="card-footer">
					<?php if ( empty( $current_user->membership_level->ID ) ) { ?>
						<a class="btn btn-lg btn-primary w-100" href="<?php echo esc_url( pmpro_url( 'checkout', '?level=' . $level->id, 'https' ) ); ?>"><?php esc_html_e( 'Select', 'paid-memberships-pro' ); ?></a>
					<?php } elseif ( ! $current_level ) { ?>
						<a class="btn btn-lg btn-primary w-100" href="<?php echo esc_url( pmpro_url( 'checkout', '?level=' . $level->id, 'https' ) ); ?>"><?php esc_html_e( 'Select', 'paid-memberships-pro' ); ?></a>
					<?php } elseif ( $current_level ) { ?>

						<?php
							// if it's a one-time-payment level, offer a link to renew
						if ( pmpro_isLevelExpiringSoon( $current_user->membership_level ) && $current_user->membership_level->allow_signups ) {
							?>
									<a class="btn btn-lg btn-primary w-100" href="<?php echo esc_url( pmpro_url( 'checkout', '?level=' . $level->id, 'https' ) ); ?>"><?php esc_html_e( 'Renew', 'paid-memberships-pro' ); ?></a>
								<?php
						} else {
							?>
									<a class="btn btn-lg btn-primary w-100 disabled" href="<?php echo esc_url( pmpro_url( 'account' ) ); ?>"><?php esc_html_e( 'Your&nbsp;Level', 'paid-memberships-pro' ); ?></a>
								<?php
						}
						?>

					<?php } ?>
				</div>

			</div>

		</div>
			<?php
		}
		?>
	</div>
</div>
<nav id="nav-below" class="navigation d-none" role="navigation">
	<div class="nav-previous alignleft">
		<?php if ( ! empty( $current_user->membership_level->ID ) ) { ?>
			<a href="<?php echo esc_url( pmpro_url( 'account' ) ); ?>" id="pmpro_levels-return-account"><?php esc_html_e( '&larr; Return to Your Account', 'paid-memberships-pro' ); ?></a>
		<?php } else { ?>
			<a href="<?php echo esc_url( home_url() ); ?>" id="pmpro_levels-return-home"><?php esc_html_e( '&larr; Return to Home', 'paid-memberships-pro' ); ?></a>
		<?php } ?>
	</div>
</nav>
