<?php
defined( 'ABSPATH' ) || exit;

class BP_Better_Messages_Shortcodes
{

    public static function instance()
    {

        // Store the instance locally to avoid private static replication
        static $instance = null;

        // Only run these methods if they haven't been run previously
        if ( null === $instance ) {
            $instance = new BP_Better_Messages_Shortcodes;
            $instance->setup_actions();
        }

        // Always return the instance
        return $instance;

        // The last metroid is in captivity. The galaxy is at peace.
    }

    public function setup_actions(){
        add_shortcode( 'bp_better_messages_unread_counter', array( $this, 'unread_counter_shortcode' ) );
        add_shortcode( 'bp_better_messages_my_messages_url', array( $this, 'bp_better_messages_url' ) );
        add_shortcode( 'bp_better_messages_pm_button', array( $this, 'bp_better_messages_pm_button' ) );
        add_shortcode( 'bp_better_messages', array( $this, 'bp_better_messages' ) );
        add_shortcode( 'bp_better_messages_group', array( $this, 'bp_better_messages_group' ) );

        /**
         * Premium buttons
         */
        add_shortcode( 'bp_better_messages_mini_chat_button',  array( $this, 'bp_better_messages_mini_chat_button' ) );
        add_shortcode( 'bp_better_messages_video_call_button', array( $this, 'bp_better_messages_video_call_button' ) );
        add_shortcode( 'bp_better_messages_audio_call_button', array( $this, 'bp_better_messages_audio_call_button' ) );
    }


    public function bp_better_messages(){
        ob_start();
        if( function_exists('bp_is_user') && bp_is_user() ) {
            echo BP_Better_Messages()->functions->get_page();
        } else {
            echo BP_Better_Messages()->functions->get_page( true );
        }
        return ob_get_clean();
    }


    public function bp_better_messages_group($args){
        ob_start();
        echo BP_Better_Messages()->functions->get_group_page( $args['group_id'] );
        return ob_get_clean();
    }

    public function bp_better_messages_pm_button( $args ){
        if( ! is_user_logged_in() ){
            return '';
        }

        $class   = 'bpbm-pm-button';
        $target  = '';
        $text    = __('Private Message', 'bp-better-messages');
        $subject = '';
        $message = '';
        $fast    = true;
        $return_url = false;

        if( isset( $args['class'] ) ) {
            $class .= ' ' . $args['class'];
        }

        if( isset( $args['target'] ) ) {
            $target .= ' target="' . $args['target'] . '"';
        }

        if( isset( $args['text'] ) ) {
            $text = $args['text'];
        }

        if( isset( $args['subject'] ) ) {
            $subject = urlencode($args['subject']);
        }

        if( isset( $args['message'] ) ) {
            $message = urlencode($args['message']);
        }

        if( isset( $args['fast_start'] ) && $args['fast_start'] === '0' ) {
            $fast = false;
        }

        if( isset( $args['url_only'] ) && $args['url_only'] === '1' ) {
            $return_url = true;
        }

        if( isset( $args['user_id'] ) ) {
            $user_id = (int) $args['user_id'];
        } else {
            $user_id = (int) BP_Better_Messages()->functions->get_member_id();
        }

        if( $user_id === get_current_user_id() ) return '';

        $userdata = get_userdata( $user_id );
        if( ! $userdata ) return '';

        $nicename = $userdata->user_nicename;

        $args = [
            'new-message' => '',
            'to' => $nicename
        ];

        $base_url = BP_Better_Messages()->functions->get_link(get_current_user_id());
        if( BP_Better_Messages()->settings['fastStart'] == '1' && $fast ){
            $args['fast'] = '1';
        }

        if( ! empty( $subject ) ){
            $args['subject'] = $subject;
        }

        if( ! empty( $message ) ){
            $args['message'] = $message;
        }

        $link = add_query_arg( $args, $base_url );

        if( $return_url ) {
            return $link;
        }

        return '<a href="' . esc_url($link) .  '" class="' . esc_attr($class) . '"' . $target . '>' . esc_attr($text) . '</a>';
    }

