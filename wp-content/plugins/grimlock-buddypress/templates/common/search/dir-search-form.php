<?php
/**
 * Output the search form markup.
 *
 * @since 2.7.0
 * @version 3.0.0
 */

$query_arg = bp_core_get_component_search_query_arg();

$value = '';
if ( $query_arg && ! empty( $_REQUEST[ $query_arg ] ) ) {
	$value = wp_unslash( $_REQUEST[ $query_arg ] );
}

?>

<div id="<?php echo esc_attr( bp_current_component() ); ?>-dir-search" class="dir-search" role="search">
	<form action="" method="get" id="search-<?php echo esc_attr( bp_current_component() ); ?>-form">
		<label for="<?php bp_search_input_name(); ?>" class="bp-screen-reader-text"><?php bp_search_placeholder(); ?></label>
		<input type="text" name="<?php echo esc_attr( bp_core_get_component_search_query_arg() ); ?>" id="<?php bp_search_input_name(); ?>" value="<?php echo esc_attr( $value ); ?>" placeholder="<?php bp_search_default_text(); ?>" />

		<input type="submit" id="<?php echo esc_attr( bp_get_search_input_name() ); ?>_submit" name="<?php bp_search_input_name(); ?>_submit" value="<?php esc_attr_e( 'Search', 'buddypress' ); ?>" />
	</form>
</div><!-- #<?php echo esc_attr( bp_current_component() ); ?>-dir-search -->
