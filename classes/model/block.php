<?php

class Model_Block extends \Orm\Model
{

    protected static $_properties = array(
        'id',
        'harmonic',
        'title',
        'description',
        'file_query',
    );

    protected static $_has_many = array(
        'block_items',
        'shows',
        'child_block_items' => array(
            'key_from' => 'id',
            'model_to' => 'Model_Block_Item',
            'key_to' => 'child_block_id',
        ),
    );

    public static function all($except_id = null)
    {

        // base query
        $blocks = Model_Block::query()
            ->order_by('title', 'asc');
        // add except
        if ($except_id)
            $blocks = $blocks->where('id', '<>', $except_id);
        // get the blocks
        $blocks = $blocks->get();
        // success
        return array_values($blocks);

    }

    public static function search($query)
    {

        $blocks = Model_Block::query()
            ->select('title')
            ->where('title', 'LIKE', $query . '%')
            ->get();

        return Helper::extract_values('title', $blocks);

    }

    public static function edit($id)
    {

        // get block
        $block = Model_Block::query()
            ->where('id', $id)
            ->get_one();
        // success
        return $block;

    }

    public static function layout($id)
    {

        // get block items
        $block = Model_Block::query()
            ->related('block_items')
            ->related('block_items.file')
            ->related('block_items.child_block')
            ->where('id', $id)
            ->order_by('block_items.id', 'asc')
            ->get_one();
        // array value items
        $block->block_items = array_values($block->block_items);
        // success
        return $block;

    }

    public static function clear_items($block_id)
    {
        $query = DB::delete('block_items');
        $query->where('block_id', $block_id);
        $query->execute();
    }

    public function populate()
    {

        // set block from post data
        $this->title = Input::post('title');
        $this->description = Input::post('description');
        $this->file_query = Input::post('file_query');

    }

    public function populate_layout()
    {

        // get post data
        $child_block_ids = Input::post('child_block_ids');
        $file_ids = Input::post('file_ids');
        $percentages = Input::post('percentages');
        $durations = Input::post('durations');
        $titles = Input::post('titles', array());

        // loop over orders
        foreach ($titles as $i => $title)
        {
            // get item vars
            $file_id = isset($file_ids[$i]) ? $file_ids[$i] : null;
            $child_block_id = isset($child_block_ids[$i]) ? $child_block_ids[$i] : null;
            $percentage = $child_block_id && ($percentages[$i] != '') ? $percentages[$i] : null;
            $duration = $child_block_id && ($durations[$i] != '') ? $durations[$i] : null;
            // create item
            $block_item = Model_Block_Item::forge();
            // set item properties
            $block_item->block_id = $this->id;
            $block_item->child_block_id = $child_block_id;
            $block_item->file_id = $file_id;
            $block_item->percentage = $percentage;
            $block_item->duration = $duration;
            // add to block items
            $this->block_items[] = $block_item;
        }
        
    }

    public function files($seconds, &$total_filled_seconds, &$musical_key)
    {

        /////////////////////
        // GET BLOCK ITEMS //
        /////////////////////

        // id determines order
        $block_items = Model_Block_Item::query()
            ->related('file')
            ->related('child_block')
            ->where('block_id', $this->id)
            ->order_by('id', 'ASC')
            ->get();

        //////////////////////////////////////
        // NO BLOCK ITEMS, FALL TO CRITERIA //
        //////////////////////////////////////

        // if we have no items, we can use the
        // current block's criteria to generate
        // else, we need to do sub-block processing
        if (count($block_items) == 0)
        {
            // use criteria to return files
            return $this->criteria_files($seconds, $total_filled_seconds, $musical_key);
        }
        else
        {
            // use items to return files
            return $this->items_files($seconds, $block_items, $total_filled_seconds, $musical_key);
        }

    }

    private function criteria_files($seconds, &$total_filled_seconds, &$musical_key)
    {

        // total dateinterval consumed so far
        $filled_seconds = 0;
        // get all files that match the criteria
        $search_files = Model_File::search($this->file_query);
        // assume there is a 50% chance of a crossfade (because it is smart ;)
        $average_transition_seconds = 0.5 * (int)Model_Setting::get_value('transition_seconds');

        /////////////////////////////////////////////////
        // LOOP OVER EACH FILE UNTIL DURATION EXCEEDED //
        /////////////////////////////////////////////////

        // store sequential files added
        $files = array();
        // loop until show filled
        while (true)
        {
            /////////////////////////////////
            // VERIFY WE HAVE TIME & FILES //
            /////////////////////////////////

            // stop if the show is filled
            if ($filled_seconds >= $seconds)
                break;
            // stop if we are out of files
            if (count($search_files) == 0)
                break;

            ///////////////////////
            // ADD HARMONIC FILE //
            ///////////////////////

            // get harmonic file according to current musical key
            if ($this->harmonic == '1')
                $file = $this->get_harmonic_file($search_files, $musical_key);
            else
                $file = array_shift($search_files);

            // add to files array
            $files[] = $file;
            // update filled seconds
            $filled_seconds += $file->duration_seconds() - $average_transition_seconds;
        }

        /////////////
        // SUCCESS //
        /////////////

        // update master total
        $total_filled_seconds += $filled_seconds;
        // success
        return $files;

    }

