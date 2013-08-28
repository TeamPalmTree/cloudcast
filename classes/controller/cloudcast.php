<?php

class Controller_Cloudcast extends Controller_Shared
{

    protected static $anonymous_rest_methods = array(
        'Controller_Engine.status'
    );

    public function router($method, $params)
    {

        ///////////////
        // KEY CHECK //
        ///////////////

        // get some auth parameters
        $key = Input::get('key');
        $rest_method = get_class($this) . '.' . $method;

        $is_restful = false;
        // if we have a key, validate against that
        // else validate againt simpleauth
        if ($key)
        {
            // load cloudcast configuration
            Config::load('cloudcast', true);
            // get cc key
            $cloudcast_key = Config::get('cloudcast.key');
            // set authorized
            if ($key != $cloudcast_key)
                throw new HttpServerErrorException;
            // we are restful
            $is_restful = true;
        }
        elseif (in_array($rest_method, self::$anonymous_rest_methods))
        {
            // we are restful
            $is_restful = true;
        }
        elseif (!Auth::check())
        {
            // redirect to welcome
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
            $this->template->section = $this->section;
            $this->template->modals = View::forge('cloudcast/modals');
            $this->template->display = View::forge('cloudcast/display');
            $this->template->navigation = View::forge('cloudcast/navigation', array(
                'section' => $this->section,
            ));
        }

        // forward to FPHP router
        parent::router($method, $params);

    }

}
