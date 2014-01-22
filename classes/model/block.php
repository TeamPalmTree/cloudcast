<?php

class Model_Block extends \Orm\Model
{

    // standard properties
    public $parent_block;
    public $filled_seconds;
    public $weighted_block_weights;
    public $ordered_block_items;
    public $ordered_block_harmonics;
    // the current schedule
    public $schedule;

    protected static $_properties = array(
        'id',
        'title',
        'description',
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
        'block_harmonics',
        'shows',
        'child_block_items' => array(
            'key_from' => 'id',
            'model_to' => 'Model_Block_Item',
            'key_to' => 'child_block_id',
        ),
    );

    public static function validate($input)
    {

        // create validation
        $validation = Validation::forge();
        $validation->add_field('title', 'Title', 'required');
        // validate weights
        foreach ($input['block_weights'] as $block_weight_index => $block_weight)
            $validation->add_field("block_weights[$block_weight_index][weight]", 'Block Weight', 'required|numeric_min[1]');
        if (isset($input['backup_block'])) $validation->add_field('backup_block[title]', 'Backup Block Title', 'required');
        // run validation
        if (!$validation->run($input)) return Helper::errors($validation);

    }

    public static function validate_layout($input)
    {

        // create validation
        $validation = Validation::forge();
        // keep track of percentage total
        $total_percentage = 0;
        // validate items
        foreach ($input['block_items'] as $block_item_index => $block_item)
        {
            if (isset($block_item['file']))
                continue;
            if (isset($block_item['percentage']))
                $input['block_items'][$block_item_index]['percentage_duration'] = $block_item['percentage'];
            if (isset($block_item['duration']))
                $input['block_items'][$block_item_index]['percentage_duration'] = $block_item['duration'];
            $validation->add_field("block_items[$block_item_index][percentage_duration]", 'Block Item Percentage or Duration', 'required');
            $validation->add_field("block_items[$block_item_index][percentage]", 'Block Item Percentage', 'numeric_min[0]|numeric_max[100]');
            $validation->add_field("block_items[$block_item_index][duration]", 'Block Item Duration', 'non_zero_duration');
            $total_percentage += isset($block_item['percentage']) ? (int)$block_item['percentage'] : 0;
        }

        // set total percentage in input
        $input['total_percentage'] = $total_percentage;
        // add validation of total percentage
        $validation->add_field('total_percentage', 'Total Percentage', 'total_percentage');
        // run validation
        if (!$validation->run($input)) return Helper::errors($validation);

    }

    public function populate($input)
    {

        // set block from post data
        $this->title = $input['title'];
        $this->description = isset($input['description']) ? $input['description'] : null;
        $this->file_query = isset($input['file_query']) ? $input['file_query'] : null;

        // delete existing block weights
        foreach ($this->block_weights as $block_weight)
            $block_weight->delete();
        // clear existing block weights array
        $this->block_weights = array();

        // get block weights
        $block_weights = $input['block_weights'];
        // add block weights
        foreach ($block_weights as $block_weight)
        {
            $this->block_weights[] = Model_Block_Weight::forge(array(
                'weight' => $block_weight['weight'],
                'file_query' => $block_weight['file_query']
            ));
        }

        // add block
        if (isset($input['backup_block']))
        {
            // find block
            $backup_block = Model_Block::find('first', array(
                'where' => array(
                    array('title', $input['backup_block']['title']),
                )
            ));
            // set backup block
            $this->backup_block_id = $backup_block->id;
        }
        else
        {
            $this->backup_block = null;
        }

        // delete existing block harmonics
        foreach ($this->block_harmonics as $block_harmonic)
            $block_harmonic->delete();
        // clear existing block harmonics array
        $this->block_harmonics = array();

        // see if we process block harmonics
        if (isset($input['block_harmonic_names']))
        {
            // get block harmonic names
            $block_harmonic_names = $input['block_harmonic_names'];
            // add block harmonics
            foreach ($block_harmonic_names as $block_harmonic_name)
            {
                $this->block_harmonics[] = Model_Block_Harmonic::forge(array(
                    'harmonic_name' => $block_harmonic_name
                ));
            }
        }

    }

    public function populate_layout($input)
    {

        // delete existing block weights
        foreach ($this->block_items as $block_item)
            $block_item->delete();
        // clear existing block weights array
        $this->block_items = array();

        // loop over orders
        foreach ($input['block_items'] as $input_block_item)
        {
            // create item
            $block_item = Model_Block_Item::forge();
            // set item properties
            $block_item->block_id = $input['id'];
            $block_item->child_block_id = isset($input_block_item['child_block']) ? $input_block_item['child_block']['id'] : null;
            $block_item->file_id = isset($input_block_item['file']) ? $input_block_item['file']['id'] : null;
            // set child block parameters
            if (isset($input_block_item['child_block']))
            {
                $block_item->percentage = isset($input_block_item['percentage']) ? $input_block_item['percentage'] : null;
                $block_item->duration = isset($input_block_item['duration']) ? $input_block_item['duration'] : null;
            }

            // add to block items
            $this->block_items[] = $block_item;
        }

    }

