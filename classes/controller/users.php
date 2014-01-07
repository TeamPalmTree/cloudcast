<?php

class Controller_Users extends Controller_Cloudcast
{

    public function get_usernames($query)
    {
        $usernames = Model_User::usernames($query);
        return $this->response($usernames);
    }

}
