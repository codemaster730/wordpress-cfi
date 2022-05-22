
<?php
$rows = array();

$rows[] = array(
    'id'      => 'published_post_counter',
    'label'   => __('Show published articles counter','social-articles'),
    'content' => $socialArticles->select( 'published_post_counter', array(
        'false' => __('False', 'social-articles'),
        'true'  => __('True', 'social-articles'),
    ), false
    ),
);

$rows[]       = array(
    'id'      => 'post_per_page',
    'label'   => __('Post per page#','social-articles'),
    'content' => $socialArticles->textinput( 'post_per_page' ),
);

$rows[]       = array(
    'id'      => 'excerpt_length',
    'label'   => __('Excerpt length#','social-articles'),
    'content' => $socialArticles->textinput( 'excerpt_length' ),
);

$save_button = '<div class="submitbutton"><input type="submit" class="button-primary" name="submit" value="'.__('Update Social Articles Settings','social-articles'). '" /></div><br class="clear"/>';
$socialArticles->postbox( 'social_articles_view_options', __( 'View', 'social-articles' ), $socialArticles->form_table( $rows ) . $save_button);
?>
