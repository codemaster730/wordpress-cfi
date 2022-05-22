<?php
/*
 * BP Profile Search - field value template 'bps-field-value'
 *
 * See http://dontdream.it/bp-profile-search/form-templates/ if you wish to modify this template or develop a new one.
 * A new or modified template should be moved to the 'buddypress/members' directory in your theme's root, otherwise it
 * will be overwritten during the next plugin update.
 *
 */

	list ($name, $value, $label) = bps_template_args();
?>
	<div class="item-meta">
		<span class="activity bps-value">
			<?php esc_html_e($value); ?>
		</span>
	</div>
<?php

// BP Profile Search - end of template
