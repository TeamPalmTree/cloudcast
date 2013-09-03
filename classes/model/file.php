<?php

class Model_File extends \Orm\Model
{

    protected $scraped_artists;
    protected $scraped_title;

    protected static $_properties = array(
        'id',
        'found_on',
        'last_play',
        'date',
        'available',
        'track',
        'BPM',
        'rating',
        'bit_rate',
        'ups',
        'downs',
        'sample_rate',
        'name',
        'duration',
        'title',
        'album',
        'artist',
        'composer',
        'conductor',
        'copyright',
        'genre',
        'ISRC',
        'label',
        'language',
        'mood',
        'key',
        'energy',
        'website',
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
    );

    protected static $artist_delimiters = array(
        'feat',
        'feat\.',
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
        if ($this->scraped_artists)
            return $this->scraped_artists;
        $this->scraped_artists = preg_split(self::artist_splitter(), strtolower($this->artist));
        return $this->scraped_artists;
    }

    public function scraped_title()
    {
        // see if it is already set
        if ($this->scraped_title)
            return $this->scraped_title;
        $this->scraped_title = preg_replace(self::$title_scraper, '', strtolower($this->title));
        return $this->scraped_title;
    }

    public function populate($scanned_file)
    {

        // keep track of changes
        $changed = false;
        // get server datetime string
        $server_datetime_string = Helper::server_datetime_string();

        // update found on
        if (!$this->found_on)
        {
            $this->found_on = $server_datetime_string;
            $changed = true;
        }

        // set file available
        if (!$this->available)
        {
            $this->available = true;
            $changed = true;
        }

        // set ups/downs
        if (!$this->ups or !$this->downs)
        {
            $this->ups = 0;
            $this->downs = 0;
            $changed = true;
        }

        // set date
        $this->populate_field('date', $scanned_file, $changed);
        // set track
        $this->populate_field('track', $scanned_file, $changed);
        // set BPM
        $this->populate_field('BPM', $scanned_file, $changed);
        // set bit_rate
        $this->populate_field('bit_rate', $scanned_file, $changed);
        // set sample_rate
        $this->populate_field('sample_rate', $scanned_file, $changed);
        // set duration
        $this->populate_field('duration', $scanned_file, $changed);
        // set title
        $this->populate_field('title', $scanned_file, $changed);
        // set album
        $this->populate_field('album', $scanned_file, $changed);
        // set artist
        $this->populate_field('artist', $scanned_file, $changed);
        // set composer
        $this->populate_field('composer', $scanned_file, $changed);
        // set conductor
        $this->populate_field('conductor', $scanned_file, $changed);
        // set copyright
        $this->populate_field('copyright', $scanned_file, $changed);
        // set genre
        $this->populate_field('genre', $scanned_file, $changed);
        // set ISRC
        $this->populate_field('ISRC', $scanned_file, $changed);
        // set label
        $this->populate_field('label', $scanned_file, $changed);
        // set language
        $this->populate_field('language', $scanned_file, $changed);
        // set mood
        $this->populate_field('mood', $scanned_file, $changed);
        // set musical key
        $this->populate_field('key', $scanned_file, $changed);
        // set energy
        $this->populate_field('energy', $scanned_file, $changed);
        // set rating
        $this->populate_field('rating', $scanned_file, $changed);

        // success
        return $changed;

    }

    private function populate_field($name, $scanned_file, &$changed)
    {
        // get the scanned file value
        $value = $scanned_file[$name];
        // see if it differs from the DB
        if ($this->$name == $value)
            return;
        // update DB value
        $this->$name = $value;
        // set changed true
        $changed = true;
    }

    public static function search(
        $search_string,
        $restrict_genres,
        $restrict_available,
        $limit,
        $randomize,
        $index
    )
    {

        //////////////////////
        // GET SEARCH QUERY //
        //////////////////////

        $search_query = self::search_query($search_string);

        //////////////////
        // RESTRICTIONS //
        //////////////////

        // see if we remove some genres from the search
        if ($restrict_genres)
            $search_query->where('genre', 'not in', self::$restricted_genres);
        // restrict to available
        if ($restrict_available)
            $search_query->where('available', true);

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

    protected static function search_query(&$search_string)
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
                self::search_query_and($search_string_and, $search_query, $server_datetime);
            }

            // success
            return $search_query;

        }
        catch(Exception $e)
        {
            return null;
        }

    }

    protected static function search_query_and(&$search_string_and, $search_query, $server_datetime)
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
            self::search_query_and_or($search_string_and_or, $search_query, $server_datetime);
        }

        // close and condition
        $search_query->and_where_close();

    }

    protected static function search_query_and_or(&$search_string_and_or, $search_query, $server_datetime)
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

        // get column/value
        $column = trim($search_string_and_or_parts[0]);
        $value = trim($search_string_and_or_parts[1]);

        //////////////////////
        // COLUMN FUNCTIONS //
        //////////////////////

        // listener rating (0-5)
        if (stripos($column, 'listener_rating') === 0)
            self::search_query_listener_rating($column);
        // popularity rating (0-5)
        else if (stripos($column, 'popularity_rating') === 0)
            self::search_query_listener_rating($column, $server_datetime);
        // total ups + downs = votes
        else if (stripos($column, 'votes') === 0)
            self::search_query_votes($column);

        /////////////////////
        // VALUE FUNCTIONS //
        /////////////////////

        // date ago
        if (stripos($value, 'date_ago') === 0)
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
        $dateinterval = new DateInterval('P' . $date_ago_string);
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

}
