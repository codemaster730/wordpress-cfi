<?php
/**
 * Template functions for Grimlock for Author Avatars List
 *
 * @package grimlock-author-avatars/inc
 */

/**
 * Display the template for the user list.
 *
 * @since 1.0.0
 *
 * @param $default string The default template for the user list.
 *
 * @return string The template for the user list.
 */
function grimlock_author_avatars_userlist_template( $default ) {
	return '<ol class="grimlock-author-avatars__author-list bp-card-list bp-card-list--members author-list">{users}</ol>';
}

/**
 * Display the template for the user.
 *
 * @since 1.0.0
 *
 * @param $default string The default template for the user.
 *
 * @return string The template for the user.
 */
function grimlock_author_avatars_user_template( $default ) {
	return '<li class="grimlock-author-avatars__user bp-card-list__item bp-card-list--members__item has-post-thumbnail"><div class="card"><div class="ov-h">{user}</div></div></li>';
}

/**
 * Prints the HTML for the BP member xprofile fields.
 *
 * @since 1.0.5
 *
 * @param string $html The sprintf template.
 * @param string $name The value (users name) passed into the span.
 * @param object $user The user object.
 *
 * @return string The extra HTML for the user.
 */
function grimlock_author_avatars_user_name_template( $html, $name, $user ) {
	if ( has_action( 'grimlock_buddypress_member_xprofile_custom_fields' ) ) :
		ob_start(); ?>
		<div class="bp-member-xprofile-custom-fields">
			<?php do_action( 'grimlock_buddypress_member_xprofile_custom_fields', $user->user_id ); ?>
		</div>
		<?php $html .= ob_get_clean();
	endif;

	return $html;
}