    public static function titles($query)
    {
        $blocks = DB::select('title')
            ->from('blocks')
            ->where('title', 'LIKE', $query . '%')
            ->as_object()
            ->execute();
        return Helper::extract_values('title', $blocks);
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

    public static function editable($id)
    {

        // get block
        $block = Model_Block::query()
            ->related('backup_block')
            ->where('id', $id)
            ->get_one();

        // get block weights
        $block->block_weights = Model_Block_Weight::query()
            ->where('block_id', $id)
            ->get();

        // success
        return $block;

    }

    public static function layoutable($id)
    {

        // get block & items
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

    public static function viewable_layoutable($block)
    {

        // set items array
        $block->block_items = array_values($block->block_items);
        // success
        return $block;

    }

    public static function viewable_editable($block)
    {

        // set weights array
        $block->block_weights = array_values($block->block_weights);

        // get block harmonics
        $block_harmonics = Model_Block_Harmonic::query()
            ->where('block_id', $block->id)
            ->get();
        // set block harmonic ids
        $block->block_harmonic_names = Helper::extract_values('harmonic_name', $block_harmonics);
        // get all harmonics
        $block->harmonics = array_values(Model_Harmonic::get_harmonics());

        // success
        return $block;

    }

    public static function viewable_creatable()
    {

        // create block
        $block = Model_Block::forge();
        // get all harmonics
        $block->harmonics = array_values(Model_Harmonic::get_harmonics());
        // success
        return $block;

    }

    public static function viewable_all()
    {

        // get blocks
        $blocks = Model_Block::query()
            ->related('block_items')
            ->related('block_weights')
            ->order_by('title', 'ASC')
            ->get();

        // set weighted and items
        foreach ($blocks as $block)
        {
            if (count($block->block_items) > 0)
                $block->itemized = true;
            if (count($block->block_weights) > 0)
                $block->weighted = true;
            unset($block->block_items);
            unset($block->block_weights);
        }

        // success
        return array_values($blocks);

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
        // set seconds
        if ($seconds == null)
            $seconds = $schedule->duration_seconds();

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

        /////////////////////////////////////////////
        // SET BLOCK WEIGHTS, ITEMS, AND HARMONICS //
        /////////////////////////////////////////////

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

        // get block harmonics
        $this->ordered_block_harmonics = Model_Block_Harmonic::query()
            ->where('block_id', $this->id)
            ->get();

        // get harmonics
        $harmonics = Model_Harmonic::get_harmonics();
        // order block harmonics by harmonic priority
        usort($this->ordered_block_harmonics, function($a, $b) use (&$harmonics) {
            $a_priority = $harmonics[$a->harmonic_name]['priority'];
            $b_priority = $harmonics[$b->harmonic_name]['priority'];
            if ($a_priority > $b_priority) return 1;
            if ($a_priority > $b_priority) return -1;
            return 0;
        });

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
        $base_files = Model_File::searched($this->file_query, true, null, true, true);
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
            $block_weight_files = Model_File::searched(
                $this->file_query . "\n" .  $block_weight->file_query,
                true, null, true, true);
            // only add weight files if we have some
            if (count($block_weight_files) > 0)
                $weighted_files[$block_weight->weight] = $block_weight_files;
        }

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
        $file = $this->harmonic_file($source_files);
        // if we have a file, we are done
        if ($file)
            return $file;

        //////////////////////////////////////////
        // WALK DOWN WEIGHTS TO CONTINUE SEARCH //
        //////////////////////////////////////////

        $weights = array_keys($weighted_files);
        // get weighted files weights
        sort($weights, SORT_NUMERIC);
        // get the next down weight index
        $weights_index = array_search($current_weight, $weights) - 1;
        // loop while we have lower weights
        while ($weights_index >= 0)
        {
            // get the next weight to check
            $current_weight = $weights[$weights_index];
            // get the weighted file set at this weight
            $source_files = $weighted_files[$current_weight];
            // attempt to choose from lower weighted files set
            $file = $this->harmonic_file($source_files);
            // did we find a file
            if ($file)
                return $file;

            // lower the weights index
            $weights_index--;
        }

        // failed for all sets
        return null;

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
            // if the random number is less than or = to the cum weights sum, we have found our set
            if ($random_number <= $cumulative_weights_sum)
            {
                // set random weight
                $random_weight = $weight;
                // success
                return $files;
            }
        }

    }

