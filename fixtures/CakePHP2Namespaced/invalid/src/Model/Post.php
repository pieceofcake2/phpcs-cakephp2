<?php
/**
 * Post Model without a namespace
 */
class Post extends AppModel
{
    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = ['User'];
}
