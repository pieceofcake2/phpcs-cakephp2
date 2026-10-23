<?php
namespace App\Test\TestCase\Model;

use App\Model\Post;
use Cake\TestSuite\CakeTestCase;
use Cake\Utility\ClassRegistry;

class TestPost extends Post
{
    public $useTable = 'posts';
}

class PostTest extends CakeTestCase
{
    public $fixtures = ['app.post'];

    /**
     * @var Post
     */
    public $Post;

    public function setUp(): void
    {
        parent::setUp();
        $this->Post = ClassRegistry::init(TestPost::class);
    }

    public function testFind(): void
    {
        $result = $this->Post->find('count');
        $this->assertSame(1, $result);
    }
}
