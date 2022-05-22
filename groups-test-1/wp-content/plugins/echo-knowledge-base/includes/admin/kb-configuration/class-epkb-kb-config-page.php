<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Display feature settings
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_KB_Config_Page {

	public $kb_config = array();
	/** @var  EPKB_KB_Config_Elements */
	public $form;
	public $feature_specs = array();
	public $kb_main_page_layout = EPKB_KB_Config_Layout_Basic::LAYOUT_NAME;
	public $kb_article_page_layout = EPKB_KB_Config_Layouts::KB_ARTICLE_PAGE_NO_LAYOUT;
	public $show_main_page = false;
	public $show_overview_page = true;
	public $show_wizard_page = false;
	public $can_save_config = true;

	public function __construct( $kb_config=array() ) {

		// ensure user has correct permissions
		if ( ! current_user_can( EPKB_Utilities::EPKB_ADMIN_CAPABILITY ) ) {
			EPKB_Utilities::ajax_show_error_die( __( 'You do not have permission to edit this knowledge base', 'echo-knowledge-base' ) );
		}

		// retrieve current KB configuration
		$kb_config = empty($kb_config) ? epkb_get_instance()->kb_config_obj->get_current_kb_configuration() : $kb_config;
		if ( is_wp_error( $kb_config ) || empty($kb_config) || ! is_array($kb_config) || count($kb_config) < 100 ) {
			$this->can_save_config = $kb_config;
			$kb_config = EPKB_KB_Config_Specs::get_default_kb_config( EPKB_KB_Config_DB::DEFAULT_KB_ID );
		}

		$this->kb_config              = $kb_config;
		$this->feature_specs          = EPKB_KB_Config_Specs::get_fields_specification( $this->kb_config['id'] );
		$this->form                   = new EPKB_KB_Config_Elements();
		$this->kb_main_page_layout    = EPKB_KB_Config_Layouts::get_kb_main_page_layout_name( $this->kb_config );
		$this->kb_article_page_layout = EPKB_KB_Config_Layouts::get_article_page_layout_name( $this->kb_config );
		$this->show_main_page         = isset($_REQUEST['epkb-demo']) || isset($_REQUEST['ekb-main-page']); // maybe deprecated
		
		if ( isset($_REQUEST['epkb-wizard-tab']) ) {
			$this->show_wizard_page = true;
			$this->show_overview_page = false;
		}
	}

	/**************************************************************************************
	 *
	 *                   MEGA MENU
	 * Prefix mm = mega menu
	 *************************************************************************************/

	public function display_mega_menu() {

		$setup_i18 = 'SETUP';
		$organize_i18 = 'ORGANIZE';
		$all_text_i18 = 'ALL TEXT';
		$tuning_i18 = 'TUNING';
		$features_i18 = 'FEATURES';

        echo '<div class="epkb-mm-sidebar">';

        $article_page_with_config_layout = $this->kb_article_page_layout != EPKB_KB_Config_Layouts::KB_ARTICLE_PAGE_NO_LAYOUT &&
                                           $this->kb_main_page_layout != EPKB_KB_Config_Layouts::SIDEBAR_LAYOUT;

        /********************************************************************
         * 1. display MAIN PAGE and ARTICLE PAGE and CATEGORY/TAG PAGE menu (right side)
         ********************************************************************/

        $main_page_core_links = array($setup_i18, $organize_i18, $all_text_i18, $tuning_i18);
        $this->mega_menu_sidebar_links( array(
            'id'            => 'eckb-mm-mp-links',
            'core_links'    => $main_page_core_links,
            'add_on_links'  => array( 'Article', 'Widgets', 'Table of Contents',
	                                    'Article Feedback', 'Analytics', 'Advanced Search' )
        ));

		if ( $this->kb_article_page_layout != EPKB_KB_Config_Layouts::KB_ARTICLE_PAGE_NO_LAYOUT ) {
			$article_page_core_links = array($setup_i18, $organize_i18, $features_i18, $tuning_i18);
		} else if ( $article_page_with_config_layout ) {
			$article_page_core_links = array($setup_i18, $features_i18, $all_text_i18, $tuning_i18);
		} else {
			$article_page_core_links = array($setup_i18, $features_i18, $tuning_i18);
		}

		// add ARTICLE PAGE menus from add-ons
        $article_page_add_on_links = apply_filters( 'epkb_kb_article_page_add_on_links', array(), $this->kb_article_page_layout, $this->kb_config );
		$article_page_all_links = empty($article_page_add_on_links) || ! is_array($article_page_add_on_links)
                                        ? $article_page_core_links : $article_page_core_links + array(9 => '&nbsp') + $article_page_add_on_links;

        $this->mega_menu_sidebar_links( array(
            'id'            => 'eckb-mm-ap-links',
            'core_links'    => $article_page_all_links,
            'add_on_links'  => ''
        ));

		$this->mega_menu_sidebar_links( array(
			'id'            => 'eckb-mm-arch-links',
			'core_links'    => array($setup_i18, $all_text_i18),
			'add_on_links'  => ''
		));

		echo '</div>';
	    echo '<div class="epkb-mm-content">';
        echo '<form id="epkb-config-config2">';

        // if add-on is deactivated (even temporarily) then set the Main Page layout to Basic
		if ( ! in_array($this->kb_main_page_layout, EPKB_KB_Config_Layouts::get_main_page_layout_names()) ) {
			$this->kb_config['kb_main_page_layout'] = EPKB_KB_Config_Layout_Basic::LAYOUT_NAME;
		}

        // if add-on is deactivated (even temporarily) then set the Article Page layout to Article
        $article_page_layouts = EPKB_KB_Config_Layouts::get_article_page_layouts( $this->kb_main_page_layout );
        if ( ! in_array($this->kb_config['kb_article_page_layout'], $article_page_layouts) ) {
            $this->kb_config['kb_article_page_layout'] = EPKB_KB_Config_Layouts::KB_ARTICLE_PAGE_NO_LAYOUT;
        }

        $grid_layout = $this->kb_main_page_layout == EPKB_KB_Config_Layouts::GRID_LAYOUT;
		$main_page_sbl = $this->kb_main_page_layout == EPKB_KB_Config_Layouts::SIDEBAR_LAYOUT;
		$categories_layout = $this->kb_main_page_layout == EPKB_KB_Config_Layouts::CATEGORIES_LAYOUT;

		// define Search Box or Advanced Search Box configuration menu
		$search_box_menu = array(
			'heading'   => 'Search Box',
			'links'     => array( 'Layout', 'Colors', 'Text', 'Advanced' )
		);
		$search_box_menu = apply_filters( 'eckb_search_box_configuration', $search_box_menu );

		$is_advanced_search = EPKB_Utilities::is_advanced_search_enabled( $this->kb_config );

        /********************************************************************
         * 2. display MAIN PAGE menu content (Right side)
         ********************************************************************/

        // MAIN PAGE - SETUP menu item
		$this->mega_menu_item_custom_html_content( array(
			'id'        => 'eckb-mm-mp-links-setup',
			'sections'  => array(
				array(
					'heading'       => '1. Layout',
					'form_elements' => array(
						array(
							'id'   => 'mega-menu-main-page-layout',
							'html' => $this->form->radio_buttons_vertical( array('label' => '') + $this->feature_specs['kb_main_page_layout'] + array(
											'current'           => $this->kb_main_page_layout,
											'input_group_class' => 'config-col-12',
											'main_label_class'  => 'config-col-4',
											'input_class'       => '',
											'radio_class'       => 'config-col-12' ) ) .
						          ( $this->kb_main_page_layout != EPKB_KB_Config_Layouts::GRID_LAYOUT ? '' :
							        $this->form->radio_buttons_vertical( array( 'label' => __( ' Category Links Go to:', 'echo-knowledge-base' ) ) + $this->feature_specs['kb_main_page_category_link'] + array(
									        'current'           => $this->kb_config['kb_main_page_category_link'],
									        'input_group_class' => 'config-col-12',
									        'main_label_class'  => 'config-col-12',
									        'input_class'       => '',
									        'radio_class'       => 'config-col-12',
									        'name'              => 'eckb_grd_link_switch'
								          ) ) )
						)
					)
				),
				array(
					'heading' => '2. Style',
					'form_elements' => array(
                        array(
                            'id'   => 'mega-menu-main-page-style',
                            'html' => $this->get_main_page_styles_html( $is_advanced_search ),
                        )
                    )
				),
				array(
					'heading' => '3. Colors',
					'form_elements' => array(
						array(
							'id'   => 'mega-menu-main-page-colors',
							'html' => $this->mega_menu_colors()
						)
					)
				),
                array(
                    'heading' => '4. Template',
                    'form_elements' => array(
                        array(
                            'id'   => 'mega-menu-main-page-kb-template',
                            'html' => $this->mega_menu_kb_templates()
                        )
                    )
                )
			)
		));

		// MAIN PAGE - ORGANIZE menu item
		$this->mega_menu_item_custom_html_content( array(
			'id'        => 'eckb-mm-mp-links-organize',
			'sections'  => array(
                array(
                    'heading' => 'Organize',
                    'links' => array(),
                    'form_elements' => array(
                        array(
                            'id'   => 'mega-menu-main-page-organize',
                            'html' => $this->mega_menu_organize()
                        )
                    )
                )
			)
		));

		// MAIN PAGE - ALL TEXT menu item
		$this->mega_menu_item_content( array(
			'id'        => 'eckb-mm-mp-links-alltext',
			'sections'  => array(
				array(
					'heading' => 'Text',
					'links' => array( 'Search Box', 'Categories', 'Articles' )
				),
			)
		));

		// MAIN PAGE - TUNING menu item
        $this->mega_menu_item_content( array(
            'id'        => 'eckb-mm-mp-links-tuning',
            'sections'  => array(

            	// ------- Search Box --------
	            $search_box_menu,
	            // ------- Content -----------
                array(
	                'heading' => 'Content',
	                'links' => ( $main_page_sbl ? array( 'Style', 'Colors', 'Text' ) : array( 'Style', 'Colors' ) ),
                ),
	            // ------- List of Articles --
                array(
	                'heading' => 'List of Articles',
                    'exclude' => $grid_layout,
	                'links' => array( 'Style', 'Colors', 'Text', 'Advanced' )
                ),
	            // ------- Categories --------
                array(
	                'heading' => 'Categories',
	                'links' => array( 'Style', 'Colors', 'Text', 'Advanced' )
                ),

            )
        ));


        /********************************************************************
         * 3. display ARTICLE PAGE menu content (Right side)
         ********************************************************************/

		// ARTICLE PAGE - SETUP menu item
		if ( $article_page_with_config_layout ) {

	        $this->mega_menu_item_custom_html_content( array(
		        'id'        => 'eckb-mm-ap-links-setup',
		        'class'     => 'article-page-sidebar-layout-option',
		        'sections'  => array(
			        array(
				        'heading' => '1. Layout',
				        'form_elements' => array(
					        array(
						        'id'   => 'mega-menu-article-page-layout',
						        'html' => $this->form->radio_buttons_vertical( array('options' => $article_page_layouts, 'label' => '') +
                                                                               $this->feature_specs['kb_article_page_layout'] + array(
								        'current'           => $this->kb_config['kb_article_page_layout'],
								        'input_group_class' => 'config-col-12',
								        'main_label_class'  => 'config-col-4',
								        'input_class'       => '',
								        'radio_class'       => 'config-col-12' ) )
					        )
				        )
			        ),
			        array(
				        'heading' => '2. Style',
				        'form_elements' => array(
					        array(
						        'id'   => 'mega-menu-article-page-style',
						        'html' => $this->get_article_page_styles_html( $is_advanced_search )
					        )
				        )
			        ),
			        array(
				        'heading' => '3. Colors',
				        'form_elements' => array(
					        array(
						        'id'   => 'mega-menu-article-page-colors',
						        'html' => $this->mega_menu_colors( false )
					        )
				        )
			        ),
                )
	        ));

        } else {
			$this->mega_menu_item_custom_html_content( array(
				'id'        => 'eckb-mm-ap-links-setup',
				'class'     => 'article-page-article-layout-option',
				'sections'  => array(
					array(
						'heading' => 'Layout',
						'form_elements' => array(
							array(
								'id'   => 'mega-menu-article-page-layout',
								'html' => $this->form->radio_buttons_vertical( array('options' => $article_page_layouts, 'label' => '') +
								                                               $this->feature_specs['kb_article_page_layout'] + array(
									                                               'current'           => $this->kb_config['kb_article_page_layout'],
									                                               'input_group_class' => 'config-col-12',
									                                               'main_label_class'  => 'config-col-4',
									                                               'input_class'       => '',
									                                               'radio_class'       => 'config-col-12' ) )
							)
						)
					)
				)) );
        }

		// ARTICLE PAGE - ORGANIZE menu item
		if ( $this->kb_article_page_layout != EPKB_KB_Config_Layouts::KB_ARTICLE_PAGE_NO_LAYOUT ) {
			$this->mega_menu_item_custom_html_content( array(
				'id'       => 'eckb-mm-ap-links-organize',
				'sections' => array(
					array(
						'heading'       => 'Organize',
						'links'         => array(),
						'form_elements' => array(
							array(
								'id'   => 'mega-menu-article-page-organize',
								'html' => $this->mega_menu_organize()
							)
						)
					)
				)
			) );
		}

        // ARTICLE PAGE - FEATURES menu item
        $this->mega_menu_item_content( array(
            'id'        => 'eckb-mm-ap-links-features',
            'sections'  => array(
                array(
                    'heading' => 'Features',
                    'links' => array( 'Article TOC', 'Back Navigation', 'Comments', 'Breadcrumb', 'Other' )
                ),
            )
        ));

        // ARTICLE PAGE - ALL TEXT menu item
        if ( $article_page_with_config_layout ) {
            // MAIN PAGE - ALL TEXT menu item
            $this->mega_menu_item_content( array(
                'id'       => 'eckb-mm-ap-links-alltext',
                'sections' => array(
                    array(
                        'heading' => 'Text',
                        'links'   => array( 'Search Box', 'Categories', 'Articles' )
                    ),
                )
            ) );
        }

        // ARTICLE PAGE - TUNING menu item
        if ( $article_page_with_config_layout ) {

	        $this->mega_menu_item_content( array(
                'id'        => 'eckb-mm-ap-links-tuning',
                'sections'  => array(
	                // ------- Search Box --------
	                $search_box_menu,
                    array(
                        'heading' => 'Content',
                        'links' => array( 'Style', 'Colors' )
                    ),
                    array(
                        'heading' => 'Categories',
                        'links' => array( 'Style', 'Colors', 'Text', 'Advanced' )
                    ),
                    array(
                        'heading' => 'List of Articles',
                        'links' => array( 'Style', 'Colors', 'Text', 'Advanced' )
                    ),
                    array(
                        'heading' => 'Article Common Path',
                        'links' => array( 'Configuration' )
                    ),
	                array(
		                'heading' => 'Article Structure',
		                'links' => array( 'Setup' )
	                ),
                )
            ));

        } else {
            $this->mega_menu_item_content( array(
                'id'        => 'eckb-mm-ap-links-tuning',
                'sections'  => array(
                    array(
                        'heading' => 'Article Common Path',
                        'links' => array( 'Configuration' )
                    ),
	                array(
		                'heading' => 'Article Structure',
		                'links' => array( 'Setup' )
                    ),
                )
            ));
        }


		/********************************************************************
		 * 4. display CATEGORIES ARCHIVE PAGE menu content (Right side)
		 ********************************************************************/

		echo '<div class="epkb-archive-page-template-config">';

			// ARCHIVE PAGE - SETUP menu item
			$this->mega_menu_item_custom_html_content( array(
				'id'        => 'eckb-mm-arch-links-setup',
				'class'     => 'epkb-mm-active',
				'sections'  => array(
					array(
						'heading' => 'Layout Style',
						'form_elements' => array(
							array(
								'id'   => 'mega-menu-archive-page-layout',
								'html' => $this->form->dropdown( $this->feature_specs['template_category_archive_page_style'] + array(
										'value' => $this->kb_config['template_category_archive_page_style'],
										'current' => $this->kb_config['template_category_archive_page_style'],
										'input_group_class' => 'config-col-6',
										'label_class' => 'config-col-6',
										'input_class' => 'config-col-6'
									) ),
							)
						)
					)

				)

			));

			// ARCHIVE PAGE - ALL TEXT menu item
			$this->mega_menu_item_custom_html_content( array(
				'id'        => 'eckb-mm-arch-links-alltext',
				'sections'  => array(
					array(
						'heading' => 'Text',
						'form_elements' => array(
							array(
								'id'   => 'mega-menu-archive-page-layout',
								'html' => $this->form->text( $this->feature_specs['template_category_archive_page_heading_description'] + array(
										'value'             => $this->kb_config['template_category_archive_page_heading_description'],
										'input_group_class' => 'config-col-6',
										'label_class'       => 'config-col-3',
										'input_class'       => 'config-col-9'
									) ),
							)
						)
					)
				)
			));

			// ARCHIVE PAGE - TUNING menu item
			$this->mega_menu_item_content( array(
				'id'        => 'eckb-mm-arch-links-tuning',
				'sections'  => array(
					array(
						'heading' => 'Page Structure',
						'links' => array( 'Setup' )
					),
				)
			));

		echo '</div>';

        // ARTICLE PAGE - add-on menu items
        do_action( 'epkb_kb_article_page_add_on_menu_content', $this->kb_config );

        echo '</form>';
        echo '</div>';

		echo '<div class="epkb-close-mm">';
                echo '<span class="ep_font_icon_arrow_carrot_up"></span>';
        echo '</div>';
	}

	private function get_main_page_styles_html( $is_advanced_search ) {
		$output = '';
		$output .= $this->form->radio_buttons_horizontal( array(
										'id' => 'main_page_reset_style',
										'name' => 'main_page_reset_style',
										'label' => 'Page Styles',
										'options' => EPKB_KB_Config_Layouts::get_main_page_style_names( $this->kb_config ),
										'input_group_class' => '',
										'main_label_class'  => '',
										'input_class'       => '',
										'radio_class'       => 'radio_buttons_resets'
									));

		if ( $is_advanced_search ) {

			$add_on_style_names = apply_filters( 'epkb_advanced_search_box_style_names', array() );

		    $output .= $this->form->radio_buttons_horizontal( array(
											'id' => 'main_page_reset_search_box_style',
											'name' => 'main_page_reset_search_box_style',
											'label' => 'Advanced Search Styles',
											'options' => $add_on_style_names,
											'input_group_class' => '',
											'main_label_class'  => '',
											'input_class'       => '',
											'radio_class'       => 'radio_buttons_resets'
										));
		}

		return $output;
	}

	private function get_article_page_styles_html( $is_advanced_search ) {

		$output = '';
		$output .= $this->form->radio_buttons_horizontal( array(
										'id' => 'article_page_reset_style', 'name' => 'article_page_reset_style','label' => '',
		                                'options' => EPKB_KB_Config_Layouts::get_article_page_style_names( $this->kb_config ),
		                                'input_group_class' => '',
		                                'main_label_class'  => '',
		                                'input_class'       => 'radio_buttons_resets',
		                                'radio_class'       => ''
		));

		if ( $is_advanced_search ) {

			$add_on_style_names = apply_filters( 'epkb_advanced_search_box_style_names', array() );

			$output .= $this->form->radio_buttons_horizontal( array(
											'id' => 'article_page_reset_search_box_style',
											'name' => 'article_page_reset_search_box_style',
											'label' => 'Advanced Search Styles',
											'options' => $add_on_style_names,
											'input_group_class' => '',
											'main_label_class'  => '',
											'input_class'       => '',
											'radio_class'       => 'radio_buttons_resets'
			));
		}

		return $output;
	}

	/**
	 * Display MAIN PAGE and ARTICLE PAGE / CATEGORY and TAG PAGE Sidebar menu items on the right side of the Mega Menu
	 *
	 * @param array $args
	 */
	private function mega_menu_sidebar_links( $args = array() ) {

		echo '<ul class="' . ( empty($args['class']) ? '' : $args['class'] ) . '" id="' . $args['id'] . '">';

		$ix = 0;
		foreach( $args['core_links'] as $link ) {
			$class = $ix++ == 0 ? 'class="epkb-mm-active"' : '';
			$linkID = $args['id'] . '-' . str_replace(' ', '', strtolower( $link ) );
			echo '<li id="' . $linkID . '" ' . $class . '>' . __( $link, 'echo-knowledge-base' ) . '</li>';
		}

		echo '</ul>';
	}

	/**
	 * Show content of a menu item (list of links on the right side)
	 *
	 * @param array $args
	 */
	private function mega_menu_item_content( $args = array() ) {

		echo '<div class="epkb-mm-links ' . ( empty($args['class']) ? '' : $args['class'] ) . '" id="' . $args['id'] . '-list' . '">';
		$total = count( $args['sections'] );
		foreach( $args['sections'] as $section ) {
			if ( ! empty($section['exclude']) ) {
				$total = $total - 1;
			}
		}

		foreach( $args['sections'] as $section ) {

            if ( ! empty($section['exclude']) ) {
                continue;
            }

			echo '<section class="epkb-section-count-' . $total . '">' .
				'	<h3>' . ( empty($section['heading']) ? '' :  __( $section['heading'], 'echo-knowledge-base' ) ) . '</h3>' .
			    '   <p>' . ( empty($section['info']) ? '' : $section['info'] ) .'</p>' .
				'	<ul>';

			foreach ( $section[ 'links'] as $link ) {
				$linkID = $args['id'] . '-' . str_replace( array( ' ', ':' ), '', strtolower($section['heading'] . '-' . $link ) );
				echo '<li id="' . $linkID . '">' . __( $link, 'echo-knowledge-base' ) . '</li>';
			}

			echo '	</ul>' .
				'</section>';
		}
		echo '</div>';
	}

	private function mega_menu_item_custom_html_content( $args = array() ) {

		echo '<div class="epkb-mm-links ' . ( empty($args['class']) ? '' : $args['class'] ) . '" id="' . $args['id'] . '-list' . '">';
		$count = count( $args['sections'] );
		foreach( $args['sections'] as $section ) {

			echo '<section class="epkb-section-count-' . $count . '">';
			echo '<h3>' . __( $section['heading'], 'echo-knowledge-base' ) . '</h3>';

			foreach ( $section['form_elements'] as $html ) {
				echo '<div id="' . $html['id'] . '">';
				echo $html['html'];
				echo '</div>';
			}
			echo '</section>';
		}
		echo '</div>';
	}

	private function mega_menu_colors( $is_main_page=true ) {
		ob_start();	    ?>

        <div class="reset_colors" id="<?php echo $is_main_page ? 'main' : 'article'; ?>_page_reset_colors">
            <ul>
                <li class="config-col-12"><?php _e( 'Black / White', 'echo-knowledge-base' ); ?></li>
                <li class="config-col-4">
                    <div class="color_palette black-white">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </li>
                <li class="config-col-8">
                    <ul class="epkb_rest_buttons">
                        <li><button type="button" value="black-white1">1</button></li>
                        <li><button type="button" value="black-white2">2</button></li>
                        <li><button type="button" value="black-white3">3</button></li>
                        <li><button type="button" value="black-white4">4</button></li>
                    </ul>
                </li>
            </ul>
            <ul>
                <li class="config-col-12"><?php _e( 'Red', 'echo-knowledge-base' ); ?></li>
                <li class="config-col-4">
                    <div class="color_palette red">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </li>
                <li class="config-col-8">
                    <ul class="epkb_rest_buttons">
                        <li><button type="button" value="red1">1</button></li>
                        <li><button type="button" value="red2">2</button></li>
                        <li><button type="button" value="red3">3</button></li>
                        <li><button type="button" value="red4">4</button></li>
                    </ul>
                </li>
            </ul>
            <ul>
                <li class="config-col-12"><?php _e( 'Blue', 'echo-knowledge-base' ); ?></li>
                <li class="config-col-4">
                    <div class="color_palette blue">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </li>
                <li class="config-col-8">
                    <ul class="epkb_rest_buttons">
                        <li><button type="button" value="blue1"> 1 </button></li>
                        <li><button type="button" value="blue2"> 2 </button></li>
                        <li><button type="button" value="blue3"> 3 </button></li>
                        <li><button type="button" value="blue4"> 4 </button></li>
                    </ul>
                </li>
            </ul>
            <ul>
                <li class="config-col-12"><?php _e( 'Green', 'echo-knowledge-base' ); ?></li>
                <li class="config-col-4">
                    <div class="color_palette green">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </li>
                <li class="config-col-8">
                    <ul class="epkb_rest_buttons">
                        <li><button type="button" value="green1">1</button></li>
                        <li><button type="button" value="green2">2</button></li>
                        <li><button type="button" value="green3">3</button></li>
                        <li><button type="button" value="green4">4</button></li>
                    </ul>
                </li>
            </ul>
        </div>    <?php

		return ob_get_clean();
	}

	/**
	 * Display menu content for KB Template Choice
	 *
	 * @return string
	 */
	private function mega_menu_kb_templates() {

		ob_start();

		echo  $this->form->radio_buttons_vertical( $this->feature_specs['templates_for_kb'] + array(
				'current'           => $this->kb_config['templates_for_kb'],
				'input_group_class' => 'config-col-12',
				'main_label_class'  => 'config-col-12',
				'input_class'       => '',
				'radio_class'       => 'config-col-12' ) );

        echo '<p><a href="https://www.echoknowledgebase.com/documentation/current-theme-template-vs-kb-template/" target="_blank" class="eckb-external-link">' . __( 'More about templates', 'echo-knowledge-base' ) . '</a>';

		return ob_get_clean();
     }

	private function mega_menu_organize() {
        ob_start();
        echo '<p><strong>' . __( 'To Organize Categories and Articles', 'echo-knowledge-base' ) . ':</strong></p>';
        echo '<p style="padding-left: 20px;">a) ' . __( 'In the preview below, drag and drop categories and articles in any order', 'echo-knowledge-base' ) . ' </p>';
        echo '<p>   OR</p>';
        echo '<p style="padding-left: 20px;">b) ' . __( 'In the configuration on the right, set chronological or alphabetical order', 'echo-knowledge-base' ) . ' </p>';
        return ob_get_clean();
	}


	/**************************************************************************************
	 *
	 *                   MAIN PANEL
	 *
	 *************************************************************************************/

	/**
	 * Display the Main Page layout preview.
	 *
	 * @param bool $display
	 * @param array $articles_seq_data
	 * @param array $category_seq_data
	 * @return string
	 */
	public function display_kb_main_page_layout_preview( $display=true, $articles_seq_data=array(), $category_seq_data=array() ) {
		global $eckb_is_kb_main_page;

		// retrieve KB preview using Current KB or Demo KB
		if ( EPKB_Utilities::post('epkb-wizard-demo-data', false, false) ) {
			$demo_data = EPKB_KB_Demo_Data::get_category_demo_data( $this->kb_main_page_layout, $this->kb_config );
			$category_seq_data = $demo_data['category_seq'];
			$articles_seq_data = $demo_data['article_seq'];
			$this->kb_config['wizard-icons'] = $demo_data['category_icons'];
		}

		$eckb_is_kb_main_page = true;   // pretend this is Main Page
		$main_page_output = EPKB_Layouts_Setup::output_main_page( $this->kb_config, true, $articles_seq_data, $category_seq_data );

		// setup test icons
		if ( $this->kb_main_page_layout == EPKB_KB_Config_Layouts::GRID_LAYOUT && EPKB_Utilities::post('epkb-wizard-demo-data', false, false) ) {
			$count = 2;
			$main_page_output = preg_replace( '/ep_font_icon_document/', 'ep_font_icon_person', $main_page_output, $count );
			$main_page_output = preg_replace( '/ep_font_icon_document/', 'ep_font_icon_shopping_cart', $main_page_output, $count );
			$main_page_output = preg_replace( '/ep_font_icon_document/', 'ep_font_icon_money', $main_page_output, $count );
			$main_page_output = preg_replace( '/ep_font_icon_document/', 'ep_font_icon_tag', $main_page_output, $count );
			$main_page_output = preg_replace( '/ep_font_icon_document/', 'ep_font_icon_credit_card', $main_page_output, $count );
			$main_page_output = preg_replace( '/ep_font_icon_document/', 'ep_font_icon_building', $main_page_output, $count );
		}
		
		if ( $display ) {
			echo $main_page_output;
		}

		return $main_page_output;
	}

	/**
	 * Show Article Page preview
	 *
	 * @param bool $display
	 * @param array $articles_seq_data
	 * @param array $category_seq_data
	 * @return mixed|string
	 */
	public function display_article_page_layout_preview( $display=false, $articles_seq_data=array(), $category_seq_data=array() ) {
		global $eckb_is_kb_main_page;

		$eckb_is_kb_main_page = false;      // for preview set Article Page

		// setup either current KB or demo KB data
		if ( ( isset($_POST['epkb_demo_kb']) && $_POST['epkb_demo_kb'] == "true" ) || ! empty($_POST['epkb-wizard-demo-data']) ) {
			$demo_data = EPKB_KB_Demo_Data::get_category_demo_data( $this->kb_config['kb_article_page_layout'], $this->kb_config );
			$category_seq_data = $demo_data['category_seq'];
			$articles_seq_data = $demo_data['article_seq'];
        }

        $temp_config = $this->kb_config;

        $demo_article = new stdClass();
        $demo_article->ID = 0;
		$demo_article->post_title = __( 'Demo Article', 'echo-knowledge-base' );
		$demo_article->post_content = wp_kses_post( EPKB_KB_Demo_Data::get_demo_article() );
		$demo_article->post_date = current_time( 'mysql' );
		$demo_article->post_modified = current_time( 'mysql' );
		$demo_article->is_demo = true;
        $demo_article = new WP_Post( $demo_article );

		$temp_config[EPKB_Articles_Admin::KB_ARTICLES_SEQ_META] = $articles_seq_data;
		$temp_config[EPKB_Categories_Admin::KB_CATEGORIES_SEQ_META] = $category_seq_data;

		// for article structure V2 we need to pass to elay demo data this way
		if ( EPKB_Articles_Setup::is_article_structure_v2( $this->kb_config )  ) {
			$GLOBALS['epkb-articles-seq-data'] = $articles_seq_data;
			$GLOBALS['epkb-categories-seq-data'] = $category_seq_data;
		}

		$article_page_output = EPKB_Articles_Setup::get_article_content_and_features( $demo_article, $demo_article->post_content, $temp_config );
		
        if ( EPKB_KB_Config_Layouts::is_article_page_displaying_sidebar( $this->kb_config['kb_article_page_layout'] ) && ! EPKB_Articles_Setup::is_article_structure_v2( $this->kb_config ) ) {
			$article_page_output = EPKB_Articles_Setup::output_article_page_with_layout( $article_page_output, $temp_config, true, $articles_seq_data, $category_seq_data );
        }

        echo $display ? $article_page_output : '';

		return $article_page_output;
	}

	/**
	 * Only with Demo mode
	 * @param bool $display
	 * @return string|void
	 */
	public function display_archive_page_layout_preview ( $display = false ){

		if ( empty($this->kb_config['template_category_archive_page_style']) ) {
			return;
		}

		// Just images for now 
		// TODO: add demo archive template to have live preview 
		
		$img_url = 'https://www.echoknowledgebase.com/wp-content/uploads/2021/02/';
		
		switch ( $this->kb_config['template_category_archive_page_style'] ) {
			case 'eckb-category-archive-style-1':
				$img_url .= 'wizard-archive-style-1.jpg';
				break;
			case 'eckb-category-archive-style-2':
				$img_url .= 'wizard-archive-style-2.jpg';
				break;
			case 'eckb-category-archive-style-3':
				$img_url .= 'wizard-archive-style-3.jpg';
				break;
			case 'eckb-category-archive-style-4':
				$img_url .= 'wizard-archive-style-4.jpg';
				break;
			case 'eckb-category-archive-style-5':
				$img_url .= 'wizard-archive-style-5.jpg';
				break;
				
		}
		
		$archive_page_output = '<img src="' . $img_url . '" class="epkb-wizard-text-archive-page-preview-image">';
		
		echo $display ? $archive_page_output : '';

		return $archive_page_output;
	}


	/**************************************************************************************
	 *
	 *                   SIDEBARS: KB MAIN PAGE
	 *
	 *************************************************************************************/

    /**
     * Display SIDEBAR for given TOP icon - KB Main Page / Article Page
     */
    private function display_sidebar() {	    ?>

        <form id="epkb-config-config">

            <div class="epkb-sidebar-container" id="epkb-main-page-settings">
                <?php $this->display_kb_main_page_sections(); ?>
            </div>

            <div class="epkb-sidebar-container" id="epkb-article-page-settings">
                <?php $this->display_article_page_sections(); ?>
            </div>

	        <div class="epkb-sidebar-container" id="epkb-archive-page-settings">
		        <?php $this->display_archive_page_sections(); ?>
	        </div>

            <div id='epkb-ajax-in-progress' style="display:none;">
                <?php esc_html__( 'Saving configuration', 'echo-knowledge-base' ); ?> <img class="epkb-ajax waiting" style="height: 30px;" src="<?php echo Echo_Knowledge_Base::$plugin_url . 'img/loading_spinner.gif'; ?>">
            </div>  <?php

	        do_action( 'eckb_additional_output', $this->kb_config );        ?>

        </form>      <?php
    }

	/**
	 * Display all sidebar forms for MAIN PAGE
	 */
	private function display_kb_main_page_sections() {
        echo $this->get_main_page_templates_form();
        echo $this->get_main_page_order_form();
		echo $this->get_main_page_styles_form();
		echo $this->get_main_page_colors_form();
        echo $this->get_main_page_text_form();
		do_action( 'eckb_main_page_sidebar_additional_output', $this->kb_config );
	}

    /**
     * Generate form fields for the MAIN PAGE side bar
     */
    public function get_main_page_templates_form() {

        ob_start();     ?>

        <div class="epkb-config-sidebar" id="epkb-config-main-setup-sidebar">
            <div class="epkb-config-sidebar-options">                        <?php
                $feature_specs = EPKB_KB_Config_Specs::get_fields_specification( $this->kb_config['id'] );
                $form = new EPKB_KB_Config_Elements();

                $arg_bn_padding_top    = $feature_specs['template_main_page_padding_top'] + array( 'value' => $this->kb_config['template_main_page_padding_top'], 'current' => $this->kb_config['template_main_page_padding_top'], 'text_class' => 'config-col-6' );
                $arg_bn_padding_bottom = $feature_specs['template_main_page_padding_bottom'] + array( 'value' => $this->kb_config['template_main_page_padding_bottom'], 'current' => $this->kb_config['template_main_page_padding_bottom'], 'text_class' => 'config-col-6' );
                $arg_bn_padding_left   = $feature_specs['template_main_page_padding_left'] + array( 'value' => $this->kb_config['template_main_page_padding_left'], 'current' => $this->kb_config['template_main_page_padding_left'], 'text_class' => 'config-col-6' );
                $arg_bn_padding_right  = $feature_specs['template_main_page_padding_right'] + array( 'value' => $this->kb_config['template_main_page_padding_right'], 'current' => $this->kb_config['template_main_page_padding_right'], 'text_class' => 'config-col-6' );

                $arg_bn_margin_top    = $feature_specs['template_main_page_margin_top'] + array( 'value' => $this->kb_config['template_main_page_margin_top'], 'current' => $this->kb_config['template_main_page_margin_top'], 'text_class' => 'config-col-6' );
                $arg_bn_margin_bottom = $feature_specs['template_main_page_margin_bottom'] + array( 'value' => $this->kb_config['template_main_page_margin_bottom'], 'current' => $this->kb_config['template_main_page_margin_bottom'], 'text_class' => 'config-col-6' );
                $arg_bn_margin_left   = $feature_specs['template_main_page_margin_left'] + array( 'value' => $this->kb_config['template_main_page_margin_left'], 'current' => $this->kb_config['template_main_page_margin_left'], 'text_class' => 'config-col-6' );
                $arg_bn_margin_right  = $feature_specs['template_main_page_margin_right'] + array( 'value' => $this->kb_config['template_main_page_margin_right'], 'current' => $this->kb_config['template_main_page_margin_right'], 'text_class' => 'config-col-6' );

                $form->option_group( $feature_specs, array(
                    'option-heading'    => 'Templates',
                    'class'             => 'eckb-mm-mp-links-setup-main-template',
                    'inputs'            => array(
	                    '0' => $form->checkbox( $feature_specs['templates_for_kb_article_reset'] + array(
			                    'value'             => $this->kb_config['templates_for_kb_article_reset'],
			                    'id'                => 'templates_for_kb_article_reset',
			                    'input_group_class' => 'config-col-12',
			                    'label_class'       => 'config-col-5',
			                    'input_class'       => 'config-col-2'
		                    ) ),
	                    '1' => $form->checkbox( $feature_specs['templates_for_kb_article_defaults'] + array(
			                    'value'             => $this->kb_config['templates_for_kb_article_defaults'],
			                    'id'                => 'templates_for_kb_article_defaults',
			                    'input_group_class' => 'config-col-12',
			                    'label_class'       => 'config-col-5',
			                    'input_class'       => 'config-col-2'
		                    ) ),
	                    '2' => $form->checkbox( $feature_specs['template_main_page_display_title'] + array(
			                    'value'             => $this->kb_config['template_main_page_display_title'],
			                    'id'                => 'template_main_page_display_title',
			                    'input_group_class' => 'config-col-12',
			                    'label_class'       => 'config-col-5',
			                    'input_class'       => 'config-col-2'
		                    ) ),
	                    '4' => $form->multiple_number_inputs(
		                    array(
			                    'id'                => 'template_main_page_padding_group',
			                    'input_group_class' => '',
			                    'main_label_class'  => '',
			                    'input_class'       => '',
			                    'label'             => 'Template Padding( px )'
		                    ),
		                    array( $arg_bn_padding_top, $arg_bn_padding_bottom, $arg_bn_padding_left, $arg_bn_padding_right )
	                    ),
	                    '5' => $form->multiple_number_inputs(
		                    array(
			                    'id'                => 'template_main_page_margin_group',
			                    'input_group_class' => '',
			                    'main_label_class'  => '',
			                    'input_class'       => '',
			                    'label'             => 'Template Margin( px )'
		                    ),
		                    array( $arg_bn_margin_top, $arg_bn_margin_bottom , $arg_bn_margin_left, $arg_bn_margin_right )
	                    ),
                    )
                )); ?>
            </div>
        </div>      <?php

        return ob_get_clean();
    }

    /**
	 * Generate form fields for the side bar
	 */
	public function get_main_page_order_form() {
		ob_start();	    ?>

		<div class="epkb-config-sidebar" id="epkb-config-ordering-sidebar" hidden>
			<div class="epkb-config-sidebar-options">            <?php

            $sequence_widets = array(
                '0' => $this->form->radio_buttons_vertical(
                    $this->feature_specs['categories_display_sequence'] +
                    array(
                        'id'        => 'front-end-columns',
                        'value'     => $this->kb_config['categories_display_sequence'],
                        'current'   => $this->kb_config['categories_display_sequence'],
                        'input_group_class' => 'config-col-12',
                        'main_label_class'  => 'config-col-12',
                        'input_class'       => 'config-col-12',
                        'radio_class'       => 'config-col-12'
                    )
                )
            );

            // Grid Layout does not show articles
            //if ( $this->kb_main_page_layout != 'Grid' ) {
                $sequence_widets[1] = $this->form->radio_buttons_vertical(
                    $this->feature_specs['articles_display_sequence'] +
                    array(
                        'id'        => 'front-end-columns',
                        'value'     => $this->kb_config['articles_display_sequence'],
                        'current'   => $this->kb_config['articles_display_sequence'],
                        'input_group_class' => 'config-col-12' . ( ($this->kb_main_page_layout == 'Grid') ? ' epkb-grid-option-hide-show' : ''),
                        'main_label_class'  => 'config-col-12',
                        'input_class'       => 'config-col-12',
                        'radio_class'       => 'config-col-12'
                    )
                );
            //}

            $this->form->option_group( $this->feature_specs, array(
                'option-heading'    => 'Organize Categories and Articles',
                'class'             => 'eckb-mm-mp-links-organize--organize',
                'inputs' => $sequence_widets
            ));            ?>
            </div>
        </div>        <?php

		return ob_get_clean();
	}

	/**
	 * Generate form fields for the side bar
	 */
	public function get_main_page_styles_form() {
		ob_start();	    ?>

		<div class="epkb-config-sidebar" id="epkb-config-styles-sidebar">
			<div class="epkb-config-sidebar-options" id="epkb_style_sidebar_options">                <?php
				apply_filters( 'epkb_kb_main_page_style_settings', $this->kb_main_page_layout, $this->kb_config ); ?>
			</div>
		</div>      <?php

		return ob_get_clean();
	}

	/**
	 * Generate form fields for the side bar
	 */
	public function get_main_page_colors_form() {
		ob_start();	    ?>

		<div class="epkb-config-sidebar" id="epkb-config-colors-sidebar">
			<div class="epkb-config-sidebar-options">
				<?php apply_filters( 'epkb_kb_main_page_colors_settings', $this->kb_main_page_layout, $this->kb_config ); ?>
			</div>
		</div>			         <?php

		return ob_get_clean();
	}

	/**
	 * Generate form fields for the side bar
	 */
	public function get_main_page_text_form() {
		ob_start();	    ?>

		<div class="epkb-config-sidebar" id="epkb-config-text-sidebar">
			<div class="epkb-config-sidebar-options">
				<?php apply_filters( 'epkb_kb_main_page_text_settings', $this->kb_main_page_layout, $this->kb_config ); ?>
			</div>
		</div>			     <?php

		return ob_get_clean();
	}


	/**************************************************************************************
	 *
	 *                   SIDEBARS: ARTICLE PAGE
	 *
	 *************************************************************************************/

	private function display_article_page_sections() {
		echo $this->get_article_page_templates_form();
		echo $this->get_article_page_version_2();
		//echo $this->get_article_page_order_form();
        echo $this->get_article_page_features_form();
        echo $this->get_article_page_styles_form();
        echo $this->get_article_page_colors_form();
        echo $this->get_article_page_text_form();
        echo $this->get_article_page_general_form();
		do_action( 'eckb_article_page_sidebar_additional_output', $this->kb_config );
	}

	/**
	 * Generate form fields for the Article page Template side bar
	 */
	public function get_article_page_templates_form() {
	     ob_start();     ?>

        <div class="epkb-config-sidebar" id="epkb-config-article-template-sidebar">
            <div class="epkb-config-sidebar-options">                <?php

                $feature_specs = EPKB_KB_Config_Specs::get_fields_specification( $this->kb_config['id'] );
                $form = new EPKB_KB_Config_Elements();

                $article_padding_top    = $feature_specs['template_article_padding_top']    + array( 'value' => $this->kb_config['template_article_padding_top'],       'current' => $this->kb_config['template_article_padding_top'],      'text_class' => 'config-col-6' );
                $article_padding_bottom = $feature_specs['template_article_padding_bottom'] + array( 'value' => $this->kb_config['template_article_padding_bottom'],    'current' => $this->kb_config['template_article_padding_bottom'],   'text_class' => 'config-col-6' );
                $article_padding_left   = $feature_specs['template_article_padding_left']   + array( 'value' => $this->kb_config['template_article_padding_left'],      'current' => $this->kb_config['template_article_padding_left'],     'text_class' => 'config-col-6' );
                $article_padding_right  = $feature_specs['template_article_padding_right']  + array( 'value' => $this->kb_config['template_article_padding_right'],     'current' => $this->kb_config['template_article_padding_right'],    'text_class' => 'config-col-6' );

                $article_margin_top    = $feature_specs['template_article_margin_top']      + array( 'value' => $this->kb_config['template_article_margin_top'],        'current' => $this->kb_config['template_article_margin_top'],       'text_class' => 'config-col-6' );
                $article_margin_bottom = $feature_specs['template_article_margin_bottom']   + array( 'value' => $this->kb_config['template_article_margin_bottom'],     'current' => $this->kb_config['template_article_margin_bottom'],    'text_class' => 'config-col-6' );
                $article_margin_left   = $feature_specs['template_article_margin_left']     + array( 'value' => $this->kb_config['template_article_margin_left'],       'current' => $this->kb_config['template_article_margin_left'],      'text_class' => 'config-col-6' );
                $article_margin_right  = $feature_specs['template_article_margin_right']    + array( 'value' => $this->kb_config['template_article_margin_right'],      'current' => $this->kb_config['template_article_margin_right'],     'text_class' => 'config-col-6' );

                $form->option_group( $feature_specs, array(
	                'option-heading'    => 'Article Template',
	                'class'             => 'eckb-mm-mp-links-setup-article-template',
	                'inputs'            => array(
		                '0' => $form->multiple_number_inputs(
			                array(
				                'id'                => 'template_article_padding_group',
				                'input_group_class' => '',
				                'main_label_class'  => '',
				                'input_class'       => '',
				                'label'             => __( 'Padding', 'echo-knowledge-base' ) . '( px )'
			                ),
			                array( $article_padding_top, $article_padding_bottom, $article_padding_left, $article_padding_right )
		                ),
		                '1' => $form->multiple_number_inputs(
			                array(
				                'id'                => 'template_article_margin_group',
				                'input_group_class' => '',
				                'main_label_class'  => '',
				                'input_class'       => '',
				                'label'             => __( 'Margin', 'echo-knowledge-base' ) . '( px )'
			                ),
			                array( $article_margin_top, $article_margin_bottom , $article_margin_left, $article_margin_right )
		                )
	                )
                ));                ?>

            </div>
        </div>      <?php

		return ob_get_clean();

	}

	/**
	 * Generate form fields for the Article Version 2
	 */
	public function get_article_page_version_2() {
		ob_start();
		$feature_specs = EPKB_KB_Config_Specs::get_fields_specification( $this->kb_config['id'] );
		$form = new EPKB_KB_Config_Elements();		?>

		<div class="epkb-config-sidebar" id="epkb-config-article-features-sidebar">
			<div class="epkb-config-sidebar-options">   			 <?php

				 // Container
				 $form->option_group( $feature_specs, array(
					 'option-heading'    => 'Article Main',
					 'class'             => 'eckb-mm-ap-links-tuning-articlestructure-setup',
					 'inputs'            => array(
						 '0' => $form->text( $feature_specs['article-container-desktop-width-v2'] +
							 array( $this->kb_config['article-container-desktop-width-v2'],
								 'value'             => $this->kb_config['article-container-desktop-width-v2'],
								 'input_group_class' => 'config-col-12',
								 'label_class'       => 'config-col-5',
								 'input_class'       => 'config-col-5'
							 ) ),
						 '1' => $form->dropdown( $feature_specs['article-container-desktop-width-units-v2'] + array(
								 'value' => $this->kb_config['article-container-desktop-width-units-v2'],
								 'current' => $this->kb_config['article-container-desktop-width-units-v2'],
								 'input_group_class' => 'config-col-12',
								 'label_class' => 'config-col-5',
								 'input_class' => 'config-col-4'
							 ) ),
					 )
				 ));

				 // Left Sidebar
				 $form->option_group( $feature_specs, array(
					 'option-heading'    => 'Article - Left Sidebar',
					 'class'             => 'eckb-mm-ap-links-tuning-articlestructure-setup',
					 'inputs'            => array(
						
						 // Sidebar width
						 '1' => $form->text( $feature_specs['article-left-sidebar-desktop-width-v2'] +
							 array( $this->kb_config['article-left-sidebar-desktop-width-v2'],
								 'value'             => $this->kb_config['article-left-sidebar-desktop-width-v2'],
								 'input_group_class' => 'config-col-12',
								 'label_class'       => 'config-col-5',
								 'input_class'       => 'config-col-5'
							 ) ),
						 // Sidebar Background Color
						 '3' => $form->text( $feature_specs['article-left-sidebar-background-color-v2'] + array(
								 'value' =>  $this->kb_config['article-left-sidebar-background-color-v2'],
								 'input_group_class' => 'config-col-12',
								 'class'             => 'ekb-color-picker',
								 'label_class'       => 'config-col-5',
								 'input_class'       => 'config-col-7 ekb-color-picker'
							 ) ),
					 )
				 ));

				 // Content
				 $form->option_group( $feature_specs, array(
					 'option-heading'    => 'Article - Content Area',
					 'class'             => 'eckb-mm-ap-links-tuning-articlestructure-setup',
					 'inputs'            => array(
						 // Content Width ( 20% )
						 '0' => $form->text( $feature_specs['article-content-desktop-width-v2'] +
							 array( $this->kb_config['article-content-desktop-width-v2'],
								 'value'             => $this->kb_config['article-content-desktop-width-v2'],
								 'input_group_class' => 'config-col-12',
								 'label_class'       => 'config-col-5',
								 'input_class'       => 'config-col-5'
							 ) ),
						 // Content Padding ( 20 ) px
						 '1' => $form->text( $feature_specs['article-content-padding-v2'] +
							 array( $this->kb_config['article-content-padding-v2'],
								 'value'             => $this->kb_config['article-content-padding-v2'],
								 'input_group_class' => 'config-col-12',
								 'label_class'       => 'config-col-5',
								 'input_class'       => 'config-col-5'
							 ) ),
						 // Content Background Color
						 '2' => $form->text( $feature_specs['article-content-background-color-v2'] + array(
								 'value' =>  $this->kb_config['article-content-background-color-v2'],
								 'input_group_class' => 'config-col-12',
								 'class'             => 'ekb-color-picker',
								 'label_class'       => 'config-col-5',
								 'input_class'       => 'config-col-7 ekb-color-picker'
							 ) ),
					 )
				 ));

				 // Right Sidebar
				 $form->option_group( $feature_specs, array(
					 'option-heading'    => 'Article - Right Sidebar',
					 'class'             => 'eckb-mm-ap-links-tuning-articlestructure-setup',
					 'inputs'            => array(
						 // Sidebar width
						 '1' => $form->text( $feature_specs['article-right-sidebar-desktop-width-v2'] +
							 array( $this->kb_config['article-right-sidebar-desktop-width-v2'],
								 'value'             => $this->kb_config['article-right-sidebar-desktop-width-v2'],
								 'input_group_class' => 'config-col-12',
								 'label_class'       => 'config-col-5',
								 'input_class'       => 'config-col-5'
							 ) ),
						 // Sidebar Background Color
						 '3' => $form->text( $feature_specs['article-right-sidebar-background-color-v2'] + array(
								 'value' =>  $this->kb_config['article-right-sidebar-background-color-v2'],
								 'input_group_class' => 'config-col-12',
								 'class'             => 'ekb-color-picker',
								 'label_class'       => 'config-col-5',
								 'input_class'       => 'config-col-7 ekb-color-picker'
							 ) ),
					 )
				 ));

				 //Advanced Settings
				 $form->option_group( $feature_specs, array(
					 'option-heading'    => 'Advanced',
					 'class'             => 'eckb-mm-ap-links-tuning-articlestructure-setup',
					 'inputs'            => array(

						 // Small Screen ( Mobile ) 1 Column Layout Breakpoint. This will make the 3 Columns turn into 1 column stacked layout.
						 '0' => $form->text( $feature_specs['article-mobile-break-point-v2'] +
							 array( $this->kb_config['article-mobile-break-point-v2'],
								 'value'             => $this->kb_config['article-mobile-break-point-v2'],
								 'input_group_class' => 'config-col-12',
								 'label_class'       => 'config-col-6',
								 'input_class'       => 'config-col-5'
							 ) ),
					 )
				 ));

				 ?>
			 </div>
		 </div>
		<?php return ob_get_clean();
	}

	/**
	 * Generate form fields for the side bar
	 */
	public function get_article_page_order_form() {
		ob_start();	    ?>

		<div class="epkb-config-sidebar" id="epkb-config-article-ordering-sidebar" hidden>
			<div class="epkb-config-sidebar-options">            <?php

				$sequence_widets = array(
					'0' => $this->form->radio_buttons_vertical(
						$this->feature_specs['categories_display_sequence'] +
						array(
							'id'        => 'front-end-columns',
							'value'     => $this->kb_config['categories_display_sequence'],
							'current'   => $this->kb_config['categories_display_sequence'],
							'input_group_class' => 'config-col-12',
							'main_label_class'  => 'config-col-12',
							'input_class'       => 'config-col-12',
							'radio_class'       => 'config-col-12'
						)
					)
				);

				$sequence_widets[1] = $this->form->radio_buttons_vertical(
					$this->feature_specs['articles_display_sequence'] +
					array(
						'id'        => 'front-end-columns',
						'value'     => $this->kb_config['articles_display_sequence'],
						'current'   => $this->kb_config['articles_display_sequence'],
						'input_group_class' => 'config-col-12',
						'main_label_class'  => 'config-col-12',
						'input_class'       => 'config-col-12',
						'radio_class'       => 'config-col-12'
					)
				);

				$this->form->option_group( $this->feature_specs, array(
					'option-heading'    => 'Organize Categories and Articles',
					'class'             => 'eckb-mm-ap-links-organize--organize',
					'inputs' => $sequence_widets
				));            ?>
			</div>
		</div>        <?php

		return ob_get_clean();
	}

    /**
     * Generate form fields for the ARTICLE PAGE side bar
     */
    public function get_article_page_features_form() {

        ob_start();     ?>

        <div class="epkb-config-sidebar" id="epkb-config-article-features-sidebar">
            <div class="epkb-config-sidebar-options">                        <?php
                $feature_specs = EPKB_KB_Config_Specs::get_fields_specification( $this->kb_config['id'] );
                $form = new EPKB_KB_Config_Elements();


                // FEATURES - Back Navigation
                $arg_bn_padding_top    = $feature_specs['back_navigation_padding_top'] + array( 'value' => $this->kb_config['back_navigation_padding_top'], 'current' => $this->kb_config['back_navigation_padding_top'], 'text_class' => 'config-col-6' );
                $arg_bn_padding_bottom = $feature_specs['back_navigation_padding_bottom'] + array( 'value' => $this->kb_config['back_navigation_padding_bottom'], 'current' => $this->kb_config['back_navigation_padding_bottom'], 'text_class' => 'config-col-6' );
                $arg_bn_padding_left   = $feature_specs['back_navigation_padding_left'] + array( 'value' => $this->kb_config['back_navigation_padding_left'], 'current' => $this->kb_config['back_navigation_padding_left'], 'text_class' => 'config-col-6' );
                $arg_bn_padding_right  = $feature_specs['back_navigation_padding_right'] + array( 'value' => $this->kb_config['back_navigation_padding_right'], 'current' => $this->kb_config['back_navigation_padding_right'], 'text_class' => 'config-col-6' );

                $arg_bn_margin_top    = $feature_specs['back_navigation_margin_top'] + array( 'value' => $this->kb_config['back_navigation_margin_top'], 'current' => $this->kb_config['back_navigation_margin_top'], 'text_class' => 'config-col-6' );
                $arg_bn_margin_bottom = $feature_specs['back_navigation_margin_bottom'] + array( 'value' => $this->kb_config['back_navigation_margin_bottom'], 'current' => $this->kb_config['back_navigation_margin_bottom'], 'text_class' => 'config-col-6' );
                $arg_bn_margin_left   = $feature_specs['back_navigation_margin_left'] + array( 'value' => $this->kb_config['back_navigation_margin_left'], 'current' => $this->kb_config['back_navigation_margin_left'], 'text_class' => 'config-col-6' );
                $arg_bn_margin_right  = $feature_specs['back_navigation_margin_right'] + array( 'value' => $this->kb_config['back_navigation_margin_right'], 'current' => $this->kb_config['back_navigation_margin_right'], 'text_class' => 'config-col-6' );

                $form->option_group( $feature_specs, array(
                    'option-heading'    => 'Back Navigation',
                    'class'             => 'eckb-mm-ap-links-features-features-backnavigation',
                    'inputs'            => array(
                        '0' => $form->checkbox( $feature_specs['article_content_enable_back_navigation'] + array(
                                'value'             => $this->kb_config['article_content_enable_back_navigation'],
                                'input_group_class' => 'config-col-12',
                                'label_class'       => 'config-col-5',
                                'input_class'       => 'config-col-2'
                            ) ),
                        '1' => $form->radio_buttons_vertical( $feature_specs['back_navigation_mode'] + array(
                                'value' => $this->kb_config['back_navigation_mode'],
                                'current'   => $this->kb_config['back_navigation_mode'],
                                'input_group_class' => 'config-col-12',
                                'main_label_class'  => 'config-col-4',
                                'input_class'       => 'config-col-8',
                                'radio_class'       => 'config-col-12'
                            ) ),
                        '2' => $form->text( $feature_specs['back_navigation_text'] + array(
                                'value'             => $this->kb_config['back_navigation_text'],
                                'input_group_class' => 'config-col-12',
                                'label_class'       => 'config-col-5',
                                'input_class'       => 'config-col-5'
                            ) ),
                        '3' => $form->text( $feature_specs['back_navigation_text_color'] + array(
                                'value'             => $this->kb_config['back_navigation_text_color'],
                                'input_group_class' => 'config-col-12',
                                'class'             => 'ekb-color-picker',
                                'label_class'       => 'config-col-5',
                                'input_class'       => 'config-col-5 ekb-color-picker'
                            ) ),
                        '4' => $form->text( $feature_specs['back_navigation_bg_color'] + array(
                                'value'             => $this->kb_config['back_navigation_bg_color'],
                                'input_group_class' => 'config-col-12',
                                'class'             => 'ekb-color-picker',
                                'label_class'       => 'config-col-5',
                                'input_class'       => 'config-col-5 ekb-color-picker'
                            ) ),
                        '5' => $form->text( $feature_specs['back_navigation_border_color'] + array(
                                'value'             => $this->kb_config['back_navigation_border_color'],
                                'input_group_class' => 'config-col-12',
                                'class'             => 'ekb-color-picker',
                                'label_class'       => 'config-col-5',
                                'input_class'       => 'config-col-5 ekb-color-picker'
                            ) ),
                        '7' => $form->radio_buttons_vertical( $feature_specs['back_navigation_border'] + array(
                                'value'             => $this->kb_config['back_navigation_border'],
                                'current'           => $this->kb_config['back_navigation_border'],
                                'input_group_class' => 'config-col-12',
                                'main_label_class'  => 'config-col-4',
                                'input_class'       => 'config-col-8',
                                'radio_class'       => 'config-col-12'
                            ) ),
                        '8' => $form->text( $feature_specs['back_navigation_border_radius'] + array(
                                'value' => $this->kb_config['back_navigation_border_radius'],
                                'input_group_class' => 'config-col-12',
                                'label_class'       => 'config-col-5',
                                'input_class'       => 'config-col-5'
                            ) ),
                        '9' => $form->text( $feature_specs['back_navigation_border_width'] + array(
                                'value' => $this->kb_config['back_navigation_border_width'],
                                'input_group_class' => 'config-col-12',
                                'label_class'       => 'config-col-5',
                                'input_class'       => 'config-col-5'
                            ) ),
                    )
                ));
                $form->option_group( $feature_specs, array(
                    'option-heading'    => 'Back Navigation - Advanced',
                    'class'             => 'eckb-mm-ap-links-features-features-backnavigation',
                    'inputs'            => array(
	                    '0' => $form->multiple_number_inputs(
		                    array(
			                    'id'                => 'back_navigation_padding_group',
			                    'input_group_class' => '',
			                    'main_label_class'  => '',
			                    'input_class'       => '',
			                    'label'             => 'Padding( px )'
		                    ),
		                    array( $arg_bn_padding_top, $arg_bn_padding_bottom, $arg_bn_padding_left, $arg_bn_padding_right )
	                    ),
	                    '1' => $form->multiple_number_inputs(
		                    array(
			                    'id'                => 'back_navigation_margin_group',
			                    'input_group_class' => '',
			                    'main_label_class'  => '',
			                    'input_class'       => '',
			                    'label'             => 'Margin( px )'
		                    ),
		                    array( $arg_bn_margin_top, $arg_bn_margin_bottom, $arg_bn_margin_left, $arg_bn_margin_right )
	                    )
                    )
                ));


                // FEATURES - Comments
                $form->option_group( $feature_specs, array(
                    'option-heading'    => 'Comments',
                    'class'             => 'eckb-mm-ap-links-features-features-comments',
                    'inputs'            => array(
                        '0' => $form->checkbox( $feature_specs['articles_comments_global'] + array(
                                'value'             => $this->kb_config['articles_comments_global'],
                                'current'           => $this->kb_config['articles_comments_global'],
                                'input_group_class' => 'config-col-12',
                                'label_class'       => 'config-col-3',
                                'input_class'       => 'config-col-9'	) ),
                    )
                ));
				
				// FEATURES - TOC

	            $arg1_active_heading = $feature_specs['article_toc_active_bg_color'] + array( 'value' => $this->kb_config['article_toc_active_bg_color'],
	                                                                                            'current' => $this->kb_config['article_toc_active_bg_color'], 'class' => 'ekb-color-picker', 'text_class' => 'config-col-6' );
	            $arg2_active_heading = $feature_specs['article_toc_active_text_color'] + array( 'value' => $this->kb_config['article_toc_active_text_color'],
	                                                                                   'current' => $this->kb_config['article_toc_active_text_color'], 'class' => 'ekb-color-picker', 'text_class' => 'config-col-6' );

	            $arg1_cursor_hover = $feature_specs['article_toc_cursor_hover_bg_color'] + array( 'value' => $this->kb_config['article_toc_cursor_hover_bg_color'],
	                                                                                          'current' => $this->kb_config['article_toc_cursor_hover_bg_color'], 'class' => 'ekb-color-picker', 'text_class' => 'config-col-6' );
	            $arg2_cursor_hover = $feature_specs['article_toc_cursor_hover_text_color'] + array( 'value' => $this->kb_config['article_toc_cursor_hover_text_color'],
	                                                                                            'current' => $this->kb_config['article_toc_cursor_hover_text_color'], 'class' => 'ekb-color-picker', 'text_class' => 'config-col-6' );

				$arg1_toc_level =  $feature_specs['article_toc_hx_level'] + array(
						'value' => $this->kb_config['article_toc_hx_level'],
						'current' => $this->kb_config['article_toc_hx_level'],
						'label_class'       => 'config-col-6',
						'input_class'       => 'config-col-6',
						'data' => array(
						'preview' => 1
					));
				$arg2_toc_level =  $feature_specs['article_toc_hy_level'] + array(
						'value' => $this->kb_config['article_toc_hy_level'],
						'current' => $this->kb_config['article_toc_hy_level'],
						'label_class'       => 'config-col-6',
						'input_class'       => 'config-col-6',
						'data' => array(
						'preview' => 1
					));

	            $form->option_group( $feature_specs, array(
                    'option-heading'    => 'TOC Settings',
                    'class'             => 'eckb-mm-ap-links-features-features-articletoc',
                    'inputs'            => array(
                        '0' => $form->checkbox( $feature_specs['article_toc_enable'] + array(
                                'value'             => $this->kb_config['article_toc_enable'],
		                        'current'           => $this->kb_config['article_toc_enable'],
                                'input_group_class' => 'config-col-12',
                                'label_class'       => 'config-col-5',
                                'input_class'       => 'config-col-5'
							) ),
                        '1' => $form->text( $feature_specs['article_toc_title'] + array(
		                        'value'             => $this->kb_config['article_toc_title'],
		                        'input_group_class' => 'config-col-12',
		                        'label_class'       => 'config-col-5',
		                        'input_class'       => 'config-col-5'
	                        ) ),
                        '2' => $form->radio_buttons_vertical( $feature_specs['article_toc_position'] + array(
		                        'value' => $this->kb_config['article_toc_position'],
		                        'current' => $this->kb_config['article_toc_position'],
		                        'input_group_class' => 'config-col-12 ' . $this->kb_config['kb_main_page_layout'],
		                        'main_label_class'  => 'config-col-5',
		                        'input_class'       => 'config-col-7',
		                        'radio_class'       => 'config-col-12'
	                        ) ),
						'4' => $form->multiple_number_inputs(
							array(
								'id'                => 'article_toc_heading_level',
								'input_group_class' => 'eckb-wizard-multiple-number-group',
								'main_label_class'  => '',
								'input_class'       => '',
								'label'             => __( 'TOC Heading: ', 'echo-knowledge-base' ),
							),
							array( $arg1_toc_level, $arg2_toc_level )
						),

                        '5' => $form->text( $feature_specs['article_toc_scroll_offset'] + array(
                                'value' => $this->kb_config['article_toc_scroll_offset'],
                                'input_group_class' => 'config-col-12',
                                'label_class'       => 'config-col-5',
                                'input_class'       => 'config-col-5'
                            ) ),
						'6' => $form->text( $feature_specs['article_toc_exclude_class'] + array(
                                'value' => $this->kb_config['article_toc_exclude_class'],
                                'input_group_class' => 'config-col-12',
                                'label_class'       => 'config-col-5',
                                'input_class'       => 'config-col-5'
                            ) ),
						'7' => $form->radio_buttons_vertical( $feature_specs['article_toc_border_mode'] + array(
                                'value' => $this->kb_config['article_toc_border_mode'],
								'current' => $this->kb_config['article_toc_border_mode'],
					            'input_group_class' => 'config-col-12',
					            'main_label_class'  => 'config-col-5',
					            'input_class'       => 'config-col-7',
					            'radio_class'       => 'config-col-12'
                            ) ),
						'8' => $form->text( $feature_specs['article_toc_position_from_top'] + array(
                                'value'             => $this->kb_config['article_toc_position_from_top'],
                                'input_group_class' => 'config-col-12',
                                'class'             => 'ekb-color-picker',
                                'label_class'       => 'config-col-5',
                                'input_class'       => 'config-col-5'
                            ) ),
                        '9' => $form->text( $feature_specs['article_toc_gutter'] + array(
		                        'value'             => $this->kb_config['article_toc_gutter'],
		                        'input_group_class' => 'config-col-12',
		                        'class'             => 'ekb-color-picker',
		                        'label_class'       => 'config-col-5',
		                        'input_class'       => 'config-col-5'
	                        ) ),
						/*'13' => $form->text( $feature_specs['article_toc_position_from_content'] + array(
                                'value'             => $this->kb_config['article_toc_position_from_content'],
                                'input_group_class' => 'config-col-12',
                                'class'             => 'ekb-color-picker',
                                'label_class'       => 'config-col-5',
                                'input_class'       => 'config-col-5'
                            ) ),*/
                    )
                ));

	            // FEATURES - TOC - COLORS
	            $form->option_group( $feature_specs, array(
		            'option-heading'    => 'TOC Colors',
		            'class'             => 'eckb-mm-ap-links-features-features-articletoc',
		            'inputs'            => array(
			            '1' => $form->text( $feature_specs['article_toc_text_color'] + array(
					            'value'             => $this->kb_config['article_toc_text_color'],
					            'input_group_class' => 'config-col-12',
					            'class'             => 'ekb-color-picker',
					            'label_class'       => 'config-col-5',
					            'input_class'       => 'config-col-5 ekb-color-picker'
				            ) ),
			            '2' => $form->text( $feature_specs['article_toc_background_color'] + array(
					            'value'             => $this->kb_config['article_toc_background_color'],
					            'input_group_class' => 'config-col-12',
					            'class'             => 'ekb-color-picker',
					            'label_class'       => 'config-col-5',
					            'input_class'       => 'config-col-5 ekb-color-picker'
				            ) ),
			            '3' => $form->text( $feature_specs['article_toc_border_color'] + array(
					            'value'             => $this->kb_config['article_toc_border_color'],
					            'input_group_class' => 'config-col-12',
					            'class'             => 'ekb-color-picker',
					            'label_class'       => 'config-col-5',
					            'input_class'       => 'config-col-5 ekb-color-picker'
				            ) ),
			            '4' => $form->text_fields_horizontal( array(
				            'id'                => 'article_list',
				            'input_group_class' => 'config-col-12',
				            'main_label_class'  => 'config-col-4',
				            'input_class'       => 'config-col-7 ekb-color-picker',
				            'label'             => 'Active Heading'
			            ), $arg1_active_heading, $arg2_active_heading ),
			            '5' => $form->text_fields_horizontal( array(
				            'id'                => 'article_list',
				            'input_group_class' => 'config-col-12',
				            'main_label_class'  => 'config-col-4',
				            'input_class'       => 'config-col-7 ekb-color-picker',
				            'label'             => 'Cursor Hover'
			            ), $arg1_cursor_hover, $arg2_cursor_hover ),
		            )
	            ));

	            // FEATURES - TOC - ADVANCED
	            $form->option_group( $feature_specs, array(
		            'option-heading'    => 'TOC Advanced',
		            'class'             => 'eckb-mm-ap-links-features-features-articletoc',
		            'inputs'            => array(
			            '1' => $form->text( $feature_specs['article_toc_width_1'] + array(
					            'value'             => $this->kb_config['article_toc_width_1'],
					            'input_group_class' => 'config-col-12',
					            'class'             => 'ekb-color-picker',
					            'label_class'       => 'config-col-5',
					            'input_class'       => 'config-col-5'
				            ) ),
			            '2' => $form->text( $feature_specs['article_toc_media_1'] + array(
					            'value'             => $this->kb_config['article_toc_media_1'],
					            'input_group_class' => 'config-col-12',
					            'class'             => 'ekb-color-picker',
					            'label_class'       => 'config-col-5',
					            'input_class'       => 'config-col-5'
				            ) ),
			            '3' => $form->text( $feature_specs['article_toc_width_2'] + array(
					            'value'             => $this->kb_config['article_toc_width_2'],
					            'input_group_class' => 'config-col-12',
					            'class'             => 'ekb-color-picker',
					            'label_class'       => 'config-col-5',
					            'input_class'       => 'config-col-5'
				            ) ),
			            '4' => $form->text( $feature_specs['article_toc_media_2'] + array(
					            'value'             => $this->kb_config['article_toc_media_2'],
					            'input_group_class' => 'config-col-12',
					            'class'             => 'ekb-color-picker',
					            'label_class'       => 'config-col-5',
					            'input_class'       => 'config-col-5'
				            ) ),
			            '5' => $form->text( $feature_specs['article_toc_media_3'] + array(
					            'value'             => $this->kb_config['article_toc_media_3'],
					            'input_group_class' => 'config-col-12',
					            'class'             => 'ekb-color-picker',
					            'label_class'       => 'config-col-5',
					            'input_class'       => 'config-col-5'
				            ) ),
		            )
	            ));



                // FEATURES - Breadcrumb
                $form->option_group( $feature_specs, array(
                    'option-heading'    => 'Breadcrumb',
                    'class'             => 'eckb-mm-ap-links-features-features-breadcrumb',
                    'inputs'            => array(
                        '0' => $form->checkbox( $feature_specs['breadcrumb_enable'] + array(
                                'value'             => $this->kb_config['breadcrumb_enable'],
                                'input_group_class' => 'config-col-12',
                                'label_class'       => 'config-col-5',
                                'input_class'       => 'config-col-2'
                            ) ),
                        '2' => $form->dropdown( $feature_specs['breadcrumb_icon_separator'] + array(
                                'value'             => $this->kb_config['breadcrumb_icon_separator'],
                                'current'           => $this->kb_config['breadcrumb_icon_separator'],
                                'input_group_class' => 'config-col-12',
                                'label_class'       => 'config-col-5',
                                'input_class'       => 'config-col-5'
                            ) ),
                        '3' => $form->text( $feature_specs['breadcrumb_text_color'] + array(
                                'value'             => $this->kb_config['breadcrumb_text_color'],
                                'input_group_class' => 'config-col-12',
                                'class'             => 'ekb-color-picker',
                                'label_class'       => 'config-col-5',
                                'input_class'       => 'config-col-5 ekb-color-picker'
                            ) ),
                        '4' => $form->text( $feature_specs['breadcrumb_description_text'] + array(
                                'value' => $this->kb_config['breadcrumb_description_text'],
                                'input_group_class' => 'config-col-12',
                                'label_class'       => 'config-col-5',
                                'input_class'       => 'config-col-5'
                            ) ),
                        '5' => $form->text( $feature_specs['breadcrumb_home_text'] + array(
                                'value'             => $this->kb_config['breadcrumb_home_text'],
                                'input_group_class' => 'config-col-12',
                                'label_class'       => 'config-col-5',
                                'input_class'       => 'config-col-5'
                            ) ),
                    )
                ));


                // FEATURES - Breadcrumb - Advanced
                $arg_bc_top1 = $feature_specs['breadcrumb_padding_top'] + array( 'value' => $this->kb_config['breadcrumb_padding_top'], 'current' => $this->kb_config['breadcrumb_padding_top'], 'text_class' => 'config-col-6' );
                $arg_bc_btm2 = $feature_specs['breadcrumb_padding_bottom'] + array( 'value' => $this->kb_config['breadcrumb_padding_bottom'], 'current' => $this->kb_config['breadcrumb_padding_bottom'], 'text_class' => 'config-col-6' );
                $arg_bc_left3 = $feature_specs['breadcrumb_padding_left'] + array( 'value' => $this->kb_config['breadcrumb_padding_left'], 'current' => $this->kb_config['breadcrumb_padding_left'], 'text_class' => 'config-col-6' );
                $arg_bc_right4 = $feature_specs['breadcrumb_padding_right'] + array( 'value' => $this->kb_config['breadcrumb_padding_right'], 'current' => $this->kb_config['breadcrumb_padding_right'], 'text_class' => 'config-col-6' );

                //Breadcrumb: Margin
	            $arg_bc_margin_top      = $feature_specs['breadcrumb_margin_top'] + array( 'value' => $this->kb_config['breadcrumb_margin_top'], 'current' => $this->kb_config['breadcrumb_margin_top'], 'text_class' => 'config-col-6' );
	            $arg_bc_margin_bottom   = $feature_specs['breadcrumb_margin_bottom'] + array( 'value' => $this->kb_config['breadcrumb_margin_bottom'], 'current' => $this->kb_config['breadcrumb_margin_bottom'], 'text_class' => 'config-col-6' );
	            $arg_bc_margin_left     = $feature_specs['breadcrumb_margin_left'] + array( 'value' => $this->kb_config['breadcrumb_margin_left'], 'current' => $this->kb_config['breadcrumb_margin_left'], 'text_class' => 'config-col-6' );
	            $arg_bc_margin_right    = $feature_specs['breadcrumb_margin_right'] + array( 'value' => $this->kb_config['breadcrumb_margin_right'], 'current' => $this->kb_config['breadcrumb_margin_right'], 'text_class' => 'config-col-6' );


	            $form->option_group( $feature_specs, array(
                    'option-heading'    => 'Breadcrumb - Advanced',
                    'class'             => 'eckb-mm-ap-links-features-features-breadcrumb',
                    'inputs'            => array(

	                    '0' => $form->multiple_number_inputs(
		                    array(
			                    'id'                => 'breadcrumb_padding_group',
			                    'input_group_class' => '',
			                    'main_label_class'  => '',
			                    'input_class'       => '',
			                    'label'             => 'Padding( px )'
		                    ),
		                    array( $arg_bc_top1, $arg_bc_btm2, $arg_bc_left3, $arg_bc_right4 )
	                    ),
	                    '1' => $form->multiple_number_inputs(
		                    array(
			                    'id'                => 'breadcrumb_margin_group',
			                    'input_group_class' => '',
			                    'main_label_class'  => '',
			                    'input_class'       => '',
			                    'label'             => 'Margin( px )'
		                    ),
		                    array( $arg_bc_margin_top, $arg_bc_margin_bottom, $arg_bc_margin_left, $arg_bc_margin_right )
	                    ),
                    )));


	            // FETAURES - other
	            $form->option_group( $feature_specs, array(
		            'option-heading'    => 'Other',
		            'class'             => 'eckb-mm-ap-links-features-features-other',
		            'inputs'            => array(
			            '1' => $form->text( $feature_specs['last_udpated_on_text'] + array(
					            'value'             => $this->kb_config['last_udpated_on_text'],
					            'input_group_class' => 'config-col-12',
					            'label_class'       => 'config-col-5',
					            'input_class'       => 'config-col-7'
				            ) ),
			            '3' => $form->text( $feature_specs['created_on_text'] + array(
					            'value'             => $this->kb_config['created_on_text'],
					            'input_group_class' => 'config-col-12',
					            'label_class'       => 'config-col-5',
					            'input_class'       => 'config-col-7'
				            ) ),
			            '5' => $form->text( $feature_specs['author_text'] + array(
					            'value'             => $this->kb_config['author_text'],
					            'input_group_class' => 'config-col-12',
					            'label_class'       => 'config-col-5',
					            'input_class'       => 'config-col-7'
				            ) ),					
						'6' => $form->radio_buttons_vertical( $feature_specs['article_meta_icon_on'] + array(
					            'value'             => $this->kb_config['article_meta_icon_on'],
					            'current'           => $this->kb_config['article_meta_icon_on'],
					            'input_group_class' => 'config-col-12',
					            'main_label_class'  => 'config-col-4',
					            'input_class'       => 'config-col-8',
					            'radio_class'       => 'config-col-12'
							) ),		
                        /* '7' => $form->dropdown( $feature_specs['date_format'] + array(
                                'value'             => $this->kb_config['date_format'],
                                'current'           => $this->kb_config['date_format'],
		                        'input_group_class' => 'config-col-12',
		                        'label_class'       => 'config-col-5',
		                        'input_class'       => 'config-col-7'
                            ) ), */
			            '8' => $form->dropdown( $feature_specs['categories_layout_list_mode'] + array(
					            'value' => $this->kb_config['categories_layout_list_mode'],
					            'current' => $this->kb_config['categories_layout_list_mode'],
					            'input_group_class' => 'config-col-12',
					            'main_label_class'  => 'config-col-3',
					            'label_class' => 'config-col-5',
					            'input_class' => 'config-col-4'
				            ) ),
		            )
	            )); ?>
            </div>
        </div>      <?php

        return ob_get_clean();
    }

	/**
	 * Generate form fields for the ARTICLE PAGE side bar
	 */
	public function get_article_page_general_form() {
		ob_start();     ?>

		<div class="epkb-config-sidebar" id="epkb-config-article-general-sidebar">
			<div class="epkb-config-sidebar-options">
                <!-- ARTICLE COMMON PATH ( URL ) -->
                <div class="kb_articles_common_path_group" id="kb_articles_common_path_group">			   <?php
                    $this->display_articles_common_path();     ?>
                </div>
			</div>
		</div>      <?php

		return ob_get_clean();
	}

	/**
	 * Generate form fields for the ARTICLE PAGE side bar
	 *
	 * @param bool $is_advanced_search
	 * @return string
	 */
	public function get_article_page_styles_form( $is_advanced_search=false ) {

        ob_start();        ?>

        <div class="epkb-config-sidebar" id="epkb-config-article-styles-sidebar">
            <div class="epkb-config-sidebar-options" id="epkb_style_sidebar_options">                <?php
	            // if we have Sidebar on the Main Page then pretend the Article Page has just Article so that we don't get twice
	            // these inputs
	            if ( $is_advanced_search || ($this->kb_main_page_layout != EPKB_KB_Config_Layouts::SIDEBAR_LAYOUT && $this->kb_article_page_layout == EPKB_KB_Config_Layouts::SIDEBAR_LAYOUT) ) {
		            apply_filters( 'epkb_article_page_style_settings', $this->kb_article_page_layout, $this->kb_config );
	            }   ?>

            </div>
        </div>      <?php

        return ob_get_clean();
    }

	/**
	 * Generate form fields for the ARTICLE PAGE side bar
	 *
	 * @param $is_advanced_search
	 * @return string
	 */
	public function get_article_page_colors_form( $is_advanced_search=false ) {

        ob_start();         ?>

		<div class="epkb-config-sidebar" id="epkb-config-article-colors-sidebar">
			<div class="epkb-config-sidebar-options">       <?php
				// if we have Sidebar on the Main Page then pretend the Article Page has just Article so that we don't get twice
				// these inputs
				if ( $is_advanced_search || ($this->kb_main_page_layout != EPKB_KB_Config_Layouts::SIDEBAR_LAYOUT && $this->kb_article_page_layout == EPKB_KB_Config_Layouts::SIDEBAR_LAYOUT) ) {
					apply_filters( 'epkb_article_page_colors_settings', $this->kb_article_page_layout, $this->kb_config );
				}				 ?>
			</div>
		</div>      <?php

		return ob_get_clean();
	}

	/**
	 * Generate form fields for the ARTICLE PAGE side bar
	 *
	 * @param bool $is_advanced_search
	 * @return string
	 */
	public function get_article_page_text_form( $is_advanced_search=false ) {

        ob_start();     ?>

		<div class="epkb-config-sidebar" id="epkb-config-article-text-sidebar">
			<div class="epkb-config-sidebar-options">   <?php
				// if we have Sidebar on the Main Page then pretend the Article Page has just Article so that we don't get twice
				// these inputs
				if ( $is_advanced_search ||  ($this->kb_main_page_layout != EPKB_KB_Config_Layouts::SIDEBAR_LAYOUT && $this->kb_article_page_layout == EPKB_KB_Config_Layouts::SIDEBAR_LAYOUT) ) {
					apply_filters( 'epkb_article_page_text_settings', $this->kb_article_page_layout, $this->kb_config );
				}				 ?>
			</div>
		</div>      <?php

		return ob_get_clean();
	}

	/**************************************************************************************
	 *
	 *                   CATEGORY ARCHIVE PAGE
	 *
	 *************************************************************************************/

	/**
	 * Generate form fields for the Archive Version 2
	 */
	private function display_archive_page_sections() {
		echo $this->get_archive_page_version_2();
	}

	/**
	 * Show the Archive Page section
	 * @return false|string
	 */
	public function get_archive_page_version_2() {
		ob_start();

		$feature_specs = EPKB_KB_Config_Specs::get_fields_specification( $this->kb_config['id'] );
		$form = new EPKB_KB_Config_Elements();		?>

		<div class="epkb-config-sidebar" id="epkb-config-archive-features-sidebar">
			<div class="epkb-config-sidebar-options">   					<?php

				// Container
				$form->option_group( $feature_specs, array(
						'option-heading'    => 'Archive Main',
						'class'             => 'eckb-mm-arch-links-tuning-pagestructure-setup',
						'inputs'            => array(
								'0' => $form->text( $feature_specs['archive-container-width-v2'] +
								                    array( $this->kb_config['archive-container-width-v2'],
										                    'value'             => $this->kb_config['archive-container-width-v2'],
										                    'input_group_class' => 'config-col-12',
										                    'label_class'       => 'config-col-5',
										                    'input_class'       => 'config-col-5'
								                    ) ),
								'1' => $form->dropdown( $feature_specs['archive-container-width-units-v2'] + array(
												'value' => $this->kb_config['archive-container-width-units-v2'],
												'current' => $this->kb_config['archive-container-width-units-v2'],
												'input_group_class' => 'config-col-12',
												'label_class' => 'config-col-5',
												'input_class' => 'config-col-4'
										) ),
						)
				));

				// Left Sidebar
				$form->option_group( $feature_specs, array(
						'option-heading'    => 'Archive - Left Sidebar',
						'class'             => 'eckb-mm-arch-links-tuning-pagestructure-setup',
						'inputs'            => array(
							// Turn on left Sidebar
								/* '0' => $form->checkbox( $feature_specs['archive-left-sidebar-on-v2'] + array(
												'value'             => $this->kb_config['archive-left-sidebar-on-v2'],
												'id'                => 'archive-left-sidebar-on-v2',
												'input_group_class' => 'config-col-12',
												'label_class'       => 'config-col-5',
												'input_class'       => 'config-col-2'
										) ), */
							// Sidebar width
								'1' => $form->text( $feature_specs['archive-left-sidebar-width-v2'] +
								                    array( $this->kb_config['archive-left-sidebar-width-v2'],
										                    'value'             => $this->kb_config['archive-left-sidebar-width-v2'],
										                    'input_group_class' => 'config-col-12',
										                    'label_class'       => 'config-col-5',
										                    'input_class'       => 'config-col-5'
								                    ) ),
							// Sidebar Padding
								'2' => $form->text( $feature_specs['archive-left-sidebar-padding-v2'] +
								                    array( $this->kb_config['archive-left-sidebar-padding-v2'],
										                    'value'             => $this->kb_config['archive-left-sidebar-padding-v2'],
										                    'input_group_class' => 'config-col-12',
										                    'label_class'       => 'config-col-5',
										                    'input_class'       => 'config-col-5'
								                    ) ),
							// Sidebar Background Color
								'3' => $form->text( $feature_specs['archive-left-sidebar-background-color-v2'] + array(
												'value' =>  $this->kb_config['archive-left-sidebar-background-color-v2'],
												'input_group_class' => 'config-col-12',
												'class'             => 'ekb-color-picker',
												'label_class'       => 'config-col-5',
												'input_class'       => 'config-col-7 ekb-color-picker'
										) ),
						)
				));

				// Content
				$form->option_group( $feature_specs, array(
						'option-heading'    => 'Archive - Content Area',
						'class'             => 'eckb-mm-arch-links-tuning-pagestructure-setup',
						'inputs'            => array(
							// Content Width
								'0' => $form->text( $feature_specs['archive-content-width-v2'] +
								                    array( $this->kb_config['archive-content-width-v2'],
										                    'value'             => $this->kb_config['archive-content-width-v2'],
										                    'input_group_class' => 'config-col-12',
										                    'label_class'       => 'config-col-5',
										                    'input_class'       => 'config-col-5'
								                    ) ),
							// Content Padding
								'1' => $form->text( $feature_specs['archive-content-padding-v2'] +
								                    array( $this->kb_config['archive-content-padding-v2'],
										                    'value'             => $this->kb_config['archive-content-padding-v2'],
										                    'input_group_class' => 'config-col-12',
										                    'label_class'       => 'config-col-5',
										                    'input_class'       => 'config-col-5'
								                    ) ),
							// Content Background Color
								'2' => $form->text( $feature_specs['archive-content-background-color-v2'] + array(
												'value' =>  $this->kb_config['archive-content-background-color-v2'],
												'input_group_class' => 'config-col-12',
												'class'             => 'ekb-color-picker',
												'label_class'       => 'config-col-5',
												'input_class'       => 'config-col-7 ekb-color-picker'
										) ),
						)
				));

				// Right Sidebar

				/*$form->option_group( $feature_specs, array(
						'option-heading'    => 'Archive - Right Sidebar',
						'class'             => 'eckb-mm-arch-links-tuning-pagestructure-setup',
						'inputs'            => array(
							// Turn on right Sidebar
							'0' => $form->checkbox( $feature_specs['archive-right-sidebar-on-v2'] + array(
												'value'             => $this->kb_config['archive-right-sidebar-on-v2'],
												'id'                => 'archive-right-sidebar-on-v2',
												'input_group_class' => 'config-col-12',
												'label_class'       => 'config-col-5',
												'input_class'       => 'config-col-2'
										) ),
							// Sidebar width
								'1' => $form->text( $feature_specs['archive-right-sidebar-width-v2'] +
								                    array( $this->kb_config['archive-right-sidebar-width-v2'],
										                    'value'             => $this->kb_config['archive-right-sidebar-width-v2'],
										                    'input_group_class' => 'config-col-12',
										                    'label_class'       => 'config-col-5',
										                    'input_class'       => 'config-col-5'
								                    ) ),
							// Sidebar Padding
								'2' => $form->text( $feature_specs['archive-right-sidebar-padding-v2'] +
								                    array( $this->kb_config['archive-right-sidebar-padding-v2'],
										                    'value'             => $this->kb_config['archive-right-sidebar-padding-v2'],
										                    'input_group_class' => 'config-col-12',
										                    'label_class'       => 'config-col-5',
										                    'input_class'       => 'config-col-5'
								                    ) ),
							// Sidebar Background Color
								'3' => $form->text( $feature_specs['archive-right-sidebar-background-color-v2'] + array(
												'value' =>  $this->kb_config['archive-right-sidebar-background-color-v2'],
												'input_group_class' => 'config-col-12',
												'class'             => 'ekb-color-picker',
												'label_class'       => 'config-col-5',
												'input_class'       => 'config-col-7 ekb-color-picker'
										) ),
						)
				));*/

				//Advanced Settings
				$form->option_group( $feature_specs, array(
						'option-heading'    => 'Advanced',
						'class'             => 'eckb-mm-arch-links-tuning-pagestructure-setup',
						'inputs'            => array(

							// Small Screen ( Mobile ) 1 Column Layout Breakpoint. This will make the 3 Columns turn into 1 column stacked layout.
								'0' => $form->text( $feature_specs['archive-mobile-break-point-v2'] +
								                    array( $this->kb_config['archive-mobile-break-point-v2'],
										                    'value'             => $this->kb_config['archive-mobile-break-point-v2'],
										                    'input_group_class' => 'config-col-12',
										                    'label_class'       => 'config-col-6',
										                    'input_class'       => 'config-col-5'
								                    ) ),
						)
				));				?>

			</div>
		</div>		<?php

		return ob_get_clean();
	}


	/**************************************************************************************
	 *
	 *                   OTHERS / SUPPORT FUNCTIONS
	 *
	 *************************************************************************************/

	/**
	 * Show list of commmon paths for articles
	 *
	 */
	public function display_articles_common_path() {

        $common_path = $this->kb_config['kb_articles_common_path'];

        // find if one of the KB Main Pages is selected; if not and we don't have custom path, select first one
        $selected_post_id = 0;
        $first_post_id = 0;
        $kb_main_pages_info = EPKB_KB_Handler::get_kb_main_pages( $this->kb_config );
        foreach ( $kb_main_pages_info as $post_id => $post_info ) {
            $first_post_id = empty($first_post_id) ? $post_id : $first_post_id;
            if ( $post_info['post_slug'] == $common_path ) {
                $selected_post_id = $post_id;
            }
        }

        $selected_post_id = empty($common_path) ? $first_post_id : $selected_post_id;

		$this->form->option_group( $this->feature_specs, array(
			'option-heading'    => 'Article Path',
			'info'              => '<p>This is recommended for advanced users, support will be at a minimum for more information about
			                                       this feature read more information <a href="https://codex.wordpress.org/Glossary#Slug" target="_blank">here
													on wordpress.org</a></p>',
			'class'             => 'eckb-mm-ap-links-tuning-articlecommonpath-configuration',
			'inputs'            => array(
				'0'         => $this->common_path_kb_main_page_slug( $selected_post_id ),
				'1'         => $this->common_path_custom_slug( $common_path, $selected_post_id )
			)
		));
		
		$this->form->option_group( $this->feature_specs, array(
			'option-heading'    => 'Category Path',
			'info'              => '<p>This is recommended for advanced users, support will be at a minimum for more information about
			                                       this feature read more information <a href="https://codex.wordpress.org/Glossary#Slug" target="_blank">here
													on wordpress.org</a></p>',
			'class'             => 'eckb-mm-ap-links-tuning-articlecommonpath-configuration eckb-mm-ap-links-tuning-articlecommonpath-configuration--category_template',
			'inputs'            => array(
				'0' => $this->categories_in_url_enabled( isset($this->kb_config['categories_in_url_enabled']) && ($this->kb_config['categories_in_url_enabled'] == 'on') )
			)
		));
	}

    /**
     * Show list of commmon paths for articles
     *
     * @param $selected_post_id
     * @return string
     */
	public function common_path_kb_main_page_slug( $selected_post_id ) {
        $kb_main_pages_info = EPKB_KB_Handler::get_kb_main_pages( $this->kb_config );

		ob_start();	    ?>

		<div class="option-heading config-col-12">
			<p> <?php _e( 'KB Article URL', 'echo-knowledge-base' ); ?>:<br> &nbsp;&nbsp;&nbsp;<?php _e( 'website address / common path / KB article slug', 'echo-knowledge-base' ); ?></p>
		</div>
		<input type="hidden" id="epkb_common_path_changed" name="epkb_common_path_changed" value="no"/>

		<h4 class="main_label config-col-12"><?php _e( 'Common path set to KB Main Page slug', 'echo-knowledge-base' ); ?>:</h4>
		<div class="radio-buttons-vertical config-col-12" id="">
			<ul>               <?php

				$ix = 0;
				foreach( $kb_main_pages_info as $post_id => $post_info ) {

                    $kb_main_page_slug = $post_info['post_slug'];

                    // for static pages we don't have KB Page slug
                   /* if ( 'page' == get_option( 'show_on_front' ) && $first_page_id = get_option( 'page_on_front' ) ) {
                        $kb_main_page_slug = '';
                    } */

					$checked1 = $post_id == $selected_post_id ? 'checked="checked" ' : '';
					$label = site_url() . '/<strong><a href="' . get_edit_post_link( $post_id ) . '" target="_blank">' . esc_html(urldecode($kb_main_page_slug)) . "</a></strong>" .
                             ( empty($kb_main_page_slug) ? '' : '/' ) . '<span style="font-style:italic;">' . __( 'KB-article-slug', 'echo-knowledge-base' ) . '</span>';    			?>

					<li class="config-col-12">
						<div class="input_container config-col-1">
							<input type="radio" name="kb_articles_common_path_rbtn" class="epkb_art_common_path_button"
							       id="<?php echo 'article_common_path_' . $ix; ?>"
							       value="<?php echo esc_html(urldecode($kb_main_page_slug)); ?>"
								<?php echo $checked1; ?>  />
						</div>
						<label class="config-col-10" for="<?php echo 'article_common_path_' . $ix ?>">
							<?php echo $label ?>
						</label>
					</li>  		<?php

					$ix++;
				}

				if ( $ix == 0 ) {   ?>
					<li class="config-col-12"><?php _e( 'No KB Main Page found.', 'echo-knowledge-base' ); ?></li>      <?php
				}     ?>

			</ul>
		</div>		<?php

		return ob_get_clean();
	}

    /**
     * Show custom path for articles common path
     *
     * @param $common_path
     * @param $selected_post_id
     * @return string
     */
	private function common_path_custom_slug( $common_path, $selected_post_id ) {

		ob_start();		?>

		<div class="kb_custom_slug kb_articles_common_path_group" id="kb_articles_common_path_group">
			<h4 class="main_label config-col-12"><?php _e( 'Common path set to custom slug', 'echo-knowledge-base' ); ?>:</h4>
			<div class="radio-buttons-vertical config-col-12" id="">
				<ul>   			<?php
                    $shared_path_input = empty($selected_post_id) ? $common_path : '';
                    $checked2 = empty($shared_path_input) ? '' : 'checked="checked" ';
                    $label = site_url() . '/' . ' <input type="text" name="kb_articles_common_path" id="kb_articles_common_path" autocomplete="off"
                                                                       value="' . esc_html(urldecode( $shared_path_input ) ) . '" placeholder="Enter slug here" maxlength="50"
                                                                        style="width: 250px;">/<span style="font-style:italic;">' . __( 'KB-article-slug', 'echo-knowledge-base' ) . '</span>'; ?>
                    <li class="config-col-12">
                        <div class="input_container config-col-1">
                            <input type="radio" name="kb_articles_common_path_rbtn" class="epkb_art_common_path_button"
                                   id="<?php echo 'article_common_path_99'; ?>"
                                   value="path_custom_slug"
                                <?php echo $checked2; ?> />
                        </div>
                        <label class="config-col-10" for="<?php echo 'article_common_path_99' ?>">
                            <?php echo $label ?>
                        </label>
                    </li>

                    <li class="config-col-12" style="color:red;"><?php _e( 'For expert users only. Backup your site first. This can break your site navigation! Limited support available.', 'echo-knowledge-base' ); ?></li>
				</ul>
			</div>
		</div>		<?php

		return ob_get_clean();
	}

	/**
	 * Show categories radio box
	 *
	 * @param $is_enabled
	 * @return string
	 */
	private function categories_in_url_enabled( $is_enabled ) {

		ob_start(); ?>

		<div class="kb_custom_slug kb_categories_url_group" id="kb_categories_url_group">
			<h4 class="main_label config-col-12"><?php _e( 'Categories in URL', 'echo-knowledge-base' ); ?>:</h4>
			<div class="radio-buttons-vertical config-col-12" id="">
				<ul>
                    <li class="config-col-12">
                        <div class="input_container config-col-1">
                            <input type="radio" name="categories_in_url_enabled"
                                   id="<?php echo 'categories_in_url_enabled_on'; ?>"
                                   value="on"
                                <?php checked( $is_enabled ); ?> />
                        </div>
                        <label class="config-col-10" for="<?php echo 'categories_in_url_enabled_on' ?>">
                            <?php _e( 'On', 'echo-knowledge-base' ); ?>
                        </label>
                    </li>
					<li class="config-col-12">
                        <div class="input_container config-col-1">
                            <input type="radio" name="categories_in_url_enabled"
                                   id="<?php echo 'categories_in_url_enabled_off'; ?>"
                                   value="off"
                                <?php checked( ! $is_enabled ); ?> />
                        </div>
                        <label class="config-col-10" for="<?php echo 'categories_in_url_enabled_off' ?>">
                            <?php _e( 'Off', 'echo-knowledge-base' ); ?>
                        </label>
                    </li>

                    <li class="config-col-12" style="color:red;"><?php _e( 'For expert users only. Backup your site first. This can break your site navigation! Limited support available.', 'echo-knowledge-base' ); ?></li>
				</ul>
			</div>
		</div>		<?php

		return ob_get_clean();
	}
}
