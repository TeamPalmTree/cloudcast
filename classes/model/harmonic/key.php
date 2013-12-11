<?php

/**
 * @title Harmonic Musical Key
 * @description  Harmonic Musical Key
 * @priority 2
 */

class Model_Harmonic_Key extends Model_Harmonic
{

    public function execute($child_harmonic_name, $block)
    {

        // get last key file
        $last_key_file = $this->last_key_file($block);
        // if we have a previous key, use it
        if ($last_key_file)
            $initial_key = $last_key_file->key;
        // else, generate a random key
        else
        {
            // generate a number from 1-12
            $initial_key_number = rand(1, 12);
            $initial_key_letter = (rand(1, 2) == 1) ? 'A' : 'B';
            $initial_key = $initial_key_number . $initial_key_letter;
        }

        // get harmonic musical keys
        $nearby_keys = MixingWheel::nearby_keys($initial_key);
        // loop through each musical key option
        foreach ($nearby_keys as $key)
        {

            // keep track of files for this key
            $key_files = array();
            // loop through all files
            foreach ($this->files as $file)
            {
                // if we find a musical key match, add to current key set
                if ($file->key == $key)
                    $key_files[] = $file;
            }

            // if we have no key files, continue
            if (count($key_files) == 0)
                continue;

            // create a new child harmonic
            $child_harmonic = Model_Harmonic::create_harmonic($child_harmonic_name);
            // add files to the next harmonic
            $child_harmonic->files = $key_files;
            // add child harmonic to children
            $this->children[$key] = $child_harmonic;

        }

    }

    protected function last_key_file($block)
    {

        // see if we have gathered files
        $last_gathered_file = end($block->schedule->gathered_files);
        // go backwards among gathered files until we find a non-promo file
        while (true)
        {
            if (!$last_gathered_file)
                break;
            // verify file not promo and has key
            if ($this->is_key_file($last_gathered_file))
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
            // verify file not promo and has key
            if ($this->is_key_file($last_previous_file))
                return $last_previous_file;
            $last_previous_file = prev($block->schedule->previous_files);
        }

        // fail
        return null;

    }

    protected function is_key_file($file)
    {
        // verify file not promo and has key
        return (!$file->is_promo() && $file->key);
    }

}

