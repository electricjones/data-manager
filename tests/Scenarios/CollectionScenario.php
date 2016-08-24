<?php
namespace Michaels\Manager\Test\Scenarios;

use Arrayzy\ArrayImitator;
use Michaels\Manager\Test\Stubs\CollectionStub;
use Michaels\Manager\Test\Stubs\NestedAndCollectionsStub;

trait CollectionScenario
{
    protected $collectionTestData = [
        'one' => [
            'two' => [
                'three' => 'three-value'
            ],
            'two_b' => 'two_b-value'
        ]
    ];

    public function test_to_collection_returns_mutable_array()
    {
        $manager = $this->getManager(['a', 'b', 'c']);
        $actual = $manager->getAll();

        $this->assertInstanceOf(get_class(new ArrayImitator()), $actual, "failed to return an instance of `ArrayImitator`");
    }

    public function test_using_collection_from_get()
    {
        $manager = $this->getManager(['one' => ['two' => ['a', 'b', 'c']]]);
        $actual = $manager->get('one.two');

        $this->assertInstanceOf(get_class(new ArrayImitator()), $actual, "failed to return an instance of `ArrayImitator`");
        $this->assertEquals(3, $actual->count(), "failed to return a working copy of `ArrayImitator`");
    }

    public function test_using_collection_from_getAll()
    {
        $manager = $this->getManager(['one' => ['two' => ['a', 'b', 'c']]]);
        $actual = $manager->all();
        $actual->push('d', 'e');

        $this->assertInstanceOf(get_class(new ArrayImitator()), $actual, "failed to return an instance of `ArrayImitator`");
        $this->assertEquals(3, $actual->count(), "failed to return a working copy of `ArrayImitator`");
        $this->assertEquals(['one' => ['two' => ['a', 'b', 'c']], 'd', 'e'], $actual->toArray(), "failed to push items onto an array");
    }

    public function test_using_collection_with_chained_methods()
    {
        $manager = $this->getManager(['one' => ['two' => ['a', 'b', 'c']]]);
        $actual = $manager->get('one.two')->push('d', 'e');

        $this->assertInstanceOf(get_class(new ArrayImitator()), $actual, "failed to return an instance of `ArrayImitator`");
        $this->assertEquals(5, $actual->count(), "failed to return a working copy of `ArrayImitator`");
        $this->assertEquals(['a', 'b', 'c', 'd', 'e'], $actual->toArray(), "failed to push items onto an array");
    }

    public function test_does_not_want_collections()
    {
        $manager = $this->getManager(['one' => ['two' => ['a', 'b', 'c']]]);
        $manager->useCollections = false;
        $actual = $manager->get('one.two');

        $this->assertNotInstanceOf(get_class(new ArrayImitator()), $actual, "failed: returned a `ArrayImitator`");
        $this->assertTrue(is_array($actual), "failed to return a regular array");
        $this->assertEquals(['a', 'b', 'c'], $actual, "failed to return correct array");
    }

    public function test_return_raw_for_not_arrayble_item()
    {
        $manager = $this->getManager(['one' => ['two' => 'two-value']]);
        $manager->useCollections = false;
        $actual = $manager->get('one.two');

        $this->assertNotInstanceOf(get_class(new ArrayImitator()), $actual, "failed: returned a `ArrayImitator`");
        $this->assertFalse(is_array($actual), "failed: returned a regular array");
        $this->assertEquals('two-value', $actual, "failed to return raw value");
    }

    public function test_throws_exception_for_undefined_collection_method()
    {
        $this->setExpectedException('\BadMethodCallException');

        $manager = $this->getManager();
        $manager->doesNotExist();
    }

    /* Integration Test using Arrayzy API directly */
    /* ToDo: decouple these methods from the Collection object */
    public function test_return_array_by_default()
    {
        $manager = $this->getManager(['one' => ['two' => ['a', 'b', 'c']]]);

        $actual = $manager->walk('one.two', function(&$value, $key) {
            $value = "$value-new";
        });

        $this->assertTrue(is_array($actual), "failed to return an array");
        $this->assertEquals(['a-new', 'b-new', 'c-new'], $actual, "failed to walk array");
    }

