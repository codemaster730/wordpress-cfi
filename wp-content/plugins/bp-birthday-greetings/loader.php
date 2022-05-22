<?php
/**
 * Plugin Name: BP Birthday Greetings
 * Plugin URI:  https://prashantdev.wordpress.com
 * Description: Members will receive a birthday greeting as a notification
 * Author:      Prashant Singh
 * Author URI:  https://profiles.wordpress.org/prashantvatsh
 * Version:     1.0.4
 * Text Domain: bp-birthday-greetings
 * License:     GPLv2 or later
 */

defined( 'ABSPATH' ) || exit;


add_action('plugins_loaded','bp_birthday_check_is_buddypress');
function bp_birthday_check_is_buddypress(){
	if ( function_exists('bp_is_active') ) {
		require( dirname( __FILE__ ) . '/bp-birthday-greetings.php' );
		require( dirname( __FILE__ ) . '/bp-birthday-widget.php' );
	}else{
		add_action( 'admin_notices', 'bp_birthday_buddypress_inactive__error' );
	}
}

function bp_birthday_buddypress_inactive__error() {
	$class = 'notice notice-error';
	$message = __( 'BP Birthday Greetings requires BuddyPress to be active and running.', 'bp-birthday-greetings' );
	printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) ); 
}

register_activation_hook(__FILE__, 'bp_birthday_plugin_activation');

function bp_birthday_plugin_activation() {
    if (! wp_next_scheduled ( 'bp_birthday_daily_event' )) {
		wp_schedule_event(time(), 'daily', 'bp_birthday_daily_event');
    }
}

add_action('bp_birthday_daily_event', 'bp_birthday_do_this_daily');

function bp_birthday_do_this_daily() {
	global $wp, $bp, $wpdb;
	$bp_birthday_option_value = bp_get_option( 'bp-dob' );
	$sql = $wpdb->prepare( "SELECT profile.user_id, profile.value FROM {$bp->profile->table_name_data} profile INNER JOIN $wpdb->users users ON profile.user_id = users.id AND user_status != 1 WHERE profile.field_id = %d", $bp_birthday_option_value);
	$profileval = $wpdb->get_results($sql);
	foreach ($profileval as $profileobj) {
		$timeoffset = get_option('gmt_offset');
		if(!is_numeric($profileobj->value)) {
			$bday = strtotime($profileobj->value) + $timeoffset;
		}else {
			$bday = $profileobj->value + $timeoffset;
		}
		if ((date_i18n("n")==date("n",$bday))&&(date_i18n("j")==date("j",$bday)))
			$birthdays[] = $profileobj->user_id;
		if(!empty($birthdays)){
			bp_birthday_happy_birthday_notification($birthdays);
		}
	}
}

function bp_birthday_happy_birthday_notification($birthdays){
	foreach ($birthdays as $key => $value) {
		bp_notifications_add_notification( array(
			'user_id'           => $value,
			'item_id'           => $value,
			'component_name'    => 'birthday',
			'component_action'  => 'ps_birthday_action',
			'date_notified'     => bp_core_current_time(),
			'is_new'            => 1,
		) );
	}
	
}

function bp_birthday_get_registered_components( $component_names = array() ) {
	if ( ! is_array( $component_names ) ) {
		$component_names = array();
	}
	array_push( $component_names, 'birthday' );
	return $component_names;
}
add_filter( 'bp_notifications_get_registered_components', 'bp_birthday_get_registered_components' );

function bp_birthday_buddypress_notifications( $content, $item_id, $secondary_item_id, $total_items, $format = 'string', $action, $component  ) {
	if ( 'ps_birthday_action' === $action ) {
		$site_title = get_bloginfo( 'name' );
		$custom_title = __("Wish you a very happy birthday. $site_title wishes you more success and peace in life.",'bp-birthday-greetings');
		$custom_link  = '';
		$custom_text = __("Wish you a very happy birthday. $site_title wishes you more success and peace in life.", 'bp-birthday-greetings');
		if ( 'string' === $format ) {
			$return = apply_filters( 'ps_birthday_filter', '<a href="' . esc_url( $custom_link ) . '" title="' . esc_attr( $custom_title ) . '">' . esc_html( $custom_text ) . '</a>', $custom_text, $custom_link );
		} else {
			$return = apply_filters( 'ps_birthday_filter', array(
				'text' => $custom_text,
				'link' => $custom_link
			), $custom_link, (int) $total_items, $custom_text, $custom_title );
		}
		return $return;
	}
}
add_filter( 'bp_notifications_get_notifications_for_user', 'bp_birthday_buddypress_notifications', 10, 7);

