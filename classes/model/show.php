<?php

class Model_Show extends \Orm\Model
{

    protected static $_properties = array(
        'id',
        'start_on',
        'available',
        'ups',
        'downs',
        'sweeper_interval',
        'duration',
        'title',
        'description',
        'sweepers_album',
        'jingles_album',
        'bumpers_album',
        'intros_album',
        'closers_album',
        'block_id',
    );

    protected static $_has_many = array(
        'schedules',
        'show_users'
    );

    protected static $_has_one = array(
        'show_repeat',
    );

    protected static $_belongs_to = array(
        'block',
    );

    public function start_on_datetime()
    {
        return Helper::server_datetime($this->start_on);
    }

    public function user_start_on()
    {
        return Helper::server_datetime_string_to_user_datetime_string($this->start_on);
    }

    public function user_start_on_datetime_string()
    {
        return Helper::datetime_string_date(Helper::server_datetime_string_to_user_datetime_string($this->start_on));
    }

    public function user_start_on_time()
    {
        return Helper::datetime_string_time(Helper::server_datetime_string_to_user_datetime_string($this->start_on));
    }

    public function user_start_on_timeday()
    {
        return Helper::server_datetime_to_user_timeday($this->start_on_datetime());
    }

    public function user_start_on_date()
    {
        return Helper::datetime_string_date(Helper::server_datetime_string_to_user_datetime_string($this->start_on));
    }

    public function user_end_at_time()
    {
        return Helper::datetime_string_time(
            Helper::server_datetime_to_user_datetime_string(
                Helper::datetime_add_duration($this->start_on_datetime(), $this->duration)
            )
        );
    }

    public function user_end_at_timeday()
    {
        return Helper::server_datetime_to_user_timeday(
                Helper::datetime_add_duration($this->start_on_datetime(), $this->duration)
            );
    }

    public function duration_seconds()
    {
        return Helper::duration_seconds($this->duration);
    }

    public static function validate($input)
    {

        // create validation
        $validation = Validation::forge();
        $validation->add_field('title', 'Title', 'required');
        $validation->add_field('duration', 'Duration', 'non_zero_duration');
        if (isset($input['block'])) $validation->add_field('block[title]', 'Block Title', 'required');
        if (isset($input['show_repeat'])) $validation->add_field('show_repeat', 'Show Repeat Day', 'day_checked');
        if (isset($input['show_repeat']['user_end_on'])) $validation->add_field('show_repeat[user_end_on]', 'Show Repeat End On', 'required');
        if (isset($input['sweepers_album'])) $validation->add_field('sweeper_interval', 'Sweeper Interval', 'required|numeric_min[1]');
        // validate show users
        foreach ($input['show_users'] as $show_user_index => $show_user)
            $validation->add_field("show_users[$show_user_index][user][username]", 'Show User Username', 'required');
        // run validation
        if (!$validation->run($input)) return Helper::errors($validation);

    }

    public function populate($input)
    {

        // initialize
        if ($this->id == 0)
        {
            // set up/down votes
            $this->ups = 0;
            $this->downs = 0;
            // set available
            $this->available = 1;
        }

        // update show
        $this->start_on = Helper::user_datetime_string_to_server_datetime_string($input['user_start_on']);
        $this->duration = $input['duration'];
        $this->title = $input['title'];
        $this->description = isset($input['description']) ? $input['description'] : null;

        // set promos albums
        $this->sweepers_album = isset($input['sweepers_album']) ? $input['sweepers_album'] : null;
        $this->jingles_album = isset($input['jingles_album']) ? $input['jingles_album'] : null;
        $this->bumpers_album = isset($input['bumpers_album']) ? $input['bumpers_album'] : null;
        $this->intros_album = isset($input['intros_album']) ? $input['intros_album'] : null;
        $this->closers_album = isset($input['closers_album']) ? $input['closers_album'] : null;
        // sweeper interval
        $this->sweeper_interval = isset($input['sweeper_interval']) ? $input['sweeper_interval'] : null;

        // add repeat
        if (isset($input['show_repeat']))
        {
            // get show repeat
            $show_repeat = $input['show_repeat'];
            // if we have an existing, update, else forge
            if ($this->show_repeat)
                $this->show_repeat->set($input['show_repeat']);
            else
                $this->show_repeat = Model_Show_Repeat::forge($show_repeat);
            // get user end on
            $user_end_on = $show_repeat['user_end_on'];
            // set the end on
            if ($user_end_on)
                $this->show_repeat->end_on = Helper::user_datetime_string_to_server_datetime_string($user_end_on);
        }
        else if ($this->show_repeat)
        {
            // clear out any existing repeat
            $this->show_repeat->delete();
            $this->show_repeat = null;
        }

        // delete existing show users
        foreach ($this->show_users as $show_user)
            $show_user->delete();
        // clear existing show users array
        $this->show_users = array();

        // get show users
        $show_users = $input['show_users'];
        // add show users
        foreach ($show_users as $show_user)
        {

            // find the user
            $user = Model_User::find('first', array(
                'where' => array(
                    array('username', $show_user['user']['username']),
                )
            ));

            // add to show users
            $this->show_users[] = Model_Show_User::forge(array(
                'user_id' => $user->id,
                'input_name' => $show_user['input_name']
            ));

        }

        // add block
        if (isset($input['block']))
        {
            // find block
            $this->block = Model_Block::find('first', array(
                'where' => array(
                    array('title', $input['block']['title']),
                )
            ));
        }
        else
        {
            // null out block reference
            $this->block = null;
        }

    }

