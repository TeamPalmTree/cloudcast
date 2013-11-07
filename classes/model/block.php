<?php

class Model_Block extends \Orm\Model
{

    // standard properties
    public $parent_block;
    public $top_block;
    public $filled_seconds;
    public $weighted_block_weights;
    public $ordered_block_items;
    // inheritable properties
    public $current_harmonic_key;
    public $current_harmonic_energy;
    public $current_harmonic_genre;
    public $current_separate_similar;
    // current properties
    public $current_key;
    public $current_energy;
    public $current_genre;
    // the current schedule
    public $schedule;

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
        'backup_block_id',
    );

    protected static $_belongs_to = array(
        'backup_block' => array(
            'key_from' => 'backup_block_id',
            'model_to' => 'Model_Block',
            'key_to' => 'id',
        ),
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
            ->related('backup_block')
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

        // add block
        if (Input::post('backup_blocked'))
        {
            $this->backup_block = Model_Block::find('first', array(
                'where' => array(
                    array('title', Input::post('backup_block')),
                )
            ));
        }
        else
        {
            $this->backup_block = null;
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

    public function gather_schedule_files($schedule, $seconds = null)
    {

        ////////////////////
        // SET PROPERTIES //
        ////////////////////

        // set schedule
        $this->schedule = $schedule;

        // set top and parent
        $this->parent_block = null;
        $this->top_block = $this;

        // set seconds
        if ($seconds == null)
            $seconds = $schedule->duration_seconds();

        // get previous file
        $previous_file = end($schedule->previous_files);
        // the current value will either be the initial value for this block, the last previous file's value, or null
        $this->current_key = $this->initial_key ? $this->initial_key : ($previous_file ? $previous_file->key : null);
        $this->current_energy = $this->initial_energy ? $this->initial_energy : ($previous_file ? $previous_file->energy : null);
        $this->current_genre = $this->initial_genre ? $this->initial_genre : ($previous_file ? $previous_file->genre : null);

        // set current harmonic genre
        if (($this->harmonic_genre == '0') or ($this->harmonic_genre == '2'))
            $this->current_harmonic_genre = '0';
        else
            $this->current_harmonic_genre = '1';

        // 1 (true) or 2 (inherited) are interpreted as true
        $this->current_harmonic_key = $this->harmonic_key == '0' ? '0' : '1';
        $this->current_harmonic_energy = $this->harmonic_energy == '0' ? '0' : '1';
        $this->current_separate_similar = $this->separate_similar == '0' ? '0' : '1';

        ///////////////////////////////
        // FORWARD TO ALL PROCESSING //
        ///////////////////////////////

        // forward
        $this->gather_files($seconds);

    }

    protected function gather_files($seconds)
    {

        //////////////////////
        // RESET PROPERTIES //
        //////////////////////

        $this->filled_seconds = 0;

        ///////////////////////////////
        // SET BLOCK WEIGHTS & ITEMS //
        ///////////////////////////////

        // get block weights
        $this->weighted_block_weights = Model_Block_Weight::weighted($this->id);
        // get block items, id determines order
        $this->ordered_block_items = Model_Block_Item::query()
            ->related('file')
            ->related('child_block')
            ->related('child_block.backup_block')
            ->where('block_id', $this->id)
            ->order_by('id', 'ASC')
            ->get();

        /////////////////////////////
        // NO ITEMS, START QUERIES //
        /////////////////////////////

        // if we have no items, we can use the
        // current block's criteria to generate
        // else, we need to do sub-block processing
        if (count($this->ordered_block_items) == 0)
            $this->gather_weighted_files($seconds);
        else
            $this->gather_items_files($seconds);

    }

    protected function gather_weighted_files($seconds)
    {

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

            /////////////////////////
            // VERIFY WE HAVE TIME //
            /////////////////////////

            // stop if the show is filled
            if ($this->filled_seconds >= $seconds)
                break;

            //////////////////
            // GATHER FILES //
            //////////////////

            // then attempt to gather a weighted file
            if (!$this->gather_weighted_file($weighted_files))
            {
                // if weighted gather fails, use the backup block
                $this->gather_backup_files($seconds);
                break;
            }

        }

    }

    protected function weighted_files()
    {

        //////////////////////////
        // GET BASE QUERY FILES //
        //////////////////////////

        // get base query files
        $base_files = Model_File::search($this->file_query, true, null, true, true);
        // if we have no base files, return an empty array
        if (count($base_files) == 0)
            return array();

        ////////////////////////////
        // NOW GET WEIGHTED FILES //
        ////////////////////////////

        // start the weighted sets with base files
        $weighted_files = array($base_files);
        // see if we have any weights defined
        if (count($this->weighted_block_weights) == 0)
            return $weighted_files;

        // append to the base query each weight
        foreach ($this->weighted_block_weights as $block_weight)
        {
            // get block weight files
            $block_weight_files = Model_File::search(
                $this->file_query . "\n" .  $block_weight->file_query,
                true, null, true, true);
            // only add weight files if we have some
            if (count($block_weight_files) > 0)
                $weighted_files[$block_weight->weight] = $block_weight_files;
        }

        // sort weights numerically
        ksort($weighted_files, SORT_NUMERIC);
        // success
        return $weighted_files;

    }

    protected function gather_weighted_file(&$weighted_files)
    {

        /////////////////////////
        // CLAIM WEIGHTED FILE //
        /////////////////////////

        // claim weighted file
        $file = $this->claim_weighted_file($weighted_files);
        // verify successful
        if (!$file)
            return false;

        ///////////////////////
        // GATHER PROMO FILE //
        ///////////////////////

        // gather promo file
        $this->gather_promo_file($file);

        /////////////////
        // GATHER FILE //
        /////////////////

        // gather file
        $this->gather_file($file);
        // success
        return true;

    }

    protected function random_weighted_files(&$weighted_files, &$random_weight)
    {

        // if we only have one, return it
        if (count($weighted_files) == 1)
        {
            // get first array element
            $random_files = reset($weighted_files);
            // set random weight
            $random_weight = key($weighted_files);
            // success
            return $random_files;
        }

        $random_files = null;
        $cumulative_weights_sum = 0;
        // first get the sum of all weights
        $weights_sum = array_sum(array_keys($weighted_files));
        // get a random number up to that sum
        $random_number = rand(1, $weights_sum);
        // loop over weighted sets
        foreach ($weighted_files as $weight => &$files)
        {
            // add to weights sum
            $cumulative_weights_sum += $weight;
            // if the random number is less than or = to the cum weights
            // sum, we have found our set :)
            if ($random_number <= $cumulative_weights_sum)
            {
                // set random weight
                $random_weight = $weight;
                // success
                return $files;
            }
        }

    }

    protected function claim_weighted_file(&$weighted_files)
    {

        //////////////////////////////
        // VERIFY WE HAVE FILE SETS //
        //////////////////////////////

        // if we have no file sets
        if (count($weighted_files) == 0)
            return null;

        /////////////////////////////////
        // GET RANDOM SEARCH FILES SET //
        /////////////////////////////////

        $current_weight = null;
        // get random files
        $source_files = $this->random_weighted_files($weighted_files, $current_weight);

        //////////////////////////
        // FIND COMPATIBLE FILE //
        //////////////////////////

        // attempt to choose from random files set
        $file = $this->find_compatible_file($source_files);
        // if we have a file, we are done
        if ($file)
            return $file;

        //////////////////////////////////////////
        // WALK DOWN WEIGHTS TO CONTINUE SEARCH //
        //////////////////////////////////////////

        // get weighted files weights
        $weights = array_keys($weighted_files);
        // get the index of the random weight
        $weights_index = array_search($current_weight, $weights);
        // loop while we have lower weights
        while ($weights_index >= 0)
        {
            // get the next weight to check
            $current_weight = $weights[$weights_index];
            // get the weighted file set at this weight
            $source_files = $weighted_files[$current_weight];
            // attempt to choose from lower weighted files set
            $file = $this->find_compatible_file($source_files);
            // did we find a file
            if ($file)
                return $file;

            // lower the weights index
            $weights_index--;
        }

        // failed for all sets
        return null;

    }

    protected function find_compatible_file($files)
    {

        ///////////////////////////////
        // REMOVE LAST FILE FROM SET //
        ///////////////////////////////

        // get previous file
        $previous_file = $this->top_block->schedule->previous_file;
        // if we have one, remove it from this set
        if ($previous_file)
            unset($files[$previous_file->id]);

        ////////////////////////////////
        // MAP COMPATIBLE FILES ARRAY //
        ////////////////////////////////

        // add a weight to each file
        $compatibles = array_map(
            function($file)
            {
                return array(
                    'file' => $file,
                    'score' => 0
                );
            },
            $files
        );

        //////////////////////////////
        // COMPATIBILITY REDUCTIONS //
        //////////////////////////////

        // attempt reduce file set by harmonic key
        if ($this->current_separate_similar == '1')
            $compatibles = $this->separate_similar_compatibles_reduction($compatibles);

        // attempt reduce file set by harmonic key
        if ($this->current_harmonic_key == '1')
            $compatibles = $this->harmonic_key_compatibles_reduction($compatibles);

        ///////////////////////////
        // COMPATIBILITY SCORING //
        ///////////////////////////

        // score genre compatibility
        if ($this->current_harmonic_genre == '1')
            $this->harmonic_genre_compatibles_scoring($compatibles);

        // score energy compatibility
        if ($this->current_harmonic_energy == '1')
            $this->harmonic_energy_compatibles_scoring($compatibles);

        ///////////////////////////
        // SORT BY COMPATIBILITY //
        ///////////////////////////

        // sort files by energy closeness
        usort($compatibles, function($a, $b)
        {
            // compare
            if ($a['score'] < $b['score'])
                return 1;
            if ($a['score'] > $b['score'])
                return -1;
            return 0;
        });

        ////////////////////////////
        // RETURN COMPATIBLE FILE //
        ////////////////////////////

        // choose first file
        $compatible = current($compatibles);
        // success
        return $compatible['file'];

    }

    protected function gather_promo_file($next_file)
    {

        // get schedule
        $schedule = $this->top_block->schedule;
        // if we have no previous file, we cannot do promos
        if (!$schedule->previous_file)
            return false;

        // get current/next genre
        $genre = $schedule->previous_file->genre;
        $next_genre = $next_file->genre;

        //////////////////
        // BUMPER CHECK //
        //////////////////

        if (($genre == 'Ad') && ($next_genre != 'Ad') && ($next_genre != 'Intro'))
        {
            // get bumper file
            $bumper_file = $schedule->bumper_file();
            // verify we have one, gather
            if ($bumper_file)
            {
                $this->gather_file($bumper_file);
                return true;
            }
        }

        ////////////////////
        // INSERT SWEEPER //
        ////////////////////

        if (($genre != 'Ad') && ($genre != 'Intro') && ($next_genre != 'Ad') && ($next_genre != 'Intro'))
        {
            // if we have gone through enough file since last sweeper
            if ($schedule->sweeper_files_count >= $schedule->sweeper_interval)
            {
                // get sweeper file
                $sweeper_file = $schedule->sweeper_file();
                // verify we have one, gather
                if ($sweeper_file)
                {
                    // insert another sweeper
                    $this->gather_file($sweeper_file);
                    // reset sweeper files count
                    $schedule->sweeper_files_count = 0;
                    return true;
                }
            }
        }

        // no promo here
        return false;

    }

    protected function gather_file($file)
    {

        //

        //////////////////////////////////
        // CALCULATE ADDITIONAL SECONDS //
        //////////////////////////////////

        // get schedule
        $schedule = $this->top_block->schedule;
        // update filled seconds with transitioned duration
        $additional_seconds = $file->transitioned_duration_seconds($schedule->previous_file);

        /////////////////////////////////
        // UPDATE TOP BLOCK PROPERTIES //
        /////////////////////////////////

        // update previous file
        $schedule->previous_file = $file;
        // update top block gathered files
        $schedule->gathered_files[] = $file;
        // update sweeper files count
        $schedule->sweeper_files_count++;

        ///////////////////////////////
        // UPDATE PROPERTIES UPWARDS //
        ///////////////////////////////

        // set initial block to us
        $block = $this;
        // update vertically filled seconds
        while ($block != null)
        {

            // add additional seconds to filled
            $block->filled_seconds += $additional_seconds;
            // update current key, energy, genre (if we have a valid new value)
            if ($file->key)
                $block->current_key = $file->key;
            if ($file->energy)
                $block->current_energy = $file->energy;
            $block->current_genre = $file->genre;
            // move upwards
            $block = $block->parent_block;

        }

    }

    protected function separate_similar_compatibles_reduction(&$compatibles)
    {

        ///////////////////////////////////
        // GENERATE PREVIOUS FILES ARRAY //
        ///////////////////////////////////

        // get schedule
        $schedule = $this->top_block->schedule;
        // get the number songs to look backwards for similar files
        $similar_files_count = (int)Model_Setting::get_value('similar_files_count');
        // get all potential previous files to check
        $previous_files = array_merge($schedule->previous_files, $schedule->gathered_files);
        // verify we have any
        if (count($previous_files) == 0)
            return $compatibles;

        //////////////////////////////////////////////////
        // WEED OUT SIMILAR FILES FROM COMPATIBLES LIST //
        //////////////////////////////////////////////////

        // keep track of different (un-similar) files
        $separate_similar_compatibles = array();
        // loop through files making sure we don't have a similar one
        foreach ($compatibles as &$compatible)
        {

            ///////////////////////////////////////////////////
            // GET COMPATIBLE FILE AND COMPARISON PARAMETERS //
            ///////////////////////////////////////////////////

            // get compatible file
            $compatible_file = $compatible['file'];
            // first split out the artists
            $compatible_file_scraped_artists = $compatible_file->scraped_artists();
            // now scrape the title
            $compatible_file_scraped_title = $compatible_file->scraped_title();

            //////////////////////////////////////////////////////////////
            // GO BACKWARDS IN PREVIOUS FILES CHECKING FOR SIMILAR FILE //
            //////////////////////////////////////////////////////////////

            // reset similar found
            $similar_found = false;
            // reset similar files index
            $previous_files_count = 1;
            // set previous files pointer to end
            $previous_file = end($previous_files);
            // loop through all previous files
            do
            {

                //////////////////////////
                // VERIFY PREVIOUS FILE //
                //////////////////////////

                // verify not sweeper/bumper
                if (($previous_file->genre == 'Sweeper') or ($previous_file->genre == 'Bumper'))
                    continue;

                //////////////////////////////
                // SIMILAR ARTIST DETECTION //
                //////////////////////////////

                // first split out the artists
                $previous_file_scraped_artists = $previous_file->scraped_artists();
                // compute intersected artists
                $intersected_artists = array_intersect($compatible_file_scraped_artists, $previous_file_scraped_artists);
                // compare artists
                if (count($intersected_artists) > 0)
                {
                    $similar_found = true;
                    break;
                }

                /////////////////////////////
                // SIMILAR TITLE DETECTION //
                /////////////////////////////

                // now scrape the title
                $previous_file_scraped_title = $previous_file->scraped_title();
                // compare titles
                if ($compatible_file_scraped_title == $previous_file_scraped_title)
                {
                    $similar_found = true;
                    break;
                }

                /////////////////////////////////////////
                // VERIFY WE HAVEN'T GONE TOO FAR BACK //
                /////////////////////////////////////////

                // verify we have not exceeded similar files count (gone too far)
                if ($previous_files_count == $similar_files_count)
                    break;
                // increment previous files count
                $previous_files_count++;

            } while ($previous_file = prev($previous_files));

            // after looking through the last X number of songs for similarity
            // make sure no similar found
            if (!$similar_found)
                $separate_similar_compatibles[] = $compatible;

        }

        // if we have no harmonic files, return original set
        if (count($separate_similar_compatibles) == 0)
            return $compatibles;

        // success
        return $separate_similar_compatibles;

    }

    protected function harmonic_key_compatibles_reduction(&$compatibles)
    {

        // if we have no original key, keep files intact
        // this will give us a starting point and initiate the show
        if (!$this->current_key)
            return $compatibles;

        // keep track of harmonic files
        $harmonic_key_compatibles = array();
        // get harmonic musical keys
        $harmonic_keys = CamelotEasyMixWheel::harmonic_keys($this->current_key);
        // loop through search files until we find a file that matches the current musical key
        foreach ($compatibles as &$compatible)
        {
            // loop through each musical key option
            foreach ($harmonic_keys as $harmonic_key)
            {
                // if we find a musical key match, we are good
                if ($compatible['file']->key == $harmonic_key)
                {
                    $harmonic_key_compatibles[] = $compatible;
                    break;
                }
            }
        }

        // if we have no harmonic files, return original set
        if (count($harmonic_key_compatibles) == 0)
            return $compatibles;

        // success
        return $harmonic_key_compatibles;

    }

    protected function harmonic_genre_compatibles_scoring(&$compatibles)
    {

        // if we have no current genre, we can do no scoring
        if (!$this->current_genre)
            return;

        // get current scraped genres
        $current_scraped_genres = explode('.', strtolower($this->current_genre));
        // loop through search files until we find a file that matches the current genre
        foreach ($compatibles as &$compatible)
        {
            // get compatible file scraped genres
            $compatible_file_scraped_genres = explode('.', strtolower($compatible['file']->genre));
            // get the difference in scraped genre arrays
            $genre_differences = array_diff($current_scraped_genres, $compatible_file_scraped_genres);
            // adjust score by array difference
            $compatible['score'] -= count($genre_differences);
        }

    }

    protected function harmonic_energy_compatibles_scoring(&$compatibles)
    {

        // if we have no original energy,
        // do no sorting and keep files as is
        if (!$this->current_energy)
            return;

        // loop through compatbiles
        // subtract the energy difference from the overall score
        foreach ($compatibles as &$compatible)
            $compatible['score'] -= abs($compatible['file']->energy - $this->current_energy);

    }

    protected function gather_backup_files($seconds)
    {

        // if we have no backup block, we are done
        if (!$this->backup_block)
            return;

        // calculate remaining seconds
        $remaining_seconds = $seconds - $this->filled_seconds;
        // run the backup block
        $this->backup_block->gather_block_files($this, $remaining_seconds);

    }

    public function gather_block_files($parent_block, $seconds)
    {

        ////////////////////////////////
        // SET INHERITABLE PROPERTIES //
        ////////////////////////////////

        // set top and parent
        $this->parent_block = $parent_block;
        $this->top_block = $parent_block->top_block;

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

        ///////////////////////////////
        // FORWARD TO ALL PROCESSING //
        ///////////////////////////////

        // forward
        $this->gather_files($seconds);

    }

    protected function gather_items_files($seconds)
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

        ///////////////////////
        // GATHER ITEM FILES //
        ///////////////////////

        // reset block items array
        reset($this->ordered_block_items);
        // track current block item
        $current_block_item = current($this->ordered_block_items);
        // loop until block filled
        while (true)
        {

            //////////////////////////////////
            // VERIFY TIME FOR ANOTHER ITEM //
            //////////////////////////////////

            // see if we are over
            if ($this->filled_seconds >= $seconds)
                break;

            //////////////////////////////////////////
            // GATHER BLOCK ITEM FILE OR ITEM BLOCK //
            //////////////////////////////////////////

            if ($current_block_item->file)
            {
                // else, gather item file
                if (!$current_block_item->file->available)
                    continue;

                // gather promo file
                $this->gather_promo_file($current_block_item->file);
                // gather file
                $this->gather_file($current_block_item->file);
            }
            else
            {
                // gather block item block files
                $this->gather_item_block_files($current_block_item, $percentage_seconds, $seconds);
            }

            /////////////////////////
            // GET NEXT BLOCK ITEM //
            /////////////////////////

            // if we have none left, break
            if (!($current_block_item = next($this->ordered_block_items)))
                break;

        }

        ///////////////////////////////
        // ADDITIONAL CRITERIA FILES //
        ///////////////////////////////

        // get remaining time
        $remaining_seconds = $seconds - $this->filled_seconds;
        // if, after running through block items, we don't have our duration filled
        // fill the remainder with our weighted files
        if ($remaining_seconds > 0)
            $this->gather_weighted_files($remaining_seconds);

    }

    protected function gather_item_block_files($block_item, $percentage_seconds, $seconds)
    {

        // calculate next item duration
        if ($block_item->duration != null)
            $block_item_duration_seconds = $block_item->duration_seconds();
        else
            $block_item_duration_seconds = Helper::percentage_seconds($percentage_seconds, $block_item->percentage);

        ///////////////////////
        // TRUNCATE OVERHANG //
        ///////////////////////

        // get remaining time
        $remaining_seconds = $seconds - $this->filled_seconds;
        // if the item duration > remaining, truncate
        if ($block_item_duration_seconds > $remaining_seconds)
            $block_item_duration_seconds = $remaining_seconds;

        /////////////////////
        // ADD CHILD FILES //
        /////////////////////

        // merge next block files into end of current files array
        if ($block_item_duration_seconds > 0)
            $block_item->child_block->gather_block_files($this, $block_item_duration_seconds);

    }

}
