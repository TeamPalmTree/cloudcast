<?php

class Model_File extends \Orm\Model
{

    protected $scraped_artists;
    protected $scraped_title;
    protected $split_genres;
    protected $is_promo;
    protected $is_sweeper_bumper;

    protected static $_properties = array(
        'id',
        'found_on',
        'modified_on',
        'last_played',
        'last_scheduled',
        'date',
        'available',
        'found',
        'BPM',
        'rating',
        'bit_rate',
        'ups',
        'downs',
        'relevance',
        'sample_rate',
        'name',
        'duration',
        'post',
        'title',
        'album',
        'artist',
        'composer',
        'conductor',
        'copyright',
        'genre',
        'ISRC',
        'language',
        'key',
        'energy'
    );

    protected static $_has_many = array(
        'block_items',
        'schedule_files',
    );

    protected static $restricted_genres = array(
        'Ad',
        'Sweeper',
        'Jingle',
        'Bumper',
        'Intro',
        'Set',
        'News',
        'Countdown',
    );

    protected static $artist_delimiters = array(
        'feat',
        'feat\.',
        'featuring',
        'featuring\.',
        'vs',
        'vs\.',
        'versus',
        '\&',
        '\,',
    );

    protected static $artist_splitter;
    protected static $title_scraper = '/([\s]*\(.*\)[\s]*)|([\s]*\[.*\][\s]*)/';

    protected static function artist_splitter()
    {

        // see if the splitter is already set
        if (self::$artist_splitter)
            return self::$artist_splitter;

        $artist_splitters = array();
        // create the splitter
        foreach (self::$artist_delimiters as $artist_delimiter)
            $artist_splitters[] = '([\s]+' . $artist_delimiter . '[\s]+)';
        // set and success
        self::$artist_splitter = '/' . implode('|', $artist_splitters) . '/';
        return self::$artist_splitter;

    }

    public function duration_seconds()
    {
        return Helper::duration_seconds($this->duration);
    }

    public function scraped_artists()
    {
        // see if it is already set
        if (isset($this->scraped_artists))
            return $this->scraped_artists;
        $this->scraped_artists = preg_split(self::artist_splitter(), strtolower($this->artist));
        return $this->scraped_artists;
    }

    public function scraped_title()
    {
        // see if it is already set
        if (isset($this->scraped_title))
            return $this->scraped_title;
        $this->scraped_title = preg_replace(self::$title_scraper, '', strtolower($this->title));
        return $this->scraped_title;
    }

    public function split_genres()
    {
        // see if it is already set
        if (isset($this->split_genres))
            return $this->split_genres;
        $this->split_genres = explode('.', strtolower($this->genre));
        return $this->split_genres;
    }
    
    public function is_promo()
    {

        // see if it is already set
        if (isset($this->is_promo))
            return $this->is_promo;
        $genre = $this->genre;
        $this->is_promo = (($genre == 'Intro') or ($genre == 'Ad') or $this->is_sweeper_bumper());
        return $this->is_promo;

    }

    public function is_sweeper_bumper()
    {

        // see if it is already set
        if (isset($this->is_sweeper_bumper))
            return $this->is_sweeper_bumper;
        $genre = $this->genre;
        $this->is_sweeper_bumper = (($genre == 'Sweeper') or ($genre == 'Bumper'));
        return $this->is_sweeper_bumper;

    }

    public function user_found_on()
    {
        return Helper::server_datetime_string_to_user_datetime_string($this->found_on);
    }

    public function user_modified_on()
    {
        return Helper::timestamp_to_user_datetime_string($this->modified_on);
    }

    public function user_last_played()
    {
        return Helper::server_datetime_string_to_user_datetime_string($this->last_played);
    }

    public function user_last_scheduled()
    {
        return Helper::server_datetime_string_to_user_datetime_string($this->last_scheduled);
    }

    public function populate_scanned($scanned_file, $server_datetime_string)
    {

        // update found on
        if (!$this->found_on)
            $this->found_on = $server_datetime_string;
        // set file found
        if (!$this->found)
            $this->found = true;
        // set file availability
        if (is_null($this->available))
            $this->available = true;
        // set relevance
        if (is_null($this->relevance))
            $this->relevance = 1;
        // set ups
        if (is_null($this->ups))
            $this->ups = 0;
        // set downs
        if (is_null($this->downs))
            $this->downs = 0;
        // populate remained of fields
        $this->set($scanned_file);

    }

