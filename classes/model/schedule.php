<?php

class Model_Schedule extends \Orm\Model
{

    // scheduling properties
    public $gathered_files = array();
    public $previous_files = array();
    public $previous_file = null;
    public $sweeper_files_count = 0;
    public $bumper_files = array();
    public $sweeper_files = array();

    protected static $_properties = array(
        'id',
        'start_on',
        'end_at',
        'available',
        'ups',
        'downs',
        'sweeper_interval',
        'sweepers_album',
        'jingles_album',
        'bumpers_album',
        'show_id',
    );

    protected static $_belongs_to = array(
        'show',
    );

    protected static $_has_many = array(
        'schedule_files',
    );

    public static function dates()
    {

        // get server datetime
        $server_datetime_string = Helper::server_datetime_string();
        // get all schedules
        $schedules = Model_Schedule::query()
            ->related('show')
            ->related('schedule_files')
            ->related('schedule_files.file')
            ->where('available', '1')
            ->where('end_at', '>', $server_datetime_string)
            ->order_by(array(
                'start_on' => 'asc',
                'schedule_files.id' => 'asc'
            ))->get();

        // organize shows into dates
        $dates = array();
        // loop over schedules
        foreach ($schedules as $schedule)
        {
            // get schedule start date
            $user_schedule_start_on_date = $schedule->user_start_on_date();
            // if we don't have it yet, add it
            if (!array_key_exists($user_schedule_start_on_date, $dates))
            {
                // add to dates
                $dates[$user_schedule_start_on_date] = array(
                    'date' => $user_schedule_start_on_date,
                    'schedules' => array()
                );
            }

            // set show start/end times adjust to user timezone
            $schedule->user_start_on_timeday = $schedule->user_start_on_timeday();
            $schedule->user_end_at_timeday = $schedule->user_end_at_timeday();
            // make files an array
            $schedule->schedule_files = array_values($schedule->schedule_files);
            // add schedule to current date
            $dates[$user_schedule_start_on_date]['schedules'][] = $schedule;
        }

        return array_values($dates);
    }

    public function user_start_on_date()
    {
        return Helper::datetime_string_date(Helper::server_datetime_string_to_user_datetime_string($this->start_on));
    }

    public function user_start_on()
    {
        return Helper::server_datetime_string_to_user_datetime_string($this->start_on);
    }

    public function client_start_on()
    {
        return Helper::server_datetime_string_to_client_datetime_string($this->start_on);
    }

    public function start_on_datetime()
    {
        return Helper::server_datetime($this->start_on);
    }

    public function end_at_datetime()
    {
        return Helper::server_datetime($this->end_at);
    }

    public function user_start_on_timeday()
    {
        return Helper::server_datetime_to_user_timeday($this->start_on_datetime());
    }

    public function user_end_at_timeday()
    {
        return Helper::server_datetime_to_user_timeday($this->end_at_datetime());
    }

    public function duration_seconds()
    {
        return $this->end_at_datetime()->getTimestamp() - $this->start_on_datetime()->getTimestamp();
    }

    public function sweeper_file()
    {

        // if we have no sweeper files
        if (!$this->sweeper_files)
        {
            // initialize sweeper files
            $this->sweeper_files = Model_File::promos('Sweeper', $this->sweepers_album);
            // return first
            return current($this->sweeper_files);
        }

        // get next sweeper file or reset
        if ($next_sweeper_file = next($this->sweeper_files))
            return $next_sweeper_file;
        // reset
        return reset($this->sweeper_files);

    }

    public function bumper_file()
    {

        // if we have no bumper files
        if (!$this->bumper_files)
        {
            // initialize bumper files
            $this->bumper_files = Model_File::promos('Bumper', $this->bumpers_album);
            // return first
            return current($this->bumper_files);
        }

        // get next bumper file or reset
        if ($next_bumper_file = next($this->bumper_files))
            return $next_bumper_file;
        // reset
        return reset($this->bumper_files);

    }

    public static function the_current($server_datetime)
    {

        // get server time
        $server_datetime_string = Helper::server_datetime_string($server_datetime);
        // find the current schedule file
        // there may be more than one of the same file, find
        // the lowest id that has been played
        $current_schedule = Model_Schedule::query()
            ->related('show')
            ->related('show.users')
            ->where('available', '1')
            ->where('start_on', '<=', $server_datetime_string)
            ->where('end_at', '>', $server_datetime_string)
            ->get_one();
        // success
        return $current_schedule;

    }

