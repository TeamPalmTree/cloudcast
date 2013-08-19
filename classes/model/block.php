<?php

class Model_Block extends \Orm\Model
{

    protected static $_properties = array(
        'id',
        'harmonic_key',
        'harmonic_energy',
        'title',
        'description',
        'file_query',
    );

    protected static $_has_many = array(
        'block_items',
        'block_weights',
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
        // get titles from the blocks found
        return Helper::extract_values('title', $blocks);

    }

    public static function edit($id)
    {

        // get block
        $block = Model_Block::query()
            ->related('block_weights')
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
        // success
        return $block;

    }

    public static function clear_items($block_id)
    {
        $query = DB::delete('block_items');
        $query->where('block_id', $block_id);
        $query->execute();
    }

    public static function clear_weights($block_id)
    {
        $query = DB::delete('block_weights');
        $query->where('block_id', $block_id);
        $query->execute();
    }

    public function populate()
    {

        // set block from post data
        $this->title = Input::post('title');
        $this->description = Input::post('description');
        $this->harmonic_key = Input::post('harmonic_key') ? '1' : '0';
        $this->harmonic_energy = Input::post('harmonic_energy') ? '1' : '0';
        $this->file_query = Input::post('file_query');

        // add weights
        if (Input::post('weighted'))
        {
            // get block weights
            $block_weights = Input::post('block_weights');
            // add new
            foreach ($block_weights as $block_weight)
            {
                // create and add block weight
                $this->block_weights[] = Model_Block_Weight::forge(array(
                    'weight' => $block_weight['weight'],
                    'file_query' => $block_weight['file_query'],
                ));
            }
        }

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

    public function gather_files($seconds, &$gathered_files, &$total_filled_seconds, &$musical_key, &$energy)
    {

        ///////////////////////////////
        // GET BLOCK WEIGHTS & ITEMS //
        ///////////////////////////////

        // get block weights
        $block_weights = Model_Block_Weight::weighted($this->id);
        // get block items;
        // id determines order
        $block_items = Model_Block_Item::query()
            ->related('file')
            ->related('child_block')
            ->where('block_id', $this->id)
            ->order_by('id', 'ASC')
            ->get();

        /////////////////////////////
        // NO ITEMS, START QUERIES //
        /////////////////////////////

        // if we have no items, we can use the
        // current block's criteria to generate
        // else, we need to do sub-block processing
        if (count($block_items) == 0)
            $this->query_files($seconds, $block_weights, $gathered_files, $total_filled_seconds, $musical_key, $energy);
        else
            $this->items_files($seconds, $block_items, $block_weights, $gathered_files, $total_filled_seconds, $musical_key, $energy);

    }

    private function query_files($seconds, &$block_weights, &$gathered_files, &$total_filled_seconds, &$musical_key, &$energy)
    {

        // total dateinterval consumed so far
        $filled_seconds = 0;

        /////////////////////////////
        // OBTAIN SEARCH FILE SETS //
        /////////////////////////////

        // get weighted search files
        $weighted_files = $this->weighted_files($block_weights);

        /////////////////////////////////
        // LOOP UNTIL SECONDS EXCEEDED //
        /////////////////////////////////

        // loop until show filled
        while (true)
        {
            /////////////////////////////////////
            // VERIFY WE HAVE TIME & FILE SETS //
            /////////////////////////////////////

            // stop if the show is filled
            if ($filled_seconds >= $seconds)
                break;

            /////////////////////////
            // CLAIM WEIGHTED FILE //
            /////////////////////////

            // claim weighted file
            $claimed_file = $this->claim_weighted_file(
                $weighted_files,
                $gathered_files,
                $musical_key,
                $energy);
            // stop if we are unable to find files
            if (!$claimed_file)
                break;

            // update filled seconds with claimed file duration
            $filled_seconds += $claimed_file->duration_seconds();
        }

        /////////////
        // SUCCESS //
        /////////////

        // update master total
        $total_filled_seconds += $filled_seconds;

    }

    private function weighted_files(&$block_weights)
    {

        //////////////////////////
        // GET BASE QUERY FILES //
        //////////////////////////

        // get base query files
        $base_files = Model_File::search(
            $this->file_query, true, true, null, true, true);
        // start the weighted sets with base files
        $weighted_files = array($base_files);

        ////////////////////////////
        // NOW GET WEIGHTED FILES //
        ////////////////////////////

        // see if we have any weights defined
        if (count($block_weights) == 0)
            return $weighted_files;

        // append to the base query each weight
        foreach ($block_weights as $block_weight)
        {
            $weighted_files[$block_weight->weight] = Model_File::search(
                $this->file_query . "\n" .  $block_weight->file_query,
                true, true, null, true, true);
        }

        // success
        return $weighted_files;

    }

    private function claim_weighted_file(&$weighted_files, &$gathered_files, &$musical_key, &$energy)
    {

        /////////////////////////////////////
        // BASE SET RETURN WITH NO WEIGHTS //
        /////////////////////////////////////

        // if we have only the base files,
        // else get a random set based on weights
        if (count($weighted_files) == 1)
            return $this->claim_file($weighted_files[0], $gathered_files, $musical_key, $energy);

        /////////////////////////////////
        // GET RANDOM SEARCH FILES SET //
        /////////////////////////////////

        // get random files
        $random_files = $this->random_files($weighted_files);
        // attempt to claim from random files set
        $file = $this->claim_file($random_files, $gathered_files, $musical_key, $energy);
        // if that failed for whatever reason, claim from base set
        if (!$file)
            return $this->claim_file($weighted_files[0], $gathered_files, $musical_key, $energy);
        // else return something from the random set
        return $file;

    }

    private function random_files(&$weighted_files)
    {
        $random_files = null;
        $cumulative_weights_sum = 0;
        // first get the sum of all weights
        $weights_sum = array_sum(array_keys($weighted_files));
        // get a random number up to that sum
        $random_number = rand(1, $weights_sum);
        // loop over weighted sets
        foreach ($weighted_files as $weight => $files)
        {
            // add to weights sum
            $cumulative_weights_sum += $weight;
            // if the random number is less than or = to the cum weights
            // sum, we have found our set :)
            if ($random_number <= $cumulative_weights_sum)
                return $files;
        }

    }

    private function claim_file(&$files, &$gathered_files, &$musical_key, &$energy)
    {

        ///////////////////////////
        // REMOVE GATHERED FILES //
        ///////////////////////////

        // update the files array with files not already gathered
        $files = array_diff_key($files, $gathered_files);
        // verify we have some still, else we fail
        if (count($files) == 0)
            return null;
        // now create a duplicate array to track harmonic files
        $harmonic_files = $files;

        ///////////////////////////////////////////
        // ATTEMPT SET REDUCTION BY HARMONIC KEY //
        ///////////////////////////////////////////

        // attempt reduce file set by harmonic key
        if ($this->harmonic_key == '1')
            $harmonic_files = $this->harmonic_key_files($harmonic_files, $musical_key);

        //////////////////////////////////
        // SORT BY CLOSEST ENERGY LEVEL //
        //////////////////////////////////

        // sort files by closest energy
        if ($this->harmonic_energy == '1')
            $this->harmonic_energy_files($harmonic_files, $energy);

        ////////////////
        // CLAIM FILE //
        ////////////////

        // claim file
        $claimed_file = current($harmonic_files);
        unset($files[$claimed_file->id]);

        // update musical key & energy
        $musical_key = $claimed_file->musical_key;
        $energy = $claimed_file->energy;

        // add to gathered files
        $gathered_files[$claimed_file->id] = $claimed_file;

        // success
        return $claimed_file;

    }

    private function harmonic_key_files(&$files, &$musical_key)
    {
        // if we have no original key, keep files intact
        // this will give us a starting point and initiate the show
        if (!$musical_key)
            return $files;

        // keep track of harmonic files
        $harmonic_key_files = array();
        // get harmonic musical keys
        $harmonic_musical_keys = CamelotEasyMixWheel::harmonic_musical_keys($musical_key);
        // loop through search files until we find a file that matches the current musical key
        foreach ($files as $file)
        {
            // loop through each musical key option
            foreach ($harmonic_musical_keys as $harmonic_musical_key)
            {
                // if we find a musical key match, we are good
                if ($file->musical_key == $harmonic_musical_key)
                {
                    $harmonic_key_files[$file->id] = $file;
                    break;
                }
            }
        }

        // if we have no harmonic files, return original set
        if (count($harmonic_key_files) == 0)
            return $files;
        // success
        return $harmonic_key_files;
    }

    private function harmonic_energy_files(&$files, &$energy)
    {
        // if we have no original energy,
        // do no sorting and keep files as is
        if (!$energy)
            return;

        usort($files, function($a, $b) use ($energy)
        {
            // calculate the abs value difference between energy levels
            $a_energy_diff = (int)$a->energy - (int)$energy;
            $b_energy_diff = (int)$b->energy - (int)$energy;
            // compare
            if ($a_energy_diff > $b_energy_diff)
                return 1;
            if ($a_energy_diff < $b_energy_diff)
                return -1;
            return 0;
        });
    }

    private function items_files($seconds, &$block_items, &$block_weights, &$gathered_files, &$total_filled_seconds, &$musical_key, &$energy)
    {
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
                $duration_seconds += $block_item->file->duration_seconds();
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
                $gathered_files[] = $block_item->file;
                // update musical key & energy
                $musical_key = $block_item->file->musical_key;
                $energy = $block_item->file->energy;
                // update filled dateinterval
                $filled_seconds += $block_item->file->duration_seconds();
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
                    $block_item->child_block->gather_files($block_item_duration_seconds, $gathered_files, $block_item_total_filled_seconds, $musical_key, $energy);
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
            $this->query_files($remaining_seconds, $block_weights, $gathered_files, $additional_total_filled_seconds, $musical_key, $energy);
            // update total seconds filled for this block
            $filled_seconds += $additional_total_filled_seconds;
        }

        /////////////
        // SUCCESS //
        /////////////

        // update total filled seconds
        $total_filled_seconds += $filled_seconds;

    }

}
