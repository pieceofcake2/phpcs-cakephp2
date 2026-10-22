<?php
App::uses('AppModel', 'Model');

/**
 * Post Model
 */
class Post extends AppModel
{
    /**
     * Public methods must not start with an underscore
     *
     * @return array
     */
    public function _published()
    {
        return $this->find('all', ['conditions' => ['published' => true]]);
    }
}
