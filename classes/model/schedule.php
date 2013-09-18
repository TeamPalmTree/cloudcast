<?php

class Model_Schedule extends \Orm\Model
{

    protected static $_properties = array(
        'id',
        'start_on',
        'end_at',
        'available',
        'ups',
        'downs',
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

    public static function clear_files($schedule_id)
    {
        $query = DB::delete('schedule_files');
        $query->where('schedule_id', $schedule_id);
        $query->execute();
    }

}
