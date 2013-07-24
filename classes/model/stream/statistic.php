<?php

class Model_Stream_Statistic extends \Orm\Model
{

    protected static $_properties = array(
        'id',
        'captured_on',
        'listeners',
        'schedule_file_id',
        'stream_id',
    );

    protected static $_belongs_to = array(
        'stream',
        'schedule_file',
    );

}
