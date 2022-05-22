<?php

class SA_Form {
    private $fields;
    private $article_id;
    private $post_type;
    private $article_status;


    function __construct($article_id)
    {

        $this->post_type = SA_Helper::get_post_type();
        $this->article_id = $article_id;
        $this->set_article_status($article_id);

        $all_fields = SA_Helper::get_registered_fields_instances($this->post_type);

        $selected_fields = SA_Helper::get_selected_fields();

        foreach ($selected_fields as $key=>$field_config){
            $original_field = $all_fields[$key];
            if(is_object($original_field)) {
                $original_config =  $original_field->get_config();
                if(is_array($original_config)){
                    $original_field_config = $original_config['field_config'];
                }else{
                    $original_field_config = get_object_vars($field_config);
                }
                $original_field->set_field_config(array_merge($original_field_config, get_object_vars($field_config)));
                $this->fields[] = $original_field;
            }
        }
    }
    public function save($data)
    {
        global $bp;

        $validation = $this->validate_fields($data);
        if($validation['status']){

            if($this->article_id == 0){
                $article = array(
                    'post_title'     => time(),
                    'post_type'     => $this->post_type,
                    'post_status'   => $this->get_update_status(),
                    'post_author'   => get_current_user_id()
                );
                $this->article_id  = wp_insert_post( $article );

            }else{
                $article = array(
                    'ID'            => $this->article_id,
                    'post_status'   => $this->get_update_status()
                );

                wp_update_post( $article );
            }

            $this->set_article_status($this->article_id);
            foreach($this->fields as $field){
                $field->save_data($this->article_id, $data);
            }

            $validation['saved_data']['message'] = $this->get_user_message();
            $validation['saved_data']['viewarticle'] = get_permalink($this->article_id);
            $validation['saved_data']['newarticle'] = $bp->loggedin_user->domain.'/articles/new';
            $validation['saved_data']['editarticle'] = $bp->loggedin_user->domain.'/articles/new?article='.$this->article_id;
        }
        return $validation;
    }

    public function show_fields()
    {
        foreach($this->fields as $field){
            $field->show_field($this->article_id);
        }
    }


    private function validate_fields($data)
    {
        $result = array();
        $result['status'] = true;
        foreach($this->fields as $field){
            $message = $field->validate($data);
            if(!empty($message)){
                $result['messages'][] = $message;
                $result['status'] = false;
            }
        }
        return $result;
    }

    public function get_article_status()
    {
        return $this->article_status;
    }

    private function set_article_status($article_id)
    {
        if($article_id > 0){
            $this->article_status = get_post($article_id)->post_status;
        }else{
            $this->article_status = "new-post";
        }
    }

    private function get_update_status()
    {
        if($_POST['publish-status']){
            return $_POST['publish-status'];
        }else {
            if($this->article_status == "new-post"){
                return 'draft';
            }else {
                return $this->get_article_status();
            }
        }
    }

    public function show_publish_actions()
    {
        include(SA_BASE_PATH.'/includes/sa-form/fields/templates/commons/publish-actions-tpart.php');

    }

    public function show_article_status(){
        $statusLabels =
            array(  "publish"=>__('Published', 'social-articles'),
                "draft"=>__('Draft', 'social-articles'),
                "pending"=>__('Under review', 'social-articles'),
                "new-post"=>__('New', 'social-articles'));

        include(SA_BASE_PATH.'/includes/sa-form/fields/templates/commons/article-status-tpart.php');
    }

    public function get_user_message()
    {
        switch ($this->article_status) {
            case 'pending':
                return __("Your article is under review. When the editors approve it you will get a notification.", "social-articles");
                break;

            case 'draft':
                return __("Your article is in draft form.", "social-articles" );
                break;

            default:
                return __("Your article has been published.", "social-articles" );

        }
    }
}

?>