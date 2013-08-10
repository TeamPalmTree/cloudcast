<?php

class Controller_Engine extends Controller_Cloudcast {

    public function get_status()
    {

        //////////////////
        // FORGE STATUS //
        //////////////////

        // create new status model
        $status = new Model_Status();
        // set off air
        $status->on_air = false;
        // get server time
        $server_datetime = Helper::server_datetime();
        // get generated time
        $status->client_generated_on = Helper::server_datetime_to_client_datetime_string($server_datetime);

        //////////////////////////////////////////
        // GET CURRENT/NEXT SCHEDULE FILES/SHOW //
        //////////////////////////////////////////

        $current_schedule_file = Model_Schedule_File::the_current($server_datetime);
        $next_schedule_file = Model_Schedule_File::the_next($server_datetime);
        $next_schedule = Model_Schedule::the_next($server_datetime);

        ////////////////////////////////////
        // SET CURRENT SCHEDULE FILE DATA //
        ////////////////////////////////////

        // current schedule file
        if ($current_schedule_file != null)
        {
            // current file
            $status->current_file_artist = $current_schedule_file->file->artist;
            $status->current_file_title = $current_schedule_file->file->title;
            $status->current_file_duration = $current_schedule_file->file->duration;
            // current show
            $status->current_show_title = $current_schedule_file->schedule->show->title;
            $status->current_show_duration = $current_schedule_file->schedule->show->duration;
            // current schedule
            $status->current_client_schedule_start_on = $current_schedule_file->schedule->client_start_on();
            // current schedule file
            $status->current_client_schedule_file_played_on = $current_schedule_file->client_played_on();
        }

        /////////////////////////////////
        // SET NEXT SCHEDULE FILE DATA //
        /////////////////////////////////

        // next schedule file
        if ($next_schedule_file != null)
        {
            // next file
            $status->next_file_artist = $next_schedule_file->file->artist;
            $status->next_file_title = $next_schedule_file->file->title;
        }

        ////////////////////////////
        // SET NEXT SCHEDULE DATA //
        ////////////////////////////

        // next schedule
        if ($next_schedule != null)
        {
            // next show
            $status->next_show_title = $next_schedule->show->title;
        }

        ////////////////
        // GET INPUTS //
        ////////////////

        $inputs = Model_Input::mapped();

        ////////////////
        // SET INPUTS //
        ////////////////

        # set statuses
        $status->schedule_input_active = (bool)$inputs['schedule']->status;
        $status->show_input_active = (bool)$inputs['show']->status;
        $status->talkover_input_active = (bool)$inputs['talkover']->status;
        $status->master_input_active = (bool)$inputs['master']->status;
        # set enableds
        $status->schedule_input_enabled = (bool)$inputs['schedule']->enabled;
        $status->show_input_enabled = (bool)$inputs['show']->enabled;
        $status->talkover_input_enabled = (bool)$inputs['talkover']->enabled;
        $status->master_input_enabled = (bool)$inputs['master']->enabled;
        # set usernames
        $status->show_input_username = ($inputs['show']->user != null) ? $inputs['show']->user->username : null;
        $status->talkover_input_username = ($inputs['talkover']->user != null) ? $inputs['talkover']->user->username : null;
        $status->master_input_username = ($inputs['master']->user != null) ? $inputs['master']->user->username : null;

        //////////////
        // SET HOST //
        //////////////

        // host
        $status->host_username = null;

        /////////////
        // SUCCESS //
        /////////////

        // success
        return $this->response($status);

    }

    public function post_update_inputs()
    {

        ///////////////////////
        // PROCESS LS INPUTS //
        ///////////////////////

        // get posted file metadata
        $input = file_get_contents('php://input');
        // get php input statuses
        $inputs = json_decode($input);

        /////////////////////
        // SAVE EACH TO DB //
        /////////////////////

        // loop over all provided input statuses
        // update DB with current status
        foreach ($inputs as $input)
        {

            /////////////////////////
            // FIND CONNECTED USER //
            /////////////////////////

            $input_user = null;
            // attempt to get user based on username
            if (isset($input->username) && $input->username != '')
            {
                // find authenticated user
                $input_user = Model_User::query()
                    ->where('username', $input->username)
                    ->get_one();
            }

            // get update query for inputs
            $query = DB::update('inputs')
                ->value('status', ($input->status == 'true') ? '1' : '0')
                ->value('enabled', ($input->enabled == 'true') ? '1' : '0')
                ->value('user_id', $input_user ? $input_user->id : null)
                ->where('name', $input->name);

            // execute
            $query->execute();

        }

        /////////////
        // SUCCESS //
        /////////////

        // send response
        return $this->response('SUCCESS');

    }

