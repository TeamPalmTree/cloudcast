<?php

/**
 * Controller for stream model access
 */
class Controller_Streams extends Controller_Cloudcast
{

    public function before()
    {
        $this->section = 'Streams';
        parent::before();
    }

    public function action_index()
    {

        // create view
        $view = View::forge('streams/index');
        // set template vars
        $this->template->title = 'Index';
        $this->template->section->body = $view;

    }

    public function action_create()
    {
        // render create form
        $view = View::forge('streams/form');
        // set view vars
        $this->template->title = 'Create';
        $this->template->section->body = $view;

    }

    public function action_edit($id)
    {

        // render create form
        $view = View::forge('streams/form');
        // set view vars
        $this->template->title = 'Edit';
        $this->template->section->body = $view;

    }

    public function action_delete($id)
    {
        if ($stream = Model_Stream::find($id))
            $stream->delete();
        Response::redirect('/streams');
    }

    public function post_create()
    {

        // get stream input
        $stream_input = Input::json();
        // if we have json data, populate
        if (count($stream_input) > 0)
        {
            // create stream
            $stream = Model_Stream::forge();
            // validate block
            if ($errors = $stream->validate($stream_input))
                return $this->errors_response($errors);
            // populate and save
            $stream->populate($stream_input);
            $stream->save();
        }
        else
        {
            // get creatable stream
            $stream = Model_Stream::viewable_creatable();
        }

        // success
        return $this->response($stream);
    }

    public function post_edit($id)
    {

        // get stream
        $stream = Model_Stream::editable($id);
        // get stream input
        $stream_input = Input::json();
        // if we have json data, populate
        if (count($stream_input) > 0)
        {
            // validate stream
            if ($errors = $stream->validate($stream_input))
                return $this->errors_response($errors);
            // populate and save
            $stream->populate($stream_input);
            $stream->save();
        }

        // success
        return $this->response($stream);

    }

    public function get_displayable()
    {

        // get all streams
        $streams = Model_Stream::viewable_active();
        // get array values
        $streams = array_values($streams);
        // success
        return $this->response($streams);

    }

    public function get_active()
    {

        // get all streams
        $streams = Model_Stream::active();
        // get array values
        $streams = array_values($streams);
        // success
        return $this->response($streams);

    }

    public function get_activate()
    {

        // get id to search for
        $id = Input::get('id');
        // get the stream
        $stream = Model_Stream::find($id);
        // set stream active and persist
        $stream->active = '1';
        $stream->save();
        // success
        return $this->response('SUCCESS');

    }

    public function post_deactivate()
    {

        // get ids to search for
        $ids = Input::post('ids');
        // update available status for streams
        $query = DB::update('streams')
            ->set(array('active' => '0'))
            ->where('id', 'in', $ids);
        // save
        $query->execute();
        // success
        return $this->response('SUCCESS');

    }

}
