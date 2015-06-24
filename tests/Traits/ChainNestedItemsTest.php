<?php
namespace Michaels\Manager\Test;

use Michaels\Manager\Test\Stubs\ChainsNestedItemsTraitStub;

class ChainsNestedItemsTest extends \PHPUnit_Framework_TestCase {

    private $manager;
    private $testData;

    public function setup()
    {
        $this->testData = [
            'one' => [
                'two' => [
                    'three' => 'three-value'
                ],
                'two_b' => 'two_b-value'
            ]
        ];

        $this->manager = new ChainsNestedItemsTraitStub();
        $this->manager->initManager($this->testData);
    }

    public function testGetThroughNestedMagicMethods()
    {
        $expectedA = $this->testData['one']['two']['three'];
        $actualA = $this->manager->one()->two()->three;

        $expectedB = $this->testData['one']['two_b'];
        $actualB = $this->manager->one()->two_b;

        $expectedC = $this->testData['one'];
        $actualC = $this->manager->one;

        $this->assertEquals($expectedA, $actualA, 'failed to retrieve first nested value');
        $this->assertEquals($expectedB, $actualB, 'failed to retrieve second nested value');
        $this->assertEquals($expectedC, $actualC, 'failed to retrieve third nested value');
    }
}

