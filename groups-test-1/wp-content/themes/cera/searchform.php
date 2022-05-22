<?php
/**
 * The template for displaying search forms
 *
 * @package cera
 */

?>

<form role="search" method="get" class="search-form form-inline" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<div class="form-group">
		<label class="sr-only">
			<span class="screen-reader-text sr-only"><?php esc_html_e( 'Search everything...', 'cera' ); ?></span>
		</label>
		<input type="search" class="search-field form-control" placeholder="<?php esc_attr_e( 'Search everything...', 'cera' ); ?>"  title="<?php esc_attr_e( 'Search for:', 'cera' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" name="s"/>
		<button type="submit" class="search-submit"><i class="fa fa-search"></i></button>
	</div>
</form><!-- .search-form -->
