<?php
namespace App\Model\Behavior;

use Cake\Model\ModelBehavior;

/**
 * Taggable Behavior with a fully qualified class name and unsorted types in one doc comment
 */
class TaggableBehavior extends ModelBehavior
{
    /**
     * Whether a record has a tag
     *
     * @param \Cake\Model\Model $Model Model using this behavior
     * @param null|string $tag Tag
     * @return bool
     */
    public function hasTag($Model, $tag = null)
    {
        return $tag !== null && $Model->exists();
    }
}
