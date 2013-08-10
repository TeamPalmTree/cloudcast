<?php

class Model_Schedule_File extends \Orm\Model
{

    protected static $_properties = array(
        'id',
        'played_on',
        'ups',
        'downs',
        'schedule_id',
        'file_id',
    );

    protected static $_belongs_to = array(
        'schedule',
        'file',
    );

    public function client_played_on()
    {
        return Helper::server_datetime_string_to_client_datetime_string($this->played_on);
    }

    public static function the_current($server_datetime)
    {
        ///////////////////////////
        // FIND LAST PLAYED FILE //
        ///////////////////////////

        // find the last played schedule file
        $last_played_schedule_files = Model_Schedule_File::query()
            ->related('schedule')
            ->related('schedule.show')
            ->related('file')
            ->where('played_on', '!=', null)
            ->order_by('played_on', 'desc')
            ->rows_limit(1)
            ->get();

        // get first or return null
        if (!$last_played_schedule_files)
            return null;
        // get last
        $last_played_schedule_file = current($last_played_schedule_files);

        ////////////////////////////////////////
        // ENSURE LAST PLAYED FILE END >= NOW //
        ////////////////////////////////////////

        // get file's duration seconds
        $last_played_schedule_file_duration_seconds = $last_played_schedule_file->file->duration_seconds();
        // get the played on datetime of the last played file
        $last_played_schedule_file_played_on_datetime = Helper::server_datetime($last_played_schedule_file->played_on);
        // add this to the played on to get end of file datetime
        $last_played_schedule_file_end_on_datetime = Helper::datetime_add_seconds($last_played_schedule_file_played_on_datetime, $last_played_schedule_file_duration_seconds);
        // ensure that the end of the file is greater than now
        if ($last_played_schedule_file_end_on_datetime >= $server_datetime)
            return $last_played_schedule_file;
        else
            return null;

    }

    public static function the_next($server_datetime)
    {

        ///////////////////////////////
        // GET CURRENT SCHEDULE FILE //
        ///////////////////////////////

        // initially set next lookup to the server datetime
        $next_schedule_start_on_datetime = $server_datetime;
        // get current schedule file
        $current_schedule_file = Model_Schedule_File::the_current($server_datetime);
        // if we have no current file, we need no lookup time adjustments
        if ($current_schedule_file != null)
        {
            // get the current file's played on datetime
            $current_schedule_file_played_on_datetime = Helper::server_datetime($current_schedule_file->played_on);
            // get number of seconds for current schedule file
            $current_schedule_file_duration_seconds = $current_schedule_file->file->duration_seconds();
            // estimate next schedule start on datetime for lookup purposes
            $next_schedule_start_on_datetime = Helper::datetime_add_seconds($current_schedule_file_played_on_datetime, $current_schedule_file_duration_seconds);
        }

        ////////////////////////////////////////////
        // USE CURRENT TO BETTER PREDICT THE NEXT //
        ////////////////////////////////////////////

        // get next schedule start on datetime string estimation
        $next_schedule_start_on_datetime_string = Helper::server_datetime_string($next_schedule_start_on_datetime);
        // find the current schedule file
        // there may be more than one of the same file, find
        // the lowest id that has been played
        $next_schedule_files = Model_Schedule_File::query()
            ->related('schedule')
            ->related('schedule.show')
            ->related('file')
            ->where('played_on', null)
            ->where('schedule.start_on', '<=', $next_schedule_start_on_datetime_string)
            ->where('schedule.end_at', '>', $next_schedule_start_on_datetime_string)
            ->order_by('id', 'asc')
            ->rows_limit(1)
            ->get();
        // get first or return null
        return $next_schedule_files ? current($next_schedule_files) : null;

    }

    /*public static function the_queue($server_datetime)
    {

        /////////////////////////////////////////////////////////
        // TRIAGE CURRENT VS NONE TO CONTINUE/INITIALIZE QUEUE //
        /////////////////////////////////////////////////////////

        // get current schedule file
        $current_schedule_file = Model_Schedule_File::the_current($server_datetime);
        // if we do not have a current, initialize the queue
        if ($current_schedule_file != null)
        {

            /////////////////////////////
            // GET FIRST SCHEDULE FILE //
            /////////////////////////////

            // get first schedule file
            $first_schedule_file = Model_Schedule_File::the_next($server_datetime);

            //////////////////////////////
            // GET SECOND SCHEDULE FILE //
            //////////////////////////////

            // now get the additional seconds for the lookup time (duration) of the second file
            $second_schedule_file_seconds = $first_schedule_file->file->duration_seconds();
            // get new date time
            $second_schedule_file_datetime = Helper::datetime_add_seconds($server_datetime, $second_schedule_file_seconds);
            // get the second schedule file
            $second_schedule_file = Model_Schedule_File::the_next($second_schedule_file_datetime, $first_schedule_file->id);

            ///////////////////////////
            // RETURN FIRST & SECOND //
            ///////////////////////////

            // this will initialize the LS queue
            // and provide smart_cross support
            return array($first_schedule_file, $second_schedule_file);

        }
        else
        {

            //////////////////////////////
            // GET SECOND SCHEDULE FILE //
            //////////////////////////////

            // now get the additional seconds for the lookup time (duration) of the second file
            $second_schedule_file_seconds = $current_schedule_file->file->duration_seconds();
            // get new date time
            $second_schedule_file_datetime = Helper::datetime_add_seconds($server_datetime, $second_schedule_file_seconds);
            // get the second schedule file
            $second_schedule_file = Model_Schedule_File::the_next($second_schedule_file_datetime);

            /////////////////////////////
            // GET THIRD SCHEDULE FILE //
            /////////////////////////////

            // now get the additional seconds for the lookup time (duration) of the third file
            $third_schedule_file_seconds = $second_schedule_file->file->duration_seconds();
            // get new date time
            $third_schedule_file_datetime = Helper::datetime_add_seconds($server_datetime, $third_schedule_file_seconds);
            // get the third schedule file
            $third_schedule_file = Model_Schedule_File::the_next($third_schedule_file_datetime, $second_schedule_file->id);

            //////////////////
            // RETURN THIRD //
            //////////////////

            // this will initialize the LS queue
            // and provide smart_cross support
            return array($third_schedule_file);

        }

    }*/

    public static function current_by_file($file_id, $server_datetime)
    {
        // get server time
        $server_datetime_string = Helper::server_datetime_string();
        // find the current schedule file with this file
        // there may be more than one of the same file, find
        // the lowest id that hasn't been played ;) bud
        $schedule_files = Model_Schedule_File::query()
            ->related('schedule')
            ->where('schedule.start_on', '<=', $server_datetime_string)
            ->where('schedule.end_at', '>', $server_datetime_string)
            ->where('played_on', null)
            ->where('file_id', $file_id)
            ->order_by('id', 'asc')
            ->rows_limit(1)
            ->get();
        // get first or return null
        return $schedule_files ? current($schedule_files) : null;

    }

}
