<?php
/**
 * Cera Menu Image Class
 *
 * @package  cera
 * @author   Themosaurus
 * @since    1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Cera_Menu_Image' ) ) :
	/**
	 * The Cera Menu Image integration class
	 */
	class Cera_Menu_Image {
		/**
		 * Setup class.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			add_filter( 'menu_image_default_sizes', array( $this, 'remove_default_sizes' ), 10, 1 );
		}

		/**
		 * Remove the default image sizes.
		 *
		 * @since 1.0.0
		 *
		 * @param  array $sizes The sizes for the menu images.
		 *
		 * @return array $sizes The updated sizes for the menu images.
		 */
		public function remove_default_sizes( $sizes ) {
			unset( $sizes['menu-36x36'] );
			unset( $sizes['menu-48x48'] );
			unset( $sizes['menu-24x24'] );
			return $sizes;
		}
	}
endif;

return new Cera_Menu_Image();
