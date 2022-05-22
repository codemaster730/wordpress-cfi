<?php

function tc_shortcodes_display() {
 ?>
			
						<table class="form-table">
					
					<tr valign="top">
						<th colspan="2">
						
						<h3>
						<?php _e ('Additional Shortcodes' , 'bbp-topic-count' ) ; ?>
						</h3>


						
<p><tt>[display-topic-count]</tt> 
	<?php _e('Displays the current users topic count', 'bbp-topic-count' ) ; ?>
</p>
<p><tt>[display-reply-count]</tt>  
	<?php _e('Displays the current users reply count', 'bbp-topic-count' ) ; ?>
</p>
<p><tt>[display-total-count]</tt>  
	<?php _e('Displays the current users total topic and reply count', 'bbp-topic-count' ) ; ?>
</p>
<p><tt>[display-top-users]</tt>
	<?php _e('Displays top x users for total topics and replies', 'bbp-topic-count' ) ; ?>
</p>
<p>
	<?php _e('This shortcode has many parameters - these are optional and only add those you need !', 'bbp-topic-count' ) ; ?>
</p>
<p><h3>
	<?php _e('display-top-users - additional parameters !', 'bbp-topic-count' ) ; ?>
</h3></p>


<p><tt>[display-top-users avatar-size="25" padding="20" before=" - " after=" topics"  show="6" count="tr" hide-admins = 'yes' profile-link = 'no' show-avatar' = 'no' show-name' = 'no' forum = '1123']</tt>

<p><i><b>
	<?php _e('Note - you only need enter parameters where you want to change the default', 'bbp-topic-count' ) ; ?>
</i></b></p>

<p><i>
	<?php _e('avatar-size', 'bbp-topic-count' ) ; ?>
</i></p>
<p>
	<?php _e("Default = '96' - the smaller the number the smaller the avatar", 'bbp-topic-count' ) ; ?>
</p>
<p></p>

<p><i>
	<?php _e('padding', 'bbp-topic-count' ) ; ?>
</i></p>
<p>
	<?php _e("Default = '50' - The space between the avatar/username and the text to the right of this", 'bbp-topic-count' ) ; ?>
</p>
<p></p>

<p><i>
	<?php _e('before', 'bbp-topic-count' ) ; ?>
</i></p>
<p>
	<?php _e("Default = blank -  Any characters/text before the count number - eg 'Topics : ", 'bbp-topic-count' ) ; ?>
</p>
<p></p>

<p><i>
	<?php _e('after', 'bbp-topic-count' ) ; ?>
</i></p>
<p>
	<?php _e("Default = blank - Any characters/text after the count number - eg ' Topics ", 'bbp-topic-count' ) ; ?>
</p>
<p></p>

<p><i>
	<?php _e('show', 'bbp-topic-count' ) ; ?>
</i></p>
<p>
	<?php _e("Default = '5' - the number of users to show", 'bbp-topic-count' ) ; ?>
</p>
<p></p>

<p><i>
	<?php _e('count', 'bbp-topic-count' ) ; ?>
</i></p>
<p>
	<?php _e("Default = 'tr' - what to count - put 't' for just topics, 'r' for just replies default is to count the total topics and replies ", 'bbp-topic-count' ) ; ?>
</p>
<p></p>

<p><i>
	<?php _e('hide-admins', 'bbp-topic-count' ) ; ?>
</i></p>
<p>
	<?php _e("Default = 'no' - if set to 'yes' - then administrators are excluded from display", 'bbp-topic-count' ) ; ?>
</p>
<p></p>

<p><i>
	<?php _e('profile-link', 'bbp-topic-count' ) ; ?>
</i></p>
<p>
	<?php _e("Default = 'yes' - if set to 'no' - then the avatar and/or name do not have a link to the users profile", 'bbp-topic-count' ) ; ?>
</p>
<p></p>

<p><i>
	<?php _e('show-avatar', 'bbp-topic-count' ) ; ?>
</i></p>
<p>
	<?php _e("Default = 'yes' -  if set to 'no' - then the avatar will not show", 'bbp-topic-count' ) ; ?>
</p>
<p></p>

<p><i>
	<?php _e('show-name', 'bbp-topic-count' ) ; ?>
</i></p>
<p>
	<?php _e("Default = 'yes' -  if set to 'no' - then the name will not show", 'bbp-topic-count' ) ; ?>
</p>
<p></p>

<p><i>
	<?php _e('forum', 'bbp-topic-count' ) ; ?>
</i></p>
<p>
	<?php _e("Default = blank - Enter a single forum ID to only count from that forum", 'bbp-topic-count' ) ; ?>
</p>
<p></p>


 
 <?php
}
