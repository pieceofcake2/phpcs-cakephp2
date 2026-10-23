<?php
/**
 * Schema of the application
 */
class AppSchema extends CakeSchema
{
    /**
     * Posts table
     *
     * @var array
     */
    public $posts = [
        'id' => ['type' => 'integer', 'null' => false, 'key' => 'primary'],
        'title' => ['type' => 'string', 'null' => false],
    ];
}
