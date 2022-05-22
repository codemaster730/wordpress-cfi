<?php
defined( 'ABSPATH' ) || exit;

class BP_Better_Messages_Mini_List
{

    public static function instance()
    {

        // Store the instance locally to avoid private static replication
        static $instance = null;

        // Only run these methods if they haven't been run previously
        if ( null === $instance ) {
            $instance = new BP_Better_Messages_Mini_List;
            $instance->setup_actions();
        }

        // Always return the instance
        return $instance;

        // The last metroid is in captivity. The galaxy is at peace.
    }

    public function setup_actions()
    {
        add_action('wp_footer', array( $this, 'html' ), 199);
    }


    public function html(){
        if( ! is_user_logged_in() ) return false;

        if (defined('WP_DEBUG') && true === WP_DEBUG) {
            // some debug to add later
        } else {
            error_reporting(0);
        }

        $user_id = get_current_user_id();
        $user    = get_userdata( $user_id );
        $roles   = $user->roles;

        $tabs = array();
        if(BP_Better_Messages()->settings['miniThreadsEnable'] === '1') {
            $restricted_roles = BP_Better_Messages()->settings['restrictViewMiniThreads'];
            $is_restricted = false;

            if( count( $restricted_roles ) > 0 ) {
                foreach( $restricted_roles as $restricted_role ){
                    if( in_array( $restricted_role, $roles ) ){
                        $is_restricted = true;
                    }
                }
            }

            if( ! $is_restricted ) {
                $tabs['messages'] = 'messages';
            }
        }

        if(BP_Better_Messages()->settings['miniFriendsEnable'] === '1'  && function_exists('friends_get_friend_user_ids')) {

            $restricted_roles = BP_Better_Messages()->settings['restrictViewMiniFriends'];
            $is_restricted = false;

            if( count( $restricted_roles ) > 0 ) {
                foreach( $restricted_roles as $restricted_role ){
                    if( in_array( $restricted_role, $roles ) ){
                        $is_restricted = true;
                    }
                }
            }

            if( ! $is_restricted ) {
                $friends = friends_get_friend_user_ids(get_current_user_id());
                if (count($friends) > 0) {
                    $tabs['friends'] = 'friends';
                }
            }
        }

        if( BP_Better_Messages()->settings['enableMiniGroups'] === '1' && function_exists('groups_get_user_groups') ) {

            $restricted_roles = BP_Better_Messages()->settings['restrictViewMiniGroups'];
            $is_restricted = false;

            if( count( $restricted_roles ) > 0 ) {
                foreach( $restricted_roles as $restricted_role ){
                    if( in_array( $restricted_role, $roles ) ){
                        $is_restricted = true;
                    }
                }
            }

            if( ! $is_restricted ) {
                $groups = groups_get_user_groups(get_current_user_id());

                if ($groups['total'] > 0) {
                    $tabs['groups'] = 'groups';
                }
            }
        }

        $tabs = apply_filters( 'bp_better_messages_bottom_widgets', $tabs );

        if( count($tabs) == 0 ) return false;

        $has_chat_footer = false;
        $has_new_button = false;

        if( BP_Better_Messages()->settings['disableNewThread'] === '0' || current_user_can('manage_options') ){
            $has_chat_footer = true;
            $has_new_button  = true;
        }

        $me = BP_Better_Messages()->functions->render_me();
        if( !! $me ){
            $has_chat_footer = true;
        }

        $chat_footer = '';

        $path = apply_filters('bp_better_messages_views_path', BP_Better_Messages()->path . '/views/');

        $template = 'layout-mini-list.php';

        $template = apply_filters( 'bp_better_messages_current_template', $path . $template, $template );

        if($template !== false) {
            BP_Better_Messages()->functions->pre_template_include();
            include($template);
            BP_Better_Messages()->functions->after_template_include();
        }
    }
}

function BP_Better_Messages_Mini_List()
{
    return BP_Better_Messages_Mini_List::instance();
}