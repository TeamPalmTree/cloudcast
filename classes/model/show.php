<?php

class Model_Show extends \Orm\Model
{

    protected static $_properties = array(
        'id',
        'start_on',
        'ups',
        'downs',
        'duration',
        'title',
        'description',
        'block_id',
    );

    protected static $_has_many = array(
        'schedules',
    );

    protected static $_many_many = array(
        'users',
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

    public static function relevant($server_datetime_string)
    {

        //////////////////////
        // GET SINGLE SHOWS //
        //////////////////////

        $single_shows = Model_Show::query()
            ->related('show_repeat')
            ->related('block')
            ->related('users')
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
            ->related('users')
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

    public static function edit($id)
    {

        // get show
        $show = Model_Show::query()
            ->related('show_repeat')
            ->related('block')
            ->related('users')
            ->where('id', $id)
            ->get_one();
        // set user vars
        $show->user_start_on = $show->user_start_on();
        if (isset($show->show_repeat->end_on))
            $show->show_repeat->user_end_on = $show->show_repeat->user_end_on_datetime_string();
        // success
        return $show;

    }

    public static function single($datetime)
    {

        return Model_Show::query()
            ->related('show_repeat')
            ->related('block')
            ->related('users')
            ->where('show_repeat.id', null)
            ->where('start_on', '>=', $datetime)
            ->order_by('start_on', 'asc')
            ->get();

    }

    public static function repeat($day, $datetime)
    {

        // get repeat shows for the given day
        $repeat_shows = Model_Show::query()
            ->related('show_repeat')
            ->related('block')
            ->related('users')
            ->where('show_repeat.' . $day, '1')
            ->and_where_open()
                ->where('show_repeat.end_on', null)
                ->or_where('show_repeat.end_on', '>=', $datetime)
            ->and_where_close()
            ->get();

        // sort by time of day, ignoring start date
        usort($repeat_shows, function($a, $b)
        {
            // remove the :
            $time_a = (int)str_replace(':', '', $a->user_start_on_time());
            $time_b = (int)str_replace(':', '', $b->user_start_on_time());
            // compare
            if ($time_a > $time_b)
                return 1;
            if ($time_a < $time_b)
                return -1;
            return 0;
        });

        // success
        return $repeat_shows;

    }

    public function populate()
    {

        // update show
        $this->start_on = Helper::user_datetime_string_to_server_datetime_string(Input::post('user_start_on'));
        $this->duration = Input::post('duration');
        $this->title = Input::post('title');
        $this->description = Input::post('description');

        // set ups/downs
        if (!$this->ups or !$this->downs)
        {
            $this->ups = 0;
            $this->downs = 0;
        }

        // add block
        if (Input::post('blocked'))
        {
            $this->block = Model_Block::find('first', array(
                'where' => array(
                    array('title', Input::post('block')),
                )
            ));
        }
        else
        {
            $this->block = null;
        }

        // add repeat
        if (Input::post('repeated'))
        {
            // get show repeat
            $this_repeat = Input::post('show_repeat', array());
            // create a repeat
            if ($this->show_repeat == null)
                $this->show_repeat = Model_Show_Repeat::forge();
            // set repeat params
            $this->show_repeat->Sunday = isset($this_repeat['Sunday']);
            $this->show_repeat->Monday = isset($this_repeat['Monday']);
            $this->show_repeat->Tuesday = isset($this_repeat['Tuesday']);
            $this->show_repeat->Wednesday = isset($this_repeat['Wednesday']);
            $this->show_repeat->Thursday = isset($this_repeat['Thursday']);
            $this->show_repeat->Friday = isset($this_repeat['Friday']);
            $this->show_repeat->Saturday = isset($this_repeat['Saturday']);

            // add repeat end
            if (Input::post('show_repeat[ends]'))
                $this->show_repeat->end_on = Helper::user_datetime_string_to_server_datetime_string(Input::post('show_repeat[user_end_on]'));
            else
                $this->show_repeat->end_on = null;

        }
        else
        {
            // delete repeat
            if ($this->show_repeat != null)
                $this->show_repeat->delete();
            $this->show_repeat = null;
        }

        // clear existing users
        $this->users = array();
        // replace hosts
        if (Input::post('hosted'))
        {
            // add new
            foreach (Input::post('users') as $user)
            {
                // get username
                $username = $user['username'];
                // if we have an empty one, ignore
                if ($username == '')
                    continue;
                // find and add user
                $this->users[] = Model_User::find('first', array(
                    'where' => array(
                        array('username', $username),
                    )
                ));
            }
        }

    }

    public function files()
    {

        //////////////////////////
        // NO BLOCK == NO FILES //
        //////////////////////////

        if ($this->block == null)
            return array();

        ///////////////////////////////////////
        // GET FILES FROM BLOCK FOR DURATION //
        ///////////////////////////////////////

        // forward to gathering loop (and so the crazy begins)
        return $this->block->files($this->duration_seconds(), null, $this->block);

    }

}
