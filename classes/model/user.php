<?php

class Model_User extends \Orm\Model
{

    protected static $_properties = array(
        'id',
        'last_login',
        'username',
        'password',
        'email',
        'group',
        'login_hash',
        'profile_fields',
    );

    protected static $_many_many = array(
        'shows',
    );

    public static function auth_create()
    {

        // get user from post data
        $username = Input::post('username');
        $password = Input::post('password');
        $group = Input::post('group');
        $email = Input::post('email');
        // get profile data
        $profile_fields = array(
            'first_name' => Input::post('first_name'),
            'last_name' => Input::post('last_name'),
            'phone' => Input::post('phone'),
        );

        // create user
        Auth::create_user(
            $username,
            $password,
            $email,
            $group,
            $profile_fields
        );

    }

    public static function auth_update($username)
    {

        // get other user values
        $values = array(
            'group' => Input::post('group'),
            'email' => Input::post('email'),
            'phone' => Input::post('phone'),
            'first_name' => Input::post('first_name'),
            'last_name' => Input::post('last_name'),
        );

        // changing password?
        if (Input::post('password'))
        {
            $values['password'] = Input::post('password');
            $values['old_password'] = Input::post('old_password');
        }

        // update user
        Auth::update_user($values, $username);

    }

    public static function auth_delete($username)
    {
        Auth::delete_user($username);
    }

    public static function edit($username)
    {

        // get user
        $user = Model_User::query()
            ->select('last_login', 'username', 'email', 'group', 'profile_fields')
            ->where('username', $username)
            ->get_one();
        // set profile fields
        $user->profile_fields = unserialize($user->profile_fields);
        // success
        return $user;

    }

    public static function display()
    {

        // get all users
        $users = Model_User::query()
            ->select('last_login', 'username', 'email', 'group', 'profile_fields')
            ->get();

        $display_users = array();
        // move properties to parent
        foreach ($users as $user)
        {
            // convert timestamp to server datetime
            $userLastLogin = new DateTime();
            $userLastLogin->setTimestamp($user->last_login);
            // unserialize profile fields
            $profile_fields = unserialize($user->profile_fields);
            // create display user
            $display_user = array(
                'id' => $user->id,
                'user_last_login' => Helper::server_datetime_to_user_datetime_string($userLastLogin),
                'username' => $user->username,
                'email' => $user->email,
                'first_name' => isset($profile_fields['first_name']) ? $profile_fields['first_name'] : null,
                'last_name' => isset($profile_fields['last_name']) ? $profile_fields['last_name'] : null,
                'phone' => isset($profile_fields['phone']) ? $profile_fields['phone'] : null,
            );

            // get user groups
            Config::load('simpleauth');
            $groups = Config::get('simpleauth.groups');
            // set group
            $display_user['group_name'] = $groups[$user->group]['name'];
            // add to array of users
            $display_users[] = $display_user;
        }

        // success
        return $display_users;

    }

    public static function search($query)

    {
        $users = Model_User::query()
            ->select('username')
            ->where('username', 'LIKE', $query . '%')
            ->get();
        return Helper::extract_values('username', $users);

    }

}
