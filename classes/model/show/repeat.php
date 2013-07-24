<?php

class Model_Show_Repeat extends \Orm\Model
{

    protected static $_properties = array(
        'id',
        'end_on',
        'Sunday',
        'Monday',
        'Tuesday',
        'Wednesday',
        'Thursday',
        'Friday',
        'Saturday',
        'show_id'
    );

    protected static $_belongs_to = array(
        'show',
    );

    public function user_end_on_datetime_string()
    {
        return Helper::server_datetime_string_to_user_datetime_string($this->end_on);
    }

    public function end_on_datetime()
    {
        if ($this->end_on == null)
            return null;
        return Helper::server_datetime($this->end_on);
    }

}
