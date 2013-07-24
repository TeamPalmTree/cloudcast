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

        ////////////////
        // SAVE TO DB //
        ////////////////

        // get all setting categories
        $settings = Model_Setting::all();
        // posted settings
        if (Input::method() == 'POST')
        {
            // populate and save each setting
            foreach ($settings as $setting)
            {
                // populate
                $setting->populate();
                // save
                $setting->save();
            }

            ////////////////////////
            // RESTART LIQUIDSOAP //
            ////////////////////////

            // tell LS to refresh it's settings
            Liquidsoap::restart();
        }

        ////////////////////////
        // RETURN TO SETTINGS //
        ////////////////////////

        // create view
        $view = View::forge('settings/index');
        // get all settings
        $view->settings = $settings;
        // set template vars
        $this->template->title = 'Index';
        $this->template->content = $view;

    }

    public function get_mapped()
    {
        // get all settings (mapped)
        $settings = Model_Setting::mapped();
        // success
        return $this->response($settings);
    }

}
