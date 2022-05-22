<?php

class SA_Field_Taxonomy_Hierarchical extends SA_Field{


    function __construct($slug, $name)
    {
        $config = array(
            "field_name"=>__CLASS__,
            "field_config"=>array(
                'label'=> $name,
                'placeholder'=>'Select your option',
                'multiple'=>false,
                'size'=>'sa-halfwidth',
                'resizable'=>true
            )
        );

        parent::__construct($slug, $name, $config);
    }

    protected function get_backend()
    {
        return SA_BASE_PATH.'/includes/sa-form/fields/templates/taxonomy-hierarchical/backend-part.php';
    }

    protected function get_frontend()
    {
        return SA_BASE_PATH.'/includes/sa-form/fields/templates/taxonomy-hierarchical/frontend-part.php';
    }


    public function get_data($article_id, $form_values)
    {
        if (empty($form_values[$this->slug])) {
            if ($article_id > 0) {
                return wp_get_post_terms($article_id, $this->slug, array("fields" => "slugs"));
            }else{
                return array();
            }
        }else{
            return $form_values[$this->slug];
        }
    }

    public function save_data($article_id, $form_values)
    {
        $terms = $form_values[$this->slug];
        wp_set_object_terms( $article_id, $terms, $this->slug, false );
    }

    public function validate($form_values)
    {

    }

    protected function enqueue_assets()
    {
        wp_enqueue_style('select2-css', SA_BASE_URL. '/assets/css/select2.min.css', array(), SA_PLUGIN_VERSION);
        wp_enqueue_script( 'select2-js', SA_BASE_URL. '/assets/js/select2.min.js', array('jquery'), SA_PLUGIN_VERSION, true );
    }
}

