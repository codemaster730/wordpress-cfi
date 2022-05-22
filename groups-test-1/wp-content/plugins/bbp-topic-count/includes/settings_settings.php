<?php

/*******************************************
* bbp topic count Settings Page
*******************************************/


function tc_settings()
{
	global $tc_options;
		
	?>
	
						<table class="form-table">
					
					<tr valign="top">
						<th colspan="2">
						
						<h3>
						<?php _e ('Settings' , 'bbp-topic-count' ) ; ?>
						</h3>

			
			<form method="post" action="options.php">

				<?php settings_fields( 'tc_settings' ); ?>
								
				<table class="form-table">
					
					<tr valign="top">
						<th colspan="2"><p> <?php _e("This plugin allows you to display topic count, reply count and total posts under the authors name and avatar in topics and replies", 'bbp-topic-count'); ?>
					</p></th>
					</tr>
					
										
					<!-------------------------------Topics ---------------------------------------->
					
					<tr valign="top">
						<th colspan="2"><h3><?php _e('Topics', 'bbp-topic-count'); ?></h3></th>
					</tr>
					
					<!-- checkbox to activate -->
					<tr valign="top">  
					<th><?php _e('Activate', 'bbp-topic-count'); ?></th>
					<td>
					<?php activate_topic_checkbox() ;?>
					</td>
					</tr>
									
					
					<tr valign="top">
					<th><?php _e('Topic Label Name', 'bbp-topic-count'); ?></th>
					<td>
						<input id="tc_settings[topic_label]" class="large-text" name="tc_settings[topic_label]" type="text" value="<?php echo isset( $tc_options['topic_label'] ) ? esc_html( $tc_options['topic_label'] ) : '';?>" /><br/>
						<label class="description" for="tc_settings[topic_label]"><?php _e( 'Enter the description eg "Topics:", "Topics - ", "Posts :" "Started : " etc.', 'bbp-topic-count' ); ?></label><br/>
					</td>
					</tr>
										
										
					<!------------------------------- Replies ------------------------------------------>
					<tr valign="top">
						<th colspan="2"><h3><?php _e('Replies', 'bbp-topic-count'); ?></h3></th>
					</tr>
					
					<!-- checkbox to activate -->
					<tr valign="top">  
					<th><?php _e('Activate', 'bbp-topic count'); ?></th>
					<td>
					<?php activate_reply_checkbox() ;?>
					</td>
					</tr>
					
					
					<tr valign="top">
						<th><?php _e('Reply Label Name', 'bbp-topic-count'); ?></th>
						<td>
							<input id="tc_settings[reply_label]" class="large-text" name="tc_settings[reply_label]" type="text" value="<?php echo isset( $tc_options['reply_label'] ) ? esc_html( $tc_options['reply_label'] ) : '';?>" /><br/>
							<label class="description" for="tc_settings[reply_label]"><?php _e( 'Enter the description eg "Replies:", "Replies - ", "Posts", "joined in : " etc.', 'bp-topic-count' ); ?></label><br/>
						</td>
					</tr>
					
					
					
					<!------------------------------- Total Posts ------------------------------------------>
					<tr valign="top">
						<th colspan="2"><h3><?php _e('Total posts (Topics + Replies)', 'bbp-topic-count'); ?></h3></th>
					</tr>
					
					<!-- checkbox to activate -->
					<tr valign="top">  
					<th><?php _e('Activate', 'bbp-topic-count'); ?></th>
					<td>
					<?php activate_totalposts_checkbox() ;?>
					</td>
					</tr>
					
					
					<tr valign="top">
						<th><?php _e('Total Posts Name', 'bbp-topic-count'); ?></th>
						<td>
							<input id="tc_settings[posts_label]" class="large-text" name="tc_settings[posts_label]" type="text" value="<?php echo isset( $tc_options['posts_label'] ) ? esc_html( $tc_options['posts_label'] ) : '';?>" /><br/>
							<label class="description" for="tc_settings[item3_label]"><?php _e( 'Enter the description eg "Total posts:", "Total Posts - ", "Total", "Posts: " etc.', 'bp-topic-count' ); ?></label><br/>
						</td>
					</tr>
					
					
					<!------------------------------- Display parameters ------------------------------------------>
					<tr valign="top">
						<th colspan="2"><h3><?php _e('Display parameters', 'bbp-topic-count'); ?></h3></th>
					</tr>
					
					<?php
					$item0="tc_settings[sep]" ;
					$value0 = (!empty($tc_options['sep']) ? $tc_options['sep'] : 0) ;
					?>
					<tr>
						<th>
						<?php _e ('Thousands Space Seperator' , 'bbp-topic-count' ) ;?>
						</th>
						<td>
						<?php
							echo '<input name="'.$item0.'" id="'.$item0.'" type="radio" value="0" class="code"  ' . checked( 0,$value0, false ) . ' />' ;
							_e ('No seperator (eg 1000)' , 'bbp-topic-count' ) ;?>
							<br>
						<?php
							echo '<input name="'.$item0.'" id="'.$item0.'" type="radio" value="1" class="code"  ' . checked( 1,$value0, false ) . ' />' ;
							_e ('Comma Seperator (eg 1,000)' , 'bbp-topic-count' ) ;?>
							<br>
						<?php
							echo '<input name="'.$item0.'" id="'.$item0.'" type="radio" value="2" class="code"  ' . checked( 2,$value0, false ) . ' />' ;
							_e ('Space Seperator (eg 1 000)' , 'bbp-topic-count' ) ;?>
						</td>
					<tr>
					
					<?php
					$item0="tc_settings[order]" ;
					$value0 = (!empty($tc_options['order']) ? $tc_options['order'] : 0) ;
					?>
					<tr>
						<th>
						<?php _e ('Display order' , 'bbp-topic-count' ) ;?>
						</th>
						<td>
						<?php
							echo '<input name="'.$item0.'" id="'.$item0.'" type="radio" value="0" class="code"  ' . checked( 0,$value0, false ) . ' />' ;
							_e ('Text then count eg \'Topics: 10\'' , 'bbp-topic-count' ) ;?>
							<br>
						<?php
							echo '<input name="'.$item0.'" id="'.$item0.'" type="radio" value="1" class="code"  ' . checked( 1,$value0, false ) . ' />' ;
							_e ('Count then text eg \'10 Topics\'' , 'bbp-topic-count' ) ;?>
							<br>
						</td>
					<tr>
				
				<?php
					$item0="tc_settings[location]" ;
					$value0 = (!empty($tc_options['location']) ? $tc_options['location'] : 0) ;
					?>
					<tr>
						<th>
						<?php _e ('Display Location' , 'bbp-topic-count' ) ;?>
						</th>
						<td>
						<?php
							echo '<input name="'.$item0.'" id="'.$item0.'" type="radio" value="0" class="code"  ' . checked( 0,$value0, false ) . ' />' ;
							_e ('Display in Author Details' , 'bbp-topic-count' ) ;?>
							<br>
						<?php
							echo '<input name="'.$item0.'" id="'.$item0.'" type="radio" value="1" class="code"  ' . checked( 1,$value0, false ) . ' />' ;
							_e ('Display in Reply content' , 'bbp-topic-count' ) ;?>
							<br>
						</td>
					<tr>
				</table>
				
				<!-- save the options -->
				<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e( 'Save Options', 'bbp-topic-count' ); ?>" />
				</p>
								
				
			</form>
		</div><!--end sf-wrap-->
	</div><!--end wrap-->
		
	<?php
}



