<?php

//Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}
else
{
global $tc_options;
	
if (empty($tc_options['location'])) add_action ('bbp_theme_after_reply_author_details', 'display_counts') ;

else add_action ('bbp_theme_before_reply_content', 'display_counts_in_reply') ;

//this function hooks to BBpress loop-single-reply.php and adds the counts to the reply display
function display_counts ($reply_id = 0) 
		{
		global $tc_options;
		$user_id=bbp_get_reply_author_id( $reply_id ) ;
		$topic_count  = bbp_get_user_topic_count_raw( $user_id);
		$reply_count = bbp_get_user_reply_count_raw( $user_id);
		$post_count   = (int) $topic_count + $reply_count;			
		$sep = (!empty($tc_options['sep']) ? $tc_options['sep'] : 0) ;
		
		if ($sep == 1) {
			$topic_count = number_format($topic_count);
			$reply_count = number_format($reply_count);
			$post_count = number_format($post_count);
		}
		if ($sep == 2) {
			$topic_count = number_format($topic_count, 0, '', ' ');
			$reply_count = number_format($reply_count,0, '',  ' ');
			$post_count = number_format($post_count, 0, '',  ' ');
		}
		echo '<div class="tc_display">' ;
		echo '<ul>' ;
		
// displays topic count
		
		$value = !empty($tc_options['activate_topics']) ? $tc_options['activate_topics'] : '';
		if(!empty ($value)) {
			echo '<li>' ;
				if (empty($tc_options['order'])) { 
					echo $label1 =  $tc_options['topic_label']." " ;
					echo $topic_count ;
				}
				else {
					echo $topic_count ;	
					echo $label1 =  $tc_options['topic_label']." " ;
				}
			echo "</li>" ;
		}
		
		
// displays replies count
		
		$value = !empty($tc_options['activate_replies']) ? $tc_options['activate_replies'] : '';
		if(!empty ($value)) {
			echo '<li>' ;
				if (empty($tc_options['order'])) { 
					echo $label2 =  $tc_options['reply_label']." " ;
					echo $reply_count ;
				}
				else {
					echo $reply_count ;
					echo $label2 =  $tc_options['reply_label']." " ;
				}
			echo "</li>" ;
		}
		
		
// displays total posts count
		
		$value = !empty($tc_options['activate_posts']) ? $tc_options['activate_posts'] : '';
		if(!empty ($value)) {
			echo '<li>' ;
				if (empty($tc_options['order'])) { 
					echo $label3 =  $tc_options['posts_label']." " ;
					echo $post_count ;
				}
				else {
					echo $post_count ;
					echo $label3 =  $tc_options['posts_label']." " ;
				}
			echo "</li>" ;
		}
		
//end of list		
		echo "</ul>" ;
		echo "</div>" ;

		}

function display_counts_in_reply ($reply_id = 0) 
		{
		global $tc_options;
		$user_id=bbp_get_reply_author_id( $reply_id ) ;
		$topic_count  = bbp_get_user_topic_count_raw( $user_id);
		$reply_count = bbp_get_user_reply_count_raw( $user_id);
		$post_count   = (int) $topic_count + $reply_count;			
		$sep = (!empty($tc_options['sep']) ? $tc_options['sep'] : 0) ;
		
		if ($sep == 1) {
			$topic_count = number_format($topic_count);
			$reply_count = number_format($reply_count);
			$post_count = number_format($post_count);
		}
		if ($sep == 2) {
			$topic_count = number_format($topic_count, 0, '', ' ');
			$reply_count = number_format($reply_count,0, '',  ' ');
			$post_count = number_format($post_count, 0, '',  ' ');
		}
		echo '<div class="tc_display">' ;
				
// displays topic count
		
		$value = !empty($tc_options['activate_topics']) ? $tc_options['activate_topics'] : '';
		if(!empty ($value)) {
			echo '<table><tr><td>' ;
				if (empty($tc_options['order'])) { 
					echo $label1 =  $tc_options['topic_label']." " ;
					echo $topic_count ;
				}
				else {
					echo $topic_count ;	
					echo $label1 =  $tc_options['topic_label']." " ;
				}
			echo '</td> ' ;
		}
		
		
// displays replies count
		
		$value = !empty($tc_options['activate_replies']) ? $tc_options['activate_replies'] : '';
		if(!empty ($value)) {
			echo '<td>' ;
				if (empty($tc_options['order'])) { 
					echo $label2 =  $tc_options['reply_label']." " ;
					echo $reply_count ;
				}
				else {
					echo $reply_count ;
					echo $label2 =  $tc_options['reply_label']." " ;
				}
			echo '</td> ' ;
		}
		
		
// displays total posts count
		
		$value = !empty($tc_options['activate_posts']) ? $tc_options['activate_posts'] : '';
		if(!empty ($value)) {
			echo '<td>' ;
				if (empty($tc_options['order'])) { 
					echo $label3 =  $tc_options['posts_label']." " ;
					echo $post_count ;
				}
				else {
					echo $post_count ;
					echo $label3 =  $tc_options['posts_label']." " ;
				}
			echo '</td> ' ;
		}
		
//end of list		
		echo '</tr></table>';
		echo "</div>" ;

		}



















}


