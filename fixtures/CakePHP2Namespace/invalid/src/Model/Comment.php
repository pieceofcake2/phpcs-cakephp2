<?php
namespace App\Model;

use Cake\Core\App;

App::uses('AppModel', 'Model');

/**
 * Comment Model that still loads a class with App::uses()
 */
class Comment extends AppModel
{
    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = ['Post'];
}
