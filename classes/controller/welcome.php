<?php
/**
 * Fuel is a fast, lightweight, community driven PHP5 framework.
 *
 * @package    Fuel
 * @version    1.5
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2013 Fuel Development Team
 * @link       http://fuelphp.com
 */

/**
 * The Welcome Controller.
 *
 * A basic controller example.  Has examples of how to set the
 * response body and status.
 *
 * @package  app
 * @extends  Controller
 */
class Controller_Welcome extends Controller_Template
{

	/**
	 * The basic welcome message
	 *
	 * @access  public
	 * @return  Response
	 */
	public function action_index()
	{
		return Response::forge(View::forge('welcome/index'));
	}

	/**
	 * A typical "Hello, Bob!" type example.  This uses a ViewModel to
	 * show how to use them.
	 *
	 * @access  public
	 * @return  Response
	 */
	public function action_login()
	{
        // if we got login data
        if (Input::post())
        {
            // check credentials
            if (Auth::login())
            {
                // Credentials ok, go right in.
                Response::redirect('schedules');
                return;
            }
            else
            {
                // Oops, no soup for you. Try to login again. Set some values to
                // repopulate the username field and give some error text back to the view.
                $data['username']    = Input::post('username');
                $data['login_error'] = 'Wrong username/password combo. Try again';
            }
        }

        // Credentials ok, go right in.
        Response::redirect('');
	}

	/**
	 * The 404 action for the application.
	 *
	 * @access  public
	 * @return  Response
	 */
	public function action_404()
	{
		return Response::forge(ViewModel::forge('welcome/404'), 404);
	}
}
