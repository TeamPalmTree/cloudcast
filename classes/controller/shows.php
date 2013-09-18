<?php

class Controller_Shows extends Controller_Cloudcast
{

    public function before()
    {
        $this->section = 'Shows';
        parent::before();
    }

	public function action_index()
	{

        // create view
        $view = View::forge('shows/index');
        // get server time, adjusted back 24 hours :)
        $server_datetime = Helper::server_datetime();
        $server_datetime->sub(new DateInterval('P1D'));
        $server_datetime_string = Helper::server_datetime_string($server_datetime);
        // get upcoming shows
        $view->single_shows = Model_Show::single($server_datetime_string);
        // get repeat shows
        $view->repeat_days = Array();
        $view->repeat_days['Sunday'] = Model_Show::repeat('Sunday', $server_datetime_string);
        $view->repeat_days['Monday'] = Model_Show::repeat('Monday', $server_datetime_string);
        $view->repeat_days['Tuesday'] = Model_Show::repeat('Tuesday', $server_datetime_string);
        $view->repeat_days['Wednesday'] = Model_Show::repeat('Wednesday', $server_datetime_string);
        $view->repeat_days['Thursday'] = Model_Show::repeat('Thursday', $server_datetime_string);
        $view->repeat_days['Friday'] = Model_Show::repeat('Friday', $server_datetime_string);
        $view->repeat_days['Saturday'] = Model_Show::repeat('Saturday', $server_datetime_string);
        // set template vars
        $this->template->title = 'Index';
		$this->template->content = $view;

	}

    public function action_create()
    {

        // posted show
        if (Input::method() == 'POST')
        {
            // create, pop, save
            $show = Model_Show::forge();
            $show->populate();
            $show->save();
            // redirect
            Response::redirect('shows');
        }

        // render create form
        $view = View::forge('shows/form');
        $view->promos_album = Model_Setting::get_value('station_name');
        // set view vars
        $this->template->title = 'Create';
        $this->template->content = $view;

    }

    public function action_edit($id)
    {

        // fetch the show to edit
        $show = Model_Show::edit($id);
        // posted show
        if (Input::method() == 'POST')
        {
            // populate & save
            $show->populate();
            $show->save();
            // redirect
            Response::redirect('shows');
            // done
            return;
        }

        // render create form
        $view = View::forge('shows/form');
        $view->show = $show;
        // set view vars
        $this->template->title = 'Edit';
        $this->template->content = $view;

    }

    public function action_delete($id)
    {
        if ($show = Model_Show::find($id))
            $show->delete();
        Response::redirect('/shows');
    }

}
