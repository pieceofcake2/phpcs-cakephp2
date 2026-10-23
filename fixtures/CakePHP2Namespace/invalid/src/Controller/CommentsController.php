<?php
namespace App\Controller;

use Cake\Utility\Hash;

/**
 * Comments Controller with a use statement that nothing uses
 */
class CommentsController extends AppController
{
    /**
     * Index action
     *
     * @return void
     */
    public function index()
    {
        $this->set('comments', $this->Comment->find('all'));
    }
}
