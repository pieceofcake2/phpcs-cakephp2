<?php
namespace App\Model;

use Exception;

/**
 * Post Model
 *
 * @property User $User
 */
class Post extends AppModel
{
    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = ['User'];

    /**
     * Publish a post
     *
     * @param int $id Post id
     * @return bool
     * @throws Exception When the post does not exist
     */
    public function publish($id)
    {
        if (!$this->exists($id)) {
            throw new Exception('Missing post');
        }

        return (bool)$this->save(['id' => $id, 'published' => true]);
    }
}
