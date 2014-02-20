<?php

class Model_Schedule extends \Orm\Model
{

    // scheduling properties
    public $gathered_files = array();
    public $previous_files = array();
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

        ///////////////////
        // GET SCHEDULES //
        ///////////////////

        // get all schedules
        $db_schedules = DB::select(
                array('schedules.id', 'id'),
                array('schedules.start_on', 'start_on'),
                array('schedules.end_at', 'end_at'),
                array('shows.id', 'show_id'),
                array('shows.title', 'show_title'),
                array('shows.jingles_album', 'show_jingles_album'),
                array('shows.intros_album', 'show_intros_album'),
                array('shows.closers_album', 'show_closers_album')
            )->from('schedules')
            ->join('shows')->on('shows.id', '=', 'schedules.show_id')
            ->where('schedules.available', '1')
            ->where('schedules.end_at', '>', $server_datetime_string)
            ->order_by('schedules.start_on', 'ASC')
            ->as_object()->execute();

        // organize shows into dates
        $dates = array();
        // loop over db schedules
        foreach ($db_schedules as $db_schedule)
        {

            ///////////////////////////////////////
            // SETUP POTENTIAL NEW SCHEDULE DATE //
            ///////////////////////////////////////

            // get schedule start date
            $user_schedule_start_on_date = Helper::datetime_string_date(Helper::server_datetime_string_to_user_datetime_string($db_schedule->start_on));
            // if we don't have it yet, add it
            if (!array_key_exists($user_schedule_start_on_date, $dates))
            {
                // add to dates
                $dates[$user_schedule_start_on_date] = array(
                    'date' => $user_schedule_start_on_date,
                    'schedules' => array()
                );
            }

            //////////////////////////////////////
            // SETUP NEW SCHEDULE & SHOW OBJECT //
            //////////////////////////////////////

            // create new schedule
            $schedule = new stdClass();
            // set schedule properties
            $schedule->id = $db_schedule->id;
            $schedule->user_start_on_timeday = Helper::server_datetime_to_user_timeday(Helper::server_datetime($db_schedule->start_on));
            $schedule->user_end_at_timeday = Helper::server_datetime_to_user_timeday(Helper::server_datetime($db_schedule->end_at));
            // create new show
            $schedule->show = new stdClass();
            // set show properties
            $schedule->show->title = $db_schedule->show_title;
            $schedule->show->jingles_album = $db_schedule->show_jingles_album;
            $schedule->show->intros_album = $db_schedule->show_intros_album;
            $schedule->show->closers_album = $db_schedule->show_closers_album;
            $schedule->show->hosted = Model_Show_User::query()->where('show_id', $db_schedule->show_id)->count() > 0;

            /////////////////////////////////////
            // GET SCHEDULE FILES FOR SCHEDULE //
            /////////////////////////////////////

            // get all schedule files for this schedule
            $db_schedule_files = DB::select(
                    array('schedule_files.id', 'id'),
                    array('schedule_files.played_on', 'played_on'),
                    array('schedule_files.queued_on', 'queued_on'),
                    array('schedule_files.skipped_on', 'skipped_on'),
                    array('files.id', 'file_id'),
                    array('files.artist', 'file_artist'),
                    array('files.title', 'file_title'),
                    array('files.genre', 'file_genre'),
                    array('files.duration', 'file_duration'),
                    array('files.post', 'file_post'),
                    array('files.key', 'file_key'),
                    array('files.energy', 'file_energy')
                )->from('schedule_files')
                ->join('files')->on('files.id', '=', 'schedule_files.file_id')
                ->where('schedule_files.schedule_id', $schedule->id)
                ->as_object()->execute();

            // set array in schedule
            $schedule->schedule_files = array();
            // loop over db schedule files
            foreach ($db_schedule_files as $db_schedule_file)
            {

                ////////////////////////////////////
                // SETUP NEW SCHEDULE FILE OBJECT //
                ////////////////////////////////////

                // create new schedule file
                $schedule_file = new stdClass();
                // set schedule file properties
                $schedule_file->id = $db_schedule_file->id;
                $schedule_file->played_on = $db_schedule_file->played_on;
                $schedule_file->queued_on = $db_schedule_file->queued_on;
                $schedule_file->skipped_on = $db_schedule_file->skipped_on;
                // create new file
                $schedule_file->file = new stdClass();
                // set file properties
                $schedule_file->file->id = $db_schedule_file->file_id;
                $schedule_file->file->artist = $db_schedule_file->file_artist;
                $schedule_file->file->title = $db_schedule_file->file_title;
                $schedule_file->file->genre = $db_schedule_file->file_genre;
                $schedule_file->file->duration = $db_schedule_file->file_duration;
                $schedule_file->file->post = $db_schedule_file->file_post;
                $schedule_file->file->key = $db_schedule_file->file_key;
                $schedule_file->file->energy = $db_schedule_file->file_energy;
                // add to schedule files array
                $schedule->schedule_files[] = $schedule_file;

            }

            // add schedule to current date
            $dates[$user_schedule_start_on_date]['schedules'][] = $schedule;
        }

        // success
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

    public function last_file()
    {

        // see if we have gathered files
        $last_gathered_file = end($this->gathered_files);
        // verify
        if ($last_gathered_file)
            return $last_gathered_file;
        // if not, use previous files
        return end($this->previous_files);

    }

    public function sweeper_file()
    {

        // if we have no sweeper files
        if (!$this->sweeper_files)
        {
            // initialize sweeper files
            $this->sweeper_files = Model_File::promos('Sweeper', $this->show->sweepers_album);
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
            $this->bumper_files = Model_File::promos('Bumper', $this->show->bumpers_album);
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
            ->order_by('start_on', 'ASC')
            ->rows_limit(1)
            ->get();
        // get first or return null
        return $next_schedules ? current($next_schedules) : null;

    }

    public static function authorization($server_datetime)
    {
        // get server time
        $server_datetime_string = Helper::server_datetime_string($server_datetime);
        // get the current schedule + users
        return Model_Schedule::query()
            ->related('show')
            ->related('show.show_users')
            ->where('available', '1')
            ->where('start_on', '<=', $server_datetime_string)
            ->where('end_at', '>', $server_datetime_string)
            ->get_one();
    }

    public static function remaining_seconds($current_schedule_file)
    {

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
            ->where('schedule_id', $current_schedule_file->schedule_id)
            ->where('played_on', null)
            ->where('skipped_on', null);

        // if takover is on, remove sweepers from durations
        if ($talkover_input->active())
            $remaining_schedule_files_query->where('file.genre', '!=', 'Sweeper');

        // get remaining schedule files
        $remaining_schedule_files = $remaining_schedule_files_query->get();

        /////////////////////////////////
        // CALCULATE REMAINING SECONDS //
        /////////////////////////////////

        $remaining_seconds = 0;
        // set previous file to the current schedule file
        $previous_remaining_schedule_file = $current_schedule_file;
        // sum remaining durations the total seconds
        foreach ($remaining_schedule_files as $remaining_schedule_file)
        {
            // sum up remaining seconds factoring in transitions
            $remaining_seconds +=
                $remaining_schedule_file->file->transitioned_duration_seconds($previous_remaining_schedule_file->file->genre);
            // update previous remaining schedule file
            $previous_remaining_schedule_file = $remaining_schedule_file;
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
                ));
        }

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
                ));
        }

    }

    public function reset_queued()
    {
        DB::update('schedule_files')
            ->value('queued_on', null)
            ->where('queued_on', '!=', null)
            ->where('skipped_on', null)
            ->where('played_on', null)
            ->where('schedule_id', $this->id)
            ->execute();
    }

}
