<?php
namespace Michaels\Manager\Test\Stubs;

/**
 * Class DependencyFacoryStub
 * @package Stubs
 */
class DependencyFactoryStub
{

    public $two;
    public $three;
    public $one;

    public function __construct($one = null, $two = null, $three = null)
    {
        $this->two = $two;
        $this->three = $three;
        $this->one = $one;
    }

    public function needs($one = null, $two = null, $three = null)
    {
        $this->two = $two;
        $this->three = $three;
        $this->one = $one;
    }
}
