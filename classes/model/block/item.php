<?php

/**
 * Model for Block Items
 *
 * A block items assigns a block to another block or file.
 * See the block model docs and item/file docs for details.
 */
class Model_Block_Item extends \Orm\Model
{

    protected static $_properties = array(
        'id',
        'percentage',
        'duration',
        'block_id',
        'file_id',
        'child_block_id',
    );

    protected static $_belongs_to = array(
        'block',
        'file',
        'child_block' => array(
            'key_from' => 'child_block_id',
            'model_to' => 'Model_Block',
            'key_to' => 'id',
        ),
    );

    public function duration_seconds()
    {
        return Helper::duration_seconds($this->duration);
    }

}
