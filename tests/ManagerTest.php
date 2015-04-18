<?php
namespace Michaels\Manager\Test;

use Codeception\Specify;
use Michaels\Manager\Manager;

class ManagerTest extends \PHPUnit_Framework_TestCase {
    use Specify;

    public function testArrayAccess()
    {
        $this->specify("it adds items through array access", function() {
            $manager = new Manager();
            $manager['alias'] = 'value';

            $this->assertEquals('value', $manager->get('alias'), 'failed to add item');
        });

        $this->specify("it retrieves items through array access", function() {
            $manager = new Manager();
            $manager->add('alias', 'value');

            $this->assertEquals('value', $manager['alias'], 'failed to retrieve item');
        });

        $this->specify("it updates items through array access", function() {
            $manager = new Manager();
            $manager->add('alias', 'value');
            $manager['alias'] = 'new-value';

            $this->assertEquals('new-value', $manager->get('alias'), 'failed to add item');
        });

        $this->specify("it deletes items through array access", function() {
            $manager = new Manager();
            $manager->add('alias', 'value');

            unset($manager['alias']);

            $this->assertArrayNotHasKey('alias', $manager->getAll(), 'failed to remove item');
        });
    }

    public function testIteratorUse()
    {
        $this->specify("it allows foreach iteration", function() {
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
        });

        $this->specify("it retrieves items through array access", function() {
            $manager = new Manager();
            $manager->add('alias', 'value');

            $this->assertEquals('value', $manager['alias'], 'failed to retrieve item');
        });

        $this->specify("it updates items through array access", function() {
            $manager = new Manager();
            $manager->add('alias', 'value');
            $manager['alias'] = 'new-value';

            $this->assertEquals('new-value', $manager->get('alias'), 'failed to add item');
        });

        $this->specify("it deletes items through array access", function() {
            $manager = new Manager();
            $manager->add('alias', 'value');

            unset($manager['alias']);

            $this->assertArrayNotHasKey('alias', $manager->getAll(), 'failed to remove item');
        });

        $this->specify("it counts items through array access", function() {
            $manager = new Manager();
            $manager->add('alias', 'value');
            $manager->add('alias2', 'value');
            $manager->add('alias3', 'value');

            $this->assertEquals(3, count($manager), 'failed to count items');
        });
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

