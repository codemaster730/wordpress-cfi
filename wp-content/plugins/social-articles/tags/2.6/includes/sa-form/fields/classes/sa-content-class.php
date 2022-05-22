<?php

class SA_Field_Content extends SA_Field{


    function __construct($slug, $name)
    {
        $config = array(
            "field_name"=>__CLASS__,
            "field_config"=>array(
                'label'=>__('Article content', 'social-articles'),
                'size'=>'sa-fullwidth',
                'resizable'=>false,
                'advance'=>false,
            )

        );
        parent::__construct($slug, $name, $config);
    }

    protected function get_backend()
    {
        return SA_BASE_PATH.'/includes/sa-form/fields/templates/content/backend-part.php';
    }

    protected function get_frontend()
    {
        return SA_BASE_PATH.'/includes/sa-form/fields/templates/content/frontend-part.php';
    }

    public function get_data($article_id, $form_values)
    {
        if (empty($form_values[$this->slug])) {
            if ($article_id > 0) {
                return get_post($article_id)->post_content;
            }else{
                return '';
            }
        }else{
            return $form_values[$this->slug];
        }
    }

    public function save_data($article_id, $form_values)
    {
        $article = get_post($article_id);
        $article->post_content = stripslashes($form_values[$this->slug]);
        wp_update_post( $article );
    }

    public function validate($form_values)
    {
        //Not available in free version
    }

    protected function enqueue_assets()
    {
        wp_enqueue_style('editor-buttons');
    }
}
