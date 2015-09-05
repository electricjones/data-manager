<?php
namespace Michaels\Manager\Test\Stubs;
use ArrayAccess;
use Countable;
use IteratorAggregate;
use JsonSerializable;
use Michaels\Manager\Contracts\ManagesItemsInterface;
use Michaels\Manager\Traits\ArrayableTrait;
use Michaels\Manager\Traits\ManagesItemsTrait;

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

    public function needs($one, $two, $three)
    {
        $this->two = $two;
        $this->three = $three;
        $this->one = $one;
    }
}
