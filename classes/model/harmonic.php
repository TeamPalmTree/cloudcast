<?php

class Model_Harmonic extends \Model
{

    public $name;
    public $title;
    public $description;
    public $priority;
    public $files = array();
    public $children = array();

    public function execute($classname, $block) { }

    public function  __construct($harmonic)
    {
        // transfer harmonic properties over
        foreach ($harmonic as $key => $value)
            $this->$key = $value;
    }

    protected static $harmonics;

    public static function get_harmonics()
    {

        // if we have harmonics set, return those
        if (isset(self::$harmonics))
            return self::$harmonics;

        // setup harmonics array
        self::$harmonics = array();
        // get the harmonics directory
        $harmonics_directory = APPPATH . 'classes/model/harmonic/';
        // open directory
        $harmonics_directory_handle = opendir($harmonics_directory);
        // get each file
        while (($harmonic_file_title = readdir($harmonics_directory_handle)) !== false)
        {

            // make sure not . or ..
            if (substr($harmonic_file_title, 0, 1) == '.')
                continue;

            // get file name
            $harmonic_file_name = $harmonics_directory . $harmonic_file_title;
            // verify file is a file
            if (!is_file($harmonic_file_name))
                continue;


            // create new harmonic
            $harmonic = array();
            // get the contents of the file
            $harmonic_file_contents = file_get_contents($harmonic_file_name);
            // get comment parameters from this file
            preg_match_all('/(@(\w*)) *([ &\w]*)/', $harmonic_file_contents, $matches);
            // get matches for groups 3 and 4
            foreach ($matches[2] as $variable_index => $variable_name)
                $harmonic[$variable_name] = $matches[3][$variable_index];

            // get pathinfo
            $harmonic_file_info = pathinfo($harmonic_file_name);
            // get name
            $harmonic_name = $harmonic['name'] = $harmonic_file_info['filename'];
            // add harmonic to array
            self::$harmonics[$harmonic_name] = $harmonic;

        }

        // success
        return self::$harmonics;

    }

    public static function create_harmonic($name)
    {

        // verify we have it
        if (!isset(self::$harmonics[$name]))
            return new Model_Harmonic(array('name' => $name));

        // get harmonic classname
        $harmonic_classname = 'Model_Harmonic_' . ucfirst($name);
        // instantiate new harmonic class
        return new $harmonic_classname(self::$harmonics[$name]);

    }

}
