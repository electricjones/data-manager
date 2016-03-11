<?php
namespace Michaels\Manager\Test\Traits;
use Michaels\Manager\Test\Scenarios\ArrayableScenario;

/**
 * Tests Array Access, JSON, and Iterator for Manager.
 * Does NOT test ManagesItemsTrait api.
 */
class ArrayableTest extends \PHPUnit_Framework_TestCase
{
    use ArrayableScenario;
}

