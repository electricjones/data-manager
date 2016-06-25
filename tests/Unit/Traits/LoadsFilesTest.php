<?php
namespace Michaels\Manager\Test\Unit\Traits;

use Michaels\Manager\Test\Scenarios\LoadsFilesScenario;
use Michaels\Manager\Test\Stubs\LoadsFilesTraitStub;

class LoadsFilesTest extends \PHPUnit_Framework_TestCase
{
    use LoadsFilesScenario;

    public function getManager($items = [])
    {
        return new LoadsFilesTraitStub($items);
    }
}
