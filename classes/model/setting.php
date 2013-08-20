<?php

class Model_Setting extends \Orm\Model
{

    protected static $_properties = array(
        'id',
        'name',
        'type',
        'value',
        'category',
    );

    protected static $_categories = array(
        'general',
        'stream_one'
    );

    protected static $mapped_settings;

    public function human_name()
    {
        return Helper::human_name($this->name);
    }

    public function populate()
    {
        if (Input::post($this->name))
            $this->value = Input::post($this->name);
    }

    public static function get_value($name)
    {
        // see if we cached the settings
        if (!self::$mapped_settings)
            self::$mapped_settings = self::mapped();
        // fetch specific setting from cached
        return self::$mapped_settings[$name];

    }

    public static function mapped()
    {
        // see if we cached the settings
        if (self::$mapped_settings)
            return self::$mapped_settings;
        // create mapped settings cache
        self::$mapped_settings = array();
        // get all settings
        $settings = Model_Setting::query()->get();
        // map settings
        foreach ($settings as $setting)
            self::$mapped_settings[$setting->name] = $setting->value;
        // success
        return self::$mapped_settings;

    }

    public static function all()
    {
        // get all settings
        $settings = Model_Setting::query()->get();
        // put general first, sort rest alphabetically
        usort($settings, function($a, $b)
        {
            // if we have the same category, do inner name comparison
            if (strcmp($a->category, $b->category) === 0)
                return strcmp($a->name, $b->name);
            // compare categories
            if ($a->category == 'general')
                return -1;
            if ($b->category == 'general')
                return 1;
            return strcmp($a->category, $b->category);
        });

        // success
        return $settings;
    }

}
