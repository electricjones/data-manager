<?php
namespace Michaels\Manager\Test\Traits;

use Arrayzy\ArrayImitator;
use Michaels\Manager\Test\Stubs\CollectionStub;
use StdClass;

class CollectionTest extends \PHPUnit_Framework_TestCase
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
    public function test_to_collection_returns_mutable_array()
    {
        $manager = new CollectionStub(['a', 'b', 'c']);
        $actual = $manager->getAll();

        $this->assertInstanceOf(get_class(new ArrayImitator()), $actual, "failed to return an instance of `ArrayImitator`");
    }

    public function test_using_collection_from_get()
    {
        $manager = new CollectionStub(['one' => ['two' => ['a', 'b', 'c']]]);
        $actual = $manager->get('one.two');

        $this->assertInstanceOf(get_class(new ArrayImitator()), $actual, "failed to return an instance of `ArrayImitator`");
        $this->assertEquals(3, $actual->count(), "failed to return a working copy of `ArrayImitator`");
    }

    public function test_using_collection_from_getAll()
    {
        $manager = new CollectionStub(['one' => ['two' => ['a', 'b', 'c']]]);
        $actual = $manager->all();
        $actual->push('d', 'e');

        $this->assertInstanceOf(get_class(new ArrayImitator()), $actual, "failed to return an instance of `ArrayImitator`");
        $this->assertEquals(3, $actual->count(), "failed to return a working copy of `ArrayImitator`");
        $this->assertEquals(['one' => ['two' => ['a', 'b', 'c']], 'd', 'e'], $actual->toArray(), "failed to push items onto an array");
    }

    public function test_using_collection_with_chained_methods()
    {
        $manager = new CollectionStub(['one' => ['two' => ['a', 'b', 'c']]]);
        $actual = $manager->get('one.two')->push('d', 'e');

        $this->assertInstanceOf(get_class(new ArrayImitator()), $actual, "failed to return an instance of `ArrayImitator`");
        $this->assertEquals(5, $actual->count(), "failed to return a working copy of `ArrayImitator`");
        $this->assertEquals(['a', 'b', 'c', 'd', 'e'], $actual->toArray(), "failed to push items onto an array");
    }

    public function test_does_not_want_collections()
    {
        $manager = new CollectionStub(['one' => ['two' => ['a', 'b', 'c']]]);
        $manager->useCollections = false;
        $actual = $manager->get('one.two');

        $this->assertNotInstanceOf(get_class(new ArrayImitator()), $actual, "failed: returned a `ArrayImitator`");
        $this->assertTrue(is_array($actual), "failed to return a regular array");
        $this->assertEquals(['a', 'b', 'c'], $actual, "failed to return correct array");
    }

    public function test_return_raw_for_not_arrayble_item()
    {
        $manager = new CollectionStub(['one' => ['two' => 'two-value']]);
        $manager->useCollections = false;
        $actual = $manager->get('one.two');

        $this->assertNotInstanceOf(get_class(new ArrayImitator()), $actual, "failed: returned a `ArrayImitator`");
        $this->assertFalse(is_array($actual), "failed: returned a regular array");
        $this->assertEquals('two-value', $actual, "failed to return raw value");
    }

    /* Test using Arrayzy API directly */
    public function test_arrayzy_walk_return_array_by_default()
    {
        $manager = new CollectionStub(['one' => ['two' => ['a', 'b', 'c']]]);

        $actual = $manager->walk('one.two', function(&$value, $key) {
            $value = "$value-new";
        });

        $this->assertTrue(is_array($actual), "failed to return an array");
        $this->assertEquals(['a-new', 'b-new', 'c-new'], $actual, "failed to walk array");
    }

    public function test_arrayzy_walk_return_array_explicitly()
    {
        $manager = new CollectionStub(['one' => ['two' => ['a', 'b', 'c']]]);

        $actual = $manager->walk('one.two', function(&$value, $key) {
            $value = "$value-new";
        }, CollectionStub::$RETURN_ARRAY);

        $this->assertTrue(is_array($actual), "failed to return an array");
        $this->assertEquals(['a-new', 'b-new', 'c-new'], $actual, "failed to walk array");
    }

    public function test_arrayzy_walk_modify_manifest()
    {
        $manager = new CollectionStub(['one' => ['two' => ['a', 'b', 'c']]]);
        $manager->add('b', 'c');

        $actual = $manager->walk('one.two', function(&$value, $key) {
            $value = "$value-new";
        }, CollectionStub::$MODIFY_MANIFEST);

        $this->assertInstanceOf("Michaels\\Manager\\Test\\Stubs\\CollectionStub", $actual, "failed to return a Manager");
        $this->assertEquals('c', $actual->get('b'), "failed to return same manager instance");
        $this->assertEquals(['a-new', 'b-new', 'c-new'], $actual->get('one.two')->toArray(), "failed to walk array");
    }

    public function test_arrayzy_walk_modify_return_collection()
    {
        $manager = new CollectionStub(['one' => ['two' => ['a', 'b', 'c']]]);

        $actual = $manager->walk('one.two', function(&$value, $key) {
            $value = "$value-new";
        }, CollectionStub::$RETURN_COLLECTION);

        $this->assertInstanceOf("Arrayzy\\ArrayImitator", $actual, "failed to return a Collection");
        $this->assertEquals(['a-new', 'b-new', 'c-new'], $actual->toArray(), "failed to walk array");
    }

    /* The rest of the Arrayzy tests return defaults */
    public function test_arrayzy_unique()
    {
        $manager = new CollectionStub(['one' => ['two' => ['a', 'b', 'b', 'c']]]);

        $actual = $manager->unique('one.two');

        $this->assertEquals([0=>'a', 1=>'b', 3=>'c'], $actual, "failed to unique array");
    }

    public function test_arrayzy_unshift()
    {
        $manager = new CollectionStub(['one' => ['two' => ['a', 'b', 'c']]]);

        $actual = $manager->unshift('one.two', 'y', 'z');

        $this->assertEquals(['y', 'z', 'a', 'b', 'c'], $actual, "failed to unshift array");
    }
}