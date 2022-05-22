<?php
class SA_Helper{

    const TITLE_FIELD = 'sa_title';
    const TAX_HIERARCHICAL_FIELD = 'sa_tax_hierarchical';
    const TAX_REGULAR_FIELD = 'sa_tax_regular';
    const CONTENT_FIELD = 'sa_content';
    const FEATURED_IMAGE_FIELD = 'sa_featured_image';

    const TITLE_FIELD_REGISTRATION_ACTION = 'sa_title_registration';
    const TAX_HIERARCHICAL_FIELD_REGISTRATION_ACTION = 'sa_tax_hierarchical_registration';
    const TAX_REGULAR_FIELD_REGISTRATION_ACTION = 'sa_tax_regular_registration';
    const CONTENT_FIELD_REGISTRATION_ACTION = 'sa_content_registration';
    const FEATURED_IMAGE_FIELD_REGISTRATION_ACTION = 'sa_featured_image_registration';

    const PREMIUM_FIELDS = 'sa_premium_fields';
    const REGISTERED_FIELDS_OPTION = 'sa_registered_fields';
    const SELECTED_FIELDS = 'sa_selected_fields';
    const POST_TYPE = 'sa_post';


    public static function get_selected_fields(){
        $selected_fields = get_option(SA_Helper::SELECTED_FIELDS);
        if(empty($selected_fields)){
            $selected_fields = self::get_registered_fields_instances(self::get_post_type());
            update_option(SA_Helper::SELECTED_FIELDS, $selected_fields);
        }
        return $selected_fields;
    }

    public static function get_post_type(){
        $post_type= get_option(SA_Helper::POST_TYPE);
        if(empty($post_type)) {
            $post_type = 'post';
            update_option(SA_Helper::POST_TYPE, $post_type);
        }

        return $post_type;
    }

    public static function get_registered_fields(){
        return get_option(SA_Helper::REGISTERED_FIELDS_OPTION, array());
    }

    public static function set_registered_fields($fields){
        if($fields == '')
            return delete_option(SA_Helper::REGISTERED_FIELDS_OPTION);
        return update_option(SA_Helper::REGISTERED_FIELDS_OPTION, $fields);
    }

    public static function get_registered_fields_instances($post_type){
        $registered_fields = SA_Helper::get_registered_fields();

        $all_fields = array();

        //Generate Title Instance
        $field_title_class = $registered_fields[SA_Helper::TITLE_FIELD];
        if(class_exists($field_title_class)) {
            $all_fields[SA_Helper::TITLE_FIELD] = new $field_title_class(SA_Helper::TITLE_FIELD, __("Title", "social-articles"));
        }

        //Generate Taxonomies Instances.
        $taxonomies = get_object_taxonomies( $post_type, 'objects' );

        if(array_key_exists(SA_Helper::TAX_HIERARCHICAL_FIELD, $registered_fields )) {
            $field_tax_hierarchical_class = $registered_fields[SA_Helper::TAX_HIERARCHICAL_FIELD];
            if (class_exists($field_tax_hierarchical_class)) {
                if (!empty($taxonomies['category']))
                    $all_fields['category'] = new $field_tax_hierarchical_class($taxonomies['category']->name, $taxonomies['category']->labels->name);
            }
        }

        if(array_key_exists(SA_Helper::TAX_REGULAR_FIELD, $registered_fields )) {
            $field_tax_normal_class = $registered_fields[SA_Helper::TAX_REGULAR_FIELD];
            if (class_exists($field_tax_normal_class)) {
                if (!empty($taxonomies['post_tag']))
                    $all_fields['post_tag'] = new $field_tax_normal_class($taxonomies['post_tag']->name, $taxonomies['post_tag']->labels->name);
            }
        }
        if(array_key_exists(SA_Helper::CONTENT_FIELD ,$registered_fields)) {
            //Generate Content Instance.
            $field_content_class = $registered_fields[SA_Helper::CONTENT_FIELD];
            if (class_exists($field_content_class)) {
                $all_fields[SA_Helper::CONTENT_FIELD] = new $field_content_class(SA_Helper::CONTENT_FIELD, __("Content", "social-articles"));
            }
        }
        if(array_key_exists(SA_Helper::FEATURED_IMAGE_FIELD ,$registered_fields)) {
            //FEATURED_IMAGE_FIELD
            $field_image_class = $registered_fields[SA_Helper::FEATURED_IMAGE_FIELD];
            if (class_exists($field_image_class)) {
                $all_fields[SA_Helper::FEATURED_IMAGE_FIELD] = new $field_image_class(SA_Helper::FEATURED_IMAGE_FIELD, __("Featured image", "social-articles"));
            }
        }

        $all_fields = apply_filters('sa_more_fields', $all_fields, $registered_fields, $post_type);
        return $all_fields;

    }

}