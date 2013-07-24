<?php

class Model_File extends \Orm\Model
{

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
        'musical_key',
        'energy',
        'website',
    );

    protected static $_has_many = array(
        'block_items',
        'schedule_files',
    );

    public function duration_seconds()
    {
        return Helper::duration_seconds($this->duration);
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
        $this->populate_field('musical_key', $scanned_file, $changed);
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
        $query,
        $limit = 0,
        $randomize = true,
        $restrict_genres = true,
        $restrict_available = true
    )
    {

        // start the query :)
        $files = Model_File::query();
        // get server datetime
        $server_datetime = Helper::server_datetime();

        try
        {
            // get query ands
            $query_ands = explode("\n", $query);
            // process each query and
            foreach ($query_ands as $query_and)
            {
                // ignore empty ands
                if ($query_and == '')
                    continue;

                // start and condition
                $files = $files->and_where_open();
                // get query ors
                $query_ors = explode(",", $query_and);
                // process each query or
                foreach ($query_ors as $query_or)
                {
                    // ignore empty ors
                    if ($query_or == '')
                        continue;

                    // get query line parts
                    if (strpos($query_or, '>=') !== false)
                        $delimiter = '>=';
                    elseif (strpos($query_or, '<=') !== false)
                        $delimiter = '<=';
                    elseif (strpos($query_or, '>') !== false)
                        $delimiter = '>';
                    elseif (strpos($query_or, '<') !== false)
                        $delimiter = '<';
                    elseif (strpos($query_or, '!=') !== false)
                        $delimiter = '!=';
                    elseif (strpos($query_or, '=') !== false)
                        $delimiter = '=';
                    elseif (strpos($query_or, '~') !== false)
                        $delimiter = '~';

                    // get or parts
                    $query_or_parts = explode($delimiter, $query_or);
                    // verify 3 parts
                    if (count($query_or_parts) != 2)
                        throw new Exception('Invalid Where Clause');

                    // column/value
                    $column = trim($query_or_parts[0]);
                    $value = trim($query_or_parts[1]);

                    ///////////////////
                    // DATE FUNCTION //
                    ///////////////////

                    if (strpos($value, 'DATEAGO(') === 0)
                    {
                        // grab innards
                        $date_ago_string = substr($value, 8, strlen($value) - 9);
                        // create date interval
                        $dateinterval = new DateInterval('P' . $date_ago_string);
                        // clone server datetime
                        $datetime = clone $server_datetime;
                        // subtract years
                        $datetime->sub($dateinterval);
                        // override value with calculation
                        $value = Helper::server_datetime_to_user_datetime_string($datetime);
                    }

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
                    $files = $files->or_where($column, $delimiter, $value);
                }

                // close and condition
                $files = $files->and_where_close();
            }
        }
        catch(Exception $e)
        {
            return array();
        }

        //////////////////
        // RESTRICTIONS //
        //////////////////

        // see if we remove some genres from the search
        if ($restrict_genres)
        {
            $restricted_genres = array();
            // get genres to restrict
            $restricted_genres[] = Model_Setting::get_value('advertisement_genre');
            $restricted_genres[] = $sweeper_genre = Model_Setting::get_value('sweeper_genre');
            $restricted_genres[] = $jingle_genre = Model_Setting::get_value('jingle_genre');
            // add to query
            $files = $files->where('genre', 'not in', $restricted_genres);
        }

        // restrict to available
        if ($restrict_available)
            $files = $files->where('available', true);

        ///////////////////////
        // RANDOMIZE & LIMIT //
        ///////////////////////

        // add random sort
        if ($randomize)
            $files = $files->order_by(DB::expr('RAND()'));
        // add limit
        if ($limit)
            $files = $files->limit($limit);


        // get em
        $files = $files->get();
        // get array values
        return array_values($files);
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
