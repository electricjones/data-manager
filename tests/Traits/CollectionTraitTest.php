<?php
namespace Michaels\Manager\Test\Traits;

use Arrayzy\MutableArray;
use Michaels\Manager\Test\Stubs\CollectionStub;
use StdClass;

class CollectionTraitTest extends \PHPUnit_Framework_TestCase
{
    public $manager;
    public $testData;

    public function setUp()
    {
        $object = new stdClass();
        $object->type = 'object';

        $this->testData = [
            'string' => '\SplObjectStorage', // Used here for overhead
            'callable' => function () {
                $return = new stdClass();
                $return->type = 'callable';
                return $return;
            },
            'object' => $object
        ];
    }

    /** Begin Tests **/
    public function testToCollectionReturnsMutableArray()
    {
        $manager = new CollectionStub(['a', 'b', 'c']);
        $actual = $manager->getAll();

        $this->assertInstanceOf(get_class(new MutableArray()), $actual, "failed to return an instance of `MutableArray`");
    }

    public function testUsingCollectionFromGet()
    {
        $manager = new CollectionStub(['one' => ['two' => ['a', 'b', 'c']]]);
        $actual = $manager->get('one.two');

        $this->assertInstanceOf(get_class(new MutableArray()), $actual, "failed to return an instance of `MutableArray`");
        $this->assertEquals(3, $actual->count(), "failed to return a working copy of `MutableArray`");
    }

    public function testUsingCollectionFromGetAll()
    {
        $manager = new CollectionStub(['one' => ['two' => ['a', 'b', 'c']]]);
        $actual = $manager->all();
        $actual->push('d', 'e');

        $this->assertInstanceOf(get_class(new MutableArray()), $actual, "failed to return an instance of `MutableArray`");
        $this->assertEquals(3, $actual->count(), "failed to return a working copy of `MutableArray`");
        $this->assertEquals(['one' => ['two' => ['a', 'b', 'c']], 'd', 'e'], $actual->toArray(), "failed to push items onto an array");
    }

    public function testUsingCollectionWithChainedMethods()
    {
        $manager = new CollectionStub(['one' => ['two' => ['a', 'b', 'c']]]);
        $actual = $manager->get('one.two')->push('d', 'e');

        $this->assertInstanceOf(get_class(new MutableArray()), $actual, "failed to return an instance of `MutableArray`");
        $this->assertEquals(5, $actual->count(), "failed to return a working copy of `MutableArray`");
        $this->assertEquals(['a', 'b', 'c', 'd', 'e'], $actual->toArray(), "failed to push items onto an array");
    }
}