/*****************************   Checkbox functions **************************/

function activate_topic_checkbox() {
 	global $tc_options ;
	$item1 = !empty($tc_options['activate_topics']) ? $tc_options['activate_topics'] : '';
	echo '<input name="tc_settings[activate_topics]" id="tc_settings[activate_topics]" type="checkbox" value="1" class="code" ' . checked( 1,$item1, false ) . ' />' ;
	_e ('Add this item to the display' , 'bbp-topic-count' ) ;
  }
  
function activate_reply_checkbox() {
 	global $tc_options ;
	$item2 = !empty($tc_options['activate_replies']) ? $tc_options['activate_replies'] : '';
	echo '<input name="tc_settings[activate_replies]" id="tc_settings[activate_replies]" type="checkbox" value="1" class="code" ' . checked( 1,$item2, false ) . ' />' ;
	_e ('Add this item to the display' , 'bbp-topic-count' ) ;
  }

function activate_totalposts_checkbox() {
 	global $tc_options ;
	$item3 = !empty($tc_options['activate_posts']) ? $tc_options['activate_posts'] : '';
	echo '<input name="tc_settings[activate_posts]" id="tc_settings[activate_posts]" type="checkbox" value="1" class="code" ' . checked( 1,$item3, false ) . ' />' ;
	_e ('Add this item to the display' , 'bbp-topic-count' ) ;
  }