<?php
App::uses('AppController', 'Controller');

/**
 * Posts Controller
 *
 * @property Post $Post
 */
class PostsController extends AppController
{
    /**
     * Components
     *
     * @var array
     */
    public $components = ['Paginator', 'Flash'];

    /**
     * Admin index action
     *
     * @return void
     */
    public function admin_index()
    {
        $this->Post->recursive = 0;
        $this->set('posts', $this->Paginator->paginate());
    }

    /**
     * View action
     *
     * @param string|null $id Post id
     * @return void
     * @throws NotFoundException
     */
    public function view($id = null)
    {
        if (!$this->Post->exists($id)) {
            throw new NotFoundException(__('Invalid post'));
        }
        $this->set('post', $this->Post->findById($id));
    }
}
