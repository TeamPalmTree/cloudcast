<?php

class Model_Schedule_File extends \Orm\Model
{

    protected static $_properties = array(
        'id',
        'played_on',
        'ups',
        'downs',
        'queued',
        'schedule_id',
        'file_id',
    );

    protected static $_belongs_to = array(
        'schedule',
        'file',
    );

    protected static $most_popular_file;

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
            ->where('schedule.available', '1')
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
            ->where('schedule.available', '1')
            ->where('schedule.start_on', '<=', $next_schedule_start_on_datetime_string)
            ->where('schedule.end_at', '>', $next_schedule_start_on_datetime_string)
            ->order_by('id', 'asc')
            ->rows_limit(1)
            ->get();
        // get first or return null
        return $next_schedule_files ? current($next_schedule_files) : null;

    }

    public static function most_popular_file($server_datetime)
    {

        // see if we have already gotten the most popular
        if (self::$most_popular_file)
            return self::$most_popular_file;

        ////////////////////////////////////////////////
        // CALCULATE POPULARITY MEASUREMENT BEGINNING //
        ////////////////////////////////////////////////

        // first get the number of voting days to look at
        $popularity_days = (int)Model_Setting::get_value('popularity_days');
        // get server datetime
        $popularity_played_on_datetime = clone $server_datetime;
        // get date interval for days in future
        $popularity_dateinterval = new DateInterval('P' . $popularity_days . 'D');
        // sub days to server time to get beginning of popularity metrics
        $popularity_played_on_datetime->sub($popularity_dateinterval);
        // get popularity time string
        $popularity_played_on_datetime_string = Helper::server_datetime_string($popularity_played_on_datetime);

        /////////////////////////////////////////////
        // FIND MOST POPULAR FILE OVER THIS PERIOD //
        /////////////////////////////////////////////

        // find the file with the most votes over the popularity period
        $most_popular_file_sum = DB::select(array('file_id'), DB::expr('SUM(ups + downs) AS votes'))
            ->from('schedule_files')
            ->where('played_on', '>=', $popularity_played_on_datetime_string)
            ->group_by('file_id')
            ->order_by('votes', 'desc')
            ->get_one();
        // now set the most popular file associated with this file id
        self::$most_popular_file = Model_File::find($most_popular_file_sum->file_id);
        // success
        return self::$most_popular_file;

    }

    public static function voteable($schedule_file_id, $server_datetime)
    {

        // get next schedule start on datetime string estimation
        $server_datetime_string = Helper::server_datetime_string($server_datetime);
        // get schedule file, and related things we are voting on
        return Model_Schedule_File::query()
            ->related('file')
            ->related('schedule')
            ->related('schedule.show')
            ->where('id', $schedule_file_id)
            ->where('available', '1')
            ->where('start_on', '<=', $server_datetime_string)
            ->where('end_at', '>', $server_datetime_string)
            ->get_one();

    }

    public static function delete_many($schedule_file_ids)
    {
        // verify we have some
        if (count($schedule_file_ids) == 0)
            return;
        // delete schedule files at DB level
        DB::delete('schedule_files')
            ->where('id', 'in', $schedule_file_ids)
            ->execute();
    }

    public static function insert($schedule_file)
    {
        DB::insert('schedule_files')
            ->set($schedule_file)
            ->execute();
    }

}
