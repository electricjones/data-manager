<?php
namespace Michaels\Manager\Test\Traits;

use Michaels\Manager\Messages\NoItemFoundMessage;
use Michaels\Manager\Test\Stubs\CustomizedItemsNameStub;
use Michaels\Manager\Test\Stubs\CustomizedManagerStub;
use Michaels\Manager\Test\Stubs\ManagesItemsTraitStub as Manager;
use Michaels\Manager\Test\Stubs\TraversableStub;
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
    public function testInitManagerFromArray()
    {
        $manager = new Manager();
        $manager->initManager($this->testData);

        $this->assertEquals($this->testData, $manager->getAll(), "Failed to return identical values set at instantiation");
    }

    public function testInitManagerFromSingle()
    {
        $manager = new Manager();
        $manager->initManager('foo');

        $this->assertEquals(['foo'], $manager->getAll());
    }

    public function testInitManagerFromNull()
    {
        $manager = new Manager();
        $manager->initManager(null);

        $this->assertEquals([], $manager->all());

        $manager = new Manager();
        $manager->initManager();

        $this->assertEquals([], $manager->all());
    }

    public function testInitManagerFromManager()
    {
        $firstManager = new Manager();
        $firstManager->initManager(['foo' => 'bar']);

        $secondManager = new Manager();
        $secondManager->initManager($firstManager);

        $this->assertEquals(['foo' => 'bar'], $secondManager->all());
    }

    public function testInitManagerFromObject()
    {
        $object = new stdClass();
        $object->foo = 'bar';
        $manager = new Manager();
        $manager->initManager($object);

        $this->assertEquals(['foo' => 'bar'], $manager->getAll());
    }

    public function testInitManagerFromTraversable()
    {
        $object = new TraversableStub();
        $object['foo'] = 'bar';
        $manager = new Manager();
        $manager->initManager($object);

        $this->assertEquals(['foo' => 'bar'], $manager->getAll());
    }

    /* Now, to save time, we use $this->manager */
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

    /* has() is an alias of exists(), tested here for coverage */
    public function testReturnTrueIfHasItem()
    {
        $this->manager->add('test', 'test-item');
        $this->manager->add('booleantest', false);

        $this->assertTrue($this->manager->has('test'), "Failed to confirm that `test` exists");
        $this->assertTrue($this->manager->has('booleantest'), "Failed to confirm that boolean false value exists");
    }

    public function testReturnFalseIfDoesNotHaveItem()
    {
        $this->assertFalse($this->manager->has('test'));
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

    public function testGetIfExistsReturnsItemIfExists()
    {
        $this->manager->add($this->simpleNestData);

        $actual = $this->manager->getIfExists('one.two');
        $expected = $this->simpleNestData['one']['two'];

        $this->assertEquals($expected, $actual, "failed to return an item that exists");
    }

    public function testGetIfExistsReturnsMessageIfNoExists()
    {
        $actual = $this->manager->getIfExists('nope');

        $this->assertInstanceOf('Michaels\Manager\Messages\NoItemFoundMessage', $actual, 'failed to return an instance of NoItemFoundMessage');
        $this->assertEquals("`nope` was not found", $actual->getMessage(), 'failed to return the correct mesage');
    }

    public function testGetIfHasReturnsItemIfExists()
    {
        $this->manager->add($this->simpleNestData);

        $actual = $this->manager->getIfHas('one.two');
        $expected = $this->simpleNestData['one']['two'];

        $this->assertEquals($expected, $actual, "failed to return an item that exists");
    }

    public function testGetIfHasReturnsMessageIfNoExists()
    {
        $actual = $this->manager->getIfHas('nope');

        $this->assertInstanceOf('Michaels\Manager\Messages\NoItemFoundMessage', $actual, 'failed to return an instance of NoItemFoundMessage');
        $this->assertEquals("`nope` was not found", $actual->getMessage(), 'failed to return the correct message');
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

    public function testResetItems()
    {
        $this->manager->add($this->testData);

        $expected = ['reset' => ['me' => ['now']]];

        $this->manager->reset($expected);

        $this->assertEquals($expected, $this->manager->getAll(), "failed to reset manager");
    }

    public function testToJson()
    {
        $this->manager->add($this->testData);

        $expected = json_encode($this->testData);

        $this->assertEquals($expected, $this->manager->toJson(), "failed to serialize json");
    }

    public function testToString()
    {
        $this->manager->add($this->testData);

        $expected = json_encode($this->testData);

        $this->assertEquals($expected, "$this->manager", "failed to return json when called as a string");
    }

    public function testIsEmpty()
    {
        $this->assertTrue($this->manager->isEmpty(), "failed to confirm an empty manager");

        $this->manager->add($this->testData);
        $this->assertFalse($this->manager->isEmpty(), "failed to deny an empty manager");
    }

    /**
     * @expectedException \Michaels\Manager\Exceptions\NestingUnderNonArrayException
     */
    public function testThrowExceptionIfTryingToNestUnderANonArray()
    {
        $manager = new Manager();
        $manager->initManager(['one' => 1, 'two' => 2]);

        $manager->add("one.two.three", "three-value");
    }

    public function testCustomizeItemsRepoName()
    {
        $manager = new Manager();
        $manager->setItemsName('thisIsJustATest');
        $manager->add('one.two.three', 'three-value');
        $manager->add('one.four', 'four-value');

        $expected = [
            'one' => [
                'two' => [
                    'three' => 'three-value',
                ],
                'four' => 'four-value'
            ]
        ];

        $this->assertEquals($expected, $manager->getAll(), 'failed to customize item repo name');
    }

    public function testCustomizeItemsRepoNameInClass()
    {
        $manager = new CustomizedItemsNameStub();
        $manager->add('one.two.three', 'three-value');
        $manager->add('one.four', 'four-value');

        $expected = [
            'one' => [
                'two' => [
                    'three' => 'three-value',
                ],
                'four' => 'four-value'
            ]
        ];

        $this->assertEquals($expected, $manager->getAll(), 'failed to customize item repo name');
        $this->assertEquals($expected, $manager->getItemsDirectly(), 'failed to set the new item repo');
        $this->assertFalse(property_exists($manager, 'items'), 'still set items');
    }

    /**
     * @expectedException \Michaels\Manager\Exceptions\ModifyingProtectedValueException
     */
    public function testProtectSingleItem()
    {
        $manager = new Manager([
            'some' => [
                'data' => [
                    'here' => 'value'
                ]
            ]
        ]);

        $manager->protect('some.data.here');
        $manager->set('some.data.here', 'new-value');
    }

    /**
     * @expectedException \Michaels\Manager\Exceptions\ModifyingProtectedValueException
     */
    public function testProtectItemsUnderANest()
    {
        $manager = new Manager([
            'some' => [
                'data' => [
                    'here' => 'value'
                ]
            ]
        ]);

        $manager->protect('some');
        $manager->set('some.data.here', 'new-value');
    }

    public function testLoadDefaultsIntoEmptyManager()
    {
        $manager = new Manager();

        $defaults = [
            'one' => [
                'two' => [
                    'three' => [
                        'true' => true,
                    ]
                ],
                'four' => 'four'
            ]
        ];

        $manager->loadDefaults($defaults);

        $this->assertEquals($defaults, $manager->getAll(), "failed to load defaults");
    }

    public function testLoadDefaultsIntoNonEmptyManager()
    {
        $defaults = [
            'one' => [
                'two' => [
                    'three' => [
                        'true' => true,
                    ]
                ],
                'four' => [
                    'six' => false,
                ]
            ],
            'five' => 5,
            'seven' => [
                'a' => 'A',
                'b' => 'B',
                'c' => 'C',
            ]
        ];

        $starting = [
            'one' => [
                'two' => [],
                'four' => 'michael'
            ],
            'seven' => [
                'c' => 'over-ridden-c',
            ],
            'eight' => 8,
        ];

        $expected = [
            'one' => [
                'two' => [
                    'three' => [
                        'true' => true,
                    ]
                ],
                'four' => 'michael',
            ],
            'five' => 5,
            'seven' => [
                'a' => 'A',
                'b' => 'B',
                'c' => 'over-ridden-c',
            ],
            'eight' => 8,
        ];

        $manager = new Manager($starting);
        $manager->loadDefaults($defaults);

        $this->assertEquals($expected, $manager->getAll(), "failed to load defaults");
    }

    public function testHydrateFromJson()
    {
        $json = json_encode($this->testData);
        $this->manager->clear();
        $this->manager->hydrateFrom('json', $json);

        $this->assertEquals($this->testData, $this->manager->getAll(), "failed to hydrate from JSON");

    }

    public function testAppendFromJson()
    {
        $startData = [
            'one' => [
                'two' => [
                    'three' => [
                        'true' => true,
                    ]
                ],
                'four' => [
                    'six' => false,
                ]
            ],
            'five' => 5,
            'six' => [
                'a' => 'A',
                'b' => 'B',
                'c' => 'C',
            ]
        ];

        $appendData = json_encode([
            'seven' => [
                'two' => [],
                'four' => 'michael'
            ],
            'eight' => [
                'foo' => 'bar',
            ],
            'nine' => 10
        ]);

        $expected = [
            'one' => [
                'two' => [
                    'three' => [
                        'true' => true,
                    ]
                ],
                'four' => [
                    'six' => false,
                ]
            ],
            'five' => 5,
            'six' => [
                'a' => 'A',
                'b' => 'B',
                'c' => 'C',
            ],
            'seven' => [
                'two' => [],
                'four' => 'michael'
            ],
            'eight' => [
                'foo' => 'bar'
            ],
            'nine' => 10
        ];

        $this->manager->reset($startData);
        $this->manager->appendFrom('json', $appendData);

        $this->assertEquals($expected, $this->manager->getAll(), "failed to append from JSON");

    }

    /**
     * @expectedException \Michaels\Manager\Exceptions\SerializationTypeNotSupportedException
     */

    public function testSerializationTypeUnsupportedExceptionForHydrate()
    {
        $data = "just a string";
        $this->manager->hydrateFrom('someType', $data);

    }

    /**
     * @expectedException \Michaels\Manager\Exceptions\SerializationTypeNotSupportedException
     */

    public function testSerializationTypeUnsupportedExceptionForAppend()
    {
        $data = "just a string";
        $this->manager->appendFrom('someType', $data);

    }

}