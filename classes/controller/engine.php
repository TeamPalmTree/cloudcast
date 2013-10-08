<?php

class Controller_Engine extends Controller_Cloudcast {

    public function get_status()
    {

        //////////////////
        // FORGE STATUS //
        //////////////////

        // create new status model
        $status = new Model_Status();
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
            $status->current_file_post = $current_schedule_file->file->post;
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

    public function get_next_queue_file()
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
            return $this->response('NONE');

        /////////////////////////////////
        // SET SCHEDULE FILE TO QUEUED //
        /////////////////////////////////

        $next_schedule_file->queued = '1';
        $next_schedule_file->save();

        ///////////////////////////////
        // CREATE AND POPULATE QUEUE //
        ///////////////////////////////

        // create new queue
        $next_queue_file = new Model_Queue_File();
        // populate
        $next_queue_file->schedule_id = $next_schedule_file->schedule->id;
        $next_queue_file->schedule_file_id = $next_schedule_file->id;
        $next_queue_file->file_name = $next_schedule_file->file->name;
        $next_queue_file->file_artist = $next_schedule_file->file->artist;
        $next_queue_file->file_title = $next_schedule_file->file->title;
        // send queue response
        return $this->response($next_queue_file);

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

        //////////////////////////
        // GET CURRENT SCHEDULE //
        //////////////////////////

        // get server datetime
        $server_datetime = Helper::server_datetime();
        // get the current schedule file
        $current_schedule = Model_Schedule::the_current($server_datetime);
        // verify current schedule
        if (!$current_schedule)
            return $this->response('INVALID_CURRENT_SCHEDULE');

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
        if (!$stream)
            return $this->response('INVALID_STREAM');

        ///////////////////////////////
        // GET CURRENT SCHEDULE FILE //
        ///////////////////////////////

        // get server date time
        $server_datetime = Helper::server_datetime();
        // get currently playing file
        $current_schedule_file = Model_Schedule_File::the_current($server_datetime);
        // if we have nothing current, we are done
        if (!$current_schedule_file)
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

    protected function schedule_promos($schedule_id, $genre)
    {

        ///////////////////
        // FIND SCHEDULE //
        ///////////////////

        // get the schedule by id
        $schedule = Model_Schedule::find($schedule_id);
        // verify current schedule
        if (!$schedule)
            return $this->response('INVALID_SCHEDULE');

        //////////////////////
        // GET PROMOS ALBUM //
        //////////////////////

        $album = null;
        // use the genre to determine which album to pull
        switch ($genre)
        {
            case 'Sweeper':
                $album = $schedule->sweepers_album;
                break;
            case 'Jingle':
                $album = $schedule->jingles_album;
                break;
            case 'Bumper':
                $album = $schedule->bumpers_album;
                break;
        }

        ///////////////////////////////
        // GET ALBUM FILES FOR GENRE //
        ///////////////////////////////

        // if we have no album, we are done
        if (!$album)
            $this->response("NONE");

        // query files
        $files = Model_File::query()
            ->where('genre', $genre)
            ->where('album', $album)
            ->where('available', '1')
            ->get();

        $file_names = array();
        // flatten files
        foreach ($files as $file)
            $file_names[] = $file->name;

        // implode file names
        $file_names = implode("\n", $file_names);
        // see if we have any
        if ($file_names == "")
            return $this->response("NONE");
        // send response
        return $this->response($file_names);

    }

    public function get_schedule_sweepers($schedule_id)
    {
        return $this->schedule_promos($schedule_id, 'Sweeper');
    }

    public function get_schedule_jingles($schedule_id)
    {
        return $this->schedule_promos($schedule_id, 'Jingle');
    }

    public function get_schedule_bumpers($schedule_id)
    {
        return $this->schedule_promos($schedule_id, 'Bumper');
    }

    public function get_queue_schedule($schedule_id)
    {

        ///////////////////
        // FIND SCHEDULE //
        ///////////////////

        // get the schedule by id
        $schedule = Model_Schedule::find($schedule_id);
        // verify current schedule
        if (!$schedule)
            return $this->response('INVALID_SCHEDULE');

        /////////////////////////////////
        // CREATE QUEUE, POPULATE SHOW //
        /////////////////////////////////

        // create new queue
        $queue_schedule = new Model_Queue_Schedule();
        // get show
        $show = $schedule->show;
        // populate show info
        $queue_schedule->show_title = $show->title;
        // populate sweeper interval
        $queue_schedule->sweeper_interval = $schedule->sweeper_interval;

        ///////////////////////////////////
        // POPULATE PROMO AVAILABILITIES //
        ///////////////////////////////////

        // get sweepers album
        $sweepers_album = $schedule->sweepers_album;
        if ($sweepers_album)
        {
            $queue_schedule->sweepers_available = Model_File::query()
                ->where('genre', 'Sweeper')
                ->where('album', $sweepers_album)
                ->where('available', '1')
                ->count() > 0;
        }

        // get jingles album
        $jingles_album = $schedule->jingles_album;
        if ($jingles_album)
        {
            $queue_schedule->jingles_available = Model_File::query()
                    ->where('genre', 'Jingle')
                    ->where('album', $jingles_album)
                    ->where('available', '1')
                    ->count() > 0;
        }

        // get bumpers album
        $bumpers_album = $schedule->bumpers_album;
        if ($bumpers_album)
        {
            $queue_schedule->bumpers_available = Model_File::query()
                    ->where('genre', 'Bumper')
                    ->where('album', $bumpers_album)
                    ->where('available', '1')
                    ->count() > 0;
        }

        // send queue response
        return $this->response($queue_schedule);

    }

    public function get_vote($vote)
    {

        ///////////////////////
        // GET SCHEDULE FILE //
        ///////////////////////

        // get server datetime
        $server_datetime = Helper::server_datetime();
        // get the voteable schedule file
        $schedule_file = Model_Schedule_File::the_current($server_datetime);
        // verify we found the schedule file
        if (!$schedule_file)
            return $this->response('INVALID_SCHEDULE_FILE');

        /////////////////////
        // VOTE VALIDATION //
        /////////////////////

        // first make sure the vote is valid
        if (($vote < -1) or ($vote > 1))
            return $this->response('INVALID_VOTE');
        // get the IP of the voter
        $ip_address = $_SERVER['REMOTE_ADDR'];
        // see if there is a vote from this IP and schedule file
        $same_vote = Model_Vote::same($ip_address, $schedule_file->id);
        // now make sure we haven't had a vote from this IP recently
        if ($same_vote)
            return $this->response('VOTE_ALREADY_CAST');

        ////////////////////////
        // UP/DOWNCOUNT VOTES //
        ////////////////////////

        // if the vote wasn't neutral
        if ($vote != 0)
        {
            // perform calcs
            $schedule_file->ups += $vote;
            $schedule_file->file->ups += $vote;
            $schedule_file->schedule->ups += $vote;
            $schedule_file->schedule->show->ups += $vote;
            // save up/down counts
            $schedule_file->save();
        }

        //////////////////
        // VOTE CASTING //
        //////////////////

        // get server date time string
        $server_datetime_string = Helper::server_datetime_string($server_datetime);
        // create a new vote
        $new_vote = Model_Vote::forge(array(
            'vote_cast' => $server_datetime_string,
            'vote' => $vote,
            'ip_address' => $ip_address,
            'schedule_file_id' => $schedule_file
        ));

        // save
        $new_vote->save();
        // return
        return $this->response('SUCCESS');

    }

}