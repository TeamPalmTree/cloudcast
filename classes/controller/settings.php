<?php

class Controller_Settings extends Controller_Cloudcast
{

    public function before()
    {
        $this->section = 'Settings';
        parent::before();
    }

    public function action_index()
    {
        // create view
        $view = View::forge('settings/index');
        // set template vars
        $this->template->title = 'Index';
        $this->template->content = $view;
    }

    public function post_save()
    {

        // get settings input
        $settings_input = Input::json();
        // if we have json data, populate
        if (count($settings_input) > 0)
            Model_Setting::commit($settings_input);
        // success
        return $this->response('SUCCESS');

    }

    public function get_categories()
    {
        // get all settings (categorized)
        $categories = Model_Setting::categories();
        // convert to value array
        $categories = array_values($categories);
        // success
        return $this->response($categories);
    }

    public function get_values()
    {
        // get all settings (values)
        $settings = Model_Setting::values();
        // success
        return $this->response($settings);
    }

}
