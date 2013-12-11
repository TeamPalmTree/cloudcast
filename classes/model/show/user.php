<?php

class Model_Show_User extends \Orm\Model
{

    protected static $_properties = array(
        'id',
        'input_name',
        'user_id',
        'show_id'
    );

    protected static $_belongs_to = array(
        'show',
        'user',
    );

}