    public static function viewable_searched($files)
    {

        // set user times
        foreach ($files as &$file)
        {
            $file->user_found_on = $file->user_found_on();
            $file->user_modified_on = $file->user_modified_on();
            $file->user_last_played = $file->user_last_played();
            $file->user_last_scheduled = $file->user_last_scheduled();
        }

        // success
        return $files;

    }

    public static function searched(
        $search_string,
        $restrict,
        $limit,
        $randomize,
        $index
    )
    {

        //////////////////////
        // GET SEARCH QUERY //
        //////////////////////

        // get a copy of the restricted genres, lower cased
        $restricted_genres = array_map('strtolower', self::$restricted_genres);
        // run the query; any use of a restricted genre will remove its restriction
        $search_query = self::search_query($search_string, $restricted_genres);

        //////////////
        // RESTRICT //
        //////////////

        // remove certain genres and unavailable files
        if ($restrict)
        {
            // remove some genres from the search
            $search_query->where('genre', 'not in', $restricted_genres);
            // restrict to available
            $search_query->where('available', '1');
        }

        ///////////////////////
        // RANDOMIZE & LIMIT //
        ///////////////////////

        // add random sort
        if ($randomize)
            $search_query->order_by(DB::expr('RAND()'));
        // add limit
        if ($limit)
            $search_query->limit($limit);

        /////////////////
        // GET & INDEX //
        /////////////////

        // get em
        $search_files = $search_query->get();
        // either return indexed, or
        if ($index)
            return $search_files;
        // get array values
        return array_values($search_files);

    }

    protected static function search_query(
        &$search_string,
        &$restricted_genres)
    {

        try
        {

            // start the query :)
            $search_query = Model_File::query();
            // get server datetime
            $server_datetime = Helper::server_datetime();
            // get query ands
            $search_string_ands = explode("\n", $search_string);
            // process each query and
            foreach ($search_string_ands as $search_string_and)
            {
                // ignore empty ands
                if ($search_string_and == '')
                    continue;
                // process and
                self::search_query_and($search_string_and, $restricted_genres, $search_query, $server_datetime);
            }

            // restrict to found only
            $search_query->where('found', '1');
            // success
            return $search_query;

        }
        catch(Exception $e)
        {
            return null;
        }

    }

    protected static function search_query_and(
        &$search_string_and,
        &$restricted_genres,
        $search_query,
        $server_datetime)
    {

        // start and condition
        $search_query->and_where_open();
        // get query ors
        $search_string_and_ors = explode(",", $search_string_and);
        // process each query or
        foreach ($search_string_and_ors as $search_string_and_or)
        {
            // ignore empty ors
            if ($search_string_and_or == '')
                continue;
            // process or
            self::search_query_and_or($search_string_and_or, $restricted_genres, $search_query, $server_datetime);
        }

        // close and condition
        $search_query->and_where_close();

    }

    protected static function search_query_and_or(
        &$search_string_and_or,
        &$restricted_genres,
        $search_query,
        $server_datetime)
    {

        ////////////////////////
        // HANDLE COMPARISONS //
        ////////////////////////

        // get query line parts
        if (strpos($search_string_and_or, '>=') !== false)
            $delimiter = '>=';
        elseif (strpos($search_string_and_or, '<=') !== false)
            $delimiter = '<=';
        elseif (strpos($search_string_and_or, '>') !== false)
            $delimiter = '>';
        elseif (strpos($search_string_and_or, '<') !== false)
            $delimiter = '<';
        elseif (strpos($search_string_and_or, '!=') !== false)
            $delimiter = '!=';
        elseif (strpos($search_string_and_or, '=') !== false)
            $delimiter = '=';
        elseif (strpos($search_string_and_or, '!~') !== false)
            $delimiter = '!~';
        elseif (strpos($search_string_and_or, '~') !== false)
            $delimiter = '~';

        ////////////////////////
        // GET COLUMN & VALUE //
        ////////////////////////

        // get or parts
        $search_string_and_or_parts = explode($delimiter, $search_string_and_or);
        // verify 3 parts
        if (count($search_string_and_or_parts) != 2)
            throw new Exception('Invalid Where Clause');

        // get column/value, trimmed and lowercase
        $column = strtolower(trim($search_string_and_or_parts[0]));
        $value = strtolower(trim($search_string_and_or_parts[1]));

        /////////////////////////////////////
        // REMOVE RESTRICTED GENRE IF USED //
        /////////////////////////////////////

        // we must query against genre
        if ($column == 'genre')
        {
            // then check for the restricted genre, and remove it if it exists
            if (($restricted_genres_key = array_search(strtolower($value), $restricted_genres)) !== false)
                unset($restricted_genres[$restricted_genres_key]);
        }

        //////////////////////
        // COLUMN FUNCTIONS //
        //////////////////////

        // listener rating (0-5)
        if (strpos($column, 'listener_rating') === 0)
            self::search_query_listener_rating($column);
        // popularity rating (0-5)
        else if (strpos($column, 'popularity_rating') === 0)
            self::search_query_listener_rating($column, $server_datetime);
        // total ups + downs = votes
        else if (strpos($column, 'votes') === 0)
            self::search_query_votes($column);

        /////////////////////
        // VALUE FUNCTIONS //
        /////////////////////

        // date ago
        if (strpos($value, 'date_ago') === 0)
            self::search_query_date_ago($value, $server_datetime);

        ///////////////////
        // LIKE HANDLING //
        ///////////////////

        if ($delimiter == '~')
        {
            // verify at least one char
            if (strlen($value) == 0)
                throw new Exception('Invalid Like Value');
            // set delimiter/value
            $delimiter = 'LIKE';
            $value = '%' . $value . '%';
        }
        else if ($delimiter == '!~')
        {
            // verify at least one char
            if (strlen($value) == 0)
                throw new Exception('Invalid Not Like Value');
            // set delimiter/value
            $delimiter = 'NOT LIKE';
            $value = '%' . $value . '%';
        }

        // add condition
        $search_query->or_where($column, $delimiter, $value);

    }

