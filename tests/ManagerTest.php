<?php
namespace Michaels\Manager\Test;

use Michaels\Manager\Manager;

/**
 * Tests Array Access, JSON, and Iterator for Manager.
 * Does NOT test ManagesItemsTrait api.
 */
class ManagerTest extends \PHPUnit_Framework_TestCase {

    public function testArrayAccessAdd()
    {
        $manager = new Manager();
        $manager['alias'] = 'value';

        $this->assertEquals('value', $manager->get('alias'), 'failed to add item');
    }

    public function testArrayAccessRetrieve()
    {
        $manager = new Manager();
        $manager->add('alias', 'value');

        $this->assertEquals('value', $manager['alias'], 'failed to retrieve item');
    }

    public function testArrayAccessUpdate()
    {
        $manager = new Manager();
        $manager->add('alias', 'value');
        $manager['alias'] = 'new-value';

        $this->assertEquals('new-value', $manager->get('alias'), 'failed to add item');
    }

    public function testArrayAccessDelete()
    {
        $manager = new Manager();
        $manager->add('alias', 'value');

        unset($manager['alias']);

        $this->assertArrayNotHasKey('alias', $manager->getAll(), 'failed to remove item');
    }

    public function testIteratorUseForEach()
    {
        $expected = [
            'one'   => [
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

    public function testCountable()
    {
        $manager = new Manager();
        $manager->add('alias', 'value');
        $manager->add('alias2', 'value');
        $manager->add('alias3', 'value');

        $this->assertEquals(3, count($manager), 'failed to count items');
    }

    public function testJsonSerializable()
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

