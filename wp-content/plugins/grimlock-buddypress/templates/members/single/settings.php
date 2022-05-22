<?php
/**
 * BuddyPress - Users Settings
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 * @version 3.0.0
 */

// @codingStandardsIgnoreFile
// Allow plugin text domain in theme.
?>
	<div id="subnav" aria-label="<?php esc_attr_e( 'Member secondary navigation', 'buddypress' ); ?>" role="navigation" class="d-flex flex-column flex-lg-row mb-4 mt-0">

		<div class="item-list-tabs primary-list-tabs no-ajax">
			<ul class="item-list-tabs-ul clearfix">
				<?php if ( bp_core_can_edit_settings() ) : ?>
					<?php bp_get_options_nav(); ?>
				<?php endif; ?>
			</ul>
		</div>

	</div><!-- #subnav -->

<?php

switch ( bp_current_action() ) :
	case 'notifications':
		bp_get_template_part( 'members/single/settings/notifications'  );
		break;
	case 'capabilities':
		bp_get_template_part( 'members/single/settings/capabilities'   );
		break;
	case 'delete-account':
		bp_get_template_part( 'members/single/settings/delete-account' );
		break;
	case 'general':
		bp_get_template_part( 'members/single/settings/general'        );
		break;
	case 'profile':
		bp_get_template_part( 'members/single/settings/profile'        );
		break;
	case 'data':
		bp_get_template_part( 'members/single/settings/data'           );
		break;
	default:
		bp_get_template_part( 'members/single/plugins'                 );
		break;
endswitch;
