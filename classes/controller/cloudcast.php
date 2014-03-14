<?php

/**
 * Base controller for all model-based controllers
 */
class Controller_Cloudcast extends Controller_Standard
{

    public function before()
    {

        // forward up
        parent::before();
        // add status and voting interfaces as anonymously available
        $this->anonymous_rest_methods[] = 'Controller_Service.*';

    }

    public function router($method, $params)
    {

        // forward to router
        parent::router($method, $params);

        // cloudcast template setup
        if (!$this->is_restful())
        {
            $this->template->site = 'CloudCast';
            $this->template->display = View::forge('cloudcast/display');
            $this->template->navigation = View::forge('cloudcast/navigation', array(
                'section' => $this->section,
            ));
        }
        else
        {
            // allow access to REST resources
            header('Access-Control-Allow-Origin: *');
        }

        // make sure we authenticated
        if ($this->is_user_authenticated)
        {
            // and have cc access
            if (!Auth::has_access('cloudcast.access'))
                throw new HttpAccessDeniedException();

        }
        else
        {
            // or make sure this is an anonymous request
            if (!$this->is_anonymous_authenticated and !$this->is_key_authenticated)
                throw new HttpAccessDeniedException();
        }

    }

}
