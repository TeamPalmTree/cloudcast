<?php

class Controller_Schedules extends Controller_Cloudcast
{

    public function before()
    {
        $this->section = 'Schedules';
        parent::before();
    }

    public function action_index()
    {

        // create view
        $view = View::forge('schedules/index');
        // get file finder
        $view->files_finder = View::forge('files/finder');
        // get all dated schedules
        $view->set('schedule_dates', Model_Schedule::dates(), false);
        // set template vars
        $this->template->title = 'Index';
        $this->template->content = $view;

    }

    public function get_delete($id)
    {
        // find the schedule
        $schedule = Model_Schedule::find($id);
        // disable the schedule
        $schedule->available = '0';
        // save
        $schedule->save();
        // success
        return $this->response('SUCCESS');
    }

    public function post_save()
    {

        ///////////////////////
        // FIND THE SCHEDULE //
        ///////////////////////

        // get posted schedule id
        $schedule_id = Input::post('id');
        // get posted schedule files
        $schedule_files = Input::post('schedule_files');
        // find schedule in database
        $schedule = Model_Schedule::query()
            ->where('id', $schedule_id)
            ->get_one();
        // verify we found it
        if (!$schedule)
            return $this->response('SCHEDULE_NOT_FOUND');

        ///////////////////////////////////////////////
        // VERIFY SCHEDULE MODIFICATION RESTRICTIONS //
        ///////////////////////////////////////////////

        // get server datetime
        $server_datetime = Helper::server_datetime();
        // get schedule datetime
        $schedule_datetime = $schedule->start_on_datetime();
        // verify the start date of the schedule is in the future
        if ($schedule_datetime <= $server_datetime)
            return $this->response('SCHEDULE_LOCKED');

        ////////////////////////
        // ADD SCHEDULE FILES //
        ////////////////////////

        // clear schedule files
        Model_Schedule::clear_files($schedule_id);
        // setup schedule files array
        $schedule->schedule_files = array();
        // loop over DTO schedule files
        foreach ($schedule_files as $schedule_file)
        {
            $schedule->schedule_files[] = Model_Schedule_File::forge(array(
                'schedule_id' => $schedule_id,
                'file_id' => $schedule_file['file_id'],
            ));
        }

        /////////////
        // SUCCESS //
        /////////////

        // save schedule
        $schedule->save();
        // send response
        return $this->response('SUCCESS');

    }

    public function get_generate($redirect = false)
    {

        ///////////
        // SETUP //
        ///////////

        // get server datetime string
        $server_datetime_string = Helper::server_datetime_string();

        //////////////
        // SCHEDULE //
        //////////////

        $this->schedule($server_datetime_string);

        //////////
        // FILL //
        //////////

        $this->fill($server_datetime_string);

        //////////////////////
        // CHECK LIQUIDSOAP //
        //////////////////////

        // we are the symbiote of lord liquidsoap
        // don't obsess... we have time for condoms later
        // the only disease we get for now is angry alpha listeners ;)

        /////////////
        // SUCCESS //
        /////////////

        // send response
        if ($redirect)
            Response::redirect('schedules');
        else
            return $this->response('SUCCESS');

    }

