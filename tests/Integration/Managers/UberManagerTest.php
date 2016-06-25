<?php
namespace Michaels\Manager\Test\Managers\Integration;

use Michaels\Manager\Manager;
use Michaels\Manager\Test\Scenarios\ArrayableScenario;
use Michaels\Manager\Test\Scenarios\ChainsNestedItemsScenario;
use Michaels\Manager\Test\Scenarios\InitManagerScenario;
use Michaels\Manager\Test\Scenarios\LoadsFilesScenario;
use Michaels\Manager\Test\Scenarios\ManagesIocScenario;
use Michaels\Manager\Test\Scenarios\ManagesItemsScenario;
use Michaels\Manager\UberManager;

/**
 * Tests customized implementations of Manager.
 * Does NOT test ManagesItemsTrait api.
 */
class UberManagerTest extends \PHPUnit_Framework_TestCase
{
    use ManagesItemsScenario, InitManagerScenario, ChainsNestedItemsScenario, ArrayableScenario, LoadsFilesScenario, ManagesIocScenario; // Pull in all the tests for ManagesItemsTrait, for integration testing

    public function getManager($items = [])
    {
        return new UberManager($items);
    }
}
