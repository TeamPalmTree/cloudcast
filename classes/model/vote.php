<?php

class Model_Vote extends \Orm\Model
{

    protected static $_properties = array(
        'id',
        'vote_cast',
        'vote',
        'ip_address',
        'schedule_file_id'
    );

    public static function same($ip_address, $schedule_file_id)
    {

        // get the last vote for this same schedule file and IP
        return Model_Vote::query()
            ->where('ip_address', $ip_address)
            ->where('schedule_file_id', $schedule_file_id)
            ->get_one();

    }

}
