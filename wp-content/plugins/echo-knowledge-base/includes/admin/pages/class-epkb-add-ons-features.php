<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Display New Features page
 *
 * @copyright   Copyright (C) 2019, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_Add_Ons_Features {

	/**
	 * Filter last features array to add latest
	 * @param $features
	 * @return array
	 */
	private static function features_list( $features=array() ) {


		/* $features['2021.09.01'] = array(
			'plugin'            => __( 'Help Dialog', 'echo-knowledge-base'),
			'title'             => __( 'Help dialog', 'echo-knowledge-base'),
			'description'       => '<p>' .sprintf( __( '%s Engage %s your website visitors and %sgain new customers%s with page-specific %s FAQs %s and %s nowledge base articles %s. Help users communicate with you ' .
			                                           '%s without leaving the page %s using a simple %s contact form %s shown with the Help Dialog.', 'echo-knowledge-base' ),
														'<strong>', '</strong>','<strong>', '</strong>','<strong>', '</strong>','<strong>', '</strong>','<strong>', '</strong>','<strong>', '</strong>' ) . '</p>',
			'image'             => Echo_Knowledge_Base::$plugin_url . 'img/featured-screenshots-help-dialog-example.jpg',
			'learn_more_url'    => 'https://wordpress.org/plugins/help-dialog/',
			'plugin-type'       => 'core',
			'type'              => 'new-feature'
		); */

		$features['2022.02.01'] = array(
			'plugin'            => __( 'KB Core', 'echo-knowledge-base'),
			'title'             => __( 'Convert Posts and CPTs into Articles', 'echo-knowledge-base'),
			'description'       => '<p>' . __( 'Convert blog and other posts as well as Custom Post Types into KB Articles.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2022/03/Featured-convert-posts-1.jpg',
			'learn_more_url'    => 'https://www.echoknowledgebase.com/documentation/convert-posts-cpts-to-articles/',
			'plugin-type'       => 'core',
			'type'              => 'new-feature'
		);

		$features['2022.01.01'] = array(
			'plugin'            => __( 'KB Core', 'echo-knowledge-base'),
			'title'             => __( 'Articles Index Directory - Shortcode', 'echo-knowledge-base'),
			'description'       => '<p>' . __( 'Use this shortcode in a page to list all articles alphabetically in three columns, grouped by first letter.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2022/02/article-index-directory-feature.png',
			'learn_more_url'    => 'https://www.echoknowledgebase.com/documentation/shortcode-articles-index-directory/',
			'plugin-type'       => 'core',
			'type'              => 'new-feature'
		);

		$features['2021.12.03'] = array(
			'plugin'            => __( 'Articles Import and Export', 'echo-knowledge-base'),
			'title'             => __( 'Migrate, Copy, Import and Export KB Content.', 'echo-knowledge-base'),
			'description'       => '<p>' . __( 'Import and export articles and their content, comments, categories, tags, and attachments. Migrate and copy articles between KBs. Edit articles outside of WordPress.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/edd/2020/08/KB-Import-Export-Banner.jpg',
			'learn_more_url'    => 'https://www.echoknowledgebase.com/wordpress-plugin/kb-articles-import-export/',
			'plugin-type'       => 'add-on',
			'type'              => 'new-feature'
		);

		$features['2021.11.02'] = array(
			'plugin'            => __( 'KB Core', 'echo-knowledge-base'),
			'title'             => __( 'Private Articles', 'echo-knowledge-base'),
			'description'       => '<p>' . __( "KB can now host private articles as well. This is not a substitute for full access control with Access Manager.", 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2021/11/Featured-Private-Articles.jpg',
			'plugin-type'       => 'core',
			'type'              => 'new-feature'
		);

		$features['2021.10.02'] = array(
			'plugin'            => __( 'KB Core', 'echo-knowledge-base'),
			'title'             => __( 'Control TOC Scroll Speed', 'echo-knowledge-base'),
			'description'       => '<p>' . __( "Configure TOC scroll speed as immediate or slow scroll.", 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2021/11/Featured-TOC-Scroll.jpg',
			'plugin-type'       => 'core',
			'type'              => 'new-feature'
		);

		$features['2021.09.03'] = array(
			'plugin'            => __( 'KB Core', 'echo-knowledge-base'),
			'title'             => __( 'Access Control to Admin Screens', 'echo-knowledge-base'),
			'description'       => '<p>' . __( "Choose what Authors and Editors can change and if they can view analytics.", 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2021/09/featured-screenshots-new-access.jpg',
			'plugin-type'       => 'core',
			'type'              => 'new-feature'
		);

		$features['2021.09.02'] = array(
			'plugin'            => __( 'KB Core', 'echo-knowledge-base'),
			'title'             => __( 'On/Off Option for Author, Date, Category', 'echo-knowledge-base'),
			'description'       => '<p>' . __( "On the Category Archive Page, the author, date and category can be turned on or off.", 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2021/09/category-archive-page-features.jpg',
			'plugin-type'       => 'core',
			'type'              => 'new-feature'
		);

		$features['2021.08.02'] = array(
			'plugin'            => __( 'Advanced Search', 'echo-knowledge-base'),
			'title'             => __( 'Filter HTML/CSS Keywords', 'echo-knowledge-base'),
			'description'       => '<p>' . __( "Words such as 'family', 'data', and 'option' could be hidden in the article's HTML/CSS. Exclude them from the user search unless they are inside the article content.", 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2021/08/Featured-Code-Improvements.jpg',
			'plugin-type'       => 'add-on',
			'type'              => 'new-feature'
		);

		/* TODO $features['2021.03.01'] = array(
			'plugin'            => __( 'Article Features BETA', 'echo-knowledge-base'),
			'title'             => __( 'Article Rating and Email Notifications', 'echo-knowledge-base'),
			'description'       => '<p>' . __( 'Current features: article rating with analytics, and email notifications (beta) for articles created and updated.', 'echo-knowledge-base') . '</p>' .
			                       ( EPKB_Utilities::is_elegant_layouts_enabled() || EPKB_Utilities::is_article_rating_enabled() || EPKB_Utilities::is_link_editor_enabled() ?
			                       '<p>' . __( 'If you do not have the new Article Features add-on in your bundle, you can get it for free.', 'echo-knowledge-base') .
			                       ' <a href="https://www.echoknowledgebase.com/documentation/bundle-users-get-article-features-for-free" target="_blank">' . __( 'Upgrade here', 'echo-knowledge-base') . '</a></p>' : '' ),
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2021/01/featured-screenshots-print-button.jpg',
			'learn_more_url'    => 'https://www.echoknowledgebase.com/wordpress-plugin/article-features/',
			'button_name'       => __( 'Learn More', 'echo-knowledge-base'),
			'plugin-type'       => 'add-on',
			'type'              => 'new-addon'
		); */

		$features['2021.05.02'] = array(
			'plugin'            => __( 'KB Core', 'echo-knowledge-base'),
			'title'             => __( 'Typography', 'echo-knowledge-base'),
			'description'       => '<p>' . __( 'Font size, family, and weight can now be configured for article title, article names, category names, TOC, breadcrumbs, back navigation, and search title.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2021/05/Featured-Typography-1.jpg',
			'learn_more_url'    => 'https://www.echoknowledgebase.com/documentation/typography-font-family-size-weight/',
			'button_name'       => __( 'Learn More', 'echo-knowledge-base'),
			'plugin-type'       => 'core',
			'type'              => 'new-feature'
		);

		$features['2021.05.01'] = array(
			'plugin'            => __( 'KB Core', 'echo-knowledge-base'),
			'title'             => __( 'Custom Icons for Sub-Categories', 'echo-knowledge-base'),
			'description'       => '<p>' . __( 'Categories Focused Layout can now have custom icons for its sub-categories.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2021/05/Featured-Custom-icons-Category-focused-1.jpg',
			'plugin-type'       => 'core',
			'type'              => 'new-feature'
		);

		$features['2021.04.02'] = array(
			'plugin'            => __( 'KB Core', 'echo-knowledge-base'),
			'title'             => __( 'Support for RTL', 'echo-knowledge-base'),
			'description'       => '<p>' . __( 'RTL (Right To Left) is a locale property indicating that text is written from right to left. This Knowledge Base fully supports RTL for both admin screens and frontend pages.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2021/05/Featured-RTL-1.jpg',
			'plugin-type'       => 'core',
			'type'              => 'new-feature'
		);

		$features['2021.04.01'] = array(
			'plugin'            => __( 'KB Core', 'echo-knowledge-base'),
			'title'             => __( 'Support for WCAG accessibility', 'echo-knowledge-base'),
			'description'       => '<p>' . __( 'KB has improved web accessibility so that people with visual impairments using screen readers can use it effectively.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2021/05/Featured-WCAG-1.jpg',
			'plugin-type'       => 'core',
			'type'              => 'new-feature'
		);

		$features['2021.03.01'] = array(
			'plugin'            => __( 'KB Core', 'echo-knowledge-base'),
			'title'             => __( 'Simple Search Analytics', 'echo-knowledge-base'),
			'description'       => '<p>' . __( 'Show a basic count of searches with articles found and with no results.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2021/03/new-features-basic-search-analytics.jpg',
			'learn_more_url'    => esc_url( admin_url('edit.php?post_type=epkb_post_type_1&page=epkb-plugin-analytics') ),
			'button_name'       => __( 'Try Now!', 'echo-knowledge-base'),
			'plugin-type'       => 'core',
			'type'              => 'new-feature'
		);

		$features['2021.02.01'] = array(
			'plugin'            => __( 'KB Core', 'echo-knowledge-base'),
			'title'             => __( 'Print Article', 'echo-knowledge-base'),
			'description'       => '<p>' . __( 'Display a print button so that users can easily print the article and save it as a PDF file. The printed article excludes the redundant site header and footer.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2021/01/featured-screenshots-print-button.jpg',
			'learn_more_url'    => ( epkb_get_instance()->kb_config_obj->get_value( 1, 'print_button_enable', null ) == 'on' ) ? EPKB_Editor_Utilities::get_one_editor_url( 'article_page', 'print_button' ) : 'https://www.echoknowledgebase.com/documentation/print-button/',
		   'button_name'       => __( 'Try Now!', 'echo-knowledge-base'),
		   'plugin-type'       => 'core',
			'type'              => 'new-feature'
		);

	   $features['2021.01.02'] = array(
		   'plugin'            => __( 'KB Core', 'echo-knowledge-base'),
		   'title'             => __( 'Design Article Header', 'echo-knowledge-base'),
		   'description'       => '<p>' . __( 'Change the order of elements in the article header. Move them up, down, left, or right. This applies to the article title, author, dates, print button, and breadcrumbs.', 'echo-knowledge-base') . '</p>',
		   'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2021/01/header-design.jpg',
		   'learn_more_url'    => EPKB_Editor_Utilities::get_one_editor_url( 'article_page', 'article_content' ),
		   'button_name'       => __( 'Try Now!', 'echo-knowledge-base'),
		   'plugin-type'       => 'core',
		   'type'              => 'new-feature'
	   );

		$features['2020.11.01'] = array(
			'plugin'            => __( 'Knowledge Base Visual Editor', 'echo-knowledge-base'),
			'title'             => __( 'Edit KB Pages', 'echo-knowledge-base'),
			'description'       => '<p>' . __( 'Change the style, colors, and features using the front-end Editor.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2020/11/front-end-editor.jpg',
			'learn_more_url'    => EPKB_Editor_Utilities::get_one_editor_url( 'main_page' ),
			'plugin-type'       => 'core',
			'type'              => 'new-feature',
			'button_name'       => __( 'Try Now', 'echo-knowledge-base'),
		);

		$features['2020.08.09'] = array(
			'plugin'            => __( 'Advanced Search', 'echo-knowledge-base'),
			'title'             => __( 'Advanced Search Shortcode for One or More KBs', 'echo-knowledge-base'),
			'description'       => '<p>' . __( 'Add Advanced Search box to any page like the Contact Us form. Search across multiple KBs.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2020/08/featured-screenshots-asea-shortcode.jpg',
			'learn_more_url'    => 'https://www.echoknowledgebase.com/documentation/shortcode-for-one-or-more-kbs/',
			'plugin-type'       => 'add-on',
			'type'              => 'new-feature'
		);

		$features['2020.07.02'] = array(
			'plugin'            => __( 'KB Core', 'echo-knowledge-base'),
			'title'             => __( 'Article Previous/Next Buttons', 'echo-knowledge-base'),
			'description'       => '<p>' . __( 'Allow your users to navigate to the next article or previous articles using the previous/next buttons.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2020/06/new-feature-article-navigation-btns.jpg',
			'learn_more_url'    => 'https://www.echoknowledgebase.com/documentation/meta-data-authors-dates/',
			'plugin-type'       => 'core',
			'type'              => 'new-feature'
		);

		$features['2020.06.01'] = array(
			'plugin'            => __( 'Advanced Search', 'echo-knowledge-base'),
			'title'             => __( 'Sub Category Filter', 'echo-knowledge-base'),
			'description'       => '<p>' . __( 'New sub-category filter option to narrow down your search.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2020/06/new-feature-sub-category-filter.jpg',
			'learn_more_url'    => 'https://www.echoknowledgebase.com/demo-11/',
			'plugin-type'       => 'core',
			'type'              => 'new-feature'
		);

		$features['2020.04.01'] = array(
			'plugin'            => __( 'KB Core', 'echo-knowledge-base'),
			'title'             => __( 'Article Sidebars', 'echo-knowledge-base'),
			'description'       => '<p>' . __( 'New article sidebars with the ability to add your own Widgets, TOC and custom code.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2020/04/new-feature-wizards-sidebars.jpg',
			'learn_more_url'    => 'https://www.echoknowledgebase.com/documentation/sidebar-overview/',
			'plugin-type'       => 'add-on',
			'type'              => 'new-feature'
		);

		$features['2020.03.01'] = array(
			'plugin'            => __( 'KB Core', 'echo-knowledge-base'),
			'title'             => __( 'Wizards', 'echo-knowledge-base'),
			'description'       => '<p>' . __( 'Use Knowledge Base Wizard for an easy way to set up your KB and to choose from predefined Templates and colors.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2020/02/new-feature-wizards.jpg',
			'learn_more_url'    => 'https://www.youtube.com/watch?v=5uI9q2ipZxU&utm_medium=newfeatures&utm_content=home&utm_campaign=wizards',
			'plugin-type'       => 'core',
			'type'              => 'new-feature'
		);

		$features['2020.02.18'] = array(
			'plugin'            => __( 'KB Core', 'echo-knowledge-base'),
			'title'             => __( 'Image Icons for Themes', 'echo-knowledge-base'),
			'description'       => '<p>' . __( 'Add Image icons to top categories in your theme. You can upload images or custom icons.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2020/02/image-icons-for-themes.jpg',
			'learn_more_url'    => 'https://www.echoknowledgebase.com/demo-12-knowledge-base-image-layout/?utm_source=plugin&utm_medium=newfeatures&utm_content=home&utm_campaign=image-icons',
			'plugin-type'       => 'core',
			'type'              => 'new-feature'
		);

		$features['2020.01.ac'] = array(
			'plugin'            => __( 'KB Core', 'echo-knowledge-base'),
			'title'             => __( 'Categories Focused Layout', 'echo-knowledge-base'),
			'description'       => '<p>' . __( 'New layout that focuses on showing categories in a sidebar on both Category Archive and Article pages.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2020/01/category-focused-layout.jpg',
			'learn_more_url'    => '',
			'plugin-type'       => 'core',
			'type'              => 'new-feature'
		);

		/*	$features['2020.01.df'] = array(
			   'plugin'            => 'KB Core',
			   'title'             => 'New Option for Date Formats',
			   'description'       => '<p>On Article pages, choose the format for the Last Updated and Created On dates.</p>',
			   'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2019/11/new-features-article-category-sequence.jpg',
			   'learn_more_url'    => '',
			   'plugin-type'       => 'core',
			   'type'              => 'new-feature'
		   ); */

		$features['2019.12.ac'] = array(
			'plugin'            => __( 'KB Core', 'echo-knowledge-base'),
			'title'             =>__(  'New Option to Show Articles Above Categories', 'echo-knowledge-base'),
			'description'       => '<p>' . __( 'On the Main Page (or Sidebar if you have the Elegant Layout add-on) the article can now be configured to appear above their peer categories and sub-categories.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2020/03/new-features-article-category-sequence-2.jpg',
			'learn_more_url'    => '',
			'plugin-type'       => 'core',
			'type'              => 'new-feature'
		);

		$features['2019.12.lv'] = array(
			'plugin'            => __( 'KB Core', 'echo-knowledge-base'),
			'title'             => __( 'Three Additional Levels of Categories', 'echo-knowledge-base'),
			'description'       => '<p>' . __( 'You can now organize your categories and articles up to six levels deep, allowing you to have more complex documentation hierarchy.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2019/11/new-feature-three-new-levels-3.jpg',
			'learn_more_url'    => '',
			'plugin-type'       => 'core',
			'type'              => 'new-feature'
		);

		$features['2019.12.oo'] = array(
			'plugin'            => __( 'KB Core', 'echo-knowledge-base'),
			'title'             => __( 'Table of Content on Article Pages', 'echo-knowledge-base'),
			'description'       => '<p>' . __( 'Articles can now display table of content (TOC) on either side. The TOC has a list of headings and subheading. Users can easily see the article structure and can navigate to any section of the article.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2019/11/new-feature-TOC-1.jpg',
			'learn_more_url'    => '',
			'plugin-type'       => 'core',
			'type'              => 'new-feature'
		);

		$features['2019.11.au'] = array(
			'plugin'            => __( 'KB Core', 'echo-knowledge-base'),
			'title'             => __( 'Articles Can Now Display Author and Creation Date', 'echo-knowledge-base'),
			'description'       => '<p>' . __( 'Configure article to display author and create date.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2019/11/new-feature-core-new-meta-1.jpg',
			'learn_more_url'    => '',
			'plugin-type'       => 'core',
			'type'              => 'new-feature'
		);

		$features['2019.11.rf'] = array(
			'plugin'            => __( 'Article Rating and Feedback', 'echo-knowledge-base'),
			'title'             => __( 'User Can Rate Articles and Submit Feedback', 'echo-knowledge-base'),
			'description'       => '<p>' . __( 'This new add-on allows users to rate articles. They can also opt to fill out a form to submit details about their vote. The admin can access the analytics to see the most and least rated articles.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2019/11/EP'.'RF-featured-image.jpg',
			'learn_more_url'    => 'https://www.echoknowledgebase.com/wordpress-plugin/article-rating-and-feedback/?utm_source=plugin&utm_medium=newfeatures&utm_content=home&utm_campaign=new-plugin',
			'plugin-type'       => 'add-on',
			'type'              => 'new-addon'
		);

		$features['2019.11.hc'] = array(
			'plugin'            => __( 'Advanced Search', 'echo-knowledge-base'),
			'title'             => __( 'Search Results Include Category for Each Article', 'echo-knowledge-base'),
			'description'       => '<p>' . __( 'Search category filter now shows category hierarchy each found article is in.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2019/11/AS'.'EA-feature-results-category.jpg',
			'learn_more_url'    => 'https://www.echoknowledgebase.com/wordpress-plugin/advanced-search/?utm_source=plugin&utm_medium=newfeatures&utm_content=home&utm_campaign=category-hierarchy',
			'plugin-type'       => 'add-on',
			'type'              => 'new-feature'
		);

		$features['2019.10.am'] = array(
			'plugin'            => __( 'KB Groups for Access Manager', 'echo-knowledge-base'),
			'title'             => __( 'Search Easily for Users to Add to KB Groups', 'echo-knowledge-base'),
			'description'       => '<p>' . __( 'The KB Groups add-on allows ordering of users into different groups and roles. The new search bar lets the administrator quickly find a specific user to make changes.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2019/11/search-users-1-1024x601.jpg',
			'learn_more_url'    => 'https://www.echoknowledgebase.com/documentation/2-3-wp-users/?utm_source=plugin&utm_medium=newfeatures&utm_content=home&utm_campaign=user-search',
			'plugin-type'       => 'add-on',
			'type'              => 'new-feature'
		);

		return $features;
	}

	/**
	 * Count new features to be used in New Features menu item title
	 * @param $count
	 * @return int
	 */
	private static function get_new_features_count( $count=0 ) {

		// if user did't see last new features
		$last_seen_version = EPKB_Utilities::get_wp_option( 'epkb_last_seen_version', '' );
		$features_list = self::features_list();
		foreach ( $features_list as $key => $val ) {
			if ( version_compare( $last_seen_version, $key ) < 0 ) {
				$count++;
			}
		}

		return $count;
	}

	/**
	 * Call when the user saw new features
	 */
	private static function update_last_seen_version() {

		$features_list = self::features_list();
		krsort($features_list);
		$last_feature_date = key( $features_list );

		$result = EPKB_Utilities::save_wp_option( 'epkb_last_seen_version', $last_feature_date, true );
		if ( is_wp_error( $result ) ) {
			EPKB_Logging::add_log( 'Could not update last seen features', $result );
			return false;
		}

		return true;
	}

	/**
	 * Get box that contains list of new features for a certain year
	 *
	 * @param $year_key
	 * @return false|string
	 */
	public static function get_new_features_box_by_year( $year_key ) {

		$features_list = self::get_new_features_list();

		ob_start();

		if ( isset( $features_list[$year_key] ) ) {     ?>
			<div class="epkb-features-container">       <?php
				self::display_new_features_details( $features_list[$year_key] );    ?>
			</div>      <?php
		}

		self::update_last_seen_version();  // clears menu count of versions not seen

		return ob_get_clean();
	}

	/**
	 * Display all new features
	 * add-ons
	 * $history = array('2019.1') = array([history_item],[history_item]...)
	 * @param $features
	 */
	private static function display_new_features_details( $features ) {
		foreach ( $features as $date => $feature ) {
			self::new_feature( $date, $feature );
		}
	}

	/**
	 * Get list of all new features
	 *
	 * @return array
	 */
	private static function get_new_features_list() {

		// get new features in last release
		$features = self::features_list(array());
		$features = empty($features) || ! is_array($features) ? array() : $features;

		$features_list = array();
		foreach ( $features as $date => $feature ) {
			$season = explode('.', $date);
			$key =  __( 'Year' ) . ' ' . $season[0];
			$features_list[$key][$date] = $feature;
		}
		return $features_list;
	}

	private static function group_by_month( $month ) {
		global $wp_locale;

		//Group 3 Month
		$month = ($month % 3 == 2) ? $month - 1 : ( ($month % 3 == 0) ? $month - 2 : $month );

		$monthName1 = ucfirst($wp_locale->get_month_abbrev($wp_locale->get_month($month)));
		$monthName2 = ucfirst($wp_locale->get_month_abbrev($wp_locale->get_month($month + 2)));

		return $monthName1 . ' - ' . $monthName2 . ' ' . date('Y');
	}

	/**
	 * Display list of CREL add-on features
	 */
	public static function display_crel_features_details() {
		$features = self::crel_features_list();
		$features = empty($features) || ! is_array($features) ? array() : $features;
		foreach ( $features as $date => $feature ) {
			self::new_feature( $date, $feature );
		}
	}

	/**
	 * Display feature information with image.
	 *
	 * @param $date
	 * @param array $args
	 */
	private static function new_feature( $date, $args = array () ) {
		global $wp_locale; 
		
		$season = explode('.', $date);
		$monthName = '';
		if ( ! empty($season[0]) && ! empty($season[1]) ) {
			$monthName = ucfirst($wp_locale->get_month_abbrev($wp_locale->get_month($season[1])));
			$date = $monthName . ' ' . $season[0];
		}

		$pluginType = '';
		switch ( $args['plugin-type']) {
			case 'add-on':
				$pluginType = '<div class="epkb-fnf__meta__addon">' . __( 'Add-on', 'echo-knowledge-base') . '</div>';
				break;
			case 'core':
				$pluginType = '<div class="epkb-fnf__meta__core">' . __( 'Core', 'echo-knowledge-base') . '</div>';
				break;
			case 'plugin':
				$pluginType = '<div class="epkb-fnf__meta__addon">' . __( 'Plugin', 'echo-knowledge-base') . '</div>';
				break;
		  case 'elementor':
			  $pluginType = '<div class="epkb-fnf__meta__addon">' . __( 'Elementor', 'echo-knowledge-base') . '</div>';
			  break;
		}

		$type = '';
		switch ( $args['type']) {
			case 'new-addon':
				$type = '<span class="epkb-fnf__header__new-add-on"> <i class="epkbfa epkbfa-plug" aria-hidden="true"></i> ' . __( 'New Add-on', 'echo-knowledge-base') . '</span>';
				break;
			case 'new-feature':
				$type = '<span class="epkb-fnf__header__new-feature">' . $monthName . '</span>';
				break;
			case 'new-plugin':
				$type = '<span class="epkb-fnf__header__new-add-on"> <i class="epkbfa epkbfa-plug " aria-hidden="true"></i>' . __( 'New Plugin', 'echo-knowledge-base') . '</span>';
				break;
		  case 'widget':
			  $type = '<span class="epkb-fnf__header__widget"> <i class="epkbfa epkbfa-puzzle-piece " aria-hidden="true"></i>' . __( 'Widget', 'echo-knowledge-base') . '</span>';
			  break;
		}		?>

		<div class="epkb-features__new-feature" class="add_on_product">

			<div class="epkb-fnf__header">
				<?php echo ( $args['plugin-type'] != 'elementor' ) ? $type : ''; ?>
				<h3 class="epkb-fnf__header__title"><?php esc_html_e( $args['title']); ?></h3>
			</div>		<?php
			if ( isset( $args['image']) ) { ?>
				<div class="featured_img epkb-fnf__img">
					<img src="<?php echo empty( $args['image']) ? '' : $args['image']; ?>">
				</div>			<?php
			}
			if ( isset( $args['video']) ) { ?>
				<div class="epkb-fnf__video">
					<iframe width="560" height="170" src="<?php echo $args['video']; ?>" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
				</div>			<?php
			}
			if ( $args['plugin-type'] != 'elementor' ) { ?>
	            <div class="epkb-fnf__meta">
					<?php echo $pluginType; ?>
					<div class="epkb-fnf__meta__plugin"><?php esc_html_e( $args['plugin']); ?></div>
					<div class="epkb-fnf__meta__date"><?php echo $date ?></div>
				</div>			<?php
			}   ?>
			<div class="epkb-fnf__body">
				<p>
					<?php echo wp_kses_post( $args['description']); ?>
				</p>
			</div>			<?php
			if ( ! empty( $args['learn_more_url'] ) ) {
			   $button_name = empty( $args['button_name']) ? __( 'Learn More', 'echo-knowledge-base' ) : $args['button_name'];    ?>
				<div class="epkb-fnf__button-container">
					<a class="epkb-primary-btn" href="<?php echo $args['learn_more_url']; ?>" target="_blank"><?php echo $button_name; ?></a>
				</div>			<?php
			}       ?>

		</div>    <?php
	}

	/**
	 * Filter crel features array to add latest
	 * @param $features
	 * @return array
	 */
	private static function crel_features_list( $features=array() ) {

		$features['2020.12.15'] = array(
			'plugin'            => __( 'Widget', 'creative-addons-for-elementor'),
			'title'             => __( 'Image Guide', 'creative-addons-for-elementor'),
			'description'       => '<p>' . __( "Add hotspots to screenshots and images, and connect each hotspot to a note.", 'creative-addons-for-elementor') . '</p>',
			'video'             => 'https://www.youtube.com/embed/SZEP_zxBvy4',
			'learn_more_url'    => 'https://www.creative-addons.com/elementor-widgets/image-guide/',
			'plugin-type'       => 'elementor',
			'type'              => 'widget'
		);

		$features['2020.12.16'] = array(
			'plugin'            => __( 'Widget', 'creative-addons-for-elementor'),
			'title'             => __( 'Text and Image', 'creative-addons-for-elementor'),
			'description'       => '<p>' . __( 'Easy way to add text and image combo with one widget.', 'creative-addons-for-elementor') . '</p>',
			'video'             => 'https://www.youtube.com/embed/0Lpi-M2i32U',
			'learn_more_url'    => 'https://www.creative-addons.com/elementor-widgets/text-image/',
			'plugin-type'       => 'elementor',
			'type'              => 'widget'
		);

		$features['2020.10.15'] = array(
			'plugin'            => __( 'Widget', 'echo-knowledge-base'),
			'title'             => __( 'Notification Box', 'echo-knowledge-base'),
			'description'       => '<p>' . __( "Provide important information using a prominent style to instantly catch reader's attention.", 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.creative-addons.com/wp-content/uploads/2020/10/Notification-box-features.jpg',
			'learn_more_url'    => 'https://www.creative-addons.com/elementor-widgets/notification-box/',
			'plugin-type'       => 'elementor',
			'type'              => 'widget'
		);

		$features['2020.10.16'] = array(
			'plugin'            => __( 'Widget', 'echo-knowledge-base'),
			'title'             => __( 'Advanced Heading', 'echo-knowledge-base'),
			'description'       => '<p>' . __( 'Create custom headings with lots of options. Add a link or a badge to take your documentation to the next level.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.creative-addons.com/wp-content/uploads/2020/10/advanced-heading-features.jpg',
			'learn_more_url'    => 'https://www.creative-addons.com/elementor-widgets/advanced-heading/',
			'plugin-type'       => 'elementor',
			'type'              => 'widget'
		);

		$features['2020.10.17'] = array(
			'plugin'            => __( 'Widget', 'echo-knowledge-base'),
			'title'             => __( 'Step-by-step / How To', 'echo-knowledge-base'),
			'description'       => '<p>' . __( 'Create amazing step-by-step documentation consistently and quickly with our powerful Steps widget.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.creative-addons.com/wp-content/uploads/2020/10/steps-features.jpg',
			'learn_more_url'    => 'https://www.creative-addons.com/elementor-widgets/steps/',
			'plugin-type'       => 'elementor',
			'type'              => 'widget'
		);

		$features['2021.05.20'] = array(
			'plugin'            => __( 'Widget', 'echo-knowledge-base'),
			'title'             => __( 'Code Block', 'echo-knowledge-base'),
			'description'       => '<p>' . __( "Embed source code examples in your article. The user can copy and expand the code. Show code examples in CSS, HTML, JS, PHP, C# and more.", 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.creative-addons.com/wp-content/uploads/2021/06/Code-block-top-image-5.png',
			'learn_more_url'    => 'https://www.creative-addons.com/elementor-widgets/code-block/',
			'plugin-type'       => 'elementor',
			'type'              => 'widget'
		);
		
		$features['2020.10.18'] = array(
		   'plugin'            => __( 'Widget', 'echo-knowledge-base'),
		   'title'             => __( 'Advanced Lists', 'echo-knowledge-base'),
		   'description'       => '<p>' . __( 'Make multi-level lists easily. Show levels as numbers, letters or in other formats.', 'echo-knowledge-base') . '</p>',
		   'image'             => 'https://www.creative-addons.com/wp-content/uploads/2020/10/advanced-list-features.jpg',
		   'learn_more_url'    => 'https://www.creative-addons.com/elementor-widgets/advanced-lists/',
		   'plugin-type'       => 'elementor',
		   'type'              => 'widget'
		);

		$features['2020.10.19'] = array(
			'plugin'            => __( 'Widget', 'echo-knowledge-base'),
			'title'             => __( 'KB Search', 'echo-knowledge-base'),
			'description'       => '<p>' . __( 'Add a Search Box to any page to search documentation stored in Echo Knowledge Base.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.creative-addons.com/wp-content/uploads/2020/10/kb-search-features.jpg',
			'learn_more_url'    => 'https://www.creative-addons.com/elementor-widgets/knowledge-base-search/',
			'plugin-type'       => 'elementor',
			'type'              => 'widget'
		);

		$features['2020.10.20'] = array(
			'plugin'            => __( 'Widget', 'echo-knowledge-base'),
			'title'             => __( 'KB Categories', 'echo-knowledge-base'),
			'description'       => '<p>' . __( 'Display your Knowledge base Categories in stunning layouts with our powerful Elementor widget.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.creative-addons.com/wp-content/uploads/2020/10/more-kb-widgets-features.jpg',
			'learn_more_url'    => 'https://www.creative-addons.com/elementor-widgets/knowledge-base-categories/',
			'plugin-type'       => 'elementor',
			'type'              => 'widget'
		);

		$features['2020.10.21'] = array(
			'plugin'            => __( 'Widget', 'echo-knowledge-base'),
			'title'             => __( 'KB Recent Articles', 'echo-knowledge-base'),
			'description'       => '<p>' . __( 'Display your Knowledge Base Articles in stunning layouts with our powerful Elementor widget.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.creative-addons.com/wp-content/uploads/2020/10/more-kb-widgets-features.jpg',
			'learn_more_url'    => 'https://www.creative-addons.com/elementor-widgets/knowledge-base-recent-articles/',
			'plugin-type'       => 'elementor',
			'type'              => 'widget'
		);

		$features['2020.10.22'] = array(
			'plugin'            => __( 'Widget', 'echo-knowledge-base'),
			'title'             => __( 'Knowledge Base Widget', 'echo-knowledge-base'),
			'description'       => '<p>' . __( 'Display your Knowledge base on any page.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.creative-addons.com/wp-content/uploads/2020/10/kb-main-page-features.jpg',
			'learn_more_url'    => 'https://www.creative-addons.com/elementor-widgets/knowledge-base/',
			'plugin-type'       => 'elementor',
			'type'              => 'widget'
		);

		return $features;
	}
}