    public function schedule($server_datetime_string)
    {

        ///////////
        // SETUP //
        ///////////

        // get schedule out days
        $schedule_out_days = (int)Model_Setting::get_value('schedule_out_days');
        // get server datetime
        $server_datetime = Helper::server_datetime();
        // get schedule out interval
        $schedule_out_interval = new DateInterval('P' . $schedule_out_days . 'D');
        // get max schedule out date time
        $max_schedule_out_datetime = clone $server_datetime;
        // get max schedule out datetime
        $max_schedule_out_datetime->add($schedule_out_interval);

        //////////////////////////////////////
        // GENERATE SCHEDULES FOR EACH SHOW //
        //////////////////////////////////////

        // get relevant shows
        $shows = Model_Show::relevant($server_datetime_string);
        // process one show at a time
        foreach ($shows as $show)
        {

            /////////////////////////////
            // NO BLOCK == NO SCHEDULE //
            /////////////////////////////

            // if we have no block, we are done
            if ($show->block == null)
                continue;

            ///////////////////////////////////////
            // NO REPEAT, SEE IF WE ARE IN RANGE //
            ///////////////////////////////////////

            // get show date time
            $show_start_on_datetime = $show->start_on_datetime();
            // check for repeat schedule
            if ($show->show_repeat == null)
            {
                // generate single schedule
                $this->schedule_single(
                    $show,
                    $show_start_on_datetime,
                    $max_schedule_out_datetime
                );
            }
            else
            {
                // generate repeated schedules
                $this->schedule_repeat(
                    $show,
                    $show_start_on_datetime,
                    $schedule_out_days,
                    $server_datetime
                );
            }

        }
    }

    public function schedule_single(
        $show,
        $show_start_on_datetime,
        $max_schedule_out_datetime)
    {

        /////////////////////////////
        // CHECK FOR SHOW IN RANGE //
        /////////////////////////////

        // make sure show is within max schedule out timeframe
        if ($show_start_on_datetime > $max_schedule_out_datetime)
            return;

        ///////////////////////////////////////////
        // VERIFY NOTHING HAS BEEN SCHEDULED YET //
        ///////////////////////////////////////////

        // calculate show ending datetime
        $show_end_at_datetime = Helper::datetime_add_duration($show_start_on_datetime, $show->duration);
        // calculate time strings
        $show_start_on_datetime_string = Helper::server_datetime_string($show_start_on_datetime);
        $show_end_at_datetime_string = Helper::server_datetime_string($show_end_at_datetime);
        // see if we have a conflict
        if ($this->schedule_conflicted($show_start_on_datetime_string, $show_end_at_datetime_string))
            return;

        /////////////////////////////////////
        // CREATE UPCOMING SINGLE SCHEDULE //
        /////////////////////////////////////

        // we are in range, make a schedule
        $schedule = Model_Schedule::forge(array(
            'start_on' => $show_start_on_datetime_string,
            'end_at' => $show_end_at_datetime_string,
            'show_id' => $show->id,
            'ups' => '0',
            'downs' => '0',
            'available' => '1'
        ));

        // save
        $schedule->save();

    }