    protected static function search_query_listener_rating(&$column)
    {
        // listener rating == ups / (ups + downs); handling the divide by zero case
        // and then normalized to a 5 point scale
        $column = DB::expr('IF((ups + downs) > 0, ups / (ups + downs) * 5, 0)');
    }

    protected static function search_query_popularity_rating(&$column, $server_datetime)
    {
        // get the most popular file over the popularity time frame
        $most_popular_file = Model_Schedule_File::most_popular_file($server_datetime);
        // now caculate the total votes of that file
        $most_popular_file_votes = $most_popular_file->ups + $most_popular_file->downs;
        // popularity is the total votes for any file we are finding / the most
        // popular files votes over the popularity time frame
        // normalized to a 5 point scale
        $column = DB::expr("(ups + downs) / $most_popular_file_votes * 5");
    }

    protected static function search_query_votes(&$column)
    {
        // return the sum of ups and downs (total votes)
        $column = DB::expr('ups + downs');
    }

    protected static function search_query_date_ago(&$value, $server_datetime)
    {
        // grab innards
        $date_ago_string = substr($value, 9, strlen($value) - 10);
        // create date interval
        $dateinterval = new DateInterval('P' . strtoupper($date_ago_string));
        // clone server datetime
        $datetime = clone $server_datetime;
        // subtract years
        $datetime->sub($dateinterval);
        // override value with calculation
        $value = Helper::server_datetime_to_user_datetime_string($datetime);
    }

    public static function catalog()
    {

        $catalog = array();
        // get all files
        $files = Model_File::find('all');
        // loop over files, add to catalog
        foreach ($files as $file)
            $catalog[$file->name] = $file;
        // success
        return $catalog;

    }

    public static function promos($genre, $album)
    {
        return Model_File::query()
            ->where('genre', $genre)
            ->where('album', $album)
            ->where('available', '1')
            ->where('found', '1')
            ->order_by(DB::expr('RAND()'))
            ->get();
    }

    public function transitioned_duration_seconds($previous_genre)
    {

        /////////////////////////////////////////////////
        // GET BASE DURATION AND TRANSITION PARAMETERS //
        /////////////////////////////////////////////////

        // start off with our current file duration
        $transitioned_duration_seconds = $this->duration_seconds();
        // get transition parameters
        $transition_cross_seconds = Model_Setting::get_value('transition_cross_seconds');
        $transition_delay_seconds = Model_Setting::get_value('transition_delay_seconds');

        //////////////////////////////////////////////////
        // MATCH LIQUIDSOAP TRANSITION FUNCTION TIMINGS //
        //////////////////////////////////////////////////

        // bumper to file
        if (($previous_genre == 'Bumper') && ($this->genre != 'Intro'))
            return $transitioned_duration_seconds + $transition_delay_seconds;
        // intro to file
        if ($previous_genre == 'Intro')
            return $transitioned_duration_seconds + $transition_delay_seconds;
        // file to sweeper or sweeper to file
        if (($this->genre == 'Sweeper') or ($previous_genre == 'Sweeper'))
            return $transitioned_duration_seconds - ($transition_cross_seconds / 2);
        // everything else is back to back
        return $transitioned_duration_seconds;

    }

}
