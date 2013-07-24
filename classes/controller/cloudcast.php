<?php

class Controller_Cloudcast extends Controller_Shared
{

    public function router($method, $params)
    {

        ///////////////
        // KEY CHECK //
        ///////////////

        // get cc key
        $key = Input::get('key');
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
        if (!$this->is_restful() && !$key)
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
