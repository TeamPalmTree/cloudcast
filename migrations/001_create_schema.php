<?php

namespace Fuel\Migrations;

class Create_Schema
{
    public function up()
    {
        ///////////
        // FILES //
        ///////////

        // files
        \DBUtil::create_table('files', array(
                'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true),
                'found_on' => array('type' => 'timestamp', 'default' => '0000-00-00 00:00:00'),
                'last_played' => array('type' => 'timestamp', 'null' => true),
                'last_scheduled' => array('type' => 'timestamp', 'null' => true),
                'date' => array('type' => 'timestamp'),
                'available' => array('type' => 'boolean', 'default' => '1'),
                'found' => array('type' => 'boolean', 'default' => '1'),
                'BPM' => array('type' => 'smallint', 'null' => true),
                'rating' => array('type' => 'smallint', 'null' => true),
                'relevance' => array('type' => 'smallint', 'null' => true),
                'modified_on' => array('type' => 'int', 'constraint' => 11, 'default' => 0),
                'bit_rate' => array('constraint' => 11, 'type' => 'int'),
                'sample_rate' => array('constraint' => 11, 'type' => 'int'),
                'ups' => array('constraint' => 11, 'type' => 'int', 'default' => '0'),
                'downs' => array('constraint' => 11, 'type' => 'int', 'default' => '0'),
                'name' => array('constraint' => 255, 'type' => 'varchar'),
                'duration' => array('constraint' => 255, 'type' => 'varchar'),
                'post' => array('constraint' => 255, 'type' => 'varchar', 'null' => true),
                'title' => array('constraint' => 255, 'type' => 'varchar'),
                'album' => array('constraint' => 255, 'type' => 'varchar', 'null' => true),
                'artist' => array('constraint' => 255, 'type' => 'varchar'),
                'composer' => array('constraint' => 255, 'type' => 'varchar', 'null' => true),
                'conductor' => array('constraint' => 255, 'type' => 'varchar', 'null' => true),
                'copyright' => array('constraint' => 255, 'type' => 'varchar', 'null' => true),
                'genre' => array('constraint' => 255, 'type' => 'varchar'),
                'ISRC' => array('constraint' => 255, 'type' => 'varchar', 'null' => true),
                'language' => array('constraint' => 255, 'type' => 'varchar', 'null' => true),
                'key' => array('constraint' => 255, 'type' => 'varchar', 'null' => true),
                'energy' => array('constraint' => 255, 'type' => 'varchar', 'null' => true),
            ), array('id'), false, 'InnoDB', 'utf8_general_ci');

        // files indexes
        \DBUtil::create_index('files', 'date');
        \DBUtil::create_index('files', 'available');
        \DBUtil::create_index('files', 'found');
        \DBUtil::create_index('files', 'modified_on');
        \DBUtil::create_index('files', 'name', null, 'unique');

        ////////////
        // BLOCKS //
        ////////////

        // blocks
        \DBUtil::create_table('blocks', array(
                'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true),
                'harmonic_key' => array('constraint' => 1, 'type' => 'tinyint', 'default' => '1'),
                'harmonic_energy' => array('constraint' => 1, 'type' => 'tinyint', 'default' => '1'),
                'harmonic_genre' => array('constraint' => 1, 'type' => 'tinyint', 'default' => '1'),
                'separate_artists' => array('constraint' => 1, 'type' => 'tinyint', 'default' => '1'),
                'separate_titles' => array('constraint' => 1, 'type' => 'tinyint', 'default' => '1'),
                'title' => array('constraint' => 255, 'type' => 'varchar'),
                'description' => array('constraint' => 255, 'type' => 'varchar', 'null' => true),
                'initial_key' => array('constraint' => 255, 'type' => 'varchar', 'null' => true),
                'initial_energy' => array('constraint' => 255, 'type' => 'varchar', 'null' => true),
                'initial_genre' => array('constraint' => 255, 'type' => 'varchar', 'null' => true),
                'file_query' => array('type' => 'text'),
                'backup_block_id' => array('constraint' => 11, 'type' => 'int', 'null' => true),
            ), array('id'), false, 'InnoDB', 'utf8_general_ci',
            array(
                array(
                    'key' => 'backup_block_id',
                    'reference' => array(
                        'table' => 'blocks',
                        'column' => 'id',
                    ),
                    'on_delete' => 'CASCADE',
                ),
            )
        );

        // blocks indexes
        \DBUtil::create_index('blocks', 'title', null, 'unique');

        // block weights
        \DBUtil::create_table('block_weights', array(
                'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true),
                'weight' => array('constraint' => 11, 'type' => 'int', 'default' => '1'),
                'file_query' => array('type' => 'text', 'null' => true),
                'block_id' => array('constraint' => 11, 'type' => 'int'),
            ), array('id'), false, 'InnoDB', 'utf8_general_ci',
            array(
                array(
                    'key' => 'block_id',
                    'reference' => array(
                        'table' => 'blocks',
                        'column' => 'id',
                    ),
                    'on_delete' => 'CASCADE',
                ),
            )
        );

        // block items
        \DBUtil::create_table('block_items', array(
                'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true),
                'percentage' => array('type' => 'smallint', 'null' => true),
                'duration' => array('constraint' => 255, 'type' => 'varchar', 'null' => true),
                'block_id' => array('constraint' => 11, 'type' => 'int'),
                'file_id' => array('constraint' => 11, 'type' => 'int', 'null' => true),
                'child_block_id' => array('constraint' => 11, 'type' => 'int', 'null' => true),
            ), array('id'), false, 'InnoDB', 'utf8_general_ci',
            array(
                array(
                    'key' => 'block_id',
                    'reference' => array(
                        'table' => 'blocks',
                        'column' => 'id',
                    ),
                    'on_delete' => 'CASCADE',
                ),
                array(
                    'key' => 'file_id',
                    'reference' => array(
                        'table' => 'files',
                        'column' => 'id',
                    ),
                    'on_delete' => 'CASCADE',
                ),
                array(
                    'key' => 'child_block_id',
                    'reference' => array(
                        'table' => 'blocks',
                        'column' => 'id',
                    ),
                    'on_delete' => 'CASCADE',
                ),
            )
        );

        // block harmonics
        \DBUtil::create_table('block_harmonics', array(
                'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true),
                'harmonic_name' => array('constraint' => 255, 'type' => 'varchar'),
                'block_id' => array('constraint' => 11, 'type' => 'int'),
            ), array('id'), false, 'InnoDB', 'utf8_general_ci',
            array(
                array(
                    'key' => 'block_id',
                    'reference' => array(
                        'table' => 'blocks',
                        'column' => 'id',
                    ),
                    'on_delete' => 'CASCADE',
                ),
            )
        );

        ////////////
        // INPUTS //
        ////////////

        // inputs
        \DBUtil::create_table('inputs', array(
                'name' => array('constraint' => 255, 'type' => 'varchar'),
                'status' => array('type' => 'boolean', 'default' => '0'),
                'enabled' => array('type' => 'boolean', 'default' => '0'),
                'user_id' => array('constraint' => 11, 'type' => 'int', 'null' => true),
            ), array('name'), false, 'InnoDB', 'utf8_general_ci');

        // inputs indexes
        \DBUtil::create_index('inputs', 'name', null, 'unique');
        \DBUtil::create_index('inputs', 'status');
        \DBUtil::create_index('inputs', 'enabled');

        // inputs data
        \DB::insert('inputs')
            ->set(array(
                'name' => 'schedule',
                'status' => '0',
                'enabled' => '1',
            ))->execute();

        \DB::insert('inputs')
            ->set(array(
                'name' => 'show',
                'status' => '0',
                'enabled' => '1',
            ))->execute();

        \DB::insert('inputs')
            ->set(array(
                'name' => 'talkover',
                'status' => '0',
                'enabled' => '1',
            ))->execute();

        \DB::insert('inputs')
            ->set(array(
                'name' => 'master',
                'status' => '0',
                'enabled' => '1',
            ))->execute();

        ///////////
        // SHOWS //
        ///////////

        // shows
        \DBUtil::create_table('shows', array(
                'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true),
                'start_on' => array('type' => 'timestamp', 'default' => '0000-00-00 00:00:00'),
                'available' => array('type' => 'boolean', 'default' => '1'),
                'ups' => array('constraint' => 11, 'type' => 'int', 'default' => '0'),
                'downs' => array('constraint' => 11, 'type' => 'int', 'default' => '0'),
                'sweeper_interval' => array('constraint' => 11, 'type' => 'int', 'null' => true),
                'duration' => array('constraint' => 255, 'type' => 'varchar'),
                'title' => array('constraint' => 255, 'type' => 'varchar'),
                'website' => array('constraint' => 255, 'type' => 'varchar', 'null' => true),
                'sweepers_album' => array('constraint' => 255, 'type' => 'varchar', 'null' => true),
                'jingles_album' => array('constraint' => 255, 'type' => 'varchar', 'null' => true),
                'bumpers_album' => array('constraint' => 255, 'type' => 'varchar', 'null' => true),
                'intros_album' => array('constraint' => 255, 'type' => 'varchar', 'null' => true),
                'closers_album' => array('constraint' => 255, 'type' => 'varchar', 'null' => true),
                'description' => array('type' => 'text'),
                'block_id' => array('constraint' => 11, 'type' => 'int', 'null' => true),
            ), array('id'), false, 'InnoDB', 'utf8_general_ci',
            array(
                array(
                    'key' => 'block_id',
                    'reference' => array(
                        'table' => 'blocks',
                        'column' => 'id',
                    ),
                    'on_delete' => 'SET NULL',
                ),
            )
        );

        // shows indexes
        \DBUtil::create_index('shows', 'start_on');
        \DBUtil::create_index('shows', 'available');
        \DBUtil::create_index('shows', 'title');

        // show repeats
        \DBUtil::create_table('show_repeats', array(
                'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true),
                'end_on' => array('type' => 'timestamp', 'null' => true),
                'Sunday' => array('type' => 'boolean'),
                'Monday' => array('type' => 'boolean'),
                'Tuesday' => array('type' => 'boolean'),
                'Wednesday' => array('type' => 'boolean'),
                'Thursday' => array('type' => 'boolean'),
                'Friday' => array('type' => 'boolean'),
                'Saturday' => array('type' => 'boolean'),
                'show_id' => array('constraint' => 11, 'type' => 'int'),
            ), array('id'), false, 'InnoDB', 'utf8_general_ci',
            array(
                array(
                    'key' => 'show_id',
                    'reference' => array(
                        'table' => 'shows',
                        'column' => 'id',
                    ),
                    'on_delete' => 'CASCADE',
                ),
            )
        );

        // show repeats indexes
        \DBUtil::create_index('show_repeats', 'end_on');

        // show users
        \DBUtil::create_table('show_users', array(
                'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true),
                'show_id' => array('constraint' => 11, 'type' => 'int'),
                'user_id' => array('constraint' => 11, 'type' => 'int'),
                'input_name' => array('constraint' => 255, 'type' => 'varchar'),
            ), array('id'), false, 'InnoDB', 'utf8_general_ci',
            array(
                array(
                    'key' => 'show_id',
                    'reference' => array(
                        'table' => 'shows',
                        'column' => 'id',
                    ),
                    'on_delete' => 'CASCADE',
                ),
                array(
                    'key' => 'input_name',
                    'reference' => array(
                        'table' => 'inputs',
                        'column' => 'name',
                    ),
                ),
            )
        );

        // show users indexes
        \DBUtil::create_index('show_users', 'user_id');

        ///////////////
        // SCHEDULES //
        ///////////////

        // schedules
        \DBUtil::create_table('schedules', array(
                'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true),
                'start_on' => array('type' => 'timestamp', 'default' => '0000-00-00 00:00:00'),
                'end_at' => array('type' => 'timestamp', 'default' => '0000-00-00 00:00:00'),
                'available' => array('type' => 'boolean', 'default' => '1'),
                'ups' => array('constraint' => 11, 'type' => 'int', 'default' => '0'),
                'downs' => array('constraint' => 11, 'type' => 'int', 'default' => '0'),
                'show_id' => array('constraint' => 11, 'type' => 'int'),
            ), array('id'), false, 'InnoDB', 'utf8_general_ci',
            array(
                array(
                    'key' => 'show_id',
                    'reference' => array(
                        'table' => 'shows',
                        'column' => 'id',
                    ),
                    'on_delete' => 'CASCADE',
                ),
            )
        );

        // schedules indexes
        \DBUtil::create_index('schedules', 'start_on');
        \DBUtil::create_index('schedules', 'end_at');
        \DBUtil::create_index('schedules', 'available');

        // schedule files
        \DBUtil::create_table('schedule_files', array(
                'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true),
                'played_on' => array('type' => 'timestamp', 'null' => true),
                'queued_on' => array('type' => 'timestamp', 'null' => true),
                'skipped_on' => array('type' => 'timestamp', 'null' => true),
                'ups' => array('constraint' => 11, 'type' => 'int', 'default' => '0'),
                'downs' => array('constraint' => 11, 'type' => 'int', 'default' => '0'),
                'schedule_id' => array('constraint' => 11, 'type' => 'int'),
                'file_id' => array('constraint' => 11, 'type' => 'int'),
            ), array('id'), false, 'InnoDB', 'utf8_general_ci',
            array(
                array(
                    'key' => 'schedule_id',
                    'reference' => array(
                        'table' => 'schedules',
                        'column' => 'id',
                    ),
                    'on_delete' => 'CASCADE',
                ),
                array(
                    'key' => 'file_id',
                    'reference' => array(
                        'table' => 'files',
                        'column' => 'id',
                    ),
                    'on_delete' => 'CASCADE',
                ),
            )
        );

        // schedule files indexes
        \DBUtil::create_index('schedule_files', 'played_on');
        \DBUtil::create_index('schedule_files', 'queued_on');
        \DBUtil::create_index('schedule_files', 'skipped_on');

        ///////////
        // VOTES //
        ///////////

        // votes
        \DBUtil::create_table('votes', array(
                'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true),
                'vote_cast' => array('type' => 'timestamp', 'default' => '0000-00-00 00:00:00'),
                'vote' => array('constraint' => 1, 'type' => 'tinyint', 'default' => '0'),
                'ip_address' => array('constraint' => 255, 'type' => 'varchar'),
                'schedule_file_id' => array('constraint' => 11, 'type' => 'int'),
            ), array('id'), false, 'InnoDB', 'utf8_general_ci',
            array(
                array(
                    'key' => 'schedule_file_id',
                    'reference' => array(
                        'table' => 'schedule_files',
                        'column' => 'id',
                    ),
                    'on_delete' => 'CASCADE',
                ),
            )
        );

        // votes indexes
        \DBUtil::create_index('votes', array('ip_address', 'schedule_file_id'), null, 'unique');

        /////////////
        // STREAMS //
        /////////////

        // streams
        \DBUtil::create_table('streams', array(
                'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true),
                'type' => array('constraint' => 255, 'type' => 'varchar'),
                'port' => array('constraint' => 11, 'type' => 'int', 'null' => true),
                'active' => array('type' => 'boolean', 'default' => '1'),
                'name' => array('constraint' => 255, 'type' => 'varchar'),
                'host' => array('constraint' => 255, 'type' => 'varchar', 'null' => true),
                'format' => array('constraint' => 255, 'type' => 'varchar', 'null' => true),
                'source_username' => array('constraint' => 255, 'type' => 'varchar', 'null' => true),
                'source_password' => array('constraint' => 255, 'type' => 'varchar', 'null' => true),
                'admin_username' => array('constraint' => 255, 'type' => 'varchar', 'null' => true),
                'admin_password' => array('constraint' => 255, 'type' => 'varchar', 'null' => true),
                'mount' => array('constraint' => 255, 'type' => 'varchar', 'null' => true),
            ), array('id'), false, 'InnoDB', 'utf8_general_ci');

        // streams indexes
        \DBUtil::create_index('streams', 'name', null, 'unique');
        \DBUtil::create_index('streams', 'active');

        // streams data
        \DB::insert('streams')
            ->set(array(
                'name' => 'Development',
                'type' => '0',
            ))->execute();

        // stream statistics
        \DBUtil::create_table('stream_statistics', array(
                'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true),
                'captured_on' => array('type' => 'timestamp'),
                'listeners' => array('constraint' => 11, 'type' => 'int'),
                'schedule_file_id' => array('constraint' => 11, 'type' => 'int'),
                'stream_id' => array('constraint' => 11, 'type' => 'int'),
            ), array('id'), false, 'InnoDB', 'utf8_general_ci',
            array(
                array(
                    'key' => 'schedule_file_id',
                    'reference' => array(
                        'table' => 'schedule_files',
                        'column' => 'id',
                    ),
                    'on_delete' => 'CASCADE',
                ),
                array(
                    'key' => 'stream_id',
                    'reference' => array(
                        'table' => 'streams',
                        'column' => 'id',
                    ),
                    'on_delete' => 'CASCADE',
                ),
            )
        );

        // SETTINGS

        \DBUtil::create_table('settings', array(
                'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true),
                'name' => array('constraint' => 255, 'type' => 'varchar'),
                'type' => array('constraint' => 255, 'type' => 'varchar'),
                'value' => array('constraint' => 255, 'type' => 'varchar', 'null' => true),
                'category' => array('constraint' => 255, 'type' => 'varchar'),
            ), array('id'), false, 'InnoDB', 'utf8_general_ci');

        \DBUtil::create_index('settings', 'name', null, 'unique');

        \DB::insert('settings')
            ->set(array(
                'name' => 'station_name',
                'type' => 'text',
                'value' => 'CloudCast',
                'category' => 'general'
            ))->execute();

        \DB::insert('settings')
            ->set(array(
                'name' => 'files_directory',
                'type' => 'text',
                'value' => "E:\\CloudCast\\prepared",
                'category' => 'general'
            ))->execute();

        \DB::insert('settings')
            ->set(array(
                'name' => 'loop_file',
                'type' => 'text',
                'value' => "E:\\CloudCast\\loops\\loop.mp3",
                'category' => 'general'
            ))->execute();

        \DB::insert('settings')
            ->set(array(
                'name' => 'schedule_out_days',
                'type' => 'text',
                'value' => '2',
                'category' => 'scheduling'
            ))->execute();

        \DB::insert('settings')
            ->set(array(
                'name' => 'popularity_days',
                'type' => 'text',
                'value' => "7",
                'category' => 'scheduling'
            ))->execute();

        \DB::insert('settings')
            ->set(array(
                'name' => 'separate_artists_count',
                'type' => 'text',
                'value' => "5",
                'category' => 'scheduling'
            ))->execute();

        \DB::insert('settings')
            ->set(array(
                'name' => 'separate_titles_count',
                'type' => 'text',
                'value' => "10",
                'category' => 'scheduling'
            ))->execute();

        \DB::insert('settings')
            ->set(array(
                'name' => 'transition_cross_seconds',
                'type' => 'text',
                'value' => '1.0',
                'category' => 'transitions'
            ))->execute();

        \DB::insert('settings')
            ->set(array(
                'name' => 'transition_fade_seconds',
                'type' => 'text',
                'value' => '3.5',
                'category' => 'transitions'
            ))->execute();

        \DB::insert('settings')
            ->set(array(
                'name' => 'transition_delay_seconds',
                'type' => 'text',
                'value' => '1.0',
                'category' => 'transitions'
            ))->execute();

        \DB::insert('settings')
            ->set(array(
                'name' => 'jingle_quiet_threshold',
                'type' => 'text',
                'value' => '-20.0',
                'category' => 'jingles'
            ))->execute();

        \DB::insert('settings')
            ->set(array(
                'name' => 'jingle_quiet_seconds',
                'type' => 'text',
                'value' => '4.0',
                'category' => 'jingles'
            ))->execute();

        \DB::insert('settings')
            ->set(array(
                'name' => 'jingle_noise_seconds',
                'type' => 'text',
                'value' => '2.0',
                'category' => 'jingles'
            ))->execute();

        \DB::insert('settings')
            ->set(array(
                'name' => 'jingle_start_delay_seconds',
                'type' => 'text',
                'value' => '30.0',
                'category' => 'jingles'
            ))->execute();

        \DB::insert('settings')
            ->set(array(
                'name' => 'jingle_end_padding_seconds',
                'type' => 'text',
                'value' => '30.0',
                'category' => 'jingles'
            ))->execute();

        \DB::insert('settings')
            ->set(array(
                'name' => 'jingle_ready_delay_seconds',
                'type' => 'text',
                'value' => '120.0',
                'category' => 'jingles'
            ))->execute();

        \DB::insert('settings')
            ->set(array(
                'name' => 'show_input_port',
                'type' => 'text',
                'value' => '12000',
                'category' => 'inputs'
            ))->execute();

        \DB::insert('settings')
            ->set(array(
                'name' => 'talkover_input_port',
                'type' => 'text',
                'value' => '12001',
                'category' => 'inputs'
            ))->execute();

        \DB::insert('settings')
            ->set(array(
                'name' => 'master_input_port',
                'type' => 'text',
                'value' => '12002',
                'category' => 'inputs'
            ))->execute();

        \DB::insert('settings')
            ->set(array(
                'name' => 'input_transition_fade_seconds',
                'type' => 'text',
                'value' => '5.0',
                'category' => 'inputs'
            ))->execute();

        \DB::insert('settings')
            ->set(array(
                'name' => 'talkover_transition_fade_seconds',
                'type' => 'text',
                'value' => '0.2',
                'category' => 'talkover'
            ))->execute();

        \DB::insert('settings')
            ->set(array(
                'name' => 'talkover_radio_amplification',
                'type' => 'text',
                'value' => '0.3',
                'category' => 'talkover'
            ))->execute();

        \DB::insert('settings')
            ->set(array(
                'name' => 'talkover_quiet_seconds',
                'type' => 'text',
                'value' => '1.0',
                'category' => 'talkover'
            ))->execute();

        \DB::insert('settings')
            ->set(array(
                'name' => 'talkover_noise_seconds',
                'type' => 'text',
                'value' => '0.0',
                'category' => 'talkover'
            ))->execute();

        \DB::insert('settings')
            ->set(array(
                'name' => 'talkover_quiet_threshold',
                'type' => 'text',
                'value' => '-25.0',
                'category' => 'talkover'
            ))->execute();

    }

    public function down()
    {
        \DBUtil::drop_table('settings');
        \DBUtil::drop_table('stream_statistics');
        \DBUtil::drop_table('streams');
        \DBUtil::drop_table('votes');
        \DBUtil::drop_table('schedule_files');
        \DBUtil::drop_table('schedules');
        \DBUtil::drop_table('show_users');
        \DBUtil::drop_table('show_repeats');
        \DBUtil::drop_table('shows');
        \DBUtil::drop_table('inputs');
        \DBUtil::drop_table('block_harmonics');
        \DBUtil::drop_table('block_items');
        \DBUtil::drop_table('block_weights');
        \DBUtil::drop_table('blocks');
        \DBUtil::drop_table('harmonics');
        \DBUtil::drop_table('files');
    }
}