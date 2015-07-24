<?php
namespace Michaels\Manager\Test\Traits;


use Michaels\Manager\Test\Stubs\DependsOnManagesItemsFailStub;
use Michaels\Manager\Test\Stubs\DependsOnManagesItemsPassStub;

class DependsOnManagesItemsTest extends \PHPUnit_Framework_TestCase
{
    public function testPassesCheck()
    {
        $stub = new DependsOnManagesItemsPassStub();

        $this->assertTrue($stub->check());
    }

    /**
     * @expectedException Michaels\Manager\Exceptions\DependencyNotMetException
     */
    public function testFailsCheck()
    {
        $stub = new DependsOnManagesItemsFailStub();
        $stub->check();
    }
}