    protected function harmonic_file($files)
    {

        // get the harmonic files tree
        $current_harmonic = $this->base_harmonic($files);
        $base_harmonic = $current_harmonic;
        // loop through tree randomly
        while (true)
        {

            // get the count of current harmonic's children
            $children_count = count($current_harmonic->children);
            // verify we have at least one
            if ($children_count == 0)
            {
                // make sure we have some files
                if (count($current_harmonic->files) == 0)
                    return null;
                // return a random file from this array
                $file_index = array_rand($current_harmonic->files);
                return $current_harmonic->files[$file_index];
            }

            // get an element at random
            $child_index = array_rand($current_harmonic->children);
            // set current harmonic to this randomly selected child harmonic
            $current_harmonic = $current_harmonic->children[$child_index];

        }

    }

    protected function base_harmonic($files)
    {

        // reset ordered block harmonics to get base
        $first_block_harmonic = reset($this->ordered_block_harmonics);
        // verify we have a first harmonic
        if (!$first_block_harmonic)
        {
            // get base harmonic
            $base_harmonic =  Model_Harmonic::create_harmonic('base');
            // set base harmonic files
            $base_harmonic->files = $files;
            // success
            return $base_harmonic;
        }

        // get base harmonic
        $base_harmonic =  Model_Harmonic::create_harmonic($first_block_harmonic->harmonic_name);
        // set base harmonic files
        $base_harmonic->files = $files;
        // recurse generate harmonic files tree
        $this->recurse_harmonic($base_harmonic, $this->ordered_block_harmonics);
        // success
        return $base_harmonic;

    }

    protected function recurse_harmonic($harmonic, $block_harmonics)
    {

        // get next ordered block harmonic
        $next_block_harmonic = next($block_harmonics);
        // if we have no next harmonic, create a genric one
        if ($next_block_harmonic)
            $child_harmonic_name = $next_block_harmonic->harmonic_name;
        else
            $child_harmonic_name = 'star';

        // execute the current harmonic to get next harmonics
        $harmonic->execute($child_harmonic_name, $this);
        // only recurse if we have a next harmonic
        if (!$next_block_harmonic)
            return;

        // recurse through generated child harmonics
        foreach ($harmonic->children as $child_harmonic)
            $this->recurse_harmonic($child_harmonic, $block_harmonics);

    }

    protected function gather_promo_file($next_file)
    {

        // get schedule
        $schedule = $this->schedule;
        // get last file
        $last_file = $schedule->last_file();
        // if we have no last file, we cannot do promos
        if (!$last_file)
            return false;

        // get current/next genre
        $genre = $last_file->genre;
        $next_genre = $next_file->genre;

        //////////////////
        // BUMPER CHECK //
        //////////////////

        if (($genre == 'Ad')
            && ($next_genre != 'Ad')
            && ($next_genre != 'Intro'))
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

        if (($genre != 'Ad')
            && ($genre != 'Intro')
            && ($next_genre != 'Ad')
            && ($next_genre != 'Intro'))
        {
            // if we have gone through enough file since last sweeper
            if ($schedule->sweeper_files_count >= $schedule->show->sweeper_interval)
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

        //////////////////////////////////
        // CALCULATE ADDITIONAL SECONDS //
        //////////////////////////////////

        // get schedule
        $schedule = $this->schedule;
        // get last file
        $last_file = $schedule->last_file();
        // update filled seconds with transitioned duration
        $additional_seconds = $file->transitioned_duration_seconds($last_file);

        /////////////////
        // UPDATE FILE //
        /////////////////

        // set last scheduled time
        //$file->last_scheduled = Helper::server_datetime_string();
        // save file
        //$file->save();

        /////////////////////
        // UPDATE SCHEDULE //
        /////////////////////

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
            // move upwards
            $block = $block->parent_block;

        }

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

        ////////////////////
        // SET PROPERTIES //
        ////////////////////

        // set top and schedule
        $this->parent_block = $parent_block;
        $this->schedule = $parent_block->schedule;

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

        ////////////////////////////////
        // GET BLOCK DURATION SECONDS //
        ////////////////////////////////

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
        // verify we have

        /////////////////////////////
        // GATHER BLOCK ITEM FILES //
        /////////////////////////////

        // if the child block is just us, it is a spacer and we need to use criteria
        // else, merge next block files into end of current files array
        if ($block_item->child_block_id == $this->id)
            $this->gather_weighted_files($block_item_duration_seconds);
        else
            $block_item->child_block->gather_block_files($this, $block_item_duration_seconds);

    }

}
