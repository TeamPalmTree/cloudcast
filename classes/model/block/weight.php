<?php

class Model_Block_Weight extends \Orm\Model
{

    protected static $_properties = array(
        'id',
        'weight',
        'file_query',
        'block_id',
    );

    protected static $_belongs_to = array(
        'block',
    );

    public static function weighted($block_id)
    {

        // block weights for the specified block
        $block_weights = Model_Block_Weight::query()
            ->where('block_id', $block_id)
            ->get();

        $weighted_block_weights = array();
        // loop through block weights to index them by weight
        foreach ($block_weights as $block_weight)
            $weighted_block_weights[$block_weight->weight] = $block_weight;

        // success
        return $weighted_block_weights;

    }

}
