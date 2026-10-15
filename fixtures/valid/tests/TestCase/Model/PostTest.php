<?php
App::uses('Post', 'Model');

class TestPost extends Post
{
    public $useTable = 'posts';
}

class PostTest extends CakeTestCase
{
    public $fixtures = ['app.post'];

    public function setUp(): void
    {
        parent::setUp();
        $this->Post = ClassRegistry::init('TestPost');
    }

    public function testFind(): void
    {
        $result = $this->Post->find('count');
        $this->assertSame(1, $result);
    }
}
