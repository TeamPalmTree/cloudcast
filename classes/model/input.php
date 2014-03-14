<?php

class Model_Input extends \Orm\Model
{

    protected static $_properties = array(
        'name',
        'status',
        'enabled',
        'user_id',
    );

    protected static $_primary_key = array('name');

    public function active() { return (($this->status == '1') and ($this->enabled == '1')); }

    public static function mapped()
    {

        $mapped_inputs = array();
        // get all inputs
        $inputs = Model_Input::query()->get();
        // map settings
        foreach ($inputs as $input)
            $mapped_inputs[$input->name] = $input;
        // success
        return $mapped_inputs;

    }

}