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

    public function get_statistics($id)
    {

        ////////////////
        // GET STREAM //
        ////////////////

        // get the stream
        $stream = Model_Stream::find($id);
        // verify stream
        if ($stream == null)
            return $this->response('INVALID_STREAM');

        ///////////////////////////////
        // GET CURRENT SCHEDULE FILE //
        ///////////////////////////////

        // get server date time
        $server_datetime = Helper::server_datetime();
        // get currently playing file
        $current_schedule_file = Model_Schedule_File::the_current($server_datetime);
        // if we have nothing current, we are done
        if ($current_schedule_file == null)
            return $this->response('INVALID_CURRENT_SCHEDULE_FILE');

        /////////////////////
        // CREATE NEW STAT //
        /////////////////////

        // create new statistic
        $stream_statistic = Model_Stream_Statistic::forge();
        // set stat properties
        $stream_statistic->stream_id = $id;
        $stream_statistic->schedule_file_id = $current_schedule_file->id;
        $stream_statistic->captured_on = Helper::server_datetime_string($server_datetime);

        ////////////////////////////////////
        // SET STATS FROM CORRECT SERVICE //
        ////////////////////////////////////

        try
        {
            // switch on stream type
            switch ($stream->type)
            {
                // Icecast
                case '1':
                    // query stats from icecast
                    $icecast_hook = new IcecastHook(
                        $stream->host,
                        $stream->port,
                        $stream->admin_username,
                        $stream->admin_password
                    );
                    // get statistics from icecast for this mount
                    $icecast_mount_statistics = $icecast_hook->mount_statistics($stream->mount);
                    // verify we got something
                    if ($icecast_mount_statistics == null)
                        throw new Exception('UNABLE_TO_OBTAIN');
                    // set listeners
                    $stream_statistic->listeners = $icecast_mount_statistics['listeners'];
                    break;

                // stats not supported
                default:
                    throw new Exception('STATISTICS_NOT_SUPPORTED');
                    break;
            }
        }
        catch (Exception $exception)
        {
            // fail
            return $this->response($exception->getMessage());
        }

        // save statistic
        $stream_statistic->save();
        // success
        return $this->response('SUCCESS');

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
