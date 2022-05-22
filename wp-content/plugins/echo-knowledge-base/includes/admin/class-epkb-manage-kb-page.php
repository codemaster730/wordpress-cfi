<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Display Manage KB page 
 *
 * @copyright   Copyright (C) 2019, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_Manage_KB_Page {
	
	private $all_kb_configs = array(); // current configs, define after handle form actions
	private $message = array(); // error/warning/success messages 
	private $active_kb_tab = 0; // active KB tab on the left panel, not always current KB, int 
	private $active_action_tab = 'manage'; // active Action tab on the top panel, string
	private $export_link = array(); // link to the file for export

	function __construct() {

		// Handle manage kb buttons and other, set messages here
		$this->handle_form_actions();

		// get configs
		$this->all_kb_configs = epkb_get_instance()->kb_config_obj->get_kb_configs();

		// Default export/import
		add_action( 'eckb_manage_content_tab_body', array( $this, 'export_import_tabs_body' ), 10, 2 );

		// Define active tabs
		$this->active_kb_tab = EPKB_KB_Handler::get_current_kb_id();
		$this->active_kb_tab = empty($this->active_kb_tab) ? EPKB_KB_Config_DB::DEFAULT_KB_ID : $this->active_kb_tab;
		$this->active_kb_tab = isset( $this->all_kb_configs[$this->active_kb_tab] ) ? $this->active_kb_tab : EPKB_KB_Config_DB::DEFAULT_KB_ID;

		$this->active_action_tab = EPKB_Utilities::get('active_action_tab');
		$this->active_action_tab = empty($this->active_action_tab) ? 'manage' : $this->active_action_tab;
	}

	/**
	 * Display page body 
	 */
	public function display_manage_kb_page() {
		
		 // only administrators can handle this page
		if ( ! current_user_can( 'manage_options' ) ) {
			EPKB_Utilities::ajax_show_error_die(__( 'You do not have permission.', 'echo-knowledge-base' ));
		}

		// reset cache and get latest KB config
		epkb_get_instance()->kb_config_obj->reset_cache();
		$this->all_kb_configs = epkb_get_instance()->kb_config_obj->get_kb_configs();   ?>

		<!-- This is to catch WP JS stuff -->
		<div class="wrap">
			<h1></h1>
		</div>
		<div class=""></div>

		<div id="ekb-admin-page-wrap">

			<div class="epkb-manage-kb-container">
				<div class="epkb-manage-header"><?php $this->show_header(); ?></div>
					<div class="epkb-manage-tabs-container">
						<div class="epkb-manage-tabs__buttons">
							<div class="epkb-manage-tabs__header"></div>
							<?php $this->show_tabs_buttons(); ?>
						</div>

						<div class="epkb-manage-tabs__content">  <?php
							$kb_id = $this->active_kb_tab;
							$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( $kb_id );
							$this->show_tabs_body($kb_id, $kb_config);							?>
						</div>
				</div>
			</div>
		</div>		<?php

		// show any notifications
		foreach ( $this->message as $class => $message ) {
			echo  EPKB_Utilities::get_bottom_notice_message_box( $message, '', $class );
		}
	}

	private function show_header() { ?>
		<h1><?php _e( 'Manage Your KB(s)', 'echo-knowledge-base' ); ?></h1><?php 
		
		// create KB button, hook for MKB
		do_action( 'eckb_manage_show_header' );
	}

	/**
	 * Display each KB Tab
	 */
	private function show_tabs_buttons() {

		$tabs = array();		
		foreach ( $this->all_kb_configs as $kb_id => $kb_config ) {

			$is_kb_archived = EPKB_Utilities::is_kb_archived( $kb_config['status'] );
			$tabs[] = array(
				'kb_id' => $kb_id,
				'link' => EPKB_KB_Handler::get_first_kb_main_page_url( $kb_config ),
				'title' => $kb_config['kb_name'],
				'target' => '#kb_' . $kb_id,
				'url' =>  $is_kb_archived ? '#' : ('edit.php?post_type=' . EPKB_KB_Handler::get_post_type( $kb_id ) . '&page=epkb-manage-kb' ),
				'link_target' => ''
			);

		}

		if ( ! empty($tabs) ) {
			$keys = array_column($tabs, 'kb_id');
			array_multisort($keys, SORT_ASC, $tabs);
		}

		// add Get more button 
		if ( ! EPKB_Utilities::is_multiple_kbs_enabled() && count($this->all_kb_configs) == 1 ) {
			$tabs[] = array(
				'kb_id' => '0', // 0 for usual link, ID for tabs changing 
				'title' => __( 'Get Additional Knowledge Bases', 'echo-knowledge-base' ),
				'url' => 'https://www.echoknowledgebase.com/wordpress-plugin/multiple-knowledge-bases/',
				'target' => '',
				'link_target' => '_blank',
			);
		}

	   // if KB is archived then user can only activate it   ?>
		 <div id='epkb-activate-kb'>  <?php
			EPKB_Utilities::dialog_box_form( array(
			  	'id' => 'epkb-activate-kbs-popup',
			  	'title' => __( 'Activate KB', 'echo-knowledge-base' ),
			  	'body' => sprintf( __( 'This knowledge base is ARCHIVED.', 'echo-knowledge-base' )),
			    'form_inputs' => array(
					0 => '<input type="hidden" name="_wpnonce_manage_kbs" value="' . wp_create_nonce( "_wpnonce_manage_kbs" ) . '">',
					1 => '<input type="hidden" name="action" value="emkb_activate_knowledge_base_v2">',
					2 => '<input type="hidden" name="emkb_kb_id" value="">'
				),
			  	'accept_label' => __( 'Activate KB', 'echo-knowledge-base' ),
			  	'accept_type' => 'warning',
			) ); ?>
		 </div>  <?php

		// show tabs buttons 
		foreach ( $tabs as $tab ) { 
			$active_class = false;
			
			// add active class to current KB tab 
			if ( $this->active_kb_tab == $tab['kb_id'] ) {
				$active_class = 'active';
			} ?>
			
			<div class="epkb-manage-tabs__button <?php echo $tab['kb_id'] == 0 ? 'epkb-manage-tabs__button_mkb' : ''; ?> <?php echo $active_class; ?>">
				<a class="epkb-manage-tabs__button__title" href="<?php echo $tab['url']; ?>" data-kb_id="<?php echo $tab['kb_id']; ?>"
				   data-target="<?php echo $tab['target']; ?>" target="<?php echo $tab['link_target']; ?>"><?php echo $tab['title']; ?></a>
			</div>   <?php
		}
	}

	/**
	 * Show selected tab content.
	 * @param $kb_id
	 * @param $kb_config
	 */
	private function show_tabs_body( $kb_id, $kb_config ) {

		$active_class = $this->active_kb_tab == $kb_id ? 'active' : false;
		$page_url = 'edit.php?post_type=' . EPKB_KB_Handler::get_post_type( $kb_id );
		$HTML = New EPKB_HTML_Elements();		?>

		<div class="epkb-manage-content  <?php echo $active_class; ?>" id="kb_<?php echo $kb_id; ?>" data-kb_id="<?php echo $kb_id; ?>">
			<div class="epkb-manage-content__header"><?php
				$manage_active = ( ( $this->active_kb_tab == $kb_id && $this->active_action_tab == 'manage' ) || ( $this->active_kb_tab != $kb_id ) ) ? 'active' : ''; ?>

				<div class="epkb-manage-content__tab-button <?php echo $manage_active; ?>" data-target="#kb_<?php echo $kb_id; ?>_manage"><i class="epkbfa epkbfa-cubes"></i><?php esc_html_e( 'Manage', 'echo-knowledge-base' ); ?></div>

				<div class="epkb-manage-content__tab-button <?php echo ( $this->active_kb_tab == $kb_id && $this->active_action_tab == 'export' ) ? 'active' : ''; ?>" data-target="#kb_<?php echo $kb_id; ?>_export"><i class="epkbfa epkbfa-cogs"></i><?php esc_html_e( 'Export', 'echo-knowledge-base' ); ?></div>
				<div class="epkb-manage-content__tab-button <?php echo ( $this->active_kb_tab == $kb_id && $this->active_action_tab == 'import' ) ? 'active' : ''; ?>" data-target="#kb_<?php echo $kb_id; ?>_import"><i class="epkbfa epkbfa-cogs"></i><?php esc_html_e( 'Import', 'echo-knowledge-base' ); ?></div>

				<?php do_action( 'eckb_manage_content_tab_header', $kb_id, $kb_config ); ?>
			</div>
			<div class="epkb-manage-content__tabs"><?php
			
				$active = ( ( $this->active_kb_tab == $kb_id && $this->active_action_tab == 'manage' )
							|| ( $this->active_kb_tab != $kb_id ) ) ? 'active' : ''; ?>

				<div id="kb_<?php echo $kb_id; ?>_manage" class="epkb-manage-content__tab <?php echo $active; ?>"><?php

					if ( $kb_config['status'] == 'archived' ) {
						$icon ='ep_font_icon_error_circle';
					}	else if ( $kb_config['status'] == 'published' ) {
						$icon ='ep_font_icon_checkmark';
					} else {
						$icon ='ep_font_icon_error_circle';
					}
					
					$link = EPKB_KB_Handler::get_first_kb_main_page_url( $kb_config );
					$edit_link = $link ? get_admin_url( null, 'post.php?post=' . url_to_postid( $link ) . '&action=edit' ) : '';
					$button_class = "epkb-aibb-btn--blue";
					$link_output =  __( "View this KB", "echo-knowledge-base" ) . '&nbsp;&nbsp;<i class="ep_font_icon_external_link"></i>';
					if ( empty($link) ) {
						$link = esc_url( admin_url('edit.php?post_type=' . EPKB_KB_Handler::get_post_type( $kb_config['id'] ) ) );
						$link_output =  __( "Add Shortcode", "echo-knowledge-base" ) . '&nbsp;&nbsp;<i class="epkbfa epkbfa-exclamation-triangle"></i>';
						$button_class = "epkb-aibb-btn--red";
					}
					if ( empty( $kb_config['kb_main_pages'] ) ) {
						EPKB_Utilities::kb_page_with_shortcode_missing_msg( $kb_config );
					}					?>

					<div class="epkb-admin-row epkb-admin-2col">						<?php

						$HTML->page_info_section( 'epkbfa epkbfa-graduation-cap', 'Status',
							'<span class="epkb-manage-kb-id">'.sprintf(__("ID #%d", "echo-knowledge-base"), $kb_config['id']) . '</span><span class="epkb-manage-kb-status"><i class="'.$icon.'"></i>'.ucfirst($kb_config['status']).'</span>',
							$link_output, $link, $button_class, $edit_link ? __("Edit Page with KB Shortcode") . '&nbsp;&nbsp;<i class="ep_font_icon_external_link"></i>' : '',
						$edit_link );						?>

						<!--Rename Box -->
						<div class="epkb-admin-info-box epkb-admin-info-box__rename">

							<div class="epkb-admin-info-box__header">
								<div class="epkb-admin-info-box__header__icon epkbfa epkbfa-pencil-square-o"></div>
								<div class="epkb-admin-info-box__header__title"><?php _e( 'Rename', 'echo-knowledge-base'); ?></div>
							</div>

							<div class="epkb-admin-info-box__body">

								<div class="epkb-admin__kb-name  epkb-admin__kb-rename">
									<div class="epkb-admin__kb-rename_label"><b><?php echo __( 'KB Name: ', 'echo-knowledge-base') ?></b><?php echo $kb_config['kb_name']; ?>
									<span class="epkb-edit-toggle"><i class="epkbfa epkbfa-pencil"></i></span></div>
									<div class="epkb-admin__kb-rename_edit">
										<form method="post">
											<input type="hidden" name="_wpnonce_manage_kbs" value="<?php echo wp_create_nonce( "_wpnonce_manage_kbs" ); ?>"/>
											<input type="hidden" name="emkb_kb_id" value="<?php echo $kb_id; ?>"/>
											<input type="text"  name="epkb-kb-name-input" value="<?php echo $kb_config['kb_name']; ?>">
											<input type="hidden" name="action" value="epkb_update_kb_name">
											<input value="<?php echo __( 'Save', 'echo-knowledge-base'); ?>" type="submit" class="epkb-aibb-btn epkb-aibb-btn--blue">
										</form>
									</div>
								</div>									<?php

								$kb_page_id = EPKB_KB_Handler::get_first_kb_main_page_id($kb_config);
								if ( ! empty( $kb_page_id ) ) { ?>
									<div class="epkb-admin__kb-page-title  epkb-admin__kb-rename">
										<div class="epkb-admin__kb-rename_label"><b><?php echo __( 'KB Page Title: ', 'echo-knowledge-base') ?></b><?php echo $kb_config['kb_main_pages'][$kb_page_id]; ?>
											<span class="epkb-edit-toggle"><i class="epkbfa epkbfa-pencil"></i></span>
										</div>
										<div class="epkb-admin__kb-rename_edit">
											<form method="post">
												<input type="hidden" name="_wpnonce_manage_kbs" value="<?php echo wp_create_nonce( "_wpnonce_manage_kbs" ); ?>"/>
												<input type="hidden" name="emkb_kb_id" value="<?php echo $kb_id; ?>"/>
												<input type="hidden" name="epkb_page_id" value="<?php echo $kb_page_id; ?>" />
												<input type="text"  name="epkb-kb-page-title-input" value="<?php echo $kb_config['kb_main_pages'][$kb_page_id]; ?>">
												<input type="hidden" name="action" value="epkb_update_kb_page_title">
												<input value="<?php echo __( 'Save', 'echo-knowledge-base'); ?>" type="submit" class="epkb-aibb-btn epkb-aibb-btn--blue">
											</form>
										</div>
									</div>									<?php
								}      ?>
								</div>

							</div>
					</div>

					<div class="epkb-admin-row epkb-admin-2col">						<?php
						$HTML->page_info_section(
								'epkbfa epkbfa-code',
								'KB Shortcode',
								'To Display a Knowledge Base Main page, add the following KB shortcode to any page: <br/><strong>[epkb-knowledge-base id='.$kb_id.']</strong> ',
								'',
								'' );

						$HTML->page_info_section(
							'epkbfa epkbfa-globe',
							'Global Wizard',
							'To view and configure KB Main Page and URLs see',
							'Global Wizard',
							 admin_url( $page_url ) . '&page=epkb-kb-configuration&wizard-global');						?>
					</div>

					<div class="epkb-admin-row">
                   <?php do_action( 'eckb_manage_content_tab_body_manage', $kb_id, $kb_config ); ?>
					</div>

				</div>

				<?php do_action( 'eckb_manage_content_tab_body', $kb_id, $kb_config ); ?>
			</div>
		</div>   <?php
	}

	/**
	 * Tabs for import and export
	 * @param $kb_id
	 * @param $kb_config
	 */
	public function export_import_tabs_body ( $kb_id, $kb_config ) { ?>

		<div id="kb_<?php echo $kb_id; ?>_export" class="epkb-manage-content__tab <?php echo ( $this->active_kb_tab == $kb_id && $this->active_action_tab == 'export' ) ? 'active' : ''; ?>  epkb-manage-content__tab--export">
			<?php $this->display_export_tab( $kb_id, $kb_config ); ?>
		</div>
		
		<div id="kb_<?php echo $kb_id; ?>_import" class="epkb-manage-content__tab <?php echo ( $this->active_kb_tab == $kb_id && $this->active_action_tab == 'import' ) ? 'active' : ''; ?>  epkb-manage-content__tab--import">
			<?php $this->display_import_tab( $kb_id, $kb_config ); ?>
		</div>   <?php
	}

	public function display_import_tab( $kb_id, $kb_config ) {	?>

		<div class="epkb-manage-content epkb-manage-content__inner_tab active">
			<div class="epkb-manage-content__header">
				<div class="epkb-manage-content__tab-button active" data-target="#kb_<?php echo $kb_id; ?>_import_config"><i class="epkbfa epkbfa-file-code-o"></i><?php esc_html_e( 'Import KB Configuration', 'echo-knowledge-base' ); ?></div>
				<?php  do_action('eckb_manage_content_import_tab_header', $kb_id, $kb_config ); ?>

			</div>
			<div class="epkb-manage-content__tabs">
				<div class="epkb-manage-content__tab active" id="kb_<?php echo $kb_id; ?>_import_config">
					<div class="epkb-admin-row epkb-admin-2col">
						<?php $this->display_import_tab_config($kb_id, $kb_config); ?>
						<?php $this->display_import_ad(); ?>
					</div>
				</div>
				<?php do_action('eckb_manage_content_import_tab_body', $kb_id, $kb_config ); ?>
			</div>
		</div>		<?php
	}

	public function display_import_tab_config ( $kb_id, $kb_config ) { ?>
		<div class="epkb-admin-info-box">

			<div class="epkb-admin-info-box__header">
				<div class="epkb-admin-info-box__header__icon epkbfa epkbfa-cogs"></div>
				<div class="epkb-admin-info-box__header__title"><?php _e( 'Import KB Configuration', 'echo-knowledge-base'); ?></div>
			</div>

			<div class="epkb-admin-info-box__body">
				<p><?php echo  __( 'This import will overwrite the following KB settings:', 'echo-knowledge-base' ); ?></p>
				<?php $this->display_import_export_info(); ?>
				<form class="epkb-import-kbs" action="<?php echo esc_url( add_query_arg( array( 'active_kb_tab' => $kb_id, 'active_action_tab' => 'import' ) ) ); ?>" method="post" enctype="multipart/form-data">
					<input type="hidden" name="_wpnonce_manage_kbs" value="<?php echo wp_create_nonce( "_wpnonce_manage_kbs" ); ?>"/>
					<input type="hidden" name="action" value="epkb_import_knowledge_base"/>
					<input type="hidden" name="emkb_kb_id" value="<?php echo $kb_id; ?>"/>
					<input class="epkb-form-label__input epkb-form-label__input--text" type="file" name="import_file" required><br>
					<input type="submit" class="epkb-aibb-btn epkb-aibb-btn--blue" value="<?php echo  __( 'Import Configuration', 'echo-knowledge-base' ); ?>" /><br/>
				</form>

			</div>

		</div>	<?php
	}

	public function display_import_ad() {

		if ( EPKB_Utilities::is_export_import_enabled() ) {
			return;
		}

		$HTML = New EPKB_HTML_Elements();

		$HTML->advertisement_ad_box( array(
				'icon'              => 'epkbfa-linode',
				'title'             => 'Import / Export Add-on',
				'img_url'           => 'https://www.echoknowledgebase.com/wp-content/uploads/edd/2020/08/KB-Import-Export-Banner.jpg',
				'desc'              => 'Import articles and categories into your knowledge base.',
				'list'              => array(
					'Import articles from another knowledge base software',
					'Generate articles from different content sources and systems',
					'Use CSV as a quick way to add short articles'
				),
				'btn_text'          => 'Buy Now!',
				'btn_url'           => 'https://www.echoknowledgebase.com/wordpress-plugin/kb-import-export/',
				'btn_color'         => 'yellow',

				'more_info_text'    => 'More Information',
				'more_info_url'     => 'https://www.echoknowledgebase.com/documentation/import-articles/',
				'more_info_color'   => 'orange',
				'box_type'			   => 'new-feature',
		));
	}

	public function display_export_tab( $kb_id, $kb_config ) {	?>

		<div class="epkb-manage-content epkb-manage-content__inner_tab active">
			<div class="epkb-manage-content__header">
				<div class="epkb-manage-content__tab-button active" data-target="#kb_<?php echo $kb_id; ?>_export_config"><i class="epkbfa epkbfa-file-code-o"></i><?php esc_html_e( 'Export KB Configuration', 'echo-knowledge-base' ); ?></div>
				<?php  do_action('eckb_manage_content_export_tab_header', $kb_id, $kb_config ); ?>

			</div>
			<div class="epkb-manage-content__tabs">
				<div class="epkb-manage-content__tab active" id="kb_<?php echo $kb_id; ?>_export_config">
					<div class="epkb-admin-row epkb-admin-2col">
						<?php $this->display_export_tab_config($kb_id, $kb_config); ?>
					</div>
				</div>
				<?php do_action('eckb_manage_content_export_tab_body', $kb_id, $kb_config ); ?>
			</div>
		</div>		<?php
	}

	public function display_export_tab_config( $kb_id, $kb_config ) {		?>

		<div class="epkb-admin-info-box">

			<div class="epkb-admin-info-box__header">
				<div class="epkb-admin-info-box__header__icon epkbfa epkbfa-cogs"></div>
				<div class="epkb-admin-info-box__header__title"><?php _e( 'Export KB Configuration', 'echo-knowledge-base'); ?></div>
			</div>

			<div class="epkb-admin-info-box__body">
				<p><?php _e( 'This will export the following KB settings:', 'echo-knowledge-base'); ?></p>
				<?php $this->display_import_export_info(); ?>
				<form class="epkb-export-kbs" action="<?php echo esc_url( add_query_arg( array( 'active_kb_tab' => $kb_id, 'active_action_tab' => 'export' ) ) ); ?>" method="post">
					<p><?php _e( 'You can export KB and add-ons configuration.', 'echo-knowledge-base'); ?></p>
					<input type="hidden" name="_wpnonce_manage_kbs" value="<?php echo wp_create_nonce( "_wpnonce_manage_kbs" ); ?>"/>
					<input type="hidden" name="action" value="epkb_export_knowledge_base"/>
					<input type="hidden" name="emkb_kb_id" value="<?php echo $kb_id; ?>"/>
					<input type="submit" class="epkb-aibb-btn epkb-aibb-btn--blue" value="<?php echo  __( 'Export Configuration', 'echo-knowledge-base' ); ?>" /><br/>
					<?php if ( !empty ( $this->export_link[$kb_id] ) ) { ?>
						<a href="<?php echo $this->export_link[$kb_id]; ?>" download class="epkb_download_export_link info-btn"><?php _e( 'Download Export File', 'echo-knowledge-base' ); ?></a>
					<?php } ?>
				</form>
			</div>
		</div>		<?php
	}

	// Handle actions that need reload of the page - manage tab and other from addons
	private function handle_form_actions() {
		
		if ( empty( $_REQUEST['action']) ) {
			return;
		}

		// clear any messages
		$this->message = array();
		
		// verify that request is authentic
		if ( ! isset( $_REQUEST['_wpnonce_manage_kbs'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce_manage_kbs'], '_wpnonce_manage_kbs' ) ) {
			$this->message['error'] = __( 'Something went wrong', 'echo-knowledge-base' ) . ' (1)';
			return;
		}
		
		// ensure user has correct permissions
		if ( ! current_user_can( 'manage_options' ) ) {
			$this->message['error'] = __( 'You do not have permission.', 'echo-knowledge-base' );
			return;
		}
		
		// retrieve KB ID we are saving
		$kb_id = empty($_POST['emkb_kb_id']) ? '' : EPKB_Utilities::sanitize_get_id( $_POST['emkb_kb_id'] );
		if ( empty($kb_id) || is_wp_error( $kb_id ) ) {
			EPKB_Logging::add_log("received invalid kb_id when archiving/deleting KB", $kb_id );
			$this->message['error'] = __( 'Something went wrong', 'echo-knowledge-base' ) . ' (2)';
			return;
		}
		
		// retrieve current KB configuration
		$current_config = epkb_get_instance()->kb_config_obj->get_kb_config( $kb_id );
		if ( is_wp_error( $current_config ) ) {
			EPKB_Logging::add_log("Could not retrieve KB config when manage KB", $kb_id );
			$this->message['error'] = __( 'Something went wrong', 'echo-knowledge-base' ) . ' (5)';
			return;
		}

		// Update KB Name
		if ( EPKB_Utilities::post('action') == 'epkb_update_kb_name' && ! empty(EPKB_Utilities::post('epkb-kb-name-input')) ) {

			$result = epkb_get_instance()->kb_config_obj->set_value( $kb_id, 'kb_name', EPKB_Utilities::post('epkb-kb-name-input') );
			if ( is_wp_error( $result ) ) {
				$this->message['error'] = __( 'Something went wrong', 'echo-knowledge-base' ) . '(65)';
				return;
			}

			$this->message['success'] = __( 'KB Name Updated', 'echo-knowledge-base' ) ;
			return;
		}

		// Update KB Page Title
		if ( EPKB_Utilities::post('action') == 'epkb_update_kb_page_title' && ! empty(EPKB_Utilities::post('epkb-kb-page-title-input')) ) {

			$epkb_page_id = EPKB_Utilities::post('epkb_page_id');
			if ( empty($epkb_page_id) ) {
			  $this->message['error'] = __( 'Something went wrong', 'echo-knowledge-base' ) . ' (66)';
			  return;
			}

			// sanitize and save configuration in the database
			$kb_main_pages = $current_config['kb_main_pages'];
			$kb_main_pages[$epkb_page_id] = EPKB_Utilities::post('epkb-kb-page-title-input');

			$result = epkb_get_instance()->kb_config_obj->set_value( $kb_id, 'kb_main_pages', $kb_main_pages );
			if ( is_wp_error( $result ) ) {
				$this->message['error'] = __( 'Something went wrong', 'echo-knowledge-base' ) . ' (67)';
				return;
			}

			$post = get_post( $epkb_page_id );
			if ( empty($post) || empty($post->post_title) ) {
			  $this->message['error'] = __( 'Something went wrong', 'echo-knowledge-base' ) . ' (68)';
			  return;
			}

			$post->post_title = EPKB_Utilities::post('epkb-kb-page-title-input');
			$result = wp_update_post( $post );
			if ( is_wp_error( $result ) ) {
				EPKB_Logging::add_log( ' Could not update KB Main Page post title', $result );
				$this->message['error'] = __( 'Something went wrong', 'echo-knowledge-base' ) . ' (69)';
				return;
			}

			$this->message['success'] = __( 'KB Page Title Updated', 'echo-knowledge-base' ) ;
			return;
		}

		// EXPORT CONFIG
		if ( EPKB_Utilities::post( 'action' ) == 'epkb_export_knowledge_base' ) {
			$export = new EPKB_Export_Import();
			$this->message = $export->download_export_file( $kb_id );
			if ( empty($this->message) ) {
				exit;
			}
			return;
		}

		// IMPORT CONFIG
		if ( EPKB_Utilities::post( 'action' ) == 'epkb_import_knowledge_base' ) {
			$import = new EPKB_Export_Import();
			$this->message = $import->import_kb_config( $kb_id );
			return;
		}

		$this->message = apply_filters( 'eckb_handle_manage_kb_actions', $this->message, $kb_id, $current_config );
		$this->message = empty($this->message) ? [] : $this->message;
	}

	/**
	 * Check do we need to show CORE kbs page 
	 */
	public static function is_show_core_kbs_page() {
		
		if ( ! EPKB_Utilities::is_multiple_kbs_enabled() ) {
			return true;
		}
		
		if ( class_exists('Echo_Multiple_Knowledge_Bases') && version_compare( Echo_Multiple_Knowledge_Bases::$version, '1.11.1', '>' ) ) {
			return true;
		}
		
		return false;
	}

	private function display_import_export_info() {		?>
		<ul>
			<li><?php _e('Configuration for all text, styles, features.', 'echo-knowledge-base'); ?></li>
			<li><?php _e('Configuration for all add-ons.', 'echo-knowledge-base'); ?></li>
		</ul>
		<p><?php _e('Instructions:', 'echo-knowledge-base'); ?></p>
		<ul>
			<li><?php _e('Test import and export on your staging or test site before importing configuration in production.', 'echo-knowledge-base'); ?></li>
			<li><?php _e('Always back up your database before starting the import.', 'echo-knowledge-base'); ?></li>
			<li><?php _e('Preferably run import outside of business hours.', 'echo-knowledge-base'); ?></li>
		</ul>		<?php
	}
}