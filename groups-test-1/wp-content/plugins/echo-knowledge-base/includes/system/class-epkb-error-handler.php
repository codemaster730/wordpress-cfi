<?php
/**
 * Notices for js errors 
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_Error_Handler {

	public function __construct() {

		// add script to the page
		add_action( 'admin_enqueue_scripts', [ 'EPKB_Error_Handler', 'add_assets' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'add_assets' ] );

		// add message to the page
		add_action( 'admin_footer', [ $this, 'add_error_popup' ] );
		add_action( 'wp_footer', [ $this, 'add_error_popup' ] );
   }
	
	public static function add_assets() { ?>
		<link rel="stylesheet" id="epkb-js-error-handlers-css" href="<?php echo Echo_Knowledge_Base::$plugin_url . 'css/error-handlers.css'; ?>" media="all">
		<script src="<?php echo Echo_Knowledge_Base::$plugin_url . 'js/error-handlers.js'; ?>" type="text/javascript"></script><?php
	}

	/**
	 * Show JS errors caught by JS error handler
	 */
	public function add_error_popup() {
	   echo '
			<div style="display:none;" class="epkb-js-error-notice">
				<div class="epkb-js-error-close">&times;</div>
				<div class="epkb-js-error-title">' . __( 'We found a JS error on this page caused by a plugin:', 'epkb-knowledge-base' ) . '</div>
				<div class="epkb-js-error-body">
					<div class="epkb-js-error-msg"></div>' .
					__( 'in: ', 'epkb-knowledge-base' ) . '<div class="epkb-js-error-url"></div>' . __( 'file', 'epkb-knowledge-base' ) . '
				</div>
				<div>' . EPKB_Utilities::contact_us_for_support() . '</div>
				<div class="epkb-js-error-about">' . __( 'Check browser console for more information (F12)', 'epkb-knowledge-base' ) . '</div>
			</div>';
	}
}
