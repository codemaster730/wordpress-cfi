<?php
/**
 * BuddyPress - Users Notifications
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
            <?php bp_get_options_nav(); ?>
        </ul>
    </div>
    <div id="members-order-select" class="last filter ml-md-auto">
        <div class="dir-filter">
            <div class="select-style">
                <?php bp_notifications_sort_order_form(); ?>
            </div>
        </div>
    </div>
</div>

<?php
switch ( bp_current_action() ) :

	case 'unread' :
		bp_get_template_part( 'members/single/notifications/unread' );
		break;

	case 'read' :
		bp_get_template_part( 'members/single/notifications/read' );
		break;

	// Any other actions.
	default :
		bp_get_template_part( 'members/single/plugins' );
		break;
endswitch;
