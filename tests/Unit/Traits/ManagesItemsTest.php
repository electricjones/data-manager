<?php
namespace Michaels\Manager\Test\Unit\Traits;

use Michaels\Manager\Manager;
use Michaels\Manager\Test\Scenarios\ManagesItemsScenario;

class ManagesItemsTest extends \PHPUnit_Framework_TestCase
{
    use ManagesItemsScenario;

    public function getManager($items = [])
    {
        return new Manager($items);
    }
}