    public function bp_better_messages_video_call_button( $args ){
        if( ! is_user_logged_in() ){
            return '';
        }

        $class   = 'bpbm-pm-button video-call';
        $target  = '';
        $text    = __('Video Call', 'bp-better-messages');
        $return_url = false;

        if( isset( $args['class'] ) ) {
            $class .= ' ' . $args['class'];
        }

        if( isset( $args['target'] ) ) {
            $target .= ' target="' . $args['target'] . '"';
        }

        if( isset( $args['text'] ) ) {
            $text = $args['text'];
        }

        if( isset( $args['url_only'] ) && $args['url_only'] === '1' ) {
            $return_url = true;
        }

        if( isset( $args['user_id'] ) ) {
            $user_id = (int) $args['user_id'];
        } else {
            $user_id = (int) BP_Better_Messages()->functions->get_member_id();
        }

        if( $user_id === get_current_user_id() ) return '';

        $userdata = get_userdata( $user_id );
        if( ! $userdata ) return '';
        $args = [
            'fast-call' => '',
            'to' => $user_id,
            'type' => 'video'
        ];

        $base_url = BP_Better_Messages()->functions->get_link(get_current_user_id());
        $link = add_query_arg( $args, $base_url );

        if( $return_url ) {
            return $link;
        }

        return '<a href="' . esc_url($link) .  '" class="' . esc_attr($class) . '" data-user-id="' . $user_id . '">' . esc_attr($text) . '</a>';
    }

    public function bp_better_messages_audio_call_button( $args ){
        if( ! is_user_logged_in() ){
            return '';
        }

        $class   = 'bpbm-pm-button audio-call';
        $text    = __('Audio Call', 'bp-better-messages');
        $return_url = false;

        if( isset( $args['class'] ) ) {
            $class .= ' ' . $args['class'];
        }

        if( isset( $args['text'] ) ) {
            $text = $args['text'];
        }

        if( isset( $args['url_only'] ) && $args['url_only'] === '1' ) {
            $return_url = true;
        }

        if( isset( $args['user_id'] ) ) {
            $user_id = (int) $args['user_id'];
        } else {
            $user_id = (int) BP_Better_Messages()->functions->get_member_id();
        }

        if( $user_id === get_current_user_id() ) return '';

        $userdata = get_userdata( $user_id );
        if( ! $userdata ) return '';

        $args = [
            'fast-call' => '',
            'to' => $user_id,
            'type' => 'audio'
        ];

        $base_url = BP_Better_Messages()->functions->get_link(get_current_user_id());
        $link = add_query_arg( $args, $base_url );

        if( $return_url ) {
            return $link;
        }

        return '<a href="' . esc_url($link) .  '" class="' . esc_attr($class) . '" data-user-id="' . $user_id . '">' . esc_attr($text) . '</a>';
    }

    public function bp_better_messages_mini_chat_button( $args ){
        if( ! is_user_logged_in() ){
            return '';
        }

        if (BP_Better_Messages()->settings['miniChatsEnable'] !== '1') {
            return '';
        }

        $class   = 'bpbm-pm-button open-mini-chat';
        $text    = __('Private Message', 'bp-better-messages');

        if( isset( $args['class'] ) ) {
            $class .= ' ' . $args['class'];
        }

        if( isset( $args['text'] ) ) {
            $text = $args['text'];
        }

        if( isset( $args['user_id'] ) ) {
            $user_id = (int) $args['user_id'];
        } else {
            $user_id = (int) BP_Better_Messages()->functions->get_member_id();
        }

        if( $user_id === get_current_user_id() ) return '';

        $userdata = get_userdata( $user_id );
        if( ! $userdata ) return '';

        $link = '#';

        return '<a href="' . esc_url($link) .  '" class="' . esc_attr($class) . '" data-user-id="' . $user_id . '">' . esc_attr($text) . '</a>';
    }

    public function bp_better_messages_url(){
        if( ! is_user_logged_in() ){
            return '';
        }

        return BP_Better_Messages()->functions->get_link( get_current_user_id() );
    }

    function unread_counter_shortcode( $args ) {
        if( ! is_user_logged_in() ){
            return '';
        }

        $hide_when_no_messages = false;
        $preserve_space = false;
        if( isset( $args['hide_when_no_messages'] ) && $args['hide_when_no_messages'] === '1' ) {
            $hide_when_no_messages = true;
        }

        if( isset( $args['preserve_space'] ) && $args['preserve_space'] === '1' ) {
            $preserve_space = true;
        }

        $classes = ['bp-better-messages-unread', 'bpbmuc'];
        if( $hide_when_no_messages ){
            $classes[] = 'bpbmuc-hide-when-null';
        }

        if( $preserve_space ){
            $classes[] = 'bpbmuc-preserve-space';
        }

        $class = implode(' ', $classes );
        if( BP_Better_Messages()->settings['mechanism'] !== 'websocket'){
            $unread = BP_Messages_Thread::get_total_threads_for_user( get_current_user_id(), 'inbox', 'unread' );
            return '<span class="' . $class . '" data-count="' . $unread . '">' . $unread . '</span>';
        } else {
            return '<span class="' . $class . '" data-count="0">0</span>';
        }
    }

}

function BP_Better_Messages_Shortcodes()
{
    return BP_Better_Messages_Shortcodes::instance();
}