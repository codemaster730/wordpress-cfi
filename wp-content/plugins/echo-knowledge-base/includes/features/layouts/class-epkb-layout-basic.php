<?php

/**
 *  Outputs the Basic Layout for knowledge base main page.
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 */
class EPKB_Layout_Basic extends EPKB_Layout {

	private $displayed_article_ids = array();

    /**
	 * Generate content of the KB main page
	 */
	public function generate_kb_main_page() {

		$class2 = $this->get_css_class( '::width' );		    ?>

		<div id="epkb-main-page-container" role="main" aria-labelledby="Knowledge Base" class="epkb-css-full-reset epkb-basic-template <?php echo method_exists( 'EPKB_Utilities', 'get_active_theme_classes' ) ? EPKB_Utilities::get_active_theme_classes( 'mp' ) : ''; ?>">
			<div <?php echo $class2; ?>>  <?php

				//  KB Search form
				$this->get_search_form();

				//  Knowledge Base Layout
				$style1 = $this->get_inline_style( 'background-color:: background_color' );				?>
				<div id="epkb-content-container" <?php echo $style1; ?> >

					<!--  Main Page Content -->
					<div class="epkb-section-container">	<?php
						$this->display_main_page_content(); ?>
					</div>

				</div>
			</div>
		</div>   <?php
	}

