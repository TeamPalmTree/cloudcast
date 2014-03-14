<?php

/**
 * Controller for promoter user model access
 */
class Controller_Users extends Controller_Cloudcast
{

    public function get_usernames($query)
    {
        $usernames = \Promoter\Model\Promoter_User::search_usernames($query);
        return $this->response($usernames);
    }

}
