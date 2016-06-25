<?php
namespace Michaels\Manager\Test\Scenarios;

trait ChainsNestedItemsScenario {

    private $chainsManager;
    private $testData = [
        'one' => [
            'two' => [
                'three' => 'three-value'
            ],
            'two_b' => 'two_b-value'
        ]
    ];

    public function setupChainsNestedItemsSetupManager()
    {
        $this->chainsManager = $this->getManager();
        $this->chainsManager->initManager($this->testData);
    }

    public function test_standard_use()
    {
        $manager = $this->getManager();
        $manager->add('key', 'value');

        $this->assertEquals('value', $manager->get('key'), 'failed to return a single item through magic method');
    }

    public function test_nested_standard_use()
    {
        $this->setupChainsNestedItemsSetupManager();
        $this->chainsManager->add('key.one.two.three', 'value');

        $this->assertEquals('value', $this->chainsManager->get('key.one.two.three'), 'failed to return a single item through magic method');
    }

    public function test_get_single_item()
    {
        $this->setupChainsNestedItemsSetupManager();
        $this->chainsManager->add('key', 'value');

        $this->assertEquals('value', $this->chainsManager->key, 'failed to return a single item through magic method');
    }

    public function test_get_through_nested_magic_methods()
    {
        $this->setupChainsNestedItemsSetupManager();
        $expectedA = $this->testData['one']['two']['three'];
        $actualA = $this->chainsManager->one()->two()->three;

        $expectedB = $this->testData['one']['two_b'];
        $actualB = $this->chainsManager->one()->two_b;

        $expectedC = $this->testData['one'];
        $actualC = $this->chainsManager->one;

        $this->assertEquals($expectedA, $actualA, 'failed to retrieve first nested value');
        $this->assertEquals($expectedB, $actualB, 'failed to retrieve second nested value');
        $this->assertEquals($expectedC, $actualC, 'failed to retrieve third nested value');
    }

    public function test_doesnt_set_level_for_valid_method_call() {
        $this->setupChainsNestedItemsSetupManager();

        $this->chainsManager->getAll();
        $this->chainsManager->one = "one";

        $this->assertEquals("one", $this->chainsManager->get('one'));
        $this->assertEquals("one", $this->chainsManager->one);
        $this->assertFalse($this->chainsManager->has('someMethod'));
    }

    public function test_add_top_level_item_through_magic_method(){
        $this->setupChainsNestedItemsSetupManager();
        $this->chainsManager->one = "one";
        $this->assertEquals("one", $this->chainsManager->get('one'));
        $this->assertEquals("one", $this->chainsManager->one);
    }

    public function test_add_nested_item_through_magic_method() {
        $this->setupChainsNestedItemsSetupManager();
        $this->chainsManager->one()->two()->three = "three";
        $this->assertEquals("three", $this->chainsManager->get('one.two.three'));
        $this->assertEquals("three", $this->chainsManager->one()->two()->three);
    }

    public function test_add_nested_item_to_array_through_magic_method() {
        $this->setupChainsNestedItemsSetupManager();
        $this->chainsManager->one()->two = [];
        $this->chainsManager->one()->two()->three()->four = "four";

        $this->assertEquals("four", $this->chainsManager->get('one.two.three.four'));
        $this->assertEquals("four", $this->chainsManager->one()->two()->three()->four);

        $this->chainsManager->five()->six = [
            'seven' => [
                'eight' => 'eight',
            ]
        ];
        $this->chainsManager->five()->six()->seven()->nine = "nine";

        $this->assertEquals("eight", $this->chainsManager->get('five.six.seven.eight'));
        $this->assertEquals("nine", $this->chainsManager->five()->six()->seven()->nine);
    }

    public function test_update_nested_item_through_magic_method() {
        $this->setupChainsNestedItemsSetupManager();
        $this->chainsManager->one()->two = [];
        $this->chainsManager->one()->two()->three()->four = "four";

        $this->chainsManager->one()->two()->three()->four = "new-four";

        $this->assertEquals("new-four", $this->chainsManager->get('one.two.three.four'), "failed to update property");
    }

    public function test_delete_nested_item_through_magic_method() {
        $this->setupChainsNestedItemsSetupManager();
        $this->chainsManager->one()->two = 'two';
        $this->chainsManager->one()->three = 'three';

        $this->chainsManager->one()->three()->drop();
        $this->assertFalse($this->chainsManager->has('one.three'), 'failed to remove property');
        $this->assertTrue($this->chainsManager->has('one.two'), 'removed wrong property');
    }
}