    public function test_return_array_explicitly()
    {
        $manager = $this->getManager(['one' => ['two' => ['a', 'b', 'c']]]);

        $actual = $manager->walk('one.two', function(&$value, $key) {
            $value = "$value-new";
        }, CollectionStub::$RETURN_ARRAY);

        $this->assertTrue(is_array($actual), "failed to return an array");
        $this->assertEquals(['a-new', 'b-new', 'c-new'], $actual, "failed to walk array");
    }

    public function test_modify_manifest()
    {
        $manager = $this->getManager(['one' => ['two' => ['a', 'b', 'c']]]);
        $manager->add('b', 'c');

        $actual = $manager->walk('one.two', function(&$value, $key) {
            $value = "$value-new";
        }, CollectionStub::$MODIFY_MANIFEST);

        $this->assertInstanceOf("Michaels\\Manager\\Test\\Stubs\\CollectionStub", $actual, "failed to return a Manager");
        $this->assertEquals('c', $actual->get('b'), "failed to return same manager instance");
        $this->assertEquals(['a-new', 'b-new', 'c-new'], $actual->get('one.two')->toArray(), "failed to walk array");
    }

    public function test_return_collection()
    {
        $manager = $this->getManager(['one' => ['two' => ['a', 'b', 'c']]]);

        $actual = $manager->walk('one.two', function(&$value, $key) {
            $value = "$value-new";
        }, CollectionStub::$RETURN_COLLECTION);

        $this->assertInstanceOf("Arrayzy\\ArrayImitator", $actual, "failed to return a Collection");
        $this->assertEquals(['a-new', 'b-new', 'c-new'], $actual->toArray(), "failed to walk array");
    }

    /* Integration tests for a couple Arrayzy methods, just for completeness */
    public function test_arrayzy_unique()
    {
        $manager = $this->getManager(['one' => ['two' => ['a', 'b', 'b', 'c']]]);

        $actual = $manager->unique('one.two');

        $this->assertEquals([0=>'a', 1=>'b', 3=>'c'], $actual, "failed to unique array");
    }

    public function test_arrayzy_unshift()
    {
        $manager = $this->getManager(['one' => ['two' => ['a', 'b', 'c']]]);

        $actual = $manager->unshift('one.two', 'y', 'z');

        $this->assertEquals(['y', 'z', 'a', 'b', 'c'], $actual, "failed to unshift array");
    }

    /* Test Using Both Collections and ChainsNestedItems */
    public function test_chains_and_collections_standard()
    {
        $stub = new NestedAndCollectionsStub();
        $stub->set('a', 'b');

        $this->assertEquals('b', $stub->get('a'), "failed");
    }

    public function test_chains_nested_items_by_itself()
    {
        $stub = new NestedAndCollectionsStub();
        $stub->initManager($this->collectionTestData);

        $expectedA = $this->collectionTestData['one']['two']['three'];
        $actualA = $stub->one()->two()->three;

        $expectedB = $this->collectionTestData['one']['two_b'];
        $actualB = $stub->one()->two_b;

        $expectedC = $this->collectionTestData['one'];
        $actualC = $stub->one;

        $this->assertEquals($expectedA, $actualA, 'failed to retrieve first nested value');
        $this->assertEquals($expectedB, $actualB, 'failed to retrieve second nested value');
        $this->assertEquals($expectedC, $actualC->toArray(), 'failed to retrieve third nested value');
    }

    public function test_chains_and_collections_get_returns_collection()
    {
        $manager = new NestedAndCollectionsStub(['one' => ['two' => ['a', 'b', 'c']]]);
        $actual = $manager->get('one.two');

        $this->assertInstanceOf(get_class(new ArrayImitator()), $actual, "failed to return an instance of `ArrayImitator`");
        $this->assertEquals(3, $actual->count(), "failed to return a working copy of `ArrayImitator`");
    }

    public function test_chains_and_collections_with_fluent_methods()
    {
        $manager = new NestedAndCollectionsStub(['one' => ['two' => ['a', 'b', 'c']]]);
        $actual = $manager->get('one.two')->push('d', 'e');

        $this->assertInstanceOf(get_class(new ArrayImitator()), $actual, "failed to return an instance of `ArrayImitator`");
        $this->assertEquals(5, $actual->count(), "failed to return a working copy of `ArrayImitator`");
        $this->assertEquals(['a', 'b', 'c', 'd', 'e'], $actual->toArray(), "failed to push items onto an array");
    }
}