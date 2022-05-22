<?php

class SA_Field_Featured_Image extends SA_Field{

    function __construct($slug, $name)
    {
        $config =  array(
            "field_name"=>__CLASS__,
            "field_config"=>array(
                'label'=>__('Featured Image', 'social-articles'),
                'size'=>'sa-fullwidth',
                'resizable'=>true
            )
        );

        parent::__construct($slug, $name, $config);
    }

    protected function get_backend()
    {
        return SA_BASE_PATH.'/includes/sa-form/fields/templates/featured-image/backend-part.php';
    }

    protected function get_frontend()
    {
        return SA_BASE_PATH.'/includes/sa-form/fields/templates/featured-image/frontend-part.php';
    }

    public function get_data($article_id, $form_values){

        if (empty($form_values[$this->slug])) {
            if ($article_id > 0) {
                return get_post_meta($article_id, '_thumbnail_id', true);
            } else {
                return '';
            }
        }else{
            return $form_values[$this->slug];
        }
    }

    public function save_data($article_id, $form_values){
        update_post_meta($article_id, '_thumbnail_id', $form_values[$this->slug]);
    }

    public function validate($form_values){

    }

    protected function enqueue_assets()
    {
        wp_enqueue_script( 'qqUploader', SA_BASE_URL. '/assets/js/fileuploader.js', array('jquery'), SA_PLUGIN_VERSION, true );
        wp_localize_script(
            'qqUploader',
            'global_data',
            array(
                'ajax_url' => admin_url( 'admin-ajax.php' )
            )
        );
    }

    public function image_uploader ()
    {
        check_ajax_referer( 'sa_security_ajax', 'security');

        $uploads = wp_upload_dir();
        if (!class_exists('qqFileUploader')) require_once(SA_BASE_PATH.'/includes/sa-form/fields/commons/uploader.php');
        $uploader = new qqFileUploader(array('jpg', 'jpeg', 'png', 'gif'));
        $result = $uploader->handleUpload($uploads['basedir'].'/');
        echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
        die();
    }

    public function attach_image()
    {

        check_ajax_referer( 'sa_security_ajax', 'security');

        $post_image = $_POST['fileName'];
        $wp_upload_dir = wp_upload_dir();
        $filenameTemp = $wp_upload_dir['basedir'].'/'.$post_image;
        $filename = $wp_upload_dir['path'].'/sa_'.time().'_'.$post_image;

        if ($post_image != "" && copy($filenameTemp,$filename)) {
            unlink($filenameTemp);
            $wp_filetype = wp_check_filetype(basename($filename), null );
            $attachment = array(
                'post_mime_type' => $wp_filetype['type'],
                'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
                'post_content' => '',
                'post_status' => 'inherit'
            );
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            $attach_id = wp_insert_attachment( $attachment, $filename);
            $attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
            wp_update_attachment_metadata( $attach_id, $attach_data );

            $image = wp_get_attachment_image_src( $attach_id );
            $preview_image = wp_get_attachment_image_src( $attach_id, 'large' );

            $result['status']= 'ok';
            $result['attachment_id']= $attach_id;
            $result['preview_image'] = $preview_image[0];
            $result['file_name'] = basename($filename);
        }else{
            $result['status']= 'error|'.$filenameTemp.'|'.$filename;
        }
        echo json_encode($result);
        die();
    }

    function add_actions()
    {
        add_action('wp_ajax_image_uploader', array(&$this, 'image_uploader'));
        add_action('wp_ajax_attach_image', array(&$this, 'attach_image'));
    }

}

