<?php
/**
 * BuddyPress - Sidebar Left
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 * @version 3.0.0
 */

?>

<aside id="secondary-right" class="widget-area sidebar sidebar--buddypress col-md-12 col-lg-3">
	<?php if ( is_active_sidebar( 'bp-sidebar-1' ) ) : ?>
		<?php dynamic_sidebar( 'bp-sidebar-1' ); ?>
	<?php endif; ?>
</aside><!-- #secondary-right -->
