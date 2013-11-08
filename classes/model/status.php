<?php

class Model_Status extends \Model
{

    // current file
    public $current_file_id;
    public $current_file_artist;
    public $current_file_title;
    public $current_file_duration;
    public $current_file_post;
    // next file
    public $next_file_artist;
    public $next_file_title;
    public $next_file_post;
    public $next_file_duration;
    // current show
    public $current_show_title;
    public $current_show_duration;
    // next show
    public $next_show_title;
    // current schedule
    public $current_client_schedule_start_on;
    // current schedule file
    public $current_client_schedule_file_played_on;
    // host
    public $host_username;
    // server
    public $client_generated_on;
    // input statuses
    public $schedule_input_active;
    public $show_input_active;
    public $talkover_input_active;
    public $master_input_active;
    // input enabled
    public $schedule_input_enabled;
    public $show_input_enabled;
    public $talkover_input_enabled;
    public $master_input_enabled;
    // input usernames
    public $show_input_username;
    public $talkover_input_username;
    public $master_input_username;

}