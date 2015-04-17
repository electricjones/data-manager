<?php
namespace Michaels\Manager\Test;

use Michaels\Manager\Manager;

class DataManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testInitializeWithData()
    {
        $expected = [
            'one' => [
                'two' => [
                    'three' => 'three-value',
                    'four' => [
                        'five' => 'five-value'
                    ],
                ],
                'six' => [
                    'seven' => 'seven-value',
                    'eight' => 'eight-value'
                ]
            ],
            'top' => 'top-value',
        ];

        $manager = new Manager($expected);

        $this->assertEquals($expected, $manager->getAll(), "failed to return identical values set at instantiation");
    }

    public function testAddAndGetSingleItem()
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
        $manager->add('test', 'test-item');
        $manager->add('booltest', false);

        $this->assertTrue($manager->exists('test'));
        $this->assertTrue($manager->exists('booltest'));
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

    /**
     * @expectedException \Michaels\Manager\ItemNotFoundException
     */
    public function testThrowsExceptionIfItemNotFound()
    {
        $manager = new Manager();
        $manager->get('doesntexist');
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

    public function testRemoveSingleItem()
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

    public function testAddNestedItems()
    {
        $manager = new Manager();
        $manager->add('one.two.three', 'three-value');
        $manager->add('one.two.four.five', 'five-value');
        $manager->add('one.six', ['seven' => 'seven-value']);
        $manager->add('one.six.eight', 'eight-value');
        $manager->add('top', 'top-value');

        $expected = [
            'one' => [
                'two' => [
                    'three' => 'three-value',
                    'four' => [
                        'five' => 'five-value'
                    ],
                ],
                'six' => [
                    'seven' => 'seven-value',
                    'eight' => 'eight-value'
                ]
            ],
            'top' => 'top-value',
        ];
        $actual = $manager->getAll();

        $this->assertEquals($expected, $actual, 'failed to add nested items');
    }

    public function testCheckExistenceOfNestedItems()
    {
        $manager = new Manager();
        $manager->add('one.two.three', 'three-value');

        // Always match against full contents
        $expected = ['one' => ['two' => ['three' => 'three-value']]];
        $actual = $manager->getAll();
        $this->assertEquals($expected, $actual, 'failed to add nested items');

        $this->assertTrue($manager->exists('one.two.three'), 'failed to confirm existence of a nested item');
        $this->assertFalse($manager->exists('one.two.no'), 'failed to deny existence of a nested item');
    }

    public function testGetNestedItems()
    {
        $manager = new Manager();
        $manager->add('one.two.three', 'three-value');

        // Always match against full contents
        $expected = ['one' => ['two' => ['three' => 'three-value']]];
        $actual = $manager->getAll();
        $this->assertEquals($expected, $actual, 'failed to add nested items');

        $this->assertEquals('three-value', $manager->get('one.two.three'), 'failed to get a single item');
    }

    public function testRemoveNestedItems()
    {
        $manager = new Manager();
        $manager->add('one.two.three', 'three-value');
        $manager->add('one.two.four', 'four-value');

        // Always match against full contents
        $expected = ['one' => ['two' => ['three' => 'three-value', 'four' => 'four-value']]];
        $actual = $manager->getAll();
        $this->assertEquals($expected, $actual, 'failed to add nested items');

        $manager->remove('one.two.three');
        $manager->remove('does.not.exist');

        $this->assertTrue($manager->exists('one.two.four'), 'failed to leave nested item in tact');
        $this->assertFalse($manager->exists('one.two.three'), 'failed to remove nested item');
    }

public function testPopulateAtInstantiation()
{
    $expected = ['one' => ['two' => ['three' => 'three-value']]];
    $manager = new Manager($expected);

    $this->assertEquals($expected, $manager->getAll(), 'failed to populate array at construction');
}
}