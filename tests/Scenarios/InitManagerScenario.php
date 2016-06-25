<?php
namespace Michaels\Manager\Test\Scenarios;

/**
 * Tests Array Access, JSON, and Iterator for Manager.
 * Does NOT test ManagesItemsTrait api.
 */
trait InitManagerScenario
{
    public function test_init_manager()
    {
        $expectedItems = ['one' => 1, 'two' => 2];

        $manager = $this->getManager($expectedItems);

        $this->assertEquals($expectedItems, $manager->getAll(), "failed to return the items from initManager");
    }
}

