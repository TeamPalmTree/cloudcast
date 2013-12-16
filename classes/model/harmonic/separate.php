<?php

/**
 * @title  Harmonic Aritst & Title Separation
 * @description  Harmonic Aritst & Title Separation
 * @priority 1
 */

class Model_Harmonic_Separate extends Model_Harmonic
{

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

        // loop through files making sure we don't have a similar one
        foreach ($this->files as $file)
        {
            // if we have no similar file, add it child harmonic's files array
            if (!$this->has_similar_file($file, $block))
                $child_harmonic->files[] = $file;
        }

    }

    protected function has_similar_file($file, $block)
    {

        // reset similar files index
        $last_files_count = 0;
        // get distance we can go back
        $separate_files_count = (int)Model_Setting::get_value('separate_files_count');

        //////////////////////////
        // CHECK GATHERED FILES //
        //////////////////////////

        // set previous files pointer to end
        $last_gathered_file = end($block->schedule->gathered_files);
        // loop through all previous files
        while (true)
        {

            // make sure we still have previous
            if (!$last_gathered_file)
                break;

            // verify not sweeper/bumper
            if (!$last_gathered_file->is_sweeper_bumper())
            {
                // verify we have not exceeded similar files count (gone too far)
                if ($last_files_count++ == $separate_files_count)
                    break;
                // verify we have not exceeded similar files count (gone too far)
                if ($this->is_similar_file($file, $last_gathered_file))
                    return true;
            }

            // keep going backwards
            $last_gathered_file = prev($block->schedule->gathered_files);

        };

        //////////////////////////
        // CHECK PREVIOUS FILES //
        //////////////////////////

        // set previous files pointer to end
        $last_previous_file = end($block->schedule->previous_files);
        // loop through all previous files
        while (true)
        {

            // make sure we still have previous
            if (!$last_previous_file)
                break;

            // verify not sweeper/bumper
            if (!$last_previous_file->is_sweeper_bumper())
            {
                // verify we have not exceeded similar files count (gone too far)
                if ($last_files_count++ == $separate_files_count)
                    break;
                // verify we have not exceeded similar files count (gone too far)
                if ($this->is_similar_file($file, $last_previous_file))
                    return true;
            }

            // keep going backwards
            $last_previous_file = prev($block->schedule->previous_files);

        };

        // no similar file found
        return false;

    }

    protected function is_similar_file($a, $b)
    {

        // compare titles
        if ($a->scraped_title() == $b->scraped_title())
        {
            // compute intersected artists
            $intersected_artists = array_intersect($a->scraped_artists(), $b->scraped_artists());
            // compare artists
            if (count($intersected_artists) > 0)
                return true;
        }

        // bot similar
        return false;

    }

}