	/**
	 * Display KB Main Page content
	 */
	private function display_main_page_content() {

		// show message that articles are comming soon if the current KB does not have any Category
		if ( ! $this->has_kb_categories ) {
			$this->show_categories_missing_message();
			return;
		}

		$class0 = $this->get_css_class('::section_box_shadow, epkb-top-category-box');
		$style0 = $this->get_inline_style( 
					'border-radius:: section_border_radius,
					 border-width:: section_border_width,
					 border-color:: section_border_color,
					 background-color:: section_body_background_color, border-style: solid' );

		$class_section_head = $this->get_css_class( 'section-head' . ( $this->kb_config[ 'section_divider' ] == 'on' ? ', section_divider' : '' ) );
		$style_section_head = $this->get_inline_style(
					'border-bottom-width:: section_divider_thickness,
					background-color:: section_head_background_color, ' .
					'border-top-left-radius:: section_border_radius,
					border-top-right-radius:: section_border_radius,
					border-bottom-color:: section_divider_color,
					padding-top:: section_head_padding_top,
					padding-bottom:: section_head_padding_bottom,
					padding-left:: section_head_padding_left,
					padding-right:: section_head_padding_right'
		);
		$style3 = $this->get_inline_style(
					'color:: section_head_font_color,
					 text-align::section_head_alignment,
					 justify-content::section_head_alignment'
		);
		
		$style31 = $this->get_inline_style(
					'color:: section_head_font_color,
			 		typography:: section_head_typography'
		);
		$style4 = $this->get_inline_style(
					'color:: section_head_description_font_color,
					 text-align::section_head_alignment,
					 typography:: section_head_description_typography'
		);
		$style5 = 'border-bottom-width:: section_border_width,
					padding-top::    section_body_padding_top,
					padding-bottom:: section_body_padding_bottom,
					padding-left::   section_body_padding_left,
					padding-right::  section_body_padding_right,
					';

		if ( $this->kb_config['section_box_height_mode'] == 'section_min_height' ) {
			$style5 .= 'min-height:: section_body_height';
		} else if ( $this->kb_config['section_box_height_mode'] == 'section_fixed_height' ) {
			$style5 .= 'overflow: auto, height:: section_body_height';
		}

		// for each CATEGORY display: a) its articles and b) top-level SUB-CATEGORIES with its articles

		$class1 = $this->get_css_class( ' ::nof_columns, eckb-categories-list' );

		$categories_icons = $this->get_category_icons();

		$header_icon_style = $this->get_inline_style( 'color:: section_head_category_icon_color, font-size:: section_head_category_icon_size' );
		$header_image_style = $this->get_inline_style( 'max-height:: section_head_category_icon_size' );

		$icon_location = empty($this->kb_config['section_head_category_icon_location']) ? '' : $this->kb_config['section_head_category_icon_location'];
		
		$top_icon_class = 'epkb-category--' . $icon_location . '-cat-icon'; ?>

		<div <?php echo $class1; //Classes that are controlled by config settings ?>>   <?php

			/** DISPLAY BOXED CATEGORIES */
			$categoryNum = 0;
			$this->displayed_article_ids = array();
			foreach ( $this->category_seq_data as $box_category_id => $box_sub_categories ) {
				$categoryNum++;

				$category_name = isset($this->articles_seq_data[$box_category_id][0]) ?	$this->articles_seq_data[$box_category_id][0] : '';
				if ( empty($category_name) ) {
					continue;
				}

				$category_icon = EPKB_KB_Config_Category::get_category_icon( $box_category_id, $categories_icons );
				$category_desc = isset($this->articles_seq_data[$box_category_id][1]) && $this->kb_config['section_desc_text_on'] == 'on' ? $this->articles_seq_data[$box_category_id][1] : '';
				$box_sub_categories = is_array($box_sub_categories) ? $box_sub_categories : array();
				$box_category_data = $this->is_builder_on ? 'data-kb-category-id=' . $box_category_id . ' data-kb-type=category ' : '';  			?>

				<!-- Section Container ( Category Box ) -->
				<section id="<?php echo 'epkb_cat_' . $categoryNum; ?>" <?php echo $class0 . ' ' . $style0; ?> >

					<!-- Section Head -->
					<div <?php echo $class_section_head . ' ' . $style_section_head; ?> >

						<!-- Category Name + Icon -->
						<div class="epkb-category-level-1 <?php echo $top_icon_class; ?>" <?php echo $box_category_data . ' ' . $style3; ?> >

							<!-- Icon Top / Left -->	                            <?php
							if ( in_array( $icon_location, array('left', 'top') ) ) {

								if ( $category_icon['type'] == 'image' ) { ?>
									<img class="epkb-cat-icon epkb-cat-icon--image "
									     src="<?php echo esc_url($category_icon['image_thumbnail_url']); ?>" alt="<?php echo $category_icon['image_alt']; ?>"
										<?php echo $header_image_style; ?>
									>								<?php
								} else { ?>
									<span class="epkb-cat-icon epkbfa <?php echo esc_html( $category_icon['name'] ); ?>" data-kb-category-icon="<?php echo esc_html( $category_icon['name'] ); ?>" <?php echo $header_icon_style; ?>></span>	<?php
								}
							}

							// Get the URL of this category if link is on
							if ( $this->kb_config['section_hyperlink_on'] == 'on' ) {
								$category_link = EPKB_Utilities::get_term_url( $box_category_id );?>
								<h2 class="epkb-cat-name"><a href="<?php echo esc_url( $category_link ); ?>" <?php echo $style31; ?>><?php echo $category_name; ?></a></h2>		<?php
							} else {        ?>
								<h2 class="epkb-cat-name" <?php echo $style31; ?>><?php echo $category_name; ?></h2>							<?php
							}	?>

							<!-- Icon Right -->     <?php
							if ( $icon_location == 'right' ) {

								if ( $category_icon['type'] == 'image' ) { ?>
									<img class="epkb-cat-icon epkb-cat-icon--image"
									     src="<?php echo esc_url($category_icon['image_thumbnail_url']); ?>" alt="<?php echo $category_icon['image_alt']; ?>"
										<?php echo $header_image_style; ?>
									>								<?php
								} else { ?>
									<span class="epkb-cat-icon epkbfa <?php echo $top_icon_class . ' ' . esc_html( $category_icon['name'] ); ?>" data-kb-category-icon="<?php echo esc_html( $category_icon['name'] ); ?>" <?php echo $header_icon_style; ?>></span>	<?php
								}
							}       ?>

						</div>

						<!-- Category Description -->						<?php
						if ( $category_desc ) {   ?>
						    <p class="epkb-cat-desc" <?php echo $style4; ?> >
						        <?php echo $category_desc; ?>
						    </p>						<?php
						}       ?>
					</div>

					<!-- Section Body -->
					<div class="epkb-section-body" <?php echo $this->get_inline_style( $style5 ); ?> >						<?php 
						
						/** DISPLAY TOP-CATEGORY ARTICLES LIST */
						if (  $this->kb_config['show_articles_before_categories'] != 'off' ) {
							$this->display_articles_list( 1, $box_category_id, ! empty($box_sub_categories) );
						}
						
						if ( ! empty($box_sub_categories) ) {
							$this->display_box_sub_categories( $box_sub_categories );
						}
						
						/** DISPLAY TOP-CATEGORY ARTICLES LIST */
						if (  $this->kb_config['show_articles_before_categories'] == 'off' ) {
							$this->display_articles_list( 1, $box_category_id, ! empty($box_sub_categories) );
						}                      ?>

					</div><!-- Section Body End -->

				</section><!-- Section End -->  <?php

			}  ?>

		</div>       <?php
	}

	/**
	 * Display categories within the Box i.e. sub-categories
	 *
	 * @param $box_sub_category_list
	 */
	private function display_box_sub_categories( $box_sub_category_list ) {     	?>

		<ul class="epkb-sub-category eckb-sub-category-ordering"> <?php

			/** DISPLAY SUB-CATEGORIES */
			foreach ( $box_sub_category_list as $box_sub_category_id => $box_sub_sub_category_list ) {
				$category_name = isset($this->articles_seq_data[$box_sub_category_id][0]) ?
											$this->articles_seq_data[$box_sub_category_id][0] : __( 'Category.', 'echo-knowledge-base' );

				$class1 = $this->get_css_class( '::expand_articles_icon, epkb-category-level-2-3__cat-icon' );
				$style1 = $this->get_inline_style( 'color:: section_category_icon_color' );
				$style2 = $this->get_inline_style( 'color:: section_category_font_color' );

				$box_sub_category_data = $this->is_builder_on ? 'data-kb-category-id=' . $box_sub_category_id  . ' data-kb-type=sub-category ' : '';  	?>

				<li <?php echo $this->get_inline_style( 'padding-bottom:: article_list_spacing,padding-top::article_list_spacing' ); ?>>
					<div class="epkb-category-level-2-3" aria-expanded="false" <?php echo $box_sub_category_data; ?>>
						<span <?php echo $class1 . ' ' . $style1; ?>	></span>
						<h3 class="epkb-category-level-2-3__cat-name" tabindex="0" <?php echo $style2; ?> ><?php echo $category_name; ?></h3>
					</div>    <?php
					
					/** DISPLAY TOP-CATEGORY ARTICLES LIST */
					if (  $this->kb_config['show_articles_before_categories'] != 'off' ) {
						$this->display_articles_list( 2, $box_sub_category_id, ! empty($box_sub_sub_category_list) );
					}
						
					/** DISPLAY SUB-SUB-CATEGORIES */
					if ( ! empty($box_sub_sub_category_list) ) {
						$this->display_box_sub_sub_categories( $box_sub_sub_category_list, 'sub-', 3 );
					}
					
					/** DISPLAY TOP-CATEGORY ARTICLES LIST */
					if (  $this->kb_config['show_articles_before_categories'] == 'off' ) {
						$this->display_articles_list( 2, $box_sub_category_id, ! empty($box_sub_sub_category_list) );
					}					?>
				</li>  <?php
			}           ?>

		</ul> <?php
	}

	/**
	 * Display categories within the Box i.e. sub-sub-categories
	 *
	 * @param $box_sub_sub_category_list
	 * @param string $level_name
	 * @param int $level_num
	 */
	private function display_box_sub_sub_categories( $box_sub_sub_category_list, $level_name, $level_num ) {

		$level_name  .= 'sub-';
		$body_style1 = is_rtl() ? $this->get_inline_style( 'padding-right:: article_list_margin' ) : $this->get_inline_style( 'padding-left:: article_list_margin' );		?>
		<ul class="epkb-sub-sub-category eckb-sub-sub-category-ordering" <?php echo $body_style1; ?>> <?php

			/** DISPLAY SUB-SUB-CATEGORIES */
			foreach ( $box_sub_sub_category_list as $box_sub_sub_category_id => $box_sub_sub_sub_category_list ) {
				$category_name = isset($this->articles_seq_data[$box_sub_sub_category_id][0]) ?
				$this->articles_seq_data[$box_sub_sub_category_id][0] : __( 'Category.', 'echo-knowledge-base' );

				$class1 = $this->get_css_class( '::expand_articles_icon, epkb-category-level-2-3__cat-icon' );
				$style1 = $this->get_inline_style( 'color:: section_category_icon_color' );
				$style2 = $this->get_inline_style( 'color:: section_category_font_color' );

				$box_sub_category_data = $this->is_builder_on ? 'data-kb-category-id=' . $box_sub_sub_category_id  . ' data-kb-type=' . $level_name . 'category ' : '';  	?>

				<li <?php echo $this->get_inline_style( 'padding-bottom:: article_list_spacing,padding-top::article_list_spacing' ); ?>>
					<div class="epkb-category-level-2-3" aria-expanded="false" <?php echo $box_sub_category_data; ?>>
						<span <?php echo $class1 . ' ' . $style1; ?> ></span>
						<h<?php echo ( $level_num + 1); ?> class="epkb-category-level-2-3__cat-name" tabindex="0" <?php echo $style2; ?> ><?php echo $category_name; ?></h<?php echo ( $level_num + 1); ?> >
					</div>    <?php
					
					/** DISPLAY TOP-CATEGORY ARTICLES LIST */
					if (  $this->kb_config['show_articles_before_categories'] != 'off' ) {
						$this->display_articles_list( $level_num, $box_sub_sub_category_id, ! empty($box_sub_sub_sub_category_list), $level_name );
					}
					
					/** RECURSION DISPLAY SUB-SUB-...-CATEGORIES */
					if ( ! empty($box_sub_sub_sub_category_list) && strlen($level_name) < 20 ) {
						$this->display_box_sub_sub_categories( $box_sub_sub_sub_category_list, $level_name , $level_num + 1 );

					}
					
					/** DISPLAY TOP-CATEGORY ARTICLES LIST */
					if (  $this->kb_config['show_articles_before_categories'] == 'off' ) {
						$this->display_articles_list( $level_num, $box_sub_sub_category_id, ! empty($box_sub_sub_sub_category_list), $level_name );
					}    ?>
				</li>  <?php
			}           ?>

		</ul> <?php
	}

	/**
	 * Display list of articles that belong to given subcategory
	 *
	 * @param $level
	 * @param $category_id
	 * @param bool $sub_category_exists - if true then we don't want to show "Articles coming soon" if there are no articles because
	 *                                   we have at least categories listed.
	 * @param string $level_name
	 */
	private function display_articles_list( $level, $category_id, $sub_category_exists=false, $level_name='' ) {

		// retrieve articles belonging to given (sub) category if any
		$articles_list = array();
		if ( isset($this->articles_seq_data[$category_id]) ) {
			$articles_list = $this->articles_seq_data[$category_id];
			unset($articles_list[0]);
			unset($articles_list[1]);
		}

		// return if we have no articles and will not show 'Articles coming soon' message
		$articles_coming_soon_msg = $this->kb_config['category_empty_msg'];
		if ( empty( $articles_list ) && ( $sub_category_exists || empty( $articles_coming_soon_msg ) ) ) {
			return;
		}

		$sub_category_styles = is_rtl() ? 'padding-right:: article_list_margin' : 'padding-left:: article_list_margin';
		if ( $level == 1 ) {
			$data_kb_type = 'article';
			$sub_category_styles = '';
		} else if ( $level == 2 ) {
			$data_kb_type = 'sub-article';
		} else {
			$data_kb_type = empty($level_name) ? 'sub-sub-article' : $level_name . 'article';
		}

		$style = 'class="' . ( $level == 1 ? 'epkb-main-category ' : '' ) .  'epkb-articles"';		?>

		<ul <?php echo $style . ' ' . $this->get_inline_style( $sub_category_styles ); ?>> <?php

			$article_num = 0;
			$nof_articles_displayed = $this->kb_config['nof_articles_displayed'];
			foreach ( $articles_list as $article_id => $article_title ) {

				if ( ! EPKB_Utilities::is_article_allowed_for_current_user( $article_id ) ) {
					continue;
				}

				$article_num++;
				$this->displayed_article_ids[$article_id] = isset($this->displayed_article_ids[$article_id]) ? $this->displayed_article_ids[$article_id] + 1 : 1;
				$seq_no = $this->displayed_article_ids[$article_id];
				$hide_class = $article_num > $nof_articles_displayed ? 'epkb-hide-elem' : '';
				$article_data = $this->is_builder_on ? 'data-kb-article-id=' . $article_id . ' data-kb-type=' . $data_kb_type : '';

				/** DISPLAY ARTICLE LINK */         ?>
				<li class="epkb-article-level-<?php echo $level . ' ' . $hide_class; ?>" <?php echo $article_data . ' ' . $this->get_inline_style( 'padding-bottom:: article_list_spacing,padding-top::article_list_spacing' ); ?> >   <?php
								$article_link_data = 'class="epkb-mp-article" ' . 'data-kb-article-id=' . $article_id;
								$this->single_article_link( $article_title, $article_id, $article_link_data, '', $seq_no ); ?>
				</li> <?php
			}

			// if article list is longer than initial article list size then show expand/collapse message
			if ( $article_num > $nof_articles_displayed ) { ?>
				<button class="epkb-show-all-articles" aria-expanded="false">
					<span class="epkb-show-text">
						<?php echo esc_html( $this->kb_config['show_all_articles_msg'] ) . ' ( ' . ( $article_num - $nof_articles_displayed ); ?> )
					</span>
					<span class="epkb-hide-text epkb-hide-elem"><?php echo esc_html( $this->kb_config['collapse_articles_msg'] ); ?></span>
				</button>					<?php
			}

			if ( $article_num == 0 ) {
				echo '<li class="epkb-articles-coming-soon">' . esc_html( $articles_coming_soon_msg ) . '</li>';
			} ?>

		</ul> <?php
	}
}