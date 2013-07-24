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
        // get all blocks
        $view->blocks = Model_Block::all();
        // set template vars
        $this->template->title = 'Index';
        $this->template->content = $view;

    }

    public function action_create()
    {

        // posted block
        if (Input::method() == 'POST')
        {
            // create pop & save
            $block = Model_Block::forge();
            $block->populate();
            $block->save();
            // redirect
            Response::redirect('blocks');
        }

        // render create form
        $view = View::forge('blocks/form');
        // set view vars
        $view->files_finder = View::forge('files/viewer');
        $view->header = 'Create Block';
        $view->action = '/blocks/create';
        // set view vars
        $this->template->title = 'Create';
        $this->template->content = $view;

    }

    public function action_edit($id)
    {

        // fetch the block to edit
        $block = Model_Block::edit($id);
        // posted block
        if (Input::method() == 'POST')
        {
            // populate save
            $block->populate();
            $block->save();
            // redirect
            Response::redirect('blocks');
            // done
            return;
        }

        // render create form
        $view = View::forge('blocks/form');
        // set view vars
        $view->files_finder = View::forge('files/viewer');
        $view->header = 'Edit ' . $block->title;
        $view->action = '/blocks/edit/' . $block->id;
        $view->set('block', $block, false);
        // set view vars
        $this->template->title = 'Edit';
        $this->template->content = $view;

    }

    public function action_layout($id)
    {

        // posted layout
        if (Input::method() == 'POST')
        {
            // clear block items
            Model_Block::clear_items($id);
            // get block from storage
            $block = Model_Block::layout($id);
            // populate block
            $block->populate_layout();
            // save the block
            $block->save();
            // redirect
            Response::redirect('blocks');
            return;
        }
        else
        {
            // get block from storage
            $block = Model_Block::layout($id);
        }

        // create view
        $view = View::forge('blocks/layout');
        // get finders
        $view->files_finder = View::forge('files/finder');
        $view->blocks_finder = View::forge('blocks/finder');
        // get all blocks
        $view->blocks_finder->blocks = Model_Block::all($id);
        // set block
        $view->block = $block;
        // set template vars
        $this->template->title = 'Layout';
        $this->template->content = $view;

    }

    public function action_delete($id)
    {
        if ($block = Model_Block::find($id))
            $block->delete();
        Response::redirect('/blocks');
    }

    public function get_search() {

        $query = Input::get('query');
        $blocks = Model_Block::search($query);
        return $this->response($blocks);

    }

}
