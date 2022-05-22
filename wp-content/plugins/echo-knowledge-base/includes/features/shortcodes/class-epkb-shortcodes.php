<?php

/**
 * Setup shortcodes
 *
 * @copyright   Copyright (c) 2018, Echo Plugins
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_Shortcodes {

	public function __construct() {
        new EPKB_Articles_Index_Shortcode();
    }
}
