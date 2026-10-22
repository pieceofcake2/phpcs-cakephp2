<?php
namespace App\Model\Behavior;

use Cake\Model\ModelBehavior;

/**
 * Counter Behavior with fully qualified class names in annotations
 */
class CounterBehavior extends ModelBehavior
{
    /**
     * Count the records
     *
     * @param \Cake\Model\Model $Model Model using this behavior
     * @return int
     * @throws \InvalidArgumentException When the model has no table
     */
    public function countAll($Model)
    {
        return $Model->find('count');
    }
}
