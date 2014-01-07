<?php

class Controller_Cloudcast extends Controller_Standard
{

    public function before()
    {
        // forward up
        parent::before();
        // add status and voting interfaces as anonymously available
        $this->anonymous_methods[] = 'Controller_Engine.status';
        $this->anonymous_methods[] = 'Controller_Engine.vote';
    }

    public function router($method, $params)
    {

        // cloudcast template setup
        if (is_object($this->template))
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

        // forward to router
        parent::router($method, $params);
        // make sure we authenticated authenticate
        if ($this->is_authenticated
            and !$this->is_restful
            and !Auth::has_access('cloudcast.access'))
        {
            // we failed to authorize
            Response::redirect();
            return;
        }

    }

}
