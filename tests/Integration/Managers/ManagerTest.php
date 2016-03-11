<?php
namespace Michaels\Manager\Test\Managers\Integration;

use Michaels\Manager\Test\Scenarios\InitManagerScenario;
use Michaels\Manager\Test\Scenarios\ManagesItemsScenario;

/**
 * Tests customized implementations of Manager.
 * Does NOT test ManagesItemsTrait api.
 */
class ManagerTest extends \PHPUnit_Framework_TestCase
{
    use ManagesItemsScenario, InitManagerScenario; // Pull in all the tests for ManagesItemsTrait, for integration testing
}
