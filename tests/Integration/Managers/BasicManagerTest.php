<?php
namespace Michaels\Manager\Test\Managers\Integration;

use Michaels\Manager\BasicManager;
use Michaels\Manager\Test\Scenarios\InitManagerScenario;
use Michaels\Manager\Test\Scenarios\ManagesItemsScenario;

/**
 * Tests customized implementations of Manager.
 * Does NOT test ManagesItemsTrait api.
 */
class BasicManagerTest extends \PHPUnit_Framework_TestCase
{
    use ManagesItemsScenario, InitManagerScenario;

    public function getManager($items = [])
    {
        return new BasicManager($items);
    }
}