    private function get_harmonic_file(&$search_files, &$musical_key)
    {
        // if we have no original key, return the first file
        if (!$musical_key)
        {
            // get first file
            $harmonic_file = array_shift($search_files);
            // update musical key
            $musical_key = $harmonic_file->musical_key;
            // success
            return $harmonic_file;
        }

        // get harmonic musical keys
        $harmonic_musical_keys = CamelotEasymixWheel::harmonic_musical_keys($musical_key);
        // shuffle musical keys
        shuffle($harmonic_musical_keys);

        // loop through each musical key option
        foreach ($harmonic_musical_keys as $harmonic_musical_key)
        {
            // loop through search files until we find a file that matches the current musical key
            foreach ($search_files as $search_file_key => $search_file)
            {
                // if we find the musical key, we are good
                if ($search_file->musical_key == $harmonic_musical_key)
                {
                    // remove the file from available
                    unset($search_files[$search_file_key]);
                    // update musical key
                    $musical_key = $search_file->musical_key;
                    // success
                    return $search_file;
                }
            }
        }

        // we failed to find a harmonically acceptable file :(
        // simply return the first search file
        // get first file
        $harmonic_file = array_shift($search_files);
        // update musical key
        $musical_key = $harmonic_file->musical_key;
        // success
        return $harmonic_file;
    }

    private function items_files($seconds, $block_items, &$total_filled_seconds, &$musical_key)
    {
        // assume there is a 50% chance of a crossfade (because it is smart ;)
        $average_transition_seconds = 0.5 * (int)Model_Setting::get_value('transition_seconds');

        //////////////////
        // CALCULATIONS //
        //////////////////

        // hold seconds allocated to duration
        $duration_seconds = 0;
        // calculate the total duration consumed by duration-based items
        foreach ($block_items as $block_item)
        {
            // get block item duration
            if ($block_item->duration != null)
                $duration_seconds += $block_item->duration_seconds();
            // get block file item duration
            if ($block_item->file != null)
                $duration_seconds += $block_item->file->duration_seconds() - $average_transition_seconds;
        }

        // now get the interval remainder for percentage-based items
        if ($duration_seconds >= $seconds)
            $percentage_seconds = 0;
        else
            $percentage_seconds = $seconds - $duration_seconds;

        /////////////////////////////
        // PROCESS EACH BLOCK ITEM //
        /////////////////////////////

        // total seconds used up
        $filled_seconds = 0;
        // create storage for sequential files
        $files = array();
        // loop over each block item
        foreach ($block_items as $block_item)
        {
            //////////////////////////////////
            // VERIFY TIME FOR ANOTHER ITEM //
            //////////////////////////////////

            if ($filled_seconds >= $seconds)
                break;

            // files are cake
            if ($block_item->file != null)
            {
                ////////////////////////
                // CHECK AVAILABILITY //
                ////////////////////////

                if (!$block_item->file->available)
                    continue;

                ///////////////////
                // FILES MOVE IN //
                ///////////////////

                // add file to array
                $files[] = $block_item->file;
                // update musical key
                $musical_key = $block_item->file->musical_key;
                // update filled dateinterval
                $filled_seconds += $block_item->file->duration_seconds() - $average_transition_seconds;
            }
            else
            {
                ///////////////////////////////
                // BLOCKS REQUIRE PROCESSING //
                ///////////////////////////////

                // calculate next item duration
                if ($block_item->duration != null)
                    $block_item_duration_seconds = $block_item->duration_seconds();
                else
                    $block_item_duration_seconds = Helper::percentage_seconds($percentage_seconds, $block_item->percentage);

                ///////////////////////
                // TRUNCATE OVERHANG //
                ///////////////////////

                // get remaining time
                $remaining_seconds = $seconds - $filled_seconds;
                // if the item duration > remaining, truncate
                if ($block_item_duration_seconds > $remaining_seconds)
                    $block_item_duration_seconds = $remaining_seconds;

                /////////////////////
                // ADD CHILD FILES //
                /////////////////////

                // keep track of total seconds for child block files from here
                $block_item_total_filled_seconds = 0;
                // merge next block files into end of current files array
                if ($block_item_duration_seconds > 0)
                    $files = array_merge($files, $block_item->child_block->files($block_item_duration_seconds, $block_item_total_filled_seconds, $musical_key));
                // update total seconds filled for this block
                $filled_seconds += $block_item_total_filled_seconds;
            }
        }

        ///////////////////////////////
        // ADDITIONAL CRITERIA FILES //
        ///////////////////////////////

        // get remaining time
        $remaining_seconds = $seconds - $filled_seconds;
        // if, after running through block items, we don't have our duration filled
        // fill the remainder with our criteria files
        if ($remaining_seconds > 0)
        {
            // keep track of total seconds for child block files from here
            $additional_total_filled_seconds = 0;
            // get more criteria files for this block
            $additional_files = $this->criteria_files($remaining_seconds, $additional_total_filled_seconds, $musical_key);
            // merge next block files into end of current files array
            $files = array_merge($files, $additional_files);
            // update total seconds filled for this block
            $filled_seconds += $additional_total_filled_seconds;
        }

        /////////////
        // SUCCESS //
        /////////////

        // update total filled seconds
        $total_filled_seconds += $filled_seconds;
        // success
        return $files;

    }

}
