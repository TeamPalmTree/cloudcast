<?php

namespace Fuel\Migrations;

class Create_Schema
{
    public function up()
    {
        // FILE

        \DBUtil::create_table('files', array(
                'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true),
                'found_on' => array('type' => 'timestamp', 'default' => '0000-00-00 00:00:00'),
                'last_play' => array('type' => 'timestamp', 'null' => true),
                'date' => array('type' => 'timestamp'),
                'available' => array('type' => 'boolean', 'default' => '1'),
                'track' => array('type' => 'smallint', 'null' => true),
                'BPM' => array('type' => 'smallint', 'null' => true),
                'rating' => array('type' => 'smallint', 'null' => true),
                'bit_rate' => array('constraint' => 11, 'type' => 'int'),
                'sample_rate' => array('constraint' => 11, 'type' => 'int'),
                'ups' => array('constraint' => 11, 'type' => 'int', 'default' => '0'),
                'downs' => array('constraint' => 11, 'type' => 'int', 'default' => '0'),
                'name' => array('constraint' => 255, 'type' => 'varchar'),
                'duration' => array('constraint' => 255, 'type' => 'varchar'),
                'title' => array('constraint' => 255, 'type' => 'varchar'),
                'album' => array('constraint' => 255, 'type' => 'varchar', 'null' => true),
                'artist' => array('constraint' => 255, 'type' => 'varchar'),
                'composer' => array('constraint' => 255, 'type' => 'varchar', 'null' => true),
                'conductor' => array('constraint' => 255, 'type' => 'varchar', 'null' => true),
                'copyright' => array('constraint' => 255, 'type' => 'varchar', 'null' => true),
                'genre' => array('constraint' => 255, 'type' => 'varchar'),
                'ISRC' => array('constraint' => 255, 'type' => 'varchar', 'null' => true),
                'label' => array('constraint' => 255, 'type' => 'varchar', 'null' => true),
                'language' => array('constraint' => 255, 'type' => 'varchar', 'null' => true),
                'mood' => array('constraint' => 255, 'type' => 'varchar', 'null' => true),
                'musical_key' => array('constraint' => 255, 'type' => 'varchar', 'null' => true),
                'energy' => array('constraint' => 255, 'type' => 'varchar', 'null' => true),
                'website' => array('constraint' => 255, 'type' => 'varchar', 'null' => true),
            ), array('id'), false, 'InnoDB', 'utf8_general_ci');

        \DBUtil::create_index('files', 'available');
        \DBUtil::create_index('files', 'name');

        // BLOCKS

        \DBUtil::create_table('blocks', array(
                'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true),
                'harmonic_key' => array('type' => 'boolean', 'default' => '1'),
                'harmonic_energy' => array('type' => 'boolean', 'default' => '1'),
                'title' => array('constraint' => 255, 'type' => 'varchar'),
                'description' => array('constraint' => 255, 'type' => 'varchar', 'null' => true),
                'file_query' => array('type' => 'text'),
            ), array('id'), false, 'InnoDB', 'utf8_general_ci');

        \DBUtil::create_table('block_weights', array(
                'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true),
                'weight' => array('constraint' => 11, 'type' => 'int', 'default' => '1'),
                'file_query' => array('type' => 'text'),
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

        // USER

        \DBUtil::create_table('users', array(
                'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true),
                'group' => array('type' => 'int', 'constraint' => 11, 'default' => 1),
                'username' => array('constraint' => 255, 'type' => 'varchar'),
                'password' => array('constraint' => 255, 'type' => 'varchar'),
                'email' => array('constraint' => 255, 'type' => 'varchar'),
                'login_hash' => array('constraint' => 255, 'type' => 'varchar'),
                'last_login' => array('type' => 'varchar', 'constraint' => 25),
                'created_at' => array('type' => 'int', 'constraint' => 11, 'default' => 0),
                'updated_at' => array('type' => 'int', 'constraint' => 11, 'default' => 0),
                'profile_fields' => array('type' => 'text'),
            ), array('id'), false, 'InnoDB', 'utf8_general_ci');

        \DB::insert('users')
            ->set(array(
                'group' => '3',
                'username' => 'admin',
                'password' => 'YWqmPGH+dOEvOh6pf83a62lzJ1QQLHRMPHhNIaohB3s=',
                'email' => 'admin@admin.com',
                'login_hash' => '4fba7e3667054d75694c2c79f3c453fd7f842ac8',
                'last_login' => '1369257335',
                'created_at' => '1369257274',
                'updated_at' => '0',
                'profile_fields' => 'a:3:{s:10:"first_name";s:5:"admin";s:9:"last_name";s:5:"admin";s:5:"phone";s:0:"";}'
            ))->execute();

        // SHOW

        \DBUtil::create_table('shows', array(
                'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true),
                'start_on' => array('type' => 'timestamp', 'default' => '0000-00-00 00:00:00'),
                'duration' => array('constraint' => 255, 'type' => 'varchar'),
                'title' => array('constraint' => 255, 'type' => 'varchar'),
                'description' => array('constraint' => 255, 'type' => 'varchar', 'null' => true),
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

        \DBUtil::create_table('shows_users', array(
                'show_id' => array('constraint' => 11, 'type' => 'int'),
                'user_id' => array('constraint' => 11, 'type' => 'int'),
            ), array(
                'show_id',
                'user_id'
            ), false, 'InnoDB', 'utf8_general_ci',
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
                    'key' => 'user_id',
                    'reference' => array(
                        'table' => 'users',
                        'column' => 'id',
                    ),
                ),
            )
        );

        \DBUtil::create_table('schedules', array(
                'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true),
                'start_on' => array('type' => 'timestamp', 'default' => '0000-00-00 00:00:00'),
                'end_at' => array('type' => 'timestamp', 'default' => '0000-00-00 00:00:00'),
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

        \DBUtil::create_table('schedule_files', array(
                'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true),
                'played_on' => array('type' => 'timestamp', 'null' => true),
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

        // STREAMS

        \DBUtil::create_table('streams', array(
                'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true),
                'type' => array('type' => 'int', 'constraint' => 11),
                'port' => array('constraint' => 11, 'type' => 'int', 'null' => true),
                'active' => array('type' => 'boolean', 'default' => '1'),
                'name' => array('constraint' => 255, 'type' => 'varchar'),
                'host' => array('constraint' => 255, 'type' => 'varchar', 'null' => true),
                'source_username' => array('constraint' => 255, 'type' => 'varchar', 'null' => true),
                'source_password' => array('constraint' => 255, 'type' => 'varchar', 'null' => true),
                'admin_username' => array('constraint' => 255, 'type' => 'varchar', 'null' => true),
                'admin_password' => array('constraint' => 255, 'type' => 'varchar', 'null' => true),
                'mount' => array('constraint' => 255, 'type' => 'varchar', 'null' => true),
            ), array('id'), false, 'InnoDB', 'utf8_general_ci');

        \DB::insert('streams')
            ->set(array(
                'name' => 'Development',
                'type' => '0',
            ))->execute();

        // STREAM STATISTICS

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

        // INPUTS

        \DBUtil::create_table('inputs', array(
                'name' => array('constraint' => 255, 'type' => 'varchar'),
                'status' => array('type' => 'boolean', 'default' => '0'),
                'enabled' => array('type' => 'boolean', 'default' => '0'),
                'user_id' => array('constraint' => 11, 'type' => 'int', 'null' => true),
            ), array('name'), false, 'InnoDB', 'utf8_general_ci',
            array(
                array(
                    'key' => 'user_id',
                    'reference' => array(
                        'table' => 'user',
                        'column' => 'id',
                    ),
                    'on_delete' => 'CASCADE',
                ),
            )
        );

        \DB::insert('inputs')
            ->set(array(
                'name' => 'schedule',
                'status' => '0',
                'enabled' => '0',
            ))->execute();

        \DB::insert('inputs')
            ->set(array(
                'name' => 'show',
                'status' => '0',
                'enabled' => '0',
            ))->execute();

        \DB::insert('inputs')
            ->set(array(
                'name' => 'talkover',
                'status' => '0',
                'enabled' => '0',
            ))->execute();

        \DB::insert('inputs')
            ->set(array(
                'name' => 'master',
                'status' => '0',
                'enabled' => '0',
            ))->execute();

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
                'name' => 'schedule_out_days',
                'type' => 'text',
                'value' => '20',
                'category' => 'general'
            ))->execute();

        \DB::insert('settings')
            ->set(array(
                'name' => 'files_directory',
                'type' => 'text',
                'value' => "F:\\DropFolder\\GDM\\",
                'category' => 'general'
            ))->execute();

        \DB::insert('settings')
            ->set(array(
                'name' => 'advertisement_genre',
                'type' => 'text',
                'value' => 'Advertisement',
                'category' => 'genres'
            ))->execute();

        \DB::insert('settings')
            ->set(array(
                'name' => 'jingle_genre',
                'type' => 'text',
                'value' => 'Jingle',
                'category' => 'genres'
            ))->execute();

        \DB::insert('settings')
            ->set(array(
                'name' => 'sweeper_genre',
                'type' => 'text',
                'value' => 'Sweeper',
                'category' => 'genres'
            ))->execute();

        \DB::insert('settings')
            ->set(array(
                'name' => 'bumper_genre',
                'type' => 'text',
                'value' => 'Bumper',
                'category' => 'genres'
            ))->execute();

        \DB::insert('settings')
            ->set(array(
                'name' => 'transition_fade_seconds',
                'type' => 'text',
                'value' => '5.0',
                'category' => 'transitions'
            ))->execute();

        \DB::insert('settings')
            ->set(array(
                'name' => 'transition_seconds',
                'type' => 'text',
                'value' => '6.0',
                'category' => 'transitions'
            ))->execute();

        \DB::insert('settings')
            ->set(array(
                'name' => 'transition_width_seconds',
                'type' => 'text',
                'value' => '2.0',
                'category' => 'transitions'
            ))->execute();

        \DB::insert('settings')
            ->set(array(
                'name' => 'transition_power_margin',
                'type' => 'text',
                'value' => '4.0',
                'category' => 'transitions'
            ))->execute();

        \DB::insert('settings')
            ->set(array(
                'name' => 'transition_high_power',
                'type' => 'text',
                'value' => '-15.0',
                'category' => 'transitions'
            ))->execute();

        \DB::insert('settings')
            ->set(array(
                'name' => 'transition_medium_power',
                'type' => 'text',
                'value' => '-32.0',
                'category' => 'transitions'
            ))->execute();

        \DB::insert('settings')
            ->set(array(
                'name' => 'jingle_quiet_threshold',
                'type' => 'text',
                'value' => '-15.0',
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
                'value' => '1.0',
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
                'value' => '20.0',
                'category' => 'jingles'
            ))->execute();

        \DB::insert('settings')
            ->set(array(
                'name' => 'show_input_port',
                'type' => 'text',
                'value' => '10000',
                'category' => 'inputs'
            ))->execute();

        \DB::insert('settings')
            ->set(array(
                'name' => 'talkover_input_port',
                'type' => 'text',
                'value' => '11000',
                'category' => 'inputs'
            ))->execute();

        \DB::insert('settings')
            ->set(array(
                'name' => 'master_input_port',
                'type' => 'text',
                'value' => '12000',
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
                'value' => '-40.0',
                'category' => 'talkover'
            ))->execute();

    }

    public function down()
    {
        \DBUtil::drop_table('settings');
        \DBUtil::drop_table('inputs');
        \DBUtil::drop_table('stream_statistics');
        \DBUtil::drop_table('streams');
        \DBUtil::drop_table('schedule_files');
        \DBUtil::drop_table('schedules');
        \DBUtil::drop_table('shows_users');
        \DBUtil::drop_table('show_repeats');
        \DBUtil::drop_table('shows');
        \DBUtil::drop_table('users');
        \DBUtil::drop_table('block_items');
        \DBUtil::drop_table('blocks');
        \DBUtil::drop_table('files');
    }
}