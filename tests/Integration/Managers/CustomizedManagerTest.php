<?php
namespace Michaels\Manager\Test\Integration\Managers;

use Michaels\Manager\Contracts\ManagesItemsInterface;
use Michaels\Manager\Test\Scenarios\InitManagerScenario;
use Michaels\Manager\Test\Scenarios\ManagesItemsScenario;
use Michaels\Manager\Test\Stubs\CustomizedItemsNameStub;
use Michaels\Manager\Test\Stubs\CustomizedManagerStub;

/**
 * Tests customized implementations of Manager.
 * Does NOT test ManagesItemsTrait api.
 */
class CustomizedManagerTest extends \PHPUnit_Framework_TestCase {

    use ManagesItemsScenario, InitManagerScenario; // Pull in all the tests for ManagesItemsTrait, for integration testing

    /**
     * @param array $items
     * @param null $other
     * @return ManagesItemsInterface
     */
    public function getManager($items = [], $other = null)
    {
        return new CustomizedManagerStub($items, $other);
    }

    public function test_init_manager()
    {
        $expectedItems = ['one' => 1, 'two' => 2];
        $expectedOther = 'other-field';
        $manager = $this->getManager($expectedItems, $expectedOther);
        $this->assertEquals($expectedItems, $manager->getAll(), "failed to return the items from initManager");
        $this->assertEquals($expectedOther, $manager->getOther(), "failed to return customized field");
    }

    public function test_customize_items_repo_nameInClass()
    {
        $manager = new CustomizedItemsNameStub();
        $manager->add('one.two.three', 'three-value');
        $manager->add('one.four', 'four-value');

        $expected = [
            'one' => [
                'two' => [
                    'three' => 'three-value',
                ],
                'four' => 'four-value'
            ]
        ];

        $this->assertEquals($expected, $manager->getAll(), 'failed to customize item repo name');
        $this->assertEquals($expected, $manager->getItemsDirectly(), 'failed to set the new item repo');
        $this->assertFalse(property_exists($manager, 'items'), 'still set items');
    }
}