    public static function the_next($server_datetime)
    {

        // get server time
        $server_datetime_string = Helper::server_datetime_string($server_datetime);
        // find the current schedule file
        // there may be more than one of the same file, find
        // the lowest id that has been played
        $next_schedules = Model_Schedule::query()
            ->related('show')
            ->where('available', '1')
            ->where('start_on', '>', $server_datetime_string)
            ->order_by('start_on', 'asc')
            ->rows_limit(1)
            ->get();
        // get first or return null
        return $next_schedules ? current($next_schedules) : null;

    }

    public static function remaining_seconds($id)
    {

        //////////////////
        // GET SCHEDULE //
        //////////////////

        $schedule = Model_Schedule::find($id);

        ///////////////////////////////
        // GET TALKOVER INPUT STATUS //
        ///////////////////////////////

        $talkover_input = Model_Input::query()
            ->where('name', 'talkover')
            ->get_one();

        /////////////////////////////////////
        // GATHER REMAINING SCHEDULE FILES //
        /////////////////////////////////////

        // get remaining file durations query for this schedule
        $remaining_schedule_files_query =
            Model_Schedule_File::query()
            ->related('file')
            ->where('schedule_id', $id)
            ->where('played_on', null)
            ->where('skipped', '0');

        // if takover is on, remove sweepers from durations
        if ($talkover_input->active())
            $remaining_schedule_files_query->where('files.genre', '!=', 'Sweeper');

        // get remaining schedule files
        $remaining_schedule_files = $remaining_schedule_files_query->get();

        /////////////////////////////////
        // CALCULATE REMAINING SECONDS //
        /////////////////////////////////

        $remaining_seconds = 0;
        $previous_remaining_schedule_file = null;
        // sum remaining durations the total seconds
        foreach ($remaining_schedule_files as $remaining_schedule_file)
        {
            // if we have no previous remaining schedule file, update previous file
            // else, get file to file combined duration, which factors in transition adjustments
            if ($previous_remaining_schedule_file == null)
                $previous_remaining_schedule_file = $remaining_schedule_file;
            else
                $remaining_seconds += $remaining_schedule_file->file->transitioned_duration_seconds($previous_remaining_schedule_file->file->genre);
        }

        // success
        return $remaining_seconds;

    }

    public function files()
    {
        $files = array();
        // gather all files from previous schedule
        foreach ($this->schedule_files as $schedule_file)
            $files[] = $schedule_file->file;
        // success
        return $files;
    }

    public function gather_files($previous_files)
    {

        //////////////////////////
        // NO BLOCK == NO FILES //
        //////////////////////////

        if ($this->show->block == null)
            return array();

        ///////////////////////////////////////
        // GET FILES FROM BLOCK FOR DURATION //
        ///////////////////////////////////////

        // set previous files
        $this->previous_files = $previous_files;
        // forward to gathering loop (and so the crazy begins)
        $this->show->block->gather_schedule_files($this);

    }

    public function gather_backup_files($seconds)
    {
        /////////////////////////////////
        // NO BLOCK == NO BACKUP FILES //
        /////////////////////////////////

        if ($this->show->block == null)
            return array();

        ////////////////////////////////////////
        // NO BACKUP BLOCK == NO BACKUP FILES //
        ////////////////////////////////////////

        if ($this->show->block->backup_block == null)
            return array();

        ///////////////////////////////////////
        // GET FILES FROM BLOCK FOR DURATION //
        ///////////////////////////////////////

        // set previous files to our existing files
        $this->previous_files = $this->files();
        // forward to gathering loop (and so the crazy begins)
        return $this->show->block->backup_block->gather_schedule_files($this, $seconds);

    }

    public function fill($previous_files)
    {

        // set schedule show files
        $this->gather_files($previous_files);
        // generate each schedule file
        foreach ($this->gathered_files as $gathered_file)
        {
            // add new schedule file to array
            $this->schedule_files[] =
                Model_Schedule_File::forge(array(
                    'schedule_id' => $this->id,
                    'file_id' => $gathered_file->id,
                    'ups' => 0,
                    'downs' => 0,
                    'queued' => '0',
                    'skipped' => '0'
                ));
        }

    }

    public function backup_fill($seconds)
    {

        // set backup files
        $this->gather_backup_files($seconds);
        // generate each schedule file
        foreach ($this->gathered_files as $gathered_file)
        {
            // add new schedule file to array
            $this->schedule_files[] =
                Model_Schedule_File::forge(array(
                    'schedule_id' => $this->id,
                    'file_id' => $gathered_file->id,
                    'ups' => 0,
                    'downs' => 0,
                    'queued' => '0',
                    'skipped' => '0'
                ));
        }

    }

    public function reset_queued()
    {
        DB::update('schedule_files')
            ->value('queued', '0')
            ->where('queued', '1')
            ->where('skipped', '0')
            ->where('played_on', null)
            ->where('schedule_id', $this->id)
            ->execute();
    }

}
