<?php
namespace Michaels\Manager\Test\Scenarios;

use Michaels\Manager\Messages\NoItemFoundMessage;
use Michaels\Manager\Test\Stubs\CustomizedItemsNameStub;
use Michaels\Manager\Test\Stubs\ManagesItemsTraitStub as Manager;
use Michaels\Manager\Test\Stubs\ManagesItemsTraitStub;
use Michaels\Manager\Test\Stubs\TraversableStub;
use Michaels\Manager\Test\Unit\Traits\ManagesItemsTest;
use StdClass;

trait ManagesItemsScenario
{
    public $managesItemsTestData = [
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

    private $simpleNestData = ['one' => ['two' => ['three' => 'three-value']]];

    private function assertFullManifest($manager, $manifest = null, $message = false)
    {
        $expected = ($manifest) ? $manifest : $this->managesItemsTestData;
        $message = ($message) ? $message : 'failed to add nested items';

        $actual = $manager->getAll();
        $this->assertEquals($expected, $actual, $message);
    }

    /** Begin Tests **/
    public function test_init_manager_from_array()
    {
        $manager = $this->getManager();
        $manager->initManager($this->managesItemsTestData);

        $this->assertEquals($this->managesItemsTestData, $manager->getAll(), "Failed to return identical values set at instantiation");
    }

    public function test_init_manager_from_single()
    {
        $manager = $this->getManager();
        $manager->initManager('foo');

        $this->assertEquals(['foo'], $manager->getAll());
    }

    public function test_init_manager_from_null()
    {
        $manager = $this->getManager();
        $manager->initManager(null);

        $this->assertEquals([], $manager->all());

        $manager = $this->getManager();
        $manager->initManager();

        $this->assertEquals([], $manager->all());
    }

    public function test_init_manager_from_manager()
    {
        $firstManager = $this->getManager();
        $firstManager->initManager(['foo' => 'bar']);

        $secondManager = $this->getManager();
        $secondManager->initManager($firstManager);

        $this->assertEquals(['foo' => 'bar'], $secondManager->all());
    }

    public function test_init_manager_from_object()
    {
        $object = new stdClass();
        $object->foo = 'bar';
        $manager = $this->getManager();
        $manager->initManager($object);

        $this->assertEquals(['foo' => 'bar'], $manager->getAll());
    }

    public function test_init_manager_from_traversable()
    {
        $object = new TraversableStub();
        $object['foo'] = 'bar';
        $manager = $this->getManager();
        $manager->initManager($object);

        $this->assertEquals(['foo' => 'bar'], $manager->getAll());
    }

    /* Now, to save time, we use $this->getManager() */
    public function test_add_and_get_single_item()
    {
        $manager = $this->getManager();
        $manager->add('alias', 'value');

        $this->assertArrayHasKey('alias', $manager->getAll(), 'Failed to confirm that manager has item `alias`');
        $this->assertEquals('value', $manager->get('alias'), 'Failed to get a single item');
    }

    public function test_add_multiple_items_at_once()
    {
        $manager = $this->getManager();
        $manager->add([
            'objectTest' => new StdClass(),
            'closureTest' => function () {
                return true;
            },
            'stringTest' => 'value'
        ]);

        $items = $manager->getAll();

        $this->assertArrayHasKey('objectTest', $items, 'Failed to confirm that manager has key `objectTest`');
        $this->assertArrayHasKey('closureTest', $items, 'Failed to confirm that manager has key `closureTest`');
        $this->assertArrayHasKey('stringTest', $items, 'Failed to confirm that manager has key `stringTest`');
    }

    public function test_return_true_if_item_exists()
    {
        $manager = $this->getManager();
        $manager->add('test', 'test-item');
        $manager->add('booleantest', false);

        $this->assertTrue($manager->exists('test'), "Failed to confirm that `test` exists");
        $this->assertTrue($manager->exists('booleantest'), "Failed to confirm that boolean false value exists");
    }

    public function test_return_false_if_item_does_not_exist()
    {
        $this->assertFalse($this->getManager()->exists('test'));
    }

    /* has() is an alias of exists(), tested here for coverage */
    public function test_return_true_if_has_item()
    {
        $manager = $this->getManager();
        $manager->add('test', 'test-item');
        $manager->add('booleantest', false);

        $this->assertTrue($manager->has('test'), "Failed to confirm that `test` exists");
        $this->assertTrue($manager->has('booleantest'), "Failed to confirm that boolean false value exists");
    }

    public function test_return_false_if_does_not_have_item()
    {
        $this->assertFalse($this->getManager()->has('test'));
    }

    public function test_provides_fallback_value()
    {
        $this->getManager()->add('one', 'one-value');

        $actual = $this->getManager()->get('two', 'default-value');

        $this->assertEquals('default-value', $actual, 'failed to return a fallback value');
    }

    public function test_throws_exception_if_item_not_found()
    {
        $this->setExpectedException('\Michaels\Manager\Exceptions\ItemNotFoundException');
        $this->getManager()->get('doesntexist');
    }

    public function test_get_if_exists_returns_item_if_exists()
    {
        $manager = $this->getManager();
        $manager->add($this->simpleNestData);

        $actual = $manager->getIfExists('one.two');
        $expected = $this->simpleNestData['one']['two'];

        $this->assertEquals($expected, $actual, "failed to return an item that exists");
    }

    public function test_get_if_exists_returns_message_if_no_exists()
    {
        $actual = $this->getManager()->getIfExists('nope');

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
        $manager = $this->getManager();
        $manager->add($this->simpleNestData);

        $actual = $manager->getIfHas('one.two');
        $expected = $this->simpleNestData['one']['two'];

        $this->assertEquals($expected, $actual, "failed to return an item that exists");
    }

    public function test_get_if_has_returns_message_if_no_exists()
    {
        $actual = $this->getManager()->getIfHas('nope');

        $this->assertInstanceOf('Michaels\Manager\Messages\NoItemFoundMessage', $actual, 'failed to return an instance of NoItemFoundMessage');
        $this->assertEquals("`nope` was not found", $actual->getMessage(), 'failed to return the correct message');
    }

    public function test_update_single_item()
    {
        $manager = $this->getManager();
        $manager->add('item', 'original-value');
        $manager->set('item', 'new-value');

        $this->assertEquals('new-value', $manager->get('item'), 'Failed to update a single item');
    }

    public function test_update_multiple_items()
    {
        $manager = $this->getManager();
        $manager->add('item', 'original-value');
        $manager->add('item2', 'other-original-value');
        $manager->set(['item' => 'new-value', 'item2' => 'other-new-value']);

        $this->assertEquals('new-value', $manager->get('item'), 'Failed to update first item');
        $this->assertEquals('other-new-value', $manager->get('item2'), 'Failed to update second item');
    }

    public function test_remove_single_item()
    {
        $manager = $this->getManager();
        $manager->add([
            'one' => 'one',
            'two' => 'two'
        ]);

        $manager->remove('one');

        $items = $manager->getAll();

        $this->assertArrayNotHasKey('one', $items, 'failed to remove `one`');
        $this->assertArrayHasKey('two', $items, 'failed to leave `two` intact');
    }

    public function test_clear()
    {
        $this->getManager()->add([
            'one' => 'one',
            'two' => 'two'
        ]);

        $this->getManager()->clear();
        $items = $this->getManager()->getAll();

        $this->assertEmpty($items, "Failed to empty manager");
    }

    public function test_add_nested_items()
    {
        $manager = $this->getManager();

        $manager->add('one.two.three', 'three-value');
        $manager->add('one.two.four.five', 'five-value');
        $manager->add('one.six', ['seven' => 'seven-value']);
        $manager->add('one.six.eight', 'eight-value');
        $manager->add('top', 'top-value');

        $this->assertFullManifest($manager);
    }

    public function test_check_existence_of_nested_items()
    {
        $manager = $this->getManager();
        $manager->add('one.two.three', 'three-value');

        $this->assertFullManifest($manager, $this->simpleNestData);
        $this->assertTrue($manager->exists('one.two.three'), 'failed to confirm existence of a nested item');
        $this->assertFalse($manager->exists('one.two.no'), 'failed to deny existence of a nested item');
    }

    public function test_get_nested_items()
    {
        $manager = $this->getManager();
        $manager->add('one.two.three', 'three-value');

        $this->assertFullManifest($manager, $this->simpleNestData);
        $this->assertEquals('three-value', $manager->get('one.two.three'), 'failed to get a single item');
    }

    public function test_remove_nested_items()
    {
        $manager = $this->getManager();
        $manager->add('one.two.three', 'three-value');
        $manager->add('one.two.four', 'four-value');

        $manager->remove('one.two.three');
        $manager->remove('does.not.exist');

        $this->assertFullManifest($manager, ['one' => ['two' => ['four' => 'four-value']]]);
        $this->assertTrue($manager->exists('one.two.four'), 'failed to leave nested item in tact');
        $this->assertFalse($manager->exists('one.two.three'), 'failed to remove nested item');
    }

    public function test_reset_items()
    {
        $manager = $this->getManager();
        $manager->add($this->managesItemsTestData);

        $expected = ['reset' => ['me' => ['now']]];

        $manager->reset($expected);

        $this->assertEquals($expected, $manager->getAll(), "failed to reset manager");
    }

    public function test_to_json()
    {
        $manager = $this->getManager();
        $manager->add($this->managesItemsTestData);

        $expected = json_encode($this->managesItemsTestData);

        $this->assertEquals($expected, $manager->toJson(), "failed to serialize json");
    }

    public function test_to_string()
    {
        $manager = $this->getManager();
        $manager->add($this->managesItemsTestData);

        $expected = json_encode($this->managesItemsTestData);

        $this->assertEquals($expected, "$manager", "failed to return json when called as a string");
    }

    public function test_is_empty()
    {
        $manager = $this->getManager();
        $this->assertTrue($this->getManager()->isEmpty(), "failed to confirm an empty manager");

        $manager->add($this->managesItemsTestData);
        $this->assertFalse($manager->isEmpty(), "failed to deny an empty manager");
    }

    public function test_throw_exception_if_trying_to_nest_under_anon_array()
    {
        $this->setExpectedException('\Michaels\Manager\Exceptions\NestingUnderNonArrayException');

        $manager = $this->getManager();
        $manager->initManager(['one' => 1, 'two' => 2]);

        $manager->add("one.two.three", "three-value");
    }

    public function test_customize_items_repo_name()
    {
        $manager = new ManagesItemsTraitStub();
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

    public function test_protect_single_item()
    {
        $this->setExpectedException('\Michaels\Manager\Exceptions\ModifyingProtectedValueException');
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

    public function test_protect_items_under_anest()
    {
        $this->setExpectedException('\Michaels\Manager\Exceptions\ModifyingProtectedValueException');

        $manager = $this->getManager([
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
        $manager = $this->getManager();

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

    public function test_push_single_value_onto_string()
    {
        $this->setExpectedException('\Michaels\Manager\Exceptions\NestingUnderNonArrayException');

        $manager = new Manager(['one' => ['two' => 'string']]);
        $manager->push('one.two', 'three');

        $this->assertEquals(['three'], $manager->get('one.two'), "failed to push value onto array");
    }

    public function test_push_single_value_onto_nothing()
    {
        $this->setExpectedException('\Michaels\Manager\Exceptions\ItemNotFoundException');

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