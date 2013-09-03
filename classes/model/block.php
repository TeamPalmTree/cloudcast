<?php

class Model_Block extends \Orm\Model
{

    protected $parent_block;
    protected $top_block;
    protected $gathered_files = array();
    protected $filled_seconds = 0;
    protected $current_harmonic_key;
    protected $current_harmonic_energy;
    protected $current_harmonic_genre;
    protected $current_separate_similar;
    protected $current_key;
    protected $current_energy;
    protected $current_genre;
    protected $weighted_block_weights;
    protected $ordered_block_items;

    protected static $_properties = array(
        'id',
        'harmonic_key',
        'harmonic_energy',
        'harmonic_genre',
        'separate_similar',
        'title',
        'description',
        'initial_key',
        'initial_energy',
        'initial_genre',
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

    public static $options = array(
        '0' => 'No',
        '1' => 'Yes',
        '2' => 'Inherit',
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
        $this->harmonic_key = Input::post('harmonic_key');
        $this->harmonic_energy = Input::post('harmonic_energy');
        $this->harmonic_genre = Input::post('harmonic_genre');
        $this->separate_similar = Input::post('separate_similar');
        $this->file_query = Input::post('file_query');

        // get initial key and energy
        $initial_key = Input::post('initial_key');
        $initial_energy = Input::post('initial_energy');
        $initial_genre = Input::post('initial_genre');
        // set initial key and energy
        $this->initial_key = (($this->harmonic_key == '0') || ($initial_key == '')) ? null : $initial_key;
        $this->initial_energy = (($this->harmonic_energy == '0') || ($initial_energy == '')) ? null : $initial_energy;
        $this->initial_genre = (($this->harmonic_genre == '0') || ($initial_genre == '')) ? null : $initial_genre;

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

    public function files($seconds, $parent_block, $top_block)
    {

        ////////////////////////
        // SET CURRENT VALUES //
        ////////////////////////

        $this->files_set($parent_block, $top_block);

        /////////////////////////////
        // NO ITEMS, START QUERIES //
        /////////////////////////////

        // if we have no items, we can use the
        // current block's criteria to generate
        // else, we need to do sub-block processing
        if (count($this->ordered_block_items) == 0)
            $this->query_files($seconds);
        else
            $this->items_files($seconds);
        // success
        return $this->gathered_files;

    }

    protected function files_set($parent_block, $top_block)
    {

        //////////////////////
        // SET PARENT & TOP //
        //////////////////////

        $this->parent_block = $parent_block;
        $this->top_block = $top_block;

        //////////////////////////////
        // SET OTHER CURRENT VALUES //
        //////////////////////////////

        // if we have no parent, we at the top
        if (!$parent_block)
        {

            // set current key
            if ($this->initial_key == null)
                $this->current_key = null;
            // set current energy
            if ($this->initial_energy == null)
                $this->current_energy = null;

            // set current harmonic genre
            if (($this->harmonic_genre == '0') or ($this->harmonic_genre == '2'))
                $this->current_harmonic_genre = '0';
            else
                $this->current_harmonic_genre = '1';

            // 1 (true) or 2 (inherited) are interpreted as true
            $this->current_harmonic_key = $this->harmonic_key == '0' ? '0' : '1';
            $this->current_harmonic_energy = $this->harmonic_energy == '0' ? '0' : '1';
            $this->current_separate_similar = $this->separate_similar == '0' ? '0' : '1';

        }
        else
        {

            // set current key
            if ($this->initial_key == null)
                $this->current_key = $parent_block->current_key;
            // set current energy
            if ($this->initial_energy == null)
                $this->current_energy = $parent_block->current_energy;

            // set current harmonic genre
            $this->current_harmonic_genre = $this->harmonic_genre == '2' ? $parent_block->current_harmonic_genre : $this->harmonic_genre;
            // pull values from our parent if we inherit, else from ourselves
            $this->current_harmonic_key = $this->harmonic_key == '2' ? $parent_block->current_harmonic_key : $this->harmonic_key;
            $this->current_harmonic_energy = $this->harmonic_energy == '2' ? $parent_block->current_harmonic_energy : $this->harmonic_energy;
            $this->current_separate_similar = $this->separate_similar == '2' ? $parent_block->current_separate_similar : $this->separate_similar;

        }

        ///////////////////////////////
        // SET BLOCK WEIGHTS & ITEMS //
        ///////////////////////////////

        // get block weights
        $this->weighted_block_weights = Model_Block_Weight::weighted($this->id);
        // get block items, id determines order
        $this->ordered_block_items = Model_Block_Item::query()
            ->related('file')
            ->related('child_block')
            ->where('block_id', $this->id)
            ->order_by('id', 'ASC')
            ->get();

    }

    protected function query_files($seconds)
    {

        // total dateinterval consumed so far
        $filled_seconds = 0;

        /////////////////////////////
        // OBTAIN SEARCH FILE SETS //
        /////////////////////////////

        // get weighted search files
        $weighted_files = $this->weighted_files();

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
            $claimed_file = $this->claim_weighted_file($weighted_files);
            // stop if we are unable to find files
            if (!$claimed_file)
                break;

            // update filled seconds with claimed file duration
            $filled_seconds += $claimed_file->duration_seconds();

        }

        /////////////
        // SUCCESS //
        /////////////

        // update filled seconds
        $this->fill_seconds($seconds);

    }

    protected function fill_seconds($seconds)
    {
        // set initial block to us
        $block = $this;
        // update vertically filled seconds
        while ($block != null)
        {
            $block->filled_seconds += $seconds;
            $block = $block->parent_block;
        }
    }

    protected function weighted_files()
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
        if (count($this->weighted_block_weights) == 0)
            return $weighted_files;

        // append to the base query each weight
        foreach ($this->weighted_block_weights as $block_weight)
        {
            $weighted_files[$block_weight->weight] = Model_File::search(
                $this->file_query . "\n" .  $block_weight->file_query,
                true, true, null, true, true);
        }

        // success
        return $weighted_files;

    }

