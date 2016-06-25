<?php
namespace Michaels\Manager\Test\Unit\Traits;

use Michaels\Manager\Test\Scenarios\CollectionScenario;
use Michaels\Manager\Test\Stubs\CollectionStub;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
   use CollectionScenario;

   public function getManager($items = [])
   {
      return new CollectionStub($items);
   }
}