<?php
namespace Michaels\Manager\Test\Unit\Traits;

use Michaels\Manager\Test\Scenarios\ManagesIocScenario;
use Michaels\Manager\Test\Stubs\IocManagerStub;

class ManagesIocTest extends \PHPUnit_Framework_TestCase
{
   use ManagesIocScenario;

   public function getManager($items = [])
   {
      return new IocManagerStub($items);
   }
}