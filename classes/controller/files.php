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
        // set setter sidebar
        $view->file_setter = View::forge('files/setter');
        // set files total count
        $view->files_count = Model_File::query()
            ->where('found', '1')
            ->count();
        $view->available_files_count = Model_File::query()
            ->where('available', '1')
            ->where('found', '1')
            ->count();
        $view->unavailable_files_count = Model_File::query()
            ->where('available', '0')
            ->where('found', '1')
            ->count();
        // set template vars
        $this->template->title = 'Index';
        $this->template->content = $view;

    }

    public function get_search()
    {

        // get the query
        $query = Input::get('query');
        $restrict = (Input::get('restrict') == 'true');
        $randomize = (Input::get('randomize') == 'true');
        // search with 100 limit while randomizing and restricting genres
        $files = Model_File::searched($query, $restrict, 100, $randomize, false);
        // get viewable files
        $files = Model_File::viewable_searched($files);
        // success
        return $this->response($files);

    }

    public function get_set_post()
    {

        // get post and id of file
        $post = Input::get('post');
        $id = Input::get('id');
        // support null post
        if ($post == '')
            $post = null;
        // update post for files
        $query = DB::update('files')
            ->set(array('post' => $post))
            ->where('id', $id);
        // save
        $query->execute();
        // success
        return $this->response('SUCCESS');

    }

    public function post_set_properties()
    {

        // get posted ids and properties
        $ids = Input::json('ids');
        $properties = Input::json('properties');

        // get files for these ids
        $files = Model_File::query()->where('id', 'IN', $ids)->get();
        // loop over all files
        foreach ($files as &$file)
        {
            // convert property objects to array
            foreach ($properties as $property)
                $file->set($property['name'], $property['value']);
            // update the actual file
            TagScanner::write_file($file);
            // save to database
            $file->save();
        }

        // success
        return $this->response('SUCCESS');

    }

    public function get_scan()
    {

        ///////////
        // SETUP //
        ///////////

        // get all files in the DB
        $files = Model_File::catalog();
        // get files directory
        $files_directory = Model_Setting::get_value('files_directory');
        // get server datetime string
        $server_datetime_string = Helper::server_datetime_string();
        // get modified times of files
        $file_modified_ons = array_map(function($file) {
            return $file->modified_on;
        }, $files);

        ////////////////////////////////////////////
        // RUN TAG SCANNER, CREATE/UPDATE CATALOG //
        ////////////////////////////////////////////

        // scan directory for tags
        $scanned_files = TagScanner::scan_files($files_directory, $file_modified_ons);
        // loop over scanned files to update DB
        foreach ($scanned_files as $scanned_file_name => &$scanned_file)
        {

            ////////////////////////////////
            // CHECK FOR NO MODIFICATIONS //
            ////////////////////////////////

            if (is_null($scanned_file))
                continue;

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

            // populate file
            $file->populate_scanned($scanned_file, $server_datetime_string);
            // write file (to get it to our standards)
            TagScanner::write_file($file);
            // save file
            $file->save();

        }

        //////////////////////////////
        // UPDATE FILE AVAILABILITY //
        //////////////////////////////

        // get keys of the DB list
        $file_names = array_keys($files);
        // get keys of the scanned list
        $scanned_file_names = array_keys($scanned_files);
        // intersect the arrays to determine which DB files are found
        $found_file_names = array_intersect($file_names, $scanned_file_names);
        // diff the avail and DB files to determine unavailable files
        $lost_file_names = array_diff($file_names, $found_file_names);
        // set available files available
        foreach ($lost_file_names as &$lost_file_name)
        {
            // get the file to mark unavailable
            $file = $files[$lost_file_name];
            // see if we need to update found
            if ($file->found)
            {
                $file->found = false;
                $file->save();
            }
        }

        // send response
        return $this->response('SUCCESS');

    }

}
