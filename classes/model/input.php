<?php

class Model_Input extends \Orm\Model
{

    protected static $_properties = array(
        'name',
        'status',
        'enabled',
        'user_id',
    );

    protected static $_belongs_to = array(
        'user',
    );

    protected static $_primary_key = array('name');

    public static function mapped()
    {

        $mapped_inputs = array();
        // get all inputs
        $inputs = Model_Input::query()
            ->related('user')
            ->get();
        // map settings
        foreach ($inputs as $input)
            $mapped_inputs[$input->name] = $input;
        // success
        return $mapped_inputs;

    }

}