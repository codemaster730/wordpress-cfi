<?php

function tc_settings_page() {
	?>
	<div class="wrap">
		<div id="upb-wrap" class="upb-help">
			<h2>
				<?php _e('bbp Topic Count', 'tc-topic-count'); ?>
			</h2>
			<?php
			if ( ! isset( $_REQUEST['updated'] ) )
				$_REQUEST['updated'] = false;
			?>
			
			<?php if ( false !== $_REQUEST['updated'] ) : ?>
			<div class="updated fade">
				<p>
					<strong>
						<?php _e( 'Settings saved', 'tc-topic-count'); ?>
					</strong>
				</p>
			</div>
			<?php endif; ?>
			
			<?php //tests if we have selected a tab
            if( isset( $_GET[ 'tab' ] ) ) {
				$active_tab = $_GET[ 'tab' ];
			}
			else {
				$active_tab= 'shortcodes';
            } 
			?>
		
	<?php // sets up the tabs ?>			
	<h2 class="nav-tab-wrapper">
	<a href="?page=bbp-topic-count&tab=settings" class="nav-tab <?php echo $active_tab == 'settings' ? 'nav-tab-active' : ''; ?>"><?php _e('Settings', 'tc-topic-count'); ?></a>
	<a href="?page=bbp-topic-count&tab=shortcodes" class="nav-tab <?php echo $active_tab == 'shortcodes' ? 'nav-tab-active' : ''; ?>"><?php _e('Shortcodes', 'tc-topic-count'); ?> </a>
	</h2>
	
	
	
	<table class="form-table">
		<tr>		
			<td>
				<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
					<input type="hidden" name="cmd" value="_s-xclick">
					<input type="hidden" name="hosted_button_id" value="S6PZGWPG3HLEA">
					<input type="image" src="https://www.paypalobjects.com/en_US/GB/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal – The safer, easier way to pay online.">
					<img alt="" border="0" src="https://www.paypalobjects.com/en_GB/i/scr/pixel.gif" width="1" height="1">
				</form>
			</td>
			<td>
				<?php _e('If you find this plugin useful, please consider donating just a few dollars to help me develop and maintain it. You support will be appreciated', 'tc-topic-count'); ?>
			</td>
			</tr>
	</table>
	
	<?php
	//**** Settings
	if ($active_tab == 'settings' ) {
		tc_settings();
	}

	//****  Shortcodes
	if ($active_tab == 'shortcodes' ) {
		tc_shortcodes_display() ;
	}

	
}	//end of function settings_page()

// register the plugin settings
function tc_register_settings() {

	// create whitelist of options
	register_setting( 'tc_settings', 'tc_settings' );
	}
//call register settings function
add_action( 'admin_init', 'tc_register_settings' );


function tc_settings_menu() {

	// add settings page
	add_submenu_page('options-general.php', __('bbp topic count', 'bbp-topic-count'), __('bbp topic count', 'bbp-topic-count'), 'manage_options', 'bbp-topic-count', 'tc_settings_page');
	
}
add_action('admin_menu', 'tc_settings_menu');