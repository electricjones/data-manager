<?php
/*
 * This file needs some cleanup. It tests scenarios if using both the ChainsNestedItemsTrait
 * and the CollectionTrait. There are simply too many scenarios to cleanly test them all atm.
 *
 * This usage should be avoided. It can cause weird naming collisions.
 */
namespace Michaels\Manager\Test\Traits;

use Arrayzy\ArrayImitator;
use Michaels\Manager\Contracts\ManagesItemsInterface;
use Michaels\Manager\Traits\ChainsNestedItemsTrait;
use Michaels\Manager\Traits\CollectionTrait;
use Michaels\Manager\Traits\ManagesItemsTrait;

class NestedCollectionsStub implements ManagesItemsInterface
{
    use ManagesItemsTrait, ChainsNestedItemsTrait, CollectionTrait {
        CollectionTrait::__call insteadof ChainsNestedItemsTrait;
    }

    public function __construct(array $items = null)
    {
        $this->initManager($items);
    }
}

class NestedAndCollectionsTest extends \PHPUnit_Framework_TestCase
{
    protected $testData = [
        'one' => [
            'two' => [
                'three' => 'three-value'
            ],
            'two_b' => 'two_b-value'
        ]
    ];

    public function test_standard()
    {
        $stub = new NestedCollectionsStub();
        $stub->set('a', 'b');

        $this->assertEquals('b', $stub->get('a'), "failed");
    }

    public function test_chains_nested_items_by_itself()
    {
        $stub = new NestedCollectionsStub();
        $stub->initManager($this->testData);

        $expectedA = $this->testData['one']['two']['three'];
        $actualA = $stub->one()->two()->three;

        $expectedB = $this->testData['one']['two_b'];
        $actualB = $stub->one()->two_b;

        $expectedC = $this->testData['one'];
        $actualC = $stub->one;

        $this->assertEquals($expectedA, $actualA, 'failed to retrieve first nested value');
        $this->assertEquals($expectedB, $actualB, 'failed to retrieve second nested value');
        $this->assertEquals($expectedC, $actualC->toArray(), 'failed to retrieve third nested value');
    }

    public function test_using_collection_from_get()
    {
        $manager = new NestedCollectionsStub(['one' => ['two' => ['a', 'b', 'c']]]);
        $actual = $manager->get('one.two');

        $this->assertInstanceOf(get_class(new ArrayImitator()), $actual, "failed to return an instance of `ArrayImitator`");
        $this->assertEquals(3, $actual->count(), "failed to return a working copy of `ArrayImitator`");
    }

    public function test_using_collection_with_chained_methods()
    {
        $manager = new NestedCollectionsStub(['one' => ['two' => ['a', 'b', 'c']]]);
        $actual = $manager->get('one.two')->push('d', 'e');

        $this->assertInstanceOf(get_class(new ArrayImitator()), $actual, "failed to return an instance of `ArrayImitator`");
        $this->assertEquals(5, $actual->count(), "failed to return a working copy of `ArrayImitator`");
        $this->assertEquals(['a', 'b', 'c', 'd', 'e'], $actual->toArray(), "failed to push items onto an array");
    }

    public function test_arrayzy_walk_return_array_by_default()
    {
        $manager = new NestedCollectionsStub(['one' => ['two' => ['a', 'b', 'c']]]);

        $actual = $manager->walk('one.two', function(&$value, $key) {
            $value = "$value-new";
        });

        $this->assertTrue(is_array($actual), "failed to return an array");
        $this->assertEquals(['a-new', 'b-new', 'c-new'], $actual, "failed to walk array");
    }
}