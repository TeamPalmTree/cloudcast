<?php

class Controller_Files extends Controller_Cloudcast
{

    public function before()
    {
        $this->section = 'Files';
        parent::before();
    }

    public function action_index()
    {

        // create view
        $view = View::forge('files/index');
        // set files total count
        $view->files_count = Model_File::query()->count();
        // set template vars
        $this->template->title = 'Index';
        $this->template->content = $view;

    }

    public function get_activate()
    {

        // get id to search for
        $id = Input::get('id');
        // get the file
        $file = Model_File::find($id);
        // verify the file is physically there
        if (!is_file($file->name))
            return $this->response('FILE_NOT_FOUND');
        // set file available and persist
        $file->available = '1';
        $file->save();
        // success
        return $this->response('SUCCESS');

    }

    public function get_deactivate()
    {

        // get id to search for
        $id = Input::get('id');
        // get the file
        $file = Model_File::find($id);
        // set file available and persist
        $file->available = '0';
        $file->save();
        // success
        return $this->response('SUCCESS');

    }

    public function get_search()
    {

        // get the query
        $query = Input::get('query');
        $restrict = (Input::get('restrict') == 'true');
        // search with 100 limit while randomizing and restricting genres
        $files = Model_File::search($query, $restrict, 100, false, false);
        // success
        return $this->response($files);

    }

    public function get_scan($redirect = false)
    {

        ///////////
        // SETUP //
        ///////////

        // get all files in the DB
        $files = Model_File::catalog();
        // get files directory
        $files_directory = Model_Setting::get_value('files_directory');

        ////////////////////////////////////////////
        // RUN TAG SCANNER, CREATE/UPDATE CATALOG //
        ////////////////////////////////////////////

        // scan directory for tags
        $scanned_files = TagScanner::scan_files($files_directory);
        // loop over scanned files to update DB
        foreach ($scanned_files as $scanned_file_name => $scanned_file)
        {

            ///////////////////////////
            // GET EXISTING/NEW FILE //
            ///////////////////////////

            $file = null;
            // pull from existing or create new
            if (array_key_exists($scanned_file_name, $files))
                $file = $files[$scanned_file_name];
            else
                $file = Model_File::forge(array('name' => $scanned_file_name));

            ////////////////////////
            // POPULATE/SAVE FILE //
            ////////////////////////

            // populate file and save if it has changed
            if ($file->populate($scanned_file))
                $file->save();

        }

        //////////////////////////////
        // UPDATE FILE AVAILABILITY //
        //////////////////////////////

        // get keys of the DB list
        $file_names = array_keys($files);
        // get keys of the scanned list
        $scanned_file_names = array_keys($scanned_files);
        // intersect the arrays to determine which DB files are available
        $available_file_names = array_intersect($file_names, $scanned_file_names);
        // diff the avail and DB files to determine unavailable files
        $unavailable_file_names = array_diff($file_names, $available_file_names);
        // set available files available
        foreach ($unavailable_file_names as $unavailable_file_name)
        {
            // get the file to mark unavailable
            $file = $files[$unavailable_file_name];
            // see if we need to update availability
            if ($file->available)
            {
                $file->available = false;
                $file->save();
            }
        }

        /////////////////////////
        // RELOAD LS PLAYLISTS //
        /////////////////////////

        // tell LS to update its jingles, sweepers, and bumpers
        LiquidsoapHook::reload_playlists();

        /////////////
        // SUCCESS //
        /////////////

        // send response
        if ($redirect)
            Response::redirect('files');
        else
            return $this->response('SUCCESS');

    }

    public function get_genre($genre)
    {

        // query files
        $files = Model_File::query()
            ->where('genre', $genre)
            ->get();

        $file_names = array();
        // flatten files
        foreach ($files as $file)
            $file_names[] = $file->name;

        // implode file names
        $file_names = implode("\n", $file_names);
        // if we have none, return none
        if ($file_names == "")
            $file_names = "NONE";

        // send response
        return $this->response($file_names);

    }

}
