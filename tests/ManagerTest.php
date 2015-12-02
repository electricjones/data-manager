<?php
namespace Michaels\Manager\Test;

use Michaels\Manager\Manager;

/**
 * Tests customized implementations of Manager.
 * Does NOT test ManagesItemsTrait api.
 */
class ManagerTest extends \PHPUnit_Framework_TestCase {

    public function test_init_manager()
    {
        $expectedItems = ['one' => 1, 'two' => 2];

        $manager = new Manager($expectedItems);

        $this->assertEquals($expectedItems, $manager->getAll(), "failed to return the items from initManager");
    }
}

