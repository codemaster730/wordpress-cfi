<?php
/**
 * Themosaurus_Merlin Class
 *
 * @author  Themosaurus
 * @since   1.0.0
 * @package themosaurus-merlin
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Themosaurus_Merlin' ) ) :
	/**
	 * The Themosaurus Merlin class
	 */
	abstract class Themosaurus_Merlin {

		public function __construct() {
			$theme = wp_get_theme();

			if ( is_admin() && class_exists( 'TGM_Plugin_Activation' ) && isset( $_GET['demo'] ) ) {
				add_filter( "{$theme->template}_tgm_plugin_activation_register_plugins",     array( $this, 'add_tgmpa_demo_plugins'           ) );
				add_action( 'import_start',                                                  array( $this, 'import_start'                     ) );
				add_action( 'init',                                                          array( $this, 'disable_plugin_actions_redirects' ) );
				add_filter( 'woocommerce_prevent_automatic_wizard_redirect',                 '__return_true'                                    );

				// Prevent infinite loop in bbPress
				remove_filter( 'the_title', 'bbp_get_reply_title_fallback', 2 );
			}

			if ( class_exists( 'LP_Admin_Assets' ) && isset( $_GET['page'] ) && $_GET['page'] === 'merlin' ) {
				remove_action( 'admin_footer', array( LP_Admin_Assets::instance(), 'show_overlay' ) );
			}

			add_filter( 'merlin_import_files',             array( $this, 'merlin_import_files'       ) );
			add_action( 'merlin_after_all_import',         array( $this, 'merlin_after_import_setup' ) );
			add_filter( "{$theme->template}_merlin_steps", array( $this, 'change_merlin_steps'       ) );

			// Fix Elementor import issues
			if ( method_exists( '\Elementor\Compatibility', 'on_wxr_importer_pre_process_post_meta' ) ) {
				remove_filter( 'wxr_importer.pre_process.post_meta', array( 'Elementor\Compatibility', 'on_wxr_importer_pre_process_post_meta' ) );
			}

			$this->init();
		}

		/**
		 * Dynamically add plugins to TGMPA depending on the selected demo during import
		 *
		 * @param $plugins
		 *
		 * @return array
		 */
		public function add_tgmpa_demo_plugins( $plugins ) {
			$demo_setups = array_values( $this->get_demo_setups_args() ); // Turn into non-associative to make sure we have the same indexes as Merlin's import_files property
			if ( ! empty( $demo_setups[ $_GET['demo'] ] ) ) {
				if ( ! empty( $demo_setups[ $_GET['demo'] ]['tgmpa'] ) ) {
					$plugins = array_merge( $plugins, $demo_setups[ $_GET['demo'] ]['tgmpa'] );
				}

				if ( ! empty( $_GET['use-elementor'] ) ) {
					$plugins[] = array(
						'name'     => 'Elementor',
						'slug'     => 'elementor',
						'required' => true,
					);
					$plugins[] = array(
						'name'         => 'Grimlock for Elementor',
						'slug'         => 'grimlock-elementor',
						'source'       => 'http://files.themosaurus.com/grimlock-elementor/grimlock-elementor.zip',
						'required'     => false,
						'external_url' => 'https://www.themosaurus.com/',
					);
				}
			}

			return $plugins;
		}

		/**
		 * Apply some tweaks before import starts
		 */
		public function import_start() {
			add_filter( 'http_request_args', array( $this, 'disable_ssl_verification' ), 1000 );

			// Call before import method
			$demos                = array_values( $this->get_demo_setups_args() );
			$before_import_method = $demos[ $_GET['demo'] ]['before_import_method'];
			if ( ! empty( $before_import_method ) && method_exists( $this, $before_import_method ) ) {
				call_user_func( array( $this, $before_import_method ) );
			}

			// Update the permalink structure
			update_option( 'permalink_structure', '/%postname%/' );
			flush_rewrite_rules();
		}

		/**
		 * Disable SSL verification in WP_Http request when import starts (to avoid Let's Encrypt certificate expired error on assets import)
		 *
		 * @param array $args WP_Http request args
		 *
		 * @return array
		 */
		public function disable_ssl_verification( $args ) {
			$args['sslverify'] = false;
			return $args;
		}

		/**
		 * Prevent some plugins from triggering a redirection when activated (since it breaks Merlin's activation process)
		 */
		public function disable_plugin_actions_redirects() {
			delete_transient( 'elementor_activation_redirect' );
			delete_transient( '_epkb_plugin_installed' );
			delete_option( 'ecs_force_user_redirect' );
		}

		/**
		 * Setup demo import files list for Merlin
		 *
		 * @param $args
		 *
		 * @return array
		 */
		public function merlin_import_files( $args ) {
			return array_merge( $args, $this->get_demo_setups_args() );
		}

		/**
		 * Process adjustments after demo import
		 */
		public function merlin_after_import_setup() {
			// Process demo specific adjustments
			if ( isset( $_GET['demo'] ) ) {
				if ( ! empty( $_GET['use-elementor'] ) ) {
					$this->setup_elementor();
				}

				// Call after import method
				$demos               = array_values( $this->get_demo_setups_args() );
				$after_import_method = $demos[ $_GET['demo'] ]['after_import_method'];
				if ( ! empty( $after_import_method ) && method_exists( $this, $after_import_method ) ) {
					call_user_func( array( $this, $after_import_method ) );
				}
			}

			flush_rewrite_rules();
		}

		/**
		 * Remove the "child theme" step from Merlin
		 *
		 * @param $steps
		 *
		 * @return mixed
		 */
		public function change_merlin_steps( $steps ) {
			unset( $steps['child'] );
			return $steps;
		}

		/**
		 * Get the demo setups arguments that will be sent to Merlin WP
		 *
		 * @return array
		 */
		protected abstract function get_demo_setups_args();

		/**
		 * Update the front and/or blog page
		 *
		 * @param null|string $front_page_title The title of the front page
		 * @param null|string $blog_page_title The title of the blog page
		 */
		protected function update_front_and_blog_page_by_title( $front_page_title = null, $blog_page_title = null ) {
			$front_page = get_page_by_title( $front_page_title );
			$blog_page  = get_page_by_title( $blog_page_title );

			update_option( 'show_on_front', 'page' );
			if ( ! empty( $front_page ) ) {
				update_option( 'page_on_front', $front_page->ID );
			}
			if ( ! empty( $blog_page ) ) {
				update_option( 'page_for_posts', $blog_page->ID );
			}
		}

		/**
		 * Remove some pages that usually appear as duplicates after the XML import
		 *
		 * @param array $duplicate_page_slugs (Optional) Array of additional slugs of pages to remove
		 */
		protected function delete_duplicate_pages( $duplicate_page_slugs = array() ) {
			$duplicate_page_slugs = array_merge( array(
				'duplicate_members_page'  => 'members-2',
				'duplicate_activity_page' => 'activity-2',
				'duplicate_activate_page' => 'activate-2',
				'duplicate_groups_page'   => 'groups-2',
				'duplicate_register_page' => 'register-2',
				'duplicate_sample_page'   => 'sample-2',
			), $duplicate_page_slugs );

			foreach ( $duplicate_page_slugs as $duplicate_page_slug ) {
				$duplicate_page = get_page_by_path( $duplicate_page_slug );

				if ( ! empty( $duplicate_page ) ) {
					wp_delete_post( $duplicate_page->ID );
				}
			}
		}

		/**
		 * Change the default category and optionally delete the "Uncategorized" category
		 */
		protected function change_default_category( $category_slug, $delete_uncategorized = true ) {
			$category = get_category_by_slug( $category_slug );
			if ( ! empty( $category ) ) {
				update_option( 'default_category', $category->term_id );
			}

			if ( $delete_uncategorized ) {
				$uncategorized_cat = get_category_by_slug( 'uncategorized' );
				if ( ! empty( $uncategorized_cat ) ) {
					wp_delete_category( $uncategorized_cat->term_id );
				}
			}
		}

		/**
		 * Assign menus to menu locations
		 *
		 * @param array $menus Array containing menu locations as keys and menu ids as values.
		 * E.g.: array( 'primary' => 3, 'secondary' => 5 )
		 */
		protected function assign_menus_to_locations( $menus ) {
			// Transform menu names into menu ids
			$menus = array_map( function( $menu_name ) {
				$menu = get_term_by( 'name', $menu_name, 'nav_menu' );

				if ( empty( $menu ) ) {
					return false;
				}

				return $menu->term_id;
			}, $menus );

			// Filter out menus that don't exist
			$menus = array_filter( $menus, 'boolval' );

			// Assign menus to their locations.
			set_theme_mod( 'nav_menu_locations', $menus );
		}

		/**
		 * Fix hierarchy of menu items in a given menu
		 *
		 * @param string $menu_name Name of the menu to fix
		 * @param array $menu_hierarchy Array containing the menu hierarchy. Keys are the parent item title, values are an array of children items titles.
		 * E.g.: array( 'Events' => array( 'Calendar', 'Venues', 'Organizers' ) )
		 */
		protected function fix_menu_items_hierarchy( $menu_name, $menu_hierarchy ) {
			$menu = get_term_by( 'name', $menu_name, 'nav_menu' );

			// Bail if menu doesn't exist
			if ( empty( $menu ) ) {
				return;
			}

			$menu_items = wp_get_nav_menu_items( $menu );

			foreach ( $menu_hierarchy as $parent_menu_item_title => $child_menu_items ) {
				if ( ! is_array( $child_menu_items ) ) {
					continue;
				}

				$parent_menu_item = false;

				// Find parent menu item by title
				foreach ( $menu_items as $menu_item ) {
					if ( trim( strip_tags( $menu_item->title ) ) === $parent_menu_item_title ) {
						$parent_menu_item = $menu_item;
						break;
					}
				}

				foreach ( $child_menu_items as $child_menu_item_key => $child_menu_item_value ) {
					if ( is_array( $child_menu_item_value ) ) {
						$this->fix_menu_items_hierarchy( $menu_name, $child_menu_items );
						$child_menu_item_title = $child_menu_item_key;
					}
					else {
						$child_menu_item_title = $child_menu_item_value;
					}

					$child_menu_item  = false;

					// Find child menu item by title
					foreach ( $menu_items as $menu_item ) {
						if ( trim( strip_tags( $menu_item->title ) ) === $child_menu_item_title ) {
							$child_menu_item = $menu_item;
							break;
						}
					}

					// Fix parent relationship
					if ( ! empty( $parent_menu_item ) && ! empty( $child_menu_item ) ) {
						$child_menu_item->menu_item_parent = $parent_menu_item->db_id;
						$this->update_menu_item( $menu->term_id, $child_menu_item );
					}
				}
			}
		}

		/**
		 * Fix menu items titles in a given menu
		 *
		 * @param string $menu_name Name of the menu to fix
		 * @param array $menu_items_to_fix Array containing the menu items titles to fix. Keys are the imported item title (or the menu item property set for the $match_property parameter), values are the new (fixed) item title.
		 * E.g.: array( 'Members' => '<span class="text-primary">Members</span>' )
		 * @param string $match_property The menu item property to match against when looking for the menu item to change. Matches against menu item title by default.
		 */
		protected function fix_menu_items_titles( $menu_name, $menu_items_to_fix, $match_property = 'title' ) {
			$menu = get_term_by( 'name', $menu_name, 'nav_menu' );

			// Bail if menu doesn't exist
			if ( empty( $menu ) ) {
				return;
			}

			$menu_items = wp_get_nav_menu_items( $menu );

			foreach ( $menu_items_to_fix as $menu_item_match_property => $fixed_title ) {
				foreach ( $menu_items as $menu_item ) {
					if ( trim( $menu_item->$match_property ) === $menu_item_match_property ) {
						$menu_item->title = $fixed_title;
						$this->update_menu_item( $menu->term_id, $menu_item );
						break;
					}
				}
			}
		}

		/**
		 * @param string $menu_name Name of the menu to fix
		 * @param array $menu_items_to_delete By default, a list of menu item titles to delete, or any other menu item property to match against if the $match_property parameter is set
		 * @param string $match_property The menu item property to match against when looking for the menu item to delete. Matches against menu item title by default.
		 */
		protected function delete_menu_items( $menu_name, $menu_items_to_delete, $match_property = 'title' ) {
			$menu = get_term_by( 'name', $menu_name, 'nav_menu' );

			// Bail if menu doesn't exist
			if ( empty( $menu ) ) {
				return;
			}

			$menu_items = wp_get_nav_menu_items( $menu );

			foreach ( $menu_items_to_delete as $menu_item_match_property ) {
				foreach ( $menu_items as $menu_item ) {
					if ( trim( $menu_item->$match_property ) === $menu_item_match_property ) {
						wp_delete_post( $menu_item->db_id );
						break;
					}
				}
			}
		}

		/**
		 * Convert relative urls in menu items into absolute urls
		 */
		protected function convert_menu_items_relative_urls_to_absolute_urls() {
			$menus = get_terms( array( 'taxonomy' => 'nav_menu', 'hide_empty' => false ) );

			// Bail if there are no menus
			if ( ! is_array( $menus ) ) {
				return;
			}

			foreach ( $menus as $menu ) {
				$menu_items = wp_get_nav_menu_items( $menu );

				foreach ( $menu_items as $menu_item ) {
					if ( class_exists( 'BuddyPress' ) ) {
						$directory_pages = bp_get_option( 'bp-pages' );

						// Replace the relative "register" url by the BP register page url
						if ( strpos( $menu_item->url, '/register' ) ) {
							$menu_item->url = esc_url( get_permalink( $directory_pages['register'] ) );
							$this->update_menu_item( $menu->term_id, $menu_item );
						}
					}

					// Replace relative urls by their absolute counterpart
					if ( $menu_item->url[0] === '/' ) {
						$menu_item->url = home_url( $menu_item->url );
						$this->update_menu_item( $menu->term_id, $menu_item );
					}
				}
			}
		}

		/**
		 * Update a menu item
		 *
		 * @param int $menu_id Menu id
		 * @param WP_Post $menu_item Menu item
		 *
		 * @return int|WP_Error Menu item db_id if update succeeded, WP_Error if failed
		 */
		protected function update_menu_item( $menu_id, $menu_item ) {
			return wp_update_nav_menu_item( $menu_id, $menu_item->db_id, array(
				'menu-item-db-id'       => $menu_item->db_id,
				'menu-item-object-id'   => $menu_item->object_id,
				'menu-item-object'      => $menu_item->object,
				'menu-item-url'         => $menu_item->url,
				'menu-item-parent-id'   => $menu_item->menu_item_parent,
				'menu-item-position'    => $menu_item->menu_order,
				'menu-item-type'        => $menu_item->type,
				'menu-item-title'       => $menu_item->title,
				'menu-item-description' => $menu_item->description,
				'menu-item-attr-title'  => false,
				'menu-item-target'      => $menu_item->target,
				'menu-item-classes'     => is_array( $menu_item->classes ) ? implode( ' ', $menu_item->classes ) : $menu_item->classes,
				'menu-item-xfn'         => $menu_item->xfn,
				'menu-item-status'      => $menu_item->post_status,
			) );
		}

		/**
		 * Generate a new xprofile field if it doesn't already exists
		 *
		 * @param array $args Array of field args. Can contain the following :
		 * array(
		 *      'name' => The field name
		 *      'type' => The field type
		 *      'field_group' => The field group (tab)
		 *      'required' => Whether the field is required
		 *      'choices' => Choices for selectbox fields
		 *      'description' => The field description
		 * )
		 *
		 * @return bool|int Field id on success, false on failure
		 */
		protected function generate_xprofile_field( $args ) {
			$args = wp_parse_args( $args, array(
				'name'        => '',
				'type'        => 'textbox',
				'field_group' => 1,
				'required'    => false,
				'choices'     => array(),
				'description' => '',
			) );

			if ( function_exists( 'xprofile_get_field_id_from_name' ) && ! xprofile_get_field_id_from_name( $args['name'] ) ) {
				$profile_field_id = xprofile_insert_field( array(
					'field_group_id' => $args['field_group'],
					'name'           => $args['name'],
					'description'    => $args['description'],
					'is_required'    => $args['required'],
					'type'           => $args['type'],
					'can_delete'     => 1,
				) );

				if ( ( 'selectbox' === $args['type'] || 'checkbox' === $args['type'] ) && $profile_field_id ) {
					foreach ( $args['choices'] as $i => $choice ) {
						xprofile_insert_field( array(
							'field_group_id' => $args['field_group'],
							'parent_id'      => $profile_field_id,
							'type'           => $args['type'],
							'name'           => $choice,
							'option_order'   => $i + 1,
						) );
					}
				}

				return $profile_field_id;
			}

			return false;
		}

		/**
		 * Generate a new xprofile field group
		 *
		 * @param array $args Array of field group args. Can contain the following :
		 * array(
		 *      'id'          => ID of the field group
		 *      'name'        => Name of the field group
		 *      'description' => Description of the field group
		 *      'can_delete'  => Whether the field group can be deleted manually or not
		 * )
		 */
		protected function generate_xprofile_field_group( $args ) {
			if ( function_exists( 'xprofile_insert_field_group' ) ) {
				$args = wp_parse_args( $args, array(
					'id'          => 2,
					'name'        => 'Details',
					'description' => '',
					'can_delete'  => true,
				) );

				xprofile_insert_field_group( array(
					'field_group_id' => $args['id'],
					'name'           => $args['name'],
					'description'    => $args['description'],
					'can_delete'     => $args['can_delete'],
				) );
			}
		}

		/**
		 * Fix BP Profile Search form after import
		 *
		 * @param string $name The form name
		 * @param array $fields The form field names in the same order as the original exported form
		 * @param string $results_page_title The results page title
		 */
		protected function fix_bps_form( $name, $fields, $results_page_title ) {
			$form         = get_page_by_title( $name, 'OBJECT', 'bps_form' );
			$results_page = get_page_by_title( $results_page_title );

			if ( ! empty( $form ) ) {
				// Get the BPS form's options
				$bps_options = get_post_meta( $form->ID, 'bps_options', true );

				// Format fields array as BPS expects it
				$fields = array_map( function( $field ) {
					if ( $field === 'any' ) {
						return 'field_any';
					}

					$field_id = xprofile_get_field_id_from_name( $field );

					if ( empty( $field_id ) ) {
						return false;
					}

					return 'field_' . $field_id;
				}, $fields );

				// Filter out false values
				$fields = array_filter( $fields, 'boolval' );

				// Replace the fields in the BPS form's options
				$bps_options['field_code'] = $fields;

				// Replace the results page id in the BPS form's options
				$bps_options['action'] = strval( $results_page->ID );

				// Update the BPS form's options
				update_post_meta( $form->ID, 'bps_options', $bps_options );
			}
		}

		/**
		 * Assign Paid Memberships Pro pages in settings. Without the $pages parameter, will use the pages generated by default by PMPro
		 *
		 * @param array $pages (Optional) Array of PMPro pages to assign. Keys are the PMPro page option, values are the page title to assign to that option.
		 * E.g.: array( 'account' => 'My Account', 'billing' => 'Billing' )
		 */
		protected function assign_pmpro_pages( $pages = array() ) {
			if ( function_exists( 'pmpro_init' ) ) {
				$pages = wp_parse_args( $pages, array(
					'account'            => 'Membership Account',
					'billing'            => 'Membership Billing',
					'cancel'             => 'Membership Cancel',
					'checkout'           => 'Membership Checkout',
					'confirmation'       => 'Membership Confirmation',
					'invoice'            => 'Membership Invoice',
					'levels'             => 'Membership Levels',
					'pmprobp_restricted' => 'Access Restricted',
				) );

				foreach ( $pages as $page_type => $page_title ) {
					$page = get_page_by_title( $page_title );

					if ( ! empty( $page ) ) {
						pmpro_setOption( "{$page_type}_page_id", $page->ID, 'intval' );
					}
				}
			}
		}

		/**
		 * Assign WooCommerce pages in settings. Without the $pages parameter, will use the pages generated by default by WooCommerce
		 *
		 * @param array $pages (Optional) Array of WC pages to assign. Keys are the WC page option, values are the page title to assign to that option.
		 * E.g.: array( 'shop' => 'Shop', 'cart' => 'Cart' )
		 */
		protected function assign_wc_pages( $pages = array() ) {
			if ( function_exists( 'WC' ) ) {
				$pages = wp_parse_args( $pages, array(
					'shop'      => 'Shop',
					'cart'      => 'Cart',
					'checkout'  => 'Checkout',
					'myaccount' => 'My account',
				) );

				foreach ( $pages as $page_type => $page_title ) {
					$page = get_page_by_title( $page_title );

					if ( ! empty( $page ) ) {
						update_option( "woocommerce_{$page_type}_page_id", $page->ID );
					}
				}
			}
		}

		/**
		 * Enable user registrations
		 */
		protected function enable_user_registrations() {
			update_option( 'users_can_register', 1 );
		}

		/**
		 * Update BuddyPress theme package setting to use the "legacy" templates
		 */
		protected function enable_bp_legacy() {
			if ( class_exists( 'BuddyPress' ) ) {
				bp_update_option( '_bp_theme_package_id', 'legacy' );
			}
		}

		/**
		 * Enable Yoast SEO Breadcrumbs and adjust breadcrumbs settings
		 *
		 * @param array $settings Array of yoast settings. Leave empty to activate breadcrumbs and change separators to a bullet.
		 * Can contain the following :
		 * array(
		 *      'enable_breadcrumbs'    => Whether to enable the breadcrumbs or not
		 *      'breadcrumbs_separator' => Separator to use in breadcrumbs
		 *      'titles_separator_icon' => Name of the icon to use as a separator for page titles (in browser tab)
		 * )
		 */
		protected function adjust_yoast_settings( $settings = array() ) {
			if ( function_exists( 'yoast_breadcrumb' ) ) {
				$settings = wp_parse_args( $settings, array(
					'enable_breadcrumbs'    => true,
					'breadcrumbs_separator' => '•',
					'titles_separator_icon' => 'sc-bull',
				) );

				$yoast_titles_options                       = get_option( 'wpseo_titles', array() );
				$yoast_titles_options['breadcrumbs-enable'] = ! empty( $settings['enable_breadcrumbs'] ) ? 'on' : 'off';
				$yoast_titles_options['breadcrumbs-sep']    = $settings['breadcrumbs_separator'];
				$yoast_titles_options['separator']          = $settings['titles_separator_icon'];

				update_option( 'wpseo_titles', $yoast_titles_options );
			}
		}

		/**
		 * Activate a selection of JetPack modules
		 *
		 * @param array $modules The list of modules to activate
		 */
		protected function activate_jetpack_modules( $modules = array() ) {
			if ( class_exists( 'Jetpack' ) ) {
				foreach ( $modules as $module ) {
					Jetpack::activate_module( $module, false, false );
				}
			}
		}

		/**
		 * Update a selection of JetPack options
		 *
		 * @param array $options Array of options to update. Must be in format: array( 'option_name' => 'option_value' )
		 */
		protected function update_jetpack_options( $options ) {
			if ( class_exists( 'Jetpack_Options' ) ) {
				Jetpack_Options::update_options( $options );
			}
		}

		/**
		 * Update a selection of Paid Memberships Pro options
		 *
		 * @param array $options Array of options to update. Must be in format: array( 'option_name' => 'option_value' )
		 */
		protected function update_pmpro_options( $options ) {
			if ( function_exists( 'pmpro_setOption' ) ) {
				foreach ( $options as $option_name => $option ) {
					pmpro_setOption( $option_name, $option );
				}
			}
		}

		/**
		 * Update a selection of The Events Calendar options
		 *
		 * @param array $options Array of options to update. Must be in format: array( 'option_name' => 'option_value' )
		 */
		protected function update_tec_options( $options ) {
			foreach ( $options as $option => $value ) {
				tribe_update_option( $option, $value );
			}
		}

		/**
		 * Disable a selection of Getwid blocks
		 *
		 * @param array $blocks
		 */
		protected function disable_getwid_blocks( $blocks ) {
			if ( function_exists( 'getwid' ) ) {
				foreach ( $blocks as $block ) {
					update_option( "getwid/{$block}::disabled", true );
				}
			}
		}

		/**
		 * Set the site's custom logo using the given attachment
		 *
		 * @param string $attachment_name Name of the attachment to use as custom logo
		 */
		protected function set_custom_logo( $attachment_name ) {
			$custom_logo = get_page_by_title( $attachment_name, OBJECT, 'attachment' );
			if ( ! empty( $custom_logo ) ) {
				set_theme_mod( 'custom_logo', $custom_logo->ID );
			}
		}

		/**
		 * Update category thumbnails
		 *
		 * @param array $category_thumbnails Array of category thumbnails keyed by category slug. E.g. : array( 'featured' => 'featured-star-thumb' )
		 */
		protected function assign_category_thumbnails( $category_thumbnails ) {
			$categories = get_terms( array( 'taxonomy' => 'category', 'hide_empty' => false ) );

			foreach ( $categories as $category ) {
				if ( ! empty( $category_thumbnails[ $category->slug ] ) ) {
					$thumbnail = get_page_by_title( $category_thumbnails[ $category->slug ], OBJECT, 'attachment' );

					if ( ! empty( $thumbnail ) ) {
						update_term_meta( $category->term_id, '_thumbnail_id', $thumbnail->ID );
					}
				}
			}
		}

		/**
		 * Replace pages with their Elementor template counterpart and adjust Elementor settings
		 */
		public function setup_elementor() {
			if ( ! class_exists( 'Elementor\Plugin' ) ) {
				return;
			}

			update_option( 'elementor_disable_color_schemes', 'yes' );
			update_option( 'elementor_disable_typography_schemes', 'yes' );
			update_option( 'elementor_container_width', '1400' );
			update_option( 'elementor_space_between_widgets', '0' );
			update_option( 'elementor_viewport_lg', '992' );
			update_option( 'elementor_viewport_md', '768' );
			update_option( 'elementor_page_title_selector', '#custom_header' );
			update_option( 'elementor_load_fa4_shim', 'yes' );

			$elementor = Elementor\Plugin::instance();

			$pages = get_posts( array(
				'post_type'      => 'page',
				'posts_per_page' => -1,
			) );

			foreach ( $pages as $page ) {
				$elementor_template = get_page_by_title( $page->post_title, 'OBJECT', 'elementor_library' );

				if ( ! empty( $elementor_template ) ) {
					$template_data = $elementor->templates_manager->get_template_data( array(
						'display'       => true,
						'edit_mode'     => true,
						'page_settings' => true,
						'source'        => "local",
						'template_id'   => $elementor_template->ID,
					) );

					$elementor_page_template = 'elementor_header_footer';

					if ( ! empty( $template_data['page_settings'] ) ) {
						update_post_meta( $page->ID, '_elementor_page_settings', $template_data['page_settings'] );

						if ( ! empty( $template_data['page_settings']['template'] ) ) {
							$elementor_page_template = $template_data['page_settings']['template'];
						}
					}

					update_post_meta( $page->ID, '_wp_page_template', $elementor_page_template );
					update_post_meta( $page->ID, '_elementor_edit_mode', 'builder' );

					if ( ! empty( $template_data['content'] ) ) {
						update_post_meta( $page->ID, '_elementor_data', $template_data['content'] );
					}
				}
			}
		}

		/**
		 * Enable/disable and setup a selection of BuddyPress components
		 *
		 * @param array $components Array of components to enable and/or disable. E.g. : array( 'friends' => true, 'groups' => false )
		 */
		protected function setup_bp_components( $components ) {
			if ( ! function_exists( 'buddypress' ) ) {
				return;
			}

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			require_once( buddypress()->plugin_dir . '/bp-core/admin/bp-core-admin-schema.php' );

			$bp_active_components = bp_get_option( 'bp-active-components', array() );
			foreach ( $components as $component_name => $enabled ) {
				$bp_active_components[ $component_name ] = ! empty( $enabled ) ? 1 : 0;
			}

			// Generate necessary pages for newly activated components
			bp_core_install( $bp_active_components );
			bp_update_option( 'bp-active-components', $bp_active_components );
			bp_core_add_page_mappings( $bp_active_components );

			// Remove all BP emails to prevent ID jumps that could potentially mess up the demo import
			$bp_emails = get_posts( array( 'post_type' => bp_get_email_post_type(), 'posts_per_page' => -1, 'fields' => 'ids' ) );
			foreach ( $bp_emails as $bp_email ) {
				wp_delete_post( $bp_email, true );
			}

			// Regenerate emails after import
			add_action( 'merlin_after_all_import', 'bp_core_install_emails' );
		}

		/**
		 * Set directory locations, text strings, and settings for Merlin
		 */
		public function init() {
			new Merlin(
				$config = apply_filters( 'themosaurus_merlin_config', array(
					'directory'            => 'libs/merlin', // Location / directory where Merlin WP is placed in your theme.
					'merlin_url'           => 'merlin', // The wp-admin page slug where Merlin WP loads.
					'parent_slug'          => 'themes.php', // The wp-admin parent page slug for the admin menu item.
					'capability'           => 'manage_options', // The capability required for this menu to be displayed to the user.
					'child_action_btn_url' => 'https://codex.wordpress.org/child_themes', // URL for the 'child-action-link'.
					'dev_mode'             => true, // Enable development mode for testing.
					'license_step'         => false, // EDD license activation step.
					'license_required'     => false, // Require the license activation step.
					'license_help_url'     => '', // URL for the 'license-tooltip'.
					'edd_remote_api_url'   => '', // EDD_Theme_Updater_Admin remote_api_url.
					'edd_item_name'        => '', // EDD_Theme_Updater_Admin item_name.
					'edd_theme_slug'       => '', // EDD_Theme_Updater_Admin item_slug.
					'ready_big_button_url' => home_url(), // Link for the big button on the ready step.
				) ),
				$strings = apply_filters( 'themosaurus_merlin_strings', array(
					'admin-menu'               => esc_html__( 'Theme Setup', 'themosaurus-merlin' ),
					/* translators: 1: Title Tag 2: Theme Name 3: Closing Title Tag */
					'title%s%s%s%s'            => esc_html__( '%1$s%2$s Themes &lsaquo; Theme Setup: %3$s%4$s', 'themosaurus-merlin' ),
					'return-to-dashboard'      => esc_html__( 'Return to the dashboard', 'themosaurus-merlin' ),
					'ignore'                   => esc_html__( 'Disable this wizard', 'themosaurus-merlin' ),
					'btn-skip'                 => esc_html__( 'Skip', 'themosaurus-merlin' ),
					'btn-next'                 => esc_html__( 'Next', 'themosaurus-merlin' ),
					'btn-start'                => esc_html__( 'Start', 'themosaurus-merlin' ),
					'btn-no'                   => esc_html__( 'Cancel', 'themosaurus-merlin' ),
					'btn-plugins-install'      => esc_html__( 'Install', 'themosaurus-merlin' ),
					'btn-child-install'        => esc_html__( 'Install', 'themosaurus-merlin' ),
					'btn-content-install'      => esc_html__( 'Install', 'themosaurus-merlin' ),
					'btn-import'               => esc_html__( 'Import', 'themosaurus-merlin' ),
					'btn-license-activate'     => esc_html__( 'Activate', 'themosaurus-merlin' ),
					'btn-license-skip'         => esc_html__( 'Later', 'themosaurus-merlin' ),
					/* translators: Theme Name */
					'license-header%s'         => esc_html__( 'Activate %s', 'themosaurus-merlin' ),
					/* translators: Theme Name */
					'license-header-success%s' => esc_html__( '%s is Activated', 'themosaurus-merlin' ),
					/* translators: Theme Name */
					'license%s'                => esc_html__( 'Enter your license key to enable remote updates and theme support.', 'themosaurus-merlin' ),
					'license-label'            => esc_html__( 'License key', 'themosaurus-merlin' ),
					'license-success%s'        => esc_html__( 'The theme is already registered, so you can go to the next step!', 'themosaurus-merlin' ),
					'license-json-success%s'   => esc_html__( 'Your theme is activated! Remote updates and theme support are enabled.', 'themosaurus-merlin' ),
					'license-tooltip'          => esc_html__( 'Need help?', 'themosaurus-merlin' ),
					/* translators: Theme Name */
					'welcome-header%s'         => esc_html__( 'Welcome to %s', 'themosaurus-merlin' ),
					'welcome-header-success%s' => esc_html__( 'Hi. Welcome back', 'themosaurus-merlin' ),
					'welcome%s'                => esc_html__( 'This wizard will set up your theme, install plugins, and import content. It is optional & should take only a few minutes.', 'themosaurus-merlin' ),
					'welcome-success%s'        => esc_html__( 'You may have already run this theme setup wizard. If you would like to proceed anyway, click on the "Start" button below.', 'themosaurus-merlin' ),
					'child-header'             => esc_html__( 'Install Child Theme', 'themosaurus-merlin' ),
					'child-header-success'     => esc_html__( 'You\'re good to go!', 'themosaurus-merlin' ),
					'child'                    => esc_html__( 'Let\'s build & activate a child theme so you may easily make theme changes.', 'themosaurus-merlin' ),
					'child-success%s'          => esc_html__( 'Your child theme has already been installed and is now activated, if it wasn\'t already.', 'themosaurus-merlin' ),
					'child-action-link'        => esc_html__( 'Learn about child themes', 'themosaurus-merlin' ),
					'child-json-success%s'     => esc_html__( 'Awesome. Your child theme has already been installed and is now activated.', 'themosaurus-merlin' ),
					'child-json-already%s'     => esc_html__( 'Awesome. Your child theme has been created and is now activated.', 'themosaurus-merlin' ),
					'demo-header'              => esc_html__( 'Select Your Demo', 'themosaurus-merlin' ),
					'demo'                     => esc_html__( 'You can import some demo content to help you get started with the theme.', 'themosaurus-merlin' ),
					'plugins-header'           => esc_html__( 'Install Plugins', 'themosaurus-merlin' ),
					'plugins-header-success'   => esc_html__( 'You\'re up to speed!', 'themosaurus-merlin' ),
					'plugins'                  => esc_html__( 'Let\'s install some essential WordPress plugins to get your site up to speed.', 'themosaurus-merlin' ),
					'plugins-success%s'        => esc_html__( 'The required WordPress plugins are all installed and up to date. Press "Next" to continue the setup wizard.', 'themosaurus-merlin' ),
					'plugins-action-link'      => esc_html__( 'Advanced', 'themosaurus-merlin' ),
					'import-header'            => esc_html__( 'Import Content', 'themosaurus-merlin' ),
					'import'                   => esc_html__( 'Let\'s import content to your website, to help you get familiar with the theme.', 'themosaurus-merlin' ),
					'import-action-link'       => esc_html__( 'Advanced', 'themosaurus-merlin' ),
					'ready-header'             => esc_html__( 'All done. Have fun!', 'themosaurus-merlin' ),
					/* translators: Theme Author */
					'ready%s'                  => esc_html__( 'Your theme has been all set up. Enjoy your new theme by %s.', 'themosaurus-merlin' ),
					'ready-action-link'        => esc_html__( 'More links', 'themosaurus-merlin' ),
					'ready-big-button'         => esc_html__( 'View your website', 'themosaurus-merlin' ),
					'ready-link-1'             => sprintf( '<a href="%1$s" target="_blank">%2$s</a>', 'https://doc.themosaurus.com/themosaurus-merlin', esc_html__( 'Documentation', 'themosaurus-merlin' ) ),
					'ready-link-2'             => sprintf( '<a href="%1$s" target="_blank">%2$s</a>', 'https://support.themosaurus.com/', esc_html__( 'Theme Support', 'themosaurus-merlin' ) ),
					'ready-link-3'             => sprintf( '<a href="%1$s">%2$s</a>', admin_url( 'customize.php' ), esc_html__( 'Start Customizing', 'themosaurus-merlin' ) ),
				) )
			);
		}
	}
endif;