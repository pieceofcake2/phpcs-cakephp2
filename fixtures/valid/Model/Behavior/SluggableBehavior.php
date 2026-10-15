<?php
App::uses('ModelBehavior', 'Model');

/**
 * Sluggable Behavior
 */
class SluggableBehavior extends ModelBehavior
{
    /**
     * Fill the slug before saving
     *
     * @param Model $Model Model using this behavior
     * @param array $options Options passed from Model::save()
     * @return bool
     */
    public function beforeSave(Model $Model, $options = [])
    {
        $Model->data[$Model->alias]['slug'] = $this->_slug($Model->data[$Model->alias]['title']);

        return true;
    }

    /**
     * Make a slug
     *
     * @param string $title Title
     * @return string
     */
    protected function _slug($title)
    {
        return strtolower(Inflector::slug($title, '-'));
    }
}
