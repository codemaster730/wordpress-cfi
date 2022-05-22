<?php
abstract class SA_Field{

    public $slug;
    public $name;
    public $config;

    abstract protected function get_backend();
    abstract protected function get_frontend();
    abstract protected function enqueue_assets();
    abstract public function validate($form_values);
    abstract public function get_data($article_id, $form_values);
    abstract public function save_data($article_id, $form_values);

    function __construct($slug, $name, $config){
        $this->slug = $slug;
        $this->name = $name;
        $this->config = $config;
    }

    public function show_field($article_id=0)
    {
        $this->enqueue_assets();
        include($this->get_frontend());
    }

    public function show_backend_field()
    {
        include($this->get_backend());
    }

    public function get_config()
    {
        return $this->config;
    }

    public function set_config($config)
    {
        $this->config = $config;
    }

    public function set_field_config($config)
    {
        $this->config['field_config'] = $config;
    }

    public function get_width(){
        return $this->config['field_config']['size'] == 'sa-halfwidth' ? '50%' : '100%';
    }

    public function add_actions(){} 

}