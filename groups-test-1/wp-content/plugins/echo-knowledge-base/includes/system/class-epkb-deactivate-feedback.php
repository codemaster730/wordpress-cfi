<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * If user is deactivating plugin, find out why
 */
class EPKB_Deactivate_Feedback {

	public function __construct() {
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_feedback_dialog_scripts' ] );
		add_action( 'wp_ajax_epkb_deactivate_feedback', [ $this, 'ajax_epkb_deactivate_feedback' ] );
	}

	/**
	 * Enqueue feedback dialog scripts.
	 */
	public function enqueue_feedback_dialog_scripts() {
		add_action( 'admin_footer', [ $this, 'output_deactivate_feedback_dialog' ] );

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_register_script( 'epkb-dialog', Echo_Knowledge_Base::$plugin_url . 'js/lib/dialog' . $suffix . '.js', array('jquery'), Echo_Knowledge_Base::$version );
		wp_register_script( 'epkb-admin-feedback', Echo_Knowledge_Base::$plugin_url . 'js/admin-feedback' . $suffix . '.js', array('jquery'), Echo_Knowledge_Base::$version );
		wp_register_style( 'epkb-admin-feedback-style', Echo_Knowledge_Base::$plugin_url . 'css/admin-plugin-feedback' . $suffix . '.css', array(), Echo_Knowledge_Base::$version );

		wp_enqueue_script( 'epkb-dialog' );
		wp_enqueue_script( 'epkb-admin-feedback' );
		wp_enqueue_style( 'epkb-admin-feedback-style' );
	}

	/**
	 * Display a dialog box to ask the user why they deactivated the KB.
	 */
	public function output_deactivate_feedback_dialog() {

		$first_version = get_option('epkb_version_first');
		$current_version = get_option('epkb_version');
		if ( version_compare( $first_version, $current_version, '==' ) ) {
			$deactivate_reasons = $this->get_deactivate_reasons( 1 );
		} else {
			$deactivate_reasons = $this->get_deactivate_reasons( 2 );
		} 	?>

		<div id="epkb-deactivate-feedback-dialog-wrapper">
			<div id="epkb-deactivate-feedback-dialog-header">
				<span id="epkb-deactivate-feedback-dialog-header-title"><?php echo __( 'Quick Feedback', 'echo-knowledge-base' ); ?></span>
			</div>
			<form id="epkb-deactivate-feedback-dialog-form" method="post">				<?php
				wp_nonce_field( '_epkb_deactivate_feedback_nonce' );				?>
				<input type="hidden" name="action" value="epkb_deactivate_feedback" />

				<div id="epkb-deactivate-feedback-dialog-form-caption"><?php echo __( 'If you have a moment, please share why you are deactivating KB:', 'echo-knowledge-base' ); ?></div>
				<div id="epkb-deactivate-feedback-dialog-form-body">
					<div id="epkb-deactivate-feedback-dialog-form-error" style="display:none;"><?php echo __( 'Please Select an Option', 'echo-knowledge-base' ); ?></div>					<?php

						foreach ( $deactivate_reasons as $reason_key => $reason ) :		?>
							<div class="epkb-deactivate-feedback-dialog-input-wrapper">
								<input id="epkb-deactivate-feedback-<?php echo esc_attr( $reason_key ); ?>" class="epkb-deactivate-feedback-dialog-input" type="radio" name="reason_key" value="<?php echo esc_attr( $reason_key ); ?>" />
								<label for="epkb-deactivate-feedback-<?php echo esc_attr( $reason_key ); ?>" class="epkb-deactivate-feedback-dialog-label"><?php echo esc_html( $reason['title'] ); ?></label>
								<?php if ( ! empty( $reason['alert'] ) ) : ?>
									<div class="epkb-feedback-text"><?php echo $reason['alert']; ?></div>
								<?php endif; ?>
								<?php if ( ! empty( $reason['input_placeholder'] ) ) : ?>
											 <input class="epkb-feedback-text" type="text" name="reason_<?php echo esc_attr( $reason_key ); ?>" placeholder="<?php echo esc_attr( $reason['input_placeholder'] ); ?>" />
								<?php endif; ?>
								<?php if ( ! empty( $reason['contact_me'] ) ) : ?>
									<div class="epkb-feedback-checkbox">
										<input id="epkb-deactivate-feedback-contact" class="epkb-deactivate-feedback-dialog-input" type="checkbox" name="contact_me_<?php echo esc_attr( $reason_key ); ?>" value="yes" /><label for="epkb-deactivate-feedback-contact" class="epkb-deactivate-feedback-dialog-label"><?php echo __( 'Contact Me', 'echo-knowledge-base' ); ?></label>
									</div>
								<?php endif; ?>
								<?php if ( ! empty( $reason['button'] ) ) : ?>
									<div class="epkb-feedback-button"><a class="epkb-feedback-button__green" target="_blank" href="<?php echo $reason['button']['url']; ?>"><?php echo $reason['button']['title']; ?></a></div>
								<?php endif; ?>
							</div>					<?php
						endforeach; ?>

				</div>
			</form>
		</div>		<?php
	}

