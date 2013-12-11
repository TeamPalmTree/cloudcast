<?php

class Model_Block_Harmonic extends \Orm\Model
{

    protected static $_properties = array(
        'id',
        'harmonic_name',
        'block_id'
    );

    protected static $_belongs_to = array(
        'block',
    );

}
