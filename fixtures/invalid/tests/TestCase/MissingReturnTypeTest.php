<?php
App::uses('Post', 'Model');

class TestPost extends Post
{
    public function beforeSave($options = [])
    {
        return true;
    }
}

class MissingReturnTypeTest extends CakeTestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    /**
     * @return void
     */
    public function testWithAnnotationOnly()
    {
        $this->assertTrue(true);
    }
}