    protected function claim_weighted_file(&$weighted_files)
    {

        /////////////////////////////////////
        // BASE SET RETURN WITH NO WEIGHTS //
        /////////////////////////////////////

        // if we have only the base files,
        // else get a random set based on weights
        if (count($weighted_files) == 1)
            return $this->claim_file($weighted_files[0]);

        /////////////////////////////////
        // GET RANDOM SEARCH FILES SET //
        /////////////////////////////////

        // get random files
        $random_files = $this->random_files($weighted_files);
        // attempt to claim from random files set
        $file = $this->claim_file($random_files);
        // if that failed for whatever reason, claim from base set
        if (!$file)
            return $this->claim_file($weighted_files[0]);
        // else return something from the random set
        return $file;

    }

    protected function random_files(&$weighted_files)
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

    protected function claim_file(&$files)
    {

        ///////////////////////////////
        // REMOVE PRE-GATHERED FILES //
        ///////////////////////////////

        // update the files array with files not already gathered
        $files = array_diff_key($files, $this->top_block->gathered_files);
        // verify we have some still, else we fail
        if (count($files) == 0)
            return null;
        // now create a duplicate array to track compatible files
        $compatible_files = $files;

        ///////////////////////////////////////////////////
        // ATTEMPT SET REDUCTION BY SIMILAR FILE REMOVAL //
        ///////////////////////////////////////////////////

        // attempt reduce file set by harmonic key
        if ($this->current_separate_similar == '1')
            $compatible_files = $this->separate_similar_files($compatible_files);

        /////////////////////////////////////////////
        // ATTEMPT SET REDUCTION BY HARMONIC GENRE //
        /////////////////////////////////////////////

        // attempt reduce file set by harmonic key
        if ($this->current_harmonic_genre == '1')
            $compatible_files = $this->harmonic_genre_files($compatible_files);

        ///////////////////////////////////////////
        // ATTEMPT SET REDUCTION BY HARMONIC KEY //
        ///////////////////////////////////////////

        // attempt reduce file set by harmonic key
        if ($this->current_harmonic_key == '1')
            $compatible_files = $this->harmonic_key_files($compatible_files);

        //////////////////////////////////
        // SORT BY CLOSEST ENERGY LEVEL //
        //////////////////////////////////

        // sort files by closest energy
        if ($this->current_harmonic_energy == '1')
            $this->harmonic_energy_files($compatible_files);

        /////////////////////////
        // CLAIM & GATHER FILE //
        /////////////////////////

        // claim file
        $claimed_file = current($compatible_files);
        unset($files[$claimed_file->id]);
        // gather claimed file
        $this->gather_file($claimed_file);
        // success
        return $claimed_file;

    }

    protected function gather_file($file)
    {
        // update musical key & energy
        $this->current_key = $file->key;
        $this->current_energy = $file->energy;
        $this->current_genre = $file->genre;
        // update top block gathered files
        $this->top_block->gathered_files[$file->id] = $file;
    }

