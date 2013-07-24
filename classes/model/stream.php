<?php

class Model_Stream extends \Orm\Model
{

    protected static $_properties = array(
        'id',
        'name',
        'type',
        'active',
        'host',
        'port',
        'source_username',
        'source_password',
        'admin_username',
        'admin_password',
        'mount',
    );

    protected static $_has_many = array(
        'stream_statistics',
    );

    public static $types = array(
        '0' => 'Local',
        '1' => 'Icecast',
    );

    public function populate()
    {

        // set stream from post data
        $this->name = Input::post('name');
        $this->type = Input::post('type');
        $this->host = Input::post('host');
        $this->port = Input::post('port');
        $this->source_username = Input::post('source_username');
        $this->source_password = Input::post('source_password');
        $this->admin_username = Input::post('admin_username');
        $this->admin_password = Input::post('admin_password');
        $this->mount = Input::post('mount');

    }

    public static function edit($id)
    {

        // get stream
        $stream = Model_Stream::query()
            ->where('id', $id)
            ->get_one();
        // success
        return $stream;

    }

    public static function display()
    {

        // get all streams
        $streams = Model_Stream::query()
            ->select('name', 'active', 'type')
            ->order_by('name', 'asc')
            ->get();

        $display_streams = array();
        // move properties to parent
        foreach ($streams as $stream)
        {
            // create display stream
            $display_stream = array(
                'id' => $stream->id,
                'name' => $stream->name,
                'active' => $stream->active,
                'type_name' => self::$types[$stream->type],
            );
            // add to display streams
            $display_streams[] = $display_stream;
        }

        // success
        return $display_streams;

    }

}
