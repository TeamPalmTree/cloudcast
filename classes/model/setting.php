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

    protected static $setting_values;

    public function human_name()
    {
        return Helper::human_name($this->name);
    }

    public static function commit($settings_input)
    {

        // get mapped settings
        $settings = self::mapped();
        // populate each mapped setting with new input value
        foreach ($settings_input as &$setting_input)
        {
            // get setting name
            $setting = $settings[$setting_input['name']];
            // set value
            $setting->value = $setting_input['value'];
            // save setting
            $setting->save();
        }

    }

    public static function mapped()
    {
        // get all settings
        $settings = Model_Setting::query()->get();

        $mapped_settings = array();
        // map settings
        foreach ($settings as $setting)
            $mapped_settings[$setting->name] = $setting;

        // success
        return $mapped_settings;

    }

    public static function values()
    {
        // see if we cached the settings
        if (isset(self::$setting_values))
            return self::$setting_values;
        // create values settings cache
        self::$setting_values = array();
        // get all settings
        $settings = Model_Setting::query()->get();
        // map settings
        foreach ($settings as $setting)
            self::$setting_values[$setting->name] = $setting->value;
        // success
        return self::$setting_values;

    }

    public static function get_value($name)
    {
        // see if we cached the settings
        if (!isset(self::$setting_values))
            self::$setting_values = self::values();
        // fetch specific setting from cached
        return self::$setting_values[$name];

    }

    public static function categories()
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


        $categories = array();
        $current_category = null;
        // categorize each setting (group)
        foreach ($settings as $setting)
        {

            // get the category for this setting
            $setting_category = $setting->category;
            // see if it is new
            if ($setting->category != $current_category)
            {
                // create a new category
                $categories[$setting_category] = array(
                    'name' => $setting_category,
                    'settings' => array()
                );
                // set current category
                $current_category = $setting_category;
            }

            // add to categories
            $categories[$setting_category]['settings'][] = $setting;

        }

        // success
        return $categories;
    }

}
