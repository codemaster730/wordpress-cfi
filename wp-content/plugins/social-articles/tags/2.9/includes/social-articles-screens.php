<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

function social_articles_screen() {
	global $bp;
	do_action( 'social_articles_screen' );
	bp_core_load_template( apply_filters( 'social_articles_screen', 'members/single/articles' ) );
}

function my_articles_screen() {
	global $bp;
	do_action( 'my_articles_screen' );
	bp_core_load_template( apply_filters( 'my_articles_screen', 'members/single/articles' ) );
}

function new_article_screen() {
	global $bp;
	do_action( 'new_article_screen' );
	bp_core_load_template( apply_filters( 'new_article_screen', 'members/single/articles' ) );
}

function draft_articles_screen() {
    global $bp;
    do_action( 'draft_articles_screen' );
    bp_core_load_template( apply_filters( 'draft_articles_screen', 'members/single/articles' ) );
}

function pending_articles_screen() {
    global $bp;
    do_action( 'pending_articles_screen' );
    bp_core_load_template( apply_filters( 'pending_articles_screen', 'members/single/articles' ) );
}

add_action('bp_after_member_body', 'sa_add_main_template');
function sa_add_main_template(){
    global $bp;
    if( !bp_sa_is_bp_default()):
        ?>
        <?php do_action( 'template_notices' );?>
        <div class="social-articles-main" role="main">
            <div id="articles-dir-list" class="articles dir-list">
                <?php
                switch($bp->current_action){
                    case 'new':
                        social_articles_load_sub_template( 'members/single/articles/new' );
                        break;
                    case 'articles':
                        social_articles_load_sub_template( 'members/single/articles/loop' );;
                        break;
                    case 'draft':
                        social_articles_load_sub_template( 'members/single/articles/draft' );
                        break;
                    case 'under-review':
                        social_articles_load_sub_template( 'members/single/articles/pending' );
                        break;
                }
                ?>
            </div>
        </div>
        <?php
        do_action('sa_counters');
    endif;
}


if ( class_exists( 'BP_Theme_Compat' ) ) {

    class SA_Theme_Compat {

        public function __construct() {

            add_action( 'bp_setup_theme_compat', array( $this, 'is_bp_plugin' ) );
        }

        public function is_bp_plugin() {
            if ( bp_is_current_component( 'articles' ) ) {
                // first we reset the post
                add_action( 'bp_template_include_reset_dummy_post_data', array( $this, 'directory_dummy_post' ) );
                // then we filter ‘the_content’ thanks to bp_replace_the_content
                //add_filter( 'bp_replace_the_content', array( $this, 'directory_content' ) );
            }
        }

        public function directory_dummy_post() {

        }

        public function directory_content() {
            bp_buffer_template_part( 'members/single/home' );
            bp_buffer_template_part( 'members/single/articles' );
        }
    }

    new SA_Theme_Compat ();


    function bp_sa_add_template_stack( $templates ) {

        if ( bp_is_current_component( 'social_articles' ) && !bp_sa_is_bp_default() ) {
            $templates[] = SA_PLUGIN_DIR . '/includes/templates';
        }
        return $templates;
    }

    add_filter( 'bp_get_template_stack', 'bp_sa_add_template_stack', 10, 1 );
}