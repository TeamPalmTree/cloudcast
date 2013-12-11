<?php

/**
 * @title  Harmonic Energy
 * @description  Harmonic Energy
 * @priority 3
 */

class Model_Harmonic_Energy extends Model_Harmonic
{

    public function execute($child_harmonic_name, $block)
    {

        // get last energy file
        $last_energy_file = $this->last_energy_file($block);
        // if we have a previous energy, use it
        if ($last_energy_file)
            $initial_energy = $last_energy_file->energy;
        // else, generate a random energy
        else
            $initial_energy = (string)rand(1, 10);

        // get nearby energies
        $nearby_energies = array($initial_energy);
        if ($initial_energy >= 1)
            $nearby_energies[] = (string)($initial_energy - 1);
        if ($initial_energy <= 9)
            $nearby_energies[] = (string)($initial_energy + 1);

        // loop through nearby energies
        foreach ($nearby_energies as $energy)
        {

            // keep track of files for this energy
            $energy_files = array();
            // loop through all files
            foreach ($this->files as $file)
            {
                // if we find an energy match, add to current energy set
                if ($file->energy == $energy)
                    $energy_files[] = $file;
            }

            // if we have no energy files, continue
            if (count($energy_files) == 0)
                continue;

            // create a new child harmonic
            $child_harmonic = Model_Harmonic::create_harmonic($child_harmonic_name);
            // add files to the next harmonic
            $child_harmonic->files = $energy_files;
            // add child harmonic to children
            $this->children[$energy] = $child_harmonic;

        }

    }

    protected function last_energy_file($block)
    {

        // see if we have gathered files
        $last_gathered_file = end($block->schedule->gathered_files);
        // go backwards among gathered files until we find a non-promo file
        while (true)
        {
            if (!$last_gathered_file)
                break;
            // see if it is an energy file
            if ($this->is_energy_file($last_gathered_file))
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
            // see if it is an energy file
            if ($this->is_energy_file($last_previous_file))
                return $last_previous_file;
            $last_previous_file = prev($block->schedule->previous_files);
        }

        // fail
        return null;

    }

    protected function is_energy_file($file)
    {
        // verify file not promo and has energy
        return (!$file->is_promo() && $file->energy);
    }

}
