<?php

/**
 * @title  Harmonic Aritst & Title Separation
 * @description  Harmonic Aritst & Title Separation
 * @priority 1
 */

class Model_Harmonic_Separate extends Model_Harmonic
{

    protected $previous_files_count;
    protected $separate_artists_count;
    protected $separate_titles_count;

    public function execute($child_harmonic_name, $block)
    {

        //////////////////////////
        // SETUP CHILD HARMONIC //
        //////////////////////////

        // instantiate child harmonic
        $child_harmonic = Model_Harmonic::create_harmonic($child_harmonic_name);
        // add to children
        $this->children[] = $child_harmonic;

        ////////////////////////////////////////////////////////
        // REMOVE FILES IN NEXT HARMONIC OF SAME TITLE+ARTIST //
        ////////////////////////////////////////////////////////

        // get artists distance we can go back
        $this->separate_artists_count = (int)Model_Setting::get_value('separate_artists_count');
        // get titles distance we can go back
        $this->separate_titles_count = (int)Model_Setting::get_value('separate_titles_count');
        // loop through files making sure we don't have a similar one
        foreach ($this->files as $file)
        {

            // reset last files count
            $this->previous_files_count = 0;
            // check amongst gathered files for similar
            if ($this->is_file_similar($file, $block->schedule->gathered_files))
                continue;
            // check amongst previous files for similar
            if ($this->is_file_similar($file, $block->schedule->previous_files))
                continue;
            // add file
            $child_harmonic->files[] = $file;

        }

    }

    protected function is_file_similar($file, &$previous_files)
    {

        //////////////////////////
        // CHECK GATHERED FILES //
        //////////////////////////

        // set previous files pointer to end
        $previous_file = end($previous_files);
        // loop through all previous files
        while (true)
        {

            // make sure we still have previous
            if (!$previous_file)
                break;

            // if we have exceeded both counts, we are done
            if (($this->previous_files_count >= $this->separate_artists_count)
                && ($this->previous_files_count >= $this->separate_titles_count))
                break;

            // verify not sweeper/bumper
            if (!$previous_file->is_sweeper_bumper())
            {

                // increment previous files count
                $this->previous_files_count++;
                // check for similar artists
                if ($this->previous_files_count < $this->separate_artists_count)
                {
                    if ($this->are_file_artists_similar($file, $previous_file))
                        return true;
                }

                // check for similar titles
                if ($this->previous_files_count < $this->separate_titles_count)
                {
                    if ($this->are_file_titles_similar($file, $previous_file))
                        return true;
                }

            }

            // keep going backwards
            $previous_file = prev($previous_files);

        };

        // no similar file found
        return false;

    }

    protected function are_file_artists_similar($a, $b)
    {

        // get a and b scraped artists
        $a_scraped_artists = $a->scraped_artists();
        $b_scraped_artists = $b->scraped_artists();
        // intersect artists
        $same_artists = array_intersect($a_scraped_artists, $b_scraped_artists);
        // see if we have any intersected
        return count($same_artists) > 0;

    }

    protected function are_file_titles_similar($a, $b)
    {

        // compare titles, check similar artists if titles are the same
        if ($a->scraped_title() == $b->scraped_title())
            return $this->are_file_artists_similar($a, $b);
        // bot similar
        return false;

    }

}
