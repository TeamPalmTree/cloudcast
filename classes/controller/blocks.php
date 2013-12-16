<?php

class Controller_Blocks extends Controller_Cloudcast
{

    public function before()
    {
        $this->section = 'Blocks';
        parent::before();
    }

    public function action_index()
    {

        // create view
        $view = View::forge('blocks/index');
        // set template vars
        $this->template->title = 'Index';
        $this->template->section->body = $view;

    }

    public function action_create()
    {

        // render create form
        $view = View::forge('blocks/form');
        // set view vars
        $view->file_viewer = View::forge('files/viewer');
        // set view vars
        $this->template->title = 'Create';
        $this->template->section->body = $view;

    }

    public function action_edit()
    {

        // render create form
        $view = View::forge('blocks/form');
        // set view vars
        $view->file_viewer = View::forge('files/viewer');
        // set view vars
        $this->template->title = 'Edit';
        $this->template->section->body = $view;

    }

    public function action_layout()
    {

        // create view
        $view = View::forge('blocks/layout');
        // get finders
        $view->files_finder = View::forge('files/finder');
        $view->blocks_finder = View::forge('blocks/finder');
        // set template vars
        $this->template->title = 'Layout';
        $this->template->section->body = $view;

    }

    public function post_edit($id)
    {

        // get block
        $block = Model_Block::editable($id);
        // get block input
        $block_input = Input::json();
        // if we have json data, populate
        if (count($block_input) > 0)
        {
            // validate block
            if ($errors = $block->validate($block_input))
                return $this->errors_response($errors);
            // populate and save
            $block->populate($block_input);
            $block->save();
        }

        // get the editable block
        $block = Model_Block::viewable_editable($block);
        // success
        return $this->response($block);

    }

    public function post_layout($id)
    {

        // get block
        $block = Model_Block::layoutable($id);
        // get block input
        $block_input = Input::json();
        // if we have json data, populate
        if (count($block_input) > 0)
        {
            // validate block
            if ($errors = $block->validate_layout($block_input))
                return $this->errors_response($errors);
            // populate and save
            $block->populate_layout($block_input);
            $block->save();
        }

        // get the layoutable block
        $block = Model_Block::viewable_layoutable($block);
        // success
        return $this->response($block);

    }

    public function post_create()
    {

        // get block input
        $block_input = Input::json();
        // if we have json data, populate
        if (count($block_input) > 0)
        {
            // create block
            $block = Model_Block::forge();
            // validate block
            if ($errors = $block->validate($block_input))
                return $this->errors_response($errors);
            // populate and save
            $block->populate($block_input);
            $block->save();
        }
        else
        {
            // get creatable block
            $block = Model_Block::viewable_creatable();
        }

        // success
        return $this->response($block);
    }

    public function post_delete()
    {

        // get ids to delete
        $ids = Input::post('ids');
        // delete blocks
        $query = DB::delete('blocks')
            ->where('id', 'in', $ids);
        // save
        $query->execute();
        // success
        return $this->response('SUCCESS');

    }

    public function get_titles($query)
    {
        // get and return just block titles
        $titles = Model_Block::titles($query);
        return $this->response($titles);

    }

    public function get_all()
    {
        // get all blocks
        $blocks = Model_Block::viewable_all();
        // success
        return $this->response($blocks);
    }

}
