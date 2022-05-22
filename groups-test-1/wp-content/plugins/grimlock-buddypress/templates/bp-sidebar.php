<?php
/**
 * BuddyPress - Sidebar
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 * @version 3.0.0
 */

?>

<aside id="secondary-right" class="widget-area sidebar sidebar--buddypress col-md-12 col-lg-4 col-xl-3">
	<?php if ( is_active_sidebar( 'bp-sidebar' ) ) : ?>
		<?php dynamic_sidebar( 'bp-sidebar' ); ?>
	<?php endif; ?>
</aside><!-- #secondary-right -->
