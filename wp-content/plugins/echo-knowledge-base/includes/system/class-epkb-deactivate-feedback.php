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
				<span id="epkb-deactivate-feedback-dialog-header-title"><?php echo esc_html__( 'Quick Feedback', 'echo-knowledge-base' ); ?></span>
			</div>
			<form id="epkb-deactivate-feedback-dialog-form" method="post">				<?php
				wp_nonce_field( '_epkb_deactivate_feedback_nonce' );				?>
				<input type="hidden" name="action" value="epkb_deactivate_feedback" />

				<div id="epkb-deactivate-feedback-dialog-form-caption"><?php echo esc_html__( 'If you have a moment, please share why you are deactivating KB:', 'echo-knowledge-base' ); ?></div>
				<div id="epkb-deactivate-feedback-dialog-form-body">
					<div id="epkb-deactivate-feedback-dialog-form-error" style="display:none;"><?php echo esc_html__( 'Please Select an Option', 'echo-knowledge-base' ); ?></div>					<?php

						foreach ( $deactivate_reasons as $reason_key => $reason_escaped_html ) :		?>
							<div class="epkb-deactivate-feedback-dialog-input-wrapper">
								<input id="epkb-deactivate-feedback-<?php echo esc_attr( $reason_key ); ?>" class="epkb-deactivate-feedback-dialog-input" type="radio" name="reason_key" value="<?php echo esc_attr( $reason_key ); ?>" />
								<label for="epkb-deactivate-feedback-<?php echo esc_attr( $reason_key ); ?>" class="epkb-deactivate-feedback-dialog-label"><?php echo esc_html( $reason_escaped_html['title'] ); ?></label>
								<?php if ( ! empty( $reason_escaped_html['escaped_alert_html'] ) ) : ?>
									<div class="epkb-feedback-text"><?php echo  $reason_escaped_html['escaped_alert_html']; ?></div>
								<?php endif; ?>
								<?php if ( ! empty( $reason_escaped_html['input_placeholder'] ) ) : ?>
											 <input class="epkb-feedback-text" type="text" name="reason_<?php echo esc_attr( $reason_key ); ?>" placeholder="<?php echo esc_attr( $reason_escaped_html['input_placeholder'] ); ?>" />
								<?php endif; ?>
								<?php if ( ! empty( $reason_escaped_html['contact_me'] ) ) : ?>
									<div class="epkb-feedback-checkbox">
										<input id="epkb-deactivate-feedback-contact" class="epkb-deactivate-feedback-dialog-input" type="checkbox" name="contact_me_<?php echo esc_attr( $reason_key ); ?>" value="yes" />
										<label for="epkb-deactivate-feedback-contact" class="epkb-deactivate-feedback-dialog-label"><?php echo esc_html__( 'Contact Me', 'echo-knowledge-base' ); ?></label>
									</div>
								<?php endif; ?>
								<?php if ( ! empty( $reason_escaped_html['button'] ) ) : ?>
									<div class="epkb-feedback-button"><a class="epkb-feedback-button__green" target="_blank" href="<?php echo esc_url( $reason_escaped_html['button']['url'] ); ?>"><?php echo esc_html( $reason_escaped_html['button']['title'] ); ?></a></div>
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

		$wpnonce_value = EPKB_Utilities::post( '_wpnonce' );
		if ( empty( $wpnonce_value ) || ! wp_verify_nonce( $wpnonce_value, '_epkb_deactivate_feedback_nonce' ) ) {
			wp_send_json_error();
		}

		$reason_type = EPKB_Utilities::post( 'reason_key', 'N/A' );
		$reason_input = EPKB_Utilities::post( "reason_{$reason_type}", 'N/A' );
		$first_version = get_option( 'epkb_version_first' );
		$contact_user = isset( $_POST["contact_me_{$reason_type}"] ) ? 'Yes' : 'No';

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
			'contact_user'      => $contact_email . ' - ' . $contact_user
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
			// not translations
			$subject = esc_html( 'Plugin Deactivation' );
			$message =  esc_html( 'Name' ) . ': ' . esc_html( $first_name ) . ' \r\n' .
				esc_html( 'Email' ) . ': ' . esc_html( $contact_email ) . ' \r\n' .
				esc_html( 'Feedback Type' ) . ': ' . esc_html( $reason_type ) . ' \r\n' .
				esc_html( 'Feedback Input' ) . ': ' . esc_html( $reason_input );

			// send the email
			EPKB_Utilities::send_email( $message, 'support@echoplugins.freshdesk.com', $contact_email, $first_name, $subject );
		}

		wp_send_json_success();
	}

	private function get_deactivate_reasons( $type ) {

		switch ( $type ) {
		   case 1:
		   	    $deactivate_reasons = [
				  'missing_feature' => [
					  'title' => __( 'I\'m missing a feature', 'echo-knowledge-base' ),
					  'input_placeholder' => __( 'Please tell us what is missing', 'echo-knowledge-base' ),
					  'contact_me' => true,
				  ],
				  'couldnt_get_the_plugin_to_work' => [
					  'title' => __( 'I couldn\'t get the plugin to work', 'echo-knowledge-base' ),
					  'input_placeholder' => __( 'Please share the reason', 'echo-knowledge-base' ),
					  //'escaped_alert_html' => sprintf( __( 'We can help you, usually within an hour! If this is our bug, we will give you our %s for free.', 'echo-knowledge-base' ), '<a href="' . $pro_link . '" target="_blank">PRO version</a>'),
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
					  'input_placeholder' => ''
				  ],
					'missing_feature' => [
					   'title' => __( 'I\'m missing a feature', 'echo-knowledge-base' ),
					   'input_placeholder' => __( 'Please tell us what is missing', 'echo-knowledge-base' ),
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
}
