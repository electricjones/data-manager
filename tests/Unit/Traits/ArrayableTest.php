<?php
namespace Michaels\Manager\Test\Unit\Traits;
use Michaels\Manager\Test\Scenarios\ArrayableScenario;
use Michaels\Manager\Test\Stubs\ArrayableManagerStub;

/**
 * Tests Array Access, JSON, and Iterator for Manager.
 * Does NOT test ManagesItemsTrait api.
 */
class ArrayableTest extends \PHPUnit_Framework_TestCase
{
    use ArrayableScenario;

    public function getManager($items = [])
    {
        return new ArrayableManagerStub($items);
    }
}

