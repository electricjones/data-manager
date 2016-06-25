<?php
namespace Michaels\Manager\Test\Managers\Integration;

use Michaels\Manager\ConfigManager;
use Michaels\Manager\Test\Scenarios\ArrayableScenario;
use Michaels\Manager\Test\Scenarios\LoadsFilesScenario;
use Michaels\Manager\Test\Scenarios\ManagesItemsScenario;

/**
 * Tests customized implementations of Manager.
 * Does NOT test ManagesItemsTrait api.
 */
class ConfigManagerTest extends \PHPUnit_Framework_TestCase
{
    use ManagesItemsScenario, ArrayableScenario, LoadsFilesScenario;

    public function getManager($items = [])
    {
        return new ConfigManager($items);
    }
}