	/**
	 * Send the user feedback when KB is deactivated.
	 */
	public function ajax_epkb_deactivate_feedback() {
		global $wp_version;

		if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], '_epkb_deactivate_feedback_nonce' ) ) {
			wp_send_json_error();
		}

		$reason_type = empty($_POST['reason_key']) ? 'N/A' : $_POST['reason_key'];
		$reason_input = empty($_POST["reason_{$reason_type}"]) ? 'N/A' : sanitize_text_field( $_POST[ "reason_{$reason_type}" ] );
		$first_version = get_option('epkb_version_first');
		$contact_user = isset($_POST["contact_me_{$reason_type}"]) ? 'Yes' : 'No';

		$contact_email = '';
		if ( $contact_user == 'Yes' ) {
			$user = EPKB_Utilities::get_current_user();
			$contact_email = empty($user) ? '' : $user->user_email;
		}

		//Theme Name and Version
		$active_theme = wp_get_theme();
		$theme_info = $active_theme->get( 'Name' ) . ' ' . $active_theme->get( 'Version' );
		
		// send feedback
		$api_params = array(
			'epkb_action'       => 'epkb_process_user_feedback',
			'feedback_type'     => $reason_type,
			'feedback_input'    => $reason_input,
			'plugin_name'       => 'KB',
			'plugin_version'    => class_exists('Echo_Knowledge_Base') ? Echo_Knowledge_Base::$version : 'N/A',
			'first_version'     => empty($first_version) ? 'N/A' : $first_version,
			'wp_version'        => $wp_version,
			'theme_info'        => $theme_info,
			'contact_user'      => $contact_email
		);

		// Call the API
		wp_remote_post(
			esc_url_raw( add_query_arg( $api_params, 'https://www.echoknowledgebase.com' ) ),
			array(
				'timeout'   => 15,
				'body'      => $api_params,
				'sslverify' => false
			)
		);

		if ( $contact_user == 'Yes' ) { 
			$user = EPKB_Utilities::get_current_user();
			$first_name = $user->first_name;
			$subject = __( 'Plugin Deactivation', 'echo-knowledge-base' );
			$message =  __( 'Name', 'echo-knowledge-base' ) . ': ' . $first_name . ' <br/>' .
				__( 'Email', 'echo-knowledge-base' ) . ': ' . $contact_email . ' <br/>' .
				__( 'Feedback Type', 'echo-knowledge-base' ) . ': ' . $reason_type . ' <br/>' .
				__( 'Feedback Input', 'echo-knowledge-base' ) . ': ' . $reason_input;

			// send the email
			EPKB_Utilities::send_email( $message, 'support@echoplugins.freshdesk.com', $contact_email, $first_name, $subject, __( 'Plugin Deactivation', 'echo-knowledge-base' ) );
		}

		wp_send_json_success();
	}

	private function get_deactivate_reasons( $type ) {

		//$pro_link = 'https://www.echoknowledgebase.com/bundle-pricing/?utm_source=plugin&utm_medium=readme&utm_content=home&utm_campaign=pro-bundle';

		switch ( $type ) {
		   case 1:
		   	    $deactivate_reasons = [
				  'missing_feature' => [
					  'title' => __( 'I\'m missing a feature', 'echo-knowledge-base' ),
					  'input_placeholder' => __( 'Please tell us what is missing', 'echo-knowledge-base' ),
					  'alert' => EPKB_Deactivate_Feedback::get_features_html(),
					  'contact_me' => true,
				  ],
				  'couldnt_get_the_plugin_to_work' => [
					  'title' => __( 'I couldn\'t get the plugin to work', 'echo-knowledge-base' ),
					  'input_placeholder' => __( 'Please share the reason', 'echo-knowledge-base' ),
					  //'alert' => sprintf( __( 'We can help you, usually within an hour! If this is our bug, we will give you our %s for free.', 'echo-knowledge-base' ), '<a href="' . $pro_link . '" target="_blank">PRO version</a>'),
					  'contact_me' => true,
				  ],
				  'other' => [
					  'title' => __( 'Other', 'echo-knowledge-base' ),
					  'input_placeholder' => __( 'Please share the reason', 'echo-knowledge-base' ),
					  'button' => [
						  'title' => __( 'Contact us for Help', 'echo-knowledge-base' ),
						  'url' => 'https://www.echoknowledgebase.com/deactivation-technical-support/'
					  ]
				  ],
			   ];
			   break;
		    case 2:
			default:
				$deactivate_reasons = [
				  'no_longer_needed' => [
					  'title' => __( 'I no longer need the plugin', 'echo-knowledge-base' ),
					  'input_placeholder' => '',
					  'alert' => sprintf( __( 'Did you know we launched a new Elementor plugin for writing posts, articles, and documents?', 'echo-knowledge-base' ) . ' %s',
						                        '<a href="https://www.creative-addons.com/" target="_blank">Learn More <i class="epkbfa epkbfa-external-link"></i></a>' ),
					  
				  ],
					'missing_feature' => [
					   'title' => __( 'I\'m missing a feature', 'echo-knowledge-base' ),
					   'input_placeholder' => __( 'Please tell us what is missing', 'echo-knowledge-base' ),
					   'alert' => EPKB_Deactivate_Feedback::get_features_html(),
						'contact_me' => true,
					],
				  'other' => [
					  'title' => __( 'Other', 'echo-knowledge-base' ),
					  'input_placeholder' => __( 'Please share the reason', 'echo-knowledge-base' ),
					  'button' => [
						  'title' => __( 'Contact us for Help', 'echo-knowledge-base' ),
						  'url' => 'https://www.echoknowledgebase.com/deactivation-technical-support/'
					  ]
				  ]
			   ];
			   break;
	   }

		return $deactivate_reasons;
	}

	private function get_features_html() {
		$features_html = '<div class="epkb-deactivate-features">';
		$features_html .= '<div class="epkb-deactivate-features__heading">' . __( 'We have these additional features in our addons', 'echo-knowledge-base' ) . '</div>';
		$features_html .= '<ul>
								<li><a href="https://www.echoknowledgebase.com/wordpress-plugin/advanced-search/" target="_blank">' . __( 'Advanced Search', 'echo-knowledge-base' ) . ' <i class="epkbfa epkbfa-external-link"></i></a> ' . __( '- analytics, tag search, more settings', 'echo-knowledge-base' ) . '</li>
								<li><a href="https://www.echoknowledgebase.com/wordpress-plugin/elegant-layouts/" target="_blank">' . __( 'More Layouts', 'echo-knowledge-base' ) . ' <i class="epkbfa epkbfa-external-link"></i></a> ' . __( '- sidebar, grid layout', 'echo-knowledge-base' ) . '</li>
								<li><a href="https://www.echoknowledgebase.com/wordpress-plugin/access-manager/" target="_blank">' . __( 'Access & Permissions', 'echo-knowledge-base' ) . ' <i class="epkbfa epkbfa-external-link"></i></a> ' . __( '- roles, groups, content restriction', 'echo-knowledge-base' ) . '</li>
								<li><a href="https://www.echoknowledgebase.com/bundle-pricing/" target="_blank">' . __( 'And more...', 'echo-knowledge-base' ) . ' <i class="epkbfa epkbfa-external-link"></i></a></li>
						  </ul>';
		$features_html .= '</div>';
		return $features_html;
	}
}