    public function authenticate($user_id, $input_name)
    {

        // loop over all show users, trying to find one
        // assigned to this show with the input specified
        foreach ($this->show_users as $show_user)
        {
            if (($show_user->user_id == $user_id)
                && ($show_user->input_name == $input_name))
                return true;
        }

        // failed to auth
        return false;

    }

    public static function relevant($server_datetime_string)
    {

        //////////////////////
        // GET SINGLE SHOWS //
        //////////////////////

        $single_shows = Model_Show::query()
            ->related('show_repeat')
            ->related('block')
            ->related('show_users')
            ->where('available', '1')
            ->where('show_repeat.id', null)
            ->where('start_on', '>=', $server_datetime_string)
            ->get();
        // get values
        $single_shows = array_values($single_shows);

        //////////////////////
        // GET REPEAT SHOWS //
        //////////////////////

        // get repeat shows (will contain some that may expire in range)
        $repeat_shows = Model_Show::query()
            ->related('show_repeat')
            ->related('block')
            ->related('show_users')
            ->where('available', '1')
            ->where('show_repeat.id', '!=', null)
            ->and_where_open()
                ->where('show_repeat.end_on', null)
                ->or_where('show_repeat.end_on', '>=', $server_datetime_string)
            ->and_where_close()
            ->get();
        // get values
        $repeat_shows = array_values($repeat_shows);

        /////////////////
        // MERGE SHOWS //
        /////////////////

        // merge to get all future shows
        // single shows get priority over repeat shows
        return array_merge($single_shows, $repeat_shows);

    }

    public static function editable($id)
    {
        // get show
        return Model_Show::query()
            ->related('show_repeat')
            ->related('block')
            ->related('show_users')
            ->related('show_users.user')
            ->where('id', $id)
            ->get_one();
    }

    public static function viewable_editable($show)
    {

        // set user time vars
        $show->user_start_on = $show->user_start_on();
        if (isset($show->show_repeat->end_on))
            $show->show_repeat->user_end_on = $show->show_repeat->user_end_on_datetime_string();

        $new_show_users = array();
        // fix up show users
        foreach ($show->show_users as $show_user)
        {
            $new_show_user = new stdClass();
            $new_show_user->user = new stdClass();
            $new_show_user->user->username = $show_user->user->username;
            $new_show_user->input_name = $show_user->input_name;
            $new_show_users[] = $new_show_user;
        }

        // set new show users
        $show->show_users = $new_show_users;
        // success
        return $show;

    }

    public static function viewable_creatable()
    {

        // create show
        $show = Model_Show::forge();
        // get station name
        $station_name = Model_Setting::get_value('station_name');
        // set up some initial values
        $show->sweepers_album = $station_name;
        $show->jingles_album = $station_name;
        $show->bumpers_album = $station_name;
        $show->sweeper_interval = 2;
        $show->duration = '00:00:00';
        // success
        return $show;

    }

    public static function viewable_singles($server_datetime_string)
    {

        // get single shows
        $shows = Model_Show::query()
            ->related('show_repeat')
            ->related('block')
            ->related('show_users')
            ->where('available', '1')
            ->where('start_on', '>=', $server_datetime_string)
            ->where('show_repeat.id', null)
            ->order_by('start_on', 'asc')
            ->get();

        // set the user start/end timedays for each show
        foreach ($shows as &$show)
        {
            // set user times
            $show->user_start_on_timeday = $show->user_start_on_timeday();
            $show->user_end_at_timeday = $show->user_end_at_timeday();
            // set hosted
            if (count($show->show_users) > 0)
                $show->hosted = true;
            // clear users
            unset($show->show_users);
        }

        // success
        return array_values($shows);

    }

    public static function viewable_repeats($day, $server_datetime_string)
    {

        // get repeat shows for the given day
        $shows = Model_Show::query()
            ->related('show_repeat')
            ->related('block')
            ->related('show_users')
            ->where('available', '1')
            ->where('show_repeat.' . $day, '1')
            ->and_where_open()
                ->where('show_repeat.end_on', null)
                ->or_where('show_repeat.end_on', '>=', $server_datetime_string)
            ->and_where_close()
            ->get();

        // set the user start/end timedays for each show
        foreach ($shows as &$show)
        {
            // set user times
            $show->user_start_on_time = $show->user_start_on_time();
            $show->user_end_at_time = $show->user_end_at_time();
            // set hosted
            if (count($show->show_users) > 0)
                $show->hosted = true;
            // clear users
            unset($show->show_users);
        }

        // sort by time of day, ignoring start date
        usort($shows, function($a, $b)
        {
            // remove the :
            $time_a = (int)str_replace(':', '', $a->user_start_on_time);
            $time_b = (int)str_replace(':', '', $b->user_end_at_time);
            // compare
            if ($time_a > $time_b)
                return 1;
            if ($time_a < $time_b)
                return -1;
            return 0;
        });

        // success
        return $shows;

    }

}
