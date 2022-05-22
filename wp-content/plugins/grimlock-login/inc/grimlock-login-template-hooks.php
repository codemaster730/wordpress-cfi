<?php
/**
 * Grimlock Login template hooks
 *
 * @package grimlock-login
 */

/**
 * Navbar Nav Menu hooks.
 *
 * @see grimlock_login_navbar_nav_menu_login_register_buttons
 *
 * @since 1.0.3
 */
add_action( 'grimlock_login_navbar_nav_menu_template', 'grimlock_login_navbar_nav_menu_login_register_buttons', 10 );

/**
 * Login Form Modal Hooks
 *
 * @see grimlock_login_navbar_nav_menu_login_register_buttons
 *
 * @since 1.0.3
 */
add_action( 'grimlock_login_form_modal_template', 'grimlock_login_form_modal', 10 );
