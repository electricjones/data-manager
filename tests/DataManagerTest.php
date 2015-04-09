<?php
namespace Michaels\Manager\Test;

use Michaels\Manager\DataManager as Manager;

class DataManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testAddSingleItem()
    {
        $manager = new Manager();
        $manager->add('alias', 'value');

        $this->assertArrayHasKey('alias', $manager->getAll(), 'Array Items does not have key `alias`');
        $this->assertEquals('value', $manager->get('alias'), 'Failed to get a single item');
    }

    public function testAddMultipleItemsAtOnce()
    {
        $manager = new Manager();
        $manager->add([
            'objectTest'    => new \StdClass(),
            'closureTest'   => function() {
                return true;
            },
            'stringTest'     => 'value'
        ]);

        $items = $manager->getAll();

        $this->assertArrayHasKey('objectTest', $items, 'Array Items does not have key `objectTest`');
        $this->assertArrayHasKey('closureTest', $items, 'Array Items does not have key `closureTest`');
        $this->assertArrayHasKey('stringTest', $items, 'Array Items does not have key `stringTest`');
    }

    public function testReturnTrueIfItemExists()
    {
        $manager = new Manager();
        $manager->add('test', 'Test\Item');

        $this->assertTrue($manager->exists('test'));
    }

    public function testReturnFalseIfItemDoesNotExist()
    {
        $manager = new Manager();

        $this->assertFalse($manager->exists('test'));
    }

    public function testCanProvideFallbackValue()
    {
        $manager = new Manager();
        $manager->add('one', 'one-value');

        $actual = $manager->get('two', 'default-value');

        $this->assertEquals('default-value', $actual, 'failed to return a fallback value');
    }

    public function testUpdateSingleItem()
    {
        $manager = new Manager();
        $manager->add('item', 'original-value');
        $manager->set('item', 'new-value');

        $this->assertEquals('new-value', $manager->get('item'), 'failed to update a single item');
    }

    public function testUpdateMultipleItems()
    {
        $manager = new Manager();
        $manager->add('item', 'original-value');
        $manager->add('item2', 'other-original-value');
        $manager->set(['item' => 'new-value', 'item2' => 'other-new-value']);

        $this->assertEquals('new-value', $manager->get('item'), 'failed to update first item');
        $this->assertEquals('other-new-value', $manager->get('item2'), 'failed to update second item');
    }

    public function testRemoveItem()
    {
        $manager = new Manager();
        $manager->add([
            'one' => 'one',
            'two' => 'two'
        ]);

        $manager->remove('one');

        $items = $manager->getAll();

        $this->assertArrayNotHasKey('one', $items, 'failed to remove `one`');
        $this->assertArrayHasKey('two', $items, 'failed to leave `two` intact');
    }

    public function testClear()
    {
        $manager = new Manager();
        $manager->add([
            'one'    => 'one',
            'two'     => 'two'
        ]);

        $manager->clear();

        $items = $manager->getAll();

        $this->assertEmpty($items, "Failed to empty manager");
    }
}