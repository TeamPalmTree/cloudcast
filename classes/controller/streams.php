<?php

class Controller_Streams extends Controller_Cloudcast
{

    public function before()
    {
        $this->section = 'Streams';
        parent::before();
    }

    public function action_index()
    {

        // get all streams
        $streams = Model_Stream::display();
        // create view
        $view = View::forge('streams/index');
        // get all streams
        $view->streams = $streams;
        // set template vars
        $this->template->title = 'Index';
        $this->template->content = $view;

    }

    public function action_create()
    {

        // posted stream
        if (Input::method() == 'POST')
        {
            // create pop & save
            $stream = Model_Stream::forge(array('active' => '1'));
            $stream->populate();
            $stream->save();
            // redirect
            Response::redirect('streams');
        }

        // render create form
        $view = View::forge('streams/form');
        // set view vars
        $view->header = 'Create Stream';
        $view->action = '/streams/create';
        // set view vars
        $this->template->title = 'Create';
        $this->template->content = $view;

    }

    public function action_edit($id)
    {

        // fetch the stream to edit
        $stream = Model_Stream::edit($id);
        // posted stream
        if (Input::method() == 'POST')
        {
            // populate save
            $stream->populate();
            $stream->save();
            // redirect
            Response::redirect('streams');
            // done
            return;
        }

        // render create form
        $view = View::forge('streams/form');
        // set view vars
        $view->header = 'Edit ' . $stream->name;
        $view->action = '/streams/edit/' . $stream->id;
        $view->set('stream', $stream, false);
        // set view vars
        $this->template->title = 'Edit';
        $this->template->content = $view;

    }

    public function action_delete($id)
    {
        if ($stream = Model_Stream::find($id))
            $stream->delete();
        Response::redirect('/streams');
    }

    public function get_active()
    {

        // get all streams
        $streams = Model_Stream::query()
            ->where('active', '1')
            ->get();
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

    public function get_deactivate()
    {

        // get id to search for
        $id = Input::get('id');
        // get the stream
        $stream = Model_Stream::find($id);
        // set stream inactive and persist
        $stream->active = '0';
        $stream->save();
        // success
        return $this->response('SUCCESS');

    }

}
