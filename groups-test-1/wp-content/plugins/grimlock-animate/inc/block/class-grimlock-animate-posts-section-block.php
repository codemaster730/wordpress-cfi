<?php
/**
 * Grimlock_Animate_Posts_Section_Block Class
 *
 * @author  Themosaurus
 * @package  grimlock-animate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The class that extends the Posts Section bloc to add animation options
 */
class Grimlock_Animate_Posts_Section_Block extends Grimlock_Animate_Query_Section_Block {
	/**
	 * Grimlock_Animate_Posts_Section_Block constructor.
	 *
	 * @param string $id_base ID of the extended block
	 */
	public function __construct( $id_base = 'grimlock_posts_section_block' ) {
		parent::__construct( $id_base );
	}
}

return new Grimlock_Animate_Posts_Section_Block();
