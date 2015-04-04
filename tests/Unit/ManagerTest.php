<?php
namespace Michaels\Midas\Test\Unit;

use Michaels\Midas\Manager;
use stdClass;

class ManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testAddSingle()
    {
        $manager = new Manager();
        $manager->add('alias', 'value');

        $this->assertArrayHasKey('alias', $manager->getAll(), 'Array Items does not have key `objectTest`');
        $this->assertEquals('value', $manager->get('alias'), 'Failed to get a single item');
    }

    public function testAddMultiple()
    {
        $manager = new Manager();
        $manager->add([
            'objectTest'    => new StdClass(),
            'closureTest'   => function() {
                return true;
            },
            'classTest'     => 'Some\Class'
        ]);

        $items = $manager->getAll();

        $this->assertArrayHasKey('objectTest', $items, 'Array Items does not have key `objectTest`');
        $this->assertArrayHasKey('closureTest', $items, 'Array Items does not have key `objectTest`');
        $this->assertArrayHasKey('classTest', $items, 'Array Items does not have key `objectTest`');
    }

    /**
     * Test that true does in fact equal true
     */
    public function testClear()
    {
        $manager = new Manager();
        $manager->add([
            'objectTest'    => new StdClass(),
            'closureTest'   => function() {
                return true;
            },
            'classTest'     => 'Some\Class'
        ]);

        $manager->clear();

        $items = $manager->getAll();

        $this->assertEmpty($items, "Failed: Items are not empty");
    }

    /**
     * Test that true does in fact equal true
     */
    public function testGetItem()
    {
        $manager = new Manager();
        $manager->add('test', 'Test\Item');

        $this->assertEquals('Test\Item', $manager->get('test'), 'Failed to get item');
    }

    /**
     * Test that true does in fact equal true
     */
    public function testGetItemThatDoesNotExist()
    {
        $manager = new Manager();

        $this->assertFalse($manager->get('test'));
    }

    /**
     * Test that true does in fact equal true
     */
    public function testReturnTrueIfItemExists()
    {
        $manager = new Manager();
        $manager->add('test', 'Test\Item');

        $this->assertTrue($manager->exists('test'));
    }

    /**
     * Test that true does in fact equal true
     */
    public function testReturnFalseIfItemDoesNotExist()
    {
        $manager = new Manager();

        $this->assertFalse($manager->exists('test'));
    }

    /**
     * Test that true does in fact equal true
     */
    public function testArrayConstruction()
    {
        $expected = [
            'one' => 'One\Class',
            'two' => 'Two\Class'
        ];

        $manager = new Manager($expected);

        $this->assertEquals($expected['one'], $manager['one'], "Failed to construct manager as an array.");
    }

    /**
     * Test that true does in fact equal true
     */
    public function testArrayAddSingleItem()
    {
        $manager = new Manager();

        $manager['one'] = 'One\Class';

        $this->assertEquals('One\Class', $manager['one'], "Failed to add `One\\Class` through ArrayAccess.");
    }

    public function testAddAndGetNamespacedItems()
    {
        $manager = new Manager();

        $manager->add('one.two.three', 'One\Two\Three');
        $manager->add('one.two.four', 'One\Two\Four');

        $items = $manager->getAll();

        $expected = [
          'one' => [
              'two' => [
                  'three' => 'One\Two\Three',
                  'four' => 'One\Two\Four'
              ]
          ]
        ];

        $this->assertEquals($expected, $items, "Failed to set namespaced items.");

        $three = $manager->get('one.two.three');
        $four = $manager->get('one.two.four');

        $this->assertEquals('One\Two\Three', $three, "Failed get first deeply namespaced value.");
        $this->assertEquals('One\Two\Four', $four, "Failed get second deeply namespaced value.");

        $this->assertFalse($manager->get('does.not.exist'));

//        $this->specify("it adds a command to an existing namespace", function () use ($midas) {
//            $midas->addCommand('exists.two', 'Class');
//        });
    }

    public function testCheckForNamespacedItems()
    {
        $manager = new Manager();

        $manager->add('one.two.three', 'One\Two\Three');

        $this->assertTrue($manager->exists('one.two.three'), "failed to confirm existence of namespaced item");
        $this->assertFalse($manager->exists('four.five.six'), 'failed to confirm non-existence of namespaced item');
    }
}
