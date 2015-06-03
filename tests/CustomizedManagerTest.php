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

    public function testInitWithNonArray()
    {
        $expectedItems = ['one' => 1, 'two' => 2];
        $expectedOther = 'other-field';

        // This tests using an Arrayable Object but NOT [] upon creation.
        // A good example of such an object is actually Manager itself, lol
        $arrayLikeObject = new Manager($expectedItems);
        $manager = new CustomizedManagerStub($arrayLikeObject, $expectedOther);

        $this->assertTrue(is_array($manager->getAll()));
        $this->assertEquals($expectedItems, $manager->getAll(), "failed to return the items as an array");

        $manager->add('three.four.five', 'five-value');

        $this->assertTrue($manager->has('three.four.five'), "failed to confirm existence of added nested value");
        $this->assertEquals("five-value", $manager->get("three.four.five"), "failed to return new nested value");

        $this->assertEquals($expectedOther, $manager->getOther(), "failed to return other value");
        // If this works, then everything should work
    }
}

