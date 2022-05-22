<?php
/**
 * BP Profile Search - filters template 'bps-filters'
 *
 * See http://dontdream.it/bp-profile-search/form-templates/ if you wish to modify this template or develop a new one.
 *
 * @package BuddyPress
 */

// @codingStandardsIgnoreFile
// Allow plugin text domain in theme.

$F = bps_escaped_filters_data ($version = '5.4');
if ( empty( $F->fields ) ) {
	return false;
}
?>
	<div class='bps_filters mb-4 p-3'>

		<a href='<?php echo esc_url( array_keys( $F->links )[0] ); ?>' class="bps_filters_reset" data-toggle="tooltip" data-placement="top" title="<?php esc_html_e( 'Reset Filters', 'grimlock-buddypress' ); ?>">
			<span class="d-none"><?php esc_html_e( 'Reset Filters', 'grimlock-buddypress' ); ?></span>
		</a>

		<div class="bps_filters__content">
			<?php
			foreach ( $F->fields as $f ) :
				$label = $f->label;
				$mode = $f->mode;
				$value = $f->value;
				$filter = $f->filter;

				?>
				<div class="bps-filters-item d-sm-inline-block mr-sm-3">
					<?php
					switch ( $filter ) {
						case 'range':
						case 'age_range':
							$min = __( 'min', 'bp-profile-search' );
							$max = __( 'max', 'bp-profile-search' );

							if ( $f->value['max'] === '' ) : ?>
								<strong><?php echo $label; ?></strong>
								<span><?php echo $min; ?>: <?php echo $value['min']; ?></span>
							<?php elseif ( $f->value['min'] === '' ) : ?>
								<strong><?php echo $label; ?></strong>
								<span><?php echo $max; ?>: <?php echo $value['max']; ?></span>
							<?php else : ?>
								<strong><?php echo $label; ?></strong>
								<span><?php echo $min; ?>: <?php echo $value['min']; ?>, <?php echo $max; ?>: <?php echo $value['max']; ?></span>
							<?php endif; ?>

							<?php break;

						case 'contains':
						case '':
						case 'like':
						case 'match_single':
							if ( ! empty ( $mode ) ) : ?>
								<strong><?php echo $label; ?></strong>
								<span><?php echo $mode; ?>: <?php echo $value; ?></span>
							<?php else : ?>
								<strong><?php echo $label; ?></strong>
								<span><?php echo $value; ?></span>
							<?php endif;
							break;

						case 'distance':
							$units = $value['units'];
							$distance = $value['distance'];
							$location = $value['location'];
							if ( $units == 'km' ) : ?>
								<strong><?php echo $label; ?></strong>
								<span><?php printf( __( 'is within: %1$s km of %2$s', 'bp-profile-search' ), $distance, $location ); ?></span>
							<?php else : ?>
								<strong><?php echo $label; ?></strong>
								<span><?php printf( __( 'is within: %1$s miles of %2$s', 'bp-profile-search' ), $distance, $location ); ?></span>
							<?php endif;
							break;

						case 'one_of':
						case 'match_any':
						case 'match_all':
							$values = implode( ', ', $value );
							if ( ! empty ( $mode ) && count( $value ) > 1 ) : ?>
								<strong><?php echo $label; ?></strong>
								<span><?php echo $mode; ?>: <?php echo $values; ?></span>
							<?php else : ?>
								<strong><?php echo $label; ?></strong>
								<span><?php echo $values; ?></span>
							<?php endif;
							break;

						default:
							$output = apply_filters( 'bps_filters_template_field', 'none', $f );
							if ( $output != 'none' ) : ?>
								<strong><?php echo $label; ?></strong>
								<span><?php echo $output; ?></span>
							<?php else : ?>
								<strong><?php echo $label; ?></strong>
								<span><?php printf( 'BP Profile Search: undefined filter <em>%s</em>', $filter ); ?></span>
							<?php endif;
							break;
					}
					?>
				</div>
			<?php endforeach; ?>

		</div>
	</div>
<?php

// BP Profile Search - end of template
