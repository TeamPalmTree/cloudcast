<?php

class Controller_Service extends Controller_Cloudcast
{

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
        if ($current_schedule_file)
        {
            // current file
            $status->current_file_id = $current_schedule_file->file->id;
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
        if ($next_schedule_file)
        {
            // next file
            $status->next_file_artist = $next_schedule_file->file->artist;
            $status->next_file_title = $next_schedule_file->file->title;
        }

        ////////////////////////////
        // SET NEXT SCHEDULE DATA //
        ////////////////////////////

        // next schedule
        if ($next_schedule)
            $status->next_show_title = $next_schedule->show->title;

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
        $status->show_input_username = \Promoter\Model\Promoter_User::username($inputs['show']->user_id);
        $status->talkover_input_username = \Promoter\Model\Promoter_User::username($inputs['talkover']->user_id);
        $status->master_input_username = \Promoter\Model\Promoter_User::username($inputs['master']->user_id);

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

    public function get_single_shows()
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

    public function get_show_repeat_days()
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

    public function get_recent_files()
    {

        // get recent files
        $recent_files = DB::select(
                array('files.artist', 'artist'),
                array('files.title', 'title')
            )->from('schedule_files')
            ->join('files')->on('schedule_files.file_id', '=', 'files.id')
            ->where('played_on', '!=', null)
            ->where('files.genre', 'NOT IN', array('Intro', 'Closer', 'Bumper', 'Sweeper', 'Ad'))
            ->order_by('played_on', 'DESC')
            ->limit(5)
            ->as_object()->execute();
        // success
        return $this->response($recent_files);

    }

}