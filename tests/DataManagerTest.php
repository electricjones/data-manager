<?php
namespace Michaels\Manager\Test;

use Michaels\Manager\DataManager as Manager;

class DataManagerTest extends \PHPUnit_Framework_TestCase
{
    protected $item = [];

    public function testAddSingleItem()
    {
        $manager = new Manager();
        $manager->add('alias', 'value');

        $this->assertArrayHasKey('alias', $manager->getAll(), 'Array Items does not have key `alias`');
        $this->assertEquals('value', $manager->get('alias'), 'Failed to get a single item');
    }
}