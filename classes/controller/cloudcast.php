<?php

class Controller_Cloudcast extends Controller_Standard
{

    protected static $anonymous_rest_methods = array(
        'Controller_Engine.status',
        'Controller_Engine.vote'
    );

    public function router($method, $params)
    {

        ///////////////
        // KEY CHECK //
        ///////////////

        // get some auth parameters
        $key = Input::get('key');
        $rest_method = get_class($this) . '.' . $method;
        // load cloudcast configuration
        Config::load('cloudcast', true);
        // get cc key
        $cloudcast_key = Config::get('cloudcast.key');

        $is_restful = false;
        // if we have a key, validate against that
        // else validate againt simpleauth
        if ($key == $cloudcast_key)
        {
            // we are restful & authorized
            $is_restful = true;
        }
        elseif (in_array($rest_method, self::$anonymous_rest_methods))
        {
            // we are restful & authorized
            $is_restful = true;
        }
        elseif (!Auth::check())
        {
            // we failed to authorize
            Response::redirect();
            return;
        }

        ////////////////////
        // TEMPLATE SETUP //
        ////////////////////

        // if we aren't restful and aren't passing a REST key
        // set up the template for the UI
        if (!$this->is_restful() && !$is_restful)
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

        // forward to FPHP router
        parent::router($method, $params);

    }

}
