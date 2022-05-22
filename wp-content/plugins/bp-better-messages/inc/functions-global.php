<?php
defined( 'ABSPATH' ) || exit;

function bm_bp_is_current_component($component = ''){
    if( ! function_exists('bp_is_current_component') ){
        return false;
    } else {
        return bp_is_current_component( $component );
    }
}