    protected function harmonic_key_files(&$files)
    {

        // if we have no original key, keep files intact
        // this will give us a starting point and initiate the show
        if (!$this->current_key)
            return $files;

        // keep track of harmonic files
        $harmonic_key_files = array();
        // get harmonic musical keys
        $harmonic_keys = CamelotEasyMixWheel::harmonic_keys($this->current_key);
        // loop through search files until we find a file that matches the current musical key
        foreach ($files as $file)
        {
            // loop through each musical key option
            foreach ($harmonic_keys as $harmonic_key)
            {
                // if we find a musical key match, we are good
                if ($file->key == $harmonic_key)
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

    protected function harmonic_genre_files(&$files)
    {

        // if we have no original key, keep files intact
        // this will give us a starting point and initiate the show
        if (!$this->current_genre)
            return $files;

        // keep track of harmonic files
        $harmonic_genre_files = array();
        // loop through search files until we find a file that matches the current genre
        foreach ($files as $file)
        {
            // if we find a genre match, we are good
            if ($file->genre == $this->current_genre)
                $harmonic_genre_files[$file->id] = $file;
        }

        // if we have no harmonic files, return original set
        if (count($harmonic_genre_files) == 0)
            return $files;
        // success
        return $harmonic_genre_files;

    }

    protected function separate_similar_files(&$files)
    {

        // get the number songs to look backwards for similar files
        $similar_files_count = (int)Model_Setting::get_value('similar_files_count');
        // initially, get a slice of the array back the number of files to check for similarity
        $similar_gathered_files = array_slice($this->top_block->gathered_files, -1 * $similar_files_count, $similar_files_count, true);

        // keep track of different (un-similar) files
        $separate_similar_files = array();
        // loop through files making sure we don't have a similar one
        foreach ($files as $file)
        {
            // first split out the artists
            $file_scraped_artists = $file->scraped_artists();
            // now scrape the title
            $file_scraped_title = $file->scraped_title();

            // reset similar found
            $similar_found = false;
            // loop through all gathered files
            foreach ($similar_gathered_files as $similar_gathered_file)
            {

                // first split out the artists
                $similar_gathered_file_scraped_artists = $similar_gathered_file->scraped_artists();
                // now scrape the title
                $similar_gathered_file_scraped_title = $similar_gathered_file->scraped_title();

                // compute intersected artists
                $intersected_artists = array_intersect($file_scraped_artists, $similar_gathered_file_scraped_artists);
                // compare artists
                if (count($intersected_artists) > 0)
                {
                    $similar_found = true;
                    break;
                }

                // compare titles
                if ($file_scraped_title == $similar_gathered_file_scraped_title)
                {
                    $similar_found = true;
                    break;
                }

            }

            // after looking through the last X number of songs for similarity
            // make sure no similar found
            if (!$similar_found)
                $separate_similar_files[$file->id] = $file;

        }

        // if we have no harmonic files, return original set
        if (count($separate_similar_files) == 0)
            return $files;
        // success
        return $separate_similar_files;

    }

    protected function harmonic_energy_files(&$files)
    {

        // if we have no original energy,
        // do no sorting and keep files as is
        if (!$this->current_energy)
            return;

        // sort files by energy closeness
        usort($files, function($a, $b)
        {
            // calculate the abs value difference between energy levels
            $a_energy_diff = (int)$a->energy - (int)$this->current_energy;
            $b_energy_diff = (int)$b->energy - (int)$this->current_energy;
            // compare
            if ($a_energy_diff > $b_energy_diff)
                return 1;
            if ($a_energy_diff < $b_energy_diff)
                return -1;
            return 0;
        });

    }

    protected function items_files($seconds)
    {

        //////////////////
        // CALCULATIONS //
        //////////////////

        // hold seconds allocated to duration
        $duration_seconds = 0;
        // calculate the total duration consumed by duration-based items
        foreach ($this->ordered_block_items as $block_item)
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
        foreach ($this->ordered_block_items as $block_item)
        {
            //////////////////////////////////
            // VERIFY TIME FOR ANOTHER ITEM //
            //////////////////////////////////

            // see if we are over
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

                // gather item file
                $this->gather_file($block_item->file);
                // update filled date interval
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

                // merge next block files into end of current files array
                if ($block_item_duration_seconds > 0)
                    $block_item->child_block->files($block_item_duration_seconds, $this, $this->top_block);
                // update key and energy to lower block's current
                $this->current_key = $block_item->child_block->current_key;
                $this->current_energy = $block_item->child_block->current_energy;
                $this->current_genre = $block_item->child_block->current_genre;
                // update total seconds filled for this block
                $filled_seconds += $block_item->child_block->filled_seconds;
            }
        }

        //////////////////
        // FILL SECONDS //
        //////////////////

        // update total filled seconds
        $this->fill_seconds($filled_seconds);

        ///////////////////////////////
        // ADDITIONAL CRITERIA FILES //
        ///////////////////////////////

        // get remaining time
        $remaining_seconds = $seconds - $filled_seconds;
        // if, after running through block items, we don't have our duration filled
        // fill the remainder with our criteria files
        if ($remaining_seconds > 0)
            $this->query_files($remaining_seconds);

    }

}
