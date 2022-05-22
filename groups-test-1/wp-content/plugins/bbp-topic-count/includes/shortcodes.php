<?php

add_shortcode('display-topic-count', 'display_topic_count');  
add_shortcode('display-reply-count', 'display_reply_count');  
add_shortcode('display-total-count', 'display_total_count');  
add_shortcode ('display-top-users', 'tc_display_top_users') ;

Function display_topic_count () {
global $tc_options;
		$user_id = bbp_get_current_user_id();
		$topic_count  = bbp_get_user_topic_count_raw( $user_id);
		return $topic_count ;
	}

Function display_reply_count () {
global $tc_options;
		$user_id = bbp_get_current_user_id();
		$reply_count = bbp_get_user_reply_count_raw( $user_id);
		return $reply_count ;
}

Function display_total_count () {
global $tc_options;
		$user_id = bbp_get_current_user_id();
		$topic_count  = bbp_get_user_topic_count_raw( $user_id);
		$reply_count = bbp_get_user_reply_count_raw( $user_id);
		$post_count   = (int) $topic_count + $reply_count;
		return $post_count ;
}

function tc_display_top_users ($attr){
	if (empty ($attr))  $attr=array ();
	//set defaults
	if (empty ($attr['show'] )) $attr['show']=5 ;
	if (empty ($attr['count'] )) $attr['count']='tr' ;
	if (empty ($attr['avatar-size'] )) $attr['avatar-size']=96 ;
	if (empty ($attr['padding'] )) $attr['padding']=50 ;
	
	if (!empty ($attr['forum'] ) && is_numeric ($attr['forum'])) $forum = $attr['forum'] ;
	
	//blank remainder so they exist!
	if (empty ($attr['show-avatar'] )) $attr['show-avatar']='' ;
	if (empty ($attr['show-name'] )) $attr['show-name']='' ;
	if (empty ($attr['before'] )) $attr['before']='' ;
	if (empty ($attr['after'] )) $attr['after']='' ;
	if (empty ($attr['remove-styling'] )) $attr['remove-styling']='' ;
	if (empty ($attr['hide-admins'] )) $attr['hide-admins']='' ;
	if (empty ($attr['profile-link'] )) $attr['profile-link']='' ;
	
	
	$count = array () ;
	
	$users= get_users () ;
	if ( $users ) :
		foreach ( $users as $user ) {
			$topic_count =  $reply_count = 0 ;
			$user_id = $user->ID ;
			if ($attr['hide-admins'] == 'yes' && user_can($user_id, 'administrator')) continue ;
			if (empty ($attr['forum'])) {
			if (strpos ($attr['count'] , "t" ) !== false) $topic_count  = bbp_get_user_topic_count_raw( $user_id);
			if (strpos ($attr['count'] , "r" ) !== false ) $reply_count = bbp_get_user_reply_count_raw( $user_id);
			}
			else {  //we have an individual forum
			if (strpos ($attr['count'] , "t" ) !== false) $topic_count  = tc_topic_count_by_forum( $user_id, $forum);
			if (strpos ($attr['count'] , "r" ) !== false ) $reply_count = tc_reply_count_by_forum(  $user_id, $forum);
			}
			$count[$user_id] = (int) $topic_count + $reply_count;	
	}
	endif ;
	
	//re-sort into descending order
	arsort($count) ;
	
	//set up in-line styling
	if ($attr['remove-styling'] !="yes") {
		$css1 = 'style="float:left;' ;
		$css2 = 'style="padding-left:'.$attr['padding'].'px ;height:'.$attr['avatar-size'].'px"' ;
		$css3 = 'style="padding-left:'.$attr['padding'].'px ; top: 50%;transform: translateY(-50%);position: relative;"' ;
	}
	else
		$css1 = $css2 = $css3 = '' ;
	
	
	$i=0 ;
	$output='' ;
	foreach($count as $user_id => $value) {
		$i++ ; 
		
		//stop if we have users with 0
		if ($value == 0 ) break ;
		$output.= '<div class="tc-user">' ;
		if ($attr['show-avatar'] !='no') {
			$output.= '<div class="tc-avatar"'.$css1.'">' ;
			if ($attr['profile-link'] == 'no' ) $output.= get_avatar( $user_id , $attr['avatar-size']); 
			else $output.= '<a class="tc-profile-link" href="' . esc_url( bbp_get_user_profile_url( $user_id) ) . '">' . get_avatar( $user_id , $attr['avatar-size']) . '</a>';
			$output.= '</div>' ;
		}
		$output.= '<div class="tc-wrapper"'.$css2.'>' ;
		$output.= '<div class="tc-content"'.$css3.'>' ;
		if ($attr['show-name'] !='no') {
			if ($attr['profile-link'] == 'no' ) $output.= get_the_author_meta ('display_name' , $user_id) ;
			else $output.= '<a class="tc-profile-link" href="' . esc_url( bbp_get_user_profile_url( $user_id) ) . '">' . get_the_author_meta ('display_name' , $user_id) . '</a>';
		}
		//the content!!
			$output.= $attr['before'].$value.$attr['after']  ;
			$output.= "</div></div><br>";
		$output.= '</div>' ;
	if ($i == $attr['show'] ) break ;
	}
return $output;
}


function tc_topic_count_by_forum ($user_id, $forum) {
	global $wpdb;
	$where = get_posts_by_author_sql( bbp_get_topic_post_type(), true, $user_id ) ;
	$count = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->posts} $where AND post_parent = '$forum' ");

	//$count = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->posts} $where post_parent = '$forum' AND post_type = '$forumt' AND post_author = '$user_id' ");
return $count ;
}

function tc_reply_count_by_forum ($user_id, $forum) {
	global $wpdb;
	$type = bbp_get_reply_post_type() ;
	//$where = get_posts_by_author_sql( bbp_get_reply_post_type(), true, $user_id ) ;
	$count = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->posts, $wpdb->postmeta	
	WHERE $wpdb->posts.post_type = '$type'
	AND ( $wpdb->posts.post_status = 'publish' OR $wpdb->posts.post_status = 'private' )
	AND $wpdb->posts.post_author = '$user_id'
	AND $wpdb->posts.ID = $wpdb->postmeta.post_id 
    AND $wpdb->postmeta.meta_key = '_bbp_forum_id'
    AND $wpdb->postmeta.meta_value = '$forum' ");
	return $count ;
}