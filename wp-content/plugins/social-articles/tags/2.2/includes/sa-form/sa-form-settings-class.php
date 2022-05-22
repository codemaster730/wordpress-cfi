<?php

class SA_Form_Settings {

    public $post_type;
    public $all_fields;
    public $available_fields;
    public $selected_fields;

    function __construct()
    {
        SA_Helper::set_registered_fields('');
        $this->register_actions();
        $this->register_fields();

    }

    private function register_actions()
    {
        add_action(SA_Helper::TITLE_FIELD_REGISTRATION_ACTION, array(&$this, 'register_field'), 10, 2);
        add_action(SA_Helper::TAX_HIERARCHICAL_FIELD_REGISTRATION_ACTION, array(&$this, 'register_field'), 10, 2);
        add_action(SA_Helper::TAX_REGULAR_FIELD_REGISTRATION_ACTION, array(&$this, 'register_field'), 10, 2);
        add_action(SA_Helper::CONTENT_FIELD_REGISTRATION_ACTION, array(&$this, 'register_field'), 10, 2);
        add_action(SA_Helper::FEATURED_IMAGE_FIELD_REGISTRATION_ACTION, array(&$this, 'register_field'), 10, 2);
    }

    private function register_fields()
    {
        do_action(SA_Helper::TITLE_FIELD_REGISTRATION_ACTION, SA_Helper::TITLE_FIELD, 'SA_Field_Title');
        do_action(SA_Helper::TAX_HIERARCHICAL_FIELD_REGISTRATION_ACTION, SA_Helper::TAX_HIERARCHICAL_FIELD, 'SA_Field_Taxonomy_Hierarchical');
        do_action(SA_Helper::TAX_REGULAR_FIELD_REGISTRATION_ACTION, SA_Helper::TAX_REGULAR_FIELD, 'SA_Field_Taxonomy_Regular');
        do_action(SA_Helper::CONTENT_FIELD_REGISTRATION_ACTION, SA_Helper::CONTENT_FIELD, 'SA_Field_Content');
        do_action(SA_Helper::FEATURED_IMAGE_FIELD_REGISTRATION_ACTION, SA_Helper::FEATURED_IMAGE_FIELD, 'SA_Field_Featured_Image');

        do_action(SA_Helper::PREMIUM_FIELDS);
    }

    public function register_field($sa_field='', $sa_class='')
    {
        
        $registered_fields = SA_Helper::get_registered_fields();
        $registered_fields[$sa_field] = $sa_class;
        SA_Helper::set_registered_fields($registered_fields);
    }

    public function init_form_instance()
    {
        $this->post_type = SA_Helper::get_post_type();

        $this->set_fields_instances();

        $this->selected_fields = SA_Helper::get_selected_fields();
        if(empty($this->selected_fields)){
            $this->selected_fields = $this->all_fields;//NOTE: first time
            $this->available_fields = array();
        }else{
            $this->available_fields = $this->all_fields;

            foreach ($this->selected_fields as $key=>$field_config){
                $original_field = $this->all_fields[$key];
                if(is_object($original_field)) {
                    $original_config =  $original_field->get_config();
                    if(is_array($original_config)){
                        $original_field_config = $original_config['field_config'];
                    }else{
                        $original_field_config = get_object_vars($field_config);
                    }
                    //$original_field_config = $original_field->get_config()['field_config'];
                    $original_field->set_field_config(array_merge($original_field_config, get_object_vars($field_config)));
                    $this->selected_fields[$key] = $original_field;
                    unset($this->available_fields[$key]);
                }else{
                    unset($this->selected_fields[$key]);
                }
            }
        }
    }

    public function save_form_instance($fields)
    {
        update_option(SA_Helper::SELECTED_FIELDS,$fields);
        $this->init_form_instance();
    }

    public function set_fields_instances()
    {
        $this->all_fields = SA_Helper::get_registered_fields_instances($this->post_type);
    }
}

?>