add_action('wp_enqueue_scripts', 'bp_birthday_enqueue_style');
function bp_birthday_enqueue_style(){
	wp_enqueue_style('birthday-style',  plugin_dir_url( __FILE__ )  .'assets/css/bp-birthday-style.css');
}

//Shortcode to list anywhere
add_shortcode('ps_birthday_list', 'bp_birthday_shortcode');
function bp_birthday_shortcode(){
	global $wp, $bp, $wpdb;
	$bp_birthday_option_value = bp_get_option( 'bp-dob' );
	$sql = $wpdb->prepare("SELECT profile.user_id, profile.value FROM {$bp->profile->table_name_data} profile INNER JOIN $wpdb->users users ON profile.user_id = users.id AND user_status != 1 WHERE profile.field_id = %d", $bp_birthday_option_value);
	$profileval = $wpdb->get_results($sql);
	foreach ($profileval as $profileobj) {
		$timeoffset = get_option('gmt_offset');
		if(!is_numeric($profileobj->value)) {
			$bday = strtotime($profileobj->value) + $timeoffset;
		}else {
			$bday = $profileobj->value + $timeoffset;
		}
		if ((date_i18n("n")==date("n",$bday))&&(date_i18n("j")==date("j",$bday)))
		$birthdays[] = $profileobj->user_id;
	}
	if(empty($birthdays)){
		$empty_message = apply_filters('bp_birthday_empty_message', __('No Birthdays Found Today.','bp-birthday-greetings'));
		echo $empty_message;
	}else{
		echo '<ul class="birthday-members-list">';
		foreach ($birthdays as $birthday => $members_id) {
			$member_name =  bp_core_get_user_displayname( $members_id );
			$btn = '';
			if ( bp_is_active( 'messages' ) ){
				$defaults = array(
					'id' => 'private_message-'.$members_id,
					'component' => 'messages',
					'must_be_logged_in' => true,
					'block_self' => true,
					'wrapper_id' => 'send-private-message-'.$members_id,
					'wrapper_class' =>'send-private-message',
					'link_href' => wp_nonce_url( bp_loggedin_user_domain() . bp_get_messages_slug() . '/compose/?r=' . bp_core_get_username( $members_id ) ),
					'link_title' => __( 'Send a private message to this user.', 'bp-birthday-greetings' ),
					'link_text' => __( 'Wish Happy Birthday', 'bp-birthday-greetings' ),
					'link_class' => 'send-message',
				);
				if( $members_id != bp_loggedin_user_id() ){
					$btn = bp_get_button( $defaults );
				}else{
					$btn='';
				}
			}
			$dp_width = bp_get_option( 'bp-dp-width' );
			$dp_width = (empty($dp_width)) ? 32 : $dp_width;
			$dp_height = bp_get_option( 'bp-dp-height' );
			$dp_height = (empty($dp_height)) ? 32 : $dp_height;
			$dp_type = bp_get_option( 'bp-dp-type' );
			$dp_type = (empty($dp_type)) ? 'thumb' : $dp_type;
			$cake_img = apply_filters('bp_birthday_cake_img', '&#127874;');
			echo '<li>'.bp_core_fetch_avatar(array('item_id' => $members_id, 'type' => $dp_type, 'width' => $dp_width, 'height' => $dp_height, 'class' => 'avatar','html'=>true));
			_e('Happy Birthday','bp-birthday-greetings');
			echo ' '.$member_name.' '.$cake_img.'</li>';
			echo $btn;
		}
		echo '</ul>';
	}
}