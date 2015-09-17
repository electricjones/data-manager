<?php
namespace Michaels\Manager\Test\Stubs;

use Michaels\Manager\Manager;

/**
 * Class CustomizedManagerStub
 * @package Stubs
 */
class CustomizedManagerStub extends Manager
{
    protected $other;

    public function __construct($items, $other)
    {
        $this->initManager($items);
        $this->other = $other;
    }

    public function getOther()
    {
        return $this->other;
    }
}
