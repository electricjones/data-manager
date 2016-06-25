<?php
namespace Michaels\Manager\Test\Unit\Traits;

use Michaels\Manager\Test\Scenarios\ChainsNestedItemsScenario;
use Michaels\Manager\Test\Stubs\ChainsNestedItemsTraitStub;

class ChainsNestedItemsTest extends \PHPUnit_Framework_TestCase
{
    use ChainsNestedItemsScenario;

    public function getManager($items = [])
    {
        return new ChainsNestedItemsTraitStub($items);
    }
}

