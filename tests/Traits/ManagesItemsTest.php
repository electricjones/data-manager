<?php
namespace Michaels\Manager\Test\Traits;

use Michaels\Manager\Test\Stubs\ManagesItemsTraitStub as Manager;
use StdClass;

class ManagesItemsTest extends \PHPUnit_Framework_TestCase
{
    public $manager;
    public $testData;
    private $simpleNestData;

    public function setup()
    {
        $this->testData = [
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

        $this->simpleNestData = ['one' => ['two' => ['three' => 'three-value']]];

        $this->manager = new Manager();
    }

    private function assertFullManifest($manifest = null, $message = false)
    {
        $expected = ($manifest) ? $manifest : $this->testData;
        $message = ($message) ? $message : 'failed to add nested items';

        $actual = $this->manager->getAll();
        $this->assertEquals($expected, $actual, $message);
    }

    /** Begin Tests **/
    public function testInitializeWithData()
    {
        $manager = new Manager($this->testData);

        $this->assertEquals($this->testData, $manager->getAll(), "Failed to return identical values set at instantiation");
    }

    public function testAddAndGetSingleItem()
    {
        $this->manager->add('alias', 'value');

        $this->assertArrayHasKey('alias', $this->manager->getAll(), 'Failed to confirm that manager has item `alias`');
        $this->assertEquals('value', $this->manager->get('alias'), 'Failed to get a single item');
    }

    public function testAddMultipleItemsAtOnce()
    {
        $this->manager->add([
            'objectTest'    => new StdClass(),
            'closureTest'   => function() {
                return true;
            },
            'stringTest'     => 'value'
        ]);

        $items = $this->manager->getAll();

        $this->assertArrayHasKey('objectTest', $items, 'Failed to confirm that manager has key `objectTest`');
        $this->assertArrayHasKey('closureTest', $items, 'Failed to confirm that manager has key `closureTest`');
        $this->assertArrayHasKey('stringTest', $items, 'Failed to confirm that manager has key `stringTest`');
    }

    public function testReturnTrueIfItemExists()
    {
        $this->manager->add('test', 'test-item');
        $this->manager->add('booleantest', false);

        $this->assertTrue($this->manager->exists('test'), "Failed to confirm that `test` exists");
        $this->assertTrue($this->manager->exists('booleantest'), "Failed to confirm that boolean false value exists");
    }

    public function testReturnFalseIfItemDoesNotExist()
    {
        $this->assertFalse($this->manager->exists('test'));
    }

    public function testProvidesFallbackValue()
    {
        $this->manager->add('one', 'one-value');

        $actual = $this->manager->get('two', 'default-value');

        $this->assertEquals('default-value', $actual, 'failed to return a fallback value');
    }

    /**
     * @expectedException \Michaels\Manager\Exceptions\ItemNotFoundException
     */
    public function testThrowsExceptionIfItemNotFound()
    {
        $this->manager->get('doesntexist');
    }

    public function testUpdateSingleItem()
    {
        $this->manager->add('item', 'original-value');
        $this->manager->set('item', 'new-value');

        $this->assertEquals('new-value', $this->manager->get('item'), 'Failed to update a single item');
    }

    public function testUpdateMultipleItems()
    {
        $this->manager->add('item', 'original-value');
        $this->manager->add('item2', 'other-original-value');
        $this->manager->set(['item' => 'new-value', 'item2' => 'other-new-value']);

        $this->assertEquals('new-value', $this->manager->get('item'), 'Failed to update first item');
        $this->assertEquals('other-new-value', $this->manager->get('item2'), 'Failed to update second item');
    }

    public function testRemoveSingleItem()
    {
        $this->manager->add([
            'one' => 'one',
            'two' => 'two'
        ]);

        $this->manager->remove('one');

        $items = $this->manager->getAll();

        $this->assertArrayNotHasKey('one', $items, 'failed to remove `one`');
        $this->assertArrayHasKey('two', $items, 'failed to leave `two` intact');
    }

    public function testClear()
    {
        $this->manager->add([
            'one'    => 'one',
            'two'     => 'two'
        ]);

        $this->manager->clear();
        $items = $this->manager->getAll();

        $this->assertEmpty($items, "Failed to empty manager");
    }

    public function testAddNestedItems()
    {
        $this->manager->add('one.two.three', 'three-value');
        $this->manager->add('one.two.four.five', 'five-value');
        $this->manager->add('one.six', ['seven' => 'seven-value']);
        $this->manager->add('one.six.eight', 'eight-value');
        $this->manager->add('top', 'top-value');

        $this->assertFullManifest();
    }

    public function testCheckExistenceOfNestedItems()
    {
        $this->manager->add('one.two.three', 'three-value');

        $this->assertFullManifest($this->simpleNestData);
        $this->assertTrue($this->manager->exists('one.two.three'), 'failed to confirm existence of a nested item');
        $this->assertFalse($this->manager->exists('one.two.no'), 'failed to deny existence of a nested item');
    }

    public function testGetNestedItems()
    {
        $this->manager->add('one.two.three', 'three-value');

        $this->assertFullManifest($this->simpleNestData);
        $this->assertEquals('three-value', $this->manager->get('one.two.three'), 'failed to get a single item');
    }

    public function testRemoveNestedItems()
    {
        $this->manager->add('one.two.three', 'three-value');
        $this->manager->add('one.two.four', 'four-value');

        $this->manager->remove('one.two.three');
        $this->manager->remove('does.not.exist');

        $this->assertFullManifest(['one' => ['two' => ['four' => 'four-value']]]);
        $this->assertTrue($this->manager->exists('one.two.four'), 'failed to leave nested item in tact');
        $this->assertFalse($this->manager->exists('one.two.three'), 'failed to remove nested item');
    }
}