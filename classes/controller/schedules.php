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
        $view->file_finder = View::forge('files/finder');
        // set template vars
        $this->template->title = 'Index';
        $this->template->content = $view;

    }

    public function post_dates()
    {
        return $this->response(Model_Schedule::dates());
    }

    public function post_deactivate()
    {

        // get ids to search for
        $ids = Input::post('ids');
        // update available status for schedules
        $query = DB::update('schedules')
            ->set(array('available' => '0'))
            ->where('id', 'in', $ids);
        // save
        $query->execute();
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
        // find schedule in database
        $schedule = Model_Schedule::query()
            ->related('schedule_files')
            ->where('id', $schedule_id)
            ->get_one();
        // verify we found it
        if (!$schedule)
            return $this->response('SCHEDULE_NOT_FOUND');

        ///////////////////////////////////////////
        // VERIFY SCHEDULE DTO IN SYNC WITH LIVE //
        ///////////////////////////////////////////

        $keep_schedule_file_ids = array();
        // get posted file ids
        $file_ids = Input::post('file_ids');
        // loop through live files, looking up to the last queued
        foreach ($schedule->schedule_files as $schedule_file)
        {

            // if we run into a file that isn't queued, we are done
            if (is_null($schedule_file->queued_on))
                break;

            // get the file ids in order
            $file_id = array_shift($file_ids);
            // verify that we have a file id
            if (!$file_id)
                return $this->response('SCHEDULE_OUT_OF_SYNC');
            // verify that it matches the schedule files
            if ($schedule_file->file->id != $file_id)
                return $this->response('SCHEDULE_OUT_OF_SYNC');

            // add file id to list of file id's we cannot delete
            $keep_schedule_file_ids[] = $schedule_file->id;

        }

        ///////////////////////////////////////
        // DELETE LIVE UNUSED SCHEDULE FILES //
        ///////////////////////////////////////

        // get schedule file ids we need to delete
        $flipped_keep_schedule_file_ids = array_flip($keep_schedule_file_ids);
        $delete_schedule_files = array_diff_key($schedule->schedule_files, $flipped_keep_schedule_file_ids);
        $delete_schedule_files_ids = array_keys($delete_schedule_files);
        // delete schedule files that are not queued or played
        Model_Schedule_File::delete_many($delete_schedule_files_ids);

        ///////////////////////////////
        // INSERT NEW SCHEDULE FILES //
        ///////////////////////////////

        // loop over remaining DTO schedule files
        foreach ($file_ids as $file_id)
        {
            Model_Schedule_File::insert(array(
                'schedule_id' => $schedule_id,
                'file_id' => $file_id,
                'ups' => '0',
                'downs' => '0',
            ));
        }

        // send response
        return $this->response('SUCCESS');

    }

    public function get_generate()
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

        // success
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
        $schedule_out_dateinterval = new DateInterval('P' . $schedule_out_days . 'D');
        // get max schedule out date time
        $max_schedule_out_datetime = clone $server_datetime;
        // get max schedule out datetime
        $max_schedule_out_datetime->add($schedule_out_dateinterval);

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
        if ($this->is_schedule_conflicted($show_start_on_datetime_string, $show_end_at_datetime_string))
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

        // get the DST hours offset
        $DST_hours_offset = Helper::DST_hours_offset($show_start_on_datetime, $server_datetime);
        // get show hours, minutes, and day
        $show_start_on_hours = (int)$show_start_on_datetime->format('H') + $DST_hours_offset;
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

            // verify the show has even started
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
            $schedule_user_start_on_day = $schedule_user_start_on_datetime->format('l');
            // verify this day is in the show repeat schedule
            if ($show->show_repeat->$schedule_user_start_on_day === '0')
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
            if ($this->is_schedule_conflicted($schedule_start_on_datetime_string, $schedule_end_at_datetime_string))
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

    private function is_schedule_conflicted($start_on_datetime_string, $end_at_datetime_string)
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
            ->related('show.block.backup_block')
            ->related('schedule_files')
            ->where('available', '1')
            ->where('end_at', '>', $server_datetime_string)
            ->order_by('start_on', 'ASC')
            ->get();

        ///////////////////////////
        // PROCESS EACH SCHEDULE //
        ///////////////////////////

        // keep track of previous contiguous schedule (to avoid file repeating)
        $previous_schedule = null;
        // loop over all schedules to fill
        foreach ($schedules as $schedule)
        {

            //////////////////////////////
            // VERIFY PREVIOUS SCHEDULE //
            //////////////////////////////

            // if previous schedule null or ends when this on starts,
            if (!is_null($previous_schedule) and ($previous_schedule->end_at != $schedule->start_on))
                $previous_schedule = null;

            //////////////////////////////
            // CHECK FOR ALREADY FILLED //
            //////////////////////////////

            // if there are already scheduled files, continue
            if (count($schedule->schedule_files) > 0)
                continue;

            ////////////////////////
            // GET PREVIOUS FILES //
            ////////////////////////

            // get previous schedule file
            if (is_null($previous_schedule))
                $previous_files = array();
            else
                $previous_files = $previous_schedule->files();

            /////////////////////////////////
            // SET SHOW FILES FOR DURATION //
            /////////////////////////////////

            // fill schedule
            $schedule->fill($previous_files);
            // save schedule
            $schedule->save();

            ///////////////////////////
            // SET PREVIOUS SCHEDULE //
            ///////////////////////////

            // set the previous schedule to us, start/end time check will be done next time around
            $previous_schedule = $schedule;

        }
    }

}
