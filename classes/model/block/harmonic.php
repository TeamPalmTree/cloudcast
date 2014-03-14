<?php

/**
 * Model for Block Harmonics
 *
 * A block harmonic assigns a block to a harmonic.
 * See the block model docs and harmonic docs for details.
 */
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
