<?php

class Controller_Users extends Controller_Cloudcast
{

    public function before()
    {
        $this->section = 'Users';
        parent::before();
    }

    public function action_logout()
    {
        // logout user
        Auth::logout();
        // redirect to login
        Response::redirect();
    }

    public function action_index()
    {

        // create view
        $view = View::forge('users/index');
        // get all users
        $view->users = Model_User::display();
        // set template vars
        $this->template->title = 'Index';
        $this->template->content = $view;

    }

    public function action_create()
    {

        // posted user
        if (Input::method() == 'POST')
        {
            // auth create
            Model_User::auth_create();
            // redirect
            Response::redirect('users');
        }

        // get user groups
        Config::load('simpleauth');
        $groups = Config::get('simpleauth.groups');
        // render create form
        $view = View::forge('users/form');
        $view->groups = $groups;
        // set view vars
        $this->template->title = 'Create';
        $this->template->content = $view;

    }

    public function action_edit($username)
    {

        // posted show
        if (Input::method() == 'POST')
        {
            // auth update
            Model_User::auth_update($username);
            // redirect
            Response::redirect('users');
            // done
            return;
        }

        // fetch the user to edit
        $user = Model_User::edit($username);
        // get user groups
        Config::load('simpleauth');
        $groups = Config::get('simpleauth.groups');
        // render create form
        $view = View::forge('users/form');
        $view->user = $user;
        $view->groups = $groups;
        // set view vars
        $this->template->title = 'Edit';
        $this->template->content = $view;

    }

    public function action_delete($username)
    {
        // auth delete
        Model_User::auth_delete($username);
        // redirect
        Response::redirect('/users');
    }

    public function get_usernames($query)
    {
        $usernames = Model_User::usernames($query);
        return $this->response($usernames);
    }

}