    public function schedule_repeat(
        $show,
        $show_start_on_datetime,
        $schedule_out_days,
        $server_datetime)
    {

        //////////////////
        // SETUP REPEAT //
        //////////////////

        // get show hours, minutes, and day
        $show_start_on_hours = (int)$show_start_on_datetime->format('H');
        $show_start_on_minutes = (int)$show_start_on_datetime->format('i');
        // get show end date time
        $show_repeat_end_on_datetime = $show->show_repeat->end_on_datetime();

        /////////////////////////////////////////
        // LOOP OVER NUMBER OF DAYS IN ADVANCE //
        /////////////////////////////////////////

        // loop over all days to schedule out, create each schedule
        for ($schedule_out_day = 0; $schedule_out_day < $schedule_out_days; $schedule_out_day++)
        {

            ////////////////////////////////////////////
            // CALCULATE SHOW START TIME FOR THIS DAY //
            ////////////////////////////////////////////

            // get server datetime
            $schedule_start_on_datetime = clone $server_datetime;
            // get date interval for days in future
            $schedule_out_dateinterval = new DateInterval('P' . $schedule_out_day . 'D');
            // add days to server time
            $schedule_start_on_datetime->add($schedule_out_dateinterval);
            // reset time to the show's start time
            $schedule_start_on_datetime->setTime($show_start_on_hours, $show_start_on_minutes);

            ////////////////////////////////////////
            // CHECK FOR SHOW STARTED & NOT ENDED //
            ////////////////////////////////////////

            // verify the show has started for this day
            if ($schedule_start_on_datetime < $show_start_on_datetime)
                continue;
            // make sure show is within end time of show
            // else no need to keep going out days ;)
            if (($show_repeat_end_on_datetime != null)
                && ($schedule_start_on_datetime > $show_repeat_end_on_datetime))
                return;

            //////////////////////////////////////////////////
            // CHECK REPEATABILITY OF THE SHOW FOR THIS DAY //
            //////////////////////////////////////////////////

            // get the user schedule start on datetime
            $schedule_user_start_on_datetime = Helper::server_datetime_to_user_datetime($schedule_start_on_datetime);
            // get the user day of the schedule start time
            $schedule_start_on_day = $schedule_user_start_on_datetime->format('l');
            // verify this day is in the show's repeat schedule
            if ($show->show_repeat->$schedule_start_on_day === '0')
                continue;

            ///////////////////////////////////////////
            // VERIFY NOTHING HAS BEEN SCHEDULED YET //
            ///////////////////////////////////////////

            // calculate show ending datetime
            $schedule_end_at_datetime = Helper::datetime_add_duration($schedule_start_on_datetime, $show->duration);
            // calculate time strings
            $schedule_start_on_datetime_string = Helper::server_datetime_string($schedule_start_on_datetime);
            $schedule_end_at_datetime_string = Helper::server_datetime_string($schedule_end_at_datetime);
            // see if we have a conflict
            if ($this->schedule_conflicted($schedule_start_on_datetime_string, $schedule_end_at_datetime_string))
                continue;

            //////////////////////////////////////////
            // CREATE SCHEDULE FOR THIS REPEAT SHOW //
            //////////////////////////////////////////

            // forge new schedule
            $schedule = Model_Schedule::forge(array(
                'start_on' => $schedule_start_on_datetime_string,
                'end_at' => $schedule_end_at_datetime_string,
                'show_id' => $show->id,
                'ups' => '0',
                'downs' => '0',
                'available' => '1'
            ));

            // save
            $schedule->save();

        }
    }

    private function schedule_conflicted($start_on_datetime_string, $end_at_datetime_string)
    {

        return Model_Schedule::query()
            ->where('available', '1')
            ->and_where_open()
                ->or_where_open()
                    ->where('start_on', '<=', $start_on_datetime_string)
                    ->where('end_at', '>', $start_on_datetime_string)
                ->or_where_close()
                ->or_where_open()
                    ->where('start_on', '<', $end_at_datetime_string)
                    ->where('end_at', '>=', $end_at_datetime_string)
                ->or_where_close()
            ->and_where_close()
            ->count() > 0;

    }

    public function fill($server_datetime_string)
    {

        ////////////////////////////
        // GET ALL SHOW SCHEDULES //
        ////////////////////////////

        // get the schedules that end after now
        // this will fill schedules that are currently in range
        $schedules = Model_Schedule::query()
            ->related('show')
            ->related('show.block')
            ->related('schedule_files')
            ->where('available', '1')
            ->where('end_at', '>', $server_datetime_string)
            ->get();

        ///////////////////////////
        // PROCESS EACH SCHEDULE //
        ///////////////////////////

        foreach ($schedules as $schedule)
        {

            //////////////////////////////
            // CHECK FOR ALREADY FILLED //
            //////////////////////////////

            if (count($schedule->schedule_files) > 0)
                continue;

            /////////////////////////////////
            // SET SHOW FILES FOR DURATION //
            /////////////////////////////////

            // set schedule files
            $files = $schedule->show->files();
            // generate each schedule file
            foreach ($files as $file)
            {

                // create schedule file
                $schedule_file = Model_Schedule_File::forge();
                // set properties
                $schedule_file->schedule_id = $schedule->id;
                $schedule_file->file_id = $file->id;
                $schedule_file->ups = 0;
                $schedule_file->downs = 0;
                // save schedule file
                $schedule_file->save();
            }

        }
    }

}
