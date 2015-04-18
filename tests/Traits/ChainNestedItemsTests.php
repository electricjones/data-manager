<?php
namespace Michaels\Manager\Test;

use Codeception\Specify;

class ChainsNestedItems extends \PHPUnit_Framework_TestCase {
    use Specify;

    public function testMethod()
    {
        $this->specify("it does something", function() {

            $this->assertTrue(true);
        });
    }
}

