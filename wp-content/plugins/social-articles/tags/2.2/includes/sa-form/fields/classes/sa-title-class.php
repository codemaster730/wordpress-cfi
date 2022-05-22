<?php

class SA_Field_Title extends SA_Field{

    function __construct($slug, $name)
    {
        $config = array(
            "field_name"=>__CLASS__,
            "field_config"=>array(
                'label'=>__('Title', 'social-articles'),
                'placeholder'=>__('Insert the title', 'social-articles'),
                'empty_message'=>__('Title is required', 'social-articles'),
                'size'=>'sa-fullwidth',
                'resizable'=>true
            )
        );
        parent::__construct($slug, $name, $config);
    }

    protected function get_backend()
    {
        return SA_BASE_PATH.'/includes/sa-form/fields/templates/title/backend-part.php';
    }

    protected function get_frontend()
    {
        return SA_BASE_PATH.'/includes/sa-form/fields/templates/title/frontend-part.php';
    }
    
    public function get_data($article_id, $form_values)
    {
        if (empty($form_values[$this->slug])) {
            if ($article_id > 0) {
                return get_post($article_id)->post_title;
            } else {
                return '';
            }
        }else{
            return $form_values[$this->slug];
        }
    }

    public function save_data($article_id, $form_values)
    {
        $article = get_post($article_id);
        $article->post_title = $form_values[$this->slug];
        $article->post_name = $form_values[$this->slug];
        wp_update_post( $article );
    }

    public function validate($form_values)
    {
        return empty($form_values[$this->slug]) ? $this->config['field_config']['empty_message'] : '';
    }

    protected function enqueue_assets()
    {
        
    }
}

