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
        $this->template->title = 'Index';
		$this->template->section->body = $view;

	}

    public function action_create()
    {
        // render create form
        $view = View::forge('shows/form');
        $this->template->title = 'Create';
        $this->template->section->body = $view;

    }

    public function action_edit()
    {

        // render create form
        $view = View::forge('shows/form');
        $this->template->title = 'Edit';
        $this->template->section->body = $view;

    }

    public function post_deactivate()
    {

        // get ids to search for
        $ids = Input::post('ids');
        // update available status for shows
        $query = DB::update('shows')
            ->set(array('available' => '0'))
            ->where('id', 'in', $ids);
        // save
        $query->execute();
        // success
        return $this->response('SUCCESS');

    }

    public function post_edit($id)
    {

        // get show
        $show = Model_Show::editable($id);
        // get show input
        $show_input = Input::json();
        // if we have json data, populate
        if (count($show_input) > 0)
        {
            // validate show
            if ($errors = $show->validate($show_input))
                return $this->errors_response($errors);
            // populate and save
            $show->populate($show_input);
            $show->save();
        }

        // get the editable show
        $show = Model_Show::viewable_editable($show);
        // success
        return $this->response($show);

    }

    public function post_create()
    {

        // get show input
        $show_input = Input::json();
        // if we have json data, populate
        if (count($show_input) > 0)
        {
            // create show
            $show = Model_Show::forge();
            // validate show
            if ($errors = $show->validate($show_input))
                return $this->errors_response($errors);
            // populate and save
            $show->populate($show_input);
            $show->save();
        }
        else
        {
            // get creatable show
            $show = Model_Show::viewable_creatable();
        }

        // success
        return $this->response($show);

    }

    public function get_singles()
    {

        // get server time, adjusted back 24 hours :)
        $server_datetime = Helper::server_datetime();
        $server_datetime->sub(new DateInterval('P1D'));
        $server_datetime_string = Helper::server_datetime_string($server_datetime);
        // get relevant single shows
        $shows = Model_Show::viewable_singles($server_datetime_string);
        // success
        return $this->response($shows);

    }

    public function get_repeat_days()
    {

        // get server time
        $server_datetime_string = Helper::server_datetime_string();
        // create repeat array
        $repeat = array();
        // set repeat shows
        $repeat[] = array('day' => 'Sunday', 'shows' => array_values(Model_Show::viewable_repeats('Sunday', $server_datetime_string)));
        $repeat[] = array('day' => 'Monday', 'shows' => array_values(Model_Show::viewable_repeats('Monday', $server_datetime_string)));
        $repeat[] = array('day' => 'Tuesday', 'shows' => array_values(Model_Show::viewable_repeats('Tuesday', $server_datetime_string)));
        $repeat[] = array('day' => 'Wednesday', 'shows' => array_values(Model_Show::viewable_repeats('Wednesday', $server_datetime_string)));
        $repeat[] = array('day' => 'Thursday', 'shows' => array_values(Model_Show::viewable_repeats('Thursday', $server_datetime_string)));
        $repeat[] = array('day' => 'Friday', 'shows' => array_values(Model_Show::viewable_repeats('Friday', $server_datetime_string)));
        $repeat[] = array('day' => 'Saturday', 'shows' => array_values(Model_Show::viewable_repeats('Saturday', $server_datetime_string)));
        // success
        return $this->response($repeat);

    }

}
