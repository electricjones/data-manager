<?php
namespace Michaels\Manager\Test\Scenarios;

use Michaels\Manager\Test\Stubs\ArrayableManagerStub as Manager;

/**
 * Tests Array Access, JSON, and Iterator for Manager.
 * Does NOT test ManagesItemsTrait api.
 */
trait ArrayableScenario
{

    public function test_array_access_add()
    {
        $manager = new Manager();
        $manager['alias'] = 'value';

        $this->assertEquals('value', $manager->get('alias'), 'failed to add item');
    }

    public function test_array_access_retrieve()
    {
        $manager = new Manager();
        $manager->add('alias', 'value');

        $this->assertEquals('value', $manager['alias'], 'failed to retrieve item');
    }

    public function test_array_access_update()
    {
        $manager = new Manager();
        $manager->add('alias', 'value');
        $manager['alias'] = 'new-value';

        $this->assertEquals('new-value', $manager->get('alias'), 'failed to add item');
    }

    public function test_array_access_delete()
    {
        $manager = new Manager();
        $manager->add('alias', 'value');

        unset($manager['alias']);

        $this->assertArrayNotHasKey('alias', $manager->getAll(), 'failed to remove item');
    }

    public function test_array_access_is_set()
    {
        $manager = new Manager();
        $manager->add('alias', 'value');

        $this->assertTrue(isset($manager['alias']), "failed to confirm an existent item");
        $this->assertFalse(isset($manager['notexist']), "failed to deny a non-existent item");
    }

    public function test_iterator_use_for_each()
    {
        $expected = [
            'one' => [
                'two' => 'two-value'
            ],
            'three' => 'three-value'
        ];

        $manager = new Manager($expected);


        $actual = [];

        foreach ($manager as $item => $value) {
            $actual[$item] = $value;
        }

        $this->assertEquals($expected, $actual, 'failed to iterate');
    }

    public function test_countable()
    {
        $manager = new Manager();
        $manager->add('alias', 'value');
        $manager->add('alias2', 'value');
        $manager->add('alias3', 'value');

        $this->assertEquals(3, count($manager), 'failed to count items');
    }

    public function test_json_serializable()
    {
        $manager = new Manager();
        $test = [
            'alias' => 'value',
            'alias2' => 'value',
            'alias3' => 'value',
        ];

        $manager->add($test);

        $expected = json_encode($test);

        $this->assertEquals($expected, $json = json_encode($manager), 'failed to encode json');
    }
}

