<?php
namespace Michaels\Manager\Test;

use Michaels\Manager\Manager;
use Michaels\Manager\Test\Stubs\CustomizedManagerStub;

/**
 * Tests customized implementations of Manager.
 * Does NOT test ManagesItemsTrait api.
 */
class CustomizedManagerTest extends \PHPUnit_Framework_TestCase {

    public function testInitManager()
    {
        $expectedItems = ['one' => 1, 'two' => 2];
        $expectedOther = 'other-field';

        $manager = new CustomizedManagerStub($expectedItems, $expectedOther);

        $this->assertEquals($expectedItems, $manager->getAll(), "failed to return the items from initManager");
        $this->assertEquals($expectedOther, $manager->getOther(), "failed to return customized field");
    }
}

