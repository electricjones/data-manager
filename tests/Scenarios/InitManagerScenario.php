<?php
namespace Michaels\Manager\Test\Scenarios;
use Michaels\Manager\Manager;

/**
 * Tests Array Access, JSON, and Iterator for Manager.
 * Does NOT test ManagesItemsTrait api.
 */
trait InitManagerScenario
{
    public function test_init_manager()
    {
        $expectedItems = ['one' => 1, 'two' => 2];

        $manager = new Manager($expectedItems);

        $this->assertEquals($expectedItems, $manager->getAll(), "failed to return the items from initManager");
    }
}

