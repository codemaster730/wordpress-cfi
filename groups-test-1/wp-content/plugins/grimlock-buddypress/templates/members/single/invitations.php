<?php
/**
 * BuddyPress - membership invitations
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 * @version 8.0.0
 */

?>

<div class="d-flex flex-column flex-lg-row mb-4 mt-0" id="subnav" aria-label="<?php esc_attr_e( 'Member secondary navigation', 'buddypress' ); ?>" role="navigation">
	<div class="item-list-tabs primary-list-tabs no-ajax">
		<ul>
			<?php bp_get_options_nav(); ?>
		</ul>
	</div>
</div>

<?php
switch ( bp_current_action() ) :

	case 'send-invites' :
		bp_get_template_part( 'members/single/invitations/send-invites' );
		break;

	case 'list-invites' :
	default :
		bp_get_template_part( 'members/single/invitations/list-invites' );
		break;

endswitch;

