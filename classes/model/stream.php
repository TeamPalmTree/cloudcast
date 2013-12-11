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
        'format',
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
        'local',
        'icecast',
    );

    public function validate($input)
    {

        // create validation
        $validation = Validation::forge();
        $validation->add_field('name', 'Name', 'required');
        $validation->add_field('type', 'Type', 'required');
        // if we have a local type, we are done validating
        if (isset($input['type']) && ($input['type'] != 'local'))
        {
            $validation->add_field('host', 'Host', 'required');
            $validation->add_field('port', 'Port', 'required|numeric_min[1]|numeric_max[65535]');
            $validation->add_field('format', 'Format', 'required');
            $validation->add_field('source_username', 'Source Username', 'required');
            $validation->add_field('source_password', 'Source Password', 'required');
            $validation->add_field('admin_username', 'Admin Username', 'required');
            $validation->add_field('admin_password', 'Admin Password', 'required');
            $validation->add_field('mount', 'Mount', 'required');
        }

        // run validation
        if (!$validation->run($input)) return Helper::errors($validation);

    }

    public function populate($input)
    {

        // initialize
        if ($this->id == 0)
        {
            // set active
            $this->active = 1;
        }

        // set stream from post data
        $this->name = isset($input['name']) ? $input['name'] : null;
        $this->type = isset($input['type']) ? $input['type'] : null;
        $this->host = isset($input['host']) ? $input['host'] : null;
        $this->port = isset($input['port']) ? $input['port'] : null;
        $this->format = isset($input['format']) ? $input['format'] : null;
        $this->source_username = isset($input['source_username']) ? $input['source_username'] : null;
        $this->source_password = isset($input['source_password']) ? $input['source_password'] : null;
        $this->admin_username = isset($input['admin_username']) ? $input['admin_username'] : null;
        $this->admin_password = isset($input['admin_password']) ? $input['admin_password'] : null;
        $this->mount = isset($input['mount']) ? $input['mount'] : null;

    }

    public static function editable($id)
    {
        return Model_Stream::query()
            ->where('id', $id)
            ->get_one();
    }

    public static function viewable_creatable()
    {
        return Model_Stream::forge(array('type' => 'icecast'));
    }

    public static function active()
    {
        return Model_Stream::query()
            ->where('active', '1')
            ->get();
    }

    public static function viewable_active()
    {
        return Model_Stream::query()
            ->select('name', 'active', 'type', 'mount')
            ->where('active', '1')
            ->order_by('name', 'ASC')
            ->get();
    }

}