    public function get_play_schedule_file($id)
    {

        ////////////////////////
        // FIND SCHEDULE FILE //
        ////////////////////////

        // find the schedule file
        $schedule_file = Model_Schedule_File::query()
            ->where('id', $id)
            ->get_one();

        ////////////////////////////////////
        // UPDATE SCHEDULE FILE PLAY DATE //
        ////////////////////////////////////

        // get server time
        $server_datetime_string = Helper::server_datetime_string();
        // update play dates
        $schedule_file->played_on = $server_datetime_string;
        $schedule_file->file->last_play = $server_datetime_string;
        // save file
        $schedule_file->save();

        /////////////
        // SUCCESS //
        /////////////

        // send response
        return $this->response('SUCCESS');

    }

    public function get_next_queue()
    {

        /////////////////////////////////////
        // FIND NEXT SCHEDULE FILE TO PLAY //
        /////////////////////////////////////

        // get server datetime
        $server_datetime = Helper::server_datetime();
        // get next schedule file
        $next_schedule_file = Model_Schedule_File::the_next($server_datetime);
        // if we have none, we are done
        if (!$next_schedule_file)
            $this->response('NONE');

        ///////////////////////////////
        // CREATE AND POPULATE QUEUE //
        ///////////////////////////////

        // create new queue
        $next_queue = new Model_Queue();
        // populate
        $next_queue->schedule_file_id = $next_schedule_file->id;
        $next_queue->show_title = $next_schedule_file->schedule->show->title;
        $next_queue->file_name = $next_schedule_file->file->name;
        $next_queue->file_artist = $next_schedule_file->file->artist;
        $next_queue->file_title = $next_schedule_file->file->title;
        // send queue response
        return $this->response($next_queue);

    }

    public function post_authenticate()
    {

        ////////////////////////////
        // PROCESS LS CREDENTIALS //
        ////////////////////////////

        // get posted file metadata
        $input = file_get_contents('php://input');
        // get php arrays from json
        $credentials = json_decode($input);

        ////////////////////
        // AUTHORIZE USER //
        ////////////////////

        // get user (we need to save their login hash)
        $user = Model_User::query()
            ->where('username', $credentials->username)
            ->get_one();
        // login user (this will kill the login hash, so we need to restore)
        if (!Auth::login($credentials->username, $credentials->password))
            return $this->response('INVALID_USER');

        //////////////////////
        // GET CURRENT SHOW //
        //////////////////////

        // get server datetime
        $server_datetime = Helper::server_datetime();
        // get the current schedule file
        $current_schedule = Model_Schedule::the_current($server_datetime);

        /////////////////////////
        // VERIFY INPUT ACCESS //
        /////////////////////////

        // capture response
        $response = 'SUCCESS';
        // switch on type of input
        switch ($credentials->type)
        {
            // show input
            case 'show':
            // talkover input
            case 'talkover':

                // if we have no current show, fail
                if (!$current_schedule)
                {
                    $response = 'NO_CURRENT_SHOW';
                    break;
                }

                // get right based on type
                $access_condition = ($credentials->type == 'show') ? 'show_input.update' : 'talkover_input.update';
                // verify the user is allowed access
                if (!Auth::has_access($access_condition))
                {
                    $response = 'USER_NOT_ALLOWED';
                    break;
                }

                // get user id
                $user_id = Auth::get_user_id();
                // verify user in show
                if (!array_key_exists($user_id[1], $current_schedule->show->users))
                {
                    $response = 'USER_NOT_IN_SHOW';
                    break;
                }
                // success
                break;

            // master input
            case 'master':
                // verify user is admin
                if (!Auth::has_access('master_input.update'))
                {
                    $response = 'USER_NOT_ALLOWED';
                    break;
                }
                // success
                break;
        }

        /////////////////////////////////////
        // RESTORE USER LOGIN HASH/SUCCESS //
        /////////////////////////////////////

        // we need to do this as to not break the UI
        $query = DB::update('users')
            ->value('last_login', $user->last_login)
            ->value('login_hash', $user->login_hash)
            ->where('username', $credentials->username);
        // execute login_hash update
        $query->execute();
        // send response
        return $this->response($response);
    }

    public function get_enable_input()
    {

        // get input & enabled
        $input = Input::get('input');
        $enabled = Input::get('enabled');
        // forward to Liquidsoap
        LiquidsoapHook::enable_input($input, $enabled);
        // send response
        return $this->response('SUCCESS');

    }

    public function get_save_stream_statistics($id)
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

}