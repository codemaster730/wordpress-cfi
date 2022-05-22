<?php
/**
 * BuddyPress - Home
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 * @version 3.0.0
 */

?>
<div id="buddypress">

	<div class="container container--narrow p-0">

		<div id="template-notices" role="alert" aria-atomic="true">
			<?php do_action( 'template_notices' ); ?>
		</div>

		<div class="activity no-ajax">
			<?php if ( bp_has_activities( 'display_comments=threaded&show_hidden=true&include=' . bp_current_action() ) ) : ?>
				<ul id="activity-stream" class="activity-list item-list grimlock-buddypress-activity-list">
					<?php while ( bp_activities() ) : bp_the_activity(); ?>
						<?php bp_get_template_part( 'activity/entry' ); ?>
					<?php endwhile; ?>
				</ul>
			<?php endif; ?>
		</div>

	</div>

</div>
