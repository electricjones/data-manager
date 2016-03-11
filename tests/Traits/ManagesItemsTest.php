<?php
namespace Michaels\Manager\Test\Traits;

use Michaels\Manager\Messages\NoItemFoundMessage;
use Michaels\Manager\Test\Stubs\CustomizedItemsNameStub;
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
    public function test_init_manager_from_array()
    {
        $manager = new Manager();
        $manager->initManager($this->testData);

        $this->assertEquals($this->testData, $manager->getAll(), "Failed to return identical values set at instantiation");
    }

    public function test_init_manager_from_single()
    {
        $manager = new Manager();
        $manager->initManager('foo');

        $this->assertEquals(['foo'], $manager->getAll());
    }

    public function test_init_manager_from_null()
    {
        $manager = new Manager();
        $manager->initManager(null);

        $this->assertEquals([], $manager->all());

        $manager = new Manager();
        $manager->initManager();

        $this->assertEquals([], $manager->all());
    }

    public function test_init_manager_from_manager()
    {
        $firstManager = new Manager();
        $firstManager->initManager(['foo' => 'bar']);

        $secondManager = new Manager();
        $secondManager->initManager($firstManager);

        $this->assertEquals(['foo' => 'bar'], $secondManager->all());
    }

    public function test_init_manager_from_object()
    {
        $object = new stdClass();
        $object->foo = 'bar';
        $manager = new Manager();
        $manager->initManager($object);

        $this->assertEquals(['foo' => 'bar'], $manager->getAll());
    }

    public function test_init_manager_from_traversable()
    {
        $object = new TraversableStub();
        $object['foo'] = 'bar';
        $manager = new Manager();
        $manager->initManager($object);

        $this->assertEquals(['foo' => 'bar'], $manager->getAll());
    }

    /* Now, to save time, we use $this->manager */
    public function test_add_and_get_single_item()
    {
        $this->manager->add('alias', 'value');

        $this->assertArrayHasKey('alias', $this->manager->getAll(), 'Failed to confirm that manager has item `alias`');
        $this->assertEquals('value', $this->manager->get('alias'), 'Failed to get a single item');
    }

    public function test_add_multiple_items_at_once()
    {
        $this->manager->add([
            'objectTest' => new StdClass(),
            'closureTest' => function () {
                return true;
            },
            'stringTest' => 'value'
        ]);

        $items = $this->manager->getAll();

        $this->assertArrayHasKey('objectTest', $items, 'Failed to confirm that manager has key `objectTest`');
        $this->assertArrayHasKey('closureTest', $items, 'Failed to confirm that manager has key `closureTest`');
        $this->assertArrayHasKey('stringTest', $items, 'Failed to confirm that manager has key `stringTest`');
    }

    public function test_return_true_if_item_exists()
    {
        $this->manager->add('test', 'test-item');
        $this->manager->add('booleantest', false);

        $this->assertTrue($this->manager->exists('test'), "Failed to confirm that `test` exists");
        $this->assertTrue($this->manager->exists('booleantest'), "Failed to confirm that boolean false value exists");
    }

    public function test_return_false_if_item_does_not_exist()
    {
        $this->assertFalse($this->manager->exists('test'));
    }

    /* has() is an alias of exists(), tested here for coverage */
    public function test_return_true_if_has_item()
    {
        $this->manager->add('test', 'test-item');
        $this->manager->add('booleantest', false);

        $this->assertTrue($this->manager->has('test'), "Failed to confirm that `test` exists");
        $this->assertTrue($this->manager->has('booleantest'), "Failed to confirm that boolean false value exists");
    }

    public function test_return_false_if_does_not_have_item()
    {
        $this->assertFalse($this->manager->has('test'));
    }

    public function test_provides_fallback_value()
    {
        $this->manager->add('one', 'one-value');

        $actual = $this->manager->get('two', 'default-value');

        $this->assertEquals('default-value', $actual, 'failed to return a fallback value');
    }

    /**
     * @expectedException \Michaels\Manager\Exceptions\ItemNotFoundException
     */
    public function test_throws_exception_if_item_not_found()
    {
        $this->manager->get('doesntexist');
    }

    public function test_get_if_exists_returns_item_if_exists()
    {
        $this->manager->add($this->simpleNestData);

        $actual = $this->manager->getIfExists('one.two');
        $expected = $this->simpleNestData['one']['two'];

        $this->assertEquals($expected, $actual, "failed to return an item that exists");
    }

    public function test_get_if_exists_returns_message_if_no_exists()
    {
        $actual = $this->manager->getIfExists('nope');

        $this->assertInstanceOf('Michaels\Manager\Messages\NoItemFoundMessage', $actual, 'failed to return an instance of NoItemFoundMessage');
        $this->assertEquals("`nope` was not found", $actual->getMessage(), 'failed to return the correct mesage');
    }

    public function test_does_not_return_no_item_found_if_item_value_is_null()
    {
        // See https://github.com/chrismichaels84/data-manager/issues/17
        $manager = new Manager(['one' => ['two' => null]]);
        $actual = $manager->getIfExists('one.two');

        $this->assertNotInstanceOf(get_class(new NoItemFoundMessage()), $actual, "failed: returned a NoItemFoundMessage");
        $this->assertEquals(null, $actual, "failed to return `null` as value");
    }

    public function test_get_if_has_returns_item_if_exists()
    {
        $this->manager->add($this->simpleNestData);

        $actual = $this->manager->getIfHas('one.two');
        $expected = $this->simpleNestData['one']['two'];

        $this->assertEquals($expected, $actual, "failed to return an item that exists");
    }

    public function test_get_if_has_returns_message_if_no_exists()
    {
        $actual = $this->manager->getIfHas('nope');

        $this->assertInstanceOf('Michaels\Manager\Messages\NoItemFoundMessage', $actual, 'failed to return an instance of NoItemFoundMessage');
        $this->assertEquals("`nope` was not found", $actual->getMessage(), 'failed to return the correct message');
    }

    public function test_update_single_item()
    {
        $this->manager->add('item', 'original-value');
        $this->manager->set('item', 'new-value');

        $this->assertEquals('new-value', $this->manager->get('item'), 'Failed to update a single item');
    }

    public function test_update_multiple_items()
    {
        $this->manager->add('item', 'original-value');
        $this->manager->add('item2', 'other-original-value');
        $this->manager->set(['item' => 'new-value', 'item2' => 'other-new-value']);

        $this->assertEquals('new-value', $this->manager->get('item'), 'Failed to update first item');
        $this->assertEquals('other-new-value', $this->manager->get('item2'), 'Failed to update second item');
    }

    public function test_remove_single_item()
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

    public function test_clear()
    {
        $this->manager->add([
            'one' => 'one',
            'two' => 'two'
        ]);

        $this->manager->clear();
        $items = $this->manager->getAll();

        $this->assertEmpty($items, "Failed to empty manager");
    }

    public function test_add_nested_items()
    {
        $this->manager->add('one.two.three', 'three-value');
        $this->manager->add('one.two.four.five', 'five-value');
        $this->manager->add('one.six', ['seven' => 'seven-value']);
        $this->manager->add('one.six.eight', 'eight-value');
        $this->manager->add('top', 'top-value');

        $this->assertFullManifest();
    }

    public function test_check_existence_of_nested_items()
    {
        $this->manager->add('one.two.three', 'three-value');

        $this->assertFullManifest($this->simpleNestData);
        $this->assertTrue($this->manager->exists('one.two.three'), 'failed to confirm existence of a nested item');
        $this->assertFalse($this->manager->exists('one.two.no'), 'failed to deny existence of a nested item');
    }

    public function test_get_nested_items()
    {
        $this->manager->add('one.two.three', 'three-value');

        $this->assertFullManifest($this->simpleNestData);
        $this->assertEquals('three-value', $this->manager->get('one.two.three'), 'failed to get a single item');
    }

    public function test_remove_nested_items()
    {
        $this->manager->add('one.two.three', 'three-value');
        $this->manager->add('one.two.four', 'four-value');

        $this->manager->remove('one.two.three');
        $this->manager->remove('does.not.exist');

        $this->assertFullManifest(['one' => ['two' => ['four' => 'four-value']]]);
        $this->assertTrue($this->manager->exists('one.two.four'), 'failed to leave nested item in tact');
        $this->assertFalse($this->manager->exists('one.two.three'), 'failed to remove nested item');
    }

    public function test_reset_items()
    {
        $this->manager->add($this->testData);

        $expected = ['reset' => ['me' => ['now']]];

        $this->manager->reset($expected);

        $this->assertEquals($expected, $this->manager->getAll(), "failed to reset manager");
    }

    public function test_to_json()
    {
        $this->manager->add($this->testData);

        $expected = json_encode($this->testData);

        $this->assertEquals($expected, $this->manager->toJson(), "failed to serialize json");
    }

    public function test_to_string()
    {
        $this->manager->add($this->testData);

        $expected = json_encode($this->testData);

        $this->assertEquals($expected, "$this->manager", "failed to return json when called as a string");
    }

    public function test_is_empty()
    {
        $this->assertTrue($this->manager->isEmpty(), "failed to confirm an empty manager");

        $this->manager->add($this->testData);
        $this->assertFalse($this->manager->isEmpty(), "failed to deny an empty manager");
    }

    /**
     * @expectedException \Michaels\Manager\Exceptions\NestingUnderNonArrayException
     */
    public function test_throw_exception_if_trying_to_nest_under_anon_array()
    {
        $manager = new Manager();
        $manager->initManager(['one' => 1, 'two' => 2]);

        $manager->add("one.two.three", "three-value");
    }

    public function test_customize_items_repo_name()
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

    public function test_customize_items_repo_nameInClass()
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
    public function test_protect_single_item()
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
    public function test_protect_items_under_anest()
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

    public function test_load_defaults_into_empty_manager()
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

    public function test_load_defaults_into_non_empty_manager()
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

    public function test_push_single_value_onto_index_array()
    {
        $manager = new Manager(['one' => ['two' => []]]);
        $manager->push('one.two', 'three');

        $this->assertEquals(['three'], $manager->get('one.two'), "failed to push value onto array");
    }

    public function test_push_multiple_values_onto_index_array()
    {
        $manager = new Manager(['one' => ['two' => []]]);
        $manager->push('one.two', 'three', 'four', 'five');

        $this->assertEquals(['three', 'four', 'five'], $manager->get('one.two'), "failed to push value onto array");
    }

    public function test_push_single_value_onto_assoc_array()
    {
        $manager = new Manager(['one' => ['two' => ['three' => 'four']]]);
        $manager->push('one.two', 'five');

        $this->assertEquals(['three' => 'four', 'five'], $manager->get('one.two'), "failed to push value onto array");
    }

    /**
     * @expectedException \Michaels\Manager\Exceptions\NestingUnderNonArrayException
     */
    public function test_push_single_value_onto_string()
    {
        $manager = new Manager(['one' => ['two' => 'string']]);
        $manager->push('one.two', 'three');

        $this->assertEquals(['three'], $manager->get('one.two'), "failed to push value onto array");
    }

    /**
     * @expectedException \Michaels\Manager\Exceptions\ItemNotFoundException
     */
    public function test_push_single_value_onto_nothing()
    {
        $manager = new Manager(['one' => ['two']]);
        $manager->push('one.two', 'three');

        $this->assertEquals(['three'], $manager->get('one.two'), "failed to push value onto array");
    }

    public function test_hydrate_append()
    {
        $manager = new Manager(['one' => ['two']]);
        $hydrate = ['three' => ['three' => 'four']];
        $manager->hydrate($hydrate, true);

        $expected = [
            'one' => ['two'],
            'three' => ['three' => 'four']
        ];

        $this->assertEquals($expected, $manager->all(), "failed to append while hydrating");
    }
}