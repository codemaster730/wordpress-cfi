<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Display wizard information that is displayed with KB Configuration page
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_KB_Config_Wizards {

	/**
	 * Display wizard Page
	 * @param $kb_id
	 * @param $is_active
	 */
	public static function display_page( $kb_id, $is_active ) { 

		// ensure user has correct permissions
		if ( ! current_user_can( EPKB_Utilities::EPKB_ADMIN_CAPABILITY ) ) {
			EPKB_Utilities::ajax_show_error_die( __( 'You do not have permission to edit this knowledge base', 'echo-knowledge-base' ) );
		}	?>
		
		<div class="epkb-old-wizards" id="epkb-old-config-wizards-content" <?php echo $is_active ? 'style="display: block;"' : ''; ?>>  <?php

			// ensure users have latest add-on
			if ( EPKB_KB_Wizard::is_wizard_disabled() ) {
				echo '<div class="epkb-wizard-error-note">' . __('Elegant Layouts, Advanced Search and Article Rating plugins need to be up to date. ', 'echo-knowledge-base') . EPKB_Utilities::contact_us_for_support() . '</div>';
				return;
			}

			$page_url = 'edit.php?post_type=' . EPKB_KB_Handler::get_post_type( $kb_id ); ?>

			<section class="epkb-wizards__row-2-col">				<?php
				self::display_wizard_box_1( array(
					'icon_class'    => 'epkbfa-paint-brush',
					'title'         => __( 'Styles and Colors Settings', 'echo-knowledge-base' ),
					'content'       => __( 'COLORS / LOOK / STYLE', 'echo-knowledge-base' ),
					'btn_text'      => __( 'Edit', 'echo-knowledge-base' ),
					'btn_url'       =>  admin_url( $page_url ) . '&page=epkb-kb-configuration&wizard-theme',
				));

				self::display_wizard_box_1( array(
						'icon_class'    => 'epkbfa-font',
						'title'         => __( 'Change Front End Text Labels', 'echo-knowledge-base' ),
						'content'       => __( 'ALL TEXT CHANGES', 'echo-knowledge-base' ),
						'btn_text'      => __( 'Edit', 'echo-knowledge-base' ),
						'btn_url'       => admin_url( $page_url ) . '&page=epkb-kb-configuration&wizard-text',
				)); 	?>
			</section>

			<section class="epkb-wizards__row-2-col">				<?php
				self::display_wizard_box_1( array(
					'icon_class'    => 'epkbfa-cog',
					'title'         => __( 'Features and Sidebars Settings', 'echo-knowledge-base' ),
					'content'       => __( 'Configure sidebars, TOC, breadcrumb, and more.', 'echo-knowledge-base' ),
					'btn_text'      => __( 'Edit', 'echo-knowledge-base' ),
					'btn_url'       => admin_url( $page_url ) . '&page=epkb-kb-configuration&wizard-features',
				));
				self::display_wizard_box_1( array(
						'icon_class'    => 'epkbfa-search',
						'title'         => __( 'Search Settings', 'echo-knowledge-base' ),
						'content'       => __( 'SEARCH BOX STYLE', 'echo-knowledge-base' ),
						'btn_text'      => __( 'Edit', 'echo-knowledge-base' ),
						'btn_url'       => admin_url( $page_url ) . '&page=epkb-kb-configuration&wizard-search'
					));     ?>
			</section>

		</div>	<?php
	}

	/**
	 * Show a box with Icon, Title, Description and Link
	 *
	 * @param $args array

	 * - ['icon_class']     Top Icon to display ( Choose between these available ones: https://fontawesome.com/v4.7.0/icons/ )
	 * - ['title']          H3 title of the box.
	 * - ['content']        Body content of the box.
	 * - ['btn_text']       Show button and the text of the button at the bottom of the box, if no text is defined no button will show up.
	 * - ['btn_url']        Button URL.
	 */
	public static function display_wizard_box_1( $args ) { ?>

		<div class="epkb-wizard-box-container_1">

			<!-- Header -------------------->
			<div class="epkb-wizard-box__header">
				<i class="epkb-wizard-box__header__icon epkbfa <?php echo $args['icon_class']; ?>"></i>
				<h3 class="epkb-wizard-box__header__title"><?php echo $args['title']; ?></h3>
			</div>

			<!-- Body ---------------------->
			<div class="epkb-wizard-box__body">
				<?php echo $args['content']; ?>
			</div>

			<!-- Footer ---------------------->
			<div class="epkb-wizard-box__footer">
					<a class="epkb-wizard-box__footer__button" href="<?php echo esc_url( $args['btn_url'] ); ?>"><?php echo $args['btn_text']; ?></a>
			</div>

		</div>	<?php
	}
}
