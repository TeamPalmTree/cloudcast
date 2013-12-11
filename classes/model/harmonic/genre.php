<?php

/**
 * @title  Harmonic Genre
 * @description  Harmonic Genre
 * @priority 4
 */

class Model_Harmonic_Genre extends Model_Harmonic
{

    public function execute($child_harmonic_name, $block)
    {

        // get last non-promo file
        $last_non_promo_file = $this->last_non_promo_file($block);
        // if we have a previous genre, use it
        if (!$last_non_promo_file)
        {
            // create a single child harmonioc
            $child_harmonic = Model_Harmonic::create_harmonic($child_harmonic_name);
            // transfer all genres over
            $child_harmonic->files = $this->files;
            // add child harmonic to children
            $this->children[] = $child_harmonic;
            return;
        }

        // split apart initial genre parts
        $nearby_genres = $last_non_promo_file->split_genres();
        // loop over all nearby genres
        foreach ($nearby_genres as &$genre)
        {
            // keep track of files for this genre
            $genre_files = array();
            // loop through all files
            foreach ($this->files as $file)
            {
                // get file split genres
                $file_split_genres = $file->split_genres();
                // if we have the genre in the array, add
                if (array_search($genre, $file_split_genres) !== false)
                    $genre_files[] = $file;
            }

            // if we have no genre files, continue
            if (count($genre_files) == 0)
                continue;

            // create a new child harmonic
            $child_harmonic = Model_Harmonic::create_harmonic($child_harmonic_name);
            // add files to the next harmonic
            $child_harmonic->files = $genre_files;
            // add child harmonic to children
            $this->children[$genre] = $child_harmonic;
        }

    }

    protected function last_non_promo_file($block)
    {

        // see if we have gathered files
        $last_gathered_file = end($block->schedule->gathered_files);
        // go backwards among gathered files until we find a non-promo file
        while (true)
        {
            if (!$last_gathered_file)
                break;
            // verify file not promo and has energy
            if (!$last_gathered_file->is_promo())
                return $last_gathered_file;
            $last_gathered_file = prev($block->schedule->gathered_files);
        }

        // see if we have previous files
        $last_previous_file = end($block->schedule->previous_files);
        // go backwards among gathered files until we find a non-promo file
        while (true)
        {
            if (!$last_previous_file)
                break;
            // verify file not promo and has energy
            if (!$last_previous_file->is_promo())
                return $last_previous_file;
            $last_previous_file = prev($block->schedule->previous_files);
        }

        // fail
        return null;

    }

}
