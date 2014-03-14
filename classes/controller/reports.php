<?php

/**
 * Controller for report model access
 */
class Controller_Reports extends Controller_Cloudcast
{

    public function before()
    {
        $this->section = 'Reports';
        parent::before();
    }

    public function action_index()
    {

        // create view
        $view = View::forge('reports/index');
        // get all reports
        // set template vars
        $this->template->title = 'Index';
        $this->template->section->body = $view;

    